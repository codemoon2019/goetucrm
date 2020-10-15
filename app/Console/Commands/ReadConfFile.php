<?php

namespace App\Console\Commands;

use App\Models\InvoiceHeader;
use App\Models\Partner;
use App\Models\PartnerPaymentInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReadConfFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:readConfFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read configuration file';

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
        Log::info('Start Read Conf File Cron');

        $hasFile = false;
        $hasError = false;
        $errorMessage = "";
        $date = date("Ymd");

        $files = Storage::disk('download')->files('/conffile');

        foreach ($files as $file) {
            $name = basename($file);
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            $csvHeaders = ["Credits", "Debits", "CreditCount",
                "DebitCount", "RejectCount"];

            if ($extension == "csv") {
                $hasFile = true;  
                $hasError = false; 

                $downloadPath = Storage::disk('download')->getDriver()
                    ->getAdapter()->getPathPrefix();


                if (($handle = fopen($downloadPath . $file, "r")) !== FALSE) {
                    $filenameWithoutExtension = str_replace('.csv','',$file); 

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if (!in_array($data[0], $csvHeaders)) {
                            $hasError = true;   
                            $errorMessage =  $data[0];
                        }
                        
                        if ($data[0] == "Error") {
                            $errorMessage = isset($data[1]) ? $data[1] : $data[0];
                        }
                    }

                    if ($hasError) {
                        $invoiceHeader = InvoiceHeader::where(
                            'filename', basename($filenameWithoutExtension))
                            ->first();
                        $invoiceHeader->is_processed = 1;
                        $invoiceHeader->failed_response_message = $errorMessage;
                        $invoiceHeader->save();

                        $cmd  = "SELECT h.partner_id, h.id, h.export_date, " . 
                                    "h.total_due, p.partner_id_reference, " .
                                    "pc.company_name, products.name, " . 
                                    "h.failed_response_message, p.merchant_mid " . 
                                "FROM invoice_headers h " .
                                "LEFT JOIN product_orders po " . 
                                    "ON po.id = h.order_id " .
                                "LEFT JOIN products " . 
                                    "ON products.id = po.product_id " .
                                "INNER JOIN partners p " . 
                                    "ON p.id = h.partner_id " .
                                "INNER JOIN partner_companies pc " . 
                                    "ON pc.partner_id = h.partner_id " .
                                "WHERE filename = '{$filenameWithoutExtension}'";
                        
                        $result = DB::select(DB::raw($cmd));

                        foreach ($result  as $r) {
                            $companyId = Partner::get_top_upline_partner($r->partner_id);   
                            $emailAddress = PartnerPaymentInfo::select('email_address')
                                ->where('partner_id', $companyId)
                                ->first()->email_address;
                            
                            $subject = "{$r->company_name}'s processing of ACH failed";
                            
                            $emailBody  = "Hi,<br/><br/>";
                            $emailBody .= "Processing of ACH failed with the following details:<br><br>";
                            $emailBody .= "Merchant Name: {$r->company_name}<br/>";
                            $emailBody .= "Merchant Reference ID: {$r->partner_id_reference}<br/>";
                            $emailBody .= "Merchant MID: {$r->merchant_mid}<br/>";
                            $emailBody .= "Invoice #: {$r->id}<br/>";
                            $emailBody .= "Product: {$r->name}<br/>";
                            $emailBody .= "Amount: {$r->total_due}<br/>";
                            $emailBody .= "Charge Date: {$r->export_date}<br/>";
                            $emailBody .= "Reason: {$r->failed_response_message}";

                            $email = collect([
                                'address' => $emailAddress,
                                'subject' => $subject,
                                'body' => $emailBody
                            ]);

                            Mail::send(['html'=>'mails.basic2'], ['email' => $email], 
                                function($message) use ($email){
                                    $message->to($email->get("address"));
                                    $message->subject($email->get("subject"));
                                    $message->from('no-reply@goetu.com');
                                }
                            );
                        }    
                    } else {
                        $invoiceHeader = InvoiceHeader::where(
                            'filename', $filenameWithoutExtension)->first();
                        $invoiceHeader->is_processed = 1;
                        $invoiceHeader->failed_response_message = $data[0];
                        $invoiceHeader->save();
                    }
                    
                    fclose($handle);
                }

                $fileContents = Storage::disk('download')->get($file);
                Storage::disk('backup')->put("{$date}_" . basename($file), $fileContents);
                Log::info(
                    "Moved from app/public/downloads/" .
                    "{$file} to app/public/backups/" . "{$date}_{" . basename($file) . "}"
                );  
                Storage::disk('download')->delete($file);
            } 
        }

        if (!$hasFile) {
            Log::info('No files to be processed.');
        }

        Log::info('End Read Conf File Cron');
    }
}
