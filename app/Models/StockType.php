<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockType extends Model
{
    protected $guarded = [];

    public function stockAmountItems()
    {
        return $this->hasMany(StockAmountItem::class);
    }

}
