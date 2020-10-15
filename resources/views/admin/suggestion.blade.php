@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Suggestions
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li class="active"> Suggestions</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-2 mb-plus-20">
                    <div class="list-group" id="divMenu">
                        <a href="{{ url('#new') }}" class="new list-group-item @if($active_class=='new') active @endif" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-envelope-o"></i><span class="label label-primary pull-right">{{$new_suggestion_count}}</span> &nbsp;&nbsp; New 
                        </a>
                        <a href="{{ url('#read') }} " class="read list-group-item @if($active_class=='read') active @endif" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-envelope-open-o"></i> &nbsp;&nbsp; Read
                        </a>
                        <a href="{{ url('#starred') }} " class="starred list-group-item @if($active_class=='starred') active @endif" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-star"></i> &nbsp;&nbsp; Starred
                        </a>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Suggestion Box</h3>
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
                                                    <th>Subject</th>
                                                    <th>Received</th>
                                                </tr>
                                                </thead>
                                                @foreach($suggestions as $item)
                                                @if($item->status == 'N')
                                                <tr>
                                                    <td><input type="checkbox" value="{{ $item->id }}" name="add_to_read[]"></td>
                                                    <td class="suggest-star" id="{{ $item->id }}"><a href="#"><i class="fa @if($item->is_starred==0) fa-star-o @else fa-star @endif text-yellow"></i></a></td>
                                                    <td class="mailbox-name">{{ $item->user->first_name }} {{ $item->user->last_name }}</td>
                                                    <td class="mailbox-subject" onclick="showContent('{{ $item->id }}')"><a href="#"><b>{{ $item->title }}</b></a>
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
                                                    <th>Subject</th>
                                                    <th>Received</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($suggestions as $item)
                                                @if($item->status == 'R')
                                                <tr>
                                                    <td><input type="checkbox" value="{{ $item->id }}" name="add_to_unread[]"></td>
                                                    <td class="suggest-star" id="{{ $item->id }}"><a href="#"><i class="fa @if($item->is_starred==0) fa-star-o @else fa-star @endif text-yellow"></i></a></td>
                                                    <td class="mailbox-name">{{ $item->user->first_name }} {{ $item->user->last_name }}</td>
                                                    <td class="mailbox-subject" onclick="showContent('{{ $item->id }}')"><a href="#"><b>{{ $item->title }}</b></a>
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
                                                <th>Subject</th>
                                                <th>Received</th>
                                            </tr>
                                            </thead>
                                            @foreach($suggestions as $item)
                                            @if($item->is_starred == 1)
                                                <tr>
                                                    <td class="suggest-star" id="{{ $item->id }}"><a href="#"><i class="fa @if($item->is_starred==0) fa-star-o @else fa-star @endif text-yellow"></i></a></td>
                                                    <td class="mailbox-name">{{ $item->user->first_name }} {{ $item->user->last_name }}</td>
                                                    <td class="mailbox-subject" onclick="showContent('{{ $item->id }}')"><a href="#"><b>{{ $item->title }}</b></a>
                                                    </td>
                                                    <td class="mailbox-date">@isset($item->created_at) {{ Carbon\Carbon::parse($item->created_at)->diffForHumans() }} @endisset</td>
                                                </tr>
                                            @endif
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




<div id="modalSuggestionPreview" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
            <div class="row">
                <div class="row-header content-header">
                    <h3 class="title">Suggestion Box <i class="fa fa-lightbulb-o"></i></h3>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="suggestionTitle">Title:<span class="required">*</span></label>
                        <input type="text" class="form-control" name="suggestionTitlePreview" id="suggestionTitlePreview" value="" placeholder="Enter Suggestion Title" readonly/>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="suggestionDescription">Suggestion:<span class="required">*</span></label>
                        <textarea class="form-control" rows="3" name="suggestionDescriptionPreview" id="suggestionDescriptionPreview" readonly></textarea>
                    </div>
                </div>
            </div>
      </div>
      <div class="modal-footer">
        
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')

@endsection