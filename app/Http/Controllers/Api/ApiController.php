<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Partner;
use App\Models\ACHConfiguration;

use DB;
use Carbon\Carbon;
use DateTime;
use File;
use Storage;

class ApiController extends Controller
{
    public function createPaymentCSV()
    {
        try{        
            $cmd ="select h.partner_id,h.id 
                    from invoice_headers h 
                    inner join partner_payment_infos p on p.partner_id = h.partner_id
                    inner join invoice_payments ip on ip.invoice_id = h.id
                    where h.status = 'S' and p.payment_type_id=1 and ip.payment_type_id=1 and IFNULL(is_exported,0)=0
                    union distinct
                    select h.partner_id,h.id  
                    from invoice_headers h 
                    inner join partner_payment_infos p on p.partner_id = h.partner_id
                    inner join invoice_payments ip on ip.invoice_id = h.id  and ip.payment_type_id = 1  
                    where h.due_date <= now() AND p.payment_type_id=1  and IFNULL(is_exported,0)=0";   

            $records = DB::select(DB::raw($cmd));  
            $results = array(); 
            foreach ($records as $r) {
                $company_id = Partner::get_upline_company($r->partner_id);
                $row = ACHConfiguration::where('partner_id',$company_id)->where('status','A')->first();
                if(isset($row)){
                    $cmd = " select '1002004141' as parent_company_id,'First Pay Solutions' as parent_company_name
                    ,'".$row->pay_token."' as company_id, '".$row->pay_to."' as company_name
                    ,'CCD' as sec_code, case when ifnull(ih.reference,'') = '' then 'No default value' else ih.reference end as description 
                    ,date_format(invoice_date,'%y%m%d') effective_date
                    ,ih.partner_id as individual_id
                    ,pc.dba as individual_name
                    ,ppi.bank_account_number,ppi.routing_number, 
                    '27' as transaction_code
                    ,ih.total_due as payment_amount,ih.id as trace_number
                    ,'' as web_transaction, ih.remarks as addenda_info
                    from invoice_headers ih
                    inner join invoice_payments ip on ip.invoice_id=ih.id
                    inner join partners p on p.id = ih.partner_id
                    inner join partner_companies pc on p.id = pc.partner_id
                    inner join partner_contacts pcon on p.id = pcon.partner_id and is_original_contact=1
                    inner join partners pp on pp.id = p.parent_id
                    inner join partner_companies ppc on pp.id = ppc.partner_id
                    inner join partner_payment_infos ppi on ppi.partner_id = ih.partner_id and ppi.payment_type_id = 1 and ppi.status = 'A'
                    where ih.id = ".$r->id;   
                    $customs = DB::select(DB::raw($cmd));       
                    $row->details = $customs;  
                    $results[] = $row;
                }
            }
            // dd($results);
            $content = "";
            $date_time=  date('Ymd') . "_" . time();
            $has_file = false;
            foreach ($results as $r){
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
                    $addenda_info = substr($detail->addenda_info,0,80); 
                    
                    $content="{$parent_company_id},{$parent_company_name},{$company_id},{$company_name},{$sec_code},{$description},{$effective_date},{$individual_id},{$individual_name},{$routing_number},{$bank_account_number},{$transaction_code},{$payment_amount},{$trace_number},{$web_transaction},{$addenda_info}";  

                    $fp = fopen($url,"a+");
                    fwrite($fp,"{$content}\r\n");
                    fclose($fp);  
                    $strIDs.=$detail->trace_number.",";      
                    $has_detail = true;
                }

                $strIDs = substr($strIDs,0,strlen($strIDs)-1);
                $datetime=date('Y-m-d H:i:s'); 
                $filename_without_extension = str_replace('.csv','',$filename);   
                if($has_detail){
                    $ftp = Storage::createFtpDriver([
                                                     'host'     => $r->sftp_address,
                                                     'username' => $r->sftp_user,
                                                     'password' => $r->sftp_password,
                                                     'timeout'  => '180',

                                                 ]); 
                    $ftp->put('test/'.$filename, $url); 
                    $has_file = true; 
                }
                $cmd = "update invoice_headers set is_exported=1,export_date='{$datetime}',filename='{$filename_without_extension}' where id in ({$strIDs})";
                $records = DB::statement($cmd);  

            }

            if ($has_file){
                $message = array(
                    'message' =>'Successfully created payment file',
                );
            } else {
                $message = array(
                    'message' =>'No file(s) to be sent',
                );    
            } 
            return $message; 

        } catch (\Exception $e) {
            return  array(
                    'message' =>$e->getMessage()
                );    
        }
        
    }


    public function downloadReturnFile()
    {
        try{
            $sftp_data = ACHConfiguration::where('partner_id',$company_id)->where('status','A')->get();
            $hasFile = false;
            foreach ($sftp_data as $r) {
                $sftp = Storage::createFtpDriver([
                                                 'host'     => $r->sftp_address,
                                                 'username' => $r->sftp_user,
                                                 'password' => $r->sftp_password,
                                                 'timeout'  => '180',

                                             ]); 
                $sftp->chdir('returns'); 
                $files = $sftp->nlist();
                foreach ($files as $file) {
                     $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                     if ($extension=="csv"){
                        $sftp->get($file, 'storage/ach_return_files/raw/'. $file);
                        $sftp->get($file, 'storage/ach_return_files/'. $file);
                        $sftp->delete($file);
                        $hasFile = true;
                     }
                }

            }

            if (!$hasFile) {
                return array(
                    'message' =>'No files in SFTP directory.'
                );         
            } else {
                return array(
                    'message' => 'File(s) successfully downloaded.'
                );              
            }
        } catch (\Exception $e) {
            return  array(
                    'message' =>$e->getMessage()
                );    
        }

    }


    public function countryZipList(){
        
        $getAllZipFromList = DB::table('us_zip_codes')
                            ->select('*')
                            ->get('*')
                            ->toArray();
        
        if($getAllZipFromList){

            return response()->json(array("system_message" => "success", $getAllZipFromList), 200);

        }
        else{

            return response()->json(array("system_message" => "no_data_found"));

        }
        
    }


}

