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

    // Cache for user lookups
    protected $userCache = [];

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
        
        // Log all product codes for manual mapping
        $this->logProductCodes();
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

        // Check for CASH TEMPO in the note
        $isTempo = $this->hasMultipleInstallments($oldTrx) || 
                (strpos(strtoupper($oldTrx->ket ?? ''), 'CASH TEMPO') !== false) ? '1' : null;

        // Create transaction with robust fallbacks
        $transaction = Transaction::create([
            'invoice' => $invoiceNumber,
            'deal_price' => $this->getNumericValue($oldTrx->harga, 0),
            'customer_id' => $customer->id,
            'seller_id' => $this->getSalesId($oldTrx->nama_sales ?? ''),
            'payment_type' => $paymentType,
            'status' => $status,
            'transaction_date' => $this->getValidDate($oldTrx->tgl_pengambilan ?? $oldTrx->tgl_ang1),
            'is_tempo' => $isTempo,
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

    protected function getSalesId($salesName)
    {
        $salesName = strtoupper(trim($salesName));
        
        // Handle special cases
        if (empty($salesName) || $salesName === 'DP' || $salesName === 'CASH') {
            return null;
        }

        // Check cache first
        if (isset($this->userCache[$salesName])) {
            return $this->userCache[$salesName];
        }

        // Find user with SALES role and matching name
        $user = User::where('name', $salesName)
            ->whereHas('role', function($query) {
                $query->where('name', 'SALES');
            })
            ->first();

        if ($user) {
            $this->userCache[$salesName] = $user->id;
            return $user->id;
        }

        // If not found, try to find any user with matching name
        $user = User::where('name', $salesName)->first();
        
        if ($user) {
            $this->userCache[$salesName] = $user->id;
            return $user->id;
        }

        // Default to null if not found
        return null;
    }

    protected function getCollectorId($collectorName)
    {
        $collectorName = strtoupper(trim($collectorName));
        
        if (empty($collectorName)) {
            return null;
        }

        // Check cache first
        if (isset($this->userCache['coll_'.$collectorName])) {
            return $this->userCache['coll_'.$collectorName];
        }

        // Find user with COLLECTOR role and matching name
        $user = User::where('name', $collectorName)
            ->whereHas('role', function($query) {
                $query->where('name', 'COLLECTOR');
            })
            ->first();

        if ($user) {
            $this->userCache['coll_'.$collectorName] = $user->id;
            return $user->id;
        }

        // If not found, try to find any user with matching name
        $user = User::where('name', $collectorName)->first();
        
        if ($user) {
            $this->userCache['coll_'.$collectorName] = $user->id;
            return $user->id;
        }

        // Default to null if not found
        return null;
    }

    protected function determinePaymentType($oldTrx)
    {
        $ket = strtoupper($oldTrx->ket ?? '');
        $outstanding = $this->calculateOutstanding($oldTrx);
        $transactionDate = $this->getValidDate($oldTrx->tgl_pengambilan ?? $oldTrx->tgl_ang1);
        
        // Consider as cash if:
        // 1. No outstanding amount AND
        // 2. (Marked as CASH or LUNAS or paid on transaction date)
        if ($outstanding <= 0 && 
            (strpos($ket, 'CASH') !== false || 
            strpos($ket, 'LUNAS') !== false ||
            $this->isFullyPaidOnTransactionDate($oldTrx))) {
            return 'cash';
        }
        
        // All other cases should be considered as installment
        return 'installment';
    }

    protected function isFullyPaidOnTransactionDate(OldTransaction $oldTrx)
    {
        $transactionDate = $this->getValidDate($oldTrx->tgl_pengambilan ?? $oldTrx->tgl_ang1);
        $outstanding = $this->calculateOutstanding($oldTrx);
        
        // Check if there are any installment payments made on transaction date
        $hasSameDayPayment = false;
        
        if (!empty($oldTrx->ang1) && $this->getValidDate($oldTrx->tgl_ang1) == $transactionDate) {
            $hasSameDayPayment = true;
        }
        if (!empty($oldTrx->ang2) && $this->getValidDate($oldTrx->tgl_ang2) == $transactionDate) {
            $hasSameDayPayment = true;
        }
        // ... check ang3, ang4, ang5 similarly if needed
        
        return $outstanding <= 0 && $hasSameDayPayment;
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
        // Ambil nama persis dari database lama, termasuk slash atau karakter khusus
        $customerName = $oldTrx->nama ?? 'UNKNOWN_' . $oldTrx->id;
        
        // Jika string kosong setelah trim, gunakan fallback
        if (empty(trim($customerName))) {
            $customerName = 'UNKNOWN_' . $oldTrx->id;
        }
        
        $customerCode = $this->generateCustomerCode($oldTrx->no_kartu ?? null, $customerName);
        $address = $oldTrx->alamat ?? 'UNKNOWN_ADDRESS';
        $phone = $oldTrx->no_telp ?? '000000000';

        // Cari customer dengan alamat yang sama persis
        $existingCustomer = Customer::where('address', $address)
            ->where('phone', $phone)
            ->first();

        // Jika tidak ada yang cocok persis, coba cari yang mirip
        if (!$existingCustomer) {
            $existingCustomer = Customer::where('address', $address)->first();
        }

        if (!$existingCustomer) {
            $existingCustomer = Customer::where('name', $customerName)
                ->where('address', 'like', '%' . $address . '%')
                ->first();
        }

        // Jika menemukan customer yang cocok, gunakan yang sudah ada
        if ($existingCustomer) {
            return $existingCustomer;
        }

        // Cari ID wilayah
        $cityId = $this->findCityId($oldTrx->kabupaten ?? null);
        $subdistrictId = $this->findSubdistrictId($oldTrx->kecamatan ?? null);
        $provinceId = $this->findProvinceId($cityId);

        // Jika tidak ada yang cocok, buat customer baru
        return Customer::create([
            'code' => $customerCode,
            'name' => $customerName,
            'address' => $address,
            'phone' => $phone,
            'village_id' => null,
            'subdistrict_id' => $subdistrictId,
            'city_id' => $cityId,
            'province_id' => $provinceId,
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

    protected function findProvinceId($cityId)
    {
        if (empty($cityId)) {
            return null;
        }

        $city = City::find($cityId);
        if ($city && $city->province_id) {
            return $city->province_id;
        }

        return null;
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

    protected function determineStatus(OldTransaction $oldTrx)
    {
        $outstanding = $this->calculateOutstanding($oldTrx);
        
        // Jika lunas (outstanding <= 0)
        if ($outstanding <= 0) {
            return 'paid';
        }
        
        // Semua kasus lainnya dianggap pending
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
        
        // Consider as tempo if multiple installments OR has CASH TEMPO note
        return $count > 1 || (strpos(strtoupper($oldTrx->ket ?? ''), 'CASH TEMPO')) !== false;
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

    protected function logProductCodes()
    {
        // Get all unique other_codes from product_variants
        $productCodes = ProductVariant::whereNotNull('other_code')
                        ->where('other_code', '!=', '')
                        ->distinct()
                        ->pluck('other_code')
                        ->toArray();

        // Prepare log directory
        $logDirectory = storage_path('logs/migrations/');
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        // Create log file
        $logFileName = 'product_codes_' . now()->format('Ymd_His') . '.log';
        $logPath = $logDirectory . $logFileName;

        // Format the data for better readability
        $logData = [
            'timestamp' => now()->toDateTimeString(),
            'total_unique_codes' => count($productCodes),
            'product_codes' => $productCodes,
            'mapping_suggestion' => 'You can use these codes to create products in the products table'
        ];

        // Write to log file
        file_put_contents($logPath, json_encode($logData, JSON_PRETTY_PRINT));

        $this->info("\nProduct codes log saved to: {$logPath}");
    }
}