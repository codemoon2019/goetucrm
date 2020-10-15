<?php

namespace App\Services\Tickets;

use App\Models\TicketType as TicketIssueType;
use Illuminate\Support\Arr;

class TicketSetup
{
    /**
     * @param departmentId | Default department to receive tickets
     */
    public function setup(int $companyId, int $departmentId) 
    {
        $ticketIssueTypesData = config('default.ticket_issue_types');
        foreach ($ticketIssueTypesData as $ticketIssueTypeData) {
            $ticketIssueType = TicketIssueType::create([
                'description' => $ticketIssueTypeData['description'],
                'default_workflow' => $ticketIssueTypeData['default_workflow'] ?? null,
                'company_id' => $companyId,
                'create_by' => 'SYSTEM',
                'update_by' => 'SYSTEM',
            ]);

            $ticketReasonsData = collect($ticketIssueTypeData['ticket_reasons']);
            $ticketReasonsData = $ticketReasonsData
                ->map(function($ticketReason) use ($companyId, $departmentId){
                    $ticketReason['company_id'] = $companyId;
                    $ticketReason['department_id'] = $departmentId;;
                    $ticketReason['create_by'] = 'SYSTEM';
                    $ticketReason['update_by'] = 'SYSTEM';

                    return $ticketReason;
                })
                ->toArray();

            $ticketIssueType->ticketReasons()->createMany($ticketReasonsData);
        }
    }
}