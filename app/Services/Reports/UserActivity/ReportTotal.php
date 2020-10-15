<?php

namespace App\Services\Reports\UserActivity;

class ReportTotal
{
    public $numberOfLogin = 0;
    public $pageVisits = 0;
    public $timeSpent = 0;

    public function all()
    {
        return [
            'number_of_login' => $this->numberOfLogin,
            'page_visits' => $this->pageVisits,
            'time_spent' => gmdate('H:i:s', $this->timeSpent)
        ];
    }

    public function allRaw()
    {
        return [
            'number_of_login' => $this->numberOfLogin,
            'page_visits' => $this->pageVisits,
            'time_spent' => $this->time_spent
        ];
    }

    public function increase($numberOfLogin, $pageVisits, $timeSpent)
    {
        $this->numberOfLogin += $numberOfLogin;
        $this->pageVisits += $pageVisits;
        $this->timeSpent += $timeSpent;
    }
    
    public function doesntHaveValue()
    {
        $value = $this->numberOfLogin;
        $value += $this->pageVisits;
        $value += $this->timeSpent;

        if ($value == 0)
            return true;

        return false;
    }
}