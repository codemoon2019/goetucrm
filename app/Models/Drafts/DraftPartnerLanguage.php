<?php

namespace App\Models\Drafts;

use Illuminate\Database\Eloquent\Model;

class DraftPartnerLanguage extends Model
{
    protected $table = 'draft_partner_languages';

    public function draftPartner()
    {
        return $this->belongsTo('App\Models\Drafts\DraftPartner', 'draft_partner_id', 'id');
    }
}
