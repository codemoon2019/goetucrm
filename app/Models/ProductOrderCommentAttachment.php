<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrderCommentAttachment extends Model
{
    protected $guarded = [];

    public function productOrderComment()
    {
        return $this->belongsTo('App\Models\ProductOrderComment');
    }
}
