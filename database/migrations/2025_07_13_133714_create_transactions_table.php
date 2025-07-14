<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('product_variant_id')->constrained('product_variants');
            $table->foreignId('seller_id')->constrained('users');       
            $table->enum('payment_type', ['credit', 'cash']);
            $table->enum('status', ['paid', 'installment']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
