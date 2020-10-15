<?php

namespace App\Models;

use App\Models\Access;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            $hasSuperAdminAccess = Access::hasPageAccess('admin', 'super admin access', true);
            if (!$hasSuperAdminAccess) {
                $builder->whereHas('user', function($query) {
                    $query->where('company_id', auth()->user()->company_id);
                });
            }
        });

        static::addGlobalScope('filter', function (Builder $builder) {
            $scopes = [
                0 => 'analyticsUsers',
                1 => 'partner',
                2 => 'agent',
                3 => 'employee',
                4 => 'merchant' 
            ];

            $builder->whereHas('user', function($query) use ($scopes) {
                $scope = $scopes[request()->filter ?? 0];
                $query->$scope();
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor and Mutators
    |--------------------------------------------------------------------------
    |
    | Write accessor and mutators below
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    |
    | Write local scopes below
    |
    */
    public function scopePeriod($query, $startDate, $endDate)
    {
        return $query
            ->whereDate('created_at', '<=', $startDate)
            ->whereDate('created_at', '>=', $endDate);
    }

    public function scopeToday($query)
    {
        $today = Carbon::today()->format('Y-m-d');
        return $query->whereDate('created_at', $today);
    }

    public function scopeYesterday($query)
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        return $query->whereDate('created_at', $yesterday);
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Write relationships below 
    |
    */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
