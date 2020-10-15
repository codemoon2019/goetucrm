<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProductOrder extends Model
{
    protected $table = 'product_orders';
    protected $dates = ['date_sent', 'date_received', 'date_signed'];
    /**
     * Relationships
     */
    public function createdBy()
    {
        return $this->belongsTo("App\Models\User", "create_by", "username");
    }

    public function details()
    {
        return $this->hasMany('App\Models\ProductOrderDetail','order_id','id');
    }

    public function invoiceHeaders()
    {
        return $this->hasMany('App\Models\InvoiceHeader', 'order_id', 'id');
    }

    public function product()
    {
       return $this->hasOne('App\Models\Product','id','product_id');
    }

    public function subTaskHeader()
    {
        return $this->hasOne('App\Models\SubTaskHeader', 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'partner_id', 'reference_id');
    }

    public function partnerCompany()
    {
        return $this->belongsTo("App\Models\PartnerCompany", "partner_id", "partner_id");
    }

    public function partner()
    {
        return $this->belongsTo("App\Models\Partner", "partner_id", "id");
    }

    public function welcomeEmail()
    {
        return $this->hasOne('App\Models\WelcomeEmailTemplate', 'product_id', 'product_id');
    }



    /**
     * Scopes
     */
    public function scopeIsActive($query)
	{
		return $query->where('status', '<>', 'D');
    }
    

    public function scopeWhereCompany($query, $companyId)
    {
        if ($companyId == -1) {
            return $query;
        }

        return $query->whereHas('user', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        });
    }

    /**
     * Static Functions
     */
    public static function getApplicationList($startDate,$endDate,$partner_access){
    	$cmd = DB::raw("SELECT po.id as order_id,p.id as product_id ,p.name,DATE_FORMAT(po.created_at, '%m/%d/%Y') as create_date,po.status,po.signature,po.product_status,
		case p.name
			WHEN 'GO3 Gift and Rewards' THEN 'gift' 
			WHEN 'GO3 Website and Online Ordering' THEN 'webolo'
			WHEN 'GO3 Credit Card' THEN 'cc'
			WHEN 'EZ2EAT Website and Online Ordering' THEN 'ezolo'
			WHEN 'GO2 Website and Online Ordering' THEN 'go2olo'
			WHEN 'GO2 POS' THEN 'pos'
			WHEN 'GO3 Rewards' THEN 'go3reward'
			WHEN 'Reservation' THEN 'reservation'
			ELSE '' END as sett_type, po.partner_id,concat(partner_contacts.first_name,' ',partner_contacts.last_name) as agent,partners.merchant_mid,partner_companies.company_name
		from product_orders po 
		INNER JOIN products p on p.id=po.product_id
		INNER JOIN partners on partners.id = po.partner_id
		INNER JOIN partner_companies on partners.id = partner_companies.partner_id
		INNER JOIN partners p_parent on partners.parent_id = p_parent.id
		INNER JOIN partner_contacts on partner_contacts.partner_id = p_parent.id and partner_contacts.is_original_contact=1
        WHERE
        po.product_status <> 'Completed' AND 
        (po.created_at >= '".$startDate."' 
        AND po.created_at <= date_add('".$endDate."', INTERVAL 1 DAY)) AND 
        po.partner_id IN(".$partner_access.") order by po.id desc");

    	$result = DB::select($cmd);

    	return $result;
    }

    public static function getBatchID(){
        $cmd = DB::raw("select ifnull(max(batch_id) + 1,1) as maxBatch from product_orders");
        $result = DB::select($cmd);
        foreach($result as $r){
            return $r->maxBatch;
        }
        return 1;
    }

    public static function getProductOrders($partner_ids="-1"){
        //DB::enableQueryLog();
        //if($partner_ids!="-1"){
            $result = DB::table('product_orders')->distinct()->select('product_orders.*','partner_companies.email','parent_partner_company.company_name','partner_companies.company_name as merchant_name')
                ->leftJoin('partner_companies','partner_companies.partner_id','=','product_orders.partner_id')
                ->leftJoin('partners','partners.id','=','product_orders.partner_id')
                ->leftJoin('partners as parent_partner','parent_partner.id','=','partners.parent_id')
                ->leftJoin('partner_companies as parent_partner_company','parent_partner_company.partner_id','=','parent_partner.id')
                ->get();
        // } else {
        //     $result = DB::table('users')->distinct()->select('users.*',DB::raw("(CASE WHEN users.status = 'A' THEN 'Active' ELSE 'Inactive' END) AS status_text"),DB::raw("IFNULL(partner_companies.company_name,'No Company') AS company"))
        //         ->whereIn('users.status', array('A','I'))
        //         ->leftJoin('user_types', function($query) {
        //             $query->whereRaw("find_in_set(user_types.id,users.user_type_id)");
        //         })
        //         ->leftJoin('partner_companies','partner_companies.id','=','users.company_id',' AND users.is_original_partner=1')
        //         ->where('user_types.create_by','<>','SYSTEM')
        //         ->get();
        // }
        //dd(DB::getQueryLog());
        return $result;

    }


    public static function getPID($partner_ids="-1"){
        $cmd = DB::raw("select p.name as PID 
                from product_orders po 
                inner join products p on po.product_id = p.id
                where po.partner_id in ({$partner_ids})
                group by p.name");
        $result = DB::select($cmd); 

        return $result;

    }

    public static function getCurrentTaskStatus($orderId){
        $cmd = DB::raw("select * from sub_task_headers where order_id = {$orderId}");
        $result = DB::select($cmd);
        if(count($result) > 0){
            $cmd = DB::raw("select IFNULL(ut.description,'Unassigned') as department,IFNULL(ut.color,'#000000') as color
                    from sub_task_headers sh
                    inner join sub_task_details sd on sd.sub_task_id = sh.id
                    left join user_types ut on ut.id = sd.department_id
                    where sh.order_id = {$orderId}
                    and sd.status not in('C','V')
                    order by task_no limit 1");
            $result = DB::select($cmd);        
            foreach($result as $r){
                return array(
                    'department' => $r->department,
                    'color' =>  $r->color,
                    'status' => 'In Progress',
                );  
            }    
            return array(
                'department' => 'None',
                'color' =>  '#28D400',
                'status' => 'Completed',
            );  

        }else{
            return array(
                'department' => 'None',
                'color' =>  '#000000',
                'status' => 'No Existing Task',
            );  
        }

    }
}
