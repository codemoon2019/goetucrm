<?php

namespace App\Services\Reports\UserActivity;

use Carbon\Carbon;

class ReportData
{
    public $data;

    private $userId;
    private $startDate;
    private $endDate;

    private $realEndDate;

    public function __construct($data, Carbon $realEndDate)
    {
        $this->data = $data;
        $this->realEndDate = $realEndDate;
    }

    public function allRaw()
    {
        return [
            'number_of_login' => $this->getNumberOfLogin(),
            'page_visits' => $this->getPageVisits(),
            'time_spent' => $this->getTimeSpent()
        ];
    }

    public function getLabel() : string
    {
        if ($this->startDate->format('Y-m-d') == $this->endDate->format('Y-m-d')) {
            return $this->startDate->format('M d, Y');
        }

        if ($this->realEndDate->lt($this->endDate)) {
            return $this->startDate->format('M d, Y') . ' to ' . 
               $this->realEndDate->format('M d, Y');
        }

        return $this->startDate->format('M d, Y') . ' to ' . 
               $this->endDate->format('M d, Y');
    }

    public function getNumberOfLogin() : int
    {
        $startDate = clone $this->startDate;
        $endDate = clone $this->endDate;

        if ($startDate->format('Y-m-d') == $endDate->format('Y-m-d')) {
            return $this->data[$this->userId]
                              [$startDate->format('Y-m-d')][0]
                              ['number_of_login'] ?? 0;
        }

        $numberOfLogin = 0;
        $diffInDays = $startDate->diffInDays($endDate);
        for ($i = 0; $i <= $diffInDays; $i++) {
            $startDateClone = (clone $startDate)->addDays($i);
            $numberOfLogin += $this->data[$this->userId]
                                         [$startDateClone->format('Y-m-d')][0]
                                         ['number_of_login'] ?? 0;

            if ($startDateClone->format('Y-m-d') == $this->realEndDate->format('Y-m-d')) {
                break;
            }
        }

        return $numberOfLogin;
    }

    public function getPageVisits() : int
    {
        $startDate = clone $this->startDate;
        $endDate = clone $this->endDate;

        if ($startDate->format('Y-m-d') == $endDate->format('Y-m-d')) {
            return $this->data[$this->userId]
                              [$startDate->format('Y-m-d')][0]
                              ['page_visits'] ?? 0;
        }

        $pageVisits = 0;
        $diffInDays = $startDate->diffInDays($endDate);
        for ($i = 0; $i <= $diffInDays; $i++) {
            $startDateClone = (clone $startDate)->addDays($i);
            $pageVisits += $this->data[$this->userId]
                                      [$startDateClone->format('Y-m-d')][0]
                                      ['page_visits'] ?? 0;

            if ($startDateClone->format('Y-m-d') == $this->realEndDate->format('Y-m-d')) {
                break;
            }
        }

        return $pageVisits;
    }

    public function getTimeSpent(string $format='RAW')
    {
        $startDate = clone $this->startDate;
        $endDate = clone $this->endDate;

        if ($startDate->format('Y-m-d') == $endDate->format('Y-m-d')) {
            $timeSpent = $this->data[$this->userId]
                              [$startDate->format('Y-m-d')][0]
                              ['time_spent'] ?? 0;
        } else {
            $timeSpent = 0;
            $diffInDays = $startDate->diffInDays($endDate);
            for ($i = 0; $i <= $diffInDays; $i++) {
                $startDateClone = (clone $startDate)->addDays($i);

                $timeSpent += $this->data[$this->userId]
                                         [$startDateClone->format('Y-m-d')][0]
                                         ['time_spent'] ?? 0;

                if ($startDateClone->format('Y-m-d') == $this->realEndDate->format('Y-m-d')) {
                    break;
                }
            }
        }

        switch ($format) {
            case 'RAW':
                return $timeSpent;

            case 'DATE':
                return gmdate('H:i:s', $timeSpent);

            default:
                throw new Exception("Undefined format given.");
        }
    }
    
    public function setFilter(
        int $userId, 
        Carbon $startDate,
        Carbon $endDate)
    {
        $this->userId = $userId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function doesntHaveValue()
    {
        $value = $this->getNumberOfLogin();
        $value += $this->getPageVisits();
        $value += $this->getTimeSpent();

        if ($value == 0)
            return true;

        return false;
    }
}