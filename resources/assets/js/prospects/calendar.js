$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        }
    });

    var curr_event;
    var readOnly = 0;
    var users = [];
    var eventCtr = $('#calCurrID').val();
    var date = new Date();
    var clearFields = ['#title', '#start', '#end', '#noteStart', '#noteStatus', 'notebox'];
    var setDefault = [{
            'id': '#strtTime',
            'val': '00:00',
        },
        {
            'id': '#endTime',
            'val': '00:00',
        },
        {
            'id': '#noteEndTime',
            'val': '00:00',
        }
    ];

    // $.getJSON('?action=get_calendar_profiles', null, function(json){
    $.ajax({
        type: 'GET',
        url: '/prospects/details/appointment/getCalendarProfiles',
        data: data = null,
        dataType: 'json',
        success: function (data) {
            var arr_po = data['users'];
            for (var i = 0; i < arr_po.length; i++) {
                users.push({
                    label: arr_po[i]['fname'] + ' ' + arr_po[i]['lname'] + '(' + arr_po[i]['email_address'] + ')',
                    value: arr_po[i]['id'] + ';Tentative'
                });
            }
        }
    });

    window.onload = function () {
        if ($('#calPartnerID').val() == -1) {
            loadCalendarActivities();
        } else {
            loadLeadCalendarActivities($('#calPartnerID').val());
        }
    };


    function entityPickerInput() {
        $("#attendees").entitypicker({
            autocomplete: {
                autoFocus: true,
                source: users,
            }
        });
        $('.entityPickerInput').addClass('form-control');
    }

    function attendeesUpdate(attendees) {
        var name = "";
        attendees = (attendees == null) ? "" : attendees;
        if (attendees != "") {
            var attend = JSON.parse(attendees);
            for (var i = 0; i < attend.length; i++) {
                var display = attend[i].value.split(';');
                if (display[1] == 'Tentative') {
                    var status = '<span class="badge badge-warning">' + display[1] + '</span>';
                } else if (display[1] == 'Confirmed') {
                    var status = '<span class="badge badge-success">' + display[1] + '</span>';
                } else if (display[1] == 'Declined') {
                    var status = '<span class="badge badge-danger">' + display[1] + '</span>';
                }
                name += '<div class="entityContainer">\
                            <div class="entityEntry ui-widget ui-widget-content ui-state-default">\
                                <div class="innerWrapper">\
                                  <span data-entity-id="' + display[2] + '" class="entityDisplay">' + display[2] + '</span>\
                                  <span class="ui-icon ui-icon-close deleteEntity">&nbsp;x</span>\
                                </div>\
                            </div>\
                            <input name="attendees" type="hidden" value="' + attend[i].value + '">\
                            '+status+' \
                        </div>';
            }
        }
        return name;
    }

    function attendeesList(attendees) {
        var name = "";
        var attend = JSON.parse(attendees);
        for (var i = 0; i < attend.length; i++) {
            var display = attend[i].value.split(';');
            if (display[1] == 'Tentative') {
                var status = '<span class="badge badge-warning">' + display[1] + '</span>';
            } else if (display[1] == 'Confirmed') {
                var status = '<span class="badge badge-success">' + display[1] + '</span>';
            } else if (display[1] == 'Declined') {
                var status = '<span class="badge badge-danger">' + display[1] + '</span>';
            }
            // name += '<span ><b>' + display[2] + '</b> - ' + display[1] + '<br>';
            name += '<div class="entityEntry ui-widget ui-widget-content ui-state-default">\
                        <div class="innerWrapper">\
                        <span data-entity-id="' + display[2] + '" class="entityDisplay">' + display[2] + '</span>\
                        </div>\
                    </div>\
                    '+status;
        }
        $('.entityPickerInput').hide();
        return '<div style="overflow-y: scroll; height:80px;">' + name + '</div>';
    }

    function textareaHenshin() {
        // tinymce.init({
        //     selector: 'textarea#agenda',
        //     height: 50,
        //     menubar: false,
        //     plugins: [
        //         'advlist autolink lists link image charmap print preview anchor',
        //         'searchreplace visualblocks code fullscreen',
        //         'insertdatetime media table contextmenu paste code'
        //     ],
        //     toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter \
        //             alignright alignjustify | bullist numlist outdent indent | link image',
        //     readonly : readOnly
        // });
        CKEDITOR.replace('agenda');
    }

    $('#myModal').on('show.bs.modal', function () {
        if (curr_event == undefined || curr_event.type == 0) {
            textareaHenshin();
        }
    });
    // $('#myModal').on('hide.bs.modal',function(){tinyMCE.editors=[]; });
    $('#myModal').on('hide.bs.modal', function () {
        CKEDITOR.instances = [];
    });

    $('#calendar').fullCalendar({
        height: $(window).height() * 0.70,
        header: {
            left: 'prev title next',
            center: '',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultDate: date,
        navLinks: true,
        selectable: true,
        selectHelper: true,
        timeFormat: 'h:mm a',
        select: function (start, end) {
            readOnly = 0;
            now = new Date();
            now.setHours(0, 0, 0, 0);
            if (now >= start) {
                alert("Cannot create an appointment on past dates.");
                return false;
            }

            var view = $('#calendar').fullCalendar('getView');
            if (view.name == 'month') {
                startDate = $('#calendar').fullCalendar('getDate');
                startDate = new Date(startDate);
                VstartM = startDate.getMonth();
                VstartY = startDate.getFullYear();

                startDate = moment(start).format().split('T');
                startDate = startDate[0];
                startDate = new Date(startDate);
                startM = startDate.getMonth();
                startY = startDate.getFullYear();

                if (VstartY > startY) {
                    $('#calendar').fullCalendar('prev');
                    return false;
                }

                if (VstartM > startM && VstartY >= startY) {
                    $('#calendar').fullCalendar('prev');
                    return false;
                }

                if (VstartY < startY) {
                    $('#calendar').fullCalendar('next');
                    return false;
                }


                if (VstartM < startM && VstartY <= startY) {
                    $('#calendar').fullCalendar('next');
                    return false;
                }


            }
            curr_event = {
                'type': 0
            };
            $('#fillForm').html(inputForm(0));
            // $('#timeZone').trigger('change');
            $('.modal-dialog').removeAttr('style');

            entityPickerInput();
            $('.entityPickerInput').addClass('form-control');
            $('#eventType').removeAttr('disabled');
            $('#eventType').val(0)

            clearInput(clearFields);
            inputDefault(setDefault);

            if (moment(start).format().indexOf('T') !== -1 && moment(end).format().indexOf('T') !== -1) {
                startTime = moment(start).format().split('T');
                endTime = moment(end).format().split('T');
                var endDate = (endTime[1] == "" || endTime[1] == undefined) ? startTime[0] : endTime[0];

                startTM = convertMTIME(startTime[1]);
                startTM = startTM.split(' ');
                istart = startTM[0];
                istartAMPM = startTM[1];
                endTM = convertMTIME(endTime[1]);
                endTM = endTM.split(' ');
                iend = endTM[0];
                iendAMPM = endTM[1];

                $('#strtTime').val(istart);
                $('#endTime').val(iend);
                $('#strtAMPM').val(istartAMPM);
                $('#endAMPM').val(iendAMPM);

                $('#start').val(startTime[0]);
                $('#noteStart').val(startTime[0]);
                $('#end').val(endDate);
                $('#noteEnd').val(endDate);
            } else {
                $('#strtTime').val("12:00");
                $('#endTime').val("12:00");
                $('#strtAMPM').val("AM");
                $('#endAMPM').val("AM");

                $('#start').val(moment(start).format());
                $('#noteStart').val(moment(start).format());
                $('#end').val(moment(end).format());
                $('#noteEnd').val(moment(end).format());
            }

            setDatePicker();

            $('#calendar').fullCalendar('unselect');
            $('#updateEvent').addClass('hide');
            $('#addEvent').removeClass('hide');
            $('#postNewEvent').removeClass('hide');
            $('#postEvent').addClass('hide');
            $('#cancelEvent').addClass('hide');
            $('#deleteEvent').addClass('hide');
            $('#postConfirm').addClass('hide');
            $('#postDecline').addClass('hide');
            $('#updateReminderEvent').addClass('hide');
            $('#myModal').modal('show');
        },
        editable: false,
        eventLimit: true,
        eventClick: function (event) {
            editCalEvent(event);
        },
        eventRender: function(event, element) {
            element.find('.fc-content').attr("id", "event-" + event.id);
        }

    });

    function clearInput(inputId) {
        if (!$.isArray(inputId)) {
            $(inputId).val('');
        } else {
            for (var i = 0; i < inputId.length; i++) {
                $(inputId[i]).val('');
            }
        }
    }

    function inputDefault(input) {
        for (var i = 0; i < input.length; i++) {
            $(input[i].id).val(input[i].val);
        }
    }

    function randId() {
        var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        chars = Math.random().toString(36).substr(8);
        return chars;
    }

    $('#postNewEvent').click(function () {
        if (!confirm("This will post the current Activity. Proceed?")) {
            return false;
        }
        return addCalEvent('P');
    });

    $('#addEvent').click(function () {
        return addCalEvent('A');
    });

    $('#postEvent').click(function () {
        if (!confirm("This will post the current Activity. Proceed?")) {
            return false;
        }
        return updateCalEvent('P');
    });

    $('#updateEvent').click(function () {
        return updateCalEvent('A');
    });

    $('#cancelEvent').click(function () {
        if (!confirm("This will cancel the current Activity. Proceed?")) {
            return false;
        }
        return updateCalEvent('C');
    });

    $('#deleteEvent').click(function () {
        if (!confirm("This will delete the current Activity. Proceed?")) {
            return false;
        }
        return updateCalEvent('D');
    });

    $('#postConfirm').click(function () {
        if (!confirm("Confirm your attendance to this event?")) {
            return false;
        }
        return updateCalEvent('CF');
    });

    $('#postDecline').click(function () {
        if (!confirm("Decline your attendance to this event?")) {
            return false;
        }
        return updateCalEvent('DC');
    });

    $('#updateReminderEvent').click(function () {
        return updateCalEvent(curr_event.status, true);
    });

    $('#agendaDay').fullCalendar({
        header: {
            right: 'today prev,next'
        },
        defaultView: 'agendaDay',
    });

    $('#eventType').change(function () {
        var select = $(this).val();
        $('#fillForm').html(inputForm(select));
        // $('#timeZone').trigger('change');
        if (select == 1) {
            $('#postNewEvent').addClass('hide');
            $('#postEvent').addClass('hide');
            $('#updateReminderEvent').addClass('hide');
            $('.modal-dialog').css('width', '30%');
            setDatePicker();
            // tinymce.EditorManager.editors = [];
            CKEDITOR.instances = [];
            $('#addEvent').html('Add');
        } else {
            $('#postNewEvent').removeClass('hide');
            $('#postEvent').addClass('hide');
            $('#updateReminderEvent').addClass('hide');
            $('.modal-dialog').removeAttr('style');
            setDatePicker();
            textareaHenshin();
            entityPickerInput();
            $('#addEvent').html('Save as Draft');
        }
    });

    function convertMTIME(time) {
        if (time == "") {
            return "12:00 AM";
        }
        time = time.split(':');
        var hours = Number(time[0]);
        var minutes = Number(time[1]);

        var timeValue;
        if (hours > 0 && hours <= 12) {
            if (hours < 10) {
                timeValue = "0" + hours;
            } else {
                timeValue = "" + hours;
            }
        } else if (hours > 12) {
            if ((hours - 12) < 10) {
                timeValue = "0" + (hours - 12);
            } else {
                timeValue = "" + (hours - 12);
            }
        } else if (hours == 0) {
            timeValue = "12";
        }

        timeValue += (minutes < 10) ? ":0" + minutes : ":" + minutes;
        timeValue += (hours >= 12) ? " PM" : " AM"; // get AM/PM
        return timeValue;
    }

    function convertAMPM(time) {
        var hours = Number(time.match(/^(\d+)/)[1]);
        var minutes = Number(time.match(/:(\d+)/)[1]);
        var AMPM = time.match(/\s(.*)$/)[1];
        if (AMPM == "PM" && hours < 12) hours = hours + 12;
        if (AMPM == "AM" && hours == 12) hours = hours - 12;
        var sHours = hours.toString();
        var sMinutes = minutes.toString();
        if (hours < 10) sHours = "0" + sHours;
        if (minutes < 10) sMinutes = "0" + sMinutes;
        return sHours + ":" + sMinutes;
    }


    $('#btnNewAppontment').click(function () {
        var start = new Date();
        var end = new Date();
        $('#fillForm').html(inputForm(0));
        $('.modal-dialog').removeAttr('style');

        entityPickerInput();
        $('.entityPickerInput').addClass('form-control');
        $('#eventType').removeAttr('disabled');
        $('#eventType').val(0)

        clearInput(clearFields);
        inputDefault(setDefault);
        $('#strtTime').val("12:00");
        $('#endTime').val("12:00");
        $('#strtAMPM').val("AM");
        $('#endAMPM').val("AM");

        $('#start').val(moment(start).format());
        $('#noteStart').val(moment(start).format());
        $('#end').val(moment(end).format());
        $('#noteEnd').val(moment(end).format());

        setDatePicker();

        $('#calendar').fullCalendar('unselect');
        $('#updateEvent').addClass('hide');
        $('#addEvent').removeClass('hide');
        $('#postNewEvent').removeClass('hide');
        $('#postEvent').addClass('hide');
        $('#cancelEvent').addClass('hide');
        $('#deleteEvent').addClass('hide');
        $('#postConfirm').addClass('hide');
        $('#postDecline').addClass('hide');
        $('#updateReminderEvent').addClass('hide');
        $('#myModal').modal('show');
    });

    function inputForm(select, status = 'A', organizer = "", partner = "") {
        organizer = (organizer == "" || organizer == null) ? $('#calOrg').val() : organizer;
        partner = (partner == "" || partner == null) ? $('#calPartner').val() : partner;
        lead = "";
        if (!(partner == "" || partner == null)) {
            lead = '<label class="form-group">Prospect : ' + partner + '</label><br>';
        }
        var getStart = (select == 1) ? $("#start").val() : $("#noteStart").val();
        var getEnd = (select == 1) ? $("#end").val() : $("#noteEnd").val();
        var heading = "";
        var defTimeZone = $('#defTimeZone').val();

        if (status == 'I') {
            heading = "Activity Invitation";
        }
        if (status == 'C') {
            heading = '<font color="red">Activity Cancelled</font>';
        }
        if (status == 'CF') {
            heading = '<font color="green">Attendance Confirmed</font>';
        }
        if (status == 'DC') {
            heading = '<font color="red">Attendance Declined</font>';
        }

        var reminderStr = reminders;
        var timeZoneStr = timezones;

        var appoint =
            '<div class="col-lg-12">\
                <h3>' + heading + '</h3>\
                <div class="form-group">\
                    <label class="form-group">Subject:<span class="required">*</span></label>\
                    <input type="text" id="title" class="form-control" value=""/>\
                </div>\
                <div class="form-group">\
                    <label class="form-group">Location:<span class="required">*</span></label>\
                    <input type="text" id="location" class="form-control" value=""/>\
                </div>\
                <label class="form-group">Start Date:</label>\
                <div class="row">\
                <div class="form-group col-sm-12 col-lg-5">\
                    <input type="text" id="start" class="form-control dateselect custom-input-10" value="' + getStart + '" style="display:inline"/>\
                </div>\
                <div class="form-group col-sm-12 col-lg-4">\
                    <input type="text" id="strtTime" class="form-control custom-input-7" value="12:00" style="display:inline" onblur="autoTime(\'strtTime\');"/>\
                </div>\
                <div class="form-group col-sm-12 col-lg-3">\
                    <select id="strtAMPM" class="form-control inline-input custom-input-5">\
                    <option value="AM">AM</option>\
                    <option value="PM">PM</option>\
                    </select>\
                </div>\
                </div>\
                <label class="form-group">End Date:</label>\
                <div class="row">\
                <div class="form-group col-sm-12 col-lg-5">\
                <input type="text" id="end" class="form-control dateselect custom-input-10" value="' + getEnd + '" style="display:inline"/>\
                </div>\
                <div class="form-group col-sm-12 col-lg-4">\
                    <input type="text" id="endTime" class="form-control custom-input-7" value="12:00" style="display:inline" onblur="autoTime(\'endTime\');"/>\
                </div>\
                <div class="form-group col-sm-12 col-lg-3">\
                    <select id="endAMPM" class="form-group form-control inline-input custom-input-5">\
                    <option value="AM">AM</option>\
                    <option value="PM">PM</option>\
                    </select>\
                </div>\
                </div>\
                <div class="form-group col-lg-12" style="padding: 0 5px;">\
                    <label class="form-group">Time Zone:</label>\
                    <select id="timeZone" class="form-control">' + timeZoneStr + '\
                    </select>\
                </div>\
                <div class="form-group">\
                    <label class="form-group">Organizer : ' + organizer + '</label><br>' + lead + '\
                </div>\
            </div>\
            <div class="col-lg-12">\
                <div class="form-group">\
                    <label class="form-group">Agenda:</label>\
                    <textarea id="agenda" class="form-control"></textarea>\
                </div>\
                <div class="form-group">\
                    <label class="form-group">Set Reminder:</label>\
                    <select id="reminder" class="form-control">' + reminderStr + '\
                    </select>\
                </div>\
                <div class="clearfix"></div>\
                <div class="form-group" hidden>\
                    <label class="form-group">Frequency</label>\
                    <select id="frequency" class="form-control">\
                        <option value="0">None</option>\
                        <option value="1">Every Day</option>\
                        <option value="2">Every Week</option>\
                        <option value="3">Every Month</option>\
                        <option value="4">Every Year</option>\
                    </select>\
                </div>\
                <input type="hidden" id="eventID" class="form-control" value=""/>\
                <div class="clearfix"></div>\
                <div class="form-group">\
                    <label class="form-group">Attendees:</label>\
                    <div id="attendees" class="picker locationsearch"></div>\
                </div>\
            </div>';

        var note =
            '<div class="col-lg-12">\
                <div class="form-group">\
                    <label class="form-group">Preset:</label>\
                    <input type="text" id="noteStart" class="form-control dateselect" value="' + getStart + '"/>\
                </div>\
                <div class="form-group">\
                    <label>Status:</label>\
                    <select id="noteStatus" class="form-control">\
                        <option value="0">No Status - Vici Dial\'s Status</option>\
                    </select>\
                </div>\
                <label class="form-group">Call back at:</label>\
                <div class="row">\
                <div class="form-group col-sm-12 col-lg-9">\
                    <input type="text" id="noteEndTime" class="form-control custom-input-5" value="12:00" style="display:inline" onblur="autoTime(\'noteEndTime\');"/>\
                </div>\
                <div class="form-group col-sm-12 col-lg-3">\
                    <select id="noteEndAMPM" class="form-control custom-input-5" style="display:inline">\
                    <option value="AM">AM</option>\
                    <option value="PM">PM</option>\
                    </select>\
                </div>\
                </div>\
                <div class="form-group" style="padding: 0 5px;">\
                    <label class="form-group">Time Zone:</label>\
                    <select id="timeZone" class="form-control">' + timeZoneStr + '\
                    </select>\
                </div>\
                <div id="noteContent" class="form-group">\
                    <label class="form-group">Note:</label>\
                    <textarea id="notebox" class="form-control" placeholder="Enter notes here.."></textarea>\
                </div>\
                <input type="hidden" id="noteEnd" class="form-control" value="' + getEnd + '"/>\
                <input type="hidden" id="eventID" class="form-control" value=""/>\
            </div>';

        select = (select == 0) ? appoint : note;

        $('#strtTime').mask('99:99');
        $('#endTime').mask('99:99');
        $('#noteEndTime').mask('99:99');
        $('#start').mask("9999-99-99");
        $('#end').mask("9999-99-99");
        $('#noteStart').mask("9999-99-99");

        return select;
    }

    $(".ui-menu-item").find('a.ui-corner-all').removeClass('ui-corner-all');

    $('#view').click(function () {
        $.ajax({
            type: 'GET',
            url: 'view_json.php',
            dataType: 'json',
            crossDomain: true,
            success: function (data) {
                $.each(data, function (key, arrVal) {

                    if (typeof arrVal === 'object') {
                        $.each(arrVal, function (subKey, subVal) {
                            switch (key) {
                                case "Time":
                                    switch (subKey) {
                                        case "From":
                                            $('#time').find('.title-from').html(subKey + ":&nbsp;");
                                            $('#time').find('.cont-from').html(subVal);
                                            break;
                                        case "To":
                                            $('#time').find('.title-to').html(subKey + ":&nbsp;");
                                            $('#time').find('.cont-to').html(subVal);
                                            break;
                                    }
                                    break;
                                case "Confirmation":
                                    switch (subKey) {
                                        case "Confirmed":
                                            $('#confirm').find('.title-conf').html(subKey + ":&nbsp;");
                                            $('#confirm').find('.cont-conf').html(subVal);
                                            break;
                                        case "Declined":
                                            $('#confirm').find('.title-dec').html(subKey + ":&nbsp;");
                                            $('#confirm').find('.cont-dec').html(subVal);
                                            break;
                                        case "Tentative":
                                            $('#confirm').find('title-tent').html(subKey + ":&nbsp;");
                                            $('#confirm').find('.cont-tent').html(subVal);
                                            break;
                                    }
                                    break;
                                case "Duration":
                                    switch (subKey) {
                                        case "From":
                                            $('#dur').find('.title-from').html(subKey + ":&nbsp;");
                                            $('#dur').find('.cont-from').html(subVal);
                                            break;
                                        case "To":
                                            $('#dur').find('.title-to').html(subKey + ":&nbsp;");
                                            $('#dur').find('.cont-to').html(subVal);
                                            break;
                                    }
                                    break;
                                case "Attendees":
                                    var content = $('#attend').find('.content');
                                    $('#viewModal').on('show.bs.modal', function () {
                                        content.html('');
                                    });
                                    content.html(content.html() + subVal + ", ");
                                    break;
                            }
                        });
                    } else {
                        switch (key) {
                            case "Subject":
                                $('#topic').find('.title').html(key + ":&nbsp;");
                                $('#topic').find('.content').html(arrVal);
                                break;
                            case "Location":
                                $('#loc').find('.title').html(key + ":&nbsp;");
                                $('#loc').find('.content').html(arrVal);
                                break;
                            case "Agenda":
                                $('#viewAgenda').find('.title').html(key + ":&nbsp;");
                                $('#viewAgenda').find('.content').html(arrVal);
                                break;
                            case "Frequency":
                                $('#freq').find('.title').html(key + ":&nbsp;");
                                $('#freq').find('.content').html(arrVal);
                                break;
                            case "Organizer":
                                $('#org').find('.title').html(key + ":&nbsp;");
                                $('#org').find('.content').html(arrVal);
                                break;
                            case "Status":
                                $('#stat').find('.content option[value=1]').prop('selected', true);
                                break;
                        }
                    }
                });
            }
        });
    });

    function appendInnerHtml(id, elem, cls, content) {
        $(id).find(elem).each(function () {
            if ($(this).hasClass(cls)) {
                $(this).html(content);
            }
        });
    }

    loadAppointments();
    function loadAppointments(){
        $('#appointments-table').dataTable().fnDestroy();
        $('#appointments-table').DataTable({
            "lengthMenu": [25, 50, 75, 100 ],
            processing: true,
            serverSide: true,
            aaSorting: [],
            ajax: '/leads/getAppointments/' + $('#calPartnerID').val() ,
            columns: [
                {
                    data: 'type'
                },
                {
                    data: 'title'
                },
                {
                    data: 'location'
                },
                {
                    data: 'start_date'
                },
                {
                    data: 'end_date'
                },

            ]
        });
    }


    function addCalEvent(actStatus) {
        var noteCls = $('#eventType').val();
        // var timeTest =/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
        var timeTest = /^((0?[1-9])|(1[0-2]))(:|\s)([0-5][0-9])$/;
        eventCtr++;
        $('#calNew').val(1);
        if (noteCls == 0) { //appointment
            if (actStatus == 'P' || actStatus == 'A') {
                if ($('#title').val() == "") {
                    alert("Please set the Activity Title");
                    return false;
                }
                if ($('#location').val() == "") {
                    alert("Please set the Activity Location");
                    return false;
                }
            }
            if (!(timeTest.test($('#strtTime').val()))) {
                alert("Please set the Correct Start Time");
                return false;
            }
            if (!(timeTest.test($('#endTime').val()))) {
                alert("Please set the Correct End Time");
                return false;
            }
            if ($('#timeZone').val() == "") {
                alert("Please set the Time Zone selected");
                return false;
            }

            var end = $('#end').val();
            var title = $('#title').val();
            var start = $('#start').val();
            // var endTime = $('#endTime').val();
            // var strtTime = $('#strtTime').val();
            var location = $('#location').val();
            var reminder = $('#reminder').val();
            var timezone = $('#timeZone').val();
            var frequency = $('#frequency').val();
            var confirmation = $('#confirmation').val();
            // var agenda = tinyMCE.editors[$('#agenda').attr('id')].getContent();
            var agenda = CKEDITOR.instances.agenda.getData();
            var attendees = JSON.stringify($(".picker").entitypicker("getEntities"));
            var partner = $('#calPartner').val();
            var partner_id = $('#calPartnerID').val();
            var uid = $('#calUID').val();

            strtTime = convertAMPM($('#strtTime').val() + ' ' + $('#strtAMPM').val());
            endTime = convertAMPM($('#endTime').val() + ' ' + $('#endAMPM').val());

            start = (strtTime == "") ? start : start + " " + strtTime;
            end = (endTime == "") ? end : end + " " + endTime;
            var startDate = new Date(start);
            var endDate = new Date(end);
            var now = new Date();
            now.setHours(0, 0, 0, 0);

            if (now > startDate || now > endDate) {
                alert("Cannot create an appointment on past dates.");
                return false;
            }

            if (endDate <= startDate) {
                alert("Start schedule must not be greater or equal to the End schedule");
                return false;
            }

            var eventData = {
                id: eventCtr.toString(),
                user_id: uid,
                end: end,
                title: title,
                start: start,
                type: noteCls,
                agenda: agenda,
                timez: timezone,
                remind: reminder,
                attend: attendees,
                location: location,
                frequency: frequency,
                confirm: confirmation,
                parent_id: -1,
                status: actStatus,
                partner_id: partner_id,
                partner: partner
            };

            if (actStatus == 'P') {
                eventData.backgroundColor = '#11d027';
            }

            $('#calID').val(eventCtr);
            $('#calTitle').val(title);
            $('#calStart').val(start);
            $('#calEnd').val(end);
            $('#calStartTime').val(strtTime);
            $('#calEndTime').val(endTime);
            $('#calType').val(noteCls);
            $('#calAgenda').val(agenda);
            $('#calTimez').val(timezone);
            $('#calRemind').val(reminder);
            $('#calAttend').val(attendees);
            $('#calLocation').val(location);
            $('#calFrequency').val(frequency);
            $('#calConfirm').val(confirmation);
            $('#calStatus').val(actStatus);
            $('#calCalStatus').val('');
            $('#calParentID').val(-1);

        } else if (noteCls == 1) { //note
            var end = $('#noteStart').val();
            var title = $('#notebox').val();
            var start = $('#noteStart').val();
            var stat = $('#noteStatus').val();
            var endTime = $('#noteEndTime').val();
            var uid = $('#calUID').val();
            var timezone = $('#timeZone').val();

            endTime = convertAMPM($('#noteEndTime').val() + ' ' + $('#noteEndAMPM').val());
            start = end + " " + endTime;
            if (!(timeTest.test($('#noteEndTime').val()))) {
                alert("Please set the Correct Time");
                return false;
            }
            if ($('#timeZone').val() == "") {
                alert("Unable to save no Time Zone selected");
                return false;
            }

            var startdate = new Date(start);
            startdate.setMinutes(startdate.getMinutes() + 30);
            end = moment(startdate).format('YYYY-MM-DD HH:mm');

            var eventData = {
                id: eventCtr.toString(),
                user_id: uid,
                title: title,
                start: start,
                end: end,
                timez: timezone,
                cal_status: stat,
                backgroundColor: '#daab00',
                borderColor: '#dac900',
                type: noteCls,
                parent_id: -1,
                status: actStatus
            };

            $('#calID').val(eventCtr);
            $('#calTitle').val(title);
            $('#calStart').val(start);
            $('#calEnd').val(end);
            $('#calStartTime').val('');
            $('#calEndTime').val(endTime);
            $('#calType').val(noteCls);
            $('#calAgenda').val('');
            $('#calTimez').val(timezone);
            $('#calRemind').val('');
            $('#calAttend').val('');
            $('#calLocation').val('');
            $('#calFrequency').val('');
            $('#calConfirm').val('');
            $('#calStatus').val(actStatus);
            $('#calCalStatus').val(stat);
            $('#calParentID').val(-1);
        }

        clearInput(clearFields);
        inputDefault(setDefault);

        var form = document.getElementById('frmCalendarActivity');
        $.ajax({
            url: '/prospects/details/appointment/saveCalendarActivity', //"?action=save_calendar_activity", // Url to which the request is send
            type: "POST", // Type of request to be send, called as method
            data: new FormData(form), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            dataType: 'json',
            contentType: false, // The content type used when sending data to the server.
            cache: false, // To unable request pages to be cached
            processData: false, // To send DOMDocument or non processed data file it is set to false
            success: function (data) // A function to be called if request succeeds
            {
                eventCtr = data.id;
                alert(data.message);
                eventData.id = eventCtr.toString();
                $('#calendar').fullCalendar('renderEvent', eventData, true);
                loadAppointments();

            }
        });
        return true;
        // $('#agendaDay').fullCalendar('renderEvent', eventData, true);
        //call database insert function here.. if adding is true..
    }

    function updateCalEvent(actStatus, reminderOnly = false) {
        // var timeTest =/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
        var timeTest = /^((0?[1-9])|(1[0-2]))(:|\s)([0-5][0-9])$/;
        var noteCls = $('#eventType').val();
        $('#calNew').val(0);
        if (noteCls == 0) { //appointment
            if ($('#title').val() == "") {
                alert("Unable to save no Activity Title");
                return false;
            }
            if ($('#location').val() == "") {
                alert("Unable to save no Activity Location");
                return false;
            }
            if (!(timeTest.test($('#strtTime').val()))) {
                alert("Unable to save please set the Correct Start Time");
                return false;
            }
            if (!(timeTest.test($('#endTime').val()))) {
                alert("Unable to save please set the Correct End Time");
                return false;
            }
            if ($('#timeZone').val() == "") {
                alert("Unable to save no Time Zone selected");
                return false;
            }

            var id = $('#eventID').val();
            var end = $('#end').val();
            var title = $('#title').val();
            var start = $('#start').val();
            // var endTime = $('#endTime').val();
            // var strtTime = $('#strtTime').val();
            var location = $('#location').val();
            var reminder = $('#reminder').val();
            var timezone = $('#timeZone').val();
            var frequency = $('#frequency').val();
            var confirmation = $('#confirmation').val();
            // var agenda = tinyMCE.editors[$('#agenda').attr('id')].getContent();
            var agenda = CKEDITOR.instances.agenda.getData();
            var attendees = JSON.stringify($(".picker").entitypicker("getEntities"));

            strtTime = convertAMPM($('#strtTime').val() + ' ' + $('#strtAMPM').val());
            endTime = convertAMPM($('#endTime').val() + ' ' + $('#endAMPM').val());

            start = (strtTime == "") ? start : start + " " + strtTime;
            end = (endTime == "") ? end : end + " " + endTime;
            var startDate = new Date(start);
            var endDate = new Date(end);
            var now = new Date();
            now.setHours(0, 0, 0, 0);

            if (now > startDate || now > endDate) {
                alert("Cannot update an appointment on past dates.");
                return false;
            }

            if (endDate <= startDate) {
                alert("Unable to save the Start schedule must not be greater or equal to the End schedule");
                return false;
            }

            if (actStatus == 'A' && actStatus != $('#calStatus').val()) {
                if ($('#calStatus').val()) {
                    actStatus = 'P';
                }
            }

            curr_event.id = id;
            curr_event.end = end;
            curr_event.title = title;
            curr_event.start = start;
            curr_event.agenda = agenda;
            curr_event.timez = timezone;
            curr_event.remind = reminder;
            if (!reminderOnly) {
                if (!(actStatus == 'C' || actStatus == 'DC')) {
                    curr_event.attend = attendees;
                }
            }
            curr_event.location = location;
            curr_event.frequency = frequency;
            curr_event.confirm = confirmation;
            curr_event.status = actStatus;

            if (actStatus == 'P' || actStatus == 'CF') {
                curr_event.backgroundColor = '#11d027';
            }
            if (actStatus == 'C' || actStatus == 'DC') {
                curr_event.backgroundColor = '#db0606';
            }


            $('#calID').val(id);
            $('#calTitle').val(title);
            $('#calStart').val(start);
            $('#calEnd').val(end);
            $('#calStartTime').val(strtTime);
            $('#calEndTime').val(endTime);
            $('#calType').val(noteCls);
            $('#calAgenda').val(agenda);
            $('#calTimez').val(timezone);
            $('#calRemind').val(reminder);
            $('#calAttend').val(attendees);
            $('#calLocation').val(location);
            $('#calFrequency').val(frequency);
            $('#calConfirm').val(confirmation);
            if (actStatus == 'A' && actStatus != $('#calStatus').val()) {
                if (!$('#calStatus').val()) {
                    $('#calStatus').val(actStatus);
                }
            } else {
                $('#calStatus').val(actStatus);
            }
            $('#calCalStatus').val('');
            $('#calParentID').val(curr_event.parent_id);
            $('#calCalStatus').val('');
            $('#calPartnerID').val(curr_event.partner_id);

        } else if (noteCls == 1) { //note
            var id = $('#eventID').val();
            var end = $('#noteStart').val();
            var title = $('#notebox').val();
            var start = $('#noteStart').val();
            var stat = $('#noteStatus').val();
            var endTime = $('#noteEndTime').val();
            var timezone = $('#timeZone').val();

            endTime = convertAMPM($('#noteEndTime').val() + ' ' + $('#noteEndAMPM').val());
            start = end + " " + endTime;
            if (!(timeTest.test($('#noteEndTime').val()))) {
                alert("Unable to save please set the Correct Time");
                return false;
            }
            if ($('#timeZone').val() == "") {
                alert("Unable to save no Time Zone selected");
                return false;
            }

            var startdate = new Date(start);
            startdate.setMinutes(startdate.getMinutes() + 30);
            end = moment(startdate).format('YYYY-MM-DD HH:mm');

            curr_event.id = id;
            curr_event.title = title;
            curr_event.start = start;
            curr_event.end = end;
            curr_event.status = stat;
            curr_event.backgroundColor = '#daab00';
            curr_event.borderColor = '#dac900';
            curr_event.status = actStatus;
            curr_event.timez = timezone;

            $('#calID').val(id);
            $('#calTitle').val(title);
            $('#calStart').val(start);
            $('#calEnd').val(end);
            $('#calStartTime').val('');
            $('#calEndTime').val(endTime);
            $('#calType').val(noteCls);
            $('#calAgenda').val('');
            $('#calTimez').val(timezone);
            $('#calRemind').val('');
            $('#calAttend').val('');
            $('#calLocation').val('');
            $('#calFrequency').val('');
            $('#calConfirm').val('');
            $('#calStatus').val(actStatus);
            $('#calCalStatus').val(stat);
            $('#calParentID').val(curr_event.parent_id);

        }

        var form = document.getElementById('frmCalendarActivity');
        if (!reminderOnly) {
            $.ajax({
                url: '/prospects/details/appointment/saveCalendarActivity', //"?action=save_calendar_activity", // Url to which the request is send
                type: "POST", // Type of request to be send, called as method
                data: new FormData(form), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                dataType: 'json',
                contentType: false, // The content type used when sending data to the server.
                cache: false, // To unable request pages to be cached
                processData: false, // To send DOMDocument or non processed data file it is set to false
                success: function (data) // A function to be called if request succeeds
                {
                    alert(data.message);
                    loadAppointments();
                }
            });
        } else {
            $.ajax({
                url: '/prospects/details/appointment/saveCalendarReminder', //"?action=save_calendar_reminder", // Url to which the request is send
                type: "POST", // Type of request to be send, called as method
                data: new FormData(form), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                dataType: 'json',
                contentType: false, // The content type used when sending data to the server.
                cache: false, // To unable request pages to be cached
                processData: false, // To send DOMDocument or non processed data file it is set to false
                success: function (data) // A function to be called if request succeeds
                {
                    alert(data.message);
                    loadAppointments();
                }
            });
        }

        if (actStatus == 'D') {
            $('#calendar').fullCalendar('removeEvents', id);
        } else {
            $('#calendar').fullCalendar('updateEvent', curr_event);
        }
        return true;
    }

    function loadCalendarActivities() {
        // showLoadingModal("Loading calendar activities. Please wait...");  
        // $.getJSON('calendar?action=get_calendar_activities', null, function(data) {
        $.ajax({
            type: 'GET',
            url: '/prospects/details/appointment/getCalendarActivities',
            data: 'id=' + null,
            dataType: 'json',
            success: function (data) {
                var calendar = data['calendar'];
                for (var i = 0; i < calendar.length; i++) {
                    var id = calendar[i]['id'];
                    var user_id = calendar[i]['user_id'];
                    var end = calendar[i]['end_date'];
                    var title = calendar[i]['title'];
                    var start = calendar[i]['start_date'];
                    var location = calendar[i]['location'];
                    var reminder = calendar[i]['reminder'];
                    var timezone = calendar[i]['time_zone'];
                    var frequency = calendar[i]['frequency'];
                    var confirmation = ''; //not yet implemented
                    var stat = calendar[i]['calendar_status'];
                    var agenda = calendar[i]['agenda'];
                    var attendees = calendar[i]['attendees'];
                    var status = calendar[i]['status'];
                    var parent_id = calendar[i]['parent_id'];
                    var parent_attendees = calendar[i]['parent_attendees'];
                    var organizer = calendar[i]['organizer'];
                    var partner_id = calendar[i]['partner_id'];
                    var partner = calendar[i]['partner'];

                    if (calendar[i]['type'] == "0") { //appointment
                        noteCls = 0;

                        var eventData = {
                            id: id,
                            user_id: user_id,
                            end: end,
                            title: title,
                            start: start,
                            type: noteCls,
                            agenda: agenda,
                            timez: timezone,
                            remind: reminder,
                            attend: attendees,
                            location: location,
                            frequency: frequency,
                            confirm: confirmation,
                            status: status,
                            parent_id: parent_id,
                            parent_attendees: parent_attendees,
                            organizer: organizer,
                            partner_id: partner_id,
                            partner: partner
                        };

                    } else if (calendar[i]['type'] == "1") { //note
                        noteCls = 1;
                        var eventData = {
                            id: id,
                            user_id: user_id,
                            title: title,
                            start: start,
                            end: end,
                            timez: timezone,
                            cal_status: stat,
                            backgroundColor: '#daab00',
                            borderColor: '#dac900',
                            type: noteCls,
                            parent_id: parent_id,
                            status: status
                        };
                    }
                    if (status == 'P' || status == 'CF') {
                        eventData.backgroundColor = '#11d027';
                    }
                    if (status == 'C' || status == 'DC') {
                        eventData.backgroundColor = '#db0606';
                    }
                    if (status == 'I') {
                        eventData.backgroundColor = '#11d4c4';
                    }

                    $('#calendar').fullCalendar('renderEvent', eventData, true);
                    // $('#agendaDay').fullCalendar('renderEvent', eventData, true);

                }
                // closeLoadingModal(); 
                eventNo = parseInt($('#calDefAct').val());
                var event = $("#calendar").fullCalendar('clientEvents', eventNo)[0]; //Customize
                if (event == undefined) {
                    // console.log('invalid event');
                } else {
                    editCalEvent(event);
                }
            }
        });
    }

    function loadLeadCalendarActivities(id) {
        // showLoadingModal("Loading calendar activities. Please wait...");  
        // $.getJSON('?action=get_calendar_activities&id='+id, null, function(data) {
        var data = 'id=' + id;
        $.ajax({
            type: 'GET',
            url: '/prospects/details/appointment/getCalendarActivities',
            data: data,
            dataType: 'json',
            success: function (data) {
                var calendar = data['calendar'];
                for (var i = 0; i < calendar.length; i++) {
                    var id = calendar[i]['id'];
                    var user_id = calendar[i]['user_id'];
                    var end = calendar[i]['end_date'];
                    var title = calendar[i]['title'];
                    var start = calendar[i]['start_date'];
                    var location = calendar[i]['location'];
                    var reminder = calendar[i]['reminder'];
                    var timezone = calendar[i]['time_zone'];
                    var frequency = calendar[i]['frequency'];
                    var confirmation = ''; //not yet implemented
                    var stat = calendar[i]['calendar_status'];
                    var agenda = calendar[i]['agenda'];
                    var attendees = calendar[i]['attendees'];
                    var status = calendar[i]['status'];
                    var parent_id = calendar[i]['parent_id'];
                    var parent_attendees = calendar[i]['parent_attendees'];
                    var organizer = calendar[i]['organizer'];
                    var partner_id = calendar[i]['partner_id'];
                    var partner = calendar[i]['partner'];

                    if (calendar[i]['type'] == "0") { //appointment
                        noteCls = 0;

                        var eventData = {
                            id: id,
                            user_id: user_id,
                            end: end,
                            title: title,
                            start: start,
                            type: noteCls,
                            agenda: agenda,
                            timez: timezone,
                            remind: reminder,
                            attend: attendees,
                            location: location,
                            frequency: frequency,
                            confirm: confirmation,
                            status: status,
                            parent_id: parent_id,
                            parent_attendees: parent_attendees,
                            organizer: organizer,
                            partner_id: partner_id,
                            partner: partner
                        };

                    } else if (calendar[i]['type'] == "1") { //note
                        noteCls = 1;
                        var eventData = {
                            id: id,
                            user_id: user_id,
                            title: title,
                            start: start,
                            end: end,
                            timez: timezone,
                            cal_status: stat,
                            backgroundColor: '#daab00',
                            borderColor: '#dac900',
                            type: noteCls,
                            parent_id: parent_id,
                            status: status
                        };
                    }
                    if (status == 'P' || status == 'CF') {
                        eventData.backgroundColor = '#11d027';
                    }
                    if (status == 'C' || status == 'DC') {
                        eventData.backgroundColor = '#db0606';
                    }
                    if (status == 'I') {
                        eventData.backgroundColor = '#11d4c4';
                    }
                    $('#calendar').fullCalendar('renderEvent', eventData, true);
                    // $('#agendaDay').fullCalendar('renderEvent', eventData, true);

                }
                // closeLoadingModal();     
            }
        });
    }

    function editCalEvent(event) {
        readOnly = 0;
        curr_event = event;
        $('select').attr('disabled', 'disabled');
        var noteCls = $('#eventType').val(event.type);
        $('#fillForm').html(inputForm(event.type, event.status, event.organizer, event.partner));
        // $('#timeZone').trigger('change');

        var start = moment(event.start).format();
        var end = moment(event.end).format();
        start = start.split('T');
        end = end.split('T');

        var defaultVal = '00:00:00';
        var istart = (start.length == 1) ? defaultVal.split(':') : start[1].split(':');
        var iend = (end.length == 1) ? defaultVal.split(':') : end[1].split(':');

        istart = istart[0] + ":" + istart[1];
        iend = iend[0] + ":" + iend[1];

        startTM = convertMTIME(istart);
        startTM = startTM.split(' ');
        istart = startTM[0];
        istartAMPM = startTM[1];
        endTM = convertMTIME(iend);
        endTM = endTM.split(' ');
        iend = endTM[0];
        iendAMPM = endTM[1];

        var startDate = new Date(moment(event.start).format());
        var endDate = new Date(moment(event.end).format());
        var nowDate = new Date();
        var newDateObj = moment(nowDate).add(30, 'm').toDate();

        var select = $('#eventType').val();
        (select == 0) ? $('.modal-dialog').removeAttr('style'):
            $('.modal-dialog').css('width', '30%');

        entityPickerInput();
        $('#eventID').val(event.id);
        if (event.type == 0) {
            $('#end').val(end[0]);
            $('#endTime').val(iend);
            $('#endAMPM').val(iendAMPM);
            $('#start').val(start[0]);
            $('#strtTime').val(istart);
            $('#strtAMPM').val(istartAMPM);
            $('#title').val(event.title);
            $('#agenda').val(event.agenda);
            $('#timeZone').val(event.timez);
            $('#reminder').val(event.remind);
            $('#location').val(event.location);
            $('#frequency').val(event.frequency);
            $('#confirmation').val(event.confirm);
            $('.entityPickerParent').find('div:not([class])').html(attendeesUpdate(event.attend));
        }
        if (event.type == 1) {
            $('#noteStart').val(start[0]);
            // $('#noteStatus').val(event.status);
            $('#noteEndTime').val(istart);
            $('#noteEndAMPM').val(istartAMPM);
            $('#notebox').val(event.title);
            $('#timeZone').val(event.timez);
            document.getElementById('noteStatus').value = event.cal_status;
        }

        $('.entityPickerInput').addClass('form-control');
        setDatePicker();
        if (event.status == 'A') {
            $('#addEvent').addClass('hide');
            $('#updateEvent').removeClass('hide');
            $('#postNewEvent').addClass('hide');
            $('#postEvent').removeClass('hide');
            $('#cancelEvent').addClass('hide');
            $('#deleteEvent').removeClass('hide');
            $('#postConfirm').addClass('hide');
            $('#postDecline').addClass('hide');
            $('#updateReminderEvent').addClass('hide');
        }
        if (event.status == 'P') {
            readOnly = 1;
            $('#addEvent').addClass('hide');
            // $('#updateEvent').addClass('hide');
            $('#updateEvent').removeClass('hide');
            $('#postNewEvent').addClass('hide');
            $('#postEvent').addClass('hide');
            $('#cancelEvent').removeClass('hide');
            $('#deleteEvent').addClass('hide');
            $('#postConfirm').addClass('hide');
            $('#postDecline').addClass('hide');
            // $('#updateReminderEvent').removeClass('hide');
            $('#updateReminderEvent').addClass('hide');
            $('#calStatus').val('P');
            $('.entityPickerInput').css('display','');
            if (newDateObj > startDate) { //cannot edit
                $('#updateEvent').addClass('hide');
                $('#cancelEvent').addClass('hide');
                $('#updateReminderEvent').addClass('hide');
                $('#myModal').find('input,textarea,select').attr("disabled", true);
                $('.entityPickerParent').find('div:not([class])').html(attendeesList(event.attend));
            }
        }
        if (event.status == 'C') {
            readOnly = 1;
            $('#addEvent').addClass('hide');
            $('#updateEvent').addClass('hide');
            $('#postNewEvent').addClass('hide');
            $('#postEvent').addClass('hide');
            $('#cancelEvent').addClass('hide');
            $('#deleteEvent').removeClass('hide');
            $('#postConfirm').addClass('hide');
            $('#postDecline').addClass('hide');
            $('#updateReminderEvent').addClass('hide');
            $('#myModal').find('input,textarea,select').attr("disabled", true);
            $('.entityPickerParent').find('div:not([class])').html(attendeesList(event.attend));
        }
        if (event.status == 'I') {
            readOnly = 1;
            $('#addEvent').addClass('hide');
            $('#updateEvent').addClass('hide');
            $('#postNewEvent').addClass('hide');
            $('#postEvent').addClass('hide');
            $('#cancelEvent').addClass('hide');
            $('#deleteEvent').addClass('hide');
            $('#postConfirm').removeClass('hide');
            $('#postDecline').removeClass('hide');
            $('#updateReminderEvent').removeClass('hide');
            $('#myModal').find('input,textarea').attr("disabled", true);
            $('#strtAMPM').attr("disabled", true);
            $('#endAMPM').attr("disabled", true);
            $('#timeZone').attr("disabled", true);
        }
        if (event.status == 'CF') {
            readOnly = 1;
            $('#addEvent').addClass('hide');
            $('#updateEvent').addClass('hide');
            $('#postNewEvent').addClass('hide');
            $('#postEvent').addClass('hide');
            $('#cancelEvent').addClass('hide');
            $('#deleteEvent').addClass('hide');
            $('#postConfirm').addClass('hide');
            $('#postDecline').removeClass('hide');
            $('#updateReminderEvent').removeClass('hide');
            $('#myModal').find('input,textarea').attr("disabled", true);
            $('#strtAMPM').attr("disabled", true);
            $('#endAMPM').attr("disabled", true);
            $('#timeZone').attr("disabled", true);
        }
        if (event.status == 'DC') {
            readOnly = 1;
            $('#addEvent').addClass('hide');
            $('#updateEvent').addClass('hide');
            $('#postNewEvent').addClass('hide');
            $('#postEvent').addClass('hide');
            $('#cancelEvent').addClass('hide');
            $('#deleteEvent').addClass('hide');
            $('#postConfirm').removeClass('hide');
            $('#postDecline').addClass('hide');
            $('#updateReminderEvent').addClass('hide');
            $('#myModal').find('input,textarea,select').attr("disabled", true);
        }

        if (event.type == 1) {
            $('#postNewEvent').addClass('hide');
            $('#postEvent').addClass('hide');
            $('#cancelEvent').addClass('hide');
        }
        if ($('#calUID').val() != event.user_id) {
            $('#addEvent').addClass('hide');
            $('#updateEvent').addClass('hide');
            $('#postNewEvent').addClass('hide');
            $('#postEvent').addClass('hide');
            $('#cancelEvent').addClass('hide');
            $('#deleteEvent').addClass('hide');
            $('#postConfirm').addClass('hide');
            $('#postDecline').addClass('hide');
            $('#updateReminderEvent').addClass('hide');
            $('#myModal').find('input,textarea,select').attr("disabled", true);
        }

        nowDate.setHours(0, 0, 0, 0);
        if (nowDate > startDate || nowDate > endDate) {
            $('#addEvent').addClass('hide');
            $('#updateEvent').addClass('hide');
            $('#postNewEvent').addClass('hide');
            $('#postEvent').addClass('hide');
            $('#cancelEvent').addClass('hide');
            $('#deleteEvent').addClass('hide');
            $('#postConfirm').addClass('hide');
            $('#postDecline').addClass('hide');
            $('#updateReminderEvent').addClass('hide');
            $('#myModal').find('input,textarea,select').attr("disabled", true);
        }

        $('#myModal').modal('show');
        $('#endAMPM').focus();
    }

});

function autoTime(time) {
    var str = $("#"+time).val();
    if (!/:/.test(str)) {
        str += ':00';
    }
    var newTime = str.replace(/^\d{1}:/, '0$&').replace(/:\d{1}$/, '$&0');
    $("#"+time).val(newTime);
}

function setDatePicker() {
    $('#strtTime').mask("99:99");
    $('#endTime').mask("99:99");
    $('#noteEndTime').mask("99:99");

    $('.dateselect').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });

    $('.dateselect').on("show", function (e) {
        e.preventDefault();
        e.stopPropagation();
    }).on("hide", function (e) {
        e.preventDefault();
        e.stopPropagation();
    });
}

window.autoTime = autoTime;