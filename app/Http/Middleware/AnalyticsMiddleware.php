<?php

namespace App\Http\Middleware;

use App\Models\Analytics;
use Carbon\Carbon;
use Closure;
use Exception;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AnalyticsMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (!$response instanceof Response) {
            return $response;
        }

        $userTypeIds = ['4', '5', '6', '11', '13']; 
        $userTypeId = explode(',', auth()->user()->user_type_id)[0];
        if (!(in_array($userTypeId, $userTypeIds) || $userTypeId > 13)) {
            return $response;
        }
        
        if (session()->has('analytics_id')) {
            try {
                $analytics = Analytics::findOrFail(session('analytics_id'));
                $analytics->time_spent = Carbon::now()->diffInSeconds($analytics->created_at);
                $analytics->save();
            } catch (Exception $ex) {
                Log::error($ex->getMessage());
            }
        }

        /** 
         * 5% chance to use agent to get analytics 
         * details if already in session.
         */
        if (session()->has('analytics_details') && rand(1, 20) != 1) {
            $analyticsDetails = session('analytics_details');
        } else {
            $analyticsDetails = $this->getAnalyticsDetails();
            session(['analytics_details' => $analyticsDetails]);
        }

        $analytics = Analytics::create(array_merge($analyticsDetails, [
            'url' => url()->current(),
            'ip_address' => $request->getClientIp(),
            'time_spent' => 0,
            'user_id' => auth()->user()->id
        ]));

        session(['analytics_id' => $analytics->id]);
        return $response;
    }

    /**
     * Private Functions
     */
    private function getAnalyticsDetails()
    {
        $agent = new Agent();
        $browser = $agent->browser();
        $browser = "{$agent->browser()} {$agent->version($browser)}";
        $platform = $agent->platform();
        $platform = "{$agent->platform()} {$agent->version($platform)}";
        if ($agent->isDesktop()) {
            $device = 'Laptop / PC';
        } else {
            $device = $agent->device();
        }

        return [
            'browser' => $browser,
            'device' => $device,
            'platform' => $platform,
        ];
    }
}
