<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTemplateHeader extends Model
{
    protected $table = 'product_template_headers';

    public function details()
    {
        return $this->hasMany('App\Models\ProductTemplateDetail','template_id','id');
    }

    public function partner()
    {
        return $this->hasOne('App\Models\Partner','id','partner_id');
    }

    public function product_type()
    {
        return $this->hasOne('App\Models\ProductType','id','product_type_id');
    }


}