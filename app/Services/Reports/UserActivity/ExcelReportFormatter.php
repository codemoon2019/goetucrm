<?php

namespace App\Services\Reports\UserActivity;

use Excel;

class ExcelReportFormatter implements ReportFormatter
{
    public function format(array $report)
    {
        Excel::create('report', function ($excel) use ($report) {
            foreach ($report['users'] as $userReport) {
                $excel->sheet($userReport['user'], function ($sheet) use ($report, $userReport) {
                    $row = 1;

                    if ($report['group_description'] !== null) {
                        $sheet->mergeCells("A{$row}:D{$row}");
                        $sheet->row($row++, ["{$report['group_description']}'s Activity Report"]);
                    }

                    $sheet->mergeCells("A{$row}:D{$row}");
                    $sheet->row($row++, ["{$userReport['user']}'s Activity Report"]);

                    if (isset($userReport['table']['rows'])) {
                        $sheet->row($row++, []);
                        $sheet->mergeCells("A{$row}:D{$row}");
                        $sheet->row($row++, [$userReport['table']['label']]);
                        $sheet->row($row++, $userReport['columns']);
    
                        $sheet->fromArray($userReport['table']['rows'], null, "A{$row}", true, false);
                        $row += count($userReport['table']['rows']);
    
                        $sheet->row($row++, array_merge(['Total'], $userReport['table']['total']));
                        $sheet->row($row++, []);
                        $sheet->data = [];
    
                        $sheet->row($row++, array_merge(['Grand Total'], $userReport['grandTotal']));
                    } else {
                        $sheet->mergeCells("A{$row}:D{$row}");
                        $sheet->row($row++, ["No data available from {$userReport['table']['label']}"] );
                    }
                });
            }
        })
        ->export('xls');
    }
}