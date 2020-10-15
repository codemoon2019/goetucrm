<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTypeReference extends Model
{
    protected $table = 'user_type_references';

    public function user_type()
    {
       return $this->hasOne('App\Models\UserType','id','user_type_id');
    }

}
