<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';

    public function agentApplicants()
    {
        return $this->hasMany('App\\Models\\AgentApplicant', 
            'state_id', 'id');
    }

    public function zips() {
        return $this->hasMany('App\\Models\\UsZipCode', 
            'state_id', 'id');
    }
    
    public function zipcn() {
        return $this->hasMany('App\\Models\\CnZipCode', 
            'state_id', 'id');
    }

    public function zipph() {
        return $this->hasMany('App\\Models\\PhZipCode', 
            'state_id', 'id');
    }
}
