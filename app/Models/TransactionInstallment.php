<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'installment_amount'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}