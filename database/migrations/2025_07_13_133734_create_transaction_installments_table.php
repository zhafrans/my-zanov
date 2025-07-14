<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->decimal('installment_amount', 20);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_installments');
    }
};
