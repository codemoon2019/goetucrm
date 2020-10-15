<?php

namespace App\Services\Analytics;

use App\Models\Analytics;
use Carbon\Carbon;

class AnalyticsLineChart
{
    public $startDate;
    public $endDate;

    public function __construct()
    {
        $this->endDate = new Carbon('first day of this month');
        $this->startDate = (clone $this->endDate)->subMonths(12);
    }

    public function getChartData()
    {
        $baseMonth = $this->endDate;
        for ($i = 0; $i < 12; $i++) {
            $firstDayOfMonth = clone $baseMonth;
            $firstDayOfMonthCopy = clone $baseMonth;
            $firstDayOfNextMonth = $firstDayOfMonthCopy->addMonths(1);

            $fdom = $firstDayOfMonth->format('Y-m-d');
            $fdonm = $firstDayOfNextMonth->format('Y-m-d');
            $chartData[] = [
                'y' => Analytics::period($fdonm, $fdom)->count(),
                'label' => $firstDayOfMonth->format('M'),
            ];

            $baseMonth->subMonths(1);
        }

        return array_reverse($chartData);
    }
}