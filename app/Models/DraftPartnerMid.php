<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftPartnerMid extends Model
{
    protected $table = 'draft_partner_mids';


    public function system()
    {
        return $this->hasOne('App\Models\PartnerSystem', 'id', 'system_id');
    }
}
