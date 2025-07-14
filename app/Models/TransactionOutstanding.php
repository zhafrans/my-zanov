<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionOutstanding extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'outstanding_amount'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}