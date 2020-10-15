<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ChatFriendRequest extends Model
{
    protected $table = 'chat_friend_requests';

    public static function get_new_friend_requests_count(){
    	$friendRequest = DB::raw("SELECT count(id) as count FROM chat_friend_requests WHERE recipient = ".auth()->user()->id." AND is_accepted_or_not = 0");

    	$result = DB::select($friendRequest);

    	return $result[0]->count;
    }
}
