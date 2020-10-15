<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantStatus extends Model
{
    const BOARDING_ID = 1;
    const FOR_APPROVAL_ID = 2;
    const BOARDED_ID = 3;
    const LIVE_ID = 4;
    const DECLINED_ID = 5;
    const CANCELLED_ID = 6;
    
    public function partner()
    {
        return $this->hasMany('App\Models\Partner');
    }
}
