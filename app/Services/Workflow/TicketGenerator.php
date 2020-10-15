<?php

namespace App\Services\Workflow;

use App\Notifications\Tickets\TicketCreatedNotification;
use App\Models\SubTaskHeader as Task;
use App\Models\SubTaskDetail as Subtask;
use App\Models\TicketHeader;
use App\Models\TicketStatus;
use App\Models\TicketType as TicketIssueType;
use App\Models\TicketReason;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

class TicketGenerator
{
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function checkIfDoable(Subtask $subtask) : bool
    {
        $hasTicketHeader = isset($subtask->ticketHeader);
        $prerequsiteCompleted = true;

        if ($hasTicketHeader)
            return false;

        if ($subtask->prerequisite_subtask == null)
            return true;

        $prerequisiteSubtask = $subtask->prerequisite_subtask;
        $ticketHeader = $prerequisiteSubtask->ticketHeader;

        if ($subtask->link_condition === 'Completion') {
            if (optional($ticketHeader)->status === TicketStatus::SOLVED)
                return true;
        } else {
            if(isset($ticketHeader)){
                if (optional($ticketHeader)->due_date->isPast())
                    return true;                
            }

        }
        
        return false;
    }

    private function getDoableSubtasks() : Collection
    {
        return $this->task->subtasks->filter(function($subtask, $key) {
            return $this->checkIfDoable($subtask);
        });
    }

    /**
     * @return int | Number of subtasks generated
     */
    public function generateTickets() : int
    {
        $this->task->load('subtasks');
        $subtasks = $this->getDoableSubtasks();

        foreach ($subtasks as $subtask) {
            $this->generateTicket($subtask);
        }

        return $subtasks->count();
    }

    public function generateTicket($subtask) : void
    {
        /** @todo Change, move issue type and reason out of this function */
        $ticketIssueType = TicketIssueType::where('default_workflow', true)
            ->where('company_id', $subtask->department->company_id)
            ->first();

        $ticketReason = TicketReason::where('default_workflow', true)
            ->where('company_id', $subtask->department->company_id)
            ->first();
        
        $dueDate = Carbon::now()->addDays($subtask->days_to_complete);
        
        $status = $subtask->status;
        switch ($subtask->status) {
            case '':
                $status = 'N';
                break;
            
            case 'T':
                $status = 'N';
                break;

            case 'C':
                $status = 'S';
                break;

            case 'V':
                $status = 'S';
                break;
        }
        
        $ticketHeader = TicketHeader::create([
            'subject' => $subtask->name,
            'description' => "Auto-generated ticket",
            'due_date' => $dueDate,

            'status' => TicketHeader::TICKET_STATUS_NEW,
            'priority' => $subtask->ticket_priority_code,
            'type' => $ticketIssueType->id, 
            'reason' => $ticketReason->id, 

            'product_id' => $this->task->productOrder->product_id,
            'requester_id' => $this->task->productOrder->partner->connectedUser->id,
            'assignee' => $subtask->assignee ?? -1,
            'department' => $subtask->department_id,
            'company_id' => $subtask->department->company_id,
            'status' => $status,

            'sub_task_detail_id' => $subtask->id,
        ]);

        $users = $ticketHeader->involved_users;
        $notification = new TicketCreatedNotification($ticketHeader);

        try {
            Notification::send($users, $notification);
        } catch (Exception $ex) {
            /** Do Nothing */
        }
    }
}