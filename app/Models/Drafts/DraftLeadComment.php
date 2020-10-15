<?php

namespace App\Models\Drafts;

use Illuminate\Database\Eloquent\Model;

class DraftLeadComment extends Model
{
    protected $table = 'draft_lead_comments';

    public function draftPartner() 
    {
        return $this->belongsTo('App\Models\Drafts\DraftLeadComment','id','draft_partner_id');
    }
}
