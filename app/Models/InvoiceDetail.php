<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = 'invoice_details';

    protected $guarded = [];
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function Partners()
    {
        return $this->belongsTo('App\Models\Partners');
    }

    public function InvoiceHeaders()
    {
        return $this->belongsTo('App\Models\InvoiceHeader');
    }
}
