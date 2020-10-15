<?php

namespace App\Services\Tickets;

use App\Contracts\Tickets\TicketActivityOperationService;
use App\Models\TicketActivity;
use App\Models\TicketHeader;
use App\Models\TicketStatus;
use App\Models\User;
use App\Services\BaseServiceImpl;
use Carbon\Carbon;

class TicketActivityOperationServiceImpl extends BaseServiceImpl implements TicketActivityOperationService
{
    public function createTicketActivity(TicketHeader $ticketHeader, User $user) : TicketActivity
    {
        $foreignKeys= [
            'assignee' => ['assignedTo', 'full_name'],
            'department' => ['userType', 'description'],
            'priority' => ['ticketPriority', 'description'],
            'product_id' => ['product', 'name' ],
            'reason' => ['ticketReason', 'description'],
            'requester_id' => ['requester', 'full_name'],
            'status' => ['ticketStatus', 'description'],
            'type' => ['ticketType', 'description']
        ];

        $timestampNow = Carbon::now()->toDateTimeString();
        $changes = $ticketHeader->getDirty();

        /** Special case */
        if ($ticketHeader->isDirty('status') && $ticketHeader->status == 'I') {
            $changes['status'] = $ticketHeader->status;
        }

        unset($changes['source_email']);
        unset($changes['email_message_id']);
        unset($changes['responsed_at_department']);
        unset($changes['responsed_at_assignee']);
        unset($changes['finished_at']);
        unset($changes['company_id']);
        unset($changes['create_by']);
        unset($changes['update_by']);

        if (isset($changes['department'])) {
            $supportResponsedAt = $timestampNow;
        }

        if (isset($changes['assignee'])) {
            $departmentResponsedAt = $timestampNow;
        }

        $mainAction = 'commented';
        if (isset($changes['status'])) {
            if ($changes['status'] == TicketStatus::IN_PROGRESS) {
                $mainAction = 'started progress';
                $startedProgressAt = $timestampNow;
            } else if ($changes['status'] == TicketStatus::SOLVED) {
                $mainAction = 'solved';
                $solvedAt = $timestampNow;
            } else if ($changes['status'] == TicketStatus::PENDING) {
                $mainAction = 'pending';
            }
         }

        /**
         * Formatting changes to be human readable
         * =================================================================
         * Format key as a readable field (e.g. requester_id => Requester)
         * Get previous and new value
         * Get relationship identifier instead of foreign key value if exist
         */
        foreach ($changes as $key => $change) {
            $newKey = null;
            if (substr($key, -3) == '_id') {
                $newKey = substr($key, 0, strlen($key) - 3);
                unset($changes[$key]);
            }

            $previousValue = null;
            $newValue = null;
            if (isset($foreignKeys[$key])) {
                $relation = $foreignKeys[$key][0];  
                $identifier = $foreignKeys[$key][1];

                $newForeignKey = $ticketHeader->$key;

                $ticketHeader->$key = $ticketHeader->getOriginal($key);
                $previousValue = optional($ticketHeader->$relation)->$identifier ?? 'N/A';

                $ticketHeader->$key = $newForeignKey;
                $ticketHeader->load($relation);
                $newValue = optional($ticketHeader->$relation)->$identifier ?? 'N/A';
            }

            $changes[$newKey ?? $key] = [
                'readable_field' => ucfirst(str_replace('_', ' ', $newKey ?? $key)),
                'previous_value' => $previousValue ?? $ticketHeader->getOriginal($key),
                'new_value' => $newValue ?? ($change ?? 'N/A')
            ];
        } 

        $data = [
            'main_action' => $mainAction,
            'changes' => json_encode($changes),
            
            'support_responsed_at' => $supportResponsedAt ?? null,
            'department_responsed_at' => $departmentResponsedAt ?? null,
            'started_progress_at' => $startedProgressAt ?? null,
            'solved_at' => $solvedAt ?? null,

            'ticket_header_id' => $ticketHeader->id,

            'create_by' => $user->username,
            'update_by' => $user->username,
        ];

        return TicketActivity::create($data);
    }
}