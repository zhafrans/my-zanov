<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->text('address');
            $table->string('phone');
            $table->foreignId('village_id')->nullable()->constrained('villages');
            $table->foreignId('subdistrict_id')->nullable()->constrained('subdistricts');
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->foreignId('province_id')->nullable()->constrained('provinces');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
