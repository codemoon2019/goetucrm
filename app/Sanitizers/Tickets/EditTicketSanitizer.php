<?php

namespace App\Sanitizers\Tickets;

use App\Services\Tickets\TicketUserClassification;
use App\Models\TicketPriority;
use App\Models\TicketReason;
use App\Models\TicketStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EditTicketSanitizer
{
    protected $data;
    protected $ticketStatusCode;

    public function __construct(array $data, string $ticketStatusCode)
    {
        $this->data = $data;
        $this->ticketStatusCode = $ticketStatusCode;
    }

    public function sanitize()
    {
        $this->sanitizeAssignee();
        $this->sanitizeMessage();
        $this->sanitizeDueDate();
        $this->sanitizeRequesterId();
        
        return $this->data;
    }

    protected function sanitizeAssignee()
    {
        if (isset($this->data['department']))
            $this->data['department'] = $this->data['department'] ?? -1;

        if (isset($this->data['assignee']))
            $this->data['assignee'] = $this->data['assignee'] ?? -1;
    }

    protected function sanitizeMessage()
    {
        $this->data['message'] = htmlentities($this->data['message']);
    }

    protected function sanitizeDueDate()
    {
        if (isset($this->data['due_date']) && isseT($this->data['due_time']))
            $this->data['due_date'] .= " {$this->data['due_time']}:00";
    }

    protected function sanitizeRequesterId()
    {
        if (isset($this->data['reference']) &&
            isset($this->data['merchant']) &&
            isset($this->data['partner'])) {

            if ($this->data['reference'] == 'Merchant') {
                $this->data['requester_id'] = $this->data['merchant'];
            } else {
                $this->data['requester_id'] = $this->data['partner'];
            }
        }
    }

    protected function sanitizeTicketStatusCode()
    {
        $this->data['ticket_status_code'] = $this->ticketStatusCode;
    }
}