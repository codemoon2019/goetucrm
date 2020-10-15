@extends('layouts.app')

@section('style')
<style>
    #calendar, #agendaDay {
        margin-bottom: 20px;
    }

    #calendar h2,
    #agendaDay h2{
        font-size: 20px;
        margin-top: 5px !important;
    }
    
    #calendar button[type=button],
    #agendaDay button[type=button]{
        border: none;
        background: #4cacff;
        box-shadow: none;
        outline: none;
    }

    #calendar button[type=button]:hover,
    #agendaDay button[type=button]:hover{
        background: #005dad;
    }

    .fc-state-active{
        background: #005dad !important;
    }

    .fc-state-default{
        text-shadow: none;
        color: #fff;
    }
    .fc-day-header{
        color: #0056a0;
        text-transform: uppercase;
        padding: 10px 0 !important;
    }
    
    .fc-state-highlight{
        background: #e1f1ff !important;
    }

    #agendaDay .fc-scroller{
        height: 450px !important;
    }
    
    .close{
        margin-bottom: 10px;
    }
    
    .entityContainer{
        display: inline;
        margin-right: 5px;
    }
    .entityEntry{
        font-size: 12px;
        border-radius: 100px;
        display: inline-block;
        padding: 3px 10px;
        margin-bottom: 5px;
    }
    
    .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
        border: 1px solid #2e6da4;
        background: #337ab7;
        font-weight: bold;
        color: #f8fafb;
    }

    .ui-state-default .ui-icon {
        float: right;
    }
    
    .ui-icon-close {
        background-position: -78px -128px;
    }

    .ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header .ui-state-focus {
        background: #cde1f1;
        font-weight: bold;
        font-size: 13px;
    }

    .ui-autocomplete{
        overflow-y: auto;
        max-height: 100px;
        max-width: 400px;
        font-size: 13px;
        text-transform: capitalize;
        position: absolute;
        z-index: 9999 !important;
        cursor: default;
        padding: 0;
        margin-top: -10px;
        list-style: none;
        background-color: #ffffff;
        border: 1px solid #ccc;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
           -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    .ui-menu .ui-menu-item:hover, .ui-menu .ui-menu-item:focus, .ui-menu .ui-menu-item:active {
        background: #cde1f1;
        border: none;
        color:#000;
        border-radius:0;
        font-weight: normal;
    }
    .title{
        font-weight: 600;
        font-size: 16px;
        color: #424242;
    }
    .sub-title{
        font-size: 15px;
        font-weight: 600;
        font-style: italic;
        color: #727272;
        margin-top: 8px;
        display: inline-block;
    }
    .fc-day-header{
        text-transform: uppercase;
        padding: 10px 0 !important;
    }
    .fc-more-cell div {
        background-color: #df9436;
        color: #ffffff;
        border-radius: 3px;
    }
    .fc-more {
        display: block;
        height: 100%;
    }
    .fc-toolbar h2 {
        width: 200px;
    }
    #viewModal .modal-title{
        font-weight: 600;
        margin: 0 auto 20px;
        display: inline-block;
        text-transform: capitalize;
    }
    @media (min-width: 768px){
        .modal-dialog {
            width: 60%;
        }
        #viewModal .modal-dialog{
            width: 35% !important;
        }
    }
</style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Lead
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li><a href="/leads">Leads</a></li>
                <li class="active">Appointments</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                @if(!isset($partner_info) || count($partner_info) <= 0)
                <h3>Lead Company</h3>
                <a href="{{ url("leads/") }}" class="btn btn-default pull-right mt-minus-40">< Back to Leads</a><br>
                <button  class="btn btn-success pull-right" id="btnView" onclick="changeView()">Calendar View</button>
                <div class="crearfix"></div>
                <small class="small-details">
                    Lead ID <br/>
                    Business Address <br/>
                    Contact Phone <br/>
                    Email Address
                </small>
                @else
                <h3>{{ $partner_info[0]->company_name }}</h3>
                <a href="{{ url("leads/") }}" class="btn btn-default pull-right mt-minus-40">< Back to Leads</a><br>
                <button  class="btn btn-success pull-right" id="btnView" onclick="changeView()">Calendar View</button>
                <div class="crearfix"></div>
                <small class="small-details">
                    {{ $partner_info[0]->partner_id_reference }} <br/>
                    {{ $partner_info[0]->address1 }}, {{ $partner_info[0]->city}} {{ $partner_info[0]->state }}, {{ $partner_info[0]->zip }}, {{ $partner_info[0]->country_name }} <br/>
                    {{ $calling_code }} {{ $partner_info[0]->phone1 }} <br/>
                    {{ $partner_info[0]->email }}
                </small>
                @endif
            </div>
            <div class="nav-tabs-custom">
                <ul class="tabs-rectangular">
                    @if($isInternal)
                    <li><a href="{{ url('leads/details/summary/'.$partner_id) }}">Summary</a></li>
                    @endif
                    <li><a href="{{ url('leads/details/profile/'.$partner_id) }}">Profile</a></li>
                    <li><a href="{{ url('leads/details/contact/'.$partner_id) }}">Contact</a></li>
                    <li><a href="{{ url('leads/details/interested/'.$partner_id) }}">Interested Products</a></li>
                    <!-- <li><a href="{{ url('leads/details/applications/'.$partner_id) }}">Applications</a></li> -->
                    <li class="active"><a href="{{ url('leads/details/appointment/'.$partner_id) }}">Appointment</a></li>
                </ul>
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs"></ul>
                <div class="tab-content no-padding">
                @if($isInternal)
                    <div class="tab-pane active"  id="calendarView" style="display: none;">
                        <div id="calendar"></div>
                        <div class="col-lg-offset-2 col-lg-8">
                            
                    <div class="modal fade" id="viewModal" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="form-group col-lg-12">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <h4 class="modal-title">Appointment</h4>
                                        <p id="topic"><span class="title">Subject: </span><span class="content"></span></p>
                                        <p id="loc"><span class="title">Location: </span><span class="content"></span></p>
                                        <p id="time">
                                            <span class="title">Time: </span><span class="content"></span>
                                            <span class="sub-title title-from">From: </span><span class="content cont-from"></span>
                                            <span class="sub-title title-to">To: </span><span class="content cont-to"></span>
                                        </p>
                                        <p id="attend">
                                            <span class="title">Attendees: </span><span class="content"></span>
                                        </p>
                                        <p id="confirm">
                                            <span class="sub-title title-conf">Confirmed: </span><span class="content cont-conf"></span>
                                            <span class="sub-title title-dec">Declined: </span><span class="content cont-dec"></span>
                                            <span class="sub-title title-tent">Tentative: </span><span class="content cont-tent"></span>
                                        </p>

                                        <p id="viewAgenda"><span class="title">Agenda: </span><span class="content"></span></p>
                                        <p id="dur">
                                            <span class="title">Duration: </span><span class="content"></span>
                                            <span class="sub-title title-from">From: </span><span class="content cont-from"></span>
                                            <span class="sub-title title-to">To: </span><span class="content cont-to"></span>
                                        </p>
                                        <p id="freq"><span class="title">Frequency: </span><span class="content"></span></p>
                                        <p id="org"><span class="title">Organizer: </span><span class="content"></span></p>
                                        <p id="stat">
                                            <span class="title">Status: </span>
                                            <span class="content">
                                                <select>
                                                    <option value="0">Pending</option>
                                                    <option value="1">Ongoing</option>
                                                    <option value="2">Complete</option>
                                                </select>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
                                    <button type="submit" id="saveView" class="btn btn-primary" data-dismiss="modal">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                </div>
            </div>
            <div class="tab-pane active"  id="listView">
                <div class="row">
                    <div class="row-header">
                        <h3 class="title">Appointment List</h3>
                        <div class="mini-drp-input pull-right mt-minus-40">
                            <button type="button" id="btnNewAppontment" class="btn btn-primary">New</button>
                        </div>

                    </div>
                    <table class="table datatables table-striped" id="appointments-table">
                        <thead>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Start</th>
                            <th>End</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>


            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                                <div class="form-group col-lg-12">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="form-group col-lg-12">
                                        <h4 class="modal-title">Select a Task</h4>
                                        <select id="eventType" class="form-control">
                                            <option value="0">Set an Appointment</option>
                                            <option value="1">Add a Note</option>
                                        </select>
                                        <div id="fillForm"></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        <div class="modal-footer">
                            <!-- <a href="#viewModal" data-toggle="modal" data-dismiss="modal" id="view">Previous</a> -->
                            <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
                            <button type="submit" id="deleteEvent" class="btn btn-primary hide" data-dismiss="modal">Delete</button>
                            <button type="submit" id="addEvent" class="btn btn-primary hide" data-dismiss="modal">Save As Draft</button>
                            <button type="submit" id="updateEvent" class="btn btn-primary hide" data-dismiss="modal">Update</button>
                            <button type="submit" id="cancelEvent" class="btn btn-primary hide" data-dismiss="modal">Cancel Activity</button>
                            <button type="submit" id="updateReminderEvent" class="btn btn-primary hide" data-dismiss="modal">Update</button>
                            <button type="submit" id="postEvent" class="btn btn-primary hide" data-dismiss="modal">Post</button>
                            <button type="submit" id="postNewEvent" class="btn btn-primary hide" data-dismiss="modal">Post</button>

                            <!-- ATTENDANCE -->
                            <button type="submit" id="postDecline" class="btn btn-primary hide" data-dismiss="modal">Decline</button>
                            <button type="submit" id="postConfirm" class="btn btn-primary hide" data-dismiss="modal">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    </section>
        <!-- /.content -->
    </div>
    <input type="hidden" name="calReminders" id="calReminders" value="{{$rm}}">
    <input type="hidden" name="calTimezones" id="calTimezones" value="{{$tz}}">
    <form id="frmCalendarActivity" name="frmCalendarActivity">
    <input type="hidden" id="defTimeZone" name="defTimeZone" value="{{$defTimeZone}}">
    <input type="hidden" id="calOrg" name="calOrg" value="{{$organizer}}">
    <input type="hidden" id="calPartnerID" name="calPartnerID" value="{{$partner_id}}">
    <input type="hidden" id="calUID" name="calUID" value="{{$uid}}">
    <input type="hidden" id="calPartner" name="calPartner" value="{{$partner_info[0]->first_name}} {{$partner_info[0]->last_name}}">
    <input type="hidden" id="calCurrID" name="calCurrID" value="{{$ctrID}}">
    <input type="hidden" id="calNew" name="calNew">
    <input type="hidden" id="calID" name="calID">
    <input type="hidden" id="calParentID" name="calParentID">
    <input type="hidden" id="calTitle" name="calTitle">
    <input type="hidden" id="calStart" name="calStart">
    <input type="hidden" id="calEnd" name="calEnd">
    <input type="hidden" id="calStartTime" name="calStartTime">
    <input type="hidden" id="calEndTime" name="calEndTime">
    <input type="hidden" id="calType" name="calType">
    <input type="hidden" id="calAgenda" name="calAgenda">
    <input type="hidden" id="calTimez" name="calTimez">
    <input type="hidden" id="calRemind" name="calRemind">
    <input type="hidden" id="calAttend" name="calAttend">
    <input type="hidden" id="calLocation" name="calLocation">
    <input type="hidden" id="calFrequency" name="calFrequency">
    <input type="hidden" id="calConfirm" name="calConfirm">
    <input type="hidden" id="calStatus" name="calStatus">
    <input type="hidden" id="calCalStatus" name="calCalStatus">
    <input type="hidden" id="calDefAct" value="-1">
    </form>
@endsection
@section('script')
    <script>
        var reminders = $('#calReminders').val();
        var timezones = $('#calTimezones').val();

        function editAppointment(eventId,start){
            
            $('#calendar').fullCalendar('gotoDate', moment(start).format());
            $('#calendar').fullCalendar('changeView','month');
            $('#event-' + eventId).trigger('click');

        }

        function changeView(){
            if ($("#btnView").html() == "Calendar View"){
                $("#calendarView").show();
                $("#listView").hide();
                $("#btnView").html("List View");
            }else{
                $("#calendarView").hide();
                $("#listView").show();
                $("#btnView").html("Calendar View");
            }
        }

    </script>
    <script src="{{ config("app.cdn") . "/js/leads/calendar.js" . "?v=" . config("app.version") }}"></script>
@endsection
