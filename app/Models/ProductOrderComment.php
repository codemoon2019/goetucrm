<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrderComment extends Model
{
    protected $table = 'product_order_comments';
    protected $guarded = [];

    public function user()
    {
       return $this->hasOne('App\Models\User','id','user_id');
    }

    public function partner()
    {
       return $this->hasOne('App\Models\Partner','id','partner_id');
    }

    public function subTaskDetail()
    {
        return $this->belongsTo('App\Models\SubTaskDetail');
    }

    public function productOrderCommentAttachments()
    {
        return $this->hasMany('App\Models\ProductOrderCommentAttachment');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\ProductOrderComment', 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany('App\Models\ProductOrderComment', 'parent_id');
    }


    /** Many to Many relationship to User */
    public function viewers()
    {
        $table = 'product_order_comment_viewer';

        return $this->belongsToMany('App\Models\User', $table)
                    ->withTimeStamps();
    }

    /** Many to Many relationship to User */
    public function emailReceivers()
    {
        $table = 'product_order_comment_email_receiver';
        
        return $this->belongsToMany('App\Models\User', $table)
                    ->withTimeStamps();
    }
}
