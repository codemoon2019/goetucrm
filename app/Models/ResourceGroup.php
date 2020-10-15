<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActorTrait;
class ResourceGroup extends Model
{
    protected $table = 'resource_groups';
    protected $fillable = ['name'];
    
    const STATUS_ACTIVE = 'A';
    
    /**
     * Resource Group Acess
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function resourceGroupAccess()
    {
        return $this->hasMany("App\\Models\\ResourceGroupAccess", "resource_group_id", "id");
    }

    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    | Local scopes allow you to define common sets of constraints that you may 
    | easily re-use throughout your application.
    |
    | Write local scopes below
    |
    */
    public function scopeActive($query)
    {
        return $query->where('status', ResourceGroup::STATUS_ACTIVE);
    }

}
