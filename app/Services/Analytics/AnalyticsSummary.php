<?php

namespace App\Services\Analytics;

use App\Models\Analytics;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsSummary
{
    public function getPageVisitsSummary($type)
    {
        $today = Carbon::today()->format('Y-m-d');
        switch ($type) {
            case 'day':
                $currentPeriod = Analytics::today()->count();
                $previousPeriod = Analytics::yesterday()->count();
                break;

            case 'week':
                $week = Carbon::today()->subDays(7)->format('Y-m-d');
                $weekBefore = Carbon::today()->subDays(7)->format('Y-m-d');
                $currentPeriod = Analytics::period($today, $week)->count();
                $previousPeriod = Analytics::period($week, $weekBefore)->count();  
                break;  

            case 'month':
                $month = Carbon::today()->subDays(30)->format('Y-m-d');
                $monthBefore = Carbon::today()->subDays(60)->format('Y-m-d');
                $currentPeriod = Analytics::period($today, $month)->count();
                $previousPeriod = Analytics::period($month, $monthBefore)->count();
                break;
        }

        return [
            'currentPeriod' => $currentPeriod,
            'previousPeriod' => $previousPeriod,
        ];
    }

    public function getMostActiveUsers()
    {
        $rawQuery  = "user_id, COUNT(id) as total_page_visits, ";
        $rawQuery .= "SUM(time_spent) as total_time_spent";
        return Analytics::select(DB::raw($rawQuery))
            ->with('user.department')
            ->groupBy('user_id')
            ->orderByDesc('total_page_visits')
            ->take(5)
            ->get()
            ->map(function($analytics) {
                $user = $analytics->user;
                $user->total_page_visits = $analytics->total_page_visits;
                $user->total_time_spent = $analytics->total_time_spent;
                return $user;
            });
    }

    public function getMostVisitedPages()
    {
        $rawQuery  = "url, COUNT(id) as total_page_visits, ";
        $rawQuery .= "SUM(time_spent) as total_time_spent";
        return Analytics::select(DB::raw($rawQuery))
            ->groupBy('url')
            ->orderByDesc('total_page_visits')
            ->take(5)
            ->get();
    }

    public function getTotalPageViews()
    {
        return Analytics::count();
    }
}