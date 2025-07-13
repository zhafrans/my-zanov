<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAmountItem extends Model
{
    protected $guarded = [];

    public function stockAmount()
    {
        return $this->belongsTo(StockAmount::class);
    }

    public function stockType()
    {
        return $this->belongsTo(StockType::class);
    }
}
