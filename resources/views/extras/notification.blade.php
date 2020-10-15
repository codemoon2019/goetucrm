@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Notification
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li class="active"> Notification</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-2 mb-plus-20">
                    <div class="list-group">
                        <a href="{{ url('#new') }}" class="list-group-item @if($active_class=='new') active @endif" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-envelope-o"></i><span class="label label-primary pull-right">{{$new_message_count}}</span> &nbsp;&nbsp; New 
                        </a>
                        <a href="{{ url('#read') }} " class="list-group-item @if($active_class=='read') active @endif" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-envelope-open-o"></i> &nbsp;&nbsp; Read
                        </a>
                        <a href="{{ url('#starred') }} " class="list-group-item @if($active_class=='starred') active @endif" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-star"></i> &nbsp;&nbsp; Starred
                        </a>
                        <a href="{{ url('#assignment') }} " class="list-group-item @if($active_class=='assignment') active @endif" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-tasks"></i> &nbsp;&nbsp; Assignment
                        </a>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Inbox</h3>
                        </div>
                        <div class="box-body no-padding">
                            <div class="table-responsive mailbox-messages">
                                <div id="div-tabs" class="tab-content no-padding">
                                    <div class="tab-pane @if($active_class=='new') active @endif" id="new">
                                        <form id="frmUpdateInbox" name="frmUpdateInbox"  method="post">
                                            <div class="btn-group" style="padding-bottom:5px;">
                                                <button type="button" class="btn btn-block btn-primary btn-flat" id="btnMarkRead">Mark as Read</button>
                                            </div>
                                            <table class="table datatables table-hover table-striped" id="newNotifTbl">
                                                <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="allnewcb" name="allnewcb" class="allnewcb"></th>
                                                    <th></th>
                                                    <th>From</th>
                                                    <th>Company</th>
                                                    <th>Subject</th>
                                                    <th>Received</th>
                                                </tr>
                                                </thead>
                                                @foreach($notification as $item)
                                                @if($item->status == 'N')
                                                <tr>
                                                    <td><input type="checkbox" value="{{ $item->id }}" name="add_to_read[]"></td>
                                                    <td class="mailbox-star" id="{{ $item->id }}"><a href="#"><i class="fa @if($item->is_starred==0) fa-star-o @else fa-star @endif text-yellow"></i></a></td>
                                                    <td class="mailbox-name" onclick="tagAndRedirect({{ $item->id }},'{{ $item->redirect_url }}');"><a href="#">{{ $item->sent_by }}</a></td>
                                                    <td class="mailbox-name" onclick="tagAndRedirect({{ $item->id }},'{{ $item->redirect_url }}');">
                                                        <a href="#">
                                                            @if (isset($item->merchant) && isset($item->partner_id_reference))
                                                                {{ $item->merchant }} - {{ $item->partner_id_reference }}
                                                            @endif
                                                        </a>
                                                    </td>
                                                    <td class="mailbox-subject" onclick="tagAndRedirect({{ $item->id }},'{{ $item->redirect_url }}');"><a href="#"><b>{{ $item->subject }}</b><br> {{ $item->message }}</a>
                                                    </td>
                                                    <td class="mailbox-date">@isset($item->created_at) {{ Carbon\Carbon::parse($item->created_at)->diffForHumans() }} @endisset</td>
                                                </tr>
                                                @endif
                                                @endforeach
                                            </table>
                                        </form>
                                    </div>
                                    <div class="tab-pane @if($active_class=='read') active @endif" id="read">
                                        <form id="frmUpdateUnread" name="frmUpdateUnread"  method="post">
                                            <div class="btn-group" style="padding-bottom:5px;">
                                                <button type="button" class="btn btn-block btn-primary btn-flat" id="btnMarkUnread">Mark as Unread</button>
                                            </div>
                                            <table class="table datatables table-hover table-striped" id="readNotifTbl">
                                                <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="allreadcb" name="allreadcb" class="allreadcb"></th>
                                                    <th></th>
                                                    <th>From</th>
                                                    <th>Company</th>
                                                    <th>Subject</th>
                                                    <th>Received</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($notification as $item)
                                                @if($item->status == 'R')
                                                <tr>
                                                    <td><input type="checkbox" value="{{ $item->id }}" name="add_to_unread[]"></td>
                                                    <td class="mailbox-star" id="{{ $item->id }}"><a href="#"><i class="fa @if($item->is_starred==0) fa-star-o @else fa-star @endif text-yellow"></i></a></td>
                                                    <td class="mailbox-name" onclick="tagAndRedirect({{ $item->id }},'{{ $item->redirect_url }}');"><a href="#">{{ $item->sent_by }}</a></td>
                                                    <td class="mailbox-name" onclick="tagAndRedirect({{ $item->id }},'{{ $item->redirect_url }}');">
                                                        <a href="#">
                                                            @if (isset($item->merchant) && isset($item->partner_id_reference))
                                                                {{ $item->merchant }} - {{ $item->partner_id_reference }}
                                                            @endif
                                                        </a>
                                                    </td>
                                                    <td class="mailbox-subject" onclick="tagAndRedirect({{ $item->id }},'{{ $item->redirect_url }}');"><a href="#"><b>{{ $item->subject }}</b><br> {{ $item->message }}</a>
                                                    </td>
                                                    <td class="mailbox-date">@isset($item->created_at) {{ Carbon\Carbon::parse($item->created_at)->diffForHumans() }} @endisset</td>
                                                </tr>
                                                @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                    <div class="tab-pane @if($active_class=='starred') active @endif" id="starred">
                                        <table class="table datatables table-condense">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>From</th>
                                                <th>Company</th>
                                                <th>Subject</th>
                                                <th>Date Received</th>
                                            </tr>
                                            </thead>
                                            @foreach($notification as $item)
                                            @if($item->is_starred == 1)
                                            <tr>
                                                <td class="mailbox-star" id="{{ $item->id }}"><a href="#"><i class="fa @if($item->is_starred==0) fa-star-o @else fa-star @endif text-yellow"></i></a></td>
                                                <td class="mailbox-name" onclick="tagAndRedirect({{ $item->id }},'{{ $item->redirect_url }}');"><a href="#">{{ $item->sent_by }}</a></td>
                                                <td class="mailbox-name" onclick="tagAndRedirect({{ $item->id }},'{{ $item->redirect_url }}');">
                                                    <a href="#">
                                                        @if (isset($item->merchant) && isset($item->partner_id_reference))
                                                            {{ $item->merchant }} - {{ $item->partner_id_reference }}
                                                        @endif    
                                                    </a>
                                                </td>
                                                <td class="mailbox-subject" onclick="tagAndRedirect({{ $item->id }},'{{ $item->redirect_url }}');"><a href="#"><b>{{ $item->subject }}</b><br> {{ $item->message }}</a>
                                                </td>
                                                <td class="mailbox-date">@isset($item->created_at) {{ Carbon\Carbon::parse($item->created_at)->diffForHumans() }} @endisset</td>
                                            </tr>
                                            @endif
                                            @endforeach
                                        </table>
                                    </div>
                                    <div class="tab-pane @if($active_class=='assignment') active @endif" id="assignment">
                                        <table class="table datatables table-condense">
                                            <thead>
                                            <tr>
                                                <th>Merchant</th>
                                                <th>Task #</th>
                                                <th>Assignment</th>
                                                <th>Due Date</th>
                                            </tr>
                                            </thead>
                                            @foreach($assignment as $item)
                                                <tr>
                                                    <td>{{ $item->company_name }}</td>
                                                    <td class="mailbox-subject" onclick="redirect_url('{{ $item->redirect_url }}');"><a href="#">#{{ $item->task_no }}</a>
                                                    <td class="mailbox-name" onclick="redirect_url('{{ $item->redirect_url }}');"><a href="#">{{ $item->task_name }} <br> {{ $item->sub_task }}</a></td>
                                                    </td>
                                                    <td class="mailbox-date">@isset($item->created_at) {{ Carbon\Carbon::parse($item->created_at)->diffForHumans() }} @endisset</td>
                                                </tr>  
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="box-footer no-padding">
                            <div class="mailbox-controls">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/extras/notification.js" . "?v=" . config("app.version") }}"></script>
@endsection