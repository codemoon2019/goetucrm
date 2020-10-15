<?php

namespace App\Providers;

use App\Models\Access;
use App\Models\TicketHeader;
use App\Models\UserType;
use App\Observers\TicketHeaderObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //for to https
        //URL::forceScheme('https');
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);

        /** Observers */
        TicketHeader::observe(TicketHeaderObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }
}
