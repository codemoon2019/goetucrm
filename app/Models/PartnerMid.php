<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerMid extends Model
{
    protected $table = 'partner_mids';


    public function system()
    {
        return $this->hasOne('App\Models\PartnerSystem', 'id', 'system_id');
    }

}
