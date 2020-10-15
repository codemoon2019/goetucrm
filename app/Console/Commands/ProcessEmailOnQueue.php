<?php

namespace App\Console\Commands;

use App\Models\EmailOnQueue;
use App\Models\TicketHeader;
use App\Models\TicketDetail;
use App\Models\TicketAttachment;
use App\Models\TicketDetailsAttachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessEmailOnQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:processEmailOnQueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process emails that is on the email_on_queues table';

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
        Log::info('Started Process Email On Queue Cron');            
        $emailOnQueue = EmailOnQueue::where('is_sent', 0)->get();

        foreach ($emailOnQueue as $email) 
        {
            try {
                $message_id = "";
                $mailTemplate = 'mails.basic';

                if ($email->ticket_header_id == -1 && $email->ticket_header_id == -1) {
                    $mailTemplate = 'mails.basic3';
                }

                Mail::send(['html'=>$mailTemplate], ['email' => $email], 
                    function($message) use ($email,&$message_id){
                        $message->to(explode(',',$email->email_address));
                        $message->subject($email->subject);
                        $message->from('no-reply@goetu.com');
                        $message_id = $message->getId();

                        if($email->ticket_header_id != -1){
                            $ticketAttachment = TicketAttachment::where('ticket_header_id',$email->ticket_header_id)->get();
                            if(isset($ticketAttachment))
                            {
                                foreach($ticketAttachment as $attach){
                                    $message->attach(storage_path('app/public/attachment/ticket').'/'.$attach->path);
                                }
                            }
                        }
                        if($email->ticket_detail_id != -1){
                            $ticketAttachment = TicketDetailsAttachment::where('ticket_detail_id',$email->ticket_detail_id)->get();
                            if(isset($ticketAttachment))
                            {
                                foreach($ticketAttachment as $attach){
                                    $message->attach(storage_path('app/public/attachment/ticket').'/'.$attach->path);
                                }
                            }
                        }

                        if ($email->invoice_header_id != -1) {
                            $message->attach(public_path()."/pdf/invoice_preview_{$email->invoice_header_id}.pdf");
                        }
                    }
                );

                if (Mail::failures()) {
                    Log::error("Failed to process email! " . 
                        "Email On Queue ID: {$email->id} " .
                        "Email On Queue Address: {$email->address} " . 
                        "Email On Queue Subject: {$email->subject} " . 
                        "Email On Queue From: {$email->from} "
                    );
                } else {
                    Log::info("Successfully processed email with Email On Queue ID of {$email->id}");
                    if($email->ticket_header_id != -1){
                        $ticket = TicketHeader::find($email->ticket_header_id);
                        if (isset($ticket))
                        {
                            $ticket->email_message_id = $message_id;
                            $ticket->save();
                        }
                    }
                    if($email->ticket_detail_id != -1){
                        $ticket = TicketDetail::find($email->ticket_detail_id);
                        if (isset($ticket))
                        {
                            $ticket->email_message_id = $message_id;
                            $ticket->save();
                        }
                    }

                    $email->is_sent = 1;
                    $email->sent_date = date('Y-m-d H:i:s');
                    $email->save();
                }
            } catch (\Exception $ex) {
                Log::error("Failed to process email! " . 
                    "Email On Queue ID: {$email->id} " .
                    "Email On Queue Address: {$email->address} " . 
                    "Email On Queue Subject: {$email->subject} " . 
                    "Email On Queue From: {$email->from} " .
                    "Error Message: {$ex->getMessage()}"
                );
            }
            sleep(10); //10 seconds interval before sending another email to avoid spam issues
        }

        Log::info('End Process Email On Queue Cron');  
    }
}
