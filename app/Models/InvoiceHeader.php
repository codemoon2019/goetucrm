<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceHeader extends Model
{
    protected $table = 'invoice_headers';

    protected $guarded = [];

    public function details()
    {
       return $this->hasMany('App\Models\InvoiceDetail','invoice_id','id');
    }

    public function payment()
    {
       return $this->hasOne('App\Models\InvoicePayment','invoice_id','id');
    }

    public function status_code()
    {
       return $this->hasOne('App\Models\InvoiceStatus','code','status');
    }

    public function partner()
    {
       return $this->hasOne('App\Models\Partner','id','partner_id');
    }

    public function productOrders()
    {
        return $this->belongsTo('App\Models\ProductOrder');
    }

    public function partnerCompany()
    {
        return $this->belongsTo("App\Models\PartnerCompany", "partner_id", "partner_id");
    }

}
