<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActiveTrait;
use App\Traits\ActorTrait;

class TicketPriority extends Model
{
    use ActiveTrait, ActorTrait;
    
    const LOW_ID = 1;
    const MEDIUM_ID = 2;
    const HIGH_ID = 3;
    const URGENT_ID = 4;

    const LOW = 'L';
    const MEDIUM = 'M';
    const HIGH = 'H';
    const URGENT = 'U';

    protected $table = 'ticket_priorities';
    protected $guarded = [];
}
