<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Auth;
class UpdateLastActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        auth()->user()->last_activity = date('Y-m-d H:i:s');
        auth()->user()->save();

        return $next($request);
    }
}
