<?php

namespace Guesl\CardConnect\CoPilot\Managers;

use Guesl\CardConnect\CoPilot\Contracts\CoPilotFactory;
use Guesl\CardConnect\CoPilot\Contracts\CoPilotService;
use Guesl\CardConnect\CoPilot\Services\CoPilotProvider;
use Illuminate\Support\Manager;

class CoPilotManager extends Manager implements CoPilotFactory
{
    /**
     * Get a driver instance.
     * @param string|null $driver
     * @return mixed
     */
    public function with(string $driver = null)
    {
        return $this->driver($driver);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return CoPilotService
     */
    public function createCoPilotDriver()
    {
        $config = $this->app["config"]["cardconnect.copilot"];
        return $this->buildProvider(
            CoPilotProvider::class, $config
        );
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return "CoPilot";
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string $provider
     * @param  array $config
     * @return mixed
     */
    public function buildProvider($provider, $config)
    {
        return new $provider($config);
    }
}
