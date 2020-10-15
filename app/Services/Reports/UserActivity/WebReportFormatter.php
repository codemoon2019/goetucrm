<?php

namespace App\Services\Reports\UserActivity;

class WebReportFormatter implements ReportFormatter
{
    public function format(array $report)
    {
        return view('reports.userActivities.show')->with([
            'report' => $report
        ]);
    }
}