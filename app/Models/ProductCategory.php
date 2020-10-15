<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
