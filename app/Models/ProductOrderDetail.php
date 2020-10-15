<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrderDetail extends Model
{
    protected $table = 'product_order_details';

    public function product()
    {
       return $this->hasOne('App\Models\Product','id','product_id');
    }


}
