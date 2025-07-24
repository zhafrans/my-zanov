<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('base_code')->nullable()->unique();
            $table->string('code')->nullable()->unique();
            $table->string('other_code')->nullable()->unique();
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->foreignId('color_id')->nullable()->constrained('colors');
            $table->foreignId('size_id')->nullable()->constrained('sizes');
            $table->foreignId('heel_id')->nullable()->constrained('heels');
            $table->enum('gender', ['man', 'woman'])->nullable();
            $table->string('image')->nullable();
            $table->string('price')->nullable();
            $table->string('installment_price')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
