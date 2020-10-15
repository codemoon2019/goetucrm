<?php

namespace App\Services\Reports\UserActivity;

class ReportFormatterFactory
{
    public function make(string $type)
    {
        switch ($type) {
            case 'web':
                return new WebReportFormatter();

            case 'excel':
                return new ExcelReportFormatter();
        }
    }
}