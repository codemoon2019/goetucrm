<?php

class Secure implements Middleware
{
    public function handle($request, Closure $next)
    {
        if (! $request-&gt;secure() && app()-&gt;environment('production')) {

            // this is a really ugly hack but it kept looping and prepnding public
            return redirect()-&gt;secure(preg_replace('%/public%', '', $request-&gt;getRequestUri()));
        }

        return $next($request);
    }
}