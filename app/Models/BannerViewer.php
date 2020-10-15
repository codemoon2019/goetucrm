<?php

namespace App\Models;

use App\Traits\ActorTrait;
use App\User;
use Illuminate\Database\Eloquent\Model;

class BannerViewer extends Model
{
    use ActorTrait;

    const VIEWER_TYPE_ALL = 'A';
    const VIEWER_TYPE_COMPANY = 'C';
    const VIEWER_TYPE_DEPARTMENT = 'D';
    const VIEWER_TYPE_USER = 'U';
    const VIEWER_TYPES = [
        BannerViewer::VIEWER_TYPE_ALL => 'All',
        BannerViewer::VIEWER_TYPE_COMPANY => 'Companies', 
        BannerViewer::VIEWER_TYPE_DEPARTMENT => 'Departments', 
        BannerViewer::VIEWER_TYPE_USER => 'Users'
    ];

    const STATUS_ACTIVE = 'A';
    const STATUS_DELETED = 'D';
    const STATUSES = [
        Banner::STATUS_ACTIVE => 'Active', 
        Banner::STATUS_DELETED => 'Deleted'
    ];

    protected $guarded = [];
    protected $dates = [
        'starts_at', 
        'ends_at', 
        'created_at', 
        'updated_at'
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessor and Mutators
    |--------------------------------------------------------------------------
    | Accessors and mutators allow you to format Eloquent attribute values when 
    | you retrieve or set them on model instances.
    |
    | Write accessor and mutators below
    |
    */
    public function getStatusReadableAttribute()
    {
        return BannerViewer::STATUSES[$this->status];
    }

    public function getViewerTypeReadableAttribute()
    {
        return BannerViewer::VIEWER_TYPES[$this->viewer_type];
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
    public function scopeViewableBy($query, User $user)
    {
        return $query->where(function($query) use ($user) {
                $query->where('viewer_type', BannerViewer::VIEWER_TYPE_COMPANY)
                    ->where('viewer_id', $user->company_id);
            })->orWhere(function($query) use ($user) {
                $query->where('viewer_type', BannerViewer::VIEWER_TYPE_DEPARTMENT)
                    ->whereIn('viewer_id', explode(',', $user->user_type_ids));
            })->orWhere(function($query) use ($user) {
                $query->where('viewer_type', BannerViewer::VIEWER_TYPE_USER)
                    ->where('viewer_id', $user->id);
            })->orWhere('viewer_type', BannerViewer::VIEWER_TYPE_ALL);
    }

    
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Relationships allows you to define table relationships as methods 
    | on your Eloquent model classes. 
    |
    | Write relationships below 
    |
    */
    public function banner()
    {
        return $this->belongsTo('App\Models\Banner');
    }
}
