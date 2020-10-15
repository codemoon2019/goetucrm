@extends('layouts.app')

@section('content')
    <link rel="stylesheet" type="text/css" href="{{ "/css/tokenize2.css" . "?v=" . config("app.version") }}">
    <link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .small-box,
        .dropdown-toggle,
        .td-toggle-departments,
        .td-toggle-online-departments,
        .span-count,
        #btn-merge-tickets,
        #btn-delete-tickets,
        #btn-assign-to-me,
        #btn-assign-tickets,
        #btn-assign-tickets-go {
            cursor: pointer;
        }

        .table-super-admin .hidden {
            display: none;
        }

        .table-super-admin .tr-company {
            background-color: rgb(64, 87, 110, 0.1) !important;
            color: rgb(0, 0, 0) !important;
            font-weight: bold;
        }

        .table-super-admin .sub-th {
            background-color: rgb(64, 87, 110, 0.8) !important;
            color: rgb(255, 255, 255) !important;
        }

        .table-super-admin .dt-center {
            text-align: center;
        }


        .table-online-user .hidden {
            display: none;
        }

        .table-online-user .tr-company {
            background-color: rgb(64, 87, 110, 0.1) !important;
            color: rgb(0, 0, 0) !important;
            font-weight: bold;
        }

        .table-online-user .sub-th {
            background-color: rgb(64, 87, 110, 0.8) !important;
            color: rgb(255, 255, 255) !important;
        }

        .table-online-user .dt-center {
            text-align: center;
        }

        .ticket-img-xs {
            
            border: 2px solid #ffffff;
            border-radius: 50%;
            box-shadow: 0 0 2.5px #000000;

            height: 20px;
            width: 20px;
        }

        .ticket-img-md {
            
            border: 2px solid #ffffff;
            border-radius: 50%;
            box-shadow: 0 0 2.5px #000000;

            height: 40px;
            width: 40px;
        }
    </style>
    <div class="content-wrapper">
        <div class="chrome-tabs">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs ui-sortable-handle">
                <li class="tab-ticket-list">
                    <a href="#Sample4" data-toggle="tab" aria-expanded="false">Ticket Listing<span class="chrome-tab-close"><i class="fa fa-times">&nbsp;</i></span></a>
                </li>

                <li class="tab-dashboard active">
                    <a href="#Sample2" data-toggle="tab" aria-expanded="false">Dashboard</a>
                </li>
            </ul>
        </div>
        <section class="content container-fluid">
            <div class="tab-content no-padding">
                <div id="Sample2" class="tab-pane tab-dashboard-content active">
                    <div>
                        @php
                            $colorSet1 = ['aqua', 'green', 'yellow', 'red'];
                            $colorSet2 = ['purple', 'maroon', 'navy', 'olive'];
                        @endphp

                        <!--All Tickets-->
                        <div class="row">
                            @if ($userType == 'USER_WITH_SUPER_ADMIN_ACCESS' || $userType == 'USER_WITH_ASSIGN_ACCESS')
                                <div class="col-lg col-xs-6">
                                    <div class="small-box bg-navy" data-filter='A' data-status='A'>
                                    <div class="inner">
                                        <h3>{{ $ticketsCount['ALL'][4] }}</h3>
                                        <p>All Tickets</p>
                                    </div>
                                </div>
                            </div>

                                @php $i = 0; @endphp

                                @foreach ($ticketStatuses as $k => $ticketStatus)
                                    <div class="col-lg col-xs-4">
                                        @if ($ticketStatus->code == App\Models\TicketStatus::TICKET_STATUS_NEW && $ticketsCount['ALL'][$i] > 0)
                                            <div class="small-box bg-blue" data-filter='A' data-status='{{ $ticketStatus->code }}'>
                                        @else
                                            <div class="small-box bg-navy" data-filter='A' data-status='{{ $ticketStatus->code }}'>
                                        @endif
                                                <div class="inner">
                                                    <h3>{{ $ticketsCount['ALL'][$i] }}</h3>
                                                    <p>All {{ $ticketStatus->description }} Tickets</p>
                                                </div>
                                                @if ($ticketStatus->code == App\Models\TicketStatus::TICKET_STATUS_NEW && $ticketsCount['ALL'][$i] > 0)
                                                    <div class="icon">
                                                        <i class="fa fa-ticket"></i>
                                                    </div>
                                                @endif
                                            </div>
                                    </div>

                                    @php 
                                        $i++ ;
                                        if ($i == 4)
                                            break;
                                    @endphp
                                @endforeach
                            @endif
                        </div><!--/All Tickets-->
                        
                        <!--Department Tickets-->
                        <div class="row">
                            <div class="col-lg col-xs-6">
                                <div class="small-box bg-navy" data-filter='D' data-status='A'>
                                    <div class="inner">
                                        <h3>{{ $ticketsCount['DEPARTMENT'][4] }}</h3>
                                        <p>Tickets in my Department&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
                                    </div>
                                </div>
                            </div>

                            @php $i = 0; @endphp

                            @foreach ($ticketStatuses as $k => $ticketStatus)
                                <div class="col-lg col-xs-4">
                                    @if ($ticketStatus->code == App\Models\TicketStatus::TICKET_STATUS_NEW && $ticketsCount['DEPARTMENT'][$i] > 0)
                                        <div class="small-box bg-blue" data-filter='D' data-status='{{ $ticketStatus->code }}'>
                                    @else 
                                        <div class="small-box bg-navy" data-filter='D' data-status='{{ $ticketStatus->code }}'>
                                    @endif
                                            <div class="inner">
                                                <h3>{{ $ticketsCount['DEPARTMENT'][$i] }}</h3>
                                                <p>{{ $ticketStatus->description }} Tickets in my Department</p>
                                            </div>
                                            
                                            @if ($ticketStatus->code == App\Models\TicketStatus::TICKET_STATUS_NEW && $ticketsCount['DEPARTMENT'][$i] > 0)
                                                <div class="icon">
                                                    <i class="fa fa-ticket"></i>
                                                </div>
                                            @endif
                                        </div>
                                </div>

                                @php
                                    $i++;
                                    if ($i == 4)
                                        break;
                                @endphp
                            @endforeach
                        </div><!--/Department Tickets-->

                        <!--My Tickets-->
                        <div class="row">
                            <div class="col-lg col-xs-6">
                                <div class="small-box bg-navy" data-filter='M' data-status='A'>
                                    <div class="inner">
                                        <h3>{{ $ticketsCount['MY'][4] }}</h3>
                                        <p>My Tickets</p>
                                    </div>
                                </div>
                            </div>

                            @php $i = 0; @endphp

                            @foreach ($ticketStatuses as $k => $ticketStatus)
                                <div class="col-lg col-xs-4">
                                    @if ($ticketStatus->code == App\Models\TicketStatus::TICKET_STATUS_NEW && $ticketsCount['MY'][$i] > 0)
                                        <div class="small-box bg-blue" data-filter='M' data-status='{{ $ticketStatus->code }}'>
                                    @else
                                        <div class="small-box bg-navy" data-filter='M' data-status='{{ $ticketStatus->code }}'>
                                    @endif
                                        <div class="inner">
                                            <h3>{{ $ticketsCount['MY'][$i] }}</h3>
                                            <p>My {{ $ticketStatus->description }} Tickets</p>
                                        </div>
                                        
                                        @if ($ticketStatus->code == App\Models\TicketStatus::TICKET_STATUS_NEW && $ticketsCount['MY'][$i] > 0)
                                            <div class="icon">
                                                <i class="fa fa-ticket"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @php 
                                    $i++;
                                    if ($i == 4)
                                        break;
                                @endphp
                            @endforeach
                        </div><!--/My Tickets-->

                        <div class="row">
                            <div class="col-md-12">
                                <br>
                                <br>
                                
                                <h3>
                                    <i class="fa fa-ticket"></i>
                                    <span>&nbsp;Tally of Tickets</span>
                                </h3>
                                
                                <div class="dotted-hr"></div>
                                
                                <br>
                                
                                <table class="table table-striped table-super-admin">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Company</th>
                                            <th>All</th>
                                            <th>New</th>
                                            <th>In Progress</th>
                                            <th>Pending</th>
                                            <th>Solved</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($companies as $company)
                                            <tr class="tr-company">
                                                <td class='td-toggle-departments' data-company='{{ $company->id }}'>&#9654;</td>
                                                <td>{{ $company->partner_company->company_name }}</td>
                                                <td><span class='span-count' data-filter='A'  data-company='{{ $company->id }}'>{{ $company->ticketHeaders()->whereStatus(null)->count() }}</span></td>
                                                <td><span class='span-count' data-filter='AN' data-company='{{ $company->id }}'>{{ $company->ticketHeaders()->whereStatus('N')->count() }}</span></td>
                                                <td><span class='span-count' data-filter='AI' data-company='{{ $company->id }}'>{{ $company->ticketHeaders()->whereStatus('I')->count() }}</span></td>
                                                <td><span class='span-count' data-filter='AP' data-company='{{ $company->id }}'>{{ $company->ticketHeaders()->whereStatus('P')->count() }}</span></td>
                                                <td><span class='span-count' data-filter='AS' data-company='{{ $company->id }}'>{{ $company->ticketHeaders()->whereStatus('S')->count() }}</span></td>
                                            </tr>

                                            <tr class='tr-company-{{ $company->id }} hidden sub-th'>
                                                <th></th>
                                                <th>Department</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>

                                            <tr class='tr-company-{{ $company->id }} hidden'>
                                                <td></td>
                                                <td><i>Unassigned</i></td>
                                                <td><span class='span-count' data-filter='A'  data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus(null)->where('department', -1)->count() }}</span></td>
                                                <td><span class='span-count' data-filter='AN' data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus('N')->where('department', -1)->count() }}</span></td>
                                                <td><span class='span-count' data-filter='AI' data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus('I')->where('department', -1)->count() }}</span></td>
                                                <td><span class='span-count' data-filter='AP' data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus('P')->where('department', -1)->count() }}</span></td>
                                                <td><span class='span-count' data-filter='AS' data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus('S')->where('department', -1)->count() }}</span></td>
                                            </tr>

                                            @foreach ($company->departments as $department)
                                                <tr class='tr-company-{{ $company->id }} hidden'>
                                                    <td></td>
                                                    <td>{{ $department->description }}</td>
                                                    <td><span class='span-count' data-filter='A'  data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $department->ticketHeaders()->whereStatus(null)->count() }}</span></td>
                                                    <td><span class='span-count' data-filter='AN' data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $department->ticketHeaders()->whereStatus('N')->count() }}</span></td>
                                                    <td><span class='span-count' data-filter='AI' data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $department->ticketHeaders()->whereStatus('I')->count() }}</span></td>
                                                    <td><span class='span-count' data-filter='AP' data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $department->ticketHeaders()->whereStatus('P')->count() }}</span></td>
                                                    <td><span class='span-count' data-filter='AS' data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $department->ticketHeaders()->whereStatus('S')->count() }}</span></td>
                                                </tr>

                                                <tr class='tr-company-{{ $company->id }} hidden'>
                                                    <td></td>
                                                    <td><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Unassigned</i></td>
                                                    <td><span class='span-count' data-filter='A'  data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus(null)->where('department', $department->id)->where('assignee', -1)->count() }}</span></td>
                                                    <td><span class='span-count' data-filter='AN' data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus('N')->where('department', $department->id)->where('assignee', -1)->count() }}</span></td>
                                                    <td><span class='span-count' data-filter='AI' data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus('I')->where('department', $department->id)->where('assignee', -1)->count() }}</span></td>
                                                    <td><span class='span-count' data-filter='AP' data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus('P')->where('department', $department->id)->where('assignee', -1)->count() }}</span></td>
                                                    <td><span class='span-count' data-filter='AS' data-company='{{ $company->id }}' data-department='N'>{{ $company->ticketHeaders()->whereStatus('S')->where('department', $department->id)->where('assignee', -1)->count() }}</span></td>
                                                </tr>

                                                @foreach($department->users as $user)
                                                    <tr class='tr-company-{{ $company->id }} hidden'>
                                                        <td></td>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $user->full_name }}</td>
                                                        <td><span class='span-count' data-filter='A'  data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $user->assignedTickets()->whereStatus(null)->count() }}</span></td>
                                                        <td><span class='span-count' data-filter='AN' data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $user->assignedTickets()->whereStatus('N')->count() }}</span></td>
                                                        <td><span class='span-count' data-filter='AI' data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $user->assignedTickets()->whereStatus('I')->count() }}</span></td>
                                                        <td><span class='span-count' data-filter='AP' data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $user->assignedTickets()->whereStatus('P')->count() }}</span></td>
                                                        <td><span class='span-count' data-filter='AS' data-company='{{ $company->id }}' data-department='{{ $department->id }}'>{{ $user->assignedTickets()->whereStatus('S')->count() }}</span></td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                                <br>
                                <br>

                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <h3><i class="fa fa-desktop"></i>&nbsp;Online Users</h3>
                                <div class="dotted-hr"></div>
                                <br>
                                <table class="table table-striped table-online-user">
                                    <thead>
                                        <tr>
                                            <th width="10px"></th>
                                            <th>Company</th>
                                            <th>Online Users</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($onlineUsers as $ol)
                                            <tr class="tr-company">
                                                <td class='td-toggle-online-departments' data-company='{{ $ol->id }}'>&#9654;</td>
                                                <td>{{ $ol->company_name }}</td>
                                                <td class='dt-center'>{{ $ol->totalCount }}</td>
                                            </tr>

                                            <tr class='tr-company-online-{{ $ol->id }} hidden sub-th'>
                                                <th></th>
                                                <th>Department</th>
                                                <th></th>
                                            </tr>

                                            @foreach ($ol->departments as $department)
                                                <tr class='tr-company-online-{{ $ol->id }} hidden'>
                                                    <td></td>
                                                    <td>{{ $department->description }}</td>
                                                    <td class='dt-center'>{{ $department->userCount}}</td>
                                                </tr>
                                                 @foreach ($department->users as $users)
                                                    <tr class='tr-company-online-{{ $ol->id }} hidden'>
                                                        <td></td>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>{{ $users->first_name }} {{ $users->last_name }}</i></td>
                                                        <td></td>
                                                        
                                                    </tr>
                                                 @endforeach

                                            @endforeach

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </div>

                <div id="Sample4" class="tab-pane tab-ticket-list tab-ticket-list-content">
                    <h3><i class="fa fa-ticket"></i> &nbsp; Ticket List</h3>
                    <form id="form-ticket" method="post">
                        @csrf   
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <select id="select-ticket-filter" class="form-control">
                                        <option value="A">All Tickets</option>
                                        @foreach ($ticketStatuses as $ticketStatus)
                                            <option value="A{{ $ticketStatus->code }}">All {{ $ticketStatus->description }} Tickets</option>
                                        @endforeach

                                        <option disabled>────────────────────</option>
                                        <option value="M">My Tickets</option>
                                        @foreach ($ticketStatuses as $ticketStatus)
                                            <option value="M{{ $ticketStatus->code}}">My {{ $ticketStatus->description }} Tickets</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if ($userType == 'USER_WITH_SUPER_ADMIN_ACCESS')
                                <div class="col-md">
                                    <div class="form-group">
                                        <select id="select-ticket-company" class="form-control">
                                            <option value="A">All Companies + Company-Less Tickets  </option>

                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->partner_company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md">
                                <div class="form-group">
                                    <select id="select-ticket-department" class="form-control">
                                        <option class='option-company--1' value="A">All</option>
                                        @if ($userType == 'USER_WITH_SUPER_ADMIN_ACCESS' || $userType == 'USER_WITH_ASSIGN_ACCESS')
                                            <option class='option-company--1' value="N">No Department</option>
                                            <option class='option-company--1' disabled>────────────────────</option>
                                        @endif

                                        @if ($userType == 'USER_WITH_SUPER_ADMIN_ACCESS' || $userType == 'USER_WITH_ASSIGN_ACCESS')
                                            <option class='option-company--1' value="AD">All Departments</option>
                                        @endif

                                        @foreach ($allDepartments as $department)
                                            <option class='option-company-{{ $department->company_id }}' value="{{ $department->id }}">{{ $department->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md">
                                <div class="form-group">
                                    <select id="select-ticket-priority" class="form-control">
                                        <option value="A">All Priorities</option>
                                        @foreach ($ticketPriorities as $ticketPriority)
                                            <option value="{{ $ticketPriority->code }}"> {{ $ticketPriority->description }} Priority </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md div-actions">
                                <div class="btn-group pull-right">
                                    @if (!$isSystemUser)
                                        <button id="btn-assign-to-me-tickets" type="button" class="btn btn-info">Assign to Me</button>
                                    @endif

                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>

                                    <ul class="dropdown-menu" role="menu">
                                        @if ($userType == 'USER_WITH_SUPER_ADMIN_ACCESS' || $userType == 'USER_WITH_ASSIGN_ACCESS' || $userType == 'USER_DEPARTMENT_HEAD')
                                            <li><a class="btn-assign-tickets" href="#">Assign</a></li>
                                        @endif

                                        @if (!$isSystemUser)
                                            <li><a class="btn-assign-tickets" href="#">Transfer</a></li>
                                        @endif

                                        @if ($allowedActions['delete'])
                                            <li><a id="btn-delete-tickets" href="#">Delete</a></li>
                                        @endif
                                        
                                        @if ($allowedActions['merge'])
                                            <li><a id="btn-merge-tickets" href="#">Merge</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div> 

                            <div class="col-md-12">
                                {{-- `merchant` is an user instance --}}
                                <div class="form-group form-group-merchant col-md-6 pl-0" style="margin-top: 10px">
                                <label>Filter by Merchant/Partner</label>
                                <select class="js-example-basic-single form-control" id="select-ticket-requester" data-placeholder="Select Merchant" data-allow-clear="true" style="width:100%">
                                    <option></option>
                                    @foreach ($requesterGroups as $requesters)
                                        @if ($company = $requesters->first()->partnerCompany)
                                            <optgroup label="{{ $company->company_name }}" class="optgroup optgroup-{{ $company->id }}">
                                        @else
                                            <optgroup label="No Company" class="optgroup optgroup--1">
                                        @endif

                                        @foreach ($requesters->sortBy('full_name') as $requester)
                                            @if ($requester->user_type_id == 8)
                                                <option value="{{ $requester->id }}" 
                                                    data-image="{{ $requester->image }}" 
                                                    data-user_type="{{ $requester->partner->partner_type->description }} ({{ $requester->username }})">
                                                    &nbsp;{{ $requester->partner->partnerCompany->company_name }}
                                                </option>
                                            @else
                                                <option value="{{ $requester->id }}" 
                                                    data-image="{{ $requester->image }}" 
                                                    data-user_type="{{ $requester->partner->partner_type->description }} ({{ $requester->username }})">
                                                    &nbsp;{{ $requester->full_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="dotted-hr"></div> <br/>
                                <table id="ticket-list" class="table responsive datatables table-condense p-0">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Subject</th>
                                            <th>Department</th>
                                            <th>Priority</th>
                                            <th>Date Created</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Type</th>
                                            <th>Assignee</th>
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

    <div class="modal fade" id="viewModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group col-lg-12">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="form-group col-lg-12">
                        <p id="attend">
                            <p class="title">Assignees: </p> <br />
                            <div>
                                <div class="visible-panel">
                                    <div class="sliding-panel">
                                        <div class="left-panel">
                                            <select class="form-control group-list" id="select-department" name="department_id">
                                                <option val="-1">Please select department</option>
                                            </select>
                                        </div>
                                        <div class="right-panel">
                                            <select class="form-control" id="select-assignee" name="assignee_id">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-flat btn-default btn-xs pull-right back hide"><i class="fa fa-chevron-left"></i> Back</a>
                            </div>
                        </p>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default " data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn-assign-tickets-go" class="btn btn-primary">Assign</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/jquery-datatables-checkboxes@1.2.11/js/dataTables.checkboxes.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        $(".nav-tabs").on("click", "a", function(e){
                e.preventDefault();
                var tab_id = $(this).attr('href');
                $('.tab-pane').removeClass('active');
                $(tab_id).addClass('active');
            })
            .on("click", "span", function (e) {
                $('.tab-ticket-list').hide();
                $('.tab-ticket-list-content').hide();
                $('.tab-ticket-list-content').removeClass('active')

                $(".tab-dashboard").addClass('active');
                $(".tab-dashboard-content").show();
            });

        $('.tab-dashboard').on('click', function() {
            $('.tab-ticket-list-content').hide();
            $('.tab-dashboard-content').show();
        })

        $('.tab-ticket-list').on('click', function() {
            $('.tab-dashboard-content').hide();
            $('.tab-ticket-list-content').show();
        })

        $('.ticket-list-datatable').dataTable({
            paging: false,
            searching: false,
            "bInfo" : false,
            "order": [[ 2, "desc" ]]
        })

        $('.select2').select2();

        $('.table-online-user .td-toggle-online-departments').on('click', function () {
            let companyId = $(this).data('company')

            $('.tr-company-online-' + companyId).toggleClass('hidden')
            if ($('.tr-company-online-' + companyId).first().hasClass('hidden')) {
                $(this).html('&#9654;')
            } else {
                $(this).html('&#9660;')
            }
        });

        $('.table-online-user').DataTable({
            paging: false,
            searching: false,
            ordering: false,
            bInfo: false,
        });

    </script>

    <script src="{{ config('app.cdn') . '/js/ticket/internal.js' . '?v=' . config('app.version') }}"></script>
@endsection