@extends('layouts.app')

@section('content')
    <div class="hidden">
        <input type="hidden" id="is_filter" value="{{ $isFilter }}"/>
    </div>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ __("model.ticket.list") }}</h1>
            <ol class="breadcrumb">
                <li><a href="#">{{ __("common.dashboard") }}</a></li>
                <li class="active">{{ __("model.ticket.list") }}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <form role="form" action="{{ url("/tickets/filter") }}" method="GET">
                <div class="row">
                {{ csrf_field() }}

                <!-- Quick Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <h3>{{ __("model.ticket.tickets") }}</h3>
                        </div>
                        <!-- <span class="badge badge-primary badge-pill">1</span> -->
                        <div class="form-group">
                            <ul class="list-group">
                                <!-- Unassigned Tickets -->
                                <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-primary ticket-filter-list">
                                    {{ __("model.ticket.unassignedTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_UNASSIGNED_TICKETS }}" id="{{ \App\Models\TicketHeader::FILTER_TICKET_UNASSIGNED_TICKETS }}">
                                    {{ __("model.ticket.unassignedTickets") }}
                                    <!--<span class="badge badge-primary badge-pill">0</span>-->
                                </li>
                                <!-- End Unassigned Tickets -->
                                <!-- My Created Tickets -->
                                <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-primary ticket-filter-list">
                                    {{ __("model.ticket.myCreatedTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_CREATED_TICKETS }}" 
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_CREATED_TICKETS }}">
                                {{ __("model.ticket.myCreatedTickets") }}
                                <!--<span class="badge badge-primary badge-pill">0</span>-->
                                </li>
                                <!-- End My Created Tickets -->

                                <!-- My Tickets -->
                                <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-primary">
                                    {{ __("model.ticket.myTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_UNSOLVED_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_UNSOLVED_TICKETS }}">
                                    {{ __("model.ticket.myUnsolvedTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_PENDING_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_PENDING_TICKETS }}">
                                    {{ __("model.ticket.myPendingTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_NEW_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_NEW_TICKETS }}">
                                    {{ __("model.ticket.myNewTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_CLOSED_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_CLOSED_TICKETS }}">
                                    {{ __("model.ticket.myClosedTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_RESOLVED_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_MY_RESOLVED_TICKETS }}">
                                    {{ __("model.ticket.myResolvedTickets") }}
                                </li>
                                <!-- End My Tickets -->

                                <!-- Group -->
                                <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-primary">
                                    {{ __("model.ticket.group") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_UNSOLVED_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_UNSOLVED_TICKETS }}">
                                    {{ __("model.ticket.groupUnsolvedTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_PENDING_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_PENDING_TICKETS }}">
                                    {{ __("model.ticket.groupPendingTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_NEW_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_NEW_TICKETS }}">
                                    {{ __("model.ticket.groupNewTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_CLOSED_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_CLOSED_TICKETS }}">
                                    {{ __("model.ticket.groupClosedTickets") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_RESOLVED_TICKETS }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_GROUP_RESOLVED_TICKETS }}">
                                    {{ __("model.ticket.groupResolvedTickets") }}
                                </li>
                                <!-- End Group -->

                                <!-- Recently Updated -->
                                <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-primary">
                                    {{ __("model.ticket.recentlyUpdated") }}
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center ticket-list-click"
                                    filter-type="{{ \App\Models\TicketHeader::FILTER_TICKET_RECENTLY_UPDATED }}"
                                    id="{{ \App\Models\TicketHeader::FILTER_TICKET_RECENTLY_UPDATED }}">
                                    {{ __("model.ticket.recentlyUpdated") }}
                                </li>
                                <!-- End recently updated-->
                            </ul>
                        </div>
                    </div>
                    <!-- End quick filter -->

                    <!-- List view -->
                    <div class="col-md-9">
                        <div class="form-group" id="ticket-buttons">
                            {{--<a href="#" class="btn btn-primary">{{ __("model.ticket.assign") }}</a>--}}
                            <a href="" class="btn btn-primary" id="btnClose"><i
                                        class="fa fa-times"></i> {{ __("model.ticket.close") }}
                            </a>
                            <a href="" id="btnMerge" class="btn btn-primary"><i
                                        class="fa fa-file"></i> {{ __("model.ticket.merge") }}
                            </a>
                            <a href="" id="btnDelete" class="btn btn-primary"><i
                                        class="fa fa-trash"></i> {{ __("model.ticket.delete") }}
                            </a>
                            <a href="{{ url("tickets/create") }}"
                               class="btn btn-primary pull-right">{{ __("model.ticket.createNewTicket") }}</a>
                            <a href="" id="btnSearch" class="btn btn-primary adv-search-btn">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                        <table class="table datatables responsive table-striped ticket-list">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="check_quick_pick"></th>
                                <th>{{ __("model.ticket.numberShort") }}</th>
                                <th>{{ __("model.ticket.type") }}</th>
                                <th>{{ __("model.ticket.title") }}</th>
                                <th>{{ __("model.ticket.from") }}</th>
                                <th>{{ __("model.ticket.partner") }}</th>
                                <th>{{ __("model.ticket.product") }}</th>
                                <th>{{ __("model.ticket.department") }}</th>
                                <th>{{ __("model.ticket.assignedTo") }}</th>
                                <th>{{ __("model.ticket.status") }}</th>
                                <th>{{ __("model.ticket.priority") }}</th>
                                <th>{{ __("common.actions") }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Generate table information -->
                            </tbody>
                        </table>
                    </div>

                    <!-- End list view-->
                </div>
            </form>
        </section>
    </div>

    <!-- Advance Search -->

    <div class="adv-search-overlay">
        <div class="adv-search">
            <div class="adv-header">
                <h4>
                    {{ __("common.advanceSearch") }} <br/>
                    <small>{{ __("model.ticket.filterTickets") }}</small>
                </h4>
                <a href="#" class="adv-close"><i class="fa fa-times-circle-o fa-2x"></i></a>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __("model.ticket.type") }}</label>
                    <select class="form-control" id="ticket_filter_type" name="ticket_filter_type">
                        <option value="" selected>{{ __("common.selectOne") }}</option>
                        @foreach(\App\Models\TicketType::where("status","=", \App\Models\TicketType::TICKET_TYPE_STATUS_ACTIVE)->get() as $ticket)
                            <option value="{{ $ticket->code }}">{{ $ticket->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __("model.ticket.product") }}</label>
                    <select class="form-control" id="ticket_filter_product" name="ticket_filter_product">
                        <option value="" selected>{{ __("common.selectOne") }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __("model.ticket.department") }}</label>
                    <select class="form-control" id="ticket_filter_department" name="ticket_filter_department">
                        <option value=" " selected>{{ __("common.selectOne") }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __("model.ticket.users") }}</label>
                    <select class="form-control" id="ticket_filter_users" name="ticket_filter_users">
                        <option value=" " selected>{{ __("common.selectOne") }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __("model.ticket.partner") }}</label>
                    <select class="form-control" id="ticket_filter_partner" name="ticket_filter_partner">
                        <option value="" selected>{{ __("common.selectOne") }}</option>
                        @foreach(\App\Models\PartnerType::where("status","=", \App\Models\PartnerType::PARTNER_TYPE_STATUS_ACTIVE)->get() as $partnerType)
                            <option value="{{ $partnerType->id }}">{{ $partnerType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __("model.ticket.partnerUser") }}</label>
                    <select class="form-control" id="ticket_filter_partner_user"
                            name="ticket_filter_partner_user">
                        <option value="" selected>{{ __("common.selectOne") }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __("model.ticket.created") }}</label>
                    <select class="form-control" id="ticket_filter_created" name="ticket_filter_created">
                        <option value="" selected>{{ __("common.selectOne") }}</option>
                        @foreach(\App\Models\TicketFilterCreation::get() as $ticketFilterCreation)
                            <option value="{{ $ticketFilterCreation->new_remarks }}">{{ $ticketFilterCreation->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="from-group">
                    <label>{{ __("model.ticket.status") }}</label>
                    <select class="form-control" id="ticket_filter_status" name="ticket_filter_status">
                        @foreach(\App\Models\TicketStatus::get() as $ticketStatus)
                            <option value="{{ $ticketStatus->code }}"
                                    {{ $ticketStatus->code == \App\Models\TicketHeader::TICKET_STATUS_OPEN ? "selected" : "" }}
                            >
                                {{ $ticketStatus->description }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __("model.ticket.dueBy") }}</label>
                    <div class="input-group">
                        <input type="checkbox" name="filter_ticket_due_by[]" id="ticket_due_by_overdue"
                               value="{{ \App\Models\TicketHeader::TICKET_DUE_BY_OVERDUE }}">
                        <label>{{ ucfirst(\App\Models\TicketHeader::TICKET_DUE_BY_OVERDUE) }}</label>
                    </div>
                    <div class="input-group">
                        <input type="checkbox" name="filter_ticket_due_by[]" id="ticket_due_by_today"
                               value="{{ \App\Models\TicketHeader::TICKET_DUE_BY_TODAY }}">
                        <label>{{ ucfirst(\App\Models\TicketHeader::TICKET_DUE_BY_TODAY) }}</label>
                    </div>
                    <div class="input-group">
                        <input type="checkbox" name="filter_ticket_due_by[]" id="ticket_due_by_tomorrow"
                               value="{{ \App\Models\TicketHeader::TICKET_DUE_BY_TOMORROW }}">
                        <label>{{ ucfirst(\App\Models\TicketHeader::TICKET_DUE_BY_TOMORROW) }}</label>
                    </div>
                    <div class="input-group">
                        <input type="checkbox" name="filter_ticket_due_by[]" id="ticket_due_by_next_8_hours"
                               value="{{ \App\Models\TicketHeader::TICKET_DUE_BY_NEXT_EIGHT_HOURS  }}">
                        <label>{{ ucfirst(\App\Models\TicketHeader::TICKET_DUE_BY_NEXT_EIGHT_HOURS) }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <input type="button" id="filter_tickets" class="btn btn-primary"
                           value="{{ __("model.ticket.filterTickets") }}"/>
                </div>
            </div>
        </div>
    </div>

    <!-- End Advance Search -->
    {{--rgba(195, 224, 239, 0.9)--}}
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/ticket/list.js" . "?v=" . config("app.version") }}"></script>
@endsection