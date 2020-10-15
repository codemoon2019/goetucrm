<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\Constant;
use App\Contracts\DashboardService;
use App\Models\Company;
use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoicePayment;
use App\Models\InvoiceFrequency;
use App\Models\Product;
use App\Models\Partner;
use App\Models\PartnerContact;
use App\Models\PartnerProduct;
use App\Models\PartnerType;
use App\Http\Resources\AdminResource;
use App\Http\Resources\AdminProductsResource;
use App\Http\Resources\AdminProductsBarResource;
use App\Http\Resources\AdminProductsInvoiceResource;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DB;
use Auth;
use App\Models\User;
use App\Models\UserType;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use DateTime;
use App\Models\Dashboard;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DashboardService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $partner_id = auth()->user()->reference_id;
        $partner_id = $partner_id == -1 ? $partner_id : Partner::get_downline_partner_ids($partner_id);
        $userType = session('user_type_desc');
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $partner_access = Partner::get_partners_access($partner_id);
        if ($partner_access==""){$partner_access=auth()->user()->reference_id;}

        $dashboard = isset($access['dashboard']) ? $access['dashboard'] : "";

        $dash_access = array(
            'leads_this_month' => strpos($dashboard, 'leads this month') !== false ? true : false,//User
            'merchant_by_agents' => strpos($dashboard, 'merchant by agents') !== false  ? true : false,//User
            'owner_dashboard' => strpos($dashboard, 'owner dashboard') !== false ? true : false ,//Owner
            'sales_per_agent' => strpos($dashboard, 'sales per agent') !== false  ? true : false,//User
            'task_completion_rate' => strpos($dashboard, 'task completion rate') !== false ? true : false,//User
            'task_list' => strpos($dashboard, 'task list') !== false ? true : false,//User
            'top_5_products' => strpos($dashboard, 'top 5 products') !== false  ? true : false,//User
            'yearly_revenue' => strpos($dashboard, 'yearly revenue') !== false  ? true : false ,//User
            'transaction_activity' => strpos($dashboard, 'transaction activity') !== false ? true : false,//Merchant
            'recent_sales' => strpos($dashboard, 'recent sales') !== false ? true : false,//Merchant
            'active_vs_closed_merchants' => strpos($dashboard, 'active vs closed merchants') !== false ? true : false, //Partner
            'merchants_enrollment' => strpos($dashboard, 'merchants enrollment') !== false ? true : false,//Partner
            'sales_trends' => strpos($dashboard, 'sales trends') !== false  ? true : false ,//Partner
            'sales_matrix' => strpos($dashboard, 'sales matrix') !== false ? true : false,//Partner
            'sales_profit' => strpos($dashboard, 'sales profit') !== false ? true : false,//Partner
            'incoming_leads_today' => strpos($dashboard, 'incoming leads today') !== false ? true : false,//Lead
            'total_leads' => strpos($dashboard, 'total leads') !== false ? true : false,//Lead
            'leads_payment_processor' => strpos($dashboard, 'leads payment processor') !== false ? true : false,//Lead
            'converted_leads' => strpos($dashboard, 'converted leads') !== false ? true : false,//Lead
            'converted_prospects' => strpos($dashboard, 'converted prospect') !== false ? true : false,//Lead
            'appointments_per_day' => strpos($dashboard, 'appointments per day') !== false ? true : false,//Lead
        );

        $salesPerAgent = ($dash_access['sales_per_agent'] == true) ? Dashboard::sales_per_agent($partner_id) : "";
        $salesYTD = ($dash_access['top_5_products'] == true) ? Dashboard::top_5_products($partner_id) : "";
        $leadInfo = ($dash_access['leads_this_month'] == true) ? Dashboard::leads_this_month($partner_id) : "";
        $revenue = ($dash_access['yearly_revenue'] == true) ? Dashboard::yearly_revenue($partner_id) : "";
        $merchants = ($dash_access['merchant_by_agents'] == true) ? Dashboard::merchant_by_agents($partner_id,$partner_access) : "";
        $tasks =($dash_access['task_list'] == true) ? Dashboard::task_list(auth()->user()->id) : "";
        $taskSummary=($dash_access['task_completion_rate'] == true) ? Dashboard::task_completion_rate(auth()->user()->id) : "";
        $ownerData=($dash_access['owner_dashboard'] == true) ? Dashboard::owner_dashboard($this->service) : "";
        $recentInvoice=($dash_access['recent_sales'] == true) ? Dashboard::recent_sales(auth()->user()->reference_id) : "";

        $user = auth()->user();
        return view('admin.home',compact('user','dash_access','ownerData','salesPerAgent','salesYTD','leadInfo','revenue','merchants','tasks','taskSummary','recentInvoice'));
    }


    public function merchantIndex()
    {
        // return $this->adminIndex();
        $merchantId = auth()->user()->reference_id;
        $cmd = "select DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as salesDate,sum(id.amount) as totalSale  from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    where ih.partner_id = {$merchantId}
                    group by ih.invoice_date
                    order by ih.invoice_date desc limit 5";
        $recentInvoice = DB::select(DB::raw($cmd));
        return view('admin.homeMerchant',compact('merchantId','recentInvoice'));
    }

    public function getMerchantDashData($info,Request $request)
    {
        $merchantId = auth()->user()->reference_id;
        $from = $request->from;
        $to = $request->to;

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if($info == 'periodic_sales'){
            $status = ($request->filter != 'A') ? " and ih.status = '{$request->filter }'" : "";
            $addQry = " and ih.partner_id = {$merchantId} ";
            if (strpos($admin_access, 'super admin access') !== false){
                $addQry = "";
            }

            $cmd = "select DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as salesDate,sum(id.amount) as InvoiceAmount from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    where ih.invoice_date >= '{$from}'
                    and ih.invoice_date <= '{$to}' {$addQry} {$status}
                    group by ih.invoice_date
                    order by ih.invoice_date";

            $sales = DB::select(DB::raw($cmd));
            return Array('data' => $sales);             
        }        


        if($info == 'product_sales'){
            $addQry = " po.partner_id = {$merchantId} and ";
            if (strpos($admin_access, 'super admin access') !== false){
                $addQry = "";
            }

            $cmd = "select p.name as main,p2.name as sub,sum(pod.quantity) as qty from product_orders po
                    inner join product_order_details pod on po.id = pod.order_id
                    inner join products p on p.id = po.product_id
                    inner join products p2 on p2.id = pod.product_id
                    where {$addQry} po.updated_at >= '{$from}'
                    and po.updated_at <= '{$to}'  
                    group by p.name,p2.name
                    order by p.name";

            $sales = DB::select(DB::raw($cmd));
            return Array('data' => $sales);             
        }  

    }

    public function getInvoiceData()
    {
        $id = auth()->user()->reference_id; 
        $out=array();

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') !== false){
            $invoice = InvoiceHeader::orderBy('invoice_date','desc')->get();
        }else{
            $invoice = InvoiceHeader::where('partner_id',$id)->orderBy('invoice_date','desc')->get();
        }

        foreach ($invoice as $p) {
            if($p->status_code->description == 'Paid'){
                $status='<span style="color:green">'.$p->status_code->description.'</span>';
            }else{
                $status='<span style="color:red">'.$p->status_code->description.'</span>';
            }
            $out[] = array(
                '<a href="javascript:void(0);" onclick="getInvoiceInfo('.$p->id.')">'.$p->id.'</a>',
                $p->reference,
                date_format(new DateTime($p->invoice_date),"m/d/Y"),
                date_format(new DateTime($p->due_date),"m/d/Y"),
                $p->total_due.' USD',
                $p->payment->type->name,
                $status
            );    
        }
        return response()->json($out);  
    }

    public function getInvoiceDetails($id)
    {
        $pid = auth()->user()->reference_id; 
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') !== false){
            $invoice = InvoiceHeader::where('id',$id)->first();
        }else{
            $invoice = InvoiceHeader::where('partner_id',$pid)->where('id',$id)->first();
        }

        foreach($invoice->details as $detail)
        {
            if($detail->product_id == -1){
                $detail->productname = $detail->description;
                $detail->category = "Not Applicable"; 
            }else{
                $detail->productname = $detail->product->name;
                $detail->category = $detail->product->category->name;          
            }
        }
        $invoice->payment_id = $invoice->payment->payment_type_id;
        $invoice->amount_paid = $invoice->payment->payment_amount == null ? 0.00 : $invoice->payment->payment_amount;
        $invoice->pending_payment = $invoice->total_due - $invoice->amount_paid;
        $invoice->amount_paid = number_format($invoice->amount_paid,2,".","");
        $invoice->pending_payment = number_format($invoice->pending_payment,2,".","");
        $invoice->invoice_date = date_format(new DateTime($invoice->invoice_date),"m/d/Y");
        $invoice->due_date = date_format(new DateTime($invoice->due_date),"m/d/Y");
        $invoice->statusDesc = $invoice->status_code->description;
        $invoice->paymentType =  $invoice->payment->type->name;

        $invoice->merchant = $invoice->partner->partner_contact()->first_name.' '. $invoice->partner->partner_contact()->middle_name .' '.$invoice->partner->partner_contact()->last_name;
        return $invoice;
    }


    public function partnerIndex()
    {
        $partnerId = auth()->user()->reference_id;
        return view('admin.homePartner',compact('partnerId'));
    }

    public function getInvoiceVolumeData()
    {
        $id = auth()->user()->reference_id; 
        $out=array();
        $partner_access = Partner::get_partners_access($id); 
        $cmd = "select pc.company_name,
                ifnull((select ih1.total_due from invoice_headers ih1
                inner join partners pt on pt.id = ih1.partner_id where pt.id = p.id order by ih1.invoice_date desc limit 1),0) as Recent,
                (select ifnull(sum(ih1.total_due),0) from invoice_headers ih1
                inner join partners pt on pt.id = ih1.partner_id where pt.id = p.id and ih1.invoice_date >= DATE(NOW()-INTERVAL 1 MONTH)) as MTD,
                (select ifnull(sum(ih1.total_due),0) from invoice_headers ih1
                inner join partners pt on pt.id = ih1.partner_id where pt.id = p.id and ih1.invoice_date >= DATE(NOW()-INTERVAL 1 YEAR)) as YTD
                from partners p 
                inner join partner_companies pc on p.id = pc.partner_id
                where p.partner_type_id = 3  and billing_status = 'Active' and
                    p.id in({$partner_access}) 
                order by pc.company_name";

        $sales = DB::select(DB::raw($cmd));
        foreach ($sales as $p) {
            $out[] = array(
                $p->company_name,
                '$ '.$p->Recent,
                '$ '.$p->MTD,
                '$ '.$p->YTD
            );    
        }
        return response()->json($out);  
    }

    public function getPartnerDashData($info,Request $request)
    {
        $id = auth()->user()->reference_id;      
        $partner_access = Partner::get_partners_access($id);   

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if($info == 'merchant_boarding_comparison'){
            $cmd = "select sum(case when status = 'A' Then 1 else 0 end) as Active,
                    sum(case when status = 'I' Then 1 else 0 end) as Inactive,
                    sum(case when status = 'V' Then 1 else 0 end) as Cancelled,
                    sum(case when status = 'T' Then 1 else 0 end) as 'Terminated',
                    DATE_FORMAT(updated_at, '%b') AS boardMonth,DATE_FORMAT(updated_at, '%Y/%m') as dt
                     from partners where partner_type_id = 3 and updated_at >= DATE(NOW()-INTERVAL 1 YEAR) and
                    id in({$partner_access}) 
                    group by DATE_FORMAT(updated_at, '%b'),DATE_FORMAT(updated_at, '%Y/%m')
                    order by  dt";

            $sales = DB::select(DB::raw($cmd));
            return Array('data' => $sales);             
        }  

        if($info == 'merchant_boarding_pie'){
            $cmd = "select sum(case when status = 'A' Then 1 else 0 end) as Active,
                    sum(case when status = 'I' Then 1 else 0 end) as Inactive,
                    sum(case when status = 'V' Then 1 else 0 end) as Cancelled,
                    sum(case when status = 'T' Then 1 else 0 end) as 'Terminated',
                    sum(1) as Total from partners 
                    where partner_type_id = 3 and 
                    id in({$partner_access})";
            // $sales = DB::select(DB::raw($cmd));
            $sales = collect(\DB::select(DB::raw($cmd)))->first();

            return Array('data' => $sales);             
        }  


        if($info == 'periodic_sales'){
            $from = $request->from;
            $to = $request->to;
            $status = ($request->filter != 'A') ? " and ih.status = '{$request->filter }'" : "";
            $cmd = "select DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as salesDate,sum(id.amount) as InvoiceAmount from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    inner join partners p on p.id = ih.partner_id
                    where ih.invoice_date >= '{$from}'
                    and ih.invoice_date <= '{$to}' and p.partner_type_id = 3 and ih.partner_id in({$partner_access}) {$status}
                    group by ih.invoice_date
                    order by ih.invoice_date";

            $sales = DB::select(DB::raw($cmd));
            return Array('data' => $sales);             
        } 

        if($info == 'sales_profit'){
            $from = $request->from;
            $to = $request->to;
            $status = ($request->filter != 'A') ? " and ih.status = '{$request->filter }'" : "";
            $addQry = " and p.parent_id = {$id} ";
            if (strpos($admin_access, 'super admin access') !== false){
                $addQry = "";
            }

            $cmd = "select ic.product_id,pr.name,sum(case ic.directUpline when 1 then (ic.sales - ic.withoutMarkUp - totalCommission)
                    else (ic.sales - ic.withoutMarkUp - totalCommission) end) as profit from invoice_commissions ic
                    inner join invoice_headers ih on ih.id = ic.invoice_id
                    inner join partners p on p.id = ic.partner_id
                    inner join products pr on pr.id = ic.product_id
                    where ih.invoice_date >= '{$from}'
                    and ih.invoice_date <= '{$to}' {$addQry}
                    group by ic.product_id,pr.name";

            $sales = DB::select(DB::raw($cmd));
            return Array('data' => $sales);             
        } 

        if($info == 'incoming_leads_today'){
            $partner_id = auth()->user()->reference_id;
            $partner_id = $partner_id == -1 ? $partner_id : Partner::get_downline_partner_ids($partner_id);
            $userType = session('user_type_desc');
            $access = session('all_user_access');
            $admin_access = isset($access['admin']) ? $access['admin'] : "";
            $partner_access = Partner::get_partners_access($partner_id);
            if ($partner_access==""){$partner_access=auth()->user()->reference_id;}

            $addQry = "and l.partner_id in({$partner_access})";
            if (strpos($admin_access, 'super admin access') !== false){
                $addQry = "";
            }

            $cmd = "select concat(pc_assignee.first_name,' ',pc_assignee.last_name) as assignee,count(l.assigned_id) as total from incoming_leads l
                    left join partner_contacts pc_assignee on pc_assignee.partner_id=l.assigned_id and pc_assignee.is_original_contact=1
                    where l.status in('N','E')  {$addQry} 
                    group by l.assigned_id,pc_assignee.first_name,pc_assignee.last_name";


            $leads = DB::select(DB::raw($cmd));
            return Array('data' => $leads);             
        } 


        if($info == 'total_leads'){
            $start = date_format(new DateTime($request->startDate),"Y-m-d");
            $end = date_format(new DateTime($request->endDate),"Y-m-d");
            $partner_id = auth()->user()->reference_id;
            $partner_id = $partner_id == -1 ? $partner_id : Partner::get_downline_partner_ids($partner_id);
            $userType = session('user_type_desc');
            $access = session('all_user_access');
            $admin_access = isset($access['admin']) ? $access['admin'] : "";
            $partner_access = Partner::get_partners_access($partner_id);
            if ($partner_access==""){$partner_access=auth()->user()->reference_id;}

            $addQry = "and id in({$partner_access})";
            if (strpos($admin_access, 'super admin access') !== false){
                $addQry = "";
            }

            $cmd = "select DATE_FORMAT(created_at,'%b') as lead,DATE_FORMAT(created_at,'%Y/%m') as dt,count(id) as total from partners 
                        where (partner_type_id = 6 or original_partner_type_id = 6) {$addQry} and 
                        created_at >= '{$start}' and created_at < '{$end} 23:59:59'

                        group by DATE_FORMAT(created_at,'%b') ,DATE_FORMAT(created_at,'%Y/%m') order by dt";

            $leads = DB::select(DB::raw($cmd));
            return Array('data' => $leads);             
        } 

        if($info == 'payment_processor'){
            $start = date_format(new DateTime($request->startDate),"Y-m-d");
            $end = date_format(new DateTime($request->endDate),"Y-m-d");
            $partner_id = auth()->user()->reference_id;
            $partner_id = $partner_id == -1 ? $partner_id : Partner::get_downline_partner_ids($partner_id);
            $userType = session('user_type_desc');
            $access = session('all_user_access');
            $admin_access = isset($access['admin']) ? $access['admin'] : "";
            $partner_access = Partner::get_partners_access($partner_id);
            if ($partner_access==""){$partner_access=auth()->user()->reference_id;}

            $addQry = "and id in({$partner_access})";
            if (strpos($admin_access, 'super admin access') !== false){
                $addQry = "";
            }

            $cmd = "select merchant_processor as name,count(id) as total  from partners 
                    where IFNULL(merchant_processor,'') <> '' {$addQry} and 
                    created_at >= '{$start}' and created_at < '{$end} 23:59:59'
                    group by merchant_processor";

            $leads = DB::select(DB::raw($cmd));
            return Array('data' => $leads);             
        } 

        if($info == 'converted_leads'){
            $start = date_format(new DateTime($request->startDate),"Y-m-d");
            $end = date_format(new DateTime($request->endDate),"Y-m-d");
            $partner_id = auth()->user()->reference_id;
            $partner_id = $partner_id == -1 ? $partner_id : Partner::get_downline_partner_ids($partner_id);
            $userType = session('user_type_desc');
            $access = session('all_user_access');
            $admin_access = isset($access['admin']) ? $access['admin'] : "";
            $partner_access = Partner::get_partners_access($partner_id);
            if ($partner_access==""){$partner_access=auth()->user()->reference_id;}

            $addQry = "and p.parent_id in({$partner_access})";
            if (strpos($admin_access, 'super admin access') !== false){
                $addQry = "";
            }

            $cmd = "select concat(pc.first_name,' ',pc.last_name) as assignee, 
                    SUM(CASE partner_type_id WHEN 6 THEN 1 ELSE 0 END) as leads,
                    SUM(CASE WHEN partner_type_id = 8 AND IFNULL(original_partner_id_reference,'') <> '' THEN 1 ELSE 0 END) as prospect
                    from partners p
                    inner join partner_contacts pc on pc.partner_id=p.parent_id and pc.is_original_contact=1
                    where ((p.partner_type_id = 8 and IFNULL(original_partner_id_reference,'') <> '') or partner_type_id = 6) {$addQry} and 
                    p.updated_at >= '{$start}' and p.updated_at < '{$end} 23:59:59'
                    group by concat(pc.first_name,' ',pc.last_name)";

            $leads = DB::select(DB::raw($cmd));
            return Array('data' => $leads);             
        } 


        if($info == 'converted_prospects'){
            $start = date_format(new DateTime($request->startDate),"Y-m-d");
            $end = date_format(new DateTime($request->endDate),"Y-m-d");
            $partner_id = auth()->user()->reference_id;
            $partner_id = $partner_id == -1 ? $partner_id : Partner::get_downline_partner_ids($partner_id);
            $userType = session('user_type_desc');
            $access = session('all_user_access');
            $admin_access = isset($access['admin']) ? $access['admin'] : "";
            $partner_access = Partner::get_partners_access($partner_id);
            if ($partner_access==""){$partner_access=auth()->user()->reference_id;}

            $addQry = "and p.parent_id in({$partner_access})";
            if (strpos($admin_access, 'super admin access') !== false){
                $addQry = "";
            }

            $cmd = "select concat(pc.first_name,' ',pc.last_name) as assignee, 
                    SUM(CASE partner_type_id WHEN 8 THEN 1 ELSE 0 END) as prospect,
                    SUM(CASE WHEN partner_type_id = 3 AND IFNULL(original_partner_id_reference,'') <> '' THEN 1 ELSE 0 END) as merchant
                    from partners p
                    inner join partner_contacts pc on pc.partner_id=p.parent_id and pc.is_original_contact=1
                    where ((p.partner_type_id = 3 and IFNULL(original_partner_id_reference,'') <> '') or partner_type_id = 8) {$addQry} and 
                    p.updated_at >= '{$start}' and p.updated_at < '{$end} 23:59:59'
                    group by concat(pc.first_name,' ',pc.last_name)";

            $leads = DB::select(DB::raw($cmd));
            return Array('data' => $leads);             
        } 

        if($info == 'appointments_per_day'){
            $start = date_format(new DateTime($request->startDate),"Y-m-d");
            $end = date_format(new DateTime($request->endDate),"Y-m-d");
            $partner_id = auth()->user()->reference_id;
            $partner_id = $partner_id == -1 ? $partner_id : Partner::get_downline_partner_ids($partner_id);
            $userType = session('user_type_desc');
            $access = session('all_user_access');
            $admin_access = isset($access['admin']) ? $access['admin'] : "";
            $partner_access = Partner::get_partners_access($partner_id);
            if ($partner_access==""){$partner_access=auth()->user()->reference_id;}

            $addQry = "and p.parent_id in({$partner_access})";
            if (strpos($admin_access, 'super admin access') !== false){
                $addQry = "";
            }

            $cmd = "select DATE_FORMAT(ca.start_date,'%b') as dtMonth,DATE_FORMAT(ca.start_date,'%Y/%m') as dt,count(id) as total from calendar_activities ca
                    where ca.start_date >= '{$start}' and ca.start_date < '{$end} 23:59:59' {$addQry} 
                    group by DATE_FORMAT(ca.start_date,'%b') ,DATE_FORMAT(ca.start_date,'%Y/%m') order by dt";

            $leads = DB::select(DB::raw($cmd));
            return Array('data' => $leads);             
        } 


    }


    public function companySales($id)
    {
        $company = Partner::find($id);
        return $company->company_sales($id);
    }

    public function getMerchants($id)
    {
        $cmd = "select p.id,pc.company_name,pt.email from partners p 
                inner join partner_companies pc on pc.partner_id = p.id
                inner join partner_contacts pt on pt.partner_id = p.id and pt.is_original_contact = 1
                where p.parent_id = {$id} and p.partner_type_id = 3
                order by pc.company_name desc"; 
        $merchants = DB::select(DB::raw($cmd));
        return $merchants;    
    }

    public function getPartnerTypes()
    {
        return response()->json(PartnerType::where('status', Constant::DEFAULT_STATUS_ACTIVE)->where("name", '<>', "Company")->get());
    }

    public function getDashData($info,Request $request)
    {
        $companyId = $request->companyId;
        $month = $request->month;
        $year = $request->year;

        if($info == 'top_partners'){
            $cmd = "select agent_c.id,agent_c.company_name as fullName,sum(id.amount) as totalSale  from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    inner join products pr on pr.id = id.product_id
                    inner join partners p on p.id = ih.partner_id
                    inner join partners agent on agent.id = p.parent_id
                    inner join partner_companies agent_c on agent.id = agent_c.partner_id
                    where ih.status = 'P' and MONTH(ih.invoice_date) = {$month}
                    and YEAR(ih.invoice_date) = {$year} and p.company_id = {$companyId}
                    group by agent_c.id,agent_c.company_name
                    order by sum(id.amount) desc";

            $partners = DB::select(DB::raw($cmd));
            return Array('data' => $partners);             
        }

        if($info == 'top_products'){
            $companyId = $request->companyId;
            $month = $request->month;
            $year = $request->year;

            $cmd = "select p_main.name,sum(id.amount) as totalSale  from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    inner join products p on p.id = id.product_id
                    inner join products p_main on p.parent_id = p_main.id
                    inner join partners pt on pt.id = ih.partner_id
                    where ih.status = 'P' and MONTH(ih.invoice_date) = {$month}
                    and YEAR(ih.invoice_date) = {$year} and pt.company_id = {$companyId}
                    group by p_main.name
                    order by sum(id.amount) desc";

            $productSale = DB::select(DB::raw($cmd));
            return Array('data' => $productSale); 
        }

        if($info == 'top_products_bar'){
            $cmd = "select p_main.id, p_main.name,sum(id.amount) as totalSale,CONCAT_WS('-',MONTH(ih.invoice_date),YEAR(ih.invoice_date)) as monthyear  
            from invoice_headers ih
            inner join invoice_details id on ih.id = id.invoice_id
            inner join products p on p.id = id.product_id
            inner join products p_main on p.parent_id = p_main.id
            inner join partners pt on pt.id = ih.partner_id
            where ih.status = 'P' and MONTH(ih.invoice_date) = {$month}
            and YEAR(ih.invoice_date) = {$year} and pt.company_id = {$companyId}
            group by p_main.id, p_main.name,CONCAT_WS('-',MONTH(ih.invoice_date),YEAR(ih.invoice_date))
            order by sum(id.amount) desc";

            $productSale = DB::select(DB::raw($cmd));
            return Array('data' => $productSale);
        }

        if($info == 'top_partners_bar'){
            $cmd = "select agent_c.id,agent_c.company_name as name,sum(id.amount) as totalSale,CONCAT_WS('-',MONTH(ih.invoice_date),YEAR(ih.invoice_date)) as monthyear  
            from invoice_headers ih
            inner join invoice_details id on ih.id = id.invoice_id
            inner join products pr on pr.id = id.product_id
            inner join partners p on p.id = ih.partner_id
            inner join partners agent on agent.id = p.parent_id
            inner join partner_companies agent_c on agent.id = agent_c.partner_id
            where ih.status = 'P' and MONTH(ih.invoice_date) = {$month}
            and YEAR(ih.invoice_date) = {$year} and p.company_id = {$companyId}
            group by agent_c.id,agent_c.company_name,CONCAT_WS('-',MONTH(ih.invoice_date),YEAR(ih.invoice_date))
            order by sum(id.amount) desc";

            $partnerSale = DB::select(DB::raw($cmd));
            return Array('data' => $partnerSale);
        }

    }

    public function getProductReceipts()
    {

    }

    public function getPartnerReceipts(Request $request)
    {
        $partnerId = $request->partnerId;
        //$invoiceDetails = Partner::where('id', $partnerId)->with('invoiceDetails')->get();
        $invoiceDetails = $this->service->fetchPartnerSale($partnerId);
        return response()->json($invoiceDetails);
    }

    public function userProfile()
    {
        $user = User::find(Auth::user()->id);
        $departments = User::getDepartmentsByID(Auth::user()->user_type_id);
        $countries = Country::where('status','A')/* ->orderBy('name','asc') */->get();
        return view('profile.edit',compact('departments','user','countries'));            
    }

    public function userProfileUpdate($id,Request $request)
    {

        $this->validate($request,[
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email_address' => "required_without:mobile_number|nullable|string|email|max:255|unique:users,email_address,{$id}",
                'mobile_number' => "required_without:email_address|nullable|string|max:255|unique:users,mobile_number,{$id}",
                'direct_office_number' => 'nullable',
                'direct_office_number_extension' => 'required_with:direct_office_number',
                'dob' => 'required',
        ]);

        $dob = date("Y-m-d", strtotime($request->input('dob')));
        $country = Country::where('name',$request->input('txtCountry'))->first();
        $user = User::find($id);

        $requireRelog = false;
        if($user->mobile_number != $request->input('mobile_number')){
            $user->is_verified_mobile = 0;
            $requireRelog = true;
        }

        if($user->email_address != $request->input('email_address')){
            $user->is_verified_email = 0;
            $requireRelog = true;
        }
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email_address = $request->input('email_address');
        $user->mobile_number = $request->input('mobile_number');
        $user->dob = $dob;
        $user->business_phone1 = $request->input('direct_office_number');
        $user->extension = $request->input('direct_office_number_extension');
        $user->country = $request->input('txtCountry');
        $user->country_code = $country->country_calling_code;
        $user->updated_at = date('Y-m-d H:i:s');
        $user->update_by = auth()->user()->username;

        if ($request->hasFile("profileImage")) {
            $attachment = $request->file('profileImage');
            $fileName = pathinfo($attachment->getClientOriginalName(),PATHINFO_FILENAME);
            $extension = $attachment->getClientOriginalExtension();
            $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
            $storagePath = Storage::disk('public')->putFileAs('user_profile', $attachment,  $filenameToStore, 'public');
            $user->image = '/storage/user_profile/'.$filenameToStore;
        }

        $user->save();

        if ( isset($user->reference_id) ) {
            if($user->department->create_by=='SYSTEM')
            {
                $partner_contact = PartnerContact::where([
                        ['partner_id', $user->reference_id],
                        ['is_original_contact', '1']
                    ])->first();

                if ( isset($partner_contact) ) {
                    $partner_contact->first_name        = $request->input('first_name');
                    $partner_contact->last_name         = $request->input('last_name');
                    $partner_contact->country           = $request->input('txtCountry');
                    $partner_contact->email             = $request->input('email_address');
                    $partner_contact->mobile_number     = $request->input('mobile_number');
                    $partner_contact->dob               = $dob;
                    $partner_contact->save();
                }
            }
        }
        if($requireRelog){
            return redirect('/logout');
        }else{
            return redirect('/user-profile')->with('success','User updated');
        }   
    }
}
