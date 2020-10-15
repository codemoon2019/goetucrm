@extends('layouts.app')

@section('style')
    @if (!$allowedActions['delete'])
        <style>
            table tr > *:nth-child(1) {
                display: none;
            }
        </style>
    @endif
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="chrome-tabs">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs ui-sortable-handle">
                <li class="active tab-ticket-list">
                    <a href="#Sample4" data-toggle="tab" aria-expanded="false">Ticket Listing</a>
                </li>
            </ul>
        </div>
        <section class="content container-fluid">
            <div class="tab-content no-padding">
                <div id="Sample4" class="tab-pane tab-ticket-list active">
                    <h3><i class="fa fa-ticket"></i> &nbsp; Ticket List</h3>
                    <form id="form-ticket" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select id="select-ticket-filter" class="form-control">
                                        <option value="M">My Tickets</option>
                                        @foreach ($ticketStatuses as $ticketStatus)
                                            <option value="M{{ $ticketStatus->code}}">My {{ $ticketStatus->description }} Tickets</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <select id="select-ticket-priority" class="form-control">
                                        <option value="A">All Priorities</option>
                                        @foreach ($ticketPriorities as $ticketPriority)
                                            <option value="{{ $ticketPriority->code }}"> {{ $ticketPriority->description }} Priority </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="btn-group pull-right">
                                    @if ($allowedActions['delete'])
                                        <button id="btn-delete-tickets" type="button" class="btn btn-info">Delete</button>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="dotted-hr"></div> <br/>
                                <table id="ticket-list" class="table responsive datatables table-condense p-0">
                                    <thead>
                                        <tr>
                                            <th style="color:red;"></th>
                                            <th>#</th>
                                            <th>Subject</th>
                                            <th>Date Created</th>
                                            <th>Status</th>
                                            <th>Type</th>
                                            <th>Created By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/jquery-datatables-checkboxes@1.2.11/js/dataTables.checkboxes.min.js"></script>
    <script>
        $('.reply-type').click(function(){
            $('.reply-type').removeClass('reply-active');
            $(this).addClass('reply-active');
        });
    </script>
    <script src="{{ config("app.cdn") . "/js/ticket/adminTicket.js" . "?v=" . config("app.version") }}"></script>
@endsection