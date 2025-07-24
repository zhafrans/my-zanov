<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OldTransaction;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\TransactionItem;
use App\Models\TransactionInstallment;
use App\Models\TransactionOutstanding;
use App\Models\Color;
use App\Models\Size;
use App\Models\User;
use App\Models\Village;
use App\Models\Subdistrict;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MigrateOldTransaction extends Command
{
    protected $signature = 'app:migrate-old';
    protected $description = 'Migrate data from old sales table to new database structure';

    // Sales name to ID mapping
    protected $salesMapping = [
        'UMI' => 2,
        'ATI' => 3,
        'INTO' => 4,
        'NISYA' => 5,
        'WINDA' => 6,
        'RESTI' => 7,
        'ANNA' => 8,
    ];

    public function handle()
    {
        $this->info("Starting migration of old transactions...");

        // Get all old transactions (remove limit for production)
        $oldTransactions = OldTransaction::orderBy('id')->get();
        $total = count($oldTransactions);
        $this->output->progressStart($total);

        $successCount = 0;
        $failCount = 0;
        $failedTransactions = [];

        foreach ($oldTransactions as $oldTrx) {
            try {
                DB::beginTransaction();
                $this->processTransaction($oldTrx);
                DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $failCount++;
                
                // Log detailed error information
                $errorDetails = [
                    'old_transaction_id' => $oldTrx->id,
                    'no_kartu' => $oldTrx->no_kartu,
                    'nama' => $oldTrx->nama ?? 'NULL',
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString(),
                    'timestamp' => now()->toDateTimeString(),
                ];
                
                Log::error('Failed to migrate transaction', $errorDetails);
                $failedTransactions[] = $errorDetails;
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        
        // Summary output
        $this->info("\nMigration completed!");
        $this->info("Successfully migrated: {$successCount} transactions");
        $this->error("Failed to migrate: {$failCount} transactions");
        
        // Save failed transactions to log file if any
        if ($failCount > 0) {
            $logFileName = 'failed_transactions_' . now()->format('Ymd_His') . '.log';
            $logDirectory = storage_path('logs/migrations/');
            
            // Create directory if it doesn't exist
            if (!file_exists($logDirectory)) {
                mkdir($logDirectory, 0755, true);
            }
            
            $logPath = $logDirectory . $logFileName;
            
            file_put_contents($logPath, json_encode($failedTransactions, JSON_PRETTY_PRINT));
            
            $this->error("Failed transactions log saved to: {$logPath}");
        }
    }

    protected function processTransaction(OldTransaction $oldTrx)
    {
        // Validate required fields with fallbacks
        $this->validateTransactionData($oldTrx);

        // Generate unique invoice number with fallback
        $invoiceBase = $oldTrx->no_kartu ?? 'INV' . Str::random(6);
        $invoiceNumber = $this->generateUniqueInvoice($invoiceBase);

        // Create or find customer with fallbacks
        $customer = $this->createCustomer($oldTrx);

        // Determine payment type and status with fallbacks
        $paymentType = strtoupper($oldTrx->ket ?? '') === 'CASH' ? 'cash' : 'installment';
        $status = $this->determineStatus($oldTrx);

        // Create transaction with fallback values
        $transaction = Transaction::create([
            'invoice' => $invoiceNumber,
            'deal_price' => $oldTrx->harga ?? 0,
            'customer_id' => $customer->id,
            'seller_id' => $this->getSalesId($oldTrx->nama_sales ?? ''),
            'payment_type' => $paymentType,
            'status' => $status,
            'transaction_date' => $oldTrx->tgl_pengambilan ?? $oldTrx->tgl_ang1 ?? now(),
            'is_tempo' => $this->hasMultipleInstallments($oldTrx) ? '1' : null,
            'tempo_at' => Carbon::parse($oldTrx->tgl_pengambilan ?? $oldTrx->tgl_ang1 ?? now())->addMonth(),
            'note' => $oldTrx->ket ?? null,
            'is_printed' => $oldTrx->is_created ?? 0,
            'created_at' => $oldTrx->created_at ?? now(),
            'updated_at' => $oldTrx->updated_at ?? now(),
        ]);

        // Create product variant and transaction item with fallbacks
        $this->createProductAndItem($oldTrx, $transaction);

        // Process installments if applicable
        if ($paymentType === 'installment') {
            $this->processInstallments($oldTrx, $transaction);
        }

        // Create outstanding record if not fully paid
        if ($status === 'pending') {
            $outstandingAmount = $this->calculateOutstanding($oldTrx);
            TransactionOutstanding::create([
                'transaction_id' => $transaction->id,
                'outstanding_amount' => $outstandingAmount,
                'created_at' => $oldTrx->created_at ?? now(),
                'updated_at' => $oldTrx->updated_at ?? now(),
            ]);
        }
    }

    protected function validateTransactionData(OldTransaction $oldTrx)
    {
        // Skip validation for empty fields and provide defaults later
        // Only validate critical numeric fields that can't have defaults
        if (isset($oldTrx->harga)) {
            if (!is_numeric($oldTrx->harga)) {
                throw new \Exception("Invalid harga value: {$oldTrx->harga} for transaction ID: {$oldTrx->id}");
            }

            if ($oldTrx->harga <= 0) {
                throw new \Exception("Harga must be greater than 0 for transaction ID: {$oldTrx->id}");
            }
        }
    }

    protected function generateUniqueInvoice($base)
    {
        $cleanBase = $base ? preg_replace('/[^a-zA-Z0-9]/', '', $base) : 'INV';
        $count = Transaction::where('invoice', 'like', $cleanBase.'%')->count();
        
        if ($count === 0) {
            return $cleanBase;
        }

        return $cleanBase . 'N' . str_pad($count + 1, 2, '0', STR_PAD_LEFT);
    }

    protected function createCustomer(OldTransaction $oldTrx)
    {
        // Find existing locations with partial matching
        $subdistrict = null;
        $city = null;
        
        if (!empty($oldTrx->kecamatan)) {
            $subdistrict = Subdistrict::where('name', 'LIKE', '%' . $oldTrx->kecamatan . '%')->first();
        }
        
        if (!empty($oldTrx->kabupaten)) {
            $city = City::where('name', 'LIKE', '%' . $oldTrx->kabupaten . '%')->first();
        }

        // Handle null name - set to "UNKNOWN"
        $customerName = empty(trim($oldTrx->nama ?? '')) ? 'UNKNOWN' : $oldTrx->nama;

        // Generate customer code with fallbacks
        $customerCode = $this->generateCustomerCode($oldTrx->no_kartu ?? null, $customerName);

        return Customer::firstOrCreate(
            ['code' => $customerCode],
            [
                'name' => $customerName,
                'address' => $oldTrx->alamat ?? '',
                'phone' => $oldTrx->no_telp ?? '',
                'village_id' => null,
                'subdistrict_id' => $subdistrict->id ?? null,
                'city_id' => $city->id ?? null,
                'province_id' => null,
                'created_at' => $oldTrx->created_at ?? now(),
                'updated_at' => $oldTrx->updated_at ?? now(),
            ]
        );
    }

    protected function generateCustomerCode($cardNumber, $customerName)
    {
        if ($cardNumber) {
            $cleanCard = preg_replace('/[^a-zA-Z0-9]/', '', $cardNumber);
            if (!empty($cleanCard)) {
                return $cleanCard;
            }
        }
        
        $nameParts = explode(' ', trim($customerName));
        $code = '';
        
        foreach (array_slice($nameParts, 0, 3) as $part) {
            $code .= strtoupper(substr($part, 0, 1));
        }
        
        if (strlen($code) < 3) {
            $code .= mt_rand(100, 999);
        } else {
            $code .= mt_rand(10, 99);
        }
        
        return $code;
    }

    protected function getSalesId($salesName)
    {
        if (empty($salesName)) {
            return 1; // Default to admin user if empty
        }

        $salesName = strtoupper(trim($salesName));
        return $this->salesMapping[$salesName] ?? 1;
    }

    protected function determineStatus(OldTransaction $oldTrx)
    {
        if (strtoupper($oldTrx->ket ?? '') === 'LUNAS' || strtoupper($oldTrx->ket ?? '') === 'CASH') {
            return 'paid';
        }
        
        if (!empty($oldTrx->ang1) || !empty($oldTrx->ang2) || !empty($oldTrx->ang3) || 
            !empty($oldTrx->ang4) || !empty($oldTrx->ang5)) {
            return 'pending';
        }
        
        return 'pending';
    }

    protected function hasMultipleInstallments(OldTransaction $oldTrx)
    {
        $count = 0;
        if (!empty($oldTrx->ang1)) $count++;
        if (!empty($oldTrx->ang2)) $count++;
        if (!empty($oldTrx->ang3)) $count++;
        if (!empty($oldTrx->ang4)) $count++;
        if (!empty($oldTrx->ang5)) $count++;
        
        return $count > 1;
    }

    protected function createProductAndItem(OldTransaction $oldTrx, Transaction $transaction)
    {
        // Handle null values with defaults
        $colorName = !empty($oldTrx->warna) ? $oldTrx->warna : 'UNKNOWN';
        $sizeName = !empty($oldTrx->size) ? $oldTrx->size : 'UNKNOWN';
        $productName = !empty($oldTrx->nama_produk) ? $oldTrx->nama_produk : 'UNKNOWN PRODUCT';

        // Find or create color with unique code
        $color = Color::firstOrCreate(
            ['name' => $colorName],
            ['code' => $this->generateUniqueColorCode($colorName)]
        );
        
        // Find or create size with unique code
        $size = Size::firstOrCreate(
            ['name' => $sizeName],
            ['code' => $this->generateUniqueSizeCode($sizeName)]
        );
        
        // Create product variant with fallbacks
        $productVariant = ProductVariant::firstOrCreate(
            ['other_code' => $productName],
            [
                'base_code' => Str::random(10),
                'code' => null,
                'other_code' => $productName,
                'product_id' => null,
                'color_id' => $color->id,
                'size_id' => $size->id ?? null,
                'heel_id' => null,
                'gender' => null,
                'image' => null,
                'price' => null,
                'installment_price' => null,
            ]
        );
        
        // Create transaction item with fallback price
        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_variant_id' => $productVariant->id,
            'stock_type_id' => null,
            'quantity' => 1,
            'snapshot_name' => $productName . ' ' . $colorName . ' ' . $sizeName,
            'snapshot_price' => $oldTrx->harga ?? 0,
            'created_at' => $oldTrx->created_at ?? now(),
            'updated_at' => $oldTrx->updated_at ?? now(),
        ]);
    }

    protected function generateUniqueColorCode($colorName)
    {
        $baseCode = strtoupper(substr(trim($colorName), 0, 1)) . mt_rand(100, 999);
        $count = Color::where('code', 'like', $baseCode.'%')->count();
        
        return $count > 0 ? $baseCode . $count : $baseCode;
    }

    protected function generateUniqueSizeCode($sizeName)
    {
        $cleanSize = preg_replace('/[^a-zA-Z0-9]/', '', $sizeName);
        
        if (is_numeric($cleanSize)) {
            $baseCode = 'SZ' . str_pad($cleanSize, 2, '0', STR_PAD_LEFT);
        } else {
            $code = strtoupper(substr($cleanSize, 0, 3));
            $baseCode = strlen($code) < 3 ? $code . mt_rand(1, 9) : $code;
        }
        
        // Check for duplicates and append number if needed
        $count = Size::where('code', 'like', $baseCode.'%')->count();
        
        return $count > 0 ? $baseCode . $count : $baseCode;
    }

    protected function processInstallments(OldTransaction $oldTrx, Transaction $transaction)
    {
        $installments = [];
        
        if (!empty($oldTrx->ang1) && !empty($oldTrx->tgl_ang1)) {
            $installments[] = [
                'amount' => $oldTrx->ang1,
                'date' => $oldTrx->tgl_ang1,
                'collector' => $oldTrx->coll1,
            ];
        }
        
        if (!empty($oldTrx->ang2) && !empty($oldTrx->tgl_ang2)) {
            $installments[] = [
                'amount' => $oldTrx->ang2,
                'date' => $oldTrx->tgl_ang2,
                'collector' => $oldTrx->coll2,
            ];
        }
        
        if (!empty($oldTrx->ang3) && !empty($oldTrx->tgl_ang3)) {
            $installments[] = [
                'amount' => $oldTrx->ang3,
                'date' => $oldTrx->tgl_ang3,
                'collector' => $oldTrx->coll3,
            ];
        }
        
        if (!empty($oldTrx->ang4) && !empty($oldTrx->tgl_ang4)) {
            $installments[] = [
                'amount' => $oldTrx->ang4,
                'date' => $oldTrx->tgl_ang4,
                'collector' => $oldTrx->coll4,
            ];
        }
        
        if (!empty($oldTrx->ang5) && !empty($oldTrx->tgl_ang5)) {
            $installments[] = [
                'amount' => $oldTrx->ang5,
                'date' => $oldTrx->tgl_ang5,
                'collector' => $oldTrx->coll5,
            ];
        }
        
        foreach ($installments as $installment) {
            TransactionInstallment::create([
                'transaction_id' => $transaction->id,
                'installment_amount' => $installment['amount'],
                'payment_date' => $installment['date'],
                'collector_id' => $this->getCollectorId($installment['collector']),
                'created_at' => $oldTrx->created_at ?? now(),
                'updated_at' => $oldTrx->updated_at ?? now(),
            ]);
        }
    }

    protected function getCollectorId($collectorName)
    {
        // Default to admin user (ID 1) if collector not specified
        return 1;
    }

    protected function calculateOutstanding(OldTransaction $oldTrx)
    {
        $totalPaid = 0;
        
        if (!empty($oldTrx->ang1)) $totalPaid += $oldTrx->ang1;
        if (!empty($oldTrx->ang2)) $totalPaid += $oldTrx->ang2;
        if (!empty($oldTrx->ang3)) $totalPaid += $oldTrx->ang3;
        if (!empty($oldTrx->ang4)) $totalPaid += $oldTrx->ang4;
        if (!empty($oldTrx->ang5)) $totalPaid += $oldTrx->ang5;
        
        return ($oldTrx->harga ?? 0) - $totalPaid;
    }
}