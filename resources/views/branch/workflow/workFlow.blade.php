@extends('layouts.app')

@section('style')
<style type="text/css">
    .box-footer {
        border-top: 0px;
        margin: 0px 15px 15px 15px;
        padding: 0px;
    }
    .subtask {
        border: 1px solid black;
        border-radius: 10px;
    }

    .box-radius {
        border: 1px solid black; 
        border-radius: 10px; 
        margin: 0px 15px 15px 15px;
        overflow:hidden;
    }
    .box-summary { 
        padding: 30px;
        overflow: auto;
        white-space: nowrap;
    }

    .box-department {
        border-radius: 20px;
        box-shadow: 0px 0px 10px rgba(0,0,0, 0.2); 
        display: inline-block;
        min-height: 500px;
        margin-right: 30px;
        overflow-y: hidden;
        width: 300px;
        vertical-align: top;
    }

    .box-header {
        background: #00C0EF;
        border-bottom: 5px solid #4C8CCF;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        color: #FFFFFF;
        font-size: 24px;                    
    }

    .box-task-list {
        padding: 40px 30px 0px 30px;
        overflow-y: auto;
    }

    .box-task-item {
        background: #00C0EF;
        color: #FFFFFF;
        cursor: pointer;
        margin-bottom: 40px;
        padding: 30px 10px;
        position: relative;
        white-space: normal;
    }

    .box-item-number {
        background: #ffffff;
        color: #000000;
        border-radius: 50%;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        height: 40px;

        position: absolute;
        top: -20px;
        left: 10px;

        width: 40px;
    }

    .box-item-number span {
        font-size: 20px;
        line-height: 40px;
    }

    .box-item-details {
        color: #A3A3A3;
        font-size: 10px;

        position: absolute;
        top: -18px;
        left: 57px;
    }

    .box-item-status {
        color: #53F658;
        font-size: 20px;

        position: absolute;
        top: -27.5px;
        right: 0px;
    }

    .box-comments-count {
        font-size: 12px;
    }

    .main-footer {
        margin-left : 0px;
    }
    .custom-fl-right{
        float: right;
    }

    .ta-right{
        text-align: right;
    }
    #post-comment select{
        display: inline-block;
        width: auto;
        height: 32px;
        padding: 4px 8px;
    }

    #post-comment .custom-fl-right, .comment-post-reply .custom-fl-right{
        margin-top: 5px;
    }

    .comment-view{
        margin-top: 0px !important;
    }

    .comment-view a{
        display: inline-block;
        vertical-align: top;
        padding: 1px 4px;
        background-color: #FFFFFF;
        margin-right: 5px;
        box-shadow: 0px 1px 4px #CDCDCD;
    }

    #comment-list .comment{
        margin: 10px 0;
        width: 100%;
        box-sizing: border-box;
        padding: 10px;
        background-color: #FFFFFF;
    }

    #comment-list .comment .comment-block{
        margin: 0 10px;
        padding: 10px 0;
        border-bottom: 1px solid #D7D7D7;
    }

    #comment-list .comment .comment-reply{
        margin-left: 30px;
        display: none;
    }

    #comment-list .comment .comment-block .comment-author{
        font-weight: 600;
    }

    #comment-list .comment .comment-block .comment-date{
        color:#3A3A3A;
    }

    #comment-list .comment .comment-block .comment-desc{
        padding-top: 6px;
    }

    #comment-list .comment .comment-options{
        padding: 10px;
    }

    #comment-list .comment .comment-options a{
        margin-right: 10px;
    }

    #comment-list .comment .comment-options a.showless, #comment-list .comment .comment-options a.cancelreply{
        display: none;
    }

    #comment-list .comment .comment-post-reply{
        display: none;
        margin: 0 10px;
        padding-top: 10px;
    }

    .discussion {
        box-shadow: 0px 0px 2px #E6E6E6;
    }

    #comment-list select {
        display: inline-block;
        width: auto;
        height: 32px;
        padding: 4px 8px;
    }




    /** New Comment Section */
    .comment-section {
        width: 100%;
    }

    .comment-section .dotted-hr {
        margin-bottom: 10px;
    }

    .comment-section .clickable {
        cursor: pointer;
    }

    .comment-section .relative {
        position: relative;
    }

    .comment-section .overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.1);
        z-index: 2;
        cursor: pointer;
    }

    .comment-section .overlay-no-bg {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 2;
        cursor: pointer;
    }

    .comment-section #text {
        position: absolute;
        top: 50%;
        left: 50%;
        color: black;
        transform: translate(-50%,-50%);
        -ms-transform: translate(-50%,-50%);
    }

    .comment-section .input-section {
        border: 1px solid rgba(81, 203, 238, 1);
        border-radius: 10px;
    }

    .comment-section .reply-input-section {
        margin-top: 20px;
        margin-left: 0px;
        margin-bottom: 10px;
    }

    .comment-section .input-section textarea {
        resize:none;
        border:none;
        border-radius: 10px;
        border-bottom-left-radius: 0px;
        border-bottom-right-radius: 0px;
    }

    .comment-section .input-section .input-actions {
        padding: 10px; 
        background-color: white; 
        border-top: 1px solid rgb(0, 0, 0, 0.1);
        border-radius: 10px;
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;
    }

    .comment-section .input-section .input-actions select {
        display: inline-block;
        width: auto
    }

    .comment-section .input-section .input-actions i {
        font-size: 1.6em;
        vertical-align: middle;
    }

    .comment-section .input-section .input-assignees {
        padding: 10px; 
        background-color: white; 
        border-top: 1px solid rgb(0, 0, 0, 0.1);
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .comment-section .comment-area {
        background: white; 
        min-height: 150px;
        padding: 0px;
        border: 1px solid rgba(81, 203, 238, 1);
        border-radius: 10px;
    }

    .comment-section .attachments-box {
        padding: 10px 12px; 
        background-color: white; 
        border-top: 1px solid rgb(0, 0, 0, 0.1)
    }

    .comment-section .attachments-list {
        list-style:none; 
        margin: 0px; 
        padding-left: 0px;
        margin-bottom: 10px;
    }

    .comment-section .attachment-icon {
        margin-right: 5px;
        font-size: 0.85em;
    }

    .comment-section .comment-box {
        padding: 20px;
    }

    .comment-section .reply-comment-box {
        border-bottom: 1px solid rgb(0, 0, 0, 0.1);
        padding: 10px 0px 0px 0px;
    }

    .comment-section .reply-comment-box:last-child {
        border-bottom: 0px;
    }

    .comment-section .reply-comment-box:first-of-type {
        margin-top: 10px;
    }
    
    .comment-section .comment-box .comment-author {
        font-size: 1.15em
    }

    .comment-section .comment-box .comment-time {
        font-size: 0.85em
    }

    .comment-section .reply-comment-box .comment-author {
        font-size: 1em
    }

    .comment-section .reply-comment-box .comment-content {
        font-size: 0.85em
    }

    .comment-section .reply-comment-box .comment-time {
        font-size: 0.70em
    }

    .comment-section .reply-load-more {
        color: blue;
        font-size: 0.80em;
        margin-top: 10px;
    }

    .comment-section .attachment-remove {
        color: red
    }

    .comment-section .reply-buttons {
        font-size: 0.85em;
        padding-top: 10px;
        border-top: 1px solid rgb(0, 0, 0, 0.1);
        margin-bottom: 0px;
    }

    .comment-section .comment-box-even {
    }

    .comment-section .comment-box-odd {
        background-color: #f7fbfd;
    }
</style>
@endsection


@section('content')

    <link rel="stylesheet" type="text/css" href="{{ "/css/tokenize2.css" . "?v=" . config("app.version") }}">
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                {{$headername}}
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><a href="/merchants/branch">Branch</a></li>
                <li class="active">{{$merchant->partner_company->company_name}}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">

            <form id="frmWorkflow" name="frmWorkflow"  method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
            <input type="hidden" id="txtDetailList" name="txtDetailList">
            <input type="hidden" id="txtSubTaskStatus" name="txtSubTaskStatus">
            <input type="hidden" id="txtDetailList" name="txtDetailList">
            <input type="hidden" id="txtSubTaskID" name="txtSubTaskID" value="{{$subtask->id}}">
            <input type="hidden" id="txtOrderID" name="txtOrderID" value="{{$subtask->order_id}}">
            <input type="hidden" id="txtPID" name="txtPID" value="{{$order->product_id}}">

            <input type="hidden" id="txtTaskLineNo" name="txtTaskLineNo" value="">
            <input type="hidden" id="txtTaskNo" name="txtTaskNo" value="">
            <input type="hidden" id="txtTaskName" name="txtTaskName" value="">
            <input type="hidden" id="txtTaskAssignee" name="txtTaskAssignee" value="">
            <input type="hidden" id="txtDueOn" name="txtDueOn" value="">
            <input type="hidden" id="txtTaskLink" name="txtTaskLink" value="">
            <input type="hidden" id="txtTaskLinkText" name="txtTaskLinkText" value="">

            </form>

            <div class="box-body">
                <span class="pull-right">
                    <h5>Product Status: <small><strong>{{$order->product_status}}</strong></small></h5>
                </span>

                <span><h5><strong>
                @foreach($categories as $cat)
                    {{$cat}}<br>
                @endforeach
                </strong></h5> </span>

                <span>
                    <h5>Day of Completion: <small><strong id="taskDueDate">{{ date_format(new DateTime($subtask->due_date),"m/d/Y")}}</strong></small></h5>
                </span>

            </div>

            @if (!$allCompleted && !$isAgent)
                <div class="col-md-12 text-right">
                    <label>
                        <input type="checkbox" class="markAllTaskAsComplete" /> &nbsp;

                        <strong>
                            Mark All Task as Complete
                        </strong>
                    </label>

                    <form id="form-mark-all" method="POST" class="hidden" action="/merchants/branchWorkflow/{{ $merchant->id }}/{{ $subtask->order_id }}">
                        @csrf
                        <input type="hidden" name="sub_task_id" value="{{ $subtask->id }}"/>
                    </form>
                </div>
            @endif

            <div class="box-radius">
                <div class="box-summary">
                    @foreach ($subTaskDetailGroups as $subTaskDetails)
                        <div class="box-department">
                            <div class="box-header text-center">
                                    @if (isset($subTaskDetails->first()->department_id))
                                    <strong style="color: {{$subTaskDetails->first()->department->color }}">
                                        {{ $subTaskDetails->first()->department->description }}
                                    </strong>
                                    @else
                                     <strong>
                                        Unassigned
                                    </strong>
                                    @endif
                            </div>

                            <div class="box-task-list">
                                @foreach ($subTaskDetails as $subTaskDetail)
                                    <div class="box-task-item text-center" data-link_number="{{$subTaskDetail->link_number}}">
                                        <div class="box-item-number text-center">
                                            <span><strong>{{ $subTaskDetail->task_no }}</strong></span>
                                        </div>
    
                                        <div class="box-item-details">
                                            <span>
                                                @if ($subTaskDetail->prerequisite != '')
                                                    Pre-req: Task No. {{ $subTaskDetail->prerequisite }}
                                                @endif
                                            </span>
                                        </div>
    
                                        <div class="box-item-status">
                                            @switch ($subTaskDetail->status)
                                                @case ('C')
                                                    <i class="fa fa-check" data-toggle="tooltip" data-placement="right" title="Complete"></i>
                                                    @break

                                                @case ('V') 
                                                    <i class="fa fa-times" data-toggle="tooltip" data-placement="right" title="Cancelled" style="color: red !important"></i>
                                                    @break

                                                @case ('I')
                                                    <i class="fa fa-th-list" data-toggle="tooltip" data-placement="right" title="In Progress" style="color: blue !important"></i>
                                                    @break

                                                @case ('P')
                                                    <i class="fa fa-pause" data-toggle="tooltip" data-placement="right" title="Pending" style="color: blue !important"></i>
                                                    @break

                                                @default
                                                    <i class="fa fa-th-list" data-toggle="tooltip" data-placement="right" title="To do" style="color: blue !important"></i>
                                            @endswitch
                                        </div>
                                        
                                        <span><strong>{{ $subTaskDetail->name }}</strong></span> <br/>
                                        <span style="font-size: 0.8em; {{ $subTaskDetail->due_date->isPast() ? 'color: red' : '' }}" >({{ $subTaskDetail->due_date->format('F j Y') }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="box-footer">
                <div id="subtask-wrapper">
                    @php
                        $colors = [
                            'N' => 'secondary',
                            'I' => 'primary',
                            'P' => 'warning',
                            'S' => 'success'
                        ];
                    @endphp 

                    @foreach($subtask->details as $detail)
                        <div class="subtask" id="subtask{{$detail->link_number}}" data-taskStatus = "{{$detail->status}}">
                            <div class="row">
                                <div class="col-sm-1 ta-right">
                                    <h5 class="subtasknum" value="{{ $detail->task_no }}">
                                        #{{ $detail->task_no }}
                                    </h5>
                                </div>

                                <div class="col-sm-11 subtaskcontent{{ $detail->link_number }}">
                                    <div class="row">
                                        <div class="col-sm-11">
                                            <div class="checkbox inline-input">
                                                @if ($detail->status != "V")
                                                    @if ($detail->userList != '')
                                                        <label>
                                                            @if (!$isAgent)
                                                                @if (isset($detail->prereqSubtaskDetail) && !($detail->prereqSubtaskDetail->status == 'C' || $detail->prereqSubtaskDetail->status == 'V'))
                                                                    <input type="checkbox" 
                                                                        class="subtaskstatus" 
                                                                        data-taskNo="{{$detail->task_no}}" 
                                                                        {{ $detail->status == 'C' ? 'checked' : ''}}
                                                                        data-toggle="tooltip" data-placement="top" title="Disabled! Prerequisite not Completed"
                                                                        disabled />
                                                                @else
                                                                    <input type="checkbox" 
                                                                        class="subtaskstatus" 
                                                                        data-taskNo="{{$detail->task_no}}" 
                                                                        {{ $detail->status == 'C' ? 'checked' : ''}} />
                                                                @endif
                                                            @endif

                                                            <strong id="taskUsers-{{ $detail->link_number }}">&nbsp;&nbsp;
                                                                @if ($detail->status == "C")
                                                                    <strike>{{ $detail->userList }}</strike>
                                                                @else 
                                                                    {{ $detail->userList }}
                                                                @endif
                                                            </strong>
                                                        </label>
                                                    @else
                                                        <label>
                                                            <strong>
                                                                NO ASSIGNEES
                                                            </strong>
                                                        </label>
                                                    @endif
                                                @else
                                                    <label>
                                                        <strong id="taskUsers-{{$detail->link_number}}">
                                                            {{ $detail->userList }}
                                                        </strong>
                                                    </label>
                                                @endif

                                                
                                                &nbsp;&nbsp;&nbsp;
                                                <span class="badge badge-secondary">{{ \App\Models\SubTaskDetail::STATUSES[(isset($detail->status)  && $detail->status != '') ? $detail->status : 'T'] }}</span>

                                                &nbsp;&nbsp;&nbsp;
                                                <a href="/tickets/create?orderId={{ $order->id }}&subTaskDetailId={{ $detail->id }}" target="_blank"
                                                    class="btn btn-sm" role="button" 
                                                    data-toggle="tooltip" data-placement="right" title="Create Ticket">
                                                    <i class="fa fa-ticket"></i>
                                                </a>
                                            </div>

                                            <p class="subtask-info" id="taskName-{{$detail->link_number}}" style="word-wrap: break-word;">
                                                @if ($detail->status == "C")
                                                    <strike>
                                                        {{ $detail->name }}
                                                    </strike>
                                                @else 
                                                    {{ $detail->name }} 
                                                @endif
                                            </p>

                                            <p class="inline-input subtask-info">
                                                <strong>Department:</strong>
                                                {{ isset($detail->department) ? $detail->department->description : 'No Department' }} 
                                            </p>

                                            <p class="inline-input subtask-info">
                                                <strong id="taskDue-{{ $detail->link_number }}">
                                                    Due Date: 
                                                </strong>

                                                {{ date_format(new DateTime($detail->due_date), "m/d/Y") }} 
                                            </p>

                                            @if ($detail->status == "C")
                                                <p class="inline-input subtask-info">
                                                    <strong id="taskDue-{{ $detail->link_number }}">
                                                        Date of Completion: 
                                                    </strong>

                                                    {{ date_format(new DateTime($detail->completion_date),"m/d/Y") }}
                                                </p>
                                            @endif

                                            @if (isset($detail->prereqSubtaskDetail) && $detail->prereqSubtaskDetail->status != 'V')
                                                <p class="inline-input subtask-info">
                                                    @if ($detail->prereqSubtaskDetail->status == 'C')
                                                        <strong>Prerequisite: </strong> 
                                                        <strike>Task No. {{ $detail->prereqSubtaskDetail->task_no }} {{ $detail->prereqSubtaskDetail->name }}</strike>
                                                    @else
                                                        <strong>Prerequisite: </strong> Task No. {{ $detail->prereqSubtaskDetail->task_no }} {{ $detail->prereqSubtaskDetail->name }}
                                                    @endif
                                                </p>
                                            @endif

                                            @if ($detail->status == "V")
                                                <p class="inline-input subtask-info">
                                                    <strong id="taskDue-{{ $detail->link_number }}" style="color:red">
                                                        ASSIGNMENT IS CANCELLED
                                                    </strong>
                                                </p>
                                            @endif
                                        </div>

                                        <div class="col-sm-1 ta-right">
                                            <div class="subtask-options">
                                                @if ($detail->status == "" && !$isAgent) 
                                                    <a href="javascript:void(0);" class="ReminSubtask text-green" data-userList="{{ $detail->userList }}" title="Remind Subtask">
                                                        <i class="fa fa-bell"></i>
                                                    </a>
                                                    
                                                    @hasAccess('merchant', 'edit workflow', null)
                                                        <a href="javascript:void(0);" class="EditSubtask text-blue" data-subid="{{ $detail->link_number }}" title="Edit Subtask">
                                                            <i class="fa fa-pencil-square"></i>
                                                        </a>
                                                    @endhasAccess

                                                    @hasAccess('merchant', 'delete workflow tasks')
                                                        <a href="javascript:void(0);" class="DelSubtask text-red" data-subid="subtask{{ $detail->link_number }}" data-lineNo="{{ $detail->link_number }}" data-taskNo="{{ $detail->task_no }}" title="Cancel Subtask">
                                                            <i class="fa fa-times-circle"></i>
                                                        </a>
                                                    @endhasAccess
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-11 subtaskcontent-edit{{$detail->link_number}}" style="display: none;">
                                    <input type="hidden" id="txtSubTaskNo-{{$detail->link_number}}" name="txtSubTaskNo-{{$detail->link_number}}" value="{{$detail->task_no}}">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">Assignment:</div>
                                            <input type="text" class="form-control" id="txtSubTaskName-{{$detail->link_number}}" name="txtSubTaskName-{{$detail->link_number}}" value="{{$detail->name}}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group-col-sm-4">
                                            <div class="form-group">
                                                <label>Department:</label>
                                                <select name="department_id" class="form-control">
                                                    <option value="-1">--Select Department--</option>
                                                    <optgroup label="{{ $departments->first()->partnerCompany->company_name }}"> 
                                                        @foreach ($departments as $department)
                                                            <option value="{{ $department->id }}" {{ $detail->department_id == $department->id ? 'selected' : ''}}>
                                                                {{ $department->description }}
                                                            </option>
                                                        @endforeach                                                        
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>

                                        @php $class="hidden"; @endphp
                                        @hasAccess('merchant', 'assign workflow', isset($detail->department_id) ? $detail->department_id : null)
                                            @php $class="" @endphp
                                        @endhasAccess

                                        <div class="form-group col-sm-4 {{ $class }}">
                                            <div class="form-group">
                                                <label>Assignees:</label>
                                                <select class="form-control assignees" id="stassign-{{$detail->link_number}}" data-is_hidden="{{ $class }}" multiple>
                                                    @foreach ($users as $user)
                                                        <option value="{{$user->id}}" 
                                                        @foreach ($detail->assignee as $assignee)
                                                            @if($assignee == $user->id) 
                                                                selected 
                                                            @endif
                                                        @endforeach
                                                            >{{$user->name}} ({{ $user->department }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group col-sm-2 custom-input-15">
                                            <label>Specify Due On:</label>
                                            <input type="text" class="form-control custom-input-8 dueDate" value=" {{ date_format(new DateTime($detail->due_date),"m/d/Y")}} " id="txtDueDate-{{$detail->link_number}}" name="txtDueDate-{{$detail->link_number}}">
                                        </div>


                                        <div class="subtasklink col-sm-11 ta-right">
                                            <div class="form-group inline-input">
                                                <a href="javascript:void(0);" class="btn btn-danger btn-xs cancelChanges" data-subid="{{$detail->link_number}}">Cancel</a>
                                                <a href="javascript:void(0);" class="btn btn-success btn-xs saveSubTask" data-subid="{{$detail->link_number}}">Save</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (count($detail->ticketHeaders) > 0)
                                    <div class="col-sm-10 offset-sm-1" id="ticket-section">
                                        <div className="dotted-hr"></div>
                                        <p><b>Tickets</b></p>

                                        <ul style="list-style:none; padding: 0px 10px">
                                            @foreach ($detail->ticketHeaders as $ticketHeader)
                                                <li>
                                                    <a href="/tickets/{{ $ticketHeader->id }}/edit/">
                                                        <span style="width: 30px; display: inline-block" 
                                                            class="text-right">{{ $ticketHeader->id }}</span>&nbsp;&nbsp;
                                                        <span>{{ $ticketHeader->subject }}</span>&nbsp;
                                                    </a>
                                                    <span class="badge badge-{{ $colors[$ticketHeader->status] }}">{{ $ticketHeader->ticketStatus->description }}</span>
                                                    <span class="remove-link" data-ticket_header_id="{{ $ticketHeader->id }}" style="color:red; cursor:pointer">&nbsp;&nbsp;&nbsp;<i class="fa fa-remove"></i></span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            

                                @if ($detail->userList != '')
                                    <div class="comment-section" 
                                        data-order_id="{{ $order->id }}" 
                                        data-sub_task_detail_id="{{ $detail->id }}" 
                                        data-order_statuses='@json($status)'>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="subtask-options pull-right">
                    <div class="subtask-option2 " >
                        <!-- <a href="javascript:void(0);" id="btnsortSubtask"><i class="fa fa-sort"></i> Re-arrange Subtask</a> -->
                        <a href="javascript:void(0);" id="btnAddNewTask"><i class="fa fa-plus-circle"></i> Add Subtask</a>
                    </div>
<!--                     <div class="subtask-option3">
                        <a href="javascript:void(0);" id="btnsortSubtaskD"><i class="fa fa-check"></i> Done Sorting</a>
                    </div> -->
                    <div class="addsubtask-option" style="display:none;">
                        <a href="javascript:void(0);" class="btn btn-danger btn-xs" id="cancelSubtask"><i class="fa fa-times"></i> Cancel</a>
                        <a href="javascript:void(0);" class="btn btn-success btn-xs" id="addSubtask"><i class="fa fa-check"></i> Submit</a>
                    </div>
                </div> 
            </div>
    </section>

    {{-- Start Server Data --}}
    @hasAccess('merchant', 'assign workflow', isset($detail->department_id) ? $detail->department_id : null)
        <input type="hidden" name="hasAssignWorkflow" value="1" />
    @else
        <input type="hidden" name="hasAssignWorkflow" value="0" />
    @endhasAccess    
    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" />
    {{-- End Server Data --}}
@endsection

@section("script")
    <script>
        let departments = @json($departments)

        $('.subtask').hide()
        $('.box-task-item').on('click', function(){
            $('.subtask').hide()

            let linkNumber = $(this).data('link_number')
            let subTask = $('#subtask' + linkNumber)
            
            subTask.show()
            $("html, body").animate({ scrollTop: $(document).height() - $(window).height() });
        })
    </script>
    <script src="{{ config('app.cdn') . '/js/merchants/commentSection/commentSection.js' . '?v=' . config('app.version') }}"></script>
    <script src="{{ config("app.cdn") . "/js/merchants/details.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/merchants/workflow.js" . "?v=" . config("app.version") }}"></script>
@endsection