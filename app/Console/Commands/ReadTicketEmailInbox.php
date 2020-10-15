<?php

namespace App\Console\Commands;

use App\Models\EmailOnQueue;
use App\Models\Partner;
use App\Models\PartnerCompany;
use App\Models\Product;
use App\Models\TicketDetail;
use App\Models\TicketEmailSupport;
use App\Models\TicketEmailSupportLogs;
use App\Models\TicketHeader;
use App\Models\User;
use App\Models\UserTypeProductAccess;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class ReadTicketEmailInbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:readTicketEmailInbox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read ticket email inbox';

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
        Log::info("Start Read Ticket Email Inbox Cron");

        $folder = "INBOX.Processed";
        $ticketEmailSupports = TicketEmailSupport::where('status', 'A')->get();
        
        $results = array();
        foreach ($ticketEmailSupports  as $ticketEmailSupport) {
            $connection = @imap_open('{' . $ticketEmailSupport->server. '/pop3/novalidate-cert' . '}', 
                $ticketEmailSupport->email_address, $ticketEmailSupport->password, OP_SILENT);
            
            if (isset($connection) || is_null($connection)) {
                Log::warning("Unable to Log in");
                continue;
            }
            
            $msgCount = imap_num_msg($connection);
            Log::info("I passed");

            $inbox = array();
            for ($i = 1; $i <= $msgCount; $i++) {
                $isTicket = 0;
                $inbox = array(
                    'index'     => $i,
                    'header'    => imap_headerinfo($connection, $i),
                    'body'      => imap_body($connection, $i),
                    'body_text' => imap_fetchbody($connection, $i,1),
                    'body_html' => imap_fetchbody($connection, $i,1.2),
                    'structure' => imap_fetchstructure($connection, $i)
                );
                
                $fileAttachment = "";
                $structure = imap_fetchstructure($connection, $i);

                $attachments = array();
                if (isset($structure->parts) && count($structure->parts)) {
                    for ($j = 0; $j < count($structure->parts); $j++) {
                        $attachments[$j] = array(
                            'is_attachment' => false,
                            'filename'      => '',
                            'name'          => '',
                            'attachment'    => ''
                        );

                        if ($structure->parts[$j]->ifdparameters) {
                            foreach ($structure->parts[$j]->dparameters as $object) {
                                if (strtolower($object->attribute) == 'filename') {
                                    $attachments[$j]['is_attachment'] = true;
                                    $attachments[$j]['filename'] = $object->value;
                                }
                            }
                        }

                        if ($structure->parts[$j]->ifparameters) {
                            foreach ($structure->parts[$j]->parameters as $object) {
                                if (strtolower($object->attribute) == 'name') {
                                    $attachments[$j]['is_attachment'] = true;
                                    $attachments[$j]['name'] = $object->value;
                                }
                            }
                        }

                        if ($attachments[$j]['is_attachment']) {
                            $attachments[$j]['attachment'] = imap_fetchbody($connection, $i, $j+1);

                            /** 3 = BASE64 encoding */
                            if ($structure->parts[$j]->encoding == 3) { 
                                $attachments[$j]['attachment'] = base64_decode($attachments[$j]['attachment']);
                            }

                            /** 4 = QUOTED-PRINTABLE encoding */
                            elseif($structure->parts[$j]->encoding == 4) { 
                                $attachments[$j]['attachment'] = quoted_printable_decode($attachments[$j]['attachment']);
                            }
                        }
                    }
                }

                /** Iterate through each attachment and save it */
                foreach($attachments as $attachment) {
                    if ($attachment['is_attachment'] == 1) {
                        $filename = preg_replace('/\s+/', '', $attachment['name']);
                        $filename = time().$filename;
                        $filename = empty($filename) ?: $attachment['filename']; 
                        $filename = empty($filename) ?: time() . ".dat"; 

                        /* if (empty($filename)) $filename = $attachment['filename'];
                        if (empty($filename)) $filename = time() . ".dat"; */

                        $folder = "uploads/ticket";
                        $fp = fopen("./". $folder . "/" . $filename, "w+");
                        fwrite($fp, $attachment['attachment']);
                        fclose($fp);
                        $fileAttachment = URL::to('/') . "//public/" . $folder . "/" . $filename;
                    }
                }
                
                $description = $this->getBody($i, $connection);

                $fromaddr = $inbox['header']->from[0]->mailbox . "@" . $inbox['header']->from[0]->host;

                $temp = User::where('email_address', $fromaddr)->first();
                $username="";    

                if (isset($temp)){
                    $username = $temp->username;    
                }

                if ($username == "") {
                    goto next_record;    
                }
                
                $in_subject = explode("/", $inbox['header']->subject);    
                $subject = $in_subject[0];

                if (strpos(strtolower($subject), 'ticket-') !== false) {
                    $ticket_number = -1;
                    $ticket = explode("-", $subject);    
                    
                    if (!isset($ticket[1])){
                        goto next_record;
                    }

                    if (!is_numeric($ticket[1])){
                        goto next_record;
                    }

                    $ticket_number = $ticket[1];    
                    $temp = TicketHeader::find($ticket_number);

                    if (!isset($temp)) {
                        goto next_record;    
                    }

                    $params = array(
                        'ticket_id' => $ticket_number, 
                        'message' => htmlspecialchars($description), 
                        'attachment' => $fileAttachment ,  
                        'message_type' => 1, 
                        'create_by' => $username,
                    );

                    $result = $this->createTicketReply($params);
                } else {  
                    /** New Ticket */
                    if (!isset($in_subject[1])) {
                        goto next_record;
                    }

                    if (!isset($in_subject[2])) {
                        goto next_record;
                    }

                    $partner_id=-1;
                    $product_id=-1;
                    $user_type_id=-1;

                    $partnerExist = Partner::where([
                        ['partner_id_reference', $in_subject[2]],
                        ['id', '<>', 1],
                        ['status', 'A']
                    ])->first();
                    
                    if ($partnerExist) {
                        $partner_id = $partnerExist->id;    
                    }

                    if ($partner_id == -1){
                        goto next_record;    
                    } 

                    $productExist = Product::where([
                        ['name', $in_subject[2]],
                        ['id', '<>', 1],
                        ['status', 'A']
                    ])->first();

                    if (isset($productExist)) {
                        $product_id = $productExist->id;    
                    }

                    if ($product_id == -1) {
                        goto next_record;    
                    }  

                    $userTypeProductAccess = UserTypeProductAccess::where('product_id', $product_id)->first();
                    
                    if (isset($userTypeProductAccess)) {
                        $user_type_id = $userTypeProductAccess->user_type_id;    
                    }

                    if ($user_type_id == -1) {
                        goto next_record;    
                    } 
                    
                    $partner_access=-1;

                    $access_id = User::select('reference_id')
                        ->where('username', $username)
                        ->first()
                        ->reference_id;
                    $partner_access = $this->get_partners_access($access_id);      
                    
                    if ($partner_access == "") { 
                        $partner_access=$id;
                    }
                    
                    $response = Partner::get_partner_ifno($partner_id, false, "3", $partner_access);
                    if (count($response) == 0){
                        goto next_record;
                    } 
                        
                    $params = array(
                        'subject'     => $subject, 
                        'partner_id'  => $partner_id, 
                        'product_id'  => $product_id, 
                        'status'      => 'O', 
                        'type'        => 'Q', 
                        'priority'    => 'L',  
                        'department'  => $user_type_id, 
                        'due_date'    => date('Y-m-d H:i:s'), 
                        'description' => htmlspecialchars($description), 
                        'attachment'  => $fileAttachment ,  
                        'assignee'    => null,   
                        'create_by'   => $username,
                    );

                    $result = $this->createTicket($params); 
                    $isTicket = 1;
                }

                /** Move email to processed */
                next_record:            
                    imap_delete($connection, $i);
                
                    /** Save Email to Logs */
                    $insertData = array(
                        'from'        => $fromaddr,
                        'to'          => $ticketEmailSupport['email_address'],
                        'subject'     => $inbox['header']->subject,
                        'message'     => $description,                 
                        'create_date' => date('Y-m-d H:i:s'),
                        'isTicket'    => $isTicket,  
                    );

                    TicketEmailSupportLogs::create($insertData);
            }

            imap_expunge($connection);
            imap_close($connection);    
        }

        Log::info("End Read Ticket Email Inbox Cron");
    }

    public function createTicketReply($params)
    {
        DB::beginTransaction();

        try {
            $data = array(
                'ticket_id'     => $params['ticket_id'], 
                'message_type'  => $params['message_type'], 
                'message'       => $params['message'], 
                'attachment'    => $params['attachment'], 
                'create_by'     => $params['create_by'],
                'create_date'   => date('Y-m-d H:i:s'),
            );

            TicketDetail::create($data);

            if (!isset($params['skip_header_update'])) {
                $data = array(
                    'update_by' => $params['create_by'],
                );  

                $ticketHeader = TicketHeader::find($params['ticket_id']);
                $ticketHeader->update_by = $params['create_by'];
                $ticketHeader->save();
            }

            DB::commitTransaction();
            
        } catch (\Exception $ex) {
            DB::rollback();
            return false;
        }

        /** Send Email Notification */
        if (!isset($params['skip_email']))
        {
            $ticketHeader = TicketHeader::find($params['ticket_id']);
            if (isset($ticketHeader)) {
                if ($ticketHeader->assignee != "") {
                    $cmd = "SELECT first_name, last_name, email_address 
                            FROM users 
                            WHERE id IN({$ticketHeader->assignee})
                            UNION DISTINCT
                                SELECT first_name, last_name, email_address 
                                FROM users 
                                WHERE reference_id = {$ticketHeader->partner_id}
                                UNION DISTINCT
                                    SELECT first_name, last_name, email_address 
                                    FROM users 
                                    WHERE username = '{$ticketHeader->create_by}'";     
                } else {
                    $cmd = "SELECT u.first_name,u.last_name,u.email_address 
                            FROM users u
                            INNER JOIN user_types ut on ut.id = u.user_type_id
                            WHERE ut.id IN({$ticketHeader->department})
                            UNION DISTINCT 
                                SELECT first_name, last_name, email_address 
                                FROM users 
                                WHERE reference_id = {$ticketHeader->partner_id}  
                                UNION distinct
                                    SELECT first_name, last_name, email_address 
                                    FROM users 
                                    WHERE username = '{$params['create_by']}'"; 
                }

                $rs = DB::select(DB::raw($cmd));
                $merchant = PartnerCompany::select('company')
                    ->where('partner_id', $ticketHeader->partner_id)->first();

                foreach ($rs as $r) {     
                    $subject = "New reply for ticket #{$ticketHeader->id} has been made";
                    $email_body ="Hi {$r['first_name']} {$r['last_name']},<br><br> 
                                A new ticket reply has been made with the following details:<br><br>
                                Merchant: {$merchant} <br><br>
                                Subject: {$ticketHeader->subject} <br><br>
                                Message: " . html_entity_decode($params['message']);
                    
                    $oqe_params = array(
                        'subject' => $subject,
                        'body' => $email_body,
                        'email_address' => $r['email_address'],
                        'create_by' => $params['create_by'],
                    );
                    
                    $this->createOnQueueEmail($oqe_params);
                }
            }
        }

        return true;
    }

    public function createOnQueueEmail($params)
    {
        DB::beginTransaction();

        try {
            $updateData = array(
                'subject'       => $params['subject'],
                'body'          => $params['body'],
                'email_address' => $params['email_address'],
                'create_by'     => $params['create_by'],
            );

            EmailOnQueue::create($updateData);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
        }

        return true;
    }

    public function createTicket($params)
    {
        $assignee = "";  
        if ($params['assignee'] != null){
            foreach ($params['assignee'] as $data) {
                $assignee = $assignee . $data . ","; 
            }

            if (strlen(trim($assignee)) > 0){
                $assignee = substr($assignee, 0, strlen($assignee) - 1);    
            }
        }

        DB::beginTransaction();
        try {
            $date = date('Y-m-d H:i:s', strtotime($params['due_date']));
            $data = array(
                'subject'     => $params['subject'], 
                'partner_id'  => $params['partner_id'], 
                'product_id'  => $params['product_id'], 
                'status'      => $params['status'], 
                'type'        => $params['type'], 
                'priority'    => $params['priority'], 
                'department'  => $params['department'], 
                'due_date'    => $date, 
                'description' => $params['description'], 
                'attachment'  => $params['attachment'], 
                'assignee'    => $assignee, 
                'create_by'   => $params['create_by'],
                'parent_id'   => $params['parent_id'], 
            );
    
            TicketHeader::create($data);
            DB::commitTransaction();
        } catch (\Exception $ex) {
            DB::rollback();
        }
        
        /** Send Email Notification */
        if ($assignee != "") {
            $cmd = "SELECT * 
                    FROM users 
                    WHERE id IN({$assignee})";
            $ms = "to you";       
        } else {
            $cmd = "SELECT u.* 
                    FROM users u
                    INNER JOIN user_types ut 
                        ON ut.id = u.user_type_id
                    WHERE ut.id IN({$params['department']})"; 
            $ms = "to your group";
        }

        $rs = DB::select(DB::raw($cmd));
        $merchant = PartnerCompany::select('company_name')
            ->where('partner_id', $ticketHeader->partner_id)->first();

        foreach ($rs as $r) {     
            
            $subject = "New Ticket has been created";
            $email_body = "Hi {$r->first_name} {$r->last_name},<br><br> 
                      A new ticket has been assigned {$ms} with the following details:<br><br>
                      Merchant: {$merchant} <br><br>
                      Subject: {$params['subject']} <br><br>
                      Message: " . html_entity_decode($params['description']);

            $oqe_params = array(
                'subject'       => $subject,
                'body'          => $email_body,
                'email_address' => $r->email_address,
                'create_by'     => $params['create_by'],
            );
            
            $this->createOnQueueEmail($oqe_params);
        }

        return true;
    }

    function getBody($uid, $imap)
    {
        $body = $this->getPart($imap, $uid, "TEXT/HTML");

        /** If HTML body is empty, try getting text body */
        if ($body == "") {
            $body = $this->getPart($imap, $uid, "TEXT/PLAIN");
        }
        return $body;
    }

    function getPart($imap, $uid, $mimetype, $structure = false, $partNumber = false)
    {
        if (!$structure) {
            $structure = imap_fetchstructure($imap, $uid, FT_UID);
        }

        if ($structure) {
            if ($mimetype == $this->getMimeType($structure)) {
                if (!$partNumber) {
                    $partNumber = 1;
                }
                $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID);
                switch ($structure->encoding) {
                    case 3:
                        return imap_base64($text);
                    case 4:
                        return imap_qprint($text);
                    default:
                        return $text;
                }
            }

            /** Multipart */
            if ($structure->type == 1) {
                foreach ($structure->parts as $index => $subStruct) {
                    $prefix = "";
                    if ($partNumber) {
                        $prefix = $partNumber . ".";
                    }
                    $data = $this->getPart($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));

                    if ($data) {
                        return $data;
                    }
                }
            }
        }

        return false;
    }

    function getMimeType($structure)
    {
        $primaryMimetype = ["TEXT", "MULTIPART", "MESSAGE", 
            "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"];

        if ($structure->subtype) {
            return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }
}
