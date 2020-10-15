<?php

namespace App\Services\Tickets;

use App\Contracts\TicketActionService;
use App\Models\Access;
use App\Models\EmailOnQueue;
use App\Models\TicketDetail;
use App\Models\TicketDetailsAttachment;
use App\Models\TicketHeader;
use App\Models\TicketStatus;
use App\Models\User;
use App\Models\UserType;
use App\Services\BaseServiceImpl;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class TicketActionServiceImpl extends BaseServiceImpl implements TicketActionService
{
    public function assignToMeTickets($ticketIds)
    {
        $userId = auth()->user()->id;
        $userTypeIds = explode(',', auth()->user()->user_type_id);
        $companyId = auth()->user()->company_id;

        $assignedTicketIds = array();
        $unprocessedTicketIds = array();

        $ticketHeaders = TicketHeader::whereIn('id', $ticketIds)
            ->with('assignee')
            ->get();

        foreach ($ticketHeaders as $ticketHeader) {
            $condition1 = in_array($ticketHeader->department, $userTypeIds);
            $condition2 = $ticketHeader->assignee == null || $ticketHeader->assignee == -1;
            $condition3 = Access::hasPageAccess('ticketing', 'assign', true);
            $condition4 = UserType::find($ticketHeader->department)->head_id == auth()->user()->id;

            if ( ($condition1 && ($condition4 || $condition2)) || $condition3) {
                $this->changeAssignee($ticketHeader, $companyId, 
                    $userTypeIds[0], $userId);
                $this->autoReplyOnAction($ticketHeader->department, 
                    'Assigned Ticket to Myself');

                $assignedTicketIds[] = $ticketHeader->id;
                continue;
            }

            $unprocessedTicketIds[] = $ticketHeader->id;
        }

        $resultObject = (object) [
            'ticketHeaders' => TicketHeader::whereIn('id', $assignedTicketIds)->get(),
            'assignedTicketIds' => $assignedTicketIds,
            'unprocessedTicketIds' => $unprocessedTicketIds
        ];

        return $resultObject;
    }

    public function assignTickets($ticketIds, $departmentId, $assigneeId)
    {
        $user = User::find($assigneeId);
        $userType = UserType::find($departmentId);
        $companyId = $userType->company_id;

        $assignedTicketIds = array();
        $unprocessedTicketIds = array();

        $ticketHeaders = TicketHeader::whereIn('id', $ticketIds)
            ->with('assignee')
            ->get();

        foreach ($ticketHeaders as $ticketHeader) {
            $currentUserType = UserType::find($ticketHeader->department);
            if (is_null($currentUserType)) {
                $condition1 = false;
            } else {
                $condition1 = $userType->head_id == auth()->user()->id;
            }
            
            $condition2 = Access::hasPageAccess('ticketing', 'assign', true);
            $condition3 = $ticketHeader->assignee == auth()->user()->id;

            if ($condition1 || $condition2 || $condition3) {
                if ($assigneeId == -1) {
                    $message = "Assigned to {$userType->description} Department";
                } else {
                    $message = "Assigned to {$user->first_name} {$user->last_name}";
                }

                $this->changeAssignee($ticketHeader, $companyId, $departmentId, $assigneeId);
                $this->autoReplyOnAction($ticketHeader->id, $message);

                $assignedTicketIds[] = $ticketHeader->id;
                continue;
            }

            $unprocessedTicketIds[] = $ticketHeader->id;
        }

        $resultObject = (object) [
            'ticketHeaders' => TicketHeader::whereIn('id', $assignedTicketIds)->get(),
            'assignedTicketIds' => $assignedTicketIds,
            'unprocessedTicketIds' => $unprocessedTicketIds
        ];

        return $resultObject;
    }

    public function mergeTickets($ticketIds)
    {
        sort($ticketIds, SORT_NUMERIC);
        $ticketHeaders = TicketHeader::whereIn('id', $ticketIds)
            ->with('ticketDetails')
            ->get();

        $requesterId = [];
        foreach ($ticketHeaders as $ticketHeader) {
            $requesterId[] = $ticketHeader->requester_id;
        }

        if (count(array_unique($requesterId)) != 1) {
            $resultObject = (object) [
                'success' => false,
            ];

            return $resultObject;
        } 

        DB::beginTransaction();
        try {
            $parentTicketHeaderId = null;
            foreach ($ticketHeaders as $ticketHeader) {
                if (is_null($parentTicketHeaderId)) {
                    $parentTicketHeaderId = $ticketHeader->id;

                    $ticketHeader->update(['update_by' => auth()->user()->username]);
                    continue;
                }

                /** Store Ticket Header as Ticket Detail  */
                $ticketDetail = TicketDetail::create([
                    'ticket_id' => $parentTicketHeaderId,
                    'message' => $ticketHeader->subject,
                    'create_by' => $ticketHeader->create_by,
                    'update_by' => $ticketHeader->update_by,
                ]);

                $ticketHeader->update([
                    'status' => 'M',
                    'update_by' => auth()->user()->username
                ]);

                foreach ($ticketHeader->attachments as $attachment) {
                    $ticketDetailAttachment = TicketDetailsAttachment::make([
                        'name' => $attachment->name,
                        'path' => $attachment->path,
                        'ticket_detail_id' => $ticketDetail->id,
                    ]);

                    $ticketDetail->attachments()->save($ticketDetailAttachment);
                }

                /** Clone Ticket Details */
                foreach ($ticketHeader->ticketDetails as $ticketDetail) {
                    $clone = $ticketDetail->replicate();
                    $clone->ticket_id = $parentTicketHeaderId;
                    $clone->save();

                    /** @todo Change approach to only save once. */
                    foreach ($ticketDetail->attachments as $attachment) {
                        $clone->attachments()->save($attachment);
                    }
                }
            }

            $this->autoReplyOnAction($parentTicketHeaderId, 
                'Merged tickets ' . implode(', ', $ticketIds));

            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
        }

        $resultObject = (object) [
            'ticketHeaders' => TicketHeader::where('id', $parentTicketHeaderId)->get(),
            'success' => true,
        ];

        return $resultObject;
    }

    public function deleteTickets($ticketIds)
    {
        $deletedTicketIds = array();
        $unprocessedTicketIds = array();

        $ticketHeaders = TicketHeader::whereIn('id', $ticketIds)->get();
        foreach ($ticketHeaders as $ticketHeader) {
            if ($ticketHeader->create_by == auth()->user()->username) {
                $ticketHeader->update([
                    'status' => 'D',
                    'update_by' => auth()->user()->username
                ]);
                
                $deletedTicketIds[] = $ticketHeader->id;
                continue;
            } 

            $unprocessedTicketIds[] = $ticketHeader->id;
        }

        $resultObject = (object) [
            'ticketHeaders' => TicketHeader::withoutGlobalScopes()->whereIn('id', $deletedTicketIds)->get(),
            'deletedTicketIds' => $deletedTicketIds,
            'unprocessedTicketIds' => $unprocessedTicketIds
        ];

        return $resultObject;
    }

    private function autoReplyOnAction($ticketHeaderId, $message)
    {
        $ticketDetail = TicketDetail::create([
            'ticket_id' => $ticketHeaderId,
            'message' => $message,
            'create_by' => auth()->user()->username,
            'update_by' => auth()->user()->username,
            'is_internal' => 1,
        ]);
    }

    private function changeAssignee($ticketHeader, $companyId, $departmentId, $userId)
    {
        $ticketHeader->company_id = $companyId;
        $ticketHeader->department = $departmentId;
        $ticketHeader->assignee = $userId;
        $ticketHeader->update_by = auth()->user()->username;
        return $ticketHeader->save();
    }   
}