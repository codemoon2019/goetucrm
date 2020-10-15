<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BeansServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $beans = config('beans');
        foreach ($beans as $i => $impl) {
            if ($impl['singleton']) {
                $this->app->singleton($i, function () use ($impl) {
                    return $this->app->make($impl['class']);
                }, empty($impl['shared']) ? null : $impl['shared']);
            } else {
                $this->app->bind($i, function () use ($impl) {
                    return $this->app->make($impl['class']);
                }, empty($impl['shared']) ? null : $impl['shared']);
            }
        }
    }
}
