<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_amount_id')->constrained('stock_amounts');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->integer('quantity');
            $table->enum('type', ['in', 'out']);
            $table->boolean('is_adjustment')->default(false);
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses');
            $table->enum('destination', ['lost', 'transfer', 'add', 'sold'])->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions');
            $table->text('note')->nullable();
            $table->integer('quantity_before')->nullable();
            $table->integer('quantity_after')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
