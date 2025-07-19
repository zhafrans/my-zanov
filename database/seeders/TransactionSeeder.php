<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionInstallment;
use App\Models\TransactionOutstanding;
use App\Models\User;
use App\Models\Customer;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::pluck('id')->toArray();
        $sellers = User::pluck('id')->toArray();
        $variants = ProductVariant::with('product')->get();

        if (empty($customers) || empty($sellers) || $variants->isEmpty()) {
            $this->command->warn("Seeder gagal: Pastikan tabel customers, users, dan product_variants sudah memiliki data.");
            return;
        }

        for ($i = 0; $i < 50; $i++) {
            $paymentType = fake()->randomElement(['installment', 'cash']);
            $status = $paymentType === 'cash' ? 'paid' : fake()->randomElement(['paid', 'pending']);

            $variantMain = $variants->random();

            // Buat transaksi utama
            $transaction = Transaction::create([
                'invoice'            => 'INV-' . strtoupper(Str::random(8)),
                'deal_price'         => fake()->numberBetween(100000, 200000),
                'customer_id'        => fake()->randomElement($customers),
                'seller_id'          => fake()->randomElement($sellers),
                'payment_type'       => $paymentType,
                'status'             => $status,
                'transaction_date'   => now(),
            ]);

            // Buat item-item yang dibeli (1-3)
            $itemCount = rand(1, 3);
            $total = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $variant = $variants->random();
                $quantity = rand(1, 3);
                $price = $variant->price ?? fake()->numberBetween(10000, 50000);
                $subtotal = $price * $quantity;

                TransactionItem::create([
                    'transaction_id'     => $transaction->id,
                    'product_variant_id' => $variant->id,
                    'quantity'           => $quantity,
                    'snapshot_name'      => $variant->name ?? 'Variant ' . $variant->id,
                    'snapshot_price'     => $price,
                ]);

                $total += $subtotal;
            }

            // Jika statusnya installment, buat angsuran dan tagihan sisa
            if ($status === 'installment') {
                $installmentCount = rand(2, 4);
                $paidAmount = 0;

                for ($k = 1; $k <= $installmentCount; $k++) {
                    $amount = floor($total / $installmentCount);
                    if ($k === $installmentCount) {
                        $amount = $total - $paidAmount;
                    }

                    TransactionInstallment::create([
                        'transaction_id'     => $transaction->id,
                        'installment_amount' => $amount,
                    ]);

                    $paidAmount += $amount;
                }

                if ($paidAmount < $total) {
                    TransactionOutstanding::create([
                        'transaction_id'     => $transaction->id,
                        'outstanding_amount' => $total - $paidAmount,
                    ]);
                }
            }
        }
    }
}
