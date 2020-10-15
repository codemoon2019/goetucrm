<?php

namespace App\Models\Drafts;

use Illuminate\Database\Eloquent\Model;

use App\Traits\NoDashPhoneTrait;
use App\Traits\SavePhoneWithDashTrait;

class DraftPartnerContact extends Model
{
    use NoDashPhoneTrait, SavePhoneWithDashTrait;

    protected $table = 'draft_partner_contacts';

    public function draftPartner() 
    {
        return $this->belongsTo('App\Models\Drafts\DraftPartner','id','draft_partner_id');
    }
}
