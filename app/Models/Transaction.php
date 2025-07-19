<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function installments()
    {
        return $this->hasMany(TransactionInstallment::class);
    }

    public function outstanding()
    {
        return $this->hasOne(TransactionOutstanding::class);
    }
}