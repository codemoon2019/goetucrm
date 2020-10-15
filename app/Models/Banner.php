<?php

namespace App\Models;

use App\Traits\ActorTrait;
use App\Models\BannerViewer;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use ActorTrait;

    const TYPE_ERROR = 'E';
    const TYPE_INFORMATION = 'I';
    const TYPE_WARNING = 'W';
    const TYPES = [
        Banner::TYPE_ERROR => 'Critical', 
        Banner::TYPE_INFORMATION => 'Information', 
        Banner::TYPE_WARNING  => 'Warning'
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
    |
    | Write accessor and mutators below
    |
    */
    public function getTypeReadableAttribute()
    {
        return Banner::TYPES[$this->type];
    }

    public function getStateAttribute()
    {
        if ($this->starts_at->isFuture()) {
            return 'Upcoming';
        } else if ($this->ends_at->isPast()) {
            return 'Ended';
        } else {
            return 'Showing';
        }
    }

    public function getStatusAttribute($value)
    {
        return Banner::STATUSES[$value];
    }

    public function getCompanyViewerIdsAttribute()
    {
        return $this->bannerViewers()
            ->where('viewer_type', BannerViewer::VIEWER_TYPE_COMPANY)
            ->pluck('viewer_id')
            ->toArray();
    }

    public function getDepartmentViewerIdsAttribute()
    {
        return $this->bannerViewers()
            ->where('viewer_type', BannerViewer::VIEWER_TYPE_DEPARTMENT)
            ->pluck('viewer_id')
            ->toArray();
    }

    public function getUserViewerIdsAttribute()
    {
        return $this->bannerViewers()
            ->where('viewer_type', BannerViewer::VIEWER_TYPE_USER)
            ->pluck('viewer_id')
            ->toArray();
    }


    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    |
    | Write local scopes below
    |
    */
    public function scopeActive($query)
    {
        return $query->where('status', Banner::STATUS_ACTIVE);
    }

    public function scopeShowing($query)
    {
        $now = Carbon::now()->toDateTimeString();
        return $query->whereRaw("starts_at <= '{$now}' AND ends_at >= '{$now}'");
    }

    public function scopeUpcoming($query)
    {
        $now = Carbon::now();
        return $query->whereDate('starts_at', '>', $now)
            ->orWhere(function($query) use ($now) {
                $query
                    ->whereDate('starts_at', '=', $now->format('Y-m-d'))
                    ->whereTime('starts_at', '>', $now->format('H:i:s'));
            });
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Write relationships below 
    |
    */
    public function bannerViewers()
    {
        return $this->hasMany('App\Models\BannerViewer');
    }
}
