<?php

namespace App\Console\Commands;

use App\Models\InvoiceHeader;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReadReturnFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:readReturnFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read return file';

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

        $hasFile = false;
        $date = date("Ymd");
        $directories = Storage::disk('download')->files('/');

        $amount = 0;
        foreach ($directories as $file) {
            $filename = basename($file);
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $processedFiles = array();

            if ($extension == 'csv') {
                $hasFile = true;
                $rows = 0;

                $downloadPath = Storage::disk('download')->getDriver()
                    ->getAdapter()->getPathPrefix();

                if (($handle = fopen($downloadPath . $filename, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $rows++;
                        $companyId          = $data[0];
                        $companyName        = $data[1];
                        $effectiveDate      = $data[2];
                        $transactionCode    = $data[3];
                        $routingNumber      = $data[4];
                        $accountNumber      = $data[5];
                        $amount             = $data[6];
                        $individualId       = trim($data[7]);
                        $individualName     = $data[8];
                        $returnReasonCode   = $data[9];
                        $returnReasonInfo   = $data[10];
                        $returnTraceNumber  = $data[11];

                        if (is_numeric(trim($individualId))) {
                            $invoiceHeader = InvoiceHeader::find($individualId);
           
                            if (!is_null($invoiceHeader)) {
                                $invoiceHeader->status = 'R';
                                $invoiceHeader->return_reason_code = $returnReasonCode;
                                $invoiceHeader->return_info = $returnReasonInfo;
                                $invoiceHeader->save();

                                $result = $this->getInvoiceHeader($individualId);

                                if (!is_null($result)) {
                                    $message = $result->return_reason;
                                    $subject = "Processing of Invoice #{$individualId} was unsuccessful.";
                                    $redirectUrl  = "/merchant/invoicenum?";
                                    $redirectUrl .= "{$result->partner_id}&{$result->invoice_id}";

                                    $data = array(
                                        'partner_id' => $result->parent_id,
                                        'source_id' => -1,
                                        'subject' => $subject,
                                        'message' => $message,
                                        'recipient' => $result->username,
                                        'status' => 'N',
                                        'create_by' => 'admin',
                                        'redirect_url' => $redirectUrl,
                                    );

                                    Notification::create($data);
                                    
                                    $this->sendMail($result);            
                                    Log::info("Processed individual id of {$individualId}");

                                    array_push($processedFiles, [
                                        'file' => $file,
                                        'filename' => $filename,
                                    ]);
                                }
                            } else {
                                Log::warning("Individual ID {$individualId} does not exist in the database.");
                            }
                        }
                    }
                    fclose($handle);

                    if ($rows == count($pf)) {
                        $fileContents = Storage::disk('download')->get($pf['file']);
                        Storage::disk('backup')->put("{$date}_{$pf['filename']}", $fileContents);
                        Storage::disk('download')->delete($file);
                        Log::info("Processed file {$filename} and now uploaded to backups and deleted on downloads");
                    } else {
                        Log::warning("Processed file {$filename} but not deleted on downloads");
                    }
                }
            }
        }
        
        if (!$hasFile) {
            Log::info('No files to be processed.');
        }

        Log::info('End Process Email On Queue Cron');  
    }

    public function getInvoiceHeader($individualId)
    {
        $cmd = "SELECT h.id AS invoice_id, p.parent_id, 
                    h.partner_id, pc.first_name, pc.last_name, 
                    pc.email, u.username, pcom.company_name, 
                    IFNULL(ir.name, '') AS return_reason 
                FROM invoice_headers h 
                INNER JOIN partners p 
                    ON p.id = h.partner_id 
                INNER JOIN partner_companies pcom 
                    ON pcom.partner_id = p.id 
                INNER JOIN partners pp 
                    ON pp.id = p.parent_id 
                INNER JOIN partner_contacts pc 
                    ON pc.partner_id = pp.id 
                    AND is_original_contact = 1 
                INNER JOIN users u 
                    ON u.reference_id = pp.id 
                LEFT JOIN invoice_reject_codes ir 
                    ON ir.code = h.return_reason_code 
                WHERE h.id = {$individualId} 
                LIMIT 1";
    
        return DB::select(DB::raw($cmd))[0]; 
    }

    public function sendMail($result)
    {
        $emailBody  = "Hi {$result->first_name} {$result->last_name}, <br />><br />";
        $emailBody .= "{$result->company_name} Invoice#{$result->invoice_id} was ";
        $emailBody .= "unsuccessfully processed due to the following reason:<br /><br />";
        $emailBody .= "{$result->return_reason}"; 

        $email = collect([
            'address' => $result->email,
            'subject' => 'ACH Processing Unsuccessful',
            'body' => $emailBody
        ]);

        Mail::send(['html'=>'mails.basic2'], ['email' => $email], 
            function($message) use ($email){
                $message->to($email->get("address"));
                $message->subject("ACH Processing Unsuccessful");
                $message->from('no-reply@goetu.com');
            }
        );
    }

    
}
