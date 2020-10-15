<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTaskDetail extends Model
{
    protected $table = 'sub_task_details';
    protected $dates = ['created_at', 'updated_at', 'due_date'];
    protected $guarded = [];

    protected $prereqSubtaskDetail;

    const STATUSES = [
        'T' => 'To do',
        'I' => 'In progress',
        'P' => 'Pending',
        'C' => 'Completed', 
        'V' => 'Cancelled',
    ];


    /*
    |--------------------------------------------------------------------------
    | Accessor and Mutators
    |--------------------------------------------------------------------------
    |
    | Write accessor and mutators below
    |
    */
    public function getPrereqSubtaskDetailAttribute()
    {
        return SubTaskDetail::where('sub_task_id', $this->sub_task_id)
            ->where('task_no', $this->prerequisite)
            ->first();
    }

    public function getPrerequisiteSubtaskAttribute()
    {
        return SubTaskDetail::where('sub_task_id', $this->sub_task_id)
            ->where('task_no', $this->prerequisite)
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    |
    | Write local scopes below
    |
    */

    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Write relationships below 
    |
    */
    public function department()
    {
        return $this->belongsTo(UserType::class, 'department_id');
    }

    public function productOrderComments()
    {
        return $this->hasMany('App\Models\ProductOrderComment');
    }

    public function task()
    {
        return $this->belongsTo(SubTaskHeader::class, 'sub_task_id', 'id');
    }

    public function ticketHeader()
    {
        return $this->hasOne(TicketHeader::class);
    }
}
