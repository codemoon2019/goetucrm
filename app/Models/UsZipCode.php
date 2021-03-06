<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsZipCode extends Model
{
	protected $table = 'us_zip_codes';

    protected $fillable = [
        'zip_code', 
        'type',
        'city',
        'state_id',
        'country_id',
        'is_primary_city',
        'is_acceptable_city',
        'county',
    ];

    public function country()
    {
        return $this->belongsTo('App\\Models\\Country', 'country_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo('App\\Models\\State', 'state_id', 'id');
    }
}
