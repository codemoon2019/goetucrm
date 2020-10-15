<?php

namespace App\Models;

use App\Traits\ActorTrait;
use Illuminate\Database\Eloquent\Model;

class TicketActivity extends Model
{
    use ActorTrait;
    
    protected $guarded = [];
    protected $dates = [
        'created_at', 
        'updated_at',
        'support_responsed_at',
        'department_responsed_at',
        'started_progress_at',
        'solved_at',
    ];

    /**
     * Accessors and Mutators
     */
    public function getTimeDifference($type, $timestamp)
    {
        switch ($type) {
            case 'department':
                $prevTimestamp = $this->department_responsed_at;
                break;
            case 'solved':
                $prevTimestamp = $this->solved_at;
                break;
            case 'support':
                $prevTimestamp = $this->support_responsed_at;
                break;
        }

        $hourDiff = $timestamp->diffInHours($prevTimestamp);
        $minuteDiff =  $timestamp->diffInMinutes($prevTimestamp) - ($hourDiff * 60);

        return "{$hourDiff}h {$minuteDiff}m";
    }

    public function ticketHeader()
    {
        return $this->belongsTo(TicketHeader::class);
    }
}
