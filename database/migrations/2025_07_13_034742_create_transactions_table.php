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
            $table->decimal('deal_price', 20);
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('seller_id')->constrained('users');       
            $table->enum('payment_type', ['installment', 'cash']);
            $table->enum('status', ['paid', 'pending']);
            $table->date('transaction_date'); 
            $table->string('is_tempo')->nullable(); 
            $table->date('tempo_at')->nullable(); 
            $table->text('note')->nullable(); 
            $table->string('is_printed')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
