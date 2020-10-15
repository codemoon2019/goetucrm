<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    protected $table = 'invoice_payments';

    protected $guarded = [];

    public function type()
    {
       return $this->hasOne('App\Models\PaymentType','id','payment_type_id');
    }

    public function invoiceHeader()
    {
       return $this->belongsTo('App\Models\InvoiceHeader', 'invoice_id', 'id');
    }
}
