<?php

namespace App\Console\Commands;

use App\Models\EmailOnQueue;
use App\Models\TicketHeader;
use App\Models\TicketDetail;
use App\Models\TicketAttachment;
use App\Models\TicketDetailAttachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Webklex\IMAP\Facades\Client;
use App\Models\User;
use Carbon\Carbon;

class ProcessEmailTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:processEmailTickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process tickets that is emailed on official email account';

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
        Log::info('Started Process Tickets On Email Cron');   

        $oClient = Client::account('default');
        $oClient->connect();
        $aFolder = $oClient->getFolders();
        $oFolder = $oClient->getFolder('INBOX'); 

        $users = User::where('status','A')->get();
        foreach ($users as $user) {
            $aMessage = $oFolder->query()->from($user->email_address)->get();
            foreach ($aMessage as $msg) {
                // $user = User::where('email_address',$msg->getFrom()[0]->mail)->first();
                if(isset($user)){
                    if($msg->getReferences()  !== null){
                        $references= explode(' ', $msg->getReferences());
                        $reference = str_replace('<', '', $references[0]);
                        $reference = str_replace('>', '', $reference);
                        $ticket = TicketHeader::where('email_message_id',$reference)->first();
                        if(isset($ticket)){
                            $message = $msg->getHTMLBody();
                            switch ($msg->getFrom()[0]->host) {
                                case 'goetu.com':
                                    $message = str_before($message, '<hr id="zwchr" data-marker="__DIVIDER__">');
                                    $message .= '</div></body></html>';
                                    break;
                                    
                                case 'gmail.com':
                                    $message = str_before($message, '<div class="gmail_extra">');
                                    break;

                                case 'yahoo.com':
                                    $message = str_before($message, '<div id="yahoo');
                                    $message .= '</div></body></html>';
                                    break;
                            }

                            $ticketDetail = new TicketDetail;
                            $ticketDetail->ticket_id = $ticket->id;
                            $ticketDetail->message = $message;
                            $ticketDetail->create_by = $user->username;
                            $ticketDetail->email_message_id = $msg->getMessageId();
                            $ticketDetail->save();
                            $msg->moveToFolder('Tickets');

                           $msg->getAttachments()->each(function ($oAttachment) use ($msg,$ticketDetail) {
                                $filename = $msg->getMessageId().$oAttachment->name;
                                $oAttachment->save(storage_path('app/public/attachment/ticket'),$msg->getMessageId().$oAttachment->name);
                                $attach = new TicketDetailsAttachment;
                                $attach->ticket_detail_id = $ticketDetail->id;
                                $attach->name = $oAttachment->name;
                                $attach->path = "/attachment/ticket/". $filename;
                                $attach->save();
                           });

                        }else{
                            $message = $msg->getHTMLBody();
                            switch ($msg->getFrom()[0]->host) {
                                case 'goetu.com':
                                    $message = str_before($message, '<hr id="zwchr" data-marker="__DIVIDER__">');
                                    $message .= '</div></body></html>';
                                    break;

                                case 'gmail.com':
                                    $message = str_before($message, '<div class="gmail_extra">');
                                    break;

                                case 'yahoo.com':
                                    $message = str_before($message, '<div id="yahoo');
                                    $message .= '</div></body></html>';
                                    break;
                            }

                            $ticket = TicketDetail::where('email_message_id',$reference)->first();
                            if(isset($ticket)){
                                $ticketDetail = new TicketDetail;
                                $ticketDetail->ticket_id = $ticket->ticket_id;
                                $ticketDetail->message = $message;
                                $ticketDetail->create_by = $user->username;
                                $ticketDetail->email_message_id = $msg->getMessageId();
                                $ticketDetail->save();
                                $msg->moveToFolder('Tickets');

                               $msg->getAttachments()->each(function ($oAttachment) use ($msg,$ticketDetail) {
                                    $filename = $msg->getMessageId().$oAttachment->name;
                                    $oAttachment->save(storage_path('app/public/attachment/ticket'),$msg->getMessageId().$oAttachment->name);
                                    $attach = new TicketDetailsAttachment;
                                    $attach->ticket_detail_id = $ticketDetail->id;
                                    $attach->name = $oAttachment->name;
                                    $attach->path = "/attachment/ticket/". $filename;
                                    $attach->save();
                               });
                            }else{
                                Log::info("Invalid email refererence".$reference);
                                $msg->moveToFolder('Ticket-Errors');
                            }
                        }

                    }else{
                        $ticket = new TicketHeader;
                        $ticket->subject = $msg->getSubject();
                        $ticket->status = 'N';
                        $ticket->type = 0;
                        $ticket->reason = 0;
                        $ticket->priority = 'H';
                        $ticket->department = -1;
                        $ticket->assignee = -1;
                        $ticket->ticket_date = Carbon::now();
                        $ticket->description = $msg->getHTMLBody();
                        $ticket->create_by = $user->username;
                        $ticket->requester_id = $user->id;
                        $ticket->source_email = $msg->getFrom()[0]->mail;
                        $ticket->email_message_id = $msg->getMessageId();
                        $ticket->save();
                        $msg->moveToFolder('Tickets');

                        $data = array(
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $msg->getFrom()[0]->mail,
                            'msg' => $msg->getHTMLBody()
                        );

                        $message_id = "";
                        $response = Mail::send(['html'=>'mails.ticketaccept'],$data,function($message) use ($data,&$message_id){
                            $message->to($data['email'],$data['first_name'].' '.$data['last_name']);
                            $message->subject('[GoETU] Ticket Confirmation');
                            $message->from('no-reply@goetu.com');
                            $message_id = $message->getId();
                        });
                        $ticket->email_message_id = $message_id;
                        $ticket->save();

                       $msg->getAttachments()->each(function ($oAttachment) use ($msg,$ticket) {
                            $filename = $msg->getMessageId().$oAttachment->name;
                            $oAttachment->save(storage_path('app/public/attachment/ticket'),$msg->getMessageId().$oAttachment->name);
                            $attach = new TicketAttachment;
                            $attach->ticket_header_id = $ticket->id;
                            $attach->name = $oAttachment->name;
                            $attach->path = "/attachment/ticket/". $filename;
                            $attach->save();
                       });

                    }
                }else{
                    $data = array(
                        'email' => $msg->getFrom()[0]->mail,
                        'msg' => $msg->getHTMLBody()
                    );

                    $message_id = "";
                    $response = Mail::send(['html'=>'mails.ticketfailed'],$data,function($message) use ($data){
                        $message->to($data['email'],'');
                        $message->subject('[GoETU] Invalid Email');
                        $message->from('no-reply@goetu.com');
                    });

                    $msg->moveToFolder('Ticket-Errors');
                    Log::info("Invalid email processed");
                }
            }

        }

        Log::info('End Process Tickets On Email Cron');  
    }
}
