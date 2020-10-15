<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelcomeEmailTemplate extends Model
{
    protected $table = 'welcome_email_templates';


    public function product()
    {
        return $this->hasOne('App\Models\Product','id','product_id');
    }
}
