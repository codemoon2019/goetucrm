<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTemplate extends Model
{
    protected $table = 'user_templates';
    protected $guarded = [];
    protected $fillable = ['resource_id'];

    public function resources()
    {
        return $this->hasMany("App\\Models\\Resource", "id", "resource_id");
    }

    public function scopeIsInternal($query)
    {
        return $query->where('resource_id', '=', 156);
    }

    public function scopeIsPartnerOrMerchant($query)
    {
        return $query->where('resource_id', '<>', 156);
    }

    public function scopeWithAssignAccess($query)
    {
        return $query->where('resource_id', '=', 159);
    }

    public function scopeWithEditWorkflowAccess($query)
    {
        return $query->where('resource_id', 284);
    }
}
