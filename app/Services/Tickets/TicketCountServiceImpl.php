<?php

namespace App\Services\Tickets;

use App\Contracts\TicketCountService;
use App\Models\TicketHeader;
use App\Models\TicketStatus;
use App\Models\UserType;
use App\Services\BaseServiceImpl;

class TicketCountServiceImpl extends BaseServiceImpl implements TicketCountService
{
    public function countTickets()
    {
        $allTicketsCount = array();
        $departmentTicketsCount = array();
        $myTicketsCount = array();

        $ticketStatuses = TicketStatus::all();

        $i = 0; 
        foreach ($ticketStatuses as $ticketStatus) {
            $allTicketsCount[] = $this->countAllTickets($ticketStatus->code);
            $departmentTicketsCount[] = $this->countTicketsInMyDepartment($ticketStatus->code);
            $myTicketsCount[] = $this->countMyTickets($ticketStatus->code);

            $i++;
            if ($i == 4)
                break; 
        }

        $allTicketsCount[] = $this->countAllTickets();
        $departmentTicketsCount[] = $this->countTicketsInMyDepartment();
        $myTicketsCount[] = $this->countMyTickets();

        return [
            'ALL' => $allTicketsCount,
            'DEPARTMENT' => $departmentTicketsCount,
            'MY' => $myTicketsCount,
        ];
    }

    private function countAllTickets($statusCode=null)
    {
        $companyId = auth()->user()->company_id;
        
        return TicketHeader::whereStatus($statusCode)
            ->whereCompany($companyId)
            ->count();
    }

    private function countTicketsInMyDepartment($statusCode=null)
    {
        $userId = auth()->user()->id;
        $userTypeIds = explode(',', auth()->user()->user_type_id);
        $companyId = auth()->user()->company_id;

        $ticketCount = TicketHeader::whereStatus($statusCode)
            ->whereDepartmentIn($userTypeIds)
            ->count();

        return $ticketCount;
    }

    private function countMyTickets($statusCode=null)
    {
        $userId = auth()->user()->id;
        $userTypeIds = explode(',', auth()->user()->user_type_id);
        $companyId = auth()->user()->company_id;

        $ticketCount = TicketHeader::whereStatus($statusCode)
            ->where(function($query) use ($userId) {
                $query->where('create_by', auth()->user()->username)
                    ->orWhere('assignee', $userId)
                    ->orWhere('requester_id', $userId)
                    ->orWhereHas('ccs', function($query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
            })
            ->count();
        
        return $ticketCount;
    }
}