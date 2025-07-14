<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'id', 'product_id');
    }
}
