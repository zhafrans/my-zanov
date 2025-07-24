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

        // Get all old transactions
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
                
                // Continue to next transaction even if this one fails
                continue;
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
        // Use no_kartu as invoice directly (allow duplicates)
        $invoiceNumber = $oldTrx->no_kartu ?? 'INV' . $oldTrx->id;

        // Create or find customer with robust fallbacks
        $customer = $this->createCustomer($oldTrx);

        // Determine payment type with fallback
        $paymentType = $this->determinePaymentType($oldTrx);
        
        // Determine status with fallback
        $status = $this->determineStatus($oldTrx);

        // Create transaction with robust fallbacks
        $transaction = Transaction::create([
            'invoice' => $invoiceNumber,
            'deal_price' => $this->getNumericValue($oldTrx->harga, 0),
            'customer_id' => $customer->id,
            'seller_id' => $this->getSalesId($oldTrx->nama_sales ?? ''),
            'payment_type' => $paymentType,
            'status' => $status,
            'transaction_date' => $this->getValidDate($oldTrx->tgl_pengambilan ?? $oldTrx->tgl_ang1),
            'is_tempo' => $this->hasMultipleInstallments($oldTrx) ? '1' : null,
            'tempo_at' => $this->getValidDate($oldTrx->tgl_pengambilan ?? $oldTrx->tgl_ang1, true),
            'note' => $oldTrx->ket ?? null,
            'is_printed' => $oldTrx->is_created ?? 0,
            'created_at' => $this->getValidDate($oldTrx->created_at),
            'updated_at' => $this->getValidDate($oldTrx->updated_at),
        ]);

        // Create product variant and transaction item with robust fallbacks
        $this->createProductAndItem($oldTrx, $transaction);

        // Process installments if applicable
        if ($paymentType === 'installment') {
            $this->processInstallments($oldTrx, $transaction);
        }

        // Calculate outstanding amount
        $outstandingAmount = $this->calculateOutstanding($oldTrx);
        
        // Always create outstanding record
        TransactionOutstanding::create([
            'transaction_id' => $transaction->id,
            'outstanding_amount' => $outstandingAmount,
            'created_at' => $this->getValidDate($oldTrx->created_at),
            'updated_at' => $this->getValidDate($oldTrx->updated_at),
        ]);

        // Update transaction status if fully paid
        if ($outstandingAmount <= 0) {
            $transaction->update(['status' => 'paid']);
        }
    }

    protected function determinePaymentType($oldTrx)
    {
        $ket = strtoupper($oldTrx->ket ?? '');
        if (strpos($ket, 'CASH') !== false) {
            return 'cash';
        }
        
        // If there are any installment payments, it's installment
        if (!empty($oldTrx->ang1) || !empty($oldTrx->ang2) || !empty($oldTrx->ang3) || 
            !empty($oldTrx->ang4) || !empty($oldTrx->ang5)) {
            return 'installment';
        }
        
        // Default to cash if no installments
        return 'cash';
    }

    protected function getNumericValue($value, $default = 0)
    {
        if (is_numeric($value)) {
            return $value;
        }
        
        // Try to extract numeric value from string
        if (is_string($value)) {
            preg_match('/\d+/', $value, $matches);
            if (isset($matches[0])) {
                return (float)$matches[0];
            }
        }
        
        return $default;
    }

    protected function getValidDate($date, $addMonth = false)
    {
        try {
            $date = Carbon::parse($date);
            if ($addMonth) {
                $date = $date->addMonth();
            }
            return $date;
        } catch (\Exception $e) {
            return now();
        }
    }

    protected function createCustomer(OldTransaction $oldTrx)
    {
        $customerName = empty(trim($oldTrx->nama ?? '')) ? 'UNKNOWN_' . $oldTrx->id : $oldTrx->nama;
        $customerCode = $this->generateCustomerCode($oldTrx->no_kartu ?? null, $customerName);
        $address = $oldTrx->alamat ?? 'UNKNOWN_ADDRESS';
        $phone = $oldTrx->no_telp ?? '000000000';

        // Cari customer dengan alamat yang sama persis
        $existingCustomer = Customer::where('address', $address)
            ->where('phone', $phone) // Tambahkan phone sebagai kriteria tambahan
            ->first();

        // Jika tidak ada yang cocok persis, coba cari yang mirip (tanpa memperhatikan phone)
        if (!$existingCustomer) {
            $existingCustomer = Customer::where('address', $address)->first();
        }

        // Jika masih tidak ada, coba cari berdasarkan nama dan alamat yang mengandung string yang sama
        if (!$existingCustomer) {
            $existingCustomer = Customer::where('name', $customerName)
                ->where('address', 'like', '%' . $address . '%')
                ->first();
        }

        // Jika menemukan customer yang cocok, gunakan yang sudah ada
        if ($existingCustomer) {
            return $existingCustomer;
        }

        // Jika tidak ada yang cocok, buat customer baru
        return Customer::create([
            'code' => $customerCode,
            'name' => $customerName,
            'address' => $address,
            'phone' => $phone,
            'village_id' => null,
            'subdistrict_id' => $this->findSubdistrictId($oldTrx->kecamatan ?? null),
            'city_id' => $this->findCityId($oldTrx->kabupaten ?? null),
            'province_id' => null,
            'created_at' => $this->getValidDate($oldTrx->created_at),
            'updated_at' => $this->getValidDate($oldTrx->updated_at),
        ]);
    }

    protected function findSubdistrictId($subdistrictName)
    {
        if (empty($subdistrictName)) {
            return null;
        }

        // Cari dengan LIKE karena format mungkin berbeda (misal: "KEC. XXX" vs "XXX")
        $subdistrict = Subdistrict::where('name', 'like', '%' . $subdistrictName . '%')->first();
        
        return $subdistrict ? $subdistrict->id : null;
    }

    protected function findCityId($cityName)
    {
        if (empty($cityName)) {
            return null;
        }

        // Handle kemungkinan format berbeda (misal: "KAB. CILACAP" vs "CILACAP")
        $city = City::where('name', 'like', '%' . str_replace('KAB.', '', $cityName) . '%')->first();
        
        return $city ? $city->id : null;
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
        $ket = strtoupper($oldTrx->ket ?? '');
        if (strpos($ket, 'LUNAS') !== false || strpos($ket, 'CASH') !== false) {
            return 'paid';
        }
        
        // If outstanding is 0, mark as paid
        $outstanding = $this->calculateOutstanding($oldTrx);
        if ($outstanding <= 0) {
            return 'paid';
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
        $colorName = !empty($oldTrx->warna) ? $oldTrx->warna : 'UNKNOWN_' . $transaction->id;
        $sizeName = !empty($oldTrx->size) ? $oldTrx->size : 'UNKNOWN_SIZE';
        $productName = !empty($oldTrx->nama_produk) ? $oldTrx->nama_produk : 'UNKNOWN_PRODUCT_' . $transaction->id;

        $color = Color::firstOrCreate(
            ['name' => $colorName],
            ['code' => $this->generateUniqueColorCode($colorName)]
        );
        
        $size = Size::firstOrCreate(
            ['name' => $sizeName],
            ['code' => $this->generateUniqueSizeCode($sizeName)]
        );
        
        $productVariant = ProductVariant::firstOrCreate(
            ['other_code' => $productName],
            [
                'base_code' => 'PROD_' . Str::random(8),
                'code' => null,
                'other_code' => $productName,
                'product_id' => null,
                'color_id' => $color->id,
                'size_id' => $size->id ?? null,
                'heel_id' => null,
                'gender' => null,
                'image' => null,
                'price' => $this->getNumericValue($oldTrx->harga, 0),
                'installment_price' => null,
            ]
        );
        
        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_variant_id' => $productVariant->id,
            'stock_type_id' => null,
            'quantity' => 1,
            'snapshot_name' => $productName . ' ' . $colorName . ' ' . $sizeName,
            'snapshot_price' => $this->getNumericValue($oldTrx->harga, 0),
            'created_at' => $this->getValidDate($oldTrx->created_at),
            'updated_at' => $this->getValidDate($oldTrx->updated_at),
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