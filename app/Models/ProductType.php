<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    const PRODUCT_TYPE_STATUS_ACTIVE = "A";

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'product_types';

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'product_type_id', 'id');
    }
}
