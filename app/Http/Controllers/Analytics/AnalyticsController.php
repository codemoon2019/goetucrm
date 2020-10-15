<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Analytics\AnalyticsLineChart;
use App\Services\Analytics\AnalyticsSummary;
use App\Services\Analytics\UserAnalytics;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * GET Parameter `filter` is being used in 
     * global scope of Analytics Model
     */
    public function index(
        AnalyticsLineChart $analyticsLineChart, 
        AnalyticsSummary $analyticsSummary)
    {
        return view('analytics.index')->with([
            'analyticsSummary' => $analyticsSummary,
            'analyticsLineChart' => $analyticsLineChart,
        ]);
    }

    public function user($userId)
    {
        $userAnalytics = new UserAnalytics($userId);
        return view('analytics.user')->with([
            'user' => $userAnalytics->user,
            'userAnalytics' => $userAnalytics,
        ]);
    }

    /**
     * GET Parameter `filter` is being used in 
     * global scope of Analytics Model
     */
    public function users()
    {
        $filterNumber = request()->filter ?? 0;
        $queryBuilder = User::with('analytics')->with('department')->has('analytics');

        $scopes = [
            0 => 'analyticsUsers',
            1 => 'partner',
            2 => 'agent',
            3 => 'employee',
            4 => 'merchant' 
        ];

        $scope = $scopes[$filterNumber];
        return view('analytics.users')->with([
            'users' => $queryBuilder->$scope()->get(),
        ]);
    }
}
