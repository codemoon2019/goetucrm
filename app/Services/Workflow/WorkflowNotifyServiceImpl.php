<?php

namespace App\Services\Workflow;

use App\Contracts\Workflow\WorkflowNotifyService;
use App\Models\EmailOnQueue;
use App\Models\Notification;
use App\Models\ProductOrder;
use App\Models\SubTaskDetail;
use App\Models\User;
use App\Models\UserType;
use App\Services\BaseServiceImpl;

class WorkflowNotifyServiceImpl extends BaseServiceImpl implements WorkflowNotifyService
{
    public function notifyOnCreate(ProductOrder $productOrder, $associatedUsers)
    {
        $notificationData = [];

        /** Get Department Heads */
        $departmentObjects = $this->getAssociatedDepartmentHeads($productOrder);

        /** Construct System and Email Message */
        $baseSysMsg  = "Case #{$productOrder->id} has been Ordered | ";

        $baseEmailMsg  = "<a href='" . url('/') . "/merchants/workflow/";
        $baseEmailMsg .= "{$productOrder->partner_id}/{$productOrder->id}'>";
        $baseEmailMsg .=    "Case #{$productOrder->id} has been Ordered. <br/>";
        $baseEmailMsg .=    "Start Assigning task to users. <br/>";
        $baseEmailMsg .= "</a>";

        $timestamp = date('Y-m-d H:i:s');
        foreach ($departmentObjects as $departmentObject) {
            $subTaskDetails = $productOrder->subTaskHeader->subTaskDetails()
                ->where('department_id', $departmentObject->departmentId)
                ->get();
            
            $sysMsg  = $baseSysMsg;
            $sysMsg .= "Tasks assigned to your Department: ";
            $emailMsg = $baseEmailMsg . '<br/><br/>';
            foreach ($subTaskDetails as $subTaskDetail) {
                /** Construct System Message */
                $sysMsg .= "Task #{$subTaskDetail->task_no} - ";

                if (strlen($subTaskDetail->name) > 20) {
                    $sysMsg .= substr($subTaskDetail->name, 0, 20);
                    $sysMsg .= '...';
                } else {
                    $sysMsg .= $subTaskDetail->name;
                    $sysMsg .= " | ";
                }

                /** Construct Email Message */
                $emailMsg .= "Task #{$subTaskDetail->task_no} - ";
                $emailMsg .= "{$subTaskDetail->name}<br/>";
            }

            $sysMsg = substr($sysMsg, 0, strlen($sysMsg) - 3);

            /** Construct Notify Department Head */
            $notificationData[] = [
                'partner_id' => -1,
                'source_id' => -1,
                'subject' => 'New Case Order',
                'message' => $sysMsg,
                'status' => 'N',
                'create_by' => $productOrder->create_by,
                'update_by' => $productOrder->update_by,
                'redirect_url' => "/merchants/workflow/" .
                    "{$productOrder->partner_id}/{$productOrder->id}",
                'recipient' => $departmentObject->departmentHead->username,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];

            /** Create Email on Queue */
            $emailData = [
                'email' => (object) [
                    'subject' => 'New Case Order', 
                    'body'=> $emailMsg
                ]
            ];

            if ($departmentObject->departmentHead->email_address && 
                $departmentObject->departmentHead->worflow_email === true) {

                $emailBody = view("mails.basic4", $emailData)->render();

                $emailOnQueue = new EmailOnQueue;
                $emailOnQueue->subject = 'New Case Order';
                $emailOnQueue->body = $emailBody;
                $emailOnQueue->email_address = $departmentObject->departmentHead
                    ->email_address;
                $emailOnQueue->create_by = auth()->user()->username;
                $emailOnQueue->is_sent = 0;
                $emailOnQueue->sent_date = null;
                $emailOnQueue->save();
            }
        }

        $departmentHeadEmails = [];
        $departmentHeadUsernames = [];
        foreach ($departmentObjects as $departmentObject) {
            $departmentHeadUsernames[] = $departmentObject->departmentHead->username;

            if (isset($departmentObject->departmentHead->email_address)) {
                $departmentHeadEmails[] = $departmentObject->departmentHead
                    ->email_address;
            }
        }

        $userEmails = isset($productOrder->createdBy->email_address) ?
            [$productOrder->createdBy->email_address] : [];

        $usernames = [$productOrder->create_by];
        foreach ($associatedUsers as $user) {
            $usernames[] = $user->username;

            if ($user->email_address && $user->workflow_email === true) {
                $userEmails[] = $user->email_address;
            }
        }

        $usernames = array_diff(array_unique($usernames), 
            array_unique($departmentHeadUsernames));

        foreach ($usernames as $username) {
            $notificationData[] = [
                'partner_id' => -1, 
                'source_id' => -1,
                'subject' => 'New Case Order',
                'message' => $baseSysMsg . 'Start assigning task to Users',
                'status' => 'N',
                'create_by' => $productOrder->create_by,
                'update_by' => $productOrder->update_by,
                'redirect_url' => "/merchants/workflow/" .
                    "{$productOrder->partner_id}/{$productOrder->id}",
                'recipient' => $username,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        /** Create Email on Queue */
        $emailData = [
            'email' => (object) [
                'subject' => 'New Case Order', 
                'body'=> $baseEmailMsg
            ]
        ];

        $userEmails = implode(',', array_diff(array_unique($userEmails), 
            array_unique($departmentHeadEmails)));
        if ($userEmails != '') {
            $emailBody = view("mails.basic4", $emailData)->render();

            $emailOnQueue = new EmailOnQueue;
            $emailOnQueue->subject = 'New Case Order';
            $emailOnQueue->body =  $emailBody;
            $emailOnQueue->email_address = $userEmails;
            $emailOnQueue->create_by = auth()->user()->username;
            $emailOnQueue->is_sent = 0;
            $emailOnQueue->sent_date = null;
            $emailOnQueue->save();
        }

        Notification::insert($notificationData);
    }

    public function notifyOnAssign(ProductOrder $productOrder, SubTaskDetail $subTaskDetail)
    {
        $originalAssociatedUserIds = json_decode($subTaskDetail->getOriginal('assignee'));
        $newAssociatedUserIds = json_decode($subTaskDetail->assignee);

        $associatedUserIds = array_diff($newAssociatedUserIds, $originalAssociatedUserIds);
        $associatedUsers = User::select('id','username','email_address')
            ->find($associatedUserIds);

        $userEmails = [];
        $usernames = [];
        foreach ($associatedUsers as $associatedUser) {
            $usernames[] = $associatedUser->username;

            if ($associatedUser->email_address && $associatedUser->workflow_email === true) {
                $userEmails[] = $associatedUser->email_address;
            }
        }

        $notificationData = [];
        $timestamp = date('Y-m-d H:i:s');
        foreach ($usernames as $username) {
            $notificationData[] = [
                'partner_id' => -1, 
                'source_id' => -1,
                'subject' => "Task has been assigned to you",
                'message' => "Task #{$subTaskDetail->task_no} - {$subTaskDetail->name}",
                'status' => 'N',
                'create_by' => auth()->user()->username,
                'update_by' => auth()->user()->username,
                'redirect_url' => "/merchants/workflow/" .
                    "{$productOrder->partner_id}/{$productOrder->id}",
                'recipient' => $username,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        Notification::insert($notificationData);


        $emailData = [
            'email' => (object) [
                'subject' => 'Task has been assigned to you', 
                'body'=> 
                    "<a href='" . url('/') . "/merchants/workflow/" .
                    "{$productOrder->partner_id}/{$productOrder->id}'>" .
                        "Task #{$subTaskDetail->task_no} - {$subTaskDetail->name} <br/>" .
                        "Task has been assigned to you." .
                    "<a>"
            ]
        ];

        $userEmails = implode(',', array_unique($userEmails));
        if ($userEmails != '') {
            $emailBody = view("mails.basic4", $emailData)->render();

            $emailOnQueue = new EmailOnQueue;
            $emailOnQueue->subject = 'Task has been assigned to you';
            $emailOnQueue->body =  $emailBody;
            $emailOnQueue->email_address = $userEmails;
            $emailOnQueue->create_by = auth()->user()->username;
            $emailOnQueue->is_sent = 0;
            $emailOnQueue->sent_date = null;
            $emailOnQueue->save();
        }
    }

    public function notifyOnCompletion(ProductOrder $productOrder, 
        SubTaskDetail $subTaskDetailPreReq, SubTaskDetail $subTaskDetail)
    {
        $associatedUserIds = json_decode($subTaskDetail->assignee);
        $associatedUsers = User::select('id','username','email_address')
            ->find($associatedUserIds);

        $userEmails = [];
        $usernames = [];
        foreach ($associatedUsers as $associatedUser) {
            $usernames[] = $associatedUser->username;

            if ($associatedUser->email_address && $associatedUser->workflow_email === true) {
                $userEmails[] = $associatedUser->email_address;
            }
        }

        $timestamp = date('Y-m-d H:i:s');
        $notificationData = array();
        foreach ($usernames as $username) {
            $notificationData[] = [
                'partner_id' => -1, 
                'source_id' => -1,
                'subject' => "Case Order #{$productOrder->id} - Prerequisite Task Completed",
                'message' => "Task #{$subTaskDetailPreReq->task_no} - " .
                    "{$subTaskDetailPreReq->name} has been completed | " .
                    "You may now start working on Task #{$subTaskDetail->task_no} - " .
                    "{$subTaskDetail->name}",
                'status' => 'N',
                'create_by' => auth()->user()->username,
                'update_by' => auth()->user()->username,
                'redirect_url' => "/merchants/workflow/" .
                    "{$productOrder->partner_id}/{$productOrder->id}",
                'recipient' => $username,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        Notification::insert($notificationData);


        $emailData = [
            'email' => (object) [
                'subject' => "Case Order #{$productOrder->id} - Prerequisite Task Completed", 
                'body'=> 
                    "<p style='font-size:1.15em'><strong>Case Order #{$productOrder->id}</strong></p><br/>" .
                    "Task #{$subTaskDetailPreReq->task_no} - " .
                    "{$subTaskDetailPreReq->name} has been completed <br/><br/>" .
                    "<a href='" . url('/') . "/merchants/workflow/" .
                    "{$productOrder->partner_id}/{$productOrder->id}'>" .    
                        "You may now start working on Task #{$subTaskDetail->task_no} - " .
                        "{$subTaskDetail->name}" .
                    "</a>"
            ]
        ];

        $userEmails = implode(',', array_unique($userEmails));
        if ($userEmails != '') {
            $emailBody = view("mails.basic4", $emailData)->render();

            $emailOnQueue = new EmailOnQueue;
            $emailOnQueue->subject = "Case Order #{$productOrder->id} - Prerequisite Task Completed";
            $emailOnQueue->body =  $emailBody;
            $emailOnQueue->email_address = $userEmails;
            $emailOnQueue->create_by = auth()->user()->username;
            $emailOnQueue->is_sent = 0;
            $emailOnQueue->sent_date = null;
            $emailOnQueue->save();
        }
    }

    /**
     * Private Functions
     */

    private function getAssociatedDepartmentHeads(ProductOrder $productOrder)
    {
        $departmentHeads = [];

        $departmentIds = [];
        $subTaskDetails = $productOrder->subTaskHeader->subTaskDetails;
        foreach ($subTaskDetails as $subTaskDetail) {
            if (is_null($subTaskDetail->department_id)) {
                continue;
            }

            $departmentIds[] = $subTaskDetail->department_id;
        }

        $departments = UserType::with('departmentHead:id,username,email_address')
            ->find( array_unique($departmentIds) );

        foreach ($departments as $department) {
            if (isset($department->departmentHead)) {
                $departmentHeads[] = (object) [
                    'departmentId' => $department->id,
                    'departmentHead' => $department->departmentHead
                ];
            }
        }

        return $departmentHeads;
    }
}