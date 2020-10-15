<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PartnerAttachment;

class Document extends Model
{
    protected $table = 'documents';

    public function partner_attachment($id)
    {
        return PartnerAttachment::where("document_id", $this->id)->where("partner_id", $id)->get();
    }

}
