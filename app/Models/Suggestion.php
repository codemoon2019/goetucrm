<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    protected $table = 'suggestions';

    public function user()
    {
        return $this->hasOne('App\Models\User','username','create_by');
    }

}
