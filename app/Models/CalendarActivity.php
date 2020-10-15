<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CalendarActivity extends Model
{
	protected $table = 'calendar_activities';
	
	protected $fillable = ['event_id'];

    public static function get_max_calendar_activity_id(){
    	$records = DB::table('calendar_activities')
    		->select(DB::raw('max(id) as ctrID'))
    		->where('user_id',auth()->user()->id)
    		->first();
    	return $records->ctrID == null ? 0 : $records->ctrID;
    } 
}
