<?php

namespace App\Services\Tickets;

use App\Contracts\TicketListService;
use App\Models\TicketHeader;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\TicketPriority;
use App\Models\UserType;
use App\Services\BaseServiceImpl;
use Exception;

class TicketListServiceImpl extends BaseServiceImpl implements TicketListService
{
    public function listInternalTickets($filterCode, $statusCode, 
        $departmentIds, $priorityCode, $companyId, $requesterId)
    {
        $ticketHeaders = null;
        if ($filterCode == 'A') {
            $ticketHeaders = $this->listAllTickets($statusCode, $priorityCode, 
                $companyId, $departmentIds, $requesterId);
        } else {
            $ticketHeaders = $this->listMyTickets($statusCode, $priorityCode,
                $companyId, $departmentIds, $requesterId);
        }

        return $ticketHeaders;
    }

    private function listAllTickets($statusCode, $priorityCode, $companyId, 
        $departmentIds, $requesterId)
    {
        $ticketQueryBuilder = TicketHeader::with('ticketStatus', 'requester')
            ->whereStatus($statusCode)
            ->wherePriority($priorityCode)     
            ->whereCompany($companyId)
            ->whereDepartmentIn($departmentIds)
            ->whereRequester($requesterId)
            ->orderBy('created_at', 'DESC');

        if ($statusCode == 'D' || $statusCode == 'M') {
            $ticketQueryBuilder = $ticketQueryBuilder->withoutGlobalScopes();
        }
        
        return $ticketQueryBuilder->get();
    }

    private function listMyTickets($statusCode, $priorityCode, $companyId, 
        $departmentIds, $requesterId)
    {
        $userId = auth()->user()->id; 

        $ticketQueryBuilder = TicketHeader::with('ticketStatus', 'requester')
            ->whereStatus($statusCode)
            ->wherePriority($priorityCode)
            ->whereDepartmentIn($departmentIds)
            ->whereRequester($requesterId)
            ->where(function($query) use ($userId) {
                $query->where('create_by', auth()->user()->username)
                    ->orWhere('assignee', $userId)
                    ->orWhere('requester_id', $userId)
                    ->orWhereHas('ccs', function($query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
            })
            ->orderBy('created_at', 'DESC');

        if ($statusCode == 'D' || $statusCode == 'M') {
            $ticketQueryBuilder = $ticketQueryBuilder->withoutGlobalScopes();
        }

        return $ticketQueryBuilder->get();
    }

    public function listPartnerOrMerchantTickets($statusCode, $priorityCode)
    {
        $userId = auth()->user()->id; 
        $userTypeIds = explode(',', auth()->user()->user_type_id);
        $companyId = auth()->user()->company_id;

        $ticketQueryBuilder = TicketHeader::with('ticketStatus', 'requester')
            ->whereStatus($statusCode)
            ->wherePriority($priorityCode)
            ->where(function($query) use ($userId) {
                $query->where('create_by', auth()->user()->username)
                    ->orWhere('assignee', $userId)
                    ->orWhere('requester_id', $userId)
                    ->orWhereHas('ccs', function($query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
            })
            ->orderBy('created_at', 'DESC');

        if ($statusCode == 'D' || $statusCode == 'M') {
            $ticketQueryBuilder = $ticketQueryBuilder->withoutGlobalScopes();
        }

        return $ticketQueryBuilder->get();
    }

    public function formatTicketsForDatatable($ticketHeaders)
    {
        $ticketStatuses = TicketStatus::all();
        $ticketTypes = TicketType::all();
        $ticketPriorities = TicketPriority::all();
        
        return datatables()->collection($ticketHeaders)
            ->addColumn('idLink', function($ticketHeader) {
                $idLink  = "<a href='/tickets/{$ticketHeader->id}/edit'>";
                $idLink .=     "{$ticketHeader->id}";
                $idLink .= "</a>";

                return $idLink;
            })
            ->editColumn('department', function($ticketHeader) {
                if ($ticketHeader->department == -1) {
                    return 'N/A';
                }

                return $ticketHeader->userType->description; 
            })
            ->editColumn('status', function($ticketHeader) use ($ticketStatuses) 
            {
                $statusObj = $ticketStatuses->where('code', $ticketHeader->status)->first();
                return is_null($statusObj) ? 'N/A' : $statusObj->description;
            })
            ->editColumn('assignee', function($ticketHeader) {
                if ($ticketHeader->assignee == -1 || $ticketHeader->assignee == null) {
                    return 'N/A';
                }

                try {
                    $fullName = $ticketHeader->assignedTo->first_name . ' ';
                    $fullName .= $ticketHeader->assignedTo->last_name;
                } catch (Exception $ex) {
                    dd('Ye');
                }
                

                return $fullName;
            })
            ->editColumn('due_date', function($ticketHeader) {
                return is_null($ticketHeader->due_date) ? 
                    'N/A' : 
                    $ticketHeader->due_date->format('m/d/Y g:i A');
            })
            ->editColumn('type', function($ticketHeader) use ($ticketTypes) {
                $typeObj = $ticketTypes->where('id', $ticketHeader->type)->first();
                return is_null($typeObj) ? 'N/A' : $typeObj->description;
            })
            ->editColumn('priority', function($ticketHeader) use ($ticketPriorities) {
                $priorityObj = $ticketPriorities->where('code', $ticketHeader->priority)->first();
                return is_null($priorityObj) ? 'N/A' : $priorityObj->description;
            })
            ->editColumn('created_by', function($ticketHeader) {
                return $ticketHeader->createdBy->full_name;
            })
            ->editColumn('created_at', function($ticketHeader) {
                return $ticketHeader->created_at->format('m/d/Y');
            })
            ->rawColumns(['idLink'])
            ->make(true);
    }
}