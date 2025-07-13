<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $guarded = [];

    public function stockAmount()
    {
        return $this->hasOne(StockAmount::class);
    }
}
