<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    protected $proxies = ['172.31.0.0/16'];

    /**
     * The headers that should be used to detect proxies.
     *
     * @var string
     */
     protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
