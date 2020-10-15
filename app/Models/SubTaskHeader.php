<?php

namespace App\Models;

use App\Models\SubTaskDetail;
use Illuminate\Database\Eloquent\Model;

class SubTaskHeader extends Model
{
    protected $table = 'sub_task_headers';
    protected $guarded = [];
    
    const STATUS_ACTIVE = 'A';

    /*
    |--------------------------------------------------------------------------
    | Accessor and Mutators
    |--------------------------------------------------------------------------
    |
    | Write accessor and mutators below
    |
    */
    public function getCompletionRatioAttribute()
    {
        $subtaskCount = $this->subtasks->count();
        $completedSubtaskCount = $this->subtasks
            ->where('status', 'C')
            ->count();

        $ipSubtaskCount = $this->subtasks
            ->where('status', 'I')
            ->count();

        return "{$completedSubtaskCount} out of {$subtaskCount} Tasks " . 
               "({$ipSubtaskCount} In Progress)";
    }

    public function getCompletedSubtasksCountAttribute()
    {
        return $this->subtasks
            ->where('status', 'C')
            ->count();
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
    public function productOrder()
    {
        return $this->belongsTo(ProductOrder::class, 'order_id', 'id');
    }

    public function subtasks()
    {
        return $this->hasMany(SubTaskDetail::class, 'sub_task_id', 'id');
    }
}
