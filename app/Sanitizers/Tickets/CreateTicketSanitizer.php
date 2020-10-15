<?php

namespace App\Sanitizers\Tickets;

use App\Services\Tickets\TicketUserClassification;
use App\Models\TicketPriority;
use App\Models\TicketReason;
use App\Models\TicketStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CreateTicketSanitizer
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function sanitize()
    {
        $this->sanitizeDescription();
        $this->sanitizeDueDate();
        $this->sanitizeRequesterId();
        $this->sanitizeStatus();
        
        return $this->data;
    }

    protected function sanitizeDescription()
    {
        $this->data['description'] = htmlentities($this->data['description']);
    }

    protected function sanitizeDueDate()
    {
        $this->data['due_date'] .= " {$this->data['due_time']}:00";
    }

    protected function sanitizeRequesterId()
    {
        switch ($this->data['requester']) {
            case 'S':
                $this->data['requester_id'] = Auth::id();
                break;

            case 'M':
                $this->data['requester_id'] = $this->data['merchant'];
                break;

            case 'P':
                $this->data['requester_id'] = $this->data['partner'];
                break;
        }
    }

    protected function sanitizeStatus()
    {
        $this->data['status'] = TicketStatus::TICKET_STATUS_NEW;
    }
}