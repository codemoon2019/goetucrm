<?php

namespace App\Services\Analytics;

use App\Models\Analytics;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserAnalytics
{
    protected $userId;
    public $user;

    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->user = User::has('analytics')
            ->with(['analytics' => function ($query) {
                return $query->orderByDesc('created_at');
            }])
            ->with('partnerCompany')
            ->findOrFail($userId);
    }

    public function getMostUsedBrowser()
    {
        return Analytics::select(DB::raw('browser, COUNT(*) as browser_count'))
            ->where('user_id', $this->userId)
            ->orderBy('browser_count', 'DESC')
            ->groupBy('browser')
            ->first()
            ->browser;
    }

    public function getMostUsedDevice()
    {
        return Analytics::select(DB::raw('device, COUNT(*) as device_count'))
            ->where('user_id', $this->userId)
            ->orderBy('device_count', 'DESC')
            ->groupBy('device')
            ->first()
            ->device;
    }

    public function getMostUsedPlatform()
    {
        return Analytics::select(DB::raw('platform, COUNT(*) as platform_count'))
            ->where('user_id', $this->userId)
            ->orderBy('platform_count', 'DESC')
            ->groupBy('platform')
            ->first()
            ->platform;
    }

    public function getTotalDaysVisited()
    {
        return Analytics::select(DB::raw('DATE(created_at) as created_at_date'))
            ->where('user_id', $this->userId)
            ->get()
            ->groupBy('created_at_date')
            ->count();
    }

    public function getTotalTimeSpent()
    {
        return Analytics::where('user_id', $this->userId)->sum('time_spent');
    }
}