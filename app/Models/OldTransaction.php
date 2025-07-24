<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldTransaction extends Model
{
    protected $connection = 'mysql_old'; // koneksi database lama
    protected $table = 'sales'; // tabel di database lama


    protected $casts = [
    'transaction_date' => 'datetime',
    'tempo_at' => 'datetime',
];
}
