<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncCalendar extends Model
{
	protected $table = 'sync_calendars';

	protected $fillable = ['calendar_id'];
}
