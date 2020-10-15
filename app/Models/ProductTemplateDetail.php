<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTemplateDetail extends Model
{
    protected $table = 'product_template_details';

    public function product()
    {
        return $this->hasOne('App\Models\Product','id','product_id');
    }

    public function frequency()
    {
        return $this->hasOne('App\Models\PaymentFrequency','name','payment_frequency');
    }

}
