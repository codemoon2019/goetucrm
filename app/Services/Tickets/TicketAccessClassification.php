<?php

namespace App\Services\Tickets;

use App\Models\User;
use App\Models\TicketHeader;

class TicketAccessClassification
{
    protected $user;
    protected $userClassification;
    protected $ticketHeader;

    public $viewOnly = false;
    public $replyOnly = false;
    public $all = false;

    const VIEW_ONLY = 0;
    const REPLY_ONLY = 1;
    const ALL_PRIVILEGES = 2;

    public function __construct(
        User $user,
        int $userClassification,
        TicketHeader $ticketHeader)
    {
        $this->user = $user;
        $this->userClassification = $userClassification;
        $this->ticketHeader = $ticketHeader;

        $this->setClassification();
    }

    public function setClassification() : int
    {
        if ($this->isTicketDeleted() ||  $this->isTicketMerged()) {
            $this->viewOnly = true;
            return self::VIEW_ONLY;
        }

        if ($this->isTicketSolved()) {
            $this->replyOnly = true;
            return self::REPLY_ONLY;
        }

        switch ($this->userClassification) {
            case TicketUserClassification::SUPER_ADMIN:
            case TicketUserClassification::COMPANY:
                $this->all = true;
                return self::ALL_PRIVILEGES;
        }

        if ($this->isUserAssignee() || 
            $this->isUserCreator() || 
            $this->isUserDepartmentHeadOfTicket()) {
            $this->all = true;
            return self::ALL_PRIVILEGES;
        }

        if ($this->isUserPartOfCC() || $this->isUserRequester()) {
            $this->replyOnly = true;
            return self::REPLY_ONLY;
        }

        $this->viewOnly = true;
        return self::VIEW_ONLY;
    }

    public function getClassification() : int
    {
        if ($this->viewOnly)
            return self::VIEW_ONLY;

        if ($this->replyOnly)
            return self::REPLY_ONLY;

        if ($this->all)
            return self::ALL_PRIVILEGES;
    }

    protected function isUserDepartmentHeadOfTicket()
    {
        return $this->user->id == $this->ticketHeader->userType->head_id;
    }

    protected function isUserAssignee()
    {
        return $this->user->id == $this->ticketHeader->assignee;
    }

    protected function isUserCreator()
    {
        return $this->user->username == $this->ticketHeader->create_by;
    }

    protected function isUserPartOfCC()
    {
        return $this->ticketHeader->ccs->contains($this->user);
    }

    protected function isUserRequester()
    {
        return $this->user->id == $this->ticketHeader->requester_id;
    }

    protected function isTicketDeleted()
    {
        return $this->ticketHeader->status == 'D';
    }

    protected function isTicketMerged()
    {
        return $this->ticketHeader->status == 'M';
    }

    protected function isTicketSolved()
    {
        return $this->ticketHeader->status == 'S';
    }
}