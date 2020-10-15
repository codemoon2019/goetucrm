<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceFrequency extends Model
{
    protected $table = 'invoice_frequencies';

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }


    public function sub_product()
    {
       return $this->hasOne('App\Models\Product','id','product_id');
    }


}
