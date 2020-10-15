<?php

namespace App\Services\Tickets;

use App\Contracts\TicketNotifyService;
use App\Models\Access;
use App\Models\EmailOnQueue;
use App\Models\Notification;
use App\Models\TicketDetail;
use App\Models\TicketHeader;
use App\Models\TicketStatus;
use App\Models\User;
use App\Models\UserType;
use App\Services\BaseServiceImpl;
use Illuminate\Support\Facades\DB;

class TicketNotifyServiceImpl extends BaseServiceImpl implements TicketNotifyService
{
    public function notifyOnCreate(TicketHeader $ticketHeader)
    {
        $this->createNotification($ticketHeader, 'create');
    }

    public function notifyOnCreateThroughEmail(TicketHeader $ticketHeader)
    {
        $this->createEmailOnQueue($ticketHeader, 'create');
    }

    public function notifyOnAction($ticketHeaders, $action, $ticketHeaderIds=null)
    {
        foreach ($ticketHeaders as $ticketHeader) {
            $this->createNotification($ticketHeader, $action, $ticketHeaderIds);
        }
    }

    public function notifyOnActionThroughEmail($ticketHeaders, $action, $ticketHeaderIds=null)
    {
        foreach ($ticketHeaders as $ticketHeader) {
            $this->createEmailOnQueue($ticketHeader, $action, $ticketHeaderIds);
        }
    }

    private function getUsernamesAssociatedWithTicket(TicketHeader $ticketHeader, $excludes=[])
    {
        $usernames = [];
       
        foreach ($ticketHeader->ccs as $cc) {
            $usernames[] = $cc->username;
        } 

        $usernames['createdBy'] = optional($ticketHeader->createdBy)->username;
        $usernames['departmentHead'] = optional(optional($ticketHeader->userType)->departmentHead)->username;
        $usernames['assignedTo'] = optional($ticketHeader->assignedTo)->username;

        $usernames = array_unique($usernames);
        $usernames = array_diff($usernames, [auth()->user()->username]);
        $usernames = array_filter($usernames);

        foreach ($excludes as $exclude) {
            unset($usernames[$exclude]);
        }

        return $usernames;
    }

    private function getEmailsAssociatedWithTicket(TicketHeader $th, $excludes=[])
    {
        $ea = []; /** ea = emailAddresses */

        foreach ($th->ccs as $cc) {
            if ($cc->email_address && $cc->ticketing_email == true) {
                $ea[] = $cc->email_address;
            }
        } 

        if ($th->createdBy && $th->createdBy->ticketing_email == true) {
            $ea['createdBy'] = $th->createdBy->email_address;
        } 

        if ($th->userType && isset($th->userType->departmentHead) && 
            $th->userType->departmentHead->ticketing_email == true) {

            $ea['departmentHead'] = $th->userType->departmentHead->email_address;
        }

        if ($th->assignedTo && $th->assignedTo->ticketing_email == true) {
            $ea['assignedTo'] = $th->assignedTo->email_address;
        }

        $ea = array_unique($ea);
        $ea = array_diff($ea, [auth()->user()->email_address]);
        $ea = array_filter($ea);
        
        foreach ($excludes as $exclude) {
            unset($ea[$exclude]);
        }

        return implode(',', $ea);
    }

    private function createNotification(TicketHeader $ticketHeader, $action,
        $ticketHeaderIds=null)
    {
        $excludes = [];
        $isPartner = $ticketHeader->createdBy->is_original_partner ? true : false;

        switch ($action) {
            case 'assign':
                $assigneeFullName = $ticketHeader->assignee == -1 ? 
                    "{$ticketHeader->userType->description} Department" : 
                    "{$ticketHeader->assignedTo->full_name}";

                $subject = "Ticket #{$ticketHeader->id} assignee changed";
                $message  = "Ticket #{$ticketHeader->id} is assigned to ";
                $message .= "{$assigneeFullName} by {$ticketHeader->updatedBy->full_name}";

                $excludes = $isPartner ? ['createdBy'] : [];
                break;

            case 'delete':
                $subject = "Ticket #{$ticketHeader->id} deleted";
                $message = "Ticket #{$ticketHeader->id} deleted by {$ticketHeader->updatedBy->full_name}";
                break;

            case 'merge':
                $subject = 'Tickets #' . implode(', ', $ticketHeaderIds) . ' Merged';
                $message  = 'Tickets #' . implode(', ', $ticketHeaderIds);
                $message .= " Merged by {$ticketHeader->updatedBy->full_name}";
                break; 

            case 'reply':
                $subject = "Ticket #{$ticketHeader->id} - {$ticketHeader->subject}";
                $message = "{$ticketHeader->updatedBy->full_name} posted a reply";
                break;

            case 'create':
                $subject = "Ticket #{$ticketHeader->id} - {$ticketHeader->subject}";
                $message = "{$ticketHeader->createdBy->full_name} created a Ticket";
                $excludes = ['createdBy'];
                break;
        }

        $usernames = $this->getUsernamesAssociatedWithTicket($ticketHeader,  $excludes);

        $timestamp = date('Y-m-d H:i:s');
        $data = array();
        foreach ($usernames as $username) {
            $data[] = [
                'partner_id' => -1,
                'source_id' => -1,
                'subject' => $subject,
                'message' => $message,
                'status' => 'N',
                'create_by' => auth()->user()->username,
                'update_by' => auth()->user()->username,
                'redirect_url' => "/tickets/{$ticketHeader->id}/edit",
                'recipient' => $username,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        Notification::insert($data);
    }

    private function createEmailOnQueue(TicketHeader $ticketHeader, $action, $ticketHeaderIds=null)
    {
        $excludes = [];
        $isPartner = $ticketHeader->createdBy->is_original_partner ? true : false;

        switch($action) {
            case 'create':
                $data = ['ticketHeader' => $ticketHeader];
                $emailBody = view("mails.tickets.create", $data)->render();
                break;

            case 'assign':
                $data = ['ticketHeader' => $ticketHeader];
                $emailBody = view("mails.tickets.assign", $data)->render();
                $excludes = $isPartner ? ['createdBy'] : [];
                break;

            case 'delete':
                $data = ['ticketHeader' => $ticketHeader];
                $emailBody = view("mails.tickets.delete", $data)->render();
                break;

            case 'merge':
                $data = ['ticketHeader' => $ticketHeader, 'ticketHeaderIds' => $ticketHeaderIds];
                $emailBody = view("mails.tickets.merge", $data)->render();
                break;
               
            case 'reply':
                $data = ['ticketHeader' => $ticketHeader];
                $emailBody = view("mails.tickets.reply", $data)->render();
                break;
        }

        $emailAddresses = $this->getEmailsAssociatedWithTicket($ticketHeader, $excludes);

        if ($emailAddresses != '') {
            $emailOnQueue = new EmailOnQueue;
            $emailOnQueue->subject = $ticketHeader->subject;
            $emailOnQueue->body =  $emailBody;
            $emailOnQueue->email_address = $emailAddresses;
            $emailOnQueue->ticket_header_id = $ticketHeader->id;
            $emailOnQueue->create_by = auth()->user()->username;
            $emailOnQueue->is_sent = 0;
            $emailOnQueue->sent_date = null;
            $emailOnQueue->save();
        }
    }
}