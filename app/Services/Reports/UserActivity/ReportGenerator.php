<?php

namespace App\Services\Reports\UserActivity;

use App\Models\Analytics;
use App\Models\User;
use App\Models\UserType;
use App\Services\Reports\UserActivity\ReportData;
use App\Services\Reports\UserActivity\ReportTotal;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ReportGenerator
{
    private $displayType;
    
    private $report;
    private $reportData;

    private $users;
    private $startDate;
    private $endDate;
    private $companyId;

    public function __construct(
        string $displayType,
        string $resourceType,
        int $resourceId, 
        Carbon $startDate,
        Carbon $endDate,
        ?int $companyId=null)
    {
        $this->displayType = $displayType;
        $this->companyId = $companyId;
        $this->setUsers($resourceType, $resourceId);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    private function createTablesSkeleton()
    {
        $startDate = clone $this->startDate;
        $endDate = clone $this->endDate;

        while (true) {
            if ($startDate->format('Y-m') == $endDate->format('Y-m')) {
                $label = $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y');
                $endDateOfMonth = $endDate;
            } else {
                $endDateOfMonth = (clone $startDate)->endOfMonth();
                $label = $startDate->format('M d, Y') . ' to ' .
                         $endDateOfMonth->format('M d, Y'); 
            }
     
            $tables[$startDate->format('Y-m')] = [
                'label' => $label,
                'startDate' => (clone $startDate),
                'endDate' => $endDateOfMonth,
            ];

            if ($startDate->format('Y-m') == $endDate->format('Y-m'))
                break;

            $startDate->addMonths(1);
            $startDate->firstOfMonth();
        }

        return $tables;
    }

    private function getLabel()
    {
        if ($this->startDate->format('Y-m-d') == $this->endDate->format('Y-m-d')) {
            $label = $this->startDate->format('M d, Y');
        } else {
            $label = $this->startDate->format('M d, Y') . ' to ' .
                     $this->endDate->format('M d, Y'); 
        }

        return $label;
    }

    public function generate()
    {
        $userIds = $this->users->pluck('id')->toArray();
        $userReportTemplate = [
            'columns' => [
                'DATE',
                'LOGINS',
                'PAGES VISITED',
                'TIME SPENT'
            ],
        ];

        $this->reportData = new ReportData(
            $this->getUserActivityData($userIds), 
            $this->endDate);
            
        foreach ($this->users as $user) {
            $userReportTemplate['user'] = $user->full_name;
            $userReport = $this->getUserActivityReport($user);

            $this->report['users'][] = array_merge(
                $userReportTemplate, 
                $userReport);
        }
        
        return $this->report;
    }

    private function getUserActivityReport(User $user)
    {
        $grandTotal = new ReportTotal;
        switch ($this->displayType) {
            case 'DAILY';
                $retVal = $this->getUserActivityReportDaily($user);
                break;

            case 'WEEKLY':
                $retVal = $this->getUserActivityReportWeekly($user);
                break;

            case 'MONTHLY':
                $retVal = $this->getUserActivityReportMonthly($user);
                break;

            case 'YEARLY':
                $retVal = $this->getUserActivityReportYearly($user);
                break;

        }

        $table = $retVal['table'];
        $tableTotal = $retVal['tableTotal'];

        if ($tableTotal->doesntHaveValue()) {
            $tableTotalArray = [];
        } else {
            $tableTotalArray = $tableTotal->all();
        }

        $table['total'] = $tableTotalArray;
        $grandTotal->increase(
            $tableTotal->numberOfLogin,
            $tableTotal->pageVisits,
            $tableTotal->timeSpent);

        Arr::forget($table, 'startDate');
        Arr::forget($table, 'endDate');
        
        if ($grandTotal->doesntHaveValue()) {
            $grandTotalArray = [];
        } else {
            $grandTotalArray = $grandTotal->all();
        }

        return [
            'table' => $table,
            'grandTotal' => $grandTotalArray
        ];
    }

    private function getUserActivityReportDaily(User $user)
    {
        $table = [
            'label' => $this->getLabel(),
            'startDate' => clone $this->startDate,
            'endDate' => clone $this->endDate,
        ];

        $tableTotal = new ReportTotal;
        $diffInDays = $table['startDate']->diffInDays($table['endDate']);
        $counter = 0;
        for ($i = 0; $i <= $diffInDays;  $i++) {
            $startDateClone = (clone $table['startDate'])->addDays($i);

            $this->reportData->setFilter(
                $user->id, 
                $startDateClone, 
                $startDateClone);

            if ($this->reportData->doesntHaveValue())
                continue;

            $table['rows'][] = [
                'date' => $this->reportData->getLabel(),
                'number_of_login' => $this->reportData->getNumberOfLogin(),
                'page_visits' => $this->reportData->getPageVisits(),
                'time_spent' => $this->reportData->getTimeSpent('DATE')
            ];

            $tableTotal->increase(
                $this->reportData->getNumberOfLogin(),
                $this->reportData->getPageVisits(),
                $this->reportData->getTimeSpent());
        }

        return [
            'table' => $table,
            'tableTotal' => $tableTotal 
        ];
    }

    private function getUserActivityReportWeekly(User $user)
    {
        $table = [
            'label' => $this->getLabel(),
            'startDate' => clone $this->startDate,
            'endDate' => clone $this->endDate,
        ];

        $tableTotal = new ReportTotal;
        $diffInDays = $table['startDate']->diffInDays($table['endDate']);
        $counter = 0;
        for ($i = 0; $i <= $diffInDays;  $i += 6) {
            $startDateClone = (clone $table['startDate'])->addDays($i + $counter);
            $endDateClone = (clone $startDateClone)->addDays(6);
            $counter++;

            $this->reportData->setFilter(
                $user->id, 
                $startDateClone, 
                $endDateClone);

            if ($this->reportData->doesntHaveValue())
                continue;

            $table['rows'][] = [
                'date' => $this->reportData->getLabel(),
                'number_of_login' => $this->reportData->getNumberOfLogin(),
                'page_visits' => $this->reportData->getPageVisits(),
                'time_spent' => $this->reportData->getTimeSpent('DATE')
            ];

            $tableTotal->increase(
                $this->reportData->getNumberOfLogin(),
                $this->reportData->getPageVisits(),
                $this->reportData->getTimeSpent());
        }

        return [
            'table' => $table,
            'tableTotal' => $tableTotal 
        ];
    }

    private function getUserActivityReportMonthly(User $user)
    {
        $table = [
            'label' => $this->getLabel(),
            'startDate' => clone $this->startDate,
            'endDate' => clone $this->endDate,
        ];

        $tableTotal = new ReportTotal;
        $diffInDays = $table['startDate']->diffInDays($table['endDate']);
        for ($i = 0; $i <= 11;  $i++) {
            $startDateClone = (clone $table['startDate'])->addMonths($i);
            $endDateClone = (clone $startDateClone)->endOfMonth();

            $this->reportData->setFilter(
                $user->id, 
                $startDateClone, 
                $endDateClone); 

            if ($this->reportData->doesntHaveValue())
                continue;

            $table['rows'][] = [
                'date' => $this->reportData->getLabel(),
                'number_of_login' => $this->reportData->getNumberOfLogin(),
                'page_visits' => $this->reportData->getPageVisits(),
                'time_spent' => $this->reportData->getTimeSpent('DATE')
            ];

            $tableTotal->increase(
                $this->reportData->getNumberOfLogin(),
                $this->reportData->getPageVisits(),
                $this->reportData->getTimeSpent());
        }

        return [
            'table' => $table,
            'tableTotal' => $tableTotal 
        ];
    }

    private function getUserActivityReportYearly(User $user)
    {
        $table = [
            'label' => $this->getLabel(),
            'startDate' => clone $this->startDate,
            'endDate' => clone $this->endDate,
        ];

        $tableTotal = new ReportTotal;
        // $diffInDays = $table['startDate']->diffInDays($table['endDate']);
        // for ($i = 0; $i <= 11;  $i++) {
        //     $startDateClone = (clone $table['startDate'])->addMonths($i);
        //     $endDateClone = (clone $startDateClone)->endOfMonth();

            $this->reportData->setFilter(
                $user->id, 
                $table['startDate'], 
                $table['endDate']); 

            // if ($this->reportData->doesntHaveValue())
            //     continue;

            $table['rows'][] = [
                'date' => $this->reportData->getLabel(),
                'number_of_login' => $this->reportData->getNumberOfLogin(),
                'page_visits' => $this->reportData->getPageVisits(),
                'time_spent' => $this->reportData->getTimeSpent('DATE')
            ];

            $tableTotal->increase(
                $this->reportData->getNumberOfLogin(),
                $this->reportData->getPageVisits(),
                $this->reportData->getTimeSpent());
        // }

        return [
            'table' => $table,
            'tableTotal' => $tableTotal 
        ];
    }


    private function getUserActivityData(array $userIds) : array
    {
        $query = "created_at >= '{$this->startDate->format('Y-m-d')}' AND " .
                 "created_at <= '{$this->endDate->format('Y-m-d')}'";

        return Analytics::select('user_id')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") AS year_and_month')
            ->selectRaw('DATE(created_at) AS date')
            ->selectRaw('COUNT(CASE WHEN time_spent = 0 THEN 1 END) AS number_of_login')
            ->selectRaw('COUNT(*) AS page_visits')
            ->selectRaw("SUM(time_spent) AS time_spent")
            ->whereIn('user_id', $userIds)
            ->whereRaw($query)
            ->groupBy('user_id')
            ->groupBy('year_and_month')
            ->groupBy('date')
            ->get()
            ->groupBy(['user_id', 'date'])
            ->toArray();
    }

    private function isYear()
    {
        if ($this->startDate->format('Y-m-d') == Carbon::firstOfYear()->format('Y-m-d') &&
            $this->endDate->format('Y-m-d') == Carbon::endOfYear()->format('Y-m-d')) {
            return true;
        }

        return false;
    }

    private function setUsers(string $resourceType, int $resourceId)
    {
        switch ($resourceType) {
            case 'usertype':
                $userType = UserType::with(['users' => function($query) {
                        $query->where('company_id', $this->companyId);
                    }])
                    ->findOrFail($resourceId);

                $this->users = $userType->users;
                $this->report['group_description'] = $userType->description;
                break;
 
            case 'department':
                $department = UserType::select()
                    ->with('users')
                    ->findOrFail($resourceId);

                $this->users = $department->users;
                $this->report['group_description'] = $department->description;
                break;

            case 'user':
                $user = User::select(['id', 'first_name', 'last_name'])
                    ->findOrFail($resourceId);

                $this->users = collect([$user]);
                $this->report['group_description'] = null;
                break;
        }
    }
}