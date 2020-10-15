<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'resources';
    protected $guarded = [];

    /**
     * Relationships
     */

    public function userTypes()
    {
        return $this->belongsToMany('App\Models\UserType', 'user_templates', 
            'resource_id', 'user_type_id');
    }

    public function userTemplates()
    {
        return $this->hasMany('App\Models\UserTemplate', 'resource_id', 'id');
    }

    /**
     * Scopes
     */

    public function scopeIsInternal($query)
    {
        return $query->where('resource_id', '=', 156);
    }
    
}
