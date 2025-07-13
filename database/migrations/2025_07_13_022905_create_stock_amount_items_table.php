<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_amount_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_amount_id')->constrained('stock_amounts');
            $table->foreignId('stock_type_id')->constrained('stock_types');
            $table->integer('amount')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_amount_items');
    }
};
