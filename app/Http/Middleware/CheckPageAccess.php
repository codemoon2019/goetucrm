<?php

namespace App\Http\Middleware;

use Closure;

class CheckPageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $moduleName, $moduleAccess)
    {
        $valid = false;
        $access = [];
        if (session()->has('all_user_access')) {
            $access = session('all_user_access');
            if ( isset($access[$moduleName]) ) {
                if (strpos($access[$moduleName], $moduleAccess) !== false) {
                    $valid=true;
                }
            }
        }

        if (isset($access['admin']) && strpos($access['admin'], 'super admin access', true)) { 
            $valid = true; 
        }

        if (!$valid) {
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        return $next($request); 
    }
}
