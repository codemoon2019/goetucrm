@extends('layouts.app')

@section('style')
<style>
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
                My Calendar
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li class="active">My Calendar</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- <div class="col-md-3">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h4 class="box-title">Other Calendars</h4>
                        </div>
                        <div class="box-body">
                            <div id="sync-buttons">
                                <button type="button" id="btnSyncGoogleCal" class="btn btn-info btn-sm" title="Sync to Google Calendar">Sync to <i class="fa fa-google"></i>oogle Calendar</button>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-body">
                            <div id="sync-buttons">
                            @if(session('googleToken') === null)
                                <button type="button" id="btnSyncGoogleCal" class="btn btn-primary btn-sm btn-flat" title="Sync to Google Calendar">Sync to <i class="fa fa-google"></i>oogle Calendar</button>
                            @endif
                            @if(strpos($url, 'vr2') === false)
                            @if(session('outlookToken') === null)
                                <button type="button" id="btnSyncOutlookCal" class="btn btn-primary btn-sm btn-flat" title="Sync to Outlook 365 Calendar">Sync to <i class="fa fa-windows"></i> Outlook Calendar</button>
                            @endif
                            @endif
                            </div>
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-lg-offset-2 col-lg-8">
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
                        <button type="submit" id="updateEventTwo" class="btn btn-primary hide" data-dismiss="modal">Update</button>
                        <button type="submit" id="cancelEvent" class="btn btn-primary hide" data-dismiss="modal">Cancel Activity</button>
                        <button type="submit" id="updateReminderEvent" class="btn btn-primary hide" data-dismiss="modal">Update</button>
                        <button type="submit" id="postEvent" class="btn btn-primary hide" data-dismiss="modal">Post</button>
                        <button type="submit" id="postNewEvent" class="btn btn-primary hide" data-dismiss="modal">Post</button>

                        <!-- ATTENDANCE -->
                        <button type="submit" id="postDecline" class="btn btn-primary hide" data-dismiss="modal">Decline</button>
                        <button type="submit" id="postConfirm" class="btn btn-primary hide" data-dismiss="modal">Confirm</button>
                    </div>
                </div>
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
            </div>
        </section>
        <!-- /.content -->
    </div>

    <input type="hidden" name="calReminders" id="calReminders" value="{{$rm}}">
    <input type="hidden" name="calTimezones" id="calTimezones" value="{{$tz}}">
    <form id="frmCalendarActivity" name="frmCalendarActivity">
    <input type="hidden" id="defTimeZone" name="defTimeZone" value="{{$defTimeZone}}">
    <input type="hidden" id="calOrg" name="calOrg" value="{{$organizer}}">
    <input type="hidden" id="calPartnerID" name="calPartnerID" value="-1">
    <input type="hidden" id="calUID" name="calUID" value="{{$uid}}">
    <input type="hidden" id="calPartner" name="calPartner" value="">
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
    <input type="hidden" id="calDefAct" value="{{$calDefAct}}">
    </form>
@endsection
@section('script')
    <script>
        var reminders = $('#calReminders').val();
        var timezones = $('#calTimezones').val();
    </script>
    <script src="{{ config("app.cdn") . "/js/calendar/calendar.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/calendar/googleCalendar.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/calendar/outlookCalendar.js" . "?v=" . config("app.version") }}"></script>
@endsection