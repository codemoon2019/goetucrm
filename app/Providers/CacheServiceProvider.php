<?php

namespace App\Providers;

use App\Models\BusinessType;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class CacheServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /*
        | ---------------------------------------------------------------------
        | Business Types / Business Industries
        | ---------------------------------------------------------------------
        */
        if (!Cache::has('business_types')) {
            $businessTypes = BusinessType::orderBy('description')
                ->get()
                ->groupBy('group')
                ->sortKeys();

            Cache::forever('business_types', $businessTypes);
        }
    }

    public function register()
    {
        //
    }
}
