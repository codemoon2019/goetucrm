<?php

namespace App\Console\Commands;

use App\Models\InvoiceHeader;
use App\Models\Partner;
use App\Models\PartnerPaymentInfo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateRefundPaymentCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:createRefundPaymentCSV';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create refund payment';

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
        Log::info('Start Check Refund Payment For CSV');
        $data = $this->getRefundPaymentsForProcess();

        $content = "";
        $datetime = date('Ymd') . "_" . time();
        $hasFile = false;

        foreach ($data as $datum) {
            $content = ""; 
            $strIDs = ""; 
            $filename = substr($datum->pay_to,0,3) . "_{$datetime}_refund_fps.csv";

            foreach ($datum->details as $detail) {
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
                $addenda_info = substr($detail->addenda_info,0,80); 
                
                $content = "{$parent_company_id},{$parent_company_name},{$company_id},{$company_name},{$sec_code},{$description},{$effective_date},{$individual_id},{$individual_name},{$routing_number},{$bank_account_number},{$transaction_code},{$payment_amount},{$trace_number},{$web_transaction},{$addenda_info}";

                Storage::disk('upload')->put($filename, $content);
                Log::info(
                    "Created a file on app/public/uploads/$filename"
                );  

                $strIDs .= $row->trace_number.",";
            }

            if ($strIDs != "") {
                /** Raw DB */
                $strIDs = substr($strIDs,0,strlen($strIDs)-1);
                $datetime = date('Y-m-d H:i:s'); 
                $cmd = "UPDATE invoice_headers SET 
                            is_exported = 1, 
                            export_date='{$datetime}' 
                        WHERE id IN ({$strIDs})";
                DB::statement($cmd); 
                $hasFile = true;  

                /** Eloquent */
                /** */
            }
        }                      

        if (!$hasFile) {
            Log::info('No file(s) to be sent');
        }

        Log::info('End Check Refund Payment For CSV');
    }

    public function getRefundPaymentsForProcess()
    {
        /** Raw DB */
        $cmd = "SELECT h.partner_id, h.id
                FROM invoice_headers h 
                INNER JOIN partner_payment_infos p 
                    ON p.partner_id = h.partner_id
                INNER JOIN invoice_payments ip 
                    ON ip.invoice_id = h.id
                WHERE h.status = 'X' 
                    AND p.payment_type_id = 1 
                    AND ip.payment_type_id = 1 
                    AND is_exported = 0
                UNION DISTINCT
                SELECT h.partner_id,h.id  
                FROM invoice_headers h 
                INNER JOIN partner_payment_infos p 
                    ON p.partner_id = h.partner_id
                INNER JOIN invoice_payments ip 
                    ON ip.invoice_id = h.id 
                    AND ip.payment_type_id = 1  
                WHERE h.due_date <= NOW() 
                    AND p.payment_type_id=1 
                    AND is_exported = 0 
                    AND h.status='X'";
        
        $invoiceHeaders = DB::select(DB::raw($cmd));


        /** Eloquent */
        /* $invoiceHeaders1 = InvoiceHeader::select('id', 'partner_id')
            ->whereHas('partner.partnerPaymentInfos', function ($query) {
                $query->where('payment_type_id', 1);
            })->whereHas('payment', function($query) {
                $query->where('payment_type_id', 1);
            })->where([
                ['status', 'X'],
                ['is_exported', 0]
            ])->get();

        $invoiceHeaders2 = InvoiceHeader::select('id', 'partner_id')
            ->whereHas('partner.partnerPaymentInfos', function ($query) {
                $query->where('payment_type_id', 1);
            })->whereHas('payment', function($query) {
                $query->where('payment_type_id', 1);
            })->where([
                ['status', 'X'],
                ['is_exported', 0],
                ['due_date', '<=', Carbon::now()]
            ])->get();

        $invoiceHeaders = $invoiceHeaders1->union($invoiceHeaders2)->all(); */

        Log::info($invoiceHeaders);

        foreach ($invoiceHeaders as $invoiceHeader) {
            $companyId = Partner::get_top_upline_partner($invoiceHeader->partner_id);

            /** Raw DB */
            /* $cmd = "SELECT sftp_address, sftp_user, 
                        sftp_password, pay_to, pay_token 
                    FROM partner_payment_info 
                    WHERE partner_id={$companyId}
                        AND payment_type_id = 1 
                        AND status ='A'";
            
            $stfpInformation = DB::select(DB::raw($cmd)); */

            /** Eloquent */
            $sftpInformation = PartnerPaymentInfo::select('sftp_address', 
                'sftp_user', 'sftp_password', 'pay_to', 'pay_token')
                ->where([
                    ['partner_id', $companyId],
                    ['payment_type_id', 1],
                    ['status', 'A'],
                ])->first();

            $results = array();

            if (isset($sftpInformation)) {
                if ($sftpInformation->sftp_address != "" && $sftpInformation->sftp_user != "" && 
                    $sftpInformation->sftp_password != "" && $sftpInformation->pay_to != "" && 
                    $sftpInformation->pay_token != "") {

                    /** Raw DB */
                    $cmd = "SELECT '1002004141' AS parent_company_id, 
                                'First Pay Solutions' AS parent_company_name,
                                '{$sftpInformation->pay_token}' AS company_id, 
                                '{$sftpInformation->pay_to}' AS company_name,
                                'CCD' AS sec_code, 
                                CASE 
                                    WHEN IFNULL(ih.reference,'') = '' 
                                    THEN 'No default value' 
                                    ELSE ih.reference 
                                END AS description,
                                DATE_FORMAT(invoice_date,'%y%m%d') effective_date,
                                ih.partner_id AS individual_id,
                                pc.dba AS individual_name,
                                ppi.bank_account_number,ppi.routing_number, 
                                '22' AS transaction_code,
                                ih.total_due AS payment_amount,ih.id AS trace_number,
                                '' AS web_transaction, ih.remarks AS addenda_info
                            FROM invoice_headers ih
                            INNER JOIN invoice_payments ip 
                                ON ip.invoice_id=ih.id
                            INNER JOIN partners p 
                                ON p.id = ih.partner_id
                            INNER JOIN partner_companies pc 
                                ON p.id = pc.partner_id
                            INNER JOIN partner_contacts pcon 
                                ON p.id = pcon.partner_id 
                                AND is_original_contact=1
                            INNER JOIN partners pp 
                                ON pp.id = p.parent_id
                            INNER JOIN partner_companies ppc 
                                ON pp.id = ppc.partner_id
                            INNER JOIN partner_payment_infos ppi 
                                ON ppi.partner_id = ih.partner_id 
                                AND ppi.payment_type_id = 1 
                                AND ppi.status = 'A'
                            WHERE ih.id = {$invoiceHeader->id}";
                    
                    $sftpInformation->details = DB::select(DB::raw($cmd));

                    /** Eloquent */
                    /** */
                }
            }

            $results[] = $sftpInformation;
        }

        return $results;
    }
}
