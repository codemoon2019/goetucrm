<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Dashboard extends Model
{
    public static function sales_per_agent($partner_id)
    {
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $addQry = " ih.partner_id in({$partner_id}) and ";
        if (strpos($admin_access, 'super admin access') !== false){
            $addQry = "";
        }

        $cmd = "select pc.company_name,pc.partner_id,sum(ih.total_due) as total from invoice_headers ih
                inner join partner_companies pc on ih.partner_id = pc.partner_id
                where {$addQry} ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL 0 DAY
                and ih.invoice_date >= NOW() - INTERVAL 60 DAY
                group by pc.company_name,pc.partner_id
                order by total desc
                limit 5"; 

        $agentSales = DB::select(DB::raw($cmd));
        $salesPerAgent = "";
        foreach($agentSales as $agent )
        {
            $salesPerAgent .= '{
                        type: "spline",
                        showInLegend: true,
                        yValueFormatString: "##.00m",
                        name: "'.$agent->company_name.'",
                        dataPoints: [';
            for( $i = 60 ; $i >= 0 ; $i-- )
            {
                $x = $i + 1;
                $cmd = "select pc.company_name,sum(ih.total_due) as total from invoice_headers ih
                        inner join partner_companies pc on ih.partner_id = pc.partner_id
                        where ih.status = 'P' and ih.invoice_date <= NOW() - INTERVAL {$i} DAY
                        and ih.invoice_date >= NOW() - INTERVAL {$x} DAY
                        and ih.partner_id = {$agent->partner_id}
                        group by pc.company_name";
                $result = collect(DB::select(DB::raw($cmd)))->first();
                if(isset($result)){
                    $salesPerAgent .=  '{ label: "'.$i.'", y: '.$result->total .' },';
                }else{
                    $salesPerAgent .=  '{ label: "'.$i.'", y: 0.00 },';
                }
            }

            $salesPerAgent .= ']},';            
        }
        return $salesPerAgent;
    }

    public static function top_5_products($partner_id)
    {

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $addQry = " ih.partner_id in({$partner_id}) and ";
        if (strpos($admin_access, 'super admin access') !== false){
            $addQry = "";
        }

        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $cmd = "select p_main.id,p_main.name,sum(id.amount) as total from invoice_headers ih
                inner join invoice_details id on ih.id = id.invoice_id
                inner join products p on p.id = id.product_id
                inner join products p_main on p.parent_id = p_main.id
                where {$addQry} ih.status = 'P' and YEAR(ih.invoice_date) = {$year}
                group by p_main.id,p_main.name order by total desc limit 5"; 

        $salesYTD = DB::select(DB::raw($cmd));
        $prevYear = $year-1;
        foreach($salesYTD as $sales )
        {
            $cmd = "select p_main.name,sum(id.amount) as total from invoice_headers ih
                inner join invoice_details id on ih.id = id.invoice_id
                inner join products p on p.id = id.product_id
                inner join products p_main on p.parent_id = p_main.id
                where {$addQry} p_main.id = {$sales->id} and ih.status = 'P' and YEAR(ih.invoice_date) = {$prevYear}
                group by p_main.name"; 

            $result = collect(DB::select(DB::raw($cmd)))->first();
            if(isset($result)){
                $sales->prev_total = $result->total;
            }else{
                $sales->prev_total = '0.00';
            }

            $cmd = "select p_main.name,sum(id.amount) as total from invoice_headers ih
                inner join invoice_details id on ih.id = id.invoice_id
                inner join products p on p.id = id.product_id
                inner join products p_main on p.parent_id = p_main.id
                where {$addQry} p_main.id = {$sales->id} and ih.status = 'P' and YEAR(ih.invoice_date) = {$prevYear} and MONTH(ih.invoice_date) = {$month}
                group by p_main.name"; 
            $prevSales = collect(DB::select(DB::raw($cmd)))->first();
            $prev = isset($prevSales) ? floatval($prevSales->total) : 0;
           
            $cmd = "select p_main.name,sum(id.amount) as total from invoice_headers ih
                inner join invoice_details id on ih.id = id.invoice_id
                inner join products p on p.id = id.product_id
                inner join products p_main on p.parent_id = p_main.id
                where {$addQry} p_main.id = {$sales->id} and ih.status = 'P' and YEAR(ih.invoice_date) = {$year} and MONTH(ih.invoice_date) = {$month}
                group by p_main.name"; 
            $currSales = collect(DB::select(DB::raw($cmd)))->first();
            $curr = isset($currSales) ? floatval($currSales->total) : 0;

            if($curr > $prev){
                $sales->name = '<i class="fa fa-arrow-up" style="color:green"></i> '.$sales->name;
            }
            if($curr == $prev){
                $sales->name = '<i class="fa fa-minus" style="color:orange"></i> '.$sales->name;
            }
            if($curr < $prev){

                $sales->name = '<i class="fa fa-arrow-down" style="color:red"></i> '.$sales->name;
            }
        }

        return $salesYTD;
	}

    public static function leads_this_month($partner_id)
    {

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $addQry = " id in({$partner_id}) and ";
        if (strpos($admin_access, 'super admin access') !== false){
            $addQry = "";
        }

    	$year = Carbon::now()->format('Y');
    	$month = Carbon::now()->format('m');
        $cmd = "select count(id) as total from partners where {$addQry} MONTH(created_at) = {$month} and YEAR(created_at) = {$year} and is_lead = 1";
        $currLead = collect(DB::select(DB::raw($cmd)))->first();
        $currLead = isset($currLead) ? floatval($currLead->total) : 0;

        $prevYear = Carbon::now()->subMonth()->format('Y');
        $prevMonth = Carbon::now()->subMonth()->format('m');
        $cmd = "select count(id) as total from partners where {$addQry} MONTH(created_at) = {$prevMonth} and YEAR(created_at) = {$prevYear} and is_lead = 1";
        $prevLead = collect(DB::select(DB::raw($cmd)))->first();
        $prevLead = isset($prevLead) ? floatval($prevLead->total) : 0;

        $cmd = "select count(id) as total from partners where {$addQry} YEAR(created_at) = {$prevYear} and is_lead = 1";
        $avgLead = collect(DB::select(DB::raw($cmd)))->first();
        $avgLead = isset($avgLead) ? floatval($avgLead->total) : 0;
        $avgLead = number_format((float)($avgLead / 12), 2, '.', '');

        $today = Carbon::now()->format('Y-m-d');
        $cmd = "select count(id) as total from partners where {$addQry} created_at >= '{$today}' and is_lead = 1";
        $todayLead = collect(DB::select(DB::raw($cmd)))->first();
        $todayLead = isset($todayLead) ? floatval($todayLead->total) : 0;

        $leadInfo = Array(
            'currLead' => $currLead,
            'prevLead' => $prevLead,
            'avgLead' => $avgLead,
            'todayLead' => $todayLead,
        );

        return $leadInfo;
    }

    public static function yearly_revenue($partner_id)
    {
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $year = Carbon::now()->format('Y');
        $prevYear = $year-1;
        $yearlyRevenue = "";
        $yearlyRevenuePrev = "";

        if (strpos($admin_access, 'super admin access') !== false){
            
            for( $i = 1 ; $i <= 12 ; $i++ )
            {
                $m = $i-1;
                $cmd = "select sum(ih.total_due) as total from invoice_headers ih
                        where ih.status = 'P' and MONTH(ih.invoice_date) = {$i} and YEAR(ih.invoice_date) = {$year}";
                $result = collect(DB::select(DB::raw($cmd)))->first();
                $result = isset($result) ? floatval($result->total) : 0;
               
                $yearlyRevenue .= "{ x: new Date(2017, {$m}, 1), y: {$result} },";
                $cmd = "select sum(ih.total_due) as total from invoice_headers ih
                        where ih.status = 'P' and MONTH(ih.invoice_date) = {$i} and YEAR(ih.invoice_date) = {$prevYear}";
                $result = collect(DB::select(DB::raw($cmd)))->first();
                $result = isset($result) ? floatval($result->total) : 0;
                $m = $i-1;
                $yearlyRevenuePrev .= "{ x: new Date(2017, {$m}, 1), y: {$result} },";
            }

        }else{

            for( $i = 1 ; $i <= 12 ; $i++ )
            {
                $m = $i-1;
                $cmd = "select sum(ih.total_due) as total from invoice_headers ih
                        where ih.partner_id in({$partner_id}) and ih.status = 'P' and MONTH(ih.invoice_date) = {$i} and YEAR(ih.invoice_date) = {$year}";
                $result = collect(DB::select(DB::raw($cmd)))->first();
                $result = isset($result) ? floatval($result->total) : 0;
               
                $yearlyRevenue .= "{ x: new Date(2017, {$m}, 1), y: {$result} },";
                $cmd = "select sum(ih.total_due) as total from invoice_headers ih
                        where ih.partner_id in({$partner_id}) and ih.status = 'P' and MONTH(ih.invoice_date) = {$i} and YEAR(ih.invoice_date) = {$prevYear}";
                $result = collect(DB::select(DB::raw($cmd)))->first();
                $result = isset($result) ? floatval($result->total) : 0;
                $m = $i-1;
                $yearlyRevenuePrev .= "{ x: new Date(2017, {$m}, 1), y: {$result} },";
            }

        }

        $revenues = Array(
        	'yearlyRevenue' => $yearlyRevenue,
        	'yearlyRevenuePrev' => $yearlyRevenuePrev,
        	);
        return $revenues;
    }

    public static function merchant_by_agents($partner_id,$partner_access){
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $addQry = " p.id in({$partner_id}) and ";
        if (strpos($admin_access, 'super admin access') !== false){
            $addQry = "";
        }

        $cmd = "select pp.id,pc.company_name,count(p.id) as agentCount from partners p 
                inner join partners pp on pp.id = p.parent_id
                inner join partner_companies pc on pc.partner_id = pp.id
                where {$addQry} p.partner_type_id = 3
                group by pp.id,pc.company_name order by agentCount desc";
        $merchants = DB::select(DB::raw($cmd));
        foreach ($merchants as $m) {
            $cmd = "select p.id,pc.company_name,pt.email from partners p 
                    inner join partner_companies pc on pc.partner_id = p.id
                    inner join partner_contacts pt on pt.partner_id = p.id and pt.is_original_contact = 1
                    where p.parent_id = {$m->id} and p.partner_type_id = 3
                    and p.id IN ({$partner_access})
                    order by pc.company_name desc"; 
            $merchant = DB::select(DB::raw($cmd));
            $m->merchant = $merchant;
        }
        return $merchants;
    }

    public static function task_list($uid){
        $cmd = "select po.partner_id,pc.company_name,p.name,po.id,sh.name as project,sd.name as taskname,sd.task_no,
                Case sd.status when 'C' then 'Completed'
                else 'Pending' end as taskStatus,
                Case when sd.status = 'C' then 'Finished'
                when now() > sd.due_date then 'Delayed'
                else 'On Track' end as progress,
                DATE_FORMAT(sd.due_date,'%d-%b-%Y') as dueDate
                from sub_task_details sd
                inner join sub_task_headers sh on sh.id = sd.sub_task_id
                inner join product_orders po on po.id = sh.order_id
                inner join products p on p.id = po.product_id
                inner join partner_companies pc on po.partner_id = pc.partner_id
                where sd.assignee like '%\"{$uid}\"%'
                order by sd.due_date desc";

        $tasks = DB::select(DB::raw($cmd));
        return $tasks;
    }	

    public static function task_completion_rate($uid){
        $cmd = "select count(id) as taskTotal
                from sub_task_details 
                where assignee like '%\"{$uid}\"%'";

        $totalTask = collect(DB::select(DB::raw($cmd)))->first();

        $cmd = "select count(id) as taskTotal
                from sub_task_details 
                where assignee like '%\"{$uid}\"%' and status = 'C'";

        $totalCompleted = collect(DB::select(DB::raw($cmd)))->first();

        $cmd = "select count(id) as taskTotal
                from sub_task_details 
                where assignee like '%\"{$uid}\"%' and status = '' and now() > due_date";

        $totalDelayed = collect(DB::select(DB::raw($cmd)))->first();

        $cmd = "select count(id) as taskTotal
                from sub_task_details 
                where assignee like '%\"{$uid}\"%' and status = '' and now() <= due_date";

        $totalOnTrack = collect(DB::select(DB::raw($cmd)))->first();

        if($totalTask->taskTotal == 0){
            $taskSummary = Array(
                        'completed' => 100,
                        'delayed' => 0,
                        'ontrack' => 0,
                    );  
        }else{
            $taskSummary = Array(
                        'completed' => ($totalCompleted->taskTotal / $totalTask->taskTotal)*100,
                        'delayed' => ($totalDelayed->taskTotal / $totalTask->taskTotal)*100,
                        'ontrack' => ($totalOnTrack->taskTotal / $totalTask->taskTotal)*100,
                    );            
        }
        return $taskSummary;
    }	


    public static function owner_dashboard($service){

        $cmd = "select pc.company_name,prp.name,pr.name as subname,pc.partner_id,sum(id.amount) as total from invoice_headers ih
            inner join invoice_details id on ih.id = id.invoice_id
            inner join products pr on pr.id = id.product_id
            inner join products prp on prp.id = pr.parent_id
            inner join partners p on p.id = ih.partner_id
            inner join partners pp on p.parent_id = pp.id
            inner join users u on (u.reference_id = pp.id and u.is_original_partner = 1)
            inner join partner_companies pc on u.company_id = pc.partner_id
            where ih.status = 'P'
            group by pc.company_name,pr.name,prp.name,pc.partner_id";
        $productSales = DB::select(DB::raw($cmd));

        $companies = $service->fetchCompanies();
        $partnerTypes = PartnerType::where('status', 'A')->get();

        return Array( 
        	'companies' => $companies, 
        	'partnerTypes' => $partnerTypes);
    }

    public static function recent_sales($merchantId ){

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $addQry = " where ih.partner_id = {$merchantId} ";
        if (strpos($admin_access, 'super admin access') !== false){
            $addQry = "";
        }

        $cmd = "select DATE_FORMAT(ih.invoice_date,'%m/%d/%Y') as salesDate,sum(id.amount) as totalSale  from invoice_headers ih
                    inner join invoice_details id on ih.id = id.invoice_id
                    {$addQry}
                    group by ih.invoice_date
                    order by ih.invoice_date desc limit 5";
        $recentInvoice = DB::select(DB::raw($cmd));
        return $recentInvoice;
    }


}
