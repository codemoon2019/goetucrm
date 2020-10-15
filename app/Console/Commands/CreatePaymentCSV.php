<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Partner;
use App\Models\ACHConfiguration;

use DB;
use Carbon\Carbon;
use DateTime;
use Log;
use File;
use Storage;



class CreatePaymentCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:createPaymentCSV';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates payments in CSV format';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**1
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {        
            $cmd  = "SELECT h.partner_id, h.id " .
                    "FROM invoice_headers h " .
                    "INNER JOIN invoice_payments ip " .
                        "ON ip.invoice_id = h.id " .
                    "WHERE h.status IN('S') " .
                        "AND ip.payment_type_id = 1 " .
                        "AND IFNULL(is_exported,0) = 0 " .
                    "UNION DISTINCT " .
                    "SELECT h.partner_id, h.id " .
                    "FROM invoice_headers h " .
                    "INNER JOIN invoice_payments ip " .
                        "ON ip.invoice_id = h.id " .
                        "AND ip.payment_type_id = 1 " .
                    "WHERE h.status IN('U') AND h.due_date <= now() " .
                        "AND IFNULL(is_exported,0) = 0 ";

            $records = DB::select(DB::raw($cmd));  
            $results = array(); 
            foreach ($records as $r) {
                $company_id = Partner::get_upline_company($r->partner_id);
                $row = ACHConfiguration::where('partner_id',$company_id)->where('status','A')->first();
                if (isset($row)) {
                    $cmd  = "SELECT ih.id,'1002004141' AS parent_company_id, " .
                            "'First Pay Solutions' AS parent_company_name, " .
                            "'{$row->pay_token}' AS company_id, " .
                            "'{$row->pay_to}' AS company_name, " .
                            "'CCD' AS sec_code, " .
                            "CASE WHEN IFNULL(ih.reference, '') = '' " .
                                "THEN 'No default value' " .
                                "ELSE ih.reference " .
                            "END AS description," .
                            "date_format(invoice_date, '%y%m%d') effective_date, " .
                            "p.partner_id_reference AS individual_id, " .
                            "ifnull(pc.company_name,'GoETU Merchant') AS individual_name, " .
                            "ppi.bank_account_number, " .
                            "ppi.routing_number, " .
                            "'27' AS transaction_code, " .
                            "ih.total_due AS payment_amount, " .
                            "ih.id AS trace_number, " .
                            "'' AS web_transaction, " .
                            "ih.remarks AS addenda_info " .
                            "FROM invoice_headers ih " .
                            "INNER JOIN invoice_payments ip " .
                                "ON ip.invoice_id = ih.id " .
                            "INNER JOIN partners p " .
                                "ON p.id = ih.partner_id " .
                            "INNER JOIN partner_companies pc " .
                                "ON p.id = pc.partner_id " .
                            "INNER JOIN partner_contacts pcon " .
                                "ON p.id = pcon.partner_id " .
                                "AND is_original_contact = 1 " .
                            "INNER JOIN partners pp " .
                                "ON pp.id = p.parent_id " .
                            "INNER JOIN partner_companies ppc " .
                                "ON pp.id = ppc.partner_id " .
                            "INNER JOIN partner_payment_infos ppi " .
                                "ON ppi.partner_id = ih.partner_id " .
                                "AND ppi.payment_type_id = 1 " .
                                "AND ppi.status = 'A' " .
                            "WHERE ih.id = {$r->id}"; 
                    $customs = DB::select(DB::raw($cmd));       
                    $row->details = $customs;  
                    $results[] = $row;
                }
            }
            $content = "";
            $date_time=  date('Ymd') . "_" . time();
            $has_file = false;
            foreach ($results as $r) {
                $has_detail=false;
                $content=""; 
                $strIDs=""; 
                $filename = substr($r->pay_to,0,3) .'_'. $date_time . "_fps.csv";
                $url = 'storage/ach_files/'.$filename;
                foreach ($r->details as $detail) {    
                    $parent_company_id = substr($detail->parent_company_id,0,10);       
                    $parent_company_name = substr($detail->parent_company_name,0,16);       
                    $company_id = substr($detail->company_id,0,10);       
                    $company_name = substr($detail->company_name,0,16);       
                    $sec_code = substr($detail->sec_code,0,3);       
                    $description = substr($detail->description,0,10);       
                    $effective_date = substr($detail->effective_date,0,6);       
                    $description = substr($detail->description,0,15);       
                    $individual_id = substr($detail->individual_id,0,15);       
                    $individual_name = substr($detail->individual_name,0,22);       
                    $routing_number = substr($detail->routing_number,0,9);       
                    $bank_account_number = substr($detail->bank_account_number,0,17);       
                    $transaction_code = substr($detail->transaction_code,0,2);       
                    $payment_amount = substr($detail->payment_amount,0,12);       
                    $trace_number = substr($detail->trace_number,0,15);       
                    $web_transaction = substr($detail->web_transaction,0,1);       
                    // $addenda_info = substr($detail->addenda_info,0,80); 
                    $cmd = "
                            SELECT pt.name,sum(id.amount) as amt from invoice_details id
                            LEFT JOIN products p on id.product_id = p.id
                            LEFT JOIN product_categories pt on pt.id = p.product_category_id
                            WHERE id.invoice_id = {$detail->id}
                            group by pt.name";

                    
                      $rems =  DB::select(DB::raw($cmd));
                      $addenda = "";
                      foreach ($rems as $rem) {
                        $ptype = is_null($rem->name) ? 'None' : $rem->name; 
                        $addenda .=   $ptype.' '.$rem->amt.' ';
                      }
                      $addenda_info = substr($addenda,0,80); 

                    $content="{$parent_company_id},{$parent_company_name},{$company_id},{$company_name},{$sec_code},{$description},{$effective_date},{$individual_id},{$individual_name},{$routing_number},{$bank_account_number},{$transaction_code},{$payment_amount},{$trace_number},{$web_transaction},{$addenda_info}";  
                    $fp = fopen($url,"a+");
                    
                    fwrite($fp,"{$content}\r\n");
                    fclose($fp);  
                    $strIDs.=$detail->trace_number.",";      
                    $has_detail = true;
                    $parent_company_id = substr($detail->parent_company_id,0,10);       
                    $parent_company_name = substr($detail->parent_company_name,0,16);       
                    $company_id = substr($detail->company_id,0,10);       
                    $company_name = substr($detail->company_name,0,16);       
                    $sec_code = substr($detail->sec_code,0,3);       
                    $description = substr($detail->description,0,10);       
                    $effective_date = substr($detail->effective_date,0,6);       
                    $description = substr($detail->description,0,15);       
                    $individual_id = substr($detail->individual_id,0,15);       
                    $individual_name = substr($detail->individual_name,0,22);       
                    $routing_number = substr($detail->routing_number,0,9);       
                    $bank_account_number = substr($detail->bank_account_number,0,17);       
                    $transaction_code = substr($detail->transaction_code,0,2);       
                    $payment_amount = substr($detail->payment_amount,0,12);       
                    $trace_number = substr($detail->trace_number,0,15);       
                    $web_transaction = substr($detail->web_transaction,0,1);       
                    $addenda_info = substr($addenda,0,80); 
                }

                   
               
                $datetime=date('Y-m-d H:i:s'); 
                $filename_without_extension = str_replace('.csv','',$filename);   
                
                if ($has_detail) {
                    $ftp = Storage::createSftpDriver([
                        'host'     => $r->sftp_address,
                        'username' => $r->sftp_user,
                        'password' => $r->sftp_password,
                        'timeout'  => '180',
                    ]);
                    if (env('APP_ENV') == 'production') {
                        if (env('ACH_ENABLE_SENDING') == 1) {
                            $ftp->put('tranfile/'.$filename, fopen($url, 'r+'));
                        } else {
                            $ftp->put('tranfile/testfile/'.$filename, fopen($url, 'r+')); 
                        }
                    } else {
                        $ftp->put('tranfile/testfile/'.$filename, fopen($url, 'r+')); 
                    }
                    $has_file = true; 
                    
                    $strIDs = substr($strIDs,0,strlen($strIDs)-1);
                    $cmd  = "UPDATE invoice_headers SET ";
                    $cmd .=     "is_exported = 1, ";
                    $cmd .=     "export_date='{$datetime}',";
                    $cmd .=     "filename='{$filename_without_extension}' "; 
                    $cmd .= "WHERE id IN ({$strIDs})";

                    $records = DB::statement($cmd); 
                }

                
            }

            if ($has_file) {
                Log::info('Successfully created payment file');
            } else {
                Log::info('No file(s) to be sent');
            } 

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
