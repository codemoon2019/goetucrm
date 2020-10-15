<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoicePayment;
use App\Models\InvoiceFrequency;
use App\Models\Product;
use App\Models\Partner;
use App\Models\PartnerType;
use App\Models\PartnerProduct;
use Excel;
use Cache;
use Carbon\Carbon;
use DB;
use App\Models\ReportExportLog;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:billing,view')->only('index');
    }

    public function index(){
        return view("reports.list");
    }

    public function report_detail(){
        return view("reports.report_detail");
    }
    public function report_payout() {
        return view("reports.report_payout");
    }

    public function report_summary(){
        return view("reports.report_summary");
    }

    public function comm_report(){
        return view("reports.commission_report");
    }

    public function report_ccsr(){
        return view("reports.report_ccsr");
    }

    public function report_cscr(){
        return view("reports.report_cscr");
    }

    public function report_itl(){
        return view("reports.report_itl");
    }
    public function report_mcr(){
        return view("reports.report_mcr");
    }

    public function report_pl(){
        return view("reports.report_pl");
    }

    public function report_new_partner() {
        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'new partners report') === false) ? false : true;
        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }      

        $pt_access = "";
        $pt_access .= isset($access['company']) ? "7," : "";
        $pt_access .= isset($access['iso']) ? "4," : "";
        $pt_access .= isset($access['sub iso']) ? "5," : "";
        $pt_access .= isset($access['agent']) ? "1," : "";
        $pt_access .= isset($access['sub agent']) ? "2," : "";
        $pt_access = ($pt_access == "") ? "" : substr($pt_access, 0, strlen($pt_access) - 1); 

        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;

            $partnerTypes = PartnerType::whereRaw("id in ({$pt_access})")->get();
            foreach ($partnerTypes as $pt) {
                $pt->list = Partner::where('status','A')->where("partner_type_id",$pt->id)->whereRaw("id in ({$partner_access})")->get();
            }
        }else{
            $partner_access="";
            $partnerTypes = PartnerType::whereRaw("id in ({$pt_access})")->get();
            foreach ($partnerTypes as $pt) {
                $pt->list = Partner::where('status','A')->where("partner_type_id",$pt->id)->get();
            }
        }

        $init = 1;
        return view("reports.report_new_partner",compact('init','partnerTypes'));
    }

    public function generate_report_new_partner($partnerId,$type,$from,$to,$export) {
        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'new partners report') === false) ? false : true;
        $report_export = (strpos($reportaccess, 'new partners export') === false) ? false : true;

        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        } 

        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
        }else{
            $partner_access="";
        }

        $parents = Partner::get_partners_access($partnerId); 
        $parents = $parents == "" ? $partnerId : $parents;

        $query = $partner_access == "" ? "parent_id <> -1" :"id in ({$partner_access})";
        $query2 = $partnerId == -1 ? "1=1" :"parent_id in({$parents})";
        if($partnerId == -1){
            $query3 = $partner_access == "" ? "" : " ih.partner_id in({$partner_access}) and ";
        }else{
            $query3 = " ih.partner_id in({$parents}) and ";
        }
        
        $cmd = "select p_main.id,p_main.name,sum(id.amount) as total from invoice_headers ih
                inner join invoice_details id on ih.id = id.invoice_id
                inner join products p on p.id = id.product_id
                inner join products p_main on p.parent_id = p_main.id
                where {$query3} ih.status = 'P'
                group by p_main.id,p_main.name order by total desc"; 

        $productSales = DB::select(DB::raw($cmd));

        if($type == 'Daily'){

            foreach($productSales as $sales){

                $cmd = "select ifnull(sum(id.amount),0) as total from invoice_headers ih
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join products p on p.id = id.product_id
                        inner join products p_main on p.parent_id = p_main.id
                        where {$query3} ih.status = 'P' and DATE_FORMAT(ih.invoice_date,'%Y-%m-%d') = '{$from}' and p_main.id = {$sales->id}";      

                $result = collect(DB::select(DB::raw($cmd)))->first();
                if(isset($result)){
                    $sales->totalFiltered = $result->total;
                }else{
                    $sales->totalFiltered = '0.00';
                }
                                   
            }

        }

        if($type == 'Weekly' || $type == 'Custom'){

            $fromDt = new Carbon($from);
            $toDt = new Carbon($to);
            $toDt = $toDt->addDay();

            foreach($productSales as $sales){

                $cmd = "select ifnull(sum(id.amount),0) as total from invoice_headers ih
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join products p on p.id = id.product_id
                        inner join products p_main on p.parent_id = p_main.id
                        where {$query3} ih.status = 'P' and ih.invoice_date >= '{$fromDt->toDateString()}' and ih.invoice_date < '{$toDt->toDateString()}'  and p_main.id = {$sales->id}";      

                $result = collect(DB::select(DB::raw($cmd)))->first();
                if(isset($result)){
                    $sales->totalFiltered = $result->total;
                }else{
                    $sales->totalFiltered = '0.00';
                }
                                   
            }
        }

        if($type == 'Monthly'){

            foreach($productSales as $sales){

                $cmd = "select ifnull(sum(id.amount),0) as total from invoice_headers ih
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join products p on p.id = id.product_id
                        inner join products p_main on p.parent_id = p_main.id
                        where {$query3} ih.status = 'P' and DATE_FORMAT(ih.invoice_date,'%Y-%m') = '{$from}' and p_main.id = {$sales->id}";      

                $result = collect(DB::select(DB::raw($cmd)))->first();
                if(isset($result)){
                    $sales->totalFiltered = $result->total;
                }else{
                    $sales->totalFiltered = '0.00';
                }
                                   
            }

        }

        if($type == 'Yearly'){

            foreach($productSales as $sales){

                $cmd = "select ifnull(sum(id.amount),0) as total from invoice_headers ih
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join products p on p.id = id.product_id
                        inner join products p_main on p.parent_id = p_main.id
                        where {$query3} ih.status = 'P' and DATE_FORMAT(ih.invoice_date,'%Y') = '{$from}' and p_main.id = {$sales->id}";      

                $result = collect(DB::select(DB::raw($cmd)))->first();
                if(isset($result)){
                    $sales->totalFiltered = $result->total;
                }else{
                    $sales->totalFiltered = '0.00';
                }
                                   
            }

        }

        $data = Array(
            'productSales' => $productSales,
        );

        if($export == 'true'){
            if(!$report_export){
                return redirect('/')->with('failed', 'You have no access to that page.');
            } 
        }else{

            $pt_access = "";
            $pt_access .= isset($access['company']) ? "7," : "";
            $pt_access .= isset($access['iso']) ? "4," : "";
            $pt_access .= isset($access['sub iso']) ? "5," : "";
            $pt_access .= isset($access['agent']) ? "1," : "";
            $pt_access .= isset($access['sub agent']) ? "2," : "";
            $pt_access = ($pt_access == "") ? "" : substr($pt_access, 0, strlen($pt_access) - 1); 

            $admin_access = isset($access['admin']) ? $access['admin'] : "";
            if (strpos($admin_access, 'super admin access') === false){
                $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
                $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;

                $partnerTypes = PartnerType::whereRaw("id in ({$pt_access})")->get();
                foreach ($partnerTypes as $pt) {
                    $pt->list = Partner::where('status','A')->where("partner_type_id",$pt->id)->whereRaw("id in ({$partner_access})")->get();
                }
            }else{
                $partner_access="";
                $partnerTypes = PartnerType::whereRaw("id in ({$pt_access})")->get();
                foreach ($partnerTypes as $pt) {
                    $pt->list = Partner::where('status','A')->where("partner_type_id",$pt->id)->get();
                }
            }

            return view("reports.report_new_partner",compact('type','from','to','data','report_export','partnerTypes','partnerId'));            
        }

    }


    public function new_partner_graph_data($partnerId,$type,$from,$to) {

        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'new partners report') === false) ? false : true;
        $report_export = (strpos($reportaccess, 'new partners export') === false) ? false : true;

        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        } 

        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
        }else{
            $partner_access="";
        }

        $parents = Partner::get_partners_access($partnerId); 
        $parents = $parents == "" ? $partnerId : $parents;

        $query = $partner_access == "" ? "parent_id <> -1" :"id in ({$partner_access})";
        $query2 = $partnerId == -1 ? "1=1" :"parent_id in({$parents})";

        if($type == 'Daily'){

            $leadProspect = Partner::whereIn('partner_type_id',Array(6,8))->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '{$from}'")->whereRaw($query)->whereRaw($query2)->get();
            $merchants = Partner::whereIn('partner_type_id',Array(3))->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '{$from}'")->whereRaw($query)->whereRaw($query2)->get();

        }

        if($type == 'Weekly' || $type == 'Custom'){

            $fromDt = new Carbon($from);
            $toDt = new Carbon($to);
            $toDt = $toDt->addDay();

            $leadProspect = Partner::whereIn('partner_type_id',Array(6,8))->where("created_at",'>=',$fromDt->toDateString())->where("created_at",'<',$toDt->toDateString())->whereRaw($query)->whereRaw($query2)->get();
            $merchants = Partner::whereIn('partner_type_id',Array(3))->where("created_at",'>=',$fromDt->toDateString())->where("created_at",'<',$toDt->toDateString())->whereRaw($query)->whereRaw($query2)->get();
        }

        if($type == 'Monthly'){

            $leadProspect = Partner::whereIn('partner_type_id',Array(6,8))->whereRaw("DATE_FORMAT(created_at,'%Y-%m') = '{$from}'")->whereRaw($query)->whereRaw($query2)->get();
            $merchants = Partner::whereIn('partner_type_id',Array(3))->whereRaw("DATE_FORMAT(created_at,'%Y-%m') = '{$from}'")->whereRaw($query)->whereRaw($query2)->get();

        }

        if($type == 'Yearly'){

            $leadProspect = Partner::whereIn('partner_type_id',Array(6,8))->whereRaw("DATE_FORMAT(created_at,'%Y') = '{$from}'")->whereRaw($query)->whereRaw($query2)->get();
            $merchants = Partner::whereIn('partner_type_id',Array(3))->whereRaw("DATE_FORMAT(created_at,'%Y') = '{$from}'")->whereRaw($query)->whereRaw($query2)->get();

        }

        $leadCountTotal = Partner::whereIn('partner_type_id',Array(6))->whereRaw($query)->whereRaw($query2)->count();      
        $prospectCountTotal = Partner::whereIn('partner_type_id',Array(8))->whereRaw($query)->whereRaw($query2)->count(); 
        $leadCount = $leadProspect->where('partner_type_id',6)->count();   
        $prospectCount = $leadProspect->where('partner_type_id',8)->count();

        $merchantBoardingTotal = Partner::whereIn('partner_type_id',Array(3))->whereIn('merchant_status_id',Array(1,2))->whereRaw($query)->whereRaw($query2)->count(); 
        $merchantBoardedTotal = Partner::whereIn('partner_type_id',Array(3))->whereIn('merchant_status_id',Array(3,4))->where('status','<>','T')->whereRaw($query)->whereRaw($query2)->count(); 
        $merchantCancelledTotal = Partner::whereIn('partner_type_id',Array(3))->whereIn('merchant_status_id',Array(5,6))->whereRaw($query)->whereRaw($query2)->count();
        $merchantTerminatedTotal = Partner::whereIn('partner_type_id',Array(3))->where('status','T')->whereRaw($query)->whereRaw($query2)->count(); 

        $merchantBoarding = $merchants->whereIn('merchant_status_id',Array(1,2))->count();  
        $merchantBoarded = $merchants->whereIn('merchant_status_id',Array(3,4))->where('status','<>','T')->count();
        $merchantCancelled = $merchants->whereIn('merchant_status_id',Array(5,6))->count();
        $merchantTerminated = $merchants->where('status','T')->count(); 

        $data = Array(
                Array('name'=> 'Leads', 'count' => $leadCount , 'total' => $leadCountTotal),
                Array('name'=> 'Prospect', 'count' => $prospectCount , 'total' => $prospectCountTotal),
                Array('name'=> 'Merchant Boarding', 'count' => $merchantBoarding , 'total' => $merchantBoardingTotal),
                Array('name'=> 'Merchant Boarded', 'count' => $merchantBoarded , 'total' => $merchantBoardedTotal),
                Array('name'=> 'Merchant Cancelled', 'count' => $merchantCancelled , 'total' => $merchantCancelledTotal),
                Array('name'=> 'Merchant Terminated', 'count' => $merchantTerminated , 'total' => $merchantTerminatedTotal),
        );


       return Array('data' => $data); 

    }


    public function new_partner_data($partnerId,$type,$from,$to,$partnerType,$status) {
        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'new partners report') === false) ? false : true;
        $report_export = (strpos($reportaccess, 'new partners export') === false) ? false : true;

        if(!$report_generate){
            return 'No Access';
        } 

        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
        }else{
            $partner_access="";
        }

        $parents = Partner::get_partners_access($partnerId); 
        $parents = $parents == "" ? $partnerId : $parents;

        $query = $partner_access == "" ? "parent_id <> -1" :"id in ({$partner_access})";
        $query2 = $partnerId == -1 ? "1=1" :"parent_id in({$parents})";

        switch($partnerType){
            case 'leads': 
                $pt_id = 6; 
                $url = '<a href="/leads/details/profile/';
                break;
            case 'prospects': 
                $pt_id = 8; 
                $url = '<a href="/prospects/details/profile/';
                break;
            case 'merchants': 
                $pt_id = 3; 
                $url = '<a href="/merchants/details/';
                break;
            default: return 'Invalid Partner Type'; break;

        }

        if($pt_id == 3){
            switch($status){
                case 'P': $query3 = "merchant_status_id in (1,2)"; break;
                case 'A': $query3 = "merchant_status_id in (3,4) and status <> 'T'"; break;
                case 'C': $query3 = "merchant_status_id in (5,6)"; break;
                case 'T': $query3 = "status = 'T'"; break;
                default:  'Invalid Status'; break;
            }

        }else{
             $query3 = "1=1";
        }


        if($from == 'all'){
            $data = Partner::whereIn('partner_type_id',Array($pt_id))->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
        }else{
            switch($type){
                case 'Weekly':
                case 'Custom':
                    $fromDt = new Carbon($from);
                    $toDt = new Carbon($to);
                    $toDt = $toDt->addDay();
                    $data = Partner::whereIn('partner_type_id',Array($pt_id))->where("created_at",'>=',$fromDt->toDateString())->where("created_at",'<',$toDt->toDateString())->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
                    break;
                case 'Daily':
                    $data = Partner::whereIn('partner_type_id',Array($pt_id))->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '{$from}'")->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
                    break;
                case 'Monthly':
                    $data = Partner::whereIn('partner_type_id',Array($pt_id))->whereRaw("DATE_FORMAT(created_at,'%Y-%m') = '{$from}'")->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
                    break;
                case 'Yearly':
                    $data = Partner::whereIn('partner_type_id',Array($pt_id))->whereRaw("DATE_FORMAT(created_at,'%Y') = '{$from}'")->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
                    break;
                default:
                    return 'Invalid Type';
                    break;
            }     
        }

     
        $out = array();

        if($pt_id == 3){
            foreach ($data as $d) {

                $statusNew = $d->status == 'T' ? '<td style="text-align: left">Terminated</td>' : '<td style="text-align: left">'.$d->merchantStatus->description.'</td>';
                $out[] = array(
                    $statusNew,
                    $url.$d->id.'/profile">'.$d->partner_id_reference.'</a>',
                    isset($d->partner_company) ? $d->partner_company->company_name : '',
                    isset($d->partner_contact()->first_name) ? $d->partner_contact()->first_name.' '.$d->partner_contact()->middle_name.' '.$d->partner_contact()->last_name : '',
                    isset($d->partner_company) ? $d->partner_company->country_code.$d->partner_company->phone1 : '',
                    isset($d->partner_contact()->first_name) ? $d->partner_contact()->country_code.$d->partner_contact()->mobile_number : '',
                    isset($d->partner_company) ? $d->partner_company->address1 : '',
                    date_format($d->created_at,"Y/m/d H:i:s")
                ); 
            }
        }else{
            foreach ($data as $d) {
                $out[] = array(
                    $d->creator->first_name.' '.$d->creator->last_name,
                    $url.$d->id.'">'.$d->partner_id_reference.'</a>',
                    isset($d->partner_company) ? $d->partner_company->company_name : '',
                    isset($d->partner_contact()->first_name) ? $d->partner_contact()->first_name.' '.$d->partner_contact()->middle_name.' '.$d->partner_contact()->last_name : '',
                    isset($d->partner_company) ? $d->partner_company->country_code.$d->partner_company->phone1 : '',
                    isset($d->partner_contact()->first_name) ? $d->partner_contact()->country_code.$d->partner_contact()->mobile_number : '',
                    isset($d->partner_company) ? $d->partner_company->address1 : '',
                    date_format($d->created_at,"Y/m/d H:i:s")
                ); 
            }            
        }
        return $out;
    }



    public function new_partner_data_export($partnerId,$type,$from,$to,$partnerType,$status) {
        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'new partners report') === false) ? false : true;
        $report_export = (strpos($reportaccess, 'new partners export') === false) ? false : true;

        if(!$report_generate){
            return 'No Access';
        } 

        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
        }else{
            $partner_access="";
        }

        $parents = Partner::get_partners_access($partnerId); 
        $parents = $parents == "" ? $partnerId : $parents;

        $query = $partner_access == "" ? "parent_id <> -1" :"id in ({$partner_access})";
        $query2 = $partnerId == -1 ? "1=1" :"parent_id in({$parents})";

        switch($partnerType){
            case 'leads': 
                $pt_id = 6; 
                $url = '<a href="/leads/details/profile/';
                break;
            case 'prospects': 
                $pt_id = 8; 
                $url = '<a href="/prospects/details/profile/';
                break;
            case 'merchants': 
                $pt_id = 3; 
                $url = '<a href="/merchants/details/';
                break;
            default: return 'Invalid Partner Type'; break;

        }

        if($pt_id == 3){
            switch($status){
                case 'P': $query3 = "merchant_status_id in (1,2)"; break;
                case 'A': $query3 = "merchant_status_id in (3,4) and status <> 'T'"; break;
                case 'C': $query3 = "merchant_status_id in (5,6)"; break;
                case 'T': $query3 = "status = 'T'"; break;
                default:  'Invalid Status'; break;
            }

        }else{
             $query3 = "1=1";
        }


        if($from == 'all'){
            $data = Partner::whereIn('partner_type_id',Array($pt_id))->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
        }else{
            switch($type){
                case 'Weekly':
                case 'Custom':
                    $fromDt = new Carbon($from);
                    $toDt = new Carbon($to);
                    $toDt = $toDt->addDay();
                    $data = Partner::whereIn('partner_type_id',Array($pt_id))->where("created_at",'>=',$fromDt->toDateString())->where("created_at",'<',$toDt->toDateString())->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
                    break;
                case 'Daily':
                    $data = Partner::whereIn('partner_type_id',Array($pt_id))->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '{$from}'")->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
                    break;
                case 'Monthly':
                    $data = Partner::whereIn('partner_type_id',Array($pt_id))->whereRaw("DATE_FORMAT(created_at,'%Y-%m') = '{$from}'")->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
                    break;
                case 'Yearly':
                    $data = Partner::whereIn('partner_type_id',Array($pt_id))->whereRaw("DATE_FORMAT(created_at,'%Y') = '{$from}'")->whereRaw($query)->whereRaw($query2)->whereRaw($query3)->get();
                    break;
                default:
                    return 'Invalid Type';
                    break;
            }     
        }

     
        $out = array();

        if($pt_id == 3){

            Excel::create('New_Partners_Report', function($excel) use($data){
                $excel->sheet('New_Partners_Report', function($sheet) use($data){
                    
                    $header = array('Status','ID','Company Name','Contact','Business Phone','Mobile #','Business Address','Date Created');
                    $sheet->row(1,$header);
                    $row=2;
                    foreach($data as $d){
                        $statusNew = $d->status == 'T' ? 'Terminated' : $d->merchantStatus->description;
                        $p = array(
                            $statusNew,
                            $d->partner_id_reference,
                            isset($d->partner_company) ? $d->partner_company->company_name : '',
                            isset($d->partner_contact()->first_name) ? $d->partner_contact()->first_name.' '.$d->partner_contact()->middle_name.' '.$d->partner_contact()->last_name : '',
                            isset($d->partner_company) ? $d->partner_company->country_code.$d->partner_company->phone1 : '',
                            isset($d->partner_contact()->first_name) ? $d->partner_contact()->country_code.$d->partner_contact()->mobile_number : '',
                            isset($d->partner_company) ? $d->partner_company->address1 : '',
                            date_format($d->created_at,"Y/m/d H:i:s")
                        ); 
                        $sheet->row($row++,$p);
                    }

                });
            })->export('xls');


        }else{

            Excel::create('New_Partners_Report', function($excel) use($data){
                $excel->sheet('New_Partners_Report', function($sheet) use($data){
                    
                    $header = array('Source','ID','Company Name','Contact','Business Phone','Mobile #','Business Address','Date Created');
                    $sheet->row(1,$header);
                    $row=2;
                    foreach($data as $d){
                        $p = array(
                            $d->creator->first_name.' '.$d->creator->last_name,
                            $d->partner_id_reference,
                            isset($d->partner_company) ? $d->partner_company->company_name : '',
                            isset($d->partner_contact()->first_name) ? $d->partner_contact()->first_name.' '.$d->partner_contact()->middle_name.' '.$d->partner_contact()->last_name : '',
                            isset($d->partner_company) ? $d->partner_company->country_code.$d->partner_company->phone1 : '',
                            isset($d->partner_contact()->first_name) ? $d->partner_contact()->country_code.$d->partner_contact()->mobile_number : '',
                            isset($d->partner_company) ? $d->partner_company->address1 : '',
                            date_format($d->created_at,"Y/m/d H:i:s")
                        ); 
                        $sheet->row($row++,$p);
                    }

                });
            })->export('xls');

        }

    }

    public function new_partner_product_data($productId,$partnerId,$type,$from,$to,Request $request){

        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'new partners report') === false) ? false : true;

        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        } 

        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
        }else{
            $partner_access="";
        }

        $parents = Partner::get_partners_access($partnerId); 
        $parents = $parents == "" ? $partnerId : $parents;

        if($partnerId == -1){
            $query = $partner_access == "" ? "  p_main.id = {$productId} and" : " ih.partner_id in({$partner_access}) and p_main.id = {$productId} and ";
        }else{
            $query = " ih.partner_id in({$parents}) and p_main.id = {$productId} and ";
        }

        if($type == 'Daily'){

            $cmd = "select p.id,p.name,sum(id.amount) as value from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    inner join products p on p.id = id.product_id
                    inner join products p_main on p.parent_id = p_main.id
                    where {$query} ih.status = 'P' and DATE_FORMAT(ih.invoice_date,'%Y-%m-%d') = '{$from}' 
                    group by p.id,p.name order by value desc"; 

            $productSales = DB::select(DB::raw($cmd));

        }

        if($type == 'Weekly' || $type == 'Custom'){

            $fromDt = new Carbon($from);
            $toDt = new Carbon($to);
            $toDt = $toDt->addDay();

            $cmd = "select p.id,p.name,sum(id.amount) as value from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    inner join products p on p.id = id.product_id
                    inner join products p_main on p.parent_id = p_main.id
                    where {$query} ih.status = 'P' and ih.invoice_date >= '{$fromDt->toDateString()}' and ih.invoice_date < '{$toDt->toDateString()}' 
                    group by p.id,p.name order by value desc"; 

            $productSales = DB::select(DB::raw($cmd));

        }

        if($type == 'Monthly'){

            $cmd = "select p.id,p.name,sum(id.amount) as value from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    inner join products p on p.id = id.product_id
                    inner join products p_main on p.parent_id = p_main.id
                    where {$query} ih.status = 'P' and DATE_FORMAT(ih.invoice_date,'%Y-%m') = '{$from}'
                    group by p.id,p.name order by value desc"; 

            $productSales = DB::select(DB::raw($cmd));

        }

        if($type == 'Yearly'){

            $cmd = "select p.id,p.name,sum(id.amount) as value from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    inner join products p on p.id = id.product_id
                    inner join products p_main on p.parent_id = p_main.id
                    where {$query} ih.status = 'P' and DATE_FORMAT(ih.invoice_date,'%Y') = '{$from}'
                    group by p.id,p.name order by value desc"; 

            $productSales = DB::select(DB::raw($cmd));

        }

        return Array('data' => $productSales); 
    }


    public function report_new_business() {
        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'new business report') === false) ? false : true;
        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }         
        $init = 1;
        return view("reports.report_new_business",compact('init'));
    }

    public function generate_report_new_business($type,$from,$to,$export) {
        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'new business report') === false) ? false : true;
        $report_export = (strpos($reportaccess, 'new business export') === false) ? false : true;
        // $report_generate = true;
        // $report_export = true;

        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        } 

        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
        }else{
            $partner_access="";
        }


        if($type == 'Daily'){
            $partners = $partner_access == "" ? Partner::where('status','<>','D')->whereIn('partner_type_id',Array(3))->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '{$from}'")->orderBy('created_at','desc')->get() : Partner::whereIn('partner_type_id',Array(3))->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '{$from}'")->whereRaw("id in ({$partner_access})")->orderBy('created_at','desc')->get();            
        }

        if($type == 'Weekly' || $type == 'Custom'){
            $fromDt = new Carbon($from);
            $toDt = new Carbon($to);
            $toDt = $toDt->addDay();
            $partners = $partner_access == "" ? Partner::where('status','<>','D')->whereIn('partner_type_id',Array(3))->where("created_at",'>=',$fromDt->toDateString())->where("created_at",'<',$toDt->toDateString())->orderBy('created_at','desc')->get() : Partner::whereIn('partner_type_id',Array(3))->where("created_at",'>=',$fromDt->toDateString())->where("created_at",'<',$toDt->toDateString())->whereRaw("id in ({$partner_access})")->orderBy('created_at','desc')->get();           
        }

        if($type == 'Monthly'){
            $partners = $partner_access == "" ? Partner::where('status','<>','D')->whereIn('partner_type_id',Array(3))->whereRaw("DATE_FORMAT(created_at,'%Y-%m') = '{$from}'")->orderBy('created_at','desc')->get() : Partner::whereIn('partner_type_id',Array(3))->whereRaw("DATE_FORMAT(created_at,'%Y-%m') = '{$from}'")->whereRaw("id in ({$partner_access})")->orderBy('created_at','desc')->get();            
        }

        if($type == 'Yearly'){
            $partners = $partner_access == "" ? Partner::where('status','<>','D')->whereIn('partner_type_id',Array(3))->whereRaw("DATE_FORMAT(created_at,'%Y') = '{$from}'")->orderBy('created_at','desc')->get() : Partner::whereIn('partner_type_id',Array(3))->whereRaw("DATE_FORMAT(created_at,'%Y') = '{$from}'")->whereRaw("id in ({$partner_access})")->orderBy('created_at','desc')->get();            
        }

        if($export == 'true'){
            if(!$report_export){
                return redirect('/')->with('failed', 'You have no access to that page.');
            } 
            Excel::create('New_Business_Report', function($excel) use($partners){
                $excel->sheet('New_Business_Report', function($sheet) use($partners){
                    
                    $header = array('Merchant Name','Contact Person','Mobile #','Email Address','Status','Date Created');
                    $sheet->row(1,$header);
                    $row=2;
                    foreach($partners as $partner){
                        switch ($partner->status) {
                                case 'A':
                                    $status = 'Active';
                                    break;
                                case 'I':
                                    $status = 'Inactive';
                                    break; 
                                case 'T':
                                    $status = 'Terminated';
                                    break;
                                case 'V':
                                    $status = 'Cancelled';
                                    break;
                                case 'P':
                                    $status = 'For Boarding';
                                    break;
                                case 'C':
                                    $status = 'For Approval';
                                    break;

                                default:
                                    $status = '';
                                    break;
                        }
                        $p = Array($partner->partner_company->company_name,
                            $partner->partner_contact()->first_name.' '.$partner->partner_contact()->middle_name.' '.$partner->partner_contact()->last_name,
                            $partner->partner_contact()->country_code.$partner->partner_contact()->mobile_number,
                            $partner->partner_company->email,
                            $status,
                            date_format($partner->created_at,"Y/m/d H:i:s")
                        );
                        $sheet->row($row++,$p);
                    }
                });
            })->export('xls');
        }else{
            return view("reports.report_new_business",compact('type','from','to','partners','report_export'));            
        }

    }

    public function report_product(){

        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'product report') === false) ? false : true;
        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        } 
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
            $merchants = Partner::where('status','A')->whereIn('partner_type_id',Array(3))->whereRaw("id in ({$partner_access})")->orderBy('created_at','desc')->get();
        }else{
            $partner_access="";
            $merchants = Partner::where('status','A')->whereIn('partner_type_id',Array(3))->orderBy('created_at','desc')->get();
        }

        $init = 1;
        return view("reports.report_product",compact('merchants','init'));
    }

    public function generate_report_product($id,$type,$from,$to,$export) {
        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'product report') === false) ? false : true;
        $report_export = (strpos($reportaccess, 'product export') === false) ? false : true;
        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        } 
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $addQry = " and ih.partner_id = {$id} ";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
            $merchants = Partner::where('status','A')->whereIn('partner_type_id',Array(3))->whereRaw("id in ({$partner_access})")->orderBy('created_at','desc')->get();
            if($id == -1){
                $addQry = "and ih.partner_id in({$partner_access}) ";
            }
        }else{
            $partner_access="";
            $merchants = Partner::where('status','A')->whereIn('partner_type_id',Array(3))->orderBy('created_at','desc')->get();
            if($id == -1){
                $addQry = " ";
            }
        }

        $dtQry = " ";
        if($type == 'Daily'){
            $dtQry .= " and DATE_FORMAT(ih.created_at,'%Y-%m-%d') = '{$from}'" ;         
        }

        if($type == 'Weekly' || $type == 'Custom'){
            $fromDt = new Carbon($from);
            $toDt = new Carbon($to);
            $toDt = $toDt->addDay();
            $dtQry .= " and ih.created_at >= '".$fromDt->toDateString()."' and ih.created_at < '".$toDt->toDateString()."' ";         
        }

        if($type == 'Monthly'){  
            $dtQry .= " and DATE_FORMAT(ih.created_at,'%Y-%m') = '{$from}'" ;           
        }

        if($type == 'Yearly'){
            $dtQry .= " and DATE_FORMAT(ih.created_at,'%Y') = '{$from}'" ;            
        }        

        $cmd = "select pr.id,pr.name,sum(id.amount) as total from invoice_headers ih
                inner join invoice_details id on ih.id = id.invoice_id
                inner join products p on p.id = id.product_id
                inner join products pr on pr.id = p.parent_id
                inner join partner_companies pc on pc.partner_id = ih.partner_id
                where p.status = 'A' and ih.status = 'P' {$addQry} {$dtQry}
                group by  pr.id,pr.name
                order by pr.name";

        $products = DB::select(DB::raw($cmd));
        $grandTotal = 0;
        foreach ($products as $p) {
            $cmd = "select pc.partner_id,pc.company_name,sum(id.amount) as total from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    inner join products p on p.id = id.product_id
                    inner join products pr on pr.id = p.parent_id
                    inner join partner_companies pc on pc.partner_id = ih.partner_id
                    where pr.id = {$p->id} and ih.status = 'P' {$addQry} {$dtQry}
                    group by  pc.partner_id,pc.company_name 
                    order by pc.company_name ";

            $merc = DB::select(DB::raw($cmd));
            foreach ($merc as $m) {
                $cmd = "select p.name as product,sum(id.amount) as amount from invoice_headers ih
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join products p on p.id = id.product_id
                        inner join products pr on pr.id = p.parent_id
                        inner join partner_companies pc on pc.partner_id = ih.partner_id
                        where pr.id = {$p->id} and ih.status = 'P' and ih.partner_id = {$m->partner_id} {$dtQry}
                        group by  p.id,p.name,pc.company_name 
                        order by p.name";

                $m->details = DB::select(DB::raw($cmd));
            }
            $p->merchants = $merc;
            $grandTotal = $p->total + $grandTotal;
        }


        if($export == 'true'){
            if(!$report_export){
                return redirect('/')->with('failed', 'You have no access to that page.');
            } 
            Excel::create('Product_Report', function($excel) use($products,$grandTotal){
                $excel->sheet('Product_Report', function($sheet) use($products,$grandTotal){
                    
                    $header = array('Product','Sub Product','Merchant Name','Amount');
                    $sheet->row(1,$header);
                    $row=2;
                    foreach($products as $p){
                        $data = Array($p->name,'','',$p->total);
                        $sheet->row($row++,$data);
                        foreach ($p->merchants as $m) {
                            $data = Array('',$m->company_name,'',$m->total);
                            $sheet->row($row++,$data);
                            foreach ($m->details as $d) {
                                $data = Array('','',$d->product,$d->amount);
                                $sheet->row($row++,$data);
                            }
                        }
                    }
                    $data = Array('','','Total',$grandTotal);
                    $sheet->row($row++,$data);
                });
            })->export('xls');
        }else{
            return view("reports.report_product",compact('id','type','from','to','products','report_export','merchants','grandTotal'));          
        }

    }


    public function report_branches(){

        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'consolidated branches report') === false) ? false : true;
        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        } 
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
            $merchants = Partner::where('status','A')->whereIn('partner_type_id',Array(3))->whereRaw("id in ({$partner_access})")->orderBy('created_at','desc')->get();
        }else{
            $partner_access="";
            $merchants = Partner::where('status','A')->whereIn('partner_type_id',Array(3))->orderBy('created_at','desc')->get();
        }

        $init = 1;
        return view("reports.report_branch",compact('merchants','init'));
    }

    public function generate_report_branches($id,$type,$from,$to,$export) {
        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $report_generate = (strpos($reportaccess, 'consolidated branches report') === false) ? false : true;
        $report_export = (strpos($reportaccess, 'consolidated branches report export') === false) ? false : true;
        if(!$report_generate){
            return redirect('/')->with('failed', 'You have no access to that page.');
        } 
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $addQry = " and ih.partner_id = {$id} ";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
            $partner_access = $partner_access == "" ? auth()->user()->reference_id : $partner_access;
            $merchants = Partner::where('status','A')->whereIn('partner_type_id',Array(3))->whereRaw("id in ({$partner_access})")->orderBy('created_at','desc')->get();
            if($id == -1){
                $addQry = "and ih.partner_id in({$partner_access}) ";
            }
        }else{
            $partner_access="";
            $merchants = Partner::where('status','A')->whereIn('partner_type_id',Array(3))->orderBy('created_at','desc')->get();
            if($id == -1){
                $addQry = " ";
            }
        }

        $dtQry = " ";

        if($type == 'Daily'){
            $dtQry .= " and DATE_FORMAT(ih.created_at,'%Y-%m-%d') = '{$from}'" ;         
        }

        if($type == 'Weekly' || $type == 'Custom'){
            $fromDt = new Carbon($from);
            $toDt = new Carbon($to);
            $toDt = $toDt->addDay();
            $dtQry .= " and ih.created_at >= '".$fromDt->toDateString()."' and ih.created_at < '".$toDt->toDateString()."' ";         
        }

        if($type == 'Monthly'){  
            $dtQry .= " and DATE_FORMAT(ih.created_at,'%Y-%m') = '{$from}'" ;           
        }

        if($type == 'Yearly'){
            $dtQry .= " and DATE_FORMAT(ih.created_at,'%Y') = '{$from}'" ;            
        }    


        if($id == -1){    
            $cmd = "select p.id,ih.company_name from partners p
                    inner join partner_companies ih on ih.partner_id = p.id
                    where p.status = 'A' and p.partner_type_id = 3 {$addQry}
                    order by ih.company_name";

            $products = DB::select(DB::raw($cmd));
            foreach ($products as $m) {
                $cmd = "select p.id,ih.company_name from partners p
                    inner join partner_companies ih on ih.partner_id = p.id
                    where p.status = 'A' and p.partner_type_id = 9 and p.parent_id = {$m->id}
                    order by ih.company_name";  
                $branches = DB::select(DB::raw($cmd));    
                $grandTotal = 0;
                foreach ($branches as $b) {

                    $cmd = "select p.name as product,sum(id.amount) as amount from invoice_headers ih
                            inner join invoice_details id on ih.id = id.invoice_id
                            inner join products p on p.id = id.product_id
                            where ih.status = 'P' and ih.partner_id = {$b->id} {$dtQry}
                            group by  p.id,p.name
                            order by p.name";

                    $b->details = DB::select(DB::raw($cmd));
                    $b->total = 0;
                    foreach($b->details as $d){
                       $grandTotal = $d->amount + $grandTotal; 
                       $b->total = $d->amount + $b->total; 
                    }
                }
                $m->branches = $branches;
                $m->grandTotal = $grandTotal;

            }

        }else{

            $cmd = "select p.id,ih.company_name from partners p
                inner join partner_companies ih on ih.partner_id = p.id
                where p.status = 'A' and p.partner_type_id = 9 and p.parent_id = {$id}
                order by ih.company_name";  
            $products = DB::select(DB::raw($cmd));    
            $grandTotal = 0;
            foreach ($products as $b) {

                $cmd = "select p.name as product,sum(id.amount) as amount from invoice_headers ih
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join products p on p.id = id.product_id
                        where ih.status = 'P' and ih.partner_id = {$b->id} {$dtQry}
                        group by  p.id,p.name
                        order by p.name";

                $b->details = DB::select(DB::raw($cmd));
                $b->total = 0;
                foreach($b->details as $d){
                   $grandTotal = $d->amount + $grandTotal; 
                   $b->total = $d->amount + $b->total; 
                }
            }
        }


        if($export == 'true'){
            if(!$report_export){
                return redirect('/')->with('failed', 'You have no access to that page.');
            } 
            if($id == -1){ 
                Excel::create('Consolidated_Branches_Report_All_Merchants', function($excel) use($products,$grandTotal){
                    $excel->sheet('Consolidated_Branches_Report', function($sheet) use($products,$grandTotal){
                        $header = array('Merchant','Branch','Product','Amount');
                        $sheet->row(1,$header);
                        $row=2;
                        foreach($products as $p){
                            if($p->grandTotal > 0){
                                $data = Array($p->company_name);
                                $sheet->row($row++,$data);
                                foreach($p->branches as $b){
                                    $data = Array('',$b->company_name);
                                    $sheet->row($row++,$data);
                                    foreach ($b->details as $d) {
                                        $data = Array('','',$d->product,$d->amount);
                                        $sheet->row($row++,$data);
                                    }
                                    $data = Array('','','Total',$b->total);
                                    $sheet->getStyle("C{$row}:D{$row}")->applyFromArray(array(
                                                    'font' => array(
                                                        'bold' => true
                                                    )
                                                ));

                                    $sheet->row($row++,$data);
                                }
                                $data = Array('','','Grand Total',$p->grandTotal);
                                $sheet->getStyle("C{$row}:D{$row}")->applyFromArray(array(
                                                    'font' => array(
                                                        'bold' => true
                                                    )
                                                ));
                                $sheet->row($row++,$data);
                                

                            }
                        }
                        
                    });
                })->export('xls');
            }else{
                $mname = Partner::find($id);
                Excel::create('Consolidated_Branches_Report_'.$mname->partner_company->company_name, function($excel) use($products,$grandTotal){
                    $excel->sheet('Consolidated_Branches_Report', function($sheet) use($products,$grandTotal){
                        $header = array('Branch','Product','Amount');
                        $sheet->row(1,$header);
                        $row=2;
                        foreach($products as $p){
                            $data = Array($p->company_name);
                            $sheet->row($row++,$data);
                            foreach ($p->details as $d) {
                                $data = Array('',$d->product,$d->amount);
                                $sheet->row($row++,$data);
                            }
                            $data = Array('','Total',$p->total);
                            $sheet->getStyle("B{$row}:C{$row}")->applyFromArray(array(
                                            'font' => array(
                                                'bold' => true
                                            )
                                        ));

                            $sheet->row($row++,$data);
                        }
                        $data = Array('','Grand Total',$grandTotal);
                        $sheet->getStyle("B{$row}:C{$row}")->applyFromArray(array(
                                            'font' => array(
                                                'bold' => true
                                            )
                                        ));
                        $sheet->row($row++,$data);
                    });
                })->export('xls');

            }
        }else{
            return view("reports.report_branch",compact('id','type','from','to','products','report_export','merchants','grandTotal'));          
        }

    }



    public function report_export_log(){
        $init = 1;
        return view("reports.report_export_log",compact('init'));
    }

    public function export_log_generate_report($from,$to){
        $orig_from = $from;
        $orig_to = $to;

        $from = new Carbon($from);
        $to = new Carbon($to);
        $to = $to->addDay();

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $is_admin =  (strpos($admin_access, 'super admin access') === false) ? false : true;

        if(!$is_admin){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        $logs = ReportExportLog::where('created_at','>=',$from->toDateString())->where('created_at','<',$to->toDateString())->get();
        return view("reports.report_export_log",compact('orig_from','orig_to','logs'));

    }


    public function report_ms(){
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $monthName = Carbon::now()->format('F');
        $init = 1;
        return view("reports.report_ms",compact('month','year','monthName','init'));
    }    

    public function report_ms_generate($month,$year){
        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);
        $userType = session('user_type_desc');
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $ms = (strpos($reportaccess, 'monthly sales report') === false) ? false : true;
        $ms_export = (strpos($reportaccess, 'monthly sales export') === false) ? false : true;
        if(!$ms){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        if (strpos($admin_access, 'super admin access') === false){
            $invoices = InvoiceHeader::where('order_id','<>',-1)
                        ->whereIn('status',array('P','U','S','O'))
                        ->whereMonth('invoice_date',$month)
                        ->whereYear('invoice_date',$year)
                        ->whereRaw('partner_id in('.$partner_id.')')->get();

            $merchants = Partner::where('partner_type_id',3)->where('status','A')->whereRaw('id in('.$partner_id.')')->get();
            $totalCmd = "select p_main.name,count(p.name) as cases,sum(id.amount) as total from invoice_headers ih
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join products p on p.id = id.product_id
                        inner join products p_main on p.parent_id = p_main.id
                        where ih.partner_id in({$partner_id})
                        and MONTH(invoice_date) = {$month} and YEAR(invoice_date)= {$year}
                        group by p_main.name";
        }else{
            $invoices = InvoiceHeader::where('order_id','<>',-1)
                        ->whereIn('status',array('P','U','S','O'))
                        ->whereMonth('invoice_date',$month)
                        ->whereYear('invoice_date',$year)->get();
            $merchants = Partner::where('partner_type_id',3)->where('status','A')->get();
            $totalCmd = "select p_main.name,count(p.name) as cases,sum(id.amount) as total from invoice_headers ih
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join products p on p.id = id.product_id
                        inner join products p_main on p.parent_id = p_main.id
                        where MONTH(invoice_date) = {$month} and YEAR(invoice_date)= {$year}
                        group by p_main.name";
        }
        $totals = DB::select(DB::raw($totalCmd));
        $amount = 0.00;
        $setupFee = 0.00;
        $monthlyFee = 0.00;
        $yearlyFee = 0.00;
        $prePaid = 0.00;
        $current = 0.00;
        $received = 0.00;
        foreach($invoices as $inv){
            foreach($inv->details as $detail){
                $type = isset($detail->product->payment_type->name) ? $detail->product->payment_type->name : "Amount";
                switch ($type) {
                    case 'Amount':
                        $amount =  $amount + $detail->amount;
                        break;
                    case 'Setup Fee':
                        $setupFee =  $setupFee + $detail->amount;
                        break; 
                    case 'Monthly Fee':
                        $monthlyFee =  $monthlyFee + $detail->amount;
                        break;
                    case 'Yearly Fee':
                        $yearlyFee =  $yearlyFee + $detail->amount;
                        break;
                    case 'Prepaid':
                        $prePaid =  $prePaid + $detail->amount;
                        break;
                    default:
                        break;
                }
            }
            if($inv->status == 'P'){
                $received = $received + $inv->total_due; 
            }
            $current = $current + $inv->total_due;  
        }
        $totalCase = 0;
        $totalAmount = 0;
        foreach ($merchants as $m) {
            $cmd = "select p.name,count(p.name) as cases,sum(id.amount) as total from invoice_headers ih
            inner join invoice_details id on ih.id = id.invoice_id
            inner join products p on p.id = id.product_id
            where ih.partner_id = {$m->id} 
            and MONTH(invoice_date) = {$month} and YEAR(invoice_date)= {$year}
            group by p.name";
            $records = DB::select(DB::raw($cmd));
            $m->products = "";
            $m->cases = "";
            $m->total_cases = 0;
            $m->total_amount = 0;
            foreach($records as $rec){
                $m->products .= $rec->name . '<br>';
                $m->cases .= $rec->cases . '<br>';
                $m->total_cases = $m->total_cases + $rec->cases;
                $m->total_amount = $m->total_amount + $rec->total;
            }
            $totalCase = $totalCase + $m->total_cases;
            $totalAmount = $totalAmount + $m->total_amount;
        }
        $monthName = date("F", mktime(0, 0, 0, $month, 1));
        return view("reports.report_ms",compact('invoices','amount','setupFee','monthlyFee','yearlyFee','prePaid','current','received','merchants','totalCase','totalAmount','totals','month','year','monthName','ms_export'));
    }

    public function report_ms_export($month,$year){

        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $ms_export = (strpos($reportaccess, 'monthly sales export') === false) ? false : true;
        if(!$ms_export){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        $log = new ReportExportLog;
        $log->user_id = auth()->user()->id;
        $log->report_name = 'Monthly Sales Report';
        $log->save();

        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);
        $title = 'monthly_sales_report_'.strtolower(date('M', mktime(0, 0, 0, $month, 10))).'_'.$year;
        Excel::create($title, function($excel) use($month,$year,$partner_id){
            $excel->sheet('monthly_sales_report_'.strtolower(date('M', mktime(0, 0, 0, $month, 10))).'_output', function($sheet) use($month,$year,$partner_id){
                $monthName = date("F", mktime(0, 0, 0, $month, 1));
                $row=1;
                $sheet->row($row++,array("Monthly Sales Report for {$monthName} {$year}"));
                $header = array('Case #','Total Amount','Received Date','CID','Product','Date','DBA','Agent','Qty','Amount','Type','Note');
                $sheet->row($row++,$header);
                $userType = session('user_type_desc');
                $access = session('all_user_access');
                $admin_access = isset($access['admin']) ? $access['admin'] : "";

                if (strpos($admin_access, 'super admin access') === false){
                    $invoices = InvoiceHeader::where('order_id','<>',-1)
                                ->where('status',array('P','U','S','O'))
                                ->whereMonth('invoice_date',$month)
                                ->whereYear('invoice_date',$year)
                                ->whereRaw('partner_id in('.$partner_id.')')->get();

                    $merchants = Partner::where('partner_type_id',3)->where('status','A')->whereRaw('id in('.$partner_id.')')->get();
                    $totalCmd = "select p_main.name,count(p.name) as cases,sum(id.amount) as total from invoice_headers ih
                                inner join invoice_details id on ih.id = id.invoice_id
                                inner join products p on p.id = id.product_id
                                inner join products p_main on p.parent_id = p_main.id
                                where ih.partner_id in({$partner_id})
                                and MONTH(invoice_date) = {$month} and YEAR(invoice_date)= {$year}
                                group by p_main.name";
                }else{
                    $invoices = InvoiceHeader::where('order_id','<>',-1)
                                ->whereIn('status',array('P','U','S','O'))
                                ->whereMonth('invoice_date',$month)
                                ->whereYear('invoice_date',$year)->get();
                    $merchants = Partner::where('partner_type_id',3)->where('status','A')->get();
                    $totalCmd = "select p_main.name,count(p.name) as cases,sum(id.amount) as total from invoice_headers ih
                                inner join invoice_details id on ih.id = id.invoice_id
                                inner join products p on p.id = id.product_id
                                inner join products p_main on p.parent_id = p_main.id
                                where MONTH(invoice_date) = {$month} and YEAR(invoice_date)= {$year}
                                group by p_main.name";
                }
                $totals = DB::select(DB::raw($totalCmd));
                $amount = 0.00;
                $setupFee = 0.00;
                $monthlyFee = 0.00;
                $yearlyFee = 0.00;
                $prePaid = 0.00;
                $current = 0.00;
                $received = 0.00;
                $i = 1;
                foreach($invoices as $inv){
                    $sheet->row($row++,Array('','$ '.$inv->total_due, ($inv->status == 'P') ? Carbon::parse($inv->updated_at)->format('m/d/Y') : '' ,$inv->partner->credit_card_reference_id,$inv->reference,Carbon::parse($inv->invoice_date)->format('m/d/Y'),$inv->partner->partner_company->dba,$inv->partner->partner_company->company_name,'','','',$inv->remarks));
                    foreach($inv->details as $detail){
                        $type = isset($detail->product->payment_type->name) ? $detail->product->payment_type->name : "Amount";
                        switch ($type) {
                            case 'Amount':
                                $amount =  $amount + $detail->amount;
                                break;
                            case 'Setup Fee':
                                $setupFee =  $setupFee + $detail->amount;
                                break; 
                            case 'Monthly Fee':
                                $monthlyFee =  $monthlyFee + $detail->amount;
                                break;
                            case 'Yearly Fee':
                                $yearlyFee =  $yearlyFee + $detail->amount;
                                break;
                            case 'Prepaid':
                                $prePaid =  $prePaid + $detail->amount;
                                break;
                            default:
                                break;
                        }
                        $sheet->row($row++,Array($i++,'','','',$detail->product->name,'','','',$detail->quantity,'$ '.$detail->amount,isset($detail->product->payment_type->name) ? $detail->product->payment_type->name : "Amount"));
                    }
                    if($inv->status == 'P'){
                        $received = $received + $inv->total_due; 
                    }
                    $current = $current + $inv->total_due; 
                }
                $sheet->row($row++,Array('','','','','','','','','Amount','$ '.number_format((float)$amount, 2, '.', '')));
                $sheet->row($row++,Array('','','','','','','','','Setup Fee','$ '.number_format((float)$setupFee, 2, '.', '')));
                $sheet->row($row++,Array('','','','','','','','','Monthly Fee','$ '.number_format((float)$monthlyFee, 2, '.', '')));
                $sheet->row($row++,Array('','','','','','','','','Yearly Fee','$ '.number_format((float)$yearlyFee, 2, '.', '')));
                $sheet->row($row++,Array('','','','','','','','','Prepaid','$ '.number_format((float)$prePaid, 2, '.', '')));
                $sheet->row($row++,Array('','','','','','','','','Current','$ '.number_format((float)$current, 2, '.', '')));
                $sheet->row($row++,Array('','','','','','','','','Total Received','$ '.number_format((float)$received, 2, '.', '')));

                $sheet->row($row++,Array("Agent Summary for {$monthName} {$year}"));
                $header = array('','Agent','Product','# of Cases','Total Cases','Total Amount');
                $sheet->row($row++,$header);
                $totalCase = 0;
                $totalAmount = 0;
                $i = 1;
                foreach ($merchants as $m) {
                    $cmd = "select p.name,count(p.name) as cases,sum(id.amount) as total from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    inner join products p on p.id = id.product_id
                    where ih.partner_id = {$m->id} 
                    and MONTH(invoice_date) = {$month} and YEAR(invoice_date)= {$year}
                    group by p.name";
                    $records = DB::select(DB::raw($cmd));
                    $m->products = "";
                    $m->cases = "";
                    $m->total_cases = 0;
                    $m->total_amount = 0;
                    $first = true;
                    foreach($records as $rec){
                        $m->total_cases = $m->total_cases + $rec->cases;
                        $m->total_amount = $m->total_amount + $rec->total;
                    }

                    foreach($records as $rec){
                        if($first){
                            $sheet->row($row++,array($i,$m->partner_company->company_name,$rec->name ,$rec->cases,$m->total_cases,'$ '.number_format((float)$m->total_amount, 2, '.', '')));
                        }else{
                            $sheet->row($row++,array('','',$rec->name ,$rec->cases));
                        }
                        $first = false;
                    }
                    if($first){
                        $sheet->row($row++,array($i++,$m->partner_company->company_name));
                    }
                    $totalCase = $totalCase + $m->total_cases;
                    $totalAmount = $totalAmount + $m->total_amount;
                }
                $sheet->row($row++,array('','','','',$totalCase,'$ '.number_format((float)$totalAmount, 2, '.', '')));
                foreach($totals as $t){
                    $sheet->row($row++,array('Total '.$t->name,'','','',$t->cases));
                }
                $sheet->row($row++,array('Total# of Cases','','','',$totalCase));
            });
        })->export('xls');
    }

    public function ach_report(){
        return view("reports.report_ach",compact('year','month'));
    }

    public function ach_generate_report($from,$to){
        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);
        $orig_from = $from;
        $orig_to = $to;

        $from = new Carbon($from);
        $to = new Carbon($to);
        $to = $to->addDay();

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'super admin access') === false){
            $invoices = InvoiceHeader::where('order_id','<>',-1)
                        ->where('status','P')->where('invoice_date','>=',$from->toDateString())->where('invoice_date','<',$to->toDateString())->whereRaw('partner_id in('.$partner_id.')')->get();
        }else{
            $invoices = InvoiceHeader::where('order_id','<>',-1)
                        ->where('status','P')->where('invoice_date','>=',$from->toDateString())->where('invoice_date','<',$to->toDateString())->get();
        }

        $newInvoices = Array();
        foreach ($invoices as $invoice) {
            if($invoice->payment->payment_type_id == 1){
                $sub_data = array();
                foreach ($invoice->details as $detail) {
                    $sub_product = Product::find($detail->product_id);
                    $product_id = $sub_product->mainproduct->code;
                    array_push($sub_data, $sub_product->name.' [$'.$detail->amount.']');
                }
                $data = array($invoice->partner->partner_id_reference,$invoice->partner->partner_company->company_name,Carbon::parse($invoice->invoice_date)->format('m/d/Y'),$invoice->total_due,$product_id,$invoice->reference);
                $newInvoices[] = array_merge($data,$sub_data);         
            }
        }

        $reportaccess = isset($access['reports']) ? $access['reports'] : "";

        $ach = (strpos($reportaccess, 'ach transaction report') === false) ? false : true;
        $ach_export = (strpos($reportaccess, 'ach transaction export') === false) ? false : true;
        if(!$ach){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }
        return view("reports.report_ach",compact('orig_from','orig_to','newInvoices','ach_export'));
    }


    public function ach_export_report($from,$to){

        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $ach_export = (strpos($reportaccess, 'ach transaction export') === false) ? false : true;
        if(!$ach_export){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        $log = new ReportExportLog;
        $log->user_id = auth()->user()->id;
        $log->report_name = 'ACH Transaction Report';
        $log->save();

        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);

        $from = new Carbon($from);
        $to = new Carbon($to);
        $to = $to->addDay();
        $title = 'achtransactions_'.strtolower(date('M', mktime(0, 0, 0, $from->month, 10))).'_'.$from->year.'_details';

        Excel::create($title, function($excel) use($from,$to,$partner_id){
            $excel->sheet('achtransactions_'.strtolower(date('M', mktime(0, 0, 0, $from->month, 10))).'_output', function($sheet) use($from,$to,$partner_id){
                $header = array('CUSTOMER ID','NAME','EED','AMOUNT','PRODUCT CODE','PRODUCT NAME');
                $row = 1;
                $sheet->row($row,$header);
                $userType = session('user_type_desc');
                $access = session('all_user_access');
                $admin_access = isset($access['admin']) ? $access['admin'] : "";
               
                if (strpos($admin_access, 'super admin access') === false){
                    $invoices = InvoiceHeader::where('order_id','<>',-1)
                                ->where('status','P')->where('invoice_date','>=',$from->toDateString())->where('invoice_date','<',$to->toDateString())->whereRaw('partner_id in('.$partner_id.')')->get();
                }else{
                    $invoices = InvoiceHeader::where('order_id','<>',-1)
                                ->where('status','P')->where('invoice_date','>=',$from->toDateString())->where('invoice_date','<',$to->toDateString())->get();
                }
                foreach ($invoices as $invoice) {
                    if($invoice->payment->payment_type_id == 1){
                        $row++;
                        $product = "";
                        $sub_data = array();
                        foreach ($invoice->details as $detail) {
                            $sub_product = Product::find($detail->product_id);
                            $product_id = $sub_product->mainproduct->code;
                            array_push($sub_data, $sub_product->name.' [$'.$detail->amount.']');
                        }
                        $data = array($invoice->partner->partner_id_reference,$invoice->partner->partner_company->company_name,Carbon::parse($invoice->invoice_date)->format('m/d/Y'),$invoice->total_due,$product_id,$invoice->reference);
                        $data = array_merge($data,$sub_data);
                        $sheet->row($row,$data);          
                    }
                }

                $sheet->cell('A1:F1', function($cell) {
                    $cell->setFontWeight('bold');
                });

            });
        })->export('xls');
    }

    public function ach_generate_residual($from,$to){
        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);
        $orig_from = $from;
        $orig_to = $to;

        $from = new Carbon($from);
        $to = new Carbon($to);
        $to = $to->addDay();

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $invoices = InvoiceHeader::where('order_id','<>',-1)
                        ->where('status','P')->where('invoice_date','>=',$from->toDateString())->where('invoice_date','<',$to->toDateString())->whereRaw('partner_id in('.$partner_id.')')->get();
        }else{
            $invoices = InvoiceHeader::where('order_id','<>',-1)
                        ->where('status','P')->where('invoice_date','>=',$from->toDateString())->where('invoice_date','<',$to->toDateString())->get();
        }
        
        $residualInvoices = Array();
        foreach ($invoices as $invoice) {
            if($invoice->payment->payment_type_id == 1){
                $sub_data = array();

                $company = "";
                $merchant = Partner::find($invoice->partner_id);
                $parent_id = $invoice->partner_id;
                while($parent_id <> -1){
                    $partner = Partner::find($parent_id);
                    if(!isset($partner)){
                        $parent_id = -1;
                    }else{
                        $parent_id = $partner->parent_id;
                        if($partner->partner_type->name == "COMPANY"){
                           $company = $partner->partner_company->company_name;
                        }
                        $name =  $partner->partner_company->company_name . ' ['.$partner->partner_type->name.']';
                        $buyrate = "";
                        foreach ($invoice->details as $detail) {
                            $partner_product = PartnerProduct::where('product_id',$detail->product_id)->where('partner_id',$parent_id)->first();
                            $sub_product = Product::find($detail->product_id);
                            $product_id = $sub_product->mainproduct->code;
                            if(isset($partner_product)) {
                                if($partner->partner_type->name == "MERCHANT")
                                {
                                    $buyrate .= $sub_product->name.' [$'.$detail->amount.']-';
                                }else{
                                    $buyrate .= $sub_product->name.' [$'.number_format((float)$partner_product->buy_rate, 2, '.', '').']-';
                                }
                            }else{
                                $buyrate = "0";
                            }                                
                        }
                        if(strlen($buyrate) > 1){
                            $buyrate = substr($buyrate, 0, strlen($buyrate) - 1);
                        }
                        array_unshift($sub_data,$partner->partner_id_reference,$name,$buyrate);
                    }

                }
                $data = array($company,$merchant->partner_company->company_name,$invoice->partner->partner_id_reference,Carbon::parse($invoice->invoice_date)->format('m/d/Y'),$invoice->total_due,$product_id,$invoice->reference);
                $data = array_merge($data,$sub_data);
                $residualInvoices[] = $data;        
            }
        }

        $reportaccess = isset($access['reports']) ? $access['reports'] : "";

        $ach = (strpos($reportaccess, 'ach transaction report') === false) ? false : true;
        $ach_export = (strpos($reportaccess, 'ach transaction export') === false) ? false : true;
        if(!$ach){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }
        return view("reports.report_ach",compact('orig_from','orig_to','residualInvoices','ach_export'));
    }


    public function ach_export_residual($from,$to){

        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $ach_export = (strpos($reportaccess, 'ach transaction export') === false) ? false : true;
        if(!$ach_export){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        $log = new ReportExportLog;
        $log->user_id = auth()->user()->id;
        $log->report_name = 'ACH Residual Report';
        $log->save();


        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);
        $from = new Carbon($from);
        $to = new Carbon($to);
        $to = $to->addDay();

        $title = 'achtransactions_'.strtolower(date('M', mktime(0, 0, 0, $from->month, 10))).'_'.$from->year.'_details';
        
        Excel::create($title, function($excel) use($from,$to,$partner_id){
            $excel->sheet('achtransactions_'.strtolower(date('M', mktime(0, 0, 0, $from->month, 10))).'_output', function($sheet) use($from,$to,$partner_id){
                $header = array('COMPANY','NAME','CUSTOMER ID','EED','AMOUNT','PRODUCT CODE','PRODUCT NAME');
                $row = 1;
                $sheet->row($row,$header);
                $userType = session('user_type_desc');
                $access = session('all_user_access');
                $admin_access = isset($access['admin']) ? $access['admin'] : "";
               
                if (strpos($admin_access, 'super admin access') === false){
                    $invoices = InvoiceHeader::where('order_id','<>',-1)
                                ->where('status','P')->where('invoice_date','>=',$from->toDateString())->where('invoice_date','<',$to->toDateString())->whereRaw('partner_id in('.$partner_id.')')->get();
                }else{
                    $invoices = InvoiceHeader::where('order_id','<>',-1)
                                ->where('status','P')->where('invoice_date','>=',$from->toDateString())->where('invoice_date','<',$to->toDateString())->get();
                }
                foreach ($invoices as $invoice) {
                    if($invoice->payment->payment_type_id == 1){
                        $row++;
                        $product = "";
                        $sub_data = array();

                        $company = "";
                        $merchant = Partner::find($invoice->partner_id);
                        $parent_id = $invoice->partner_id;
                        while($parent_id <> -1){
                            $partner = Partner::find($parent_id);
                            if(!isset($partner)){
                                $parent_id = -1;
                            }else{
                                $parent_id = $partner->parent_id;
                                if($partner->partner_type->name == "COMPANY"){
                                   $company = $partner->partner_company->company_name;
                                }
                                $name =  $partner->partner_company->company_name . ' ['.$partner->partner_type->name.']';
                                $buyrate = "";
                                foreach ($invoice->details as $detail) {
                                    $partner_product = PartnerProduct::where('product_id',$detail->product_id)->where('partner_id',$parent_id)->first();
                                    $sub_product = Product::find($detail->product_id);
                                    $product_id = $sub_product->mainproduct->code;
                                    if(isset($partner_product)) {
                                        if($partner->partner_type->name == "MERCHANT")
                                        {
                                            $buyrate .= $sub_product->name.' [$'.$detail->amount.']-';
                                        }else{
                                            $buyrate .= $sub_product->name.' [$'.number_format((float)$partner_product->buy_rate, 2, '.', '').']-';
                                        }
                                    }else{
                                        $buyrate = "0";
                                    }                                
                                }
                                if(strlen($buyrate) > 1){
                                    $buyrate = substr($buyrate, 0, strlen($buyrate) - 1);
                                }
                                array_unshift($sub_data,$partner->partner_id_reference,$name,$buyrate);
                            }

                        }

                        

                        $data = array($company,$merchant->partner_company->company_name,$invoice->partner->partner_id_reference,Carbon::parse($invoice->invoice_date)->format('m/d/Y'),$invoice->total_due,$product_id,$invoice->reference);
                        $data = array_merge($data,$sub_data);
                        $sheet->row($row,$data);          
                    }
                }

                $sheet->cell('A1:G1', function($cell) {
                    $cell->setFontWeight('bold');
                });

            });
        })->export('xls');
    }


    public function report_commission_detailed(){
        $init = 1;
        return view("reports.report_commission_detailed",compact('init'));
    }

    public function commission_generate_report_detailed($from,$to){

        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);

        $orig_from = $from;
        $orig_to = $to;

        $from = new Carbon($from);
        $to = new Carbon($to);
        $to = $to->addDay();

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $company_id = auth()->user()->company_id;

        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $com = (strpos($reportaccess, 'commission report') === false) ? false : true;
        $com_export = (strpos($reportaccess, 'commission export') === false) ? false : true;
        if(!$com){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        if (strpos($admin_access, 'super admin access') === false){
            $query = "select ih.id,p.partner_id_reference,p.merchant_mid,pc.company_name,pt.name as payment_type,
                        DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as invoice_date,
                        (id.amount/id.quantity) as price,id.amount,id.quantity,
                        ins.description as status,fr.frequency,cat.name as category, p_main.name as main_product,
                        p_sub.name as sub_product,p.merchant_url as domain,
                        DATE_FORMAT(fr.bill_date, '%m/%d/%Y') as bill_date,p_sub.id as sub_id,p.id as partner_id,p.is_cc_client,id.cost
                        from invoice_headers ih
                        inner join invoice_statuses ins on ih.status = ins.code
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join invoice_frequencies fr on fr.id = id.invoice_frequency_id
                        inner join invoice_payments ip on ip.invoice_id = ih.id
                        inner join payment_types pt on pt.id = ip.payment_type_id
                        inner join products p_sub on p_sub.id = id.product_id
                        inner join product_categories cat on cat.id = p_sub.product_category_id
                        inner join products p_main on p_main.id = p_sub.parent_id
                        inner join partners p on p.id = ih.partner_id
                        inner join partner_companies pc on pc.partner_id = p.id 
                        where ih.status = 'P' and ih.invoice_date >= '{$from->toDateString()}' and ih.invoice_date < '{$to->toDateString()}' and p.company_id = {$company_id}";

            $records = DB::select(DB::raw($query));
        }else{
            $query = "select ih.id,p.partner_id_reference,p.merchant_mid,pc.company_name,pt.name as payment_type,
                        DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as invoice_date,
                        (id.amount/id.quantity) as price,id.amount,id.quantity,
                        ins.description as status,fr.frequency,cat.name as category, p_main.name as main_product,
                        p_sub.name as sub_product,p.merchant_url as domain,
                        DATE_FORMAT(fr.bill_date, '%m/%d/%Y') as bill_date,p_sub.id as sub_id,p.id as partner_id,p.is_cc_client,id.cost
                        from invoice_headers ih
                        inner join invoice_statuses ins on ih.status = ins.code
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join invoice_frequencies fr on fr.id = id.invoice_frequency_id
                        inner join invoice_payments ip on ip.invoice_id = ih.id
                        inner join payment_types pt on pt.id = ip.payment_type_id
                        inner join products p_sub on p_sub.id = id.product_id
                        inner join product_categories cat on cat.id = p_sub.product_category_id
                        inner join products p_main on p_main.id = p_sub.parent_id
                        inner join partners p on p.id = ih.partner_id
                        inner join partner_companies pc on pc.partner_id = p.id 
                        where ih.status = 'P' and ih.invoice_date >= '{$from->toDateString()}' and ih.invoice_date < '{$to->toDateString()}'";

            $records = DB::select(DB::raw($query));
        }

        $commissionRec = array();

        foreach ($records as $rec) {
            $data = array($rec->id,$rec->partner_id_reference,$rec->company_name,$rec->payment_type,
                    $rec->invoice_date,$rec->amount,$rec->category,$rec->main_product,$rec->sub_product,$rec->domain,
                    $rec->frequency,$rec->status,$rec->bill_date);

            $subData = array();
            $cmd = "Select ic.*,ppt.name as partnerType,ppc.company_name as upline from invoice_commissions ic 
                    inner join partners pp on ic.partner_id = pp.id
                    inner join partner_types ppt on pp.partner_type_id = ppt.id
                    inner join partner_companies ppc on ppc.partner_id = pp.id
                    where ic.invoice_id = {$rec->id} and ic.product_id = {$rec->sub_id} order by ic.id";
            $commissions = DB::select(DB::raw($cmd));     
            foreach ($commissions as $comm) {
                array_push($subData,$comm->upline.' ('.$comm->partnerType.')', $comm->totalCommission);
            }

            if(isset($commissions)){
                $data = array_merge($data,$subData);
                $commissionRec[] = $data;                       
            }
        }

        return view("reports.report_commission_detailed",compact('orig_from','orig_to','commissionRec','com_export'));

    }

    public function commission_export_report_detailed($from,$to){

        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $com_export = (strpos($reportaccess, 'commission export') === false) ? false : true;
        if(!$com_export){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        $log = new ReportExportLog;
        $log->user_id = auth()->user()->id;
        $log->report_name = 'Commission Detailed Report';
        $log->save();


        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);

        $from = new Carbon($from);
        $to = new Carbon($to);
        $to = $to->addDay();
        $title = 'commission_report_'.strtolower(date('M', mktime(0, 0, 0, $from->month, 10))).'_'.$from->year;

        Excel::create($title, function($excel) use($from,$to,$partner_id){
            $excel->sheet('commission_report_'.strtolower(date('M', mktime(0, 0, 0, $from->month, 10))).'_output', function($sheet) use($from,$to,$partner_id){
                $header = array('Invoice ID','Customer ID','Merchant DBA','Payment Method','Date','Amount','Product Category','Main Product','Sub Product','Domain','Frequency','Status','Billing Date','Agent','Commission');
                $row = 1;
                $sheet->row($row,$header);
                $userType = session('user_type_desc');
                $access = session('all_user_access');
                $admin_access = isset($access['admin']) ? $access['admin'] : "";
                $company_id = auth()->user()->company_id;
                if (strpos($admin_access, 'super admin access') === false){
                    $query = "select ih.id,p.partner_id_reference,p.merchant_mid,pc.company_name,pt.name as payment_type,
                                DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as invoice_date,
                                (id.amount/id.quantity) as price,id.amount,id.quantity,
                                ins.description as status,fr.frequency,cat.name as category, p_main.name as main_product,
                                p_sub.name as sub_product,p.merchant_url as domain,
                                DATE_FORMAT(fr.bill_date, '%m/%d/%Y') as bill_date,p_sub.id as sub_id,p.id as partner_id,p.is_cc_client,id.cost
                                from invoice_headers ih
                                inner join invoice_statuses ins on ih.status = ins.code
                                inner join invoice_details id on ih.id = id.invoice_id
                                inner join invoice_frequencies fr on fr.id = id.invoice_frequency_id
                                inner join invoice_payments ip on ip.invoice_id = ih.id
                                inner join payment_types pt on pt.id = ip.payment_type_id
                                inner join products p_sub on p_sub.id = id.product_id
                                inner join product_categories cat on cat.id = p_sub.product_category_id
                                inner join products p_main on p_main.id = p_sub.parent_id
                                inner join partners p on p.id = ih.partner_id
                                inner join partner_companies pc on pc.partner_id = p.id 
                                where ih.status = 'P' and ih.invoice_date >= '{$from->toDateString()}' and ih.invoice_date < '{$to->toDateString()}' and p.company_id = {$company_id}";

                    $records = DB::select(DB::raw($query));
                }else{
                    $query = "select ih.id,p.partner_id_reference,p.merchant_mid,pc.company_name,pt.name as payment_type,
                                DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as invoice_date,
                                (id.amount/id.quantity) as price,id.amount,id.quantity,
                                ins.description as status,fr.frequency,cat.name as category, p_main.name as main_product,
                                p_sub.name as sub_product,p.merchant_url as domain,
                                DATE_FORMAT(fr.bill_date, '%m/%d/%Y') as bill_date,p_sub.id as sub_id,p.id as partner_id,p.is_cc_client,id.cost
                                from invoice_headers ih
                                inner join invoice_statuses ins on ih.status = ins.code
                                inner join invoice_details id on ih.id = id.invoice_id
                                inner join invoice_frequencies fr on fr.id = id.invoice_frequency_id
                                inner join invoice_payments ip on ip.invoice_id = ih.id
                                inner join payment_types pt on pt.id = ip.payment_type_id
                                inner join products p_sub on p_sub.id = id.product_id
                                inner join product_categories cat on cat.id = p_sub.product_category_id
                                inner join products p_main on p_main.id = p_sub.parent_id
                                inner join partners p on p.id = ih.partner_id
                                inner join partner_companies pc on pc.partner_id = p.id 
                                where ih.status = 'P' and ih.invoice_date >= '{$from->toDateString()}' and ih.invoice_date < '{$to->toDateString()}'";

                    $records = DB::select(DB::raw($query));
                }

                foreach ($records as $rec) {
                    $row++;
                    $data = array($rec->id,$rec->partner_id_reference,$rec->company_name,$rec->payment_type,
                            $rec->invoice_date,$rec->amount,$rec->category,$rec->main_product,$rec->sub_product,$rec->domain,
                            $rec->frequency,$rec->status,$rec->bill_date);

                    $subData = array();
                    $cmd = "Select ic.*,ppt.name as partnerType,ppc.company_name as upline from invoice_commissions ic 
                            inner join partners pp on ic.partner_id = pp.id
                            inner join partner_types ppt on pp.partner_type_id = ppt.id
                            inner join partner_companies ppc on ppc.partner_id = pp.id
                            where ic.invoice_id = {$rec->id} and ic.product_id = {$rec->sub_id} order by ic.id";
                    $commissions = DB::select(DB::raw($cmd));     
                    foreach ($commissions as $comm) {
                        array_push($subData,$comm->upline.' ('.$comm->partnerType.')', $comm->totalCommission);
                    }

                    if(isset($commissions)){
                        $data = array_merge($data,$subData);
                        $sheet->row($row,$data);                        
                    }
                }


                $sheet->cell('A1:AA1', function($cell) {
                    $cell->setFontWeight('bold');
                });

            });
        })->export('xls');
    }



    public function report_commission(){
        $init = 1;
        return view("reports.report_commission",compact('init'));
    }

    public function commission_generate_report($from,$to){
        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);

        $orig_from = $from;
        $orig_to = $to;

        $from = new Carbon($from);
        $to = new Carbon($to);
        $to = $to->addDay();

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $company_id = auth()->user()->company_id;

        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $com = (strpos($reportaccess, 'commission report') === false) ? false : true;
        $com_export = (strpos($reportaccess, 'commission export') === false) ? false : true;
        if(!$com){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        if (strpos($admin_access, 'super admin access') === false){
            $query = "select p.id,p.partner_id_reference,pp.partner_id_reference as agent_ref,p.merchant_mid,pc.company_name,pt.name as payment_type,
                        DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as invoice_date,
                        (id.amount/id.quantity) as price,id.amount,id.quantity,
                        ins.description as status,fr.frequency,cat.name as category, p_main.name as main_product,
                        p_sub.name as sub_product,p.merchant_url as domain,
                        DATE_FORMAT(fr.bill_date, '%m/%d/%Y') as bill_date,p_sub.id as sub_id,p.id as partner_id,p.is_cc_client,id.cost,
                        ic.*,ppt.name as partnerType,ppc.company_name as upline,p.parent_id
                        from invoice_headers ih
                        inner join invoice_statuses ins on ih.status = ins.code
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join invoice_frequencies fr on fr.id = id.invoice_frequency_id
                        inner join invoice_payments ip on ip.invoice_id = ih.id
                        inner join payment_types pt on pt.id = ip.payment_type_id
                        inner join products p_sub on p_sub.id = id.product_id
                        inner join product_categories cat on cat.id = p_sub.product_category_id
                        inner join products p_main on p_main.id = p_sub.parent_id
                        inner join partners p on p.id = ih.partner_id
                        inner join partner_companies pc on pc.partner_id = p.id 
                        inner join invoice_commissions ic on ic.invoice_id = ih.id and id.product_id = ic.product_id and ic.directUpline = 1
                        inner join partners pp on p.parent_id = pp.id
                        inner join partner_types ppt on pp.partner_type_id = ppt.id
                        inner join partner_companies ppc on ppc.partner_id = pp.id
                        where ih.invoice_date >= '{$from->toDateString()}' and ih.invoice_date < '{$to->toDateString()}' and p.company_id = {$company_id} order by p.parent_id";

            $records = DB::select(DB::raw($query));
        }else{
            $query = "select p.id,p.partner_id_reference,pp.partner_id_reference as agent_ref,p.merchant_mid,pc.company_name,pt.name as payment_type,
                        DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as invoice_date,
                        (id.amount/id.quantity) as price,id.amount,id.quantity,
                        ins.description as status,fr.frequency,cat.name as category, p_main.name as main_product,
                        p_sub.name as sub_product,p.merchant_url as domain,
                        DATE_FORMAT(fr.bill_date, '%m/%d/%Y') as bill_date,p_sub.id as sub_id,p.id as partner_id,p.is_cc_client,id.cost,
                        ic.*,ppt.name as partnerType,ppc.company_name as upline,p.parent_id
                        from invoice_headers ih
                        inner join invoice_statuses ins on ih.status = ins.code
                        inner join invoice_details id on ih.id = id.invoice_id
                        inner join invoice_frequencies fr on fr.id = id.invoice_frequency_id
                        inner join invoice_payments ip on ip.invoice_id = ih.id
                        inner join payment_types pt on pt.id = ip.payment_type_id
                        inner join products p_sub on p_sub.id = id.product_id
                        inner join product_categories cat on cat.id = p_sub.product_category_id
                        inner join products p_main on p_main.id = p_sub.parent_id
                        inner join partners p on p.id = ih.partner_id
                        inner join partner_companies pc on pc.partner_id = p.id
                        inner join invoice_commissions ic on ic.invoice_id = ih.id and id.product_id = ic.product_id and ic.directUpline = 1
                        inner join partners pp on p.parent_id = pp.id
                        inner join partner_types ppt on pp.partner_type_id = ppt.id
                        inner join partner_companies ppc on ppc.partner_id = pp.id
                        where ih.invoice_date >= '{$from->toDateString()}' and ih.invoice_date < '{$to->toDateString()}' order by p.parent_id";

            $records = DB::select(DB::raw($query));
        }
        $commissions = array();
        $data = array();
        $currCompany = "";
        $prevCompany = "";
        $totalCommission = 0;
        foreach ($records as $rec) {
            $currCompany = $rec->upline;
            if($prevCompany != ""){
                if($prevCompany != $currCompany){
                    $data = array_merge($data,array($totalCommission));
                    $totalCommission = 0;
                    $commissions[] = $data;   
                }else{
                    $commissions[] = $data;   
                }    
            }
            $totalCommission = $totalCommission + $rec->totalCommission;
            $data = array($rec->partner_id_reference,$rec->company_name,$rec->agent_ref,$rec->upline.' ('.$rec->partnerType.')',$rec->sales,$rec->withoutMarkUp,$rec->withoutMarkUpCommission,$rec->markUp,$rec->markUpCommission,$rec->totalCommission);
            $prevCompany = $rec->upline;                    
        }

        $data = array_merge($data,array($totalCommission));
        $commissions[] = $data; 
        // dd($commissions);

        return view("reports.report_commission",compact('orig_from','orig_to','commissions','com_export'));
    }

    public function commission_export_report($from,$to){

        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $com_export = (strpos($reportaccess, 'commission export') === false) ? false : true;
        if(!$com_export){
            return redirect('/')->with('failed', 'You have no access to that page.');
        }

        $log = new ReportExportLog;
        $log->user_id = auth()->user()->id;
        $log->report_name = 'Commission Report';
        $log->save();

        $partner_id = auth()->user()->reference_id;
        $partner_id = Partner::get_downline_partner_ids($partner_id);

        $from = new Carbon($from);
        $to = new Carbon($to);
        $to = $to->addDay();
        $title = 'commission_report_'.strtolower(date('M', mktime(0, 0, 0, $from->month, 10))).'_'.$from->year;

        Excel::create($title, function($excel) use($from,$to,$partner_id){
            $excel->sheet('commission_report_'.strtolower(date('M', mktime(0, 0, 0, $from->month, 10))).'_output', function($sheet) use($from,$to,$partner_id){
                $header = array('Customer ID','Merchant','Agent ID','Agent','Sales','Without Markup','Commission %','Mark -up','Markup %','Total Commission');
                $row = 1;
                $sheet->row($row,$header);
                $userType = session('user_type_desc');
                $access = session('all_user_access');
                $admin_access = isset($access['admin']) ? $access['admin'] : "";
                $company_id = auth()->user()->company_id;
                if (strpos($admin_access, 'super admin access') === false){
                    $query = "select p.id,p.partner_id_reference,pp.partner_id_reference as agent_ref,p.merchant_mid,pc.company_name,pt.name as payment_type,
                                DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as invoice_date,
                                (id.amount/id.quantity) as price,id.amount,id.quantity,
                                ins.description as status,fr.frequency,cat.name as category, p_main.name as main_product,
                                p_sub.name as sub_product,p.merchant_url as domain,
                                DATE_FORMAT(fr.bill_date, '%m/%d/%Y') as bill_date,p_sub.id as sub_id,p.id as partner_id,p.is_cc_client,id.cost,
                                ic.*,ppt.name as partnerType,ppc.company_name as upline,p.parent_id
                                from invoice_headers ih
                                inner join invoice_statuses ins on ih.status = ins.code
                                inner join invoice_details id on ih.id = id.invoice_id
                                inner join invoice_frequencies fr on fr.id = id.invoice_frequency_id
                                inner join invoice_payments ip on ip.invoice_id = ih.id
                                inner join payment_types pt on pt.id = ip.payment_type_id
                                inner join products p_sub on p_sub.id = id.product_id
                                inner join product_categories cat on cat.id = p_sub.product_category_id
                                inner join products p_main on p_main.id = p_sub.parent_id
                                inner join partners p on p.id = ih.partner_id
                                inner join partner_companies pc on pc.partner_id = p.id 
                                inner join invoice_commissions ic on ic.invoice_id = ih.id and id.product_id = ic.product_id and ic.directUpline = 1
                                inner join partners pp on p.parent_id = pp.id
                                inner join partner_types ppt on pp.partner_type_id = ppt.id
                                inner join partner_companies ppc on ppc.partner_id = pp.id
                                where ih.invoice_date >= '{$from->toDateString()}' and ih.invoice_date < '{$to->toDateString()}' and p.company_id = {$company_id} order by p.parent_id";

                    $records = DB::select(DB::raw($query));
                }else{
                    $query = "select p.id,p.partner_id_reference,pp.partner_id_reference as agent_ref,p.merchant_mid,pc.company_name,pt.name as payment_type,
                                DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as invoice_date,
                                (id.amount/id.quantity) as price,id.amount,id.quantity,
                                ins.description as status,fr.frequency,cat.name as category, p_main.name as main_product,
                                p_sub.name as sub_product,p.merchant_url as domain,
                                DATE_FORMAT(fr.bill_date, '%m/%d/%Y') as bill_date,p_sub.id as sub_id,p.id as partner_id,p.is_cc_client,id.cost,
                                ic.*,ppt.name as partnerType,ppc.company_name as upline,p.parent_id
                                from invoice_headers ih
                                inner join invoice_statuses ins on ih.status = ins.code
                                inner join invoice_details id on ih.id = id.invoice_id
                                inner join invoice_frequencies fr on fr.id = id.invoice_frequency_id
                                inner join invoice_payments ip on ip.invoice_id = ih.id
                                inner join payment_types pt on pt.id = ip.payment_type_id
                                inner join products p_sub on p_sub.id = id.product_id
                                inner join product_categories cat on cat.id = p_sub.product_category_id
                                inner join products p_main on p_main.id = p_sub.parent_id
                                inner join partners p on p.id = ih.partner_id
                                inner join partner_companies pc on pc.partner_id = p.id
                                inner join invoice_commissions ic on ic.invoice_id = ih.id and id.product_id = ic.product_id and ic.directUpline = 1
                                inner join partners pp on p.parent_id = pp.id
                                inner join partner_types ppt on pp.partner_type_id = ppt.id
                                inner join partner_companies ppc on ppc.partner_id = pp.id
                                where ih.invoice_date >= '{$from->toDateString()}' and ih.invoice_date < '{$to->toDateString()}' order by p.parent_id";

                    $records = DB::select(DB::raw($query));
                }

                $data = array();
                $currCompany = "";
                $prevCompany = "";
                $totalCommission = 0;
                foreach ($records as $rec) {
                    $currCompany = $rec->upline;
                    if($prevCompany != ""){
                        if($prevCompany != $currCompany){
                            $row++;
                            $data = array_merge($data,array($totalCommission));
                            $totalCommission = 0;
                            $sheet->row($row,$data);   
                        }else{
                            $row++;
                            $sheet->row($row,$data); 
                        }    
                    }
                    $totalCommission = $totalCommission + $rec->totalCommission;
                    $data = array($rec->partner_id_reference,$rec->company_name,$rec->agent_ref,$rec->upline.' ('.$rec->partnerType.')',$rec->sales,$rec->withoutMarkUp,$rec->withoutMarkUpCommission,$rec->markUp,$rec->markUpCommission,$rec->totalCommission);
                    $prevCompany = $rec->upline;                    
                }

                $row++;
                $data = array_merge($data,array($totalCommission));
                $sheet->row($row,$data); 

                $sheet->cell('A1:AA1', function($cell) {
                    $cell->setFontWeight('bold');
                });

            });
        })->export('xls');
    }

    public function report_billing()
    {
        $parentId = auth()->user()->reference_id;
        if ($parentId == -1) {
            $merchant = Partner::whereIn('partner_type_id', [3,9])->get();
        } else {
            $merchant = Partner::whereIn('partner_type_id', [3,9])->where('parent_id', $parentId)->get();
        }
        
        return view('reports.report_billing', compact('merchant'));
    }

    public function getInvoiceList($id, $status)
    {
        if ($status == 'U') {
            $invoices = InvoiceHeader::where('partner_id',$id)->where('status', '<>', 'P')->get();
        } else {
            $invoices = InvoiceHeader::where('partner_id',$id)->where('status', 'P')->get();
        }

        foreach ($invoices as $i) {
            $invoiceDate = date("m/d/Y", strtotime($i->invoice_date));
            $dueDate = date("m/d/Y", strtotime($i->due_date));
            $total = $i->total_due . ' USD';
            $status = $i->status == 'P' ? '<span style="color:#27ae27">Paid</span>' : '<span style="color:#e71616">Unpaid</span>';

            $out_invoices[] = array(
                $i->reference,
                $invoiceDate,
                $dueDate,
                $total,
                $i->payment->type->name,
                $status,
            );    
        }

        return response()->json($out_invoices);   
    }

    public function export_report_billing_data($status)
    {
        $parentId = auth()->user()->reference_id;
        if ($parentId == -1) {
            $data = Partner::whereIn('partner_type_id', [3,9])->get();
        } else {
            $data = Partner::whereIn('partner_type_id', [3,9])->where('parent_id', $parentId)->get();
        }

        if ($status == 'all') {
            Excel::create('All_Billing_Status_Report', function($excel) use($data){
                $excel->sheet('All_Billing_Status_Report', function($sheet) use($data){
                    
                    $header = array('ID','Type','Business Name','Invoice Status');
                    $sheet->row(1,$header);
                    $row=2;
                    foreach($data as $d){
                        if($d->invoiceHeaders->whereInStrict('status', ['U','O','C','R','S','X','L','P'])->isNotEmpty()) {
                            $status = $d->invoiceHeaders->whereInStrict('status', ['U','O','C','R','S','X','L'])->isNotEmpty() ? 'Unpaid' : 'Paid' ;
                            $p = array(
                                $d->partner_id_reference,
                                $d->partner_type_id == 3 ? 'Merchant' : 'Branch',
                                $d->partnerCompany->company_name,
                                $status,
                            ); 
                            $sheet->row($row++,$p);
                        }
                    }
                });
            })->export('xls');
        } else if ($status == 'unpaid') {
            Excel::create('Unpaid_Billing_Status_Report', function($excel) use($data){
                $excel->sheet('Unpaid_Billing_Status_Report', function($sheet) use($data){
                    
                    $header = array('ID','Type','Business Name','Invoice Status');
                    $sheet->row(1,$header);
                    $row=2;
                    foreach($data as $d){
                        if($d->invoiceHeaders->whereInStrict('status', ['U','O','C','R','S','X','L'])->isNotEmpty()) {
                            $p = array(
                                $d->partner_id_reference,
                                $d->partner_type_id == 3 ? 'Merchant' : 'Branch',
                                $d->partnerCompany->company_name,
                                'Unpaid',
                            ); 
                            $sheet->row($row++,$p);
                        }
                    }
                });
            })->export('xls');
        } else if ($status == 'paid') {
            Excel::create('Paid_Billing_Status_Report', function($excel) use($data){
                $excel->sheet('Paid_Billing_Status_Report', function($sheet) use($data){
                    
                    $header = array('ID','Type','Business Name','Invoice Status');
                    $sheet->row(1,$header);
                    $row=2;
                    foreach($data as $d){
                        if(count($d->invoiceHeaders) > 0
                            && !$d->invoiceHeaders->whereInStrict('status', ['U','O','C','R','S','X','L'])->isNotEmpty()) {
                            $p = array(
                                $d->partner_id_reference,
                                $d->partner_type_id == 3 ? 'Merchant' : 'Branch',
                                $d->partnerCompany->company_name,
                                'Paid', 
                            ); 
                            $sheet->row($row++,$p);
                        }
                    }
                });
            })->export('xls');
        } 

    }

}
