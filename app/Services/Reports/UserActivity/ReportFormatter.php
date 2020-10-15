<?php

namespace App\Services\Reports\UserActivity;

interface ReportFormatter
{
    public function format(array $report);
}