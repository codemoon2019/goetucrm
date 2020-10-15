<?php

namespace App\Http\Controllers\Calendar;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_EventOrganizer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Mail;

use App\Http\Controllers\Controller;

use App\Models\CalendarActivity;
use App\Models\CalendarReminder;
use App\Models\Notification;
use App\Models\SyncCalendar;
use App\Models\TimeZone;
use App\Models\User;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request) {
        if(isset($request->activityID)) {
            $calDefAct = $request->activityID;
        } else {
            $calDefAct = -1;
        }

        if ($request->has('code')) {
            $client = $this->getGClient($request->code);
            $this->getGoogleCalEvents($client);
        }

        if (session('outlookToken')) {
            $this->getOutlookCalendarEvents();
        }

    	$ctrID = CalendarActivity::get_max_calendar_activity_id();
        $organizer = auth()->user()->first_name.' '.auth()->user()->last_name;
        $uid = auth()->user()->id;

        $defTimeZone = config('app.timezone'); //?

        $timezones = TimeZone::orderByRaw("id = 8 desc")->get();
        $tz = '';
        foreach ($timezones as $t) {
            $tz = $tz. '<option value="'.$t->name.'">'.$t->name.'</option>';
        }

        $reminders = CalendarReminder::where('status','A')->get();
        $rm = '';
        foreach ($reminders as $r) {
            $rm = $rm. '<option value="'.$r->id.'">'.$r->description.'</option>';
        }

        $url = url()->current();

        return view("calendar.list", compact('ctrID','organizer','uid','defTimeZone','tz','rm','calDefAct','url'));
    }
    public function getCalendarProfiles() {
        $create_by = "";
        $records = DB::raw("select u.id,u.first_name as fname,u.last_name as lname,u.username,u.email_address,ifnull(user_type_id,'') user_type_id
                    ,is_verified_email,is_verified_mobile,u.country_code,ifnull(us.description,'') as status_text 
                    ,ifnull(pc.company_name,'') as company_name           
                  from users u
                  left join user_statuses us on us.code = u.status
                  left join partner_companies pc on pc.partner_id=u.reference_id
                  where status<>'D'");
        if ($create_by != ""){
            $records .= DB::raw("and create_by='".$create_by."'");
        }
               
        $users = DB::select($records);
        $results = array();
        foreach($users as $r)
        {   
            if($r->user_type_id !== ""){
                $cmd = DB::raw("SELECT * FROM user_types WHERE status='A' and id in (".$r->user_type_id.")");
                $customs = DB::select($cmd);
                $r->user_types = $customs;
            } else {
                $r->user_types = array();   
            }
            $results[] = $r;
        }
        return response()->json(array('success' => true, 'users' => $results), 200);
    }
    public function getCalendarActivities() {
        $tasks = DB::select(DB::raw("select ca.*,ca2.attendees as parent_attendees,CONCAT(u.first_name,' ',u.last_name) as organizer,
				CONCAT(pc.first_name,' ',pc.last_name) as partner
				from calendar_activities ca 
				left join calendar_activities ca2 on ca.parent_id = ca2.id
                left join users u on u.username = ca.create_by
                left join partners p on p.id = ca.partner_id
                left join partner_contacts pc on pc.partner_id = ca.partner_id
                where ca.user_id = ".auth()->user()->id." and ca.status <> 'D'"));
				// left join users u on u.id = ca2.user_id
        return response()->json(array('success' => true, 'calendar' => $tasks), 200);
    }
    public function saveCalendarActivity() {
        DB::transaction(function(){
            $startDate = date('m/d/Y h:i:s A', strtotime(Input::get('calStart')));
            $endDate = date('m/d/Y h:i:s A', strtotime(Input::get('calEnd')));
            // Create calendar activity.
            if (Input::get('calNew') == 1) {
                $calendarActivity = new CalendarActivity;
                $calendarActivity->user_id = auth()->user()->id;
                $calendarActivity->type = Input::get('calType');
                $calendarActivity->title = Input::get('calTitle');
                $calendarActivity->start_date = Input::get('calStart');
                $calendarActivity->end_date = Input::get('calEnd');
                $calendarActivity->start_time = Input::get('calStartTime');
                $calendarActivity->end_time = Input::get('calEndTime');
                $calendarActivity->agenda = Input::get('calAgenda');
                $calendarActivity->time_zone = Input::get('calTimez');
                $calendarActivity->reminder = Input::get('calRemind');
                $calendarActivity->attendees = Input::get('calAttend');
                $calendarActivity->location = Input::get('calLocation');
                $calendarActivity->frequency = Input::get('calFrequency');
                $calendarActivity->calendar_status = Input::get('calCalStatus');
                $calendarActivity->create_by = auth()->user()->username;
                $calendarActivity->status = Input::get('calStatus');
                $calendarActivity->partner_id = Input::get('calPartnerID');
                $calendarActivity->parent_id = -1;

                if (!$calendarActivity->save()) {
                    return response()->json(array(
                        'success' => false,
                        'message' => 'Unable to save calendar activity.'
                    ), 200);
                }
                $newCalID = $calendarActivity->id;

                // Create activity in google calendar.
                if (session('googleToken') !== null) {
                    $this->insertToGoogleCalendarEvent($newCalID);
                }

                // Create activity in outlook calendar.
                if (session('outlookToken') !== null) {
                    $this->insertToOutlookCalendarEvent($newCalID);
                }

            }

            // Input activity attendees.
            if (Input::get('calStatus') == 'P') {
                $message ="Title: ".Input::get('calTitle')." | Location: ".Input::get('calLocation'). " | Start: ".$startDate.
                            " | End: ".$endDate." | Timezone: ".Input::get('calTimez');
                $newCalID = Input::get('calNew') == 0 ? Input::get('calID') : $newCalID;
                $attendees = json_decode(Input::get('calAttend'));
                foreach ($attendees as $a) {
                    $attend = explode(';',trim($a->value));
                    if ($attend[0] != '') {
                        $calendarActivity = new CalendarActivity;
                        $calendarActivity->user_id = $attend[0];
                        $calendarActivity->type = Input::get('calType');
                        $calendarActivity->title = Input::get('calTitle');
                        $calendarActivity->start_date = Input::get('calStart');
                        $calendarActivity->end_date = Input::get('calEnd');
                        $calendarActivity->start_time = Input::get('calStartTime');
                        $calendarActivity->end_time = Input::get('calEndTime');
                        $calendarActivity->agenda = Input::get('calAgenda');
                        $calendarActivity->time_zone = Input::get('calTimez');
                        $calendarActivity->reminder = Input::get('calRemind');
                        $calendarActivity->location = Input::get('calLocation');
                        $calendarActivity->frequency = Input::get('calFrequency');
                        $calendarActivity->calendar_status = Input::get('calCalStatus');
                        $calendarActivity->create_by = auth()->user()->username;
                        $calendarActivity->status = 'I';
                        $calendarActivity->parent_id = $newCalID;//Input::get('calID');
                        $calendarActivity->partner_id = Input::get('calPartnerID');
                        
                        if (!$calendarActivity->save()) {
                            return response()->json(array(
                                'success' => false,
                                'message' => 'Unable to save calendar activity.'
                            ), 200);
                        }
                        $id = $calendarActivity->id;
                        $username = DB::table('users')
                            ->select('username')
                            ->where('id',$attend[0])
                            ->first();
                        $subject = "You are invited in an activity";
                        $notification = new Notification;
                        $notification->partner_id = -1;
                        $notification->source_id = -1;
                        $notification->subject = $subject;
                        $notification->message = $message;
                        $notification->recipient = $username->username;
                        $notification->status = 'N';
                        $notification->create_by = auth()->user()->username;
                        $notification->redirect_url = '/calendar?activityID='.$id;
                        $notification->save();
    
                        // Send email.
                        $email = DB::table('users')
                            ->select('email_address')
                            ->where('id',$attend[0])
                            ->first();
                        $email_address = $email->email_address;
                        $email_message = $message;
                        $email_subject = $subject;
                        $name = explode('(',$attend[2]);
                        
                        // Create mail notification.
                        $data = array(
                            'email_address' => $email_address,
                            'first_name' => $name[0],
                            'last_name' => 'you are invited',
                            'email_message' => $email_message,
                            'email_subject' => $email_subject,
                        );
                        
                        Mail::send(['html'=>'mails.incominglead'],$data,function($message) use ($data){
                            $message->to($data['email_address'],$data['first_name']);
                            $message->subject('[GoETU] '.$data['email_subject']);
                            $message->from('no-reply@goetu.com');
                        });
    
                        if (Mail::failures()) {
                            return redirect('/calendar')->with('failed','Failed to send email.');
                        }  
                        
                        $subject = "An Appointment has been Posted";
                        $notification = new Notification;
                        $notification->partner_id = -1;
                        $notification->source_id = -1;
                        $notification->subject = $subject;
                        $notification->message = $message;
                        $notification->recipient = auth()->user()->username;
                        $notification->status = 'N';
                        $notification->create_by = auth()->user()->username;
                        $notification->redirect_url = '/calendar?activityID='.Input::get('calID');
                        $notification->save();
                    }
                }
            }

            // Update calendar activity.
            if(Input::get('calNew') == 0){
                $updateCalendarActivity = CalendarActivity::find(Input::get('calID'));//$calendarActivity->id
                $updateCalendarActivity->type = Input::get('calType');
                $updateCalendarActivity->title = Input::get('calTitle');
                $updateCalendarActivity->start_date = Input::get('calStart');
                $updateCalendarActivity->end_date = Input::get('calEnd');
                $updateCalendarActivity->start_time = Input::get('calStartTime');
                $updateCalendarActivity->end_time = Input::get('calEndTime');
                $updateCalendarActivity->agenda = Input::get('calAgenda');
                $updateCalendarActivity->time_zone = Input::get('calTimez');
                $updateCalendarActivity->reminder = Input::get('calRemind');
                $updateCalendarActivity->location = Input::get('calLocation');
                $updateCalendarActivity->frequency = Input::get('calFrequency');
                $updateCalendarActivity->calendar_status = Input::get('calCalStatus');
                $updateCalendarActivity->update_by = auth()->user()->username;
                $updateCalendarActivity->status = Input::get('calStatus');
                $updateCalendarActivity->remind_flag = 0;
                if (!(Input::get('calStatus') == 'C' || Input::get('calStatus') == 'D')) {
                    $updateCalendarActivity->attendees = Input::get('calAttend');
                }
                
                
                if(!$updateCalendarActivity->save()){
                    return response()->json(array(
                        'success' => false,
                        'message' => 'Unable to save calendar activity.'
                    ), 200);
                }
                
                if (!(Input::get('calStatus') == 'C' || Input::get('calStatus') == 'D')) {
                    // Update activity in google calendar.
                    if (session('googleToken') !== null) {
                        $this->updateGoogleCalendarEvent(Input::get('calID'));
                    }
    
                    // Update activity in outlook calendar.
                    if (session('outlookToken') !== null) {
                        $this->updateOutlookCalendarEvent(Input::get('calID'));
                    }
                }

            }
            // If cancelled/deleted activity.
            if (Input::get('calStatus') == 'C' || Input::get('calStatus') == 'D') {
                $result = DB::table('calendar_activities')
                    ->where('parent_id',Input::get('calID'))
                    ->get();
                if (isset($result[0]->id)) {
                    $update = DB::table('calendar_activities')
                        ->where('parent_id', Input::get('calID'))
                        ->update(['status' => Input::get('calStatus')],['remind_flag' => 0]);
                    if (!$update) {
                        return response()->json(array(
                            'success' => false,
                            'message' => 'Unable to save calendar activity.'
                        ), 200);
                    }
                }

                // Update activity in google calendar.
                if (session('googleToken') !== null) {
                    $this->updateStatusGoogleCalendarEvent(Input::get('calID'));
                }

                // Update activity in outlook calendar.
                if (session('outlookToken') !== null) {
                    $this->updateStatusOutlookCalendarEvent(Input::get('calID'));
                }
            }

            // Create notification for cancelled activity.
            if(Input::get('calStatus') == 'C'){
                $attendees = json_decode(Input::get('calAttend'));
                foreach ($attendees as $a) {
                    $attend = explode(';',trim($a->value));
                    $id = Input::get('calID');
                    $username = DB::table('users')
                        ->select('username')
                        ->where('id',$attend[0])
                        ->first();
                    $subject = "Cancelled activity";
                    $message ="Title: ".Input::get('calTitle')." | Location: ".Input::get('calLocation'). " | Start: ".$startDate.
                    " | End: ".$endDate." | Timezone: ".Input::get('calTimez');
                    $notification = new Notification;
                    $notification->partner_id = -1;
                    $notification->source_id = -1;
                    $notification->subject = $subject;
                    $notification->message = $message;
                    $notification->recipient = $username->username;
                    $notification->status = 'N';
                    $notification->create_by = auth()->user()->username;
                    $notification->redirect_url = '/calendar?activityID='.$id;
                    $notification->save();
                }
            }

            // Notification if confirmed/declined attendance.
            if (Input::get('calStatus') == 'CF' || Input::get('calStatus') == 'DC') {
                $attendanceStatus = 'Declined';
                if (Input::get('calStatus') == 'CF') {
                    $attendanceStatus = 'Confirmed';
                }
                $parent_id = Input::get('calParentID');
                $result = DB::table('calendar_activities')
                    ->where('id',$parent_id)
                    ->get();
                if (isset($result[0]->id)) {
                    $attendees = json_decode($result[0]->attendees);
                    foreach ($attendees as $a) {
                        $attend = explode(';', trim($a->value));
                        if ($attend[0] == auth()->user()->id) {
                            $a->value = $attend[0]. ';' .$attendanceStatus. ';' .$attend[2];
                        }
                    }
                    $attendees = json_encode($attendees);

                    $updateCalendarActivity = CalendarActivity::find($parent_id);
                    $updateCalendarActivity->attendees = $attendees;
                    $updateCalendarActivity->remind_flag = 0;
                    if (!$updateCalendarActivity->save()) {
                        return response()->json(array(
                            'success' => false,
                            'message' => 'Unable to save calendar activity.'
                        ), 200);
                    }

                    $username = DB::table('users')
                        ->select('username')
                        ->where('id',$result[0]->user_id)
                        ->first();
                    $message ="Title: ".Input::get('calTitle')." | Location: ".Input::get('calLocation'). " | Start: ".$startDate.
                            " | End: ".$endDate." | Timezone: ".Input::get('calTimez');
                    $subject = auth()->user()->fname. ' ' .auth()->user()->lname. ' '.$attendanceStatus. ' Attendance on an appointment you set';
                    $notification = new Notification;
                    $notification->partner_id = -1;
                    $notification->source_id = -1;
                    $notification->subject = $subject;
                    $notification->message = $message;
                    $notification->recipient = $username->username;
                    $notification->status = 'N';
                    $notification->create_by = auth()->user()->username;
                    $notification->redirect_url = '/calendar?activityID='.$parent_id;
                    $notification->save();
                }

                // Update activity in google calendar.
                if (session('googleToken') !== null) {
                    $this->updateStatusGoogleCalendarEventAttendees($parent);
                }

                // Update activity in outlook calendar.
                if (session('outlookToken') !== null) {
                    $this->updateStatusOutlookCalendarEventAttendees($parent);
                }
            }
        });

        return response()->json(array('success' => true, 'id' => Input::get('calID'),'message' => 'Calendar Activity Updated!'), 200);
    }
    public function saveCalendarReminder(){
        DB::transaction(function(){
            $updateCalendar = new CalendarActivity;
            $updateCalendar->reminder = Input::get('calRemind');
            $updateCalendar->frequency = Input::get('calFrequency');
            $updateCalendar->update_by = auth()->user()->username;
            if (!$updateCalendar->save()) {
                return response()->json(array('success' => false, 'message' => 'Unable to save calendar reminder.'), 200);
            }
        });
        return response()->json(array('success' => false, 'id' => Input::get('calID'),'message' => 'Calendar Reminder Updated!'), 200);
    }

    public function getGClient($code)
    {
        $url = url()->current();
        $config = 'app/public/calendar/dev/';
        $clientId = 'client_secret_617423803222-eriu0bb04l8vr3mstdoe0p74uqt8ff40.apps.googleusercontent.com.json';
        
        if (strpos($url, 'vr2') !== false) {
            $config = 'app/public/calendar/staging/';
            $clientId = 'client_secret_193334722570-0s9h372f0faiogabh60a92pqbvv4guen.apps.googleusercontent.com.json';
        } elseif (strpos($url, 'uat') !== false) {
            $config = 'app/public/calendar/uat/';
            $clientId = 'client_secret_135149225271-5r2366s96rpe4eavp419l60h5062kied.apps.googleusercontent.com.json';
        } elseif (strpos($url, 'goetu.com') !== false) {
            $config = 'app/public/calendar/live/';
            $clientId = 'client_secret_15773246541-p4aum98osejfjjpjeq15lb8376bhajgd.apps.googleusercontent.com.json';
        }

        $client = new Google_Client();
        $client->setApplicationName('GoETU CRM Calendar');
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig(storage_path($config . $clientId));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setApprovalPrompt('force');

        if (session('googleToken') !== null) {
            $client->setAccessToken(session('googleToken'));
            return $client; 
        }
        
        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $accessToken = $client->getAccessToken();
                $client->setAccessToken($accessToken);
                session()->forget('googleToken');
                session(['googleToken' => json_encode($accessToken)]);
                return $client; 
            } 
            if ($code == 1) {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                return response()->json([
                    'success' => true, 
                    'url' => $authUrl
                ], 200);
            }

            if (!session('googleToken')) {
                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($code);
                $client->setAccessToken($accessToken);
                session(['googleToken' => json_encode($accessToken)]);
                return $client; 
            }
        }
    }

    private function getGoogleCalEvents($client)
    {   
        // Create new calendar for google.
        $calendarId = SyncCalendar::where('user_id', auth()->user()->id)
            ->where('calendar_tag', 'G')
            ->first();

        if (empty($calendarId)) {
            $title = 'GoETU Calendar Integration';
            $timezone = 'America/New_York';

            $cal = new Google_Service_Calendar($client);

            $google_calendar = new Google_Service_Calendar_Calendar();
            $google_calendar->setSummary($title);
            $google_calendar->setTimeZone($timezone);

            $created_calendar = $cal->calendars->insert($google_calendar);

            $calendarId = $created_calendar->getId();

            $syncCalendar = new SyncCalendar;
            $syncCalendar->user_id = auth()->user()->id;
            $syncCalendar->title = $title;
            $syncCalendar->calendar_id = $calendarId;
            $syncCalendar->calendar_tag = 'G';
            $syncCalendar->save();
        } else {
            $calendarId = $calendarId->calendar_id;
        }

        // Retrieve calendar activities then insert to google calendar.
        $fullCalendar = CalendarActivity::where('user_id', auth()->user()->id)
            ->where('status', '<>', 'D')
            ->where('is_added_to_gcal', 0)
            ->get();

        if (!empty($fullCalendar)) {
            $calEvnt = new Google_Service_Calendar($client);
            $attendees = [];

            foreach ($fullCalendar as $activity) {
                $timeZone = TimeZone::select('value')
                    ->where('name', $activity->time_zone)
                    ->first();
                $organizer = User::select('first_name', 'last_name', 'email_address')
                    ->where('username', $activity->create_by)
                    ->first();
                $organizerName = $organizer->first_name . ' ' . $organizer->last_name;
                $creator = User::select('first_name', 'last_name', 'email_address')
                    ->where('id', $activity->user_id)
                    ->first();
                $creatorName = $creator->first_name . ' ' . $creator->last_name;

                if (!empty($activity->attendees)) {
                    $allAttendee = json_decode($activity->attendees);

                    foreach ($allAttendee as $a) {
                        $attend = explode(';', trim($a->value));
                        list($part1, $part2) = explode('(', $attend[2]);
                        $attendee['displayName'] = $part1;
                        $attendee['email'] = str_replace(')', '', $part2);
                        $attendee['responseStatus'] = strtolower($attend[1]);
                        $attendees[] = $attendee;
                    }
                }

                $event = new Google_Service_Calendar_Event(array(
                    'summary' => $activity->title,
                    'location' => $activity->location,
                    'description' => $activity->agenda,
                    'organizer' => array(
                        'displayName' => $organizerName,
                        'email' => $organizer->email_address,
                    ),
                    'creator' => array(
                        'displayName' => $creatorName,
                        'email' => $creator->email_address,
                    ),
                    'attendees' => $attendees,
                ));

                $start = new Google_Service_Calendar_EventDateTime();
                $start->setDateTime(date("c", strtotime($activity->start_date)));
                $event->setStart($start);

                $end = new Google_Service_Calendar_EventDateTime();
                $end->setDateTime(date("c", strtotime($activity->end_date)));
                $event->setEnd($end);
                    
                $event = $calEvnt->events->insert($calendarId, $event);

                $updateCalAct = CalendarActivity::find($activity->id);
                $updateCalAct->is_added_to_gcal = 1;
                $updateCalAct->google_event_id = $event->id;
                $updateCalAct->save();
            }
        }

        // Retrieve google events then insert to goetu calendar.
        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        );
        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        foreach ($events as $event) {
            $timeZone = TimeZone::select('name')
                ->where('value', $event->start->timeZone)
                ->first();
            $timeZone = empty($timeZone) ? '(GMT+08:00) Manila' : $timeZone;

            $setTimeZone = empty($event->start->timeZone) ? '(GMT+08:00) Manila' : $event->start->timeZone;
            $g_datetime_start = Carbon::parse($event->start->dateTime)
                ->setTimezone($setTimeZone)
                ->format('Y-m-d H:i:s');
            $g_datetime_end = Carbon::parse($event->end->dateTime)
                ->setTimezone($setTimeZone)
                ->format('Y-m-d H:i:s');
            $g_time_start = Carbon::parse($event->start->dateTime)
                ->setTimezone($setTimeZone)
                ->format('H:i:s');
            $g_time_end = Carbon::parse($event->end->dateTime)
                ->setTimezone($setTimeZone)
                ->format('H:i:s');

            $attendees = [];
            if ($event->attendees) {
                foreach ($event->attendees as $key => $value) {
                    $response = 'Tentative';
                    if ($value->responseStatus == "accepted") {
                        $response = "Confirmed";
                    } elseif ($value->responseStatus == "declined") {
                        $response = "Declined";
                    }
                    $attendee['value'] = null . ';' . $response . ';' . $value->displayName . '(' . $value->email . ')';
                    $attendee['name'] = 'attendees';
                    $attendees[] = $attendee;
                }
            }

            // Save google calendar events to calendar_activities.
            $calendarActivity = CalendarActivity::firstOrNew(['google_event_id' => $event->id]);
            $calendarActivity->user_id = auth()->user()->id;
            $calendarActivity->type = 0;
            $calendarActivity->title = $event->summary;
            $calendarActivity->start_date = $g_datetime_start;
            $calendarActivity->end_date = $g_datetime_end;
            $calendarActivity->start_time = $g_time_start;
            $calendarActivity->end_time = $g_time_end;
            $calendarActivity->agenda = $event->summary;
            $calendarActivity->time_zone = isset($timeZone->name) ? $timeZone->name : NULL;
            $calendarActivity->attendees = json_encode($attendees);
            $calendarActivity->location = $event->location;
            $calendarActivity->create_by = auth()->user()->username;
            $calendarActivity->status = 'P';
            $calendarActivity->partner_id = auth()->user()->company_id;
            $calendarActivity->parent_id = -1;
            $calendarActivity->google_event_id = $event->id;
            $calendarActivity->event_tag = 'G';
            $calendarActivity->is_added_to_gcal = 1;
            $calendarActivity->save();
        }
        
        return redirect('/calendar')->with('success', 'Sync Complete!');
    }

    private function insertToGoogleCalendarEvent($eventId) 
    {
        // Get google calendar_id
        $sync = SyncCalendar::where('user_id', auth()->user()->id)
            ->where('calendar_tag', 'G')
            ->first();

        // Get row from db
        $activity = CalendarActivity::find($eventId);

        // Sample Insert
        $client = $this->getGClient($sync->calendar_id);
        $calEvnt = new Google_Service_Calendar($client);

        $attendees = [];

        $timeZone = TimeZone::select('value')
            ->where('name', $activity->time_zone)
            ->first();

        $organizer = User::select('first_name', 'last_name', 'email_address')
            ->where('username', $activity->create_by)
            ->first();

        $organizerName = $organizer->first_name . ' ' . $organizer->last_name;

        $creator = User::select('first_name', 'last_name', 'email_address')
            ->where('id', $activity->user_id)
            ->first();

        $creatorName = $creator->first_name . ' ' . $creator->last_name;

        if (!empty($activity->attendees)) {
            $allAttendee = json_decode($activity->attendees);

            foreach ($allAttendee as $a) {
                $attend = explode(';', trim($a->value));
                list($part1, $part2) = explode('(', $attend[2]);
                $attendee['displayName'] = $part1;
                $attendee['email'] = str_replace(')', '', $part2);
                $attendee['responseStatus'] = strtolower($attend[1]);
                $attendees[] = $attendee;
            }
        }

        $event = new Google_Service_Calendar_Event(array(
            'summary' => $activity->title,
            'location' => $activity->location,
            'description' => $activity->agenda,
            'organizer' => array(
                'displayName' => $organizerName,
                'email' => $organizer->email_address,
            ),
            'creator' => array(
                'displayName' => $creatorName,
                'email' => $creator->email_address,
            ),
            'attendees' => $attendees,
        ));

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime(date("c", strtotime($activity->start_date)));
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime(date("c", strtotime($activity->end_date)));
        $event->setEnd($end);

        $calendarId = $sync->calendar_id;
        // all inserts to goetu calendar integrate
        $insertedEvent = $calEvnt->events->insert($calendarId, $event);

        $updateCalAct = CalendarActivity::find($activity->id);
        $updateCalAct->is_added_to_gcal = 1;
        $updateCalAct->google_event_id = $insertedEvent->id;

        if ($insertedEvent && $updateCalAct->save()) {
            return true;
        } else {
            return false;
        }
    }

    private function updateGoogleCalendarEvent($eventId) 
    {
        // Retrieve google calendar_id.
        $sync = SyncCalendar::where('user_id', auth()->user()->id)
            ->where('calendar_tag', 'G')    
            ->first();

        // Retrieve calendar activity.
        $activity = CalendarActivity::find($eventId);

        // Update google calendar event.
        $client = $this->getGClient($sync->calendar_id);
        $calEvnt = new Google_Service_Calendar($client);

        // Determine event/activity origin.
        $calendarId = $activity->event_tag == "G" ? 'primary' : $sync->calendar_id;

        $attendees = [];

        if (!empty($activity->attendees)) {
            $allAttendee = json_decode($activity->attendees);

            foreach ($allAttendee as $a) {
                $attend = explode(';', trim($a->value));
                list($part1, $part2) = explode('(', $attend[2]);
                $attendee['displayName'] = $part1;
                $attendee['email'] = str_replace(')', '', $part2);
                $attendee['responseStatus'] = strtolower($attend[1]);
                $attendees[] = $attendee;
            }
        }

        $timeZone = TimeZone::select('value')
            ->where('name', $activity->time_zone)
            ->first();

        $organizer = User::select('first_name', 'last_name', 'email_address')
            ->where('username', $activity->create_by)
            ->first();

        $organizerName = $organizer->first_name . ' ' . $organizer->last_name;

        // First retrieve the event from the API.
        $event = $calEvnt->events->get($calendarId, $activity->google_event_id);

        $event->setSummary($activity->title);
        $event->setLocation($activity->location);
        $event->setDescription($activity->agenda);
        $event->attendees = $attendees;

        $organizer = new Google_Service_Calendar_EventOrganizer();
        $organizer->setEmail($organizer->email_address);
        $organizer->setDisplayName($organizerName);
        $event->setOrganizer($organizer);

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime(date("c", strtotime($activity->start_date)));
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime(date("c", strtotime($activity->end_date)));
        $event->setEnd($end);

        $updatedEventInfo = $calEvnt->events->update($calendarId, $activity->google_event_id, $event);

        if ($updatedEventInfo) {
            return true;
        } else {
            return false;
        }
    }

    private function updateStatusGoogleCalendarEvent($eventId) 
    {
        // Retrieve google calendar_id.
        $sync = SyncCalendar::where('user_id', auth()->user()->id)
            ->where('calendar_tag', 'G')    
            ->first();

        // Retrieve calendar activity.
        $activity = CalendarActivity::find($eventId);

        // Update google calendar event.
        $client = $this->getGClient($sync->calendar_id);
        $calEvnt = new Google_Service_Calendar($client);
        
        // Determine event/activity origin.
        $calendarId = $activity->event_tag == "G" ? 'primary' : $sync->calendar_id;
        
        // First retrieve the event from the API.
        $event = $calEvnt->events->get($calendarId, $activity->google_event_id);

        $status = $activity->status == "C" || $activity->status == "D" ? "cancelled" : "tentative";

        $event->setStatus($status);

        $updatedEventStatus = $calEvnt->events->update($calendarId, $activity->google_event_id, $event);

        if ($updatedEventStatus) {
            return true;
        } else {
            return false;
        }
    }

    private function updateStatusGoogleCalendarEventAttendees($eventId) 
    {
        // Retrieve google calendar_id.
        $sync = SyncCalendar::where('user_id', auth()->user()->id)
            ->where('calendar_tag', 'G')    
            ->first();

        // Retrieve calendar activity.
        $activity = CalendarActivity::find($eventId);

        // Update google calendar event.
        $client = $this->getGClient($sync->calendar_id);
        $calEvnt = new Google_Service_Calendar($client);
        
        $calendarId = $activity->event_tag == "G" ? 'primary' : $sync->calendar_id;
        
        $attendees = [];

        if (!empty($activity->attendees)) {
            $allAttendee = json_decode($activity->attendees);

            foreach ($allAttendee as $a) {
                $attend = explode(';', trim($a->value));
                list($part1, $part2) = explode('(', $attend[2]);
                $attendee['displayName'] = $part1;
                $attendee['email'] = str_replace(')', '', $part2);
                $attendee['responseStatus'] = strtolower($attend[1]);
                $attendees[] = $attendee;
            }
        }

        // First retrieve the event from the API.
        $event = $calEvnt->events->get($calendarId, $activity->google_event_id);

        $event->attendees = $attendees;

        $updatedEventAttendees = $calEvnt->events->update($calendarId, $activity->google_event_id, $event);

        if ($updatedEventAttendees) {
            return true;
        } else {
            return false;
        }
    }

    /* private function deleteGoogleCalendarEvent($eventTag, $eventId) 
    {
        // Get google calendar_id
        $sync = SyncCalendar::where('user_id', auth()->user()->id)->first();

        // Sample Delete
        $client = $this->getGClient($sync->calendar_id);
        $calEvnt = new Google_Service_Calendar($client);

        $calendarId = $eventTag == 'G' ? 'primary' : $sync->calendar_id ;

        $deletedEvent = $calEvnt->events->delete($calendarId, $eventId);

        if ($deletedEvent) {
            return true;
        } else {
            return false;
        }
    } */

    public function getOClient()
    {
        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => env('OAUTH_APP_ID'),
            'clientSecret'            => env('OAUTH_APP_PASSWORD'),
            'redirectUri'             => env('OAUTH_REDIRECT_URI'),
            'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
            'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
            'urlResourceOwnerDetails' => '',
            'scopes'                  => env('OAUTH_SCOPES')
        ]);
  
        $authUrl = $oauthClient->getAuthorizationUrl();
  
        // Save client state so we can validate in callback
        session(['oauthState' => $oauthClient->getState()]);
  
        // Redirect to AAD signin page
        return response()->json([
            'success' => true, 
            'url' => $authUrl
        ], 200);
    }

    public function outlookCallback(Request $request)
    {
        // Validate state
        $expectedState = session('oauthState');
        $request->session()->forget('oauthState');
        $providedState = $request->query('state');

        if (!isset($expectedState) || !isset($providedState) || $expectedState != $providedState) {
        return redirect('/')
            ->with('error', 'Invalid auth state')
            ->with('errorDetail', 'The provided auth state did not match the expected value');
        }

        // Authorization code should be in the "code" query param
        $authCode = $request->query('code');
        if (isset($authCode)) {
            // Initialize the OAuth client
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId'                => env('OAUTH_APP_ID'),
                'clientSecret'            => env('OAUTH_APP_PASSWORD'),
                'redirectUri'             => env('OAUTH_REDIRECT_URI'),
                'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
                'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
                'urlResourceOwnerDetails' => '',
                'scopes'                  => env('OAUTH_SCOPES')
            ]);

            try {
                // Make the token request
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $authCode
                ]);
              
                $graph = new Graph();
                $graph->setAccessToken($accessToken->getToken());
              
                $user = $graph->createRequest('GET', '/me')
                    ->setReturnType(Model\User::class)
                    ->execute();
              
                $tokenCache = new TokenCache();
                $tokenCache->storeTokens($accessToken, $user);
              
                return redirect('/calendar');
            }
            catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return redirect('/calendar')
                ->with('failed', $e->getMessage());
            }
        }

        return redirect('/calendar')
            ->with('failed', $request->query('error_description'));
    }

    private function getOutlookCalendarEvents()
    {
        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        // Save primary outlook calendar id
        $events = $graph->createRequest('GET', '/me/calendars')
            ->setReturnType(Model\Calendar::class)
            ->execute();

        $syncCalendar = SyncCalendar::firstOrNew(['calendar_id' => $events[0]->getId()]);
        $syncCalendar->user_id = auth()->user()->id;
        $syncCalendar->title = 'Outlook Primary Calendar';
        $syncCalendar->calendar_id = $events[0]->getId();
        $syncCalendar->calendar_tag = 'O';
        $syncCalendar->save();

        // Create new calendar for outlook.
        $calendarId = SyncCalendar::where('user_id', auth()->user()->id)
            ->where('calendar_tag', 'O')
            ->first();

        if (empty($calendarId)) {
            $title = 'GoETU Calendar Integration';
            $newCalendar = array(
                'name' => $title
            );
            
            $postCalendarUrl = '/me/calendars';
    
            $calendar = $graph->createRequest("POST", $postCalendarUrl)
                ->attachBody($newCalendar)
                ->setReturnType(Model\Calendar::class)
                ->execute();

            $calendarId = $calendar->getId();

            $syncCalendar = new SyncCalendar;
            $syncCalendar->user_id = auth()->user()->id;
            $syncCalendar->title = $title;
            $syncCalendar->calendar_id = $calendarId;
            $syncCalendar->calendar_tag = 'O';
            $syncCalendar->save();
        } else {
            $calendarId = $calendarId->calendar_id;
        }

        // Get calendar_activities, insert to outlook calendar.
        $fullCalendar = CalendarActivity::where('user_id', auth()->user()->id)
            ->where('status', '<>', 'D')
            ->where('is_added_to_ocal', 0)
            ->latest()
            ->limit(10)
            ->get();

        if (!empty($fullCalendar)) {
            $attendees = [];

            foreach ($fullCalendar as $activity) {
                $timeZone = TimeZone::select('value')
                    ->where('name', $activity->time_zone)
                    ->first();
                $organizer = User::select('first_name', 'last_name', 'email_address')
                    ->where('username', $activity->create_by)
                    ->first();
                $organizerName = $organizer->first_name . ' ' . $organizer->last_name;

                if (!empty($activity->attendees)) {
                    $allAttendee = json_decode($activity->attendees);

                    foreach ($allAttendee as $a) {
                        $attend = explode(';', trim($a->value));
                        list($part1, $part2) = explode('(', $attend[2]);
                        $attendee['Type'] = "required";
                        $response = "none";
                        if ($attend[1] == "Confirmed") {
                            $response = "accept";
                        } elseif ($attend[1] == "Declined") {
                            $response = "decline";
                        }
                        $attendee['Status']['Response'] = $response;
                        $attendee['EmailAddress']['Name'] = $part1;
                        $attendee['EmailAddress']['Address'] = str_replace(')', '', $part2);
                        $attendees[] = $attendee;
                    }
                }

                $data = [
                    'Subject' => $activity->title,
                    'Body' => [
                        'ContentType' => 'HTML',
                        'Content' => $activity->agenda,
                    ],
                    'Start' => [
                        'DateTime' => substr(date("c", strtotime($activity->start_date)), 0, -6),
                        'TimeZone' => 'Pacific Standard Time', // $timeZone->value,
                    ],
                    'End' => [
                        'DateTime' => substr(date("c", strtotime($activity->end_date)), 0, -6),
                        'TimeZone' => 'Pacific Standard Time', // $timeZone->value,
                    ],
                    'Location' => [
                        'DisplayName' => $activity->location,
                    ],
                    'Attendees' => $attendees,
                    'Organizer' => [
                        'EmailAddress' => [
                            'Name' => $organizerName,
                            'Address' => $organizer->email_address,
                        ]
                    ]
                ];

                $url = "/me/calendars/$calendarId/events";

                $response = $graph->createRequest("POST", $url)
                    ->attachBody($data)
                    ->setReturnType(Model\Event::class)
                    ->execute();

                $updateCalAct = CalendarActivity::find($activity->id);
                $updateCalAct->is_added_to_ocal = 1;
                $updateCalAct->outlook_event_id = $response->getId();
                $updateCalAct->save();
            }
        }
        
        // Get outlook events, insert to calendar_activities
        $getQueryParams = array(
            '$select' => 'id,subject,bodyPreview,body,organizer,start,end,location,iCalUId,attendees',
            '$orderby' => 'createdDateTime DESC'
        );

        // Append query parameters to the '/me/events' url
        $getEventsUrl = '/me/events?' . http_build_query($getQueryParams); 

        $events = $graph->createRequest('GET', $getEventsUrl)
            ->setReturnType(Model\Event::class)
            ->execute();

        // save goetu activities to outlook events on goetu calendar integrate
        if (!empty($events)) {
            foreach ($events as $event) {
                $timeZone = TimeZone::select('name')
                    ->where('value', $event->getStart()->getTimeZone())
                    ->first();
                $timeZone = empty($timeZone) ? '(GMT+08:00) Manila' : $timeZone;

                $setTimeZone = empty($event->getStart()->getTimeZone()) ? '(GMT+08:00) Manila' : $event->getStart()->getTimeZone();
                $g_datetime_start = Carbon::parse($event->getStart()->getDateTime())
                    ->setTimezone($setTimeZone)
                    ->format('Y-m-d H:i:s');
                $g_datetime_end = Carbon::parse($event->getEnd()->getDateTime())
                    ->setTimezone($setTimeZone)
                    ->format('Y-m-d H:i:s');
                $g_time_start = Carbon::parse($event->getStart()->getDateTime())
                    ->setTimezone($setTimeZone)
                    ->format('H:i:s');
                $g_time_end = Carbon::parse($event->getEnd()->getDateTime())
                    ->setTimezone($setTimeZone)
                    ->format('H:i:s');

                $attendees = [];
                if ($event->getAttendees()) {
                    foreach ($event->getAttendees() as $key => $value) {
                        $response = 'Tentative';
                        if ($value['status']['response'] == "accept") {
                            $response = "Confirmed";
                        } elseif ($value['status']['response'] == "decline") {
                            $response = "Declined";
                        }
                        $attendee['value'] = null . ';' . $response . ';' . $value['emailAddress']['name'] . '(' . $value['emailAddress']['address'] . ')';
                        $attendee['name'] = 'attendees';
                        $attendees[] = $attendee;
                    }
                }

                // Save google calendar events to calendar_activities.
                $calendarActivity = CalendarActivity::firstOrNew(['outlook_event_id' => $event->getId()]);
                $calendarActivity->user_id = auth()->user()->id;
                $calendarActivity->type = 0;
                $calendarActivity->title = $event->getSubject();
                $calendarActivity->start_date = $g_datetime_start;
                $calendarActivity->end_date = $g_datetime_end;
                $calendarActivity->start_time = $g_time_start;
                $calendarActivity->end_time = $g_time_end;
                $calendarActivity->agenda = $event->getBody()->getContent();
                $calendarActivity->time_zone = isset($timeZone->name) ? $timeZone->name : NULL;
                $calendarActivity->attendees = json_encode($attendees);
                $calendarActivity->location = $event->getLocation()->getDisplayName();
                $calendarActivity->create_by = auth()->user()->username;
                $calendarActivity->status = 'P';
                $calendarActivity->partner_id = auth()->user()->company_id;
                $calendarActivity->parent_id = -1;
                $calendarActivity->outlook_event_id = $event->getId();
                $calendarActivity->event_tag = 'O';
                $calendarActivity->is_added_to_ocal = 1;
                $calendarActivity->save();
            }
        }

        return response()->json($events);
    }

    private function insertToOutlookCalendarEvent($eventId)
    {
        // Get row from db
        $activity = CalendarActivity::find($eventId);

        $attendees = [];

        $timeZone = TimeZone::select('value')
        ->where('name', $activity->time_zone)
        ->first();
        $organizer = User::select('first_name', 'last_name', 'email_address')
            ->where('username', $activity->create_by)
            ->first();
        $organizerName = $organizer->first_name . ' ' . $organizer->last_name;

        if (!empty($activity->attendees)) {
            $allAttendee = json_decode($activity->attendees);

            foreach ($allAttendee as $a) {
                $attend = explode(';', trim($a->value));
                list($part1, $part2) = explode('(', $attend[2]);
                $attendee['Type'] = "required";
                $response = "none";
                if ($attend[1] == "Confirmed") {
                    $response = "accept";
                } elseif ($attend[1] == "Declined") {
                    $response = "decline";
                }
                $attendee['Status']['Response'] = $response;
                $attendee['EmailAddress']['Name'] = $part1;
                $attendee['EmailAddress']['Address'] = str_replace(')', '', $part2);
                $attendees[] = $attendee;
            }
        }

        $data = [
            'Subject' => $activity->title,
            'Body' => [
                'ContentType' => 'HTML',
                'Content' => $activity->agenda,
            ],
            'Start' => [
                'DateTime' => substr(date("c", strtotime($activity->start_date)), 0, -6),
                'TimeZone' => 'Pacific Standard Time', // $timeZone->value,
            ],
            'End' => [
                'DateTime' => substr(date("c", strtotime($activity->end_date)), 0, -6),
                'TimeZone' => 'Pacific Standard Time', // $timeZone->value,
            ],
            'Location' => [
                'DisplayName' => $activity->location,
            ],
            'Attendees' => $attendees,
            'Organizer' => [
                'EmailAddress' => [
                    'Name' => $organizerName,
                    'Address' => $organizer->email_address,
                ]
            ]
        ];

        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        // Get outlook calendar_id
        $sync1 = SyncCalendar::where('user_id', auth()->user()->id)
            ->whereRaw("title LIKE '%Integration%'")
            ->where('calendar_tag', 'O')
            ->first();

        $calendarId = $sync1->calendar_id;
        
        $url = "/me/calendars/$calendarId/events";

        $response = $graph->createRequest("POST", $url)
            ->attachBody($data)
            ->setReturnType(Model\Event::class)
            ->execute();

        $updateCalAct = CalendarActivity::find($activity->id);
        $updateCalAct->is_added_to_ocal = 1;
        $updateCalAct->outlook_event_id = $response->getId();
        $updateCalAct->save();

        if ($response && $updateCalAct->save()) {
            return true;
        } else {
            return false;
        }
    }

    private function updateOutlookCalendarEvent($eventId)
    {
        // Retrieve calendar activity.
        $activity = CalendarActivity::find($eventId);

        $attendees = [];

        $timeZone = TimeZone::select('value')
            ->where('name', $activity->time_zone)
            ->first();
        $organizer = User::select('first_name', 'last_name', 'email_address')
            ->where('username', $activity->create_by)
            ->first();
        $organizerName = $organizer->first_name . ' ' . $organizer->last_name;

        if (!empty($activity->attendees)) {
            $allAttendee = json_decode($activity->attendees);

            foreach ($allAttendee as $a) {
                $attend = explode(';', trim($a->value));
                list($part1, $part2) = explode('(', $attend[2]);
                $attendee['Type'] = "required";
                $response = "none";
                if ($attend[1] == "Confirmed") {
                    $response = "accept";
                } elseif ($attend[1] == "Declined") {
                    $response = "decline";
                }
                $attendee['Status']['Response'] = $response;
                $attendee['EmailAddress']['Name'] = $part1;
                $attendee['EmailAddress']['Address'] = str_replace(')', '', $part2);
                $attendees[] = $attendee;
            }
        }

        $data = [
            'Subject' => $activity->title,
            'Body' => [
                'ContentType' => 'HTML',
                'Content' => $activity->agenda,
            ],
            'Start' => [
                'DateTime' => substr(date("c", strtotime($activity->start_date)), 0, -6),
                'TimeZone' => 'Pacific Standard Time', // $timeZone->value,
            ],
            'End' => [
                'DateTime' => substr(date("c", strtotime($activity->end_date)), 0, -6),
                'TimeZone' => 'Pacific Standard Time', // $timeZone->value,
            ],
            'Location' => [
                'DisplayName' => $activity->location,
            ],
            'Attendees' => $attendees,
            'Organizer' => [
                'EmailAddress' => [
                    'Name' => $organizerName,
                    'Address' => $organizer->email_address,
                ]
            ]
        ];

        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        // Get outlook calendar_id
        $sync = SyncCalendar::where('user_id', auth()->user()->id)
            ->whereRaw("title LIKE '%Primary%'")
            ->where('calendar_tag', 'O')
            ->first();

        $sync1 = SyncCalendar::where('user_id', auth()->user()->id)
            ->whereRaw("title LIKE '%Integration%'")
            ->where('calendar_tag', 'O')
            ->first();

        $calendarId = $activity->event_tag == 'O' ? $sync->calendar_id : $sync1->calendar_id;
            
        $url = "/me/calendars/$calendarId/events/" . $activity->outlook_event_id;

        $response = $graph->createRequest("PATCH", $url)
            ->attachBody($data)
            ->setReturnType(Model\Event::class)
            ->execute();

        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    private function updateStatusOutlookCalendarEvent($eventId) 
    {
        // Retrieve calendar activity.
        $activity = CalendarActivity::find($eventId);

        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        // Get outlook calendar_id
        $sync = SyncCalendar::where('user_id', auth()->user()->id)
            ->whereRaw("title LIKE '%Primary%'")
            ->where('calendar_tag', 'O')
            ->first();

        $sync1 = SyncCalendar::where('user_id', auth()->user()->id)
            ->whereRaw("title LIKE '%Integration%'")
            ->where('calendar_tag', 'O')
            ->first();

        $calendarId = $activity->event_tag == 'O' ? $sync->calendar_id : $sync1->calendar_id;

        if ($activity->status == "C") { // Cancel
            $url = "/me/calendars/$calendarId/events/" . $activity->outlook_event_id . "/cancel";
            
            $response = $graph->createRequest("POST", $url)
                ->setReturnType(Model\Event::class)
                ->execute();

        } elseif ($activity->status == "D") { // Delete
            $url = "/me/calendars/$calendarId/events/" . $activity->outlook_event_id;
            
            $response = $graph->createRequest("DELETE", $url)
                ->setReturnType(Model\Event::class)
                ->execute();

        }

        if ($response) {
            return true;
        } else {
            return false;
        }
    }    

    private function updateStatusOutlookCalendarEventAttendees($eventId)
    {
        // Retrieve calendar activity.
        $activity = CalendarActivity::find($eventId);

        $attendees = [];

        if (!empty($activity->attendees)) {
            $allAttendee = json_decode($activity->attendees);

            foreach ($allAttendee as $a) {
                $attend = explode(';', trim($a->value));
                list($part1, $part2) = explode('(', $attend[2]);
                $attendee['Type'] = "required";
                $response = "none";
                if ($attend[1] == "Confirmed") {
                    $response = "accept";
                } elseif ($attend[1] == "Declined") {
                    $response = "decline";
                }
                $attendee['Status']['Response'] = $response;
                $attendee['EmailAddress']['Name'] = $part1;
                $attendee['EmailAddress']['Address'] = str_replace(')', '', $part2);
                $attendees[] = $attendee;
            }
        }

        $data = [
            'Attendees' => $attendees,
        ];

        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        // Get google calendar_id
        $sync = SyncCalendar::where('user_id', auth()->user()->id)
            ->whereRaw("title LIKE '%Primary%'")
            ->where('calendar_tag', 'O')
            ->first();

        $sync1 = SyncCalendar::where('user_id', auth()->user()->id)
            ->whereRaw("title LIKE '%Integration%'")
            ->where('calendar_tag', 'O')
            ->first();

        $calendarId = $activity->event_tag == 'O' ? $sync->calendar_id : $sync1->calendar_id;
        
        $url = "/me/calendars/$calendarId/events/" . $activity->outlook_event_id;

        $response = $graph->createRequest("PATCH", $url)
            ->attachBody($data)
            ->setReturnType(Model\Event::class)
            ->execute();

        if ($response) {
            return true;
        } else {
            return false;
        }
    }


    
}
