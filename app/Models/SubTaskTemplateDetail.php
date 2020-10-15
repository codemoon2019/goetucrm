<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTaskTemplateDetail extends Model
{
    protected $table = 'sub_task_template_details';
    protected $guarded = [];

    public function getProductTagsAttribute($value)
    {
        return json_decode($value);
    }

    public function prerequisite_id()
    {
       return $this->hasOne('App\Models\SubTaskTemplateDetail','line_number','prerequisite');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\UserType', 'department_id');
    }
}
