<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function agentApplicants()
    {
        return $this->hasMany(AgentApplicant::class);
    }
    
    public function states()
    {
        return $this->hasMany(State::class, 'country', 'iso_code_2');
    }

    public function zips()
    {
        return $this->hasMany(UsZipCode::class, 'country_id', 'id');
    }
    
    public function zipcn()
    {
        return $this->hasMany(CnZipCode::class, 'country_id', 'id');
    }

    public function zipph()
    {
        return $this->hasMany(PhZipCode::class, 'country_id', 'id');
    }
}
