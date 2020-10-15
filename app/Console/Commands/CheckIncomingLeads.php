<?php

namespace App\Console\Commands;

use App\Models\IncomingLead;
use App\Models\Notification;
use App\Models\Partner;
use App\Models\PartnerContact;
use App\Models\SystemSetting;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CheckIncomingLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:checkIncomingLeads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check incoming leads';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Start Checking Incoming Leads Cron');
        $systemSetting = SystemSetting::where('set_code', 
            'incoming_lead_approval')->first();

        $cmat = $systemSetting->set_value;
        $currentDatetime = strtotime(date('Y-m-d H:i:s'));

        $incomingLeads = IncomingLead::where('status', 'N')->get();

        foreach ($incomingLeads as $incomingLead){
            $leadTime = strtotime($incomingLead->created_at);
            $timediff = ($currentDatetime - $leadTime) / 3600;
            
            if ((float) $timediff > (float) $cmat) {
                $incomingLead->status = 'E';
                $incomingLead->save();
                $partnerInfo = Partner::get_partner_info($incomingLead->partner_id);
                $emailRecipient = PartnerContact::select('email')
                    ->where('partner_id', $incomingLead->creator_id)
                    ->first()
                    ->email;

                $subject  = "{$partnerInfo[0]->merchant_id} - ";
                $subject .= "{$partnerInfo[0]->first_name} ";
                $subject .= "{$partnerInfo[0]->last_name} request expires";
                
                $recipient = User::select('username')
                    ->where('reference_id', $incomingLead['creator_id'])
                    ->first()
                    ->username;

                $email = collect([
                    'body' => 'Request expires.',
                    'subject' => $subject,
                    'recipient' => $emailRecipient
                ]);

                Notification::create([
                    'partner_id' => $incomingLead->creator_id,
                    'source_id' => $incomingLead->assigned_id,
                    'subject' => $email->get("subject"),
                    'message' => $email->get("body"),
                    'status' => 'N',
                    'create_by' => 'admin',
                    'recipient' => $email->get("recipient"),
                    'redirect_url' => URL::to('/') . "/lead/edit/{$incomingLead['assigned_id']}",
                ]);
                
                Mail::send(['html' => 'mails.basic2'], ['email' => $email], 
                    function($message) use ($email){
                        $message->to($email->get("recipient"));
                        $message->subject($email->get("subject"));
                        $message->from('no-reply@goetu.com');
                    }
                );
            }
        }

        Log::info('End Checking Incoming Leads Cron');
    }
}
