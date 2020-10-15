<?php

namespace Guesl\CardConnect\CoPilot;

use Guesl\CardConnect\CoPilot\Contracts\CoPilotFactory;
use Guesl\CardConnect\CoPilot\Managers\CoPilotManager;
use Illuminate\Support\ServiceProvider;

/**
 * Cashier service provider to
 *
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 */
class CoPilotServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/cardconnect.php' => $this->app['path.config'] . DIRECTORY_SEPARATOR . 'cardconnect.php',
        ], "cardconnect");
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CoPilotFactory::class, function ($app) {
            return new CoPilotManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [CoPilotFactory::class];
    }
}
