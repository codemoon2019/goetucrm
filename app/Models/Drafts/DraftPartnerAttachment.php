<?php

namespace App\Models\Drafts;

use Illuminate\Database\Eloquent\Model;

class DraftPartnerAttachment extends Model
{
    protected $table = 'draft_partner_attachments';

    public function draftPartner() 
    {
        return $this->belongsTo('App\Models\Drafts\DraftPartner','id','draft_partner_id');
    }
}
