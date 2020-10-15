<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantWelcomeEmail extends Model
{
    protected $table = 'merchant_welcome_emails';


    public function product()
    {
        return $this->hasOne('App\Models\Product','id','product_id');
    }

}
