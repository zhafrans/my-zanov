<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $guarded = [];

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
