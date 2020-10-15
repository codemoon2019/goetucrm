<?php

namespace App\Http\Controllers\Prospects;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Config;
use Mail;
use Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;

use App\Models\Access;
use App\Models\BusinessType;
use App\Models\CalendarActivity;
use App\Models\CalendarReminder;
use App\Models\Country;
use App\Models\Drafts\DraftPartner;
use App\Models\IncomingLead;
use App\Models\LeadComment;
USE App\Models\MerchantStatus;
use App\Models\Notification;
use App\Models\Ownership;
use App\Models\Partner;
use App\Models\PartnerBillingAddress;
use App\Models\PartnerCompany;
use App\Models\PartnerContact;
use App\Models\PartnerDbaAddress;
use App\Models\PartnerMailingAddress;
use App\Models\PartnerProduct;
use App\Models\PartnerProductAccess;
use App\Models\PartnerShippingAddress;
use App\Models\PartnerType;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductOrder;
use App\Models\ProductOrderDetail;
use App\Models\PaymentFrequency;
use App\Models\State;
use App\Models\TimeZone;
use App\Models\User;
use App\Models\UserType;
use App\Models\PaymentProcessor;
use App\Models\LeadStatus;
use App\Models\UsZipCode;
use App\Models\PhZipCode;
use App\Models\CnZipCode;
use App\Models\UserCompany;
use App\Models\UserTypeReference;

class ProspectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:prospect,view')->only('index');
        $this->middleware('access:prospect,add')->only('create');
    }

    public function index()
    {
        $partner_access = -1;
        $id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
        $prospectsSearch = false;
        $advanceSearchLabel = "Prospects";
        // $products = Product::select('id','name')->where([['parent_id',-1],['status','A']])->get();

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);
        }

        if ($partner_access==""){$partner_access=$id;}

        $prospects = Partner::partner_leads_prospects($partner_access,8);
        $draftProspects = DraftPartner::get_draft_leads_prospects($partner_access,8);
        $prospects = array_merge($prospects, $draftProspects);
        
        foreach ($prospects as $p) {
        $interested_products = '';  
            foreach ($p->interested_products as $i) {
                $interested_products .= $i->name .'; ';
                $p->interested_products = $interested_products;
            }
        }
        foreach ($prospects as $p) {
        $upline = '';
            foreach ($p->upline_partners as $u) {
                //$upline .= $u->first_name .' '. $u->last_name .' - '. $u->merchant_id;
                $upline .= $u->company_name .' - '. $u->merchant_id;
                $p->upline_partners = $upline;
            }
        }

        $canDelete = false;
       
        if (strpos($admin_access, 'super admin access') !== false){
            $canDelete = true;
        }

        if (strpos($admin_access, 'super admin access') !== false){
            $partner_product_id = '';
        }else {
            $parent_id = auth()->user()->reference_id;
            if ($parent_id == -1) {
                $parent_id = auth()->user()->reference_id;
                $partner_product = DB::table('partner_product_accesses')->where('partner_id',$parent_id)->first();
                $partner_product_id = '';
                if(isset($partner_product)){
                    $partner_product_id = $partner_product->product_access;
                    if ($partner_product_id == '') {
                        $partner_product_id = -1;
                    }
                }
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }else{
                $product_id = '';
                $products = PartnerProduct::get_partner_products($parent_id);  
                foreach($products as $r)
                {
                    $product_id = $product_id . $r->product_id . ",";
                }
                $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                if ($partner_product_id == ""){ //means no product access so we have to set it to -1 so they can't tag a single product
                    $partner_product_id = -1;    
                }     
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }
        }

        $products = Product::api_get_products($partner_product_id);
        $leadaccess = isset($access['prospect']) ? $access['prospect'] : "";
        $canViewUpline = (strpos($leadaccess, 'view upline') === false) ? false : true;

        return view("prospects.list", compact('prospects','advanceSearchLabel','prospectsSearch','products','canDelete','canViewUpline'));
    }
    public function create(){
        $partner_access = -1;
        $id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
        $partner_ids = '';

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        $userAccess = isset($access['draft applicants']) ? $access['draft applicants'] : "";
        $canSaveAsDraft = (strpos($userAccess, 'draft applicants list') === false) ? false : true;
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);
        }
        
        if ($partner_access==""){$partner_access=$id;}

        // $partner_ids .= '6,';

        $partner_ids .= '8,';

        if (strlen($partner_ids) > 0){
            $partner_ids = substr($partner_ids, 0, strlen($partner_ids) - 1);         
        }

        $partner_type = PartnerType::get_partner_types($partner_ids, false,false,true);

        $country = Country::select('id','name','iso_code_2','iso_code_3')
            ->where('display_on_others', 1)
            ->get();

        $state = State::select('abbr as code','name')->where('country','US')->orderBy('name','asc')->get();

        $statePH = State::select('abbr as code','name')->where('country','PH')->orderBy('name','asc')->get();

        $stateCN = State::select('abbr as code','name')->where('country','CN')->orderBy('name','asc')->get();

        $ownership = Ownership::where('status','A')->orderBy('name','asc')->get();

        $id = -1;
       
        if (strpos($admin_access, 'super admin access') !== false){
            $partner_product_id = '';
        }else {
            $parent_id = auth()->user()->reference_id;
            if ($parent_id == -1) {
                $parent_id = auth()->user()->reference_id;
                $partner_product = DB::table('partner_product_accesses')->where('partner_id',$parent_id)->first();
                $partner_product_id = '';
                if(isset($partner_product)){
                    $partner_product_id = $partner_product->product_access;
                    if ($partner_product_id == '') {
                        $partner_product_id = -1;
                    }
                }
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    // $parent_product_ids = DB::raw("select distinct parent_id from products where id IN ($partner_product_id)");
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }else{
                $product_id = '';
                $products = PartnerProduct::get_partner_products($parent_id);  
                foreach($products as $r)
                {
                    $product_id = $product_id . $r->product_id . ",";
                }
                $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                if ($partner_product_id == ""){ //means no product access so we have to set it to -1 so they can't tag a single product
                    $partner_product_id = -1;    
                }     
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    // $parent_product_ids = DB::raw("select distinct parent_id from products where id IN ($partner_product_id)");
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }
        }

        $products = Product::api_get_products($partner_product_id);
        
        $access="";
       
        if (strpos($admin_access, 'super admin access') === false){
            $access = session('all_user_access');
            $pt_access = "";
            $pt_access .= isset($access['company']) ? "7," : "";
            $pt_access .= isset($access['iso']) ? "4," : "";
            $pt_access .= isset($access['sub iso']) ? "5," : "";
            $pt_access .= isset($access['agent']) ? "1," : "";
            $pt_access .= isset($access['sub agent']) ? "2," : "";
            $access = ($pt_access == "") ? "" : substr($pt_access, 0, strlen($pt_access) - 1); 
        }

        $upline_partner_type = PartnerType::get_partner_types($access,true);

        /** Check user's department if system defined */
        $systemUser = false;
        $userTypeIds = explode(',', auth()->user()->user_type_id);
        foreach ($userTypeIds as $id) {
            if ( UserType::find($id)->create_by == 'SYSTEM' ) {
                $systemUser = true;
                break;
            }
        }

        $userDepartment = User::find(auth()->user()->id)->department->description;
        $businessTypeGroups = Cache::get('business_types');
        $paymentProcessor = PaymentProcessor::active()->orderBy('name')->get(); 
        
        $initialCities = UsZipCode::where('state_id', 1)->orderBy('city')->get();

        return view("prospects.create", compact('partner_type', 'upline_partner_type',
            'ownership', 'state', 'country', 'products', 'statePH', 'stateCN',
            'systemUser', 'userDepartment', 'canSaveAsDraft', 'businessTypeGroups',
            'paymentProcessor', 'initialCities'));
    }
    public function profile($partner_id=null){
        $partner_access = -1;
        $id = $partner_id;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $access_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
            $partner_access = Partner::get_partners_access($access_id); 
        }

        if ($partner_access==""){$partner_access=$id;}

        $partner_info = Partner::get_partner_info($partner_id,false,"6,8",$partner_access);
        if (empty($partner_info)) {
            return redirect('/prospects')->with('failed','You have no access to that record.')->send();
        }

        // Profile
        // $partner_type = PartnerType::where([['status','A'],['included_in_leads',1]])->orderBy('sequence','asc')->get();
        $partner_type = PartnerType::get_partner_types(8, false,false,true);

        $country = Country::select('id','name','iso_code_2','iso_code_3')
            ->where('display_on_others', 1)
            ->get();

        $state = State::select('abbr as code','name','id')->where('country','US')->orderBy('name','asc')->get();

        $statePH = State::select('abbr as code','name','id')->where('country','PH')->orderBy('name','asc')->get();

        $stateCN = State::select('abbr as code','name','id')->where('country','CN')->orderBy('name','asc')->get();

        $ownership = Ownership::where('status','A')->orderBy('name','asc')->get();

        $partner_access=-1;
       
        if (strpos($admin_access, 'super admin access') === false){
            $reference_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
            $partner_access = Partner::get_partners_access();
        }

        if ($partner_access==""){$partner_access=$id;}
        $upline = Partner::get_upline_partner($id,$partner_access);

        $canEdit = 0;
        if (Access::hasPageAccess('prospect','edit',true)) {
            $canEdit = 1;
        }

        $canConvert = 0;
        if (Access::hasPageAccess('prospect','convert',true)) {
            $canConvert = 1;
        }

        // Note
        $is_agent = auth()->user()->is_agent;
        if ($is_agent > 0) {
            $is_admin = FALSE;
            $is_admin1 = 0;
        }else {
            $is_admin = TRUE;
            $is_admin1 = 1;
        }

        $comments = LeadComment::get_lead_comment($id,$is_admin1);
        $canAdd = 0;
        if (Access::hasPageAccess('prospect','add',true)) {
            $canAdd = 1;
        }

        $partner_status = DB::table('partner_statuses')
            ->select('name')
            ->where('status','A')
            ->orderBy('name','asc')
            ->get();
        $country_code = DB::table('countries')
                    ->select('country_calling_code')
                    ->where('name',$partner_info[0]->country_name)
                    ->first();
        $calling_code = $country_code->country_calling_code;

        $incoming_lead = IncomingLead::where('partner_id', $partner_id)->where('status','N')->first();
        $assigned_id = -1;
        if (isset($incoming_lead->assigned_id)) { 
            $assigned_id = $incoming_lead->assigned_id; 
        } 

        
        $businessTypeGroups = Cache::get('business_types');
        $paymentProcessor = PaymentProcessor::active()->orderBy('name')->get();
        $leadStatus = LeadStatus::active()->orderBy('name')->get(); 

        $isInternal = session('is_internal');
        if (!$isInternal) {
            return redirect("/prospects/details/summary/{$partner_id}");
        }

        $usCities = UsZipCode::select('city')->orderBy('city')->distinct()->get();
        $phCities = PhZipCode::select('city')->orderBy('city')->distinct()->get();
        $cnCities = CnZipCode::select('city')->orderBy('city')->distinct()->get();

        return view("prospects.details.profile", 
            compact('partner_id', 'businessTypeGroups', 'partner_info','partner_type',
            'country','state','statePH','stateCN','ownership','canEdit','canConvert','upline',
            'is_admin','canAdd','partner_status','comments','calling_code','assigned_id',
            'paymentProcessor','leadStatus','isInternal','usCities','phCities','cnCities'));
    }
    public function contact($partner_id=null){
        $partner_access = -1;
        $id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);
        }

        if ($partner_access==""){$partner_access=$id;}

        $partner_info = Partner::get_partner_info($partner_id,false,"6,8",$partner_access);
        if (empty($partner_info)) {
            return redirect('/leads')->with('failed','You have no access to that record.')->send();
        }

        $partner_type = PartnerType::where([['status','A'],['included_in_leads',1]])->whereIn('id',[6,8])->orderBy('sequence','asc')->get();

        $country = Country::select('id','name','iso_code_2','iso_code_3')
            ->where('display_on_others', 1)
            ->get();

        $state = State::select('abbr as code','name')->where('country','US')->orderBy('code','asc')->get();

        $statePH = State::select('abbr as code','name')->where('country','PH')->orderBy('code','asc')->get();

        $stateCN = State::select('abbr as code','name')->where('country','CN')->orderBy('code','asc')->get();

        $ownership = Ownership::where('status','A')->orderBy('name','asc')->get();

        $upline = Partner::get_upline_partner($id,$partner_access);

        $canEdit = 0;
        if (Access::hasPageAccess('lead','edit',true) || Access::hasPageAccess('prospect','edit',true)) {
            $canEdit = 1;
        }
        $country_code = DB::table('countries')
                    ->select('country_calling_code')
                    ->where('name',$partner_info[0]->country_name)
                    ->first();
        $calling_code = $country_code->country_calling_code;

        $isInternal = session('is_internal');
        if (!$isInternal) {
            return redirect("/prospects/details/summary/{$partner_id}");
        }

        return view("prospects.details.contact", 
            compact('partner_id','partner_info','partner_type','country','state','statePH','stateCN','ownership','canEdit','upline','calling_code','isInternal'));
    }
    public function interested($partner_id=null){
        $partner_access = -1;
        $id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
        $username = auth()->user()->username;
        $usertype = DB::select(DB::raw("SELECT ut.* FROm users u LEFT JOIN user_types ut ON FIND_IN_SET(ut.id, u.user_type_id) > 0 WHERE u.username="."'".$username."'"));

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id); 
        }

        if ($partner_access==""){$partner_access=$id;}

        $partner_info = Partner::get_partner_info($partner_id,false,"6,8",$partner_access);
        if (empty($partner_info)) {
            return redirect('/prospects')->with('failed','You have no access to that record.')->send();
        }

        $partner_type = PartnerType::where([['status','A'],['included_in_leads',1]])->whereIn('id',[6,8])->orderBy('sequence','asc')->get();

        $country = Country::select('id','name','iso_code_2','iso_code_3')
            ->where('display_on_others', 1)
            ->get();

        $state = State::select('abbr as code','name')->where('country','US')->orderBy('code','asc')->get();

        $statePH = State::select('abbr as code','name')->where('country','PH')->orderBy('code','asc')->get();

        $stateCN = State::select('abbr as code','name')->where('country','CN')->orderBy('code','asc')->get();

        $ownership = Ownership::where('status','A')->orderBy('name','asc')->get();

        $upline = Partner::get_upline_partner($id,$partner_access);

        if (strpos($admin_access, 'super admin access') === false && $partner_info[0]->parent_id == -1) {
            $partner_product_id = '';
        } else {
            $parent_id = $partner_info[0]->parent_id;
            if ($parent_id == -1) {
                $parent_id = $partner_id;
                $partner_product = DB::table('partner_product_accesses')->where('partner_id',$parent_id)->first();
                if(isset($partner_product->product_access))
                {
                    $partner_product_id = $partner_product->product_access;
                } else {
                    $partner_product_id = -1;
                }
                if ($partner_product_id == '') {
                    $partner_product_id = -1;
                }
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    // $parent_product_ids = DB::raw("select distinct parent_id from products where id IN ($partner_product_id)");
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }else{
                $product_id = '';
                $products = PartnerProduct::get_partner_products($parent_id);  
                foreach($products as $r)
                {
                    $product_id = $product_id . $r->product_id . ",";
                }
                $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                if ($partner_product_id == ""){ //means no product access so we have to set it to -1 so they can't tag a single product
                    $partner_product_id = -1;    
                }     
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    // $parent_product_ids = DB::raw("select distinct parent_id from products where id IN ($partner_product_id)");
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }
        }
        $ip_id = DB::select("select interested_products as ip from partners where id=".$partner_id);

        $products = Product::api_get_products($partner_product_id,-1,-1,$ip_id[0]->ip == null ? -1 : $ip_id[0]->ip);

        $interested_products = Partner::get_interested_products($partner_id);

        $country_code = DB::table('countries')
                    ->select('country_calling_code')
                    ->where('name',$partner_info[0]->country_name)
                    ->first();
        $calling_code = $country_code->country_calling_code;

        $isInternal = session('is_internal');
        if (!$isInternal) {
            return redirect("/prospects/details/summary/{$partner_id}");
        }

        return view("prospects.details.interested", 
            compact('partner_id','partner_info','partner_type','country','state','statePH','stateCN','ownership','upline','products','interested_products','calling_code','isInternal'));
    }
    public function application($partner_id=null){
        $partner_access = -1;
        $id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id); 
        }

        if ($partner_access==""){$partner_access=$id;}

        $partner_info = Partner::get_partner_info($partner_id,false,"6,8",$partner_access);
        if (empty($partner_info)) {
            return redirect('/leads')->with('failed','You have no access to that record.')->send();
        }

        if (strpos($admin_access, 'super admin access') === false && $partner_info[0]->parent_id == -1) {
            $partner_product_id = '';
        }else {
            $parent_id = $partner_info[0]->parent_id;
            if ($parent_id == -1) {
                $parent_id = $partner_id;
                $partner_product = DB::table('partner_product_accesses')->where('partner_id',$parent_id)->first();
                if(isset($partner_product->product_access))
                {
                    $partner_product_id = $partner_product->product_access;
                } else {
                    $partner_product_id = -1;
                }
                if ($partner_product_id == '') {
                    $partner_product_id = -1;
                }
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    // $parent_product_ids = DB::raw("select distinct parent_id from products where id IN ($partner_product_id)");
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }else{
                $product_id = '';
                $products = PartnerProduct::get_partner_products($parent_id);  
                foreach($products as $r)
                {
                    $product_id = $product_id . $r->product_id . ",";
                }
                $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                if ($partner_product_id == ""){ //means no product access so we have to set it to -1 so they can't tag a single product
                    $partner_product_id = -1;    
                }     
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    // $parent_product_ids = DB::raw("select distinct parent_id from products where id IN ($partner_product_id)");
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }
        }

        $products = Product::api_get_products($partner_product_id,$partner_id);
        foreach($products as $p)
        {
            $p->subproducts = Product::get_child_products($p->id,$partner_info[0]->parent_id);
            $categories = Array();
            foreach($p->subproducts as $s){
                $categories[] = $s->product_category_id;
            }
            $p->categories = ProductCategory::whereIn('id',$categories)->get();
        }


        $country_code = DB::table('countries')
                    ->select('country_calling_code')
                    ->where('name',$partner_info[0]->country_name)
                    ->first();
        $calling_code = $country_code->country_calling_code;

        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canEditPaymentFrequency = (strpos($merchantaccess, 'order payment frequency edit') === false) ? false : true;

        $isInternal = session('is_internal');
        if (!$isInternal) {
            return redirect("/prospects/details/summary/{$partner_id}");
        }

        return view("prospects.details.products", 
            compact('partner_id','partner_info','products','calling_code','canEditPaymentFrequency','isInternal'));
    }
    public function appointment($partner_id=null){
        $partner_access = -1;
        $id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
        $username = auth()->user()->username;
        $uid = auth()->user()->id;
        $usertype = DB::select(DB::raw("SELECT ut.* FROm users u LEFT JOIN user_types ut ON FIND_IN_SET(ut.id, u.user_type_id) > 0 WHERE u.username="."'".$username."'"));

        $ctrID = CalendarActivity::get_max_calendar_activity_id();
        $organizer = auth()->user()->first_name.' '.auth()->user()->last_name;

        if (strpos($usertype[0]->description, 'SUPER ADMIN') === false) {
            $partner_access = Partner::get_partners_access($id);
        }

        if ($partner_access==""){$partner_access=$id;}

        $partner_info = Partner::get_partner_info($partner_id,false,"6,8",$partner_access);
        if (empty($partner_info)) {
            return redirect('/leads')->with('failed','You have no access to that record.')->send();
        }

        $partner_type = PartnerType::where([['status','A'],['included_in_leads',1]])->orderBy('sequence','asc')->get();

        $defTimeZone = config('app.timezone'); //?

        $timezones = TimeZone::orderByRaw("id = 8 desc")->get();
        $tz = '';
        foreach ($timezones as $t) {
            $tz = $tz. '<option value="'.$t->name.'">'.$t->name.'</option>';
        }

        $reminders = CalendarReminder::where('status','A')->get();
        $rm = '';
        foreach ($reminders as $r) {
            $rm = $rm. '<option value="'.$r->id.'">'.$r->description.'</option>';
        }
        $country_code = DB::table('countries')
                    ->select('country_calling_code')
                    ->where('name',$partner_info[0]->country_name)
                    ->first();
        $calling_code = $country_code->country_calling_code;

        $isInternal = session('is_internal');
        if (!$isInternal) {
            return redirect("/prospects/details/summary/{$partner_id}");
        }

        return view("prospects.details.appointment", 
            compact('partner_id','partner_info','ctrID','partner_type','uid','defTimeZone','tz','rm','organizer','calling_code','isInternal'));
    }
    public function incoming(){
        $partner_access = -1;
        $id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
        $username = auth()->user()->username;
        $usertype = DB::select(DB::raw("SELECT ut.* FROm users u LEFT JOIN user_types ut ON FIND_IN_SET(ut.id, u.user_type_id) > 0 WHERE u.username="."'".$username."'"));

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);
        }

        if ($partner_access==""){$partner_access=$id;}

        $incoming_leads = IncomingLead::get_incoming_leads($id,8);

        return view("prospects.incoming",compact('incoming_leads')); 
    }
    public function createLeadProspect(Request $request){  
        if(Input::get('assignTo') && !Input::get('assignee')){
            return response()->json(array(
                'success'       => false, 
                'msg'           => "Please select valid partner/upline!", 
            ), 200);
        }else {
            // $pc1_exist = DB::select(DB::raw("SELECT pc.id FROM partner_companies pc
            //     LEFT JOIN partners p 
            //     ON pc.partner_id = p.id
            //     WHERE p.status = 'A' AND UCASE(pc.email) = '".Input::get('txtEmailPros')."'"));
            // $pc2_exist = DB::select(DB::raw("SELECT pc.id FROM partner_companies pc
            //     LEFT JOIN partners p 
            //     ON pc.partner_id = p.id
            //     WHERE p.status = 'A' AND UCASE(pc.phone1) = '".Input::get('businessPhone1')."'"));
            // $pc3_exist = DB::select(DB::raw("SELECT pc.id FROM partner_contacts pc
            //     LEFT JOIN partners p 
            //     ON pc.partner_id = p.id
            //     WHERE p.status = 'A' AND UCASE(pc.mobile_number) = '".Input::get('mobileNumber')."'"));

            // if ($pc1_exist) {
            //     return response()->json(array(
            //         'success'       => false, 
            //         'msg'           => "Business email has already been used.", 
            //     ), 200);
            // }
            // if($pc2_exist){
            //     return response()->json(array(
            //         'success'       => false, 
            //         'msg'           => "Business Phone 1 has already been used.", 
            //     ), 200);
            // }
            // if($pc3_exist){
            //     return response()->json(array(
            //         'success'       => false, 
            //         'msg'           => "Mobile number has already been used.", 
            //     ), 200);
            // }
    
            // Create Notifiation
            DB::transaction(function() use ($request) {
                if(Input::get('selfAssign') == 1){
                    $parent_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;  
                    $original_parent_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id; 
                }else{
                    $parent_id = Input::get('assignee') == null ? auth()->user()->reference_id : Input::get('assignee');  
                    $original_parent_id = Input::get('assignee') == null ? auth()->user()->reference_id : Input::get('assignee');     		
                }

                $state = '';
                if(Input::get('country') == 'United States'){
                    $state = Input::get('txtState');
                }
                if(Input::get('country') == 'Philippines'){
                    $state = Input::get('txtStatePH');
                }
                if(Input::get('country') == 'China'){
                    $state = Input::get('txtStateCN');
                }
                $lead_id = DB::table('partners')
                    ->select(DB::raw('count(id)+1 max_id'))
                    ->where('partner_type_id',Input::get('txtPartnerTypeId'))
                    ->first();
                $partner_type_description= DB::table('partner_types')
                    ->select('name')
                    ->where('id',Input::get('txtPartnerTypeId'))
                    ->first();
                
                $interested_products = Input::get('product_access');
                // if (Input::get('product_access')) {
                //     $interested_products = implode(',',Input::get('product_access'));
                // }

                $original_parent_id = $parent_id;
                $creator_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id; 
                if ($original_parent_id != $creator_id){
                    $parent_id= $creator_id;
                }

                // Create Partners -> Prospect
                $leadsProspects = new Partner;
                $leadsProspects->create_by = auth()->user()->username;          
                $leadsProspects->update_by = auth()->user()->username;          
                $leadsProspects->status = 'A';
                $leadsProspects->partner_type_id = Input::get('txtPartnerTypeId');          
                $leadsProspects->original_partner_type_id = Input::get('txtPartnerTypeId');           
                $leadsProspects->parent_id = $parent_id; 
                $leadsProspects->original_parent_id = $original_parent_id; 
                $leadsProspects->logo = Input::get('logo') == "" ? "" : Input::get('logo');
                $leadsProspects->partner_id_reference = '';           
                $leadsProspects->merchant_processor = Input::get('currentProcessor');       
                $leadsProspects->interested_products = $interested_products;            
                $leadsProspects->partner_status = 'New';        
                $leadsProspects->business_type_code = Input::get('mcc');        
                $leadsProspects->save(); 

                if ($leadsProspects->parent_id != -1)
                {
                    $leadsProspects->company_id = Partner::get_upline_company($leadsProspects->id);     
                } else {
                    $leadsProspects->company_id = -1;    
                }
                $leadsProspects->save(); 

                // Create Lead Comment
                if (Input::get('note') != "") {
                    $leadsComment = new LeadComment;
                    $leadsComment->partner_id = $leadsProspects->id;
                    $leadsComment->comment = Input::get('note');
                    $leadsComment->parent_id = -1;
                    $leadsComment->create_by = auth()->user()->username;
                    $leadsComment->user_id = auth()->user()->id;
                    $leadsComment->attachment = '';
                    $leadsComment->is_internal = 0;
                    $leadsComment->lead_status = '';
                    $leadsComment->save();
                }
                // Update Partners -> Lead/Prospect partner_id_reference
                $lead_id = substr($partner_type_description->name,0,1) .(10000+$lead_id->max_id); 
                $leadsProspects = Partner::find($leadsProspects->id);
                $leadsProspects->partner_id_reference = $lead_id;
                $leadsProspects->save();
                // Create Partner Company 
                $country_code = DB::table('countries')
                    ->select('country_calling_code')
                    ->where('name',Input::get('country'))
                    ->first();
                $partnerCompany = new PartnerCompany;
                $partnerCompany->partner_id = $leadsProspects->id;
                $partnerCompany->company_name = Input::get('legalName');
                $partnerCompany->dba = Input::get('dba');
                $partnerCompany->country = Input::get('country');
                $partnerCompany->country_code = $country_code->country_calling_code;
                $partnerCompany->address1 = Input::get('businessAddress1');
                $partnerCompany->address2 = Input::get('businessAddress2');
                $partnerCompany->city = Input::get('city');
                $partnerCompany->state = $state;
                $partnerCompany->zip = Input::get('zip');
                $partnerCompany->phone1 = Input::get('businessPhone1');
                $partnerCompany->extension = Input::get('extension1');
                $partnerCompany->phone2 = Input::get('businessPhone2');
                $partnerCompany->extension_2 = Input::get('extension2');
                $partnerCompany->fax = Input::get('fax');
                $partnerCompany->mobile_number = Input::get('mobileNumber');
                $partnerCompany->email = Input::get('txtEmailPros');
                $partnerCompany->ownership = Input::get('ownership');
                $partnerCompany->save();
                // Create Partner Contact
                $country_code = DB::table('countries')
                    ->select('country_calling_code')
                    ->where('name',Input::get('country'))
                    ->first();
                $partnerContact = new PartnerContact;
                $partnerContact->partner_id = $leadsProspects->id;
                $partnerContact->first_name = Input::get('fname');
                $partnerContact->middle_name = Input::get('mname');
                $partnerContact->last_name = Input::get('lname');
                $partnerContact->position = Input::get('title');
                $partnerContact->country = '';
                $partnerContact->country_code = $country_code->country_calling_code;
                $partnerContact->address1 = '';
                $partnerContact->address2 = '';
                $partnerContact->city = '';
                $partnerContact->state = '';
                $partnerContact->zip = '';
                $partnerContact->other_number = Input::get('cphone1');
                $partnerContact->other_number_2 = Input::get('cphone2');
                $partnerContact->fax = Input::get('contactFax');
                $partnerContact->mobile_number = Input::get('mobileNumber');
                $partnerContact->email = Input::get('txtEmail2Pros');
                $partnerContact->is_original_contact = 1;
                $partnerContact->save();

                if (isset($request->txtDraftPartnerId)) {
                    $draft = DraftPartner::find($request->txtDraftPartnerId);
                    $draft->is_stored_to_partners = 1;
                    $draft->save();
                }

                if($original_parent_id != $creator_id){
                    $incomingLeads = new IncomingLead;
                    $incomingLeads->create_by = auth()->user()->username;
                    $incomingLeads->status = 'N';
                    $incomingLeads->assigned_id = $original_parent_id;
                    $incomingLeads->partner_id = $leadsProspects->id;
                    $incomingLeads->partner_type_id = Input::get('txtPartnerTypeId');
                    $incomingLeads->creator_id = $creator_id;
                    $incomingLeads->previous_assigned_id = $creator_id;
                    $incomingLeads->save();

                    $recipient = DB::table('users')
                        ->select('username')
                        ->where('reference_id',$parent_id)
                        ->first(); 
                    $recipient = $recipient == null ? -1 : $recipient->username;
                    $recipient1 = DB::select(DB::raw("select p.partner_id_reference as username from partners p left join partner_companies pc on p.id=pc.partner_id where p.id = ".$original_parent_id));
                    $recipients = [$recipient,$recipient1[0]->username];
                    $email_recipient = DB::table('partner_contacts')
                        ->select('email')
                        ->where('partner_id',$original_parent_id)
                        ->first(); 
                    $email_message = "Request has been sent on incoming prospects.";
                    $email_subject = $lead_id. ' - ' .  Input::get('fname') . ' '. Input::get('lname')  . ' request for approval.';

                    for ($i=0; $i < sizeof($recipients); $i++) { 
                        $notifications = new Notification;
                        $notifications->partner_id = $original_parent_id;
                        $notifications->source_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
                        $notifications->recipient = $recipients[$i];
                        $notifications->subject = $email_subject;
                        $notifications->message = $email_message;
                        $notifications->status = 'N';
                        $notifications->create_by = auth()->user()->username;
                        $notifications->redirect_url = '/prospects/incoming';
                        $notifications->save();
                    }

                    // Create Mail Notification
                    if (Input::get('txtEmailPros')) {
                        $data = array(
                            'first_name' => Input::get('fname'),
                            'last_name' => Input::get('lname'),
                            'email_message' => $email_message,
                            'email_address' => Input::get('txtEmailPros'),
                            'email_subject' => $email_subject,
                        );
                        
                        Mail::send(['html'=>'mails.incominglead'],$data,function($message) use ($data){
                            $message->to($data['email_address'],$data['first_name'].' '.$data['last_name']);
                            $message->subject('[GoETU] '.$data['email_subject']);
                            $message->from('no-reply@goetu.com');
                        });
    
                        if (Mail::failures()) {
                            return redirect('/prospects')->with('failed','Failed to send email.');
                        }  
                    }
                }
            });
        }
        return response()->json(array(
            'success'       => true, 
            'msg'           => "Prospect successfully created", 
        ), 200);
    }
    public function loadUplineLIst(Request $request){
        $partner_type_id = $request->partner_type_id;
        $id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id; 
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        $partner_access="";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);
        }
        if ($partner_access==""){$partner_access=$id;}
        
        $upline = Partner::get_downline_partner($id,$partner_access,$partner_type_id);
        $option = ""; 
        if (count($upline) > 0){
            foreach($upline as $row){
                $option .= '<option data-image="' . $row->image . '" value="' . $row->parent_id .  '">&nbsp;' . $row->partner_id_reference . " - " . $row->dba  . '</option> ';
            }
        }

        return response()->json(array(
            'success'       => true, 
            'msg'           => "Upline has been loaded.",
            'data'          => $option, 
        ), 200);
    }
    public function getCountryCallingCode(Request $request){
        $country_calling_code = DB::table('countries')
            ->select('country_calling_code')
            ->where('name',$request->country_name)
            ->first();

        return response()->json(array(
            'success'       => true, 
            'msg'           => "Data has been loaded.",
            'data'          => $country_calling_code->country_calling_code, 
        ), 200);   
    }
    public function deleteInterestedProduct(Request $request){
        $interested_products = DB::select("select interested_products as ip from partners where id=".$request->partner_id);
        
        $id = $request->product_id;
        $allFaves = explode(',', $interested_products[0]->ip);
        $index = array_search($id,$allFaves);
        if($index !== false){
            unset($allFaves[$index]);
        }
        $allFaves=implode(',',$allFaves);

        $updatePartner = Partner::find($request->partner_id);
        $updatePartner->interested_products = $allFaves;
        $updatePartner->update_by = auth()->user()->username;

        if ($updatePartner->save()) {
            return response()->json(array(
                'success'                   => true, 
                'msg'                       => "Product has been removed!", 
            ), 200);
        }else {
            return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Unable to update!", 
            ), 200);
        }
    }
    public function addInterestedProduct(Request $request){
        $updatePartner = Partner::find($request->partner_id);
        if (empty($updatePartner->interested_products)) {
            $updatePartner->interested_products = implode(',', $request->add_products);
        }else {
            $updatePartner->interested_products = $updatePartner->interested_products.','.implode(',', $request->add_products);
        }
        $updatePartner->update_by = auth()->user()->username;

        if ($updatePartner->save()) {
            return response()->json(array(
                'success'                   => true, 
                'msg'                       => "Product has been added!", 
            ), 200);
        }else {
            return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Unable to update!", 
            ), 200);
        }
    }
    public function updateIncomingLeadRequest(Request $request){
        $info = IncomingLead::get_incoming_lead_info_by_id($request->id);

        if (!isset($info[0]->id)) {
            return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Unable to accept the request.", 
            ), 200);
        }
        $recipient = DB::table('users')
            ->select('username')
            ->where('reference_id',$info[0]->creator_id)
            ->first();
        $recipient = $recipient == null ? -1 : $recipient->username;

        $recipient1 = DB::select(DB::raw("select p.partner_id_reference as username from partners p left join partner_companies pc on p.id=pc.partner_id where p.id = ".$info[0]->assigned_id));
        $recipients = [$recipient,$recipient1[0]->username];

        $updateRequest = IncomingLead::find($request->id);
        $updateRequest->update_by = auth()->user()->username;
        $updateRequest->status = $request->status;
        if(!$updateRequest->save()){
            return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Unable to update incoming prospects!", 
            ), 200);
        }

        $updatePartner = Partner::find($info[0]->partner_id);
        $updatePartner->update_by = auth()->user()->username;
        $updatePartner->parent_id = $info[0]->assigned_id;
        $updatePartner->company_id = Partner::get_upline_company($info[0]->partner_id);     

        if ($request->status == 'A') {
            if ($info[0]->partner_type_id == 6) {
                $new_pir = "PL" . preg_replace("/[^0-9,.]/", "", $info[0]->partner_id_reference);
                $updatePartner->partner_id_reference = $new_pir;
                $updatePartner->partner_type_id = 8;
            }
            if(!$updatePartner->save()){
                return response()->json(array(
                    'success'                => false, 
                    'msg'                    => "Unable to update partner!", 
                ), 200);
            }
        }

        $status_text = "";
        if ($request->status == "A"){$status_text = "accepted";}else{$status_text="declined";}
        //send enail
        $email = DB::table('users')
            ->select('email_address')
            ->where('reference_id',$info[0]->creator_id)
            ->first();
        /*$email = DB::table('users')
            ->select('email_address')
            ->where('username',$recipient1[0]->username)
            ->first();*/
        // $email_address = $info[0]->assigned_by_email; 
        $email_address = $email->email_address;
        $email_message = $info[0]->assignee." Prospect request has been " . $status_text;
        $email_subject = $info[0]->partner_id_reference . ' - ' .  $info[0]->assignee . ' request update';
        
        // Create Mail Notification
        $data = array(
            'email_address' => $email_address,
            'first_name' => '',//$info[0]->assignee,
            'last_name' => '',
            'email_message' => $email_message,
            'email_subject' => $email_subject,
        );
        
        Mail::send(['html'=>'mails.incominglead'],$data,function($message) use ($data){
            $message->to($data['email_address'],$data['first_name']);
            $message->subject('[GoETU] '.$data['email_subject']);
            $message->from('no-reply@goetu.com');
        });

        if (Mail::failures()) {
            return redirect('/prospects')->with('failed','Failed to send email.');
        }  

        for ($i=0; $i < sizeof($recipients); $i++) { 
            $notifications = new Notification;
            $notifications->partner_id = $info[0]->creator_id;
            $notifications->source_id = $info[0]->assigned_id;
            $notifications->recipient = $recipients[$i];
            $notifications->subject = $email_subject;
            $notifications->message = $email_message;
            $notifications->status = 'N';
            $notifications->create_by = auth()->user()->username;
            $notifications->redirect_url = '/prospects/details/profile/'.$info[0]->partner_id;
            $notifications->save();
           /* if (!$notifications->save()) {
                return response()->json(array(
                    'success'                   => false, 
                    'msg'                       => "Unable to update notification!", 
                ), 200);
            }else{
                return response()->json(array(
                    'success'                       => true,
                    'msg'                           => "Notification has been updated/sent!"
                ), 200);
            }*/
        }
        return response()->json(array(
            'success'                       => true,
            'msg'                           => "Notification has been updated/sent!"
        ), 200);

    }
    public function addComment(){
        $partner_id = Input::get('txtPartnerId');
        $sTempfile = "";
        $is_internal = 0;
        $is_public = 0;
        $partner_status = "";
        if (!empty(Input::get('txtPartnerStatus'))){$partner_status=Input::get('txtPartnerStatus');}

        if ($partner_status != "") {
            $updatePartner = Partner::find($partner_id);
            $updatePartner->partner_status = $partner_status;
            $updatePartner->update_by = auth()->user()->username;
            if (!$updatePartner->save()) {
                return response()->json(array(
                    'success'                   => false, 
                    'msg'                       => "Unable to update status!", 
                ), 200);
            }
        }

        $leadComment = new LeadComment;
        $leadComment->partner_id = Input::get('txtPartnerId');
        $leadComment->comment = Input::get('txtComment');
        $leadComment->parent_id = Input::get('txtParentId');
        $leadComment->create_by = auth()->user()->username;
        $leadComment->user_id = auth()->user()->id;
        $leadComment->attachment = $sTempfile;
        $leadComment->is_internal = $is_internal;
        $leadComment->lead_status = $partner_status;

        if (!$leadComment->save()) {
            return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Unable to add comment!", 
            ), 200);
        }else {
            return response()->json(array(
                'success'                   => true, 
                'msg'                       => "Comment has been posted!", 
            ), 200);
        }
    }
    public function addSubComment(){
        $comment_id = Input::get('txtParentId');
        $sTempFileName ="";
        $is_internal = 0;
        $is_public = 0;
        $partner_status = "";
        if (!empty(Input::get('txtPartnerStatusSub'))){$partner_status=Input::get('txtPartnerStatusSub');}

        if ($partner_status != "") {
            $updatePartner = Partner::find(Input::get('txtPartnerId'));
            $updatePartner->partner_status = $partner_status;
            $updatePartner->update_by = auth()->user()->username;
            if (!$updatePartner->save()) {
                return response()->json(array(
                    'success'                   => false, 
                    'msg'                       => "Unable to update status!", 
                ), 200);
            }
        }

        $leadComment = new LeadComment;
        $leadComment->partner_id = Input::get('txtPartnerId');
        $leadComment->comment = Input::get('txtSubComment');
        $leadComment->parent_id = Input::get('txtParentId');
        $leadComment->create_by = auth()->user()->username;
        $leadComment->user_id = auth()->user()->id;
        $leadComment->attachment = $sTempFileName;
        $leadComment->lead_status = $partner_status;
        if (!$leadComment->save()) {
            return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Unable to add comment!", 
            ), 200);
        }else {
            $leadComment = LeadComment::find(Input::get('txtParentId'));
            if(!$leadComment->save()){
                return response()->json(array(
                    'success'                   => false, 
                    'msg'                       => "Unable to add comment!", 
                ), 200);
            }
            return response()->json(array(
                'success'                   => true, 
                'msg'                       => "Comment has been posted!", 
            ), 200);

        }
    }
    public function updateLeadProspect(){
        $username = auth()->user()->username;
        $usertype = DB::select(DB::raw("SELECT ut.* FROm users u LEFT JOIN user_types ut ON FIND_IN_SET(ut.id, u.user_type_id) > 0 WHERE u.username="."'".$username."'"));

        $state = "";
        if (Input::get('country') == 'United States') {
            $state = Input::get('txtState');
        }
        if (Input::get('country') == 'Philippines') {
            $state = Input::get('txtStatePH');
        }
        if (Input::get('country') == 'China') {
            $state = Input::get('txtStateCN');
        }

        $partner_info = Partner::get_partner_info(Input::get('txtLeadID'));
        $parent_id = Input::get('assignTo');
        $previous_parent_id = $partner_info[0]->parent_id;
        $creator_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
        $recipient = DB::table('users')
                        ->select('username')
                        ->where('reference_id',$parent_id)
                        ->first(); 
        $recipient = $recipient == null ? -1 : $recipient->username;
        $incoming_lead = IncomingLead::where('partner_id', Input::get('txtLeadID'))->where('status','N')->first();
        $assigned_id = -1;
        if (isset($incoming_lead->assigned_id)) { 
            $assigned_id = $incoming_lead->assigned_id; 
        }
        if (Input::get('assignedID') != Input::get('assignTo') && Input::get('assignTo') == -1) {
            $check_ic = DB::table('incoming_leads')
                    ->where([['status','N'],['partner_id',Input::get('txtLeadID')]])
                    ->first();
            if (isset($check_ic->id)) {
                return response()->json(array(
                    'success'   => false, 
                    'msg'       => "There is a current request for this prospect. You are not allowed to change the assigned person to 'Unassigned' until it has been declined, accepted or the request expires.", 
                ), 200);
            }
        }
        if ($parent_id != $previous_parent_id && $parent_id != $assigned_id) { 
            if ($parent_id != -1) {
                $check_ic = DB::table('incoming_leads')
                    ->where([['status','N'],['partner_id',Input::get('txtLeadID')]])
                    ->first();
                if (isset($check_ic->id)) {
                    return response()->json(array(
                        'success'   => false, 
                        'msg'       => "There is a current request for this prospect. You are not allowed to change the assigned person until it has been declined, accepted or the request expires.", 
                    ), 200);
                }
            }

            $incoming_leads = new IncomingLead;
            $incoming_leads->create_by = auth()->user()->username;
            $incoming_leads->status = 'N';
            $incoming_leads->assigned_id = $parent_id;
            $incoming_leads->previous_assigned_id = $previous_parent_id;
            $incoming_leads->partner_id = Input::get('txtLeadID');
            $incoming_leads->partner_type_id = $partner_info[0]->partner_type_id;
            $incoming_leads->creator_id = $creator_id;
            if (!$incoming_leads->save()) {
                return response()->json(array(
                    'success'   => false, 
                    'msg'       => "Unable to create incoming prospect request", 
                ), 200);
            }

            $email_recipient = DB::table('partner_contacts')
                ->select('email')
                ->where('partner_id',$parent_id)
                ->first(); 
            $email_message = "Request has been sent on incoming prospects.";
            $email_subject = $partner_info[0]->merchant_id. ' - ' .  $partner_info[0]->first_name . ' '. $partner_info[0]->last_name  . ' request for approval.';

            $notifications = new Notification;
            $notifications->partner_id = $parent_id;
            $notifications->source_id = $creator_id;
            $notifications->recipient = $recipient;
            $notifications->subject = $email_subject;
            $notifications->message = $email_message;
            $notifications->status = 'N';
            $notifications->create_by = auth()->user()->username;
            $notifications->redirect_url = '/prospects/incoming';
            $notifications->save();

            // Create Mail Notification
            $data = array(
                'first_name' => $partner_info[0]->first_name,
                'last_name' => $partner_info[0]->last_name,
                'email_message' => $email_message,
                'email_address' => $email_recipient->email,
                'email_subject' => $email_subject,
            );
            
            Mail::send(['html'=>'mails.incominglead'],$data,function($message) use ($data){
                $message->to($data['email_address'],$data['first_name'].' '.$data['last_name']);
                $message->subject('[GoETU] '.$data['email_subject']);
                $message->from('no-reply@goetu.com');
            });

            if (Mail::failures()) {
                return redirect('/prospects')->with('failed','Failed to send email.');
            }  

            $parent_id = $previous_parent_id;
        }

        $updatePartner = Partner::find(Input::get('txtLeadID'));
        $updatePartner->update_by = auth()->user()->username;
        $updatePartner->status = 'A';
        $updatePartner->parent_id = $parent_id;
        $updatePartner->logo = "";
        $updatePartner->business_type_code = Input::get('mcc');
        $updatePartner->merchant_processor = Input::get('currentProcessor');
        $updatePartner->lead_status_id = Input::get('currentStatus');
        if (!$updatePartner->save()) {
            return response()->json(array(
                'success'   => false, 
                'msg'       => "Unable to update prospect.", 
            ), 200);
        }
        $updatePartner->company_id = Partner::get_upline_company($updatePartner->id);  
        $updatePartner->save();
        // Update Partner Company 
        $country_code = DB::table('countries')
            ->select('country_calling_code')
            ->where('name',Input::get('country'))
            ->first();
        $partnerCompany = PartnerCompany::where('partner_id', Input::get('txtLeadID'))->firstOrFail();
        $partnerCompany->company_name = Input::get('legalName');
        $partnerCompany->ownership = Input::get('ownership');
        $partnerCompany->dba = Input::get('dba');
        $partnerCompany->country = Input::get('country');
        $partnerCompany->country_code = $country_code->country_calling_code;
        $partnerCompany->address1 = Input::get('businessAddress1');
        $partnerCompany->address2 = Input::get('businessAddress2');
        $partnerCompany->city = Input::get('city');
        $partnerCompany->state = $state;
        $partnerCompany->zip = Input::get('zip');
        $partnerCompany->phone1 = Input::get('businessPhone1');
        $partnerCompany->extension = Input::get('extension1');
        $partnerCompany->phone2 = Input::get('businessPhone2');
        $partnerCompany->extension_2 = Input::get('extension2');
        $partnerCompany->fax = Input::get('fax');
        $partnerCompany->mobile_number = $partner_info[0]->mobile_number;
        $partnerCompany->email = Input::get('txtEmailPros');
        if (!$partnerCompany->save()) {
            return response()->json(array(
                'success'   => false, 
                'msg'       => "Unable to update prospect.", 
            ), 200);
        }else{
            return response()->json(array(
                'success' => true,
                'msg'     => 'Prospect updated!',
            ), 200);
        }
    }
    public function updateContact(){
        $partner_info = Partner::get_partner_info(Input::get('txtLeadID'));

        // Update Partner Contact
        $country_code = DB::table('countries')
            ->select('country_calling_code')
            ->where('name',Input::get('countryName'))
            ->first();
        $partnerContact = PartnerContact::where('partner_id', Input::get('txtLeadID'))->firstOrFail();
        $partnerContact->first_name = Input::get('fname');
        $partnerContact->middle_name = Input::get('mname');
        $partnerContact->last_name = Input::get('lname');
        $partnerContact->position = Input::get('title');
        $partnerContact->country = Input::get('countryName');
        $partnerContact->country_code = $country_code->country_calling_code;
        $partnerContact->address1 = $partner_info[0]->contact_address1;
        $partnerContact->address2 = $partner_info[0]->contact_address2;
        $partnerContact->city = $partner_info[0]->contact_city;
        $partnerContact->state = $partner_info[0]->contact_state;
        $partnerContact->zip = $partner_info[0]->contact_zip;
        $partnerContact->other_number = Input::get('cphone1');
        $partnerContact->other_number_2 = Input::get('cphone2');
        $partnerContact->fax = Input::get('contactFax');
        $partnerContact->mobile_number = Input::get('mobileNumber');
        $partnerContact->email = Input::get('txtEmail2Pros');
        $partnerContact->update_by = auth()->user()->username;
        if(!$partnerContact->save()){
            return response()->json(array(
                'success'   => false, 
                'msg'       => "Unable to update contact.", 
            ), 200);
        }else {
            return response()->json(array(
                'success'   => true, 
                'msg'       => "Contact has been updated.", 
            ), 200);
        }
    }
    public function convertToMerchant(){
        $new_pir = "M" . Input::get('txtPartnerReferenceID');

        $convertToMerchant = Partner::find(Input::get('txtPartnerID'));

        $convertToMerchant->original_partner_id_reference = $convertToMerchant->partner_id_reference;
        $convertToMerchant->original_partner_type_id = $convertToMerchant->partner_type_id;
        $convertToMerchant->conversion_date = date('Y-m-d H:i:s');

        $convertToMerchant->update_by = auth()->user()->username;
        $convertToMerchant->partner_id_reference = $new_pir;
        $convertToMerchant->status = 'A';
        $convertToMerchant->merchant_mid = Input::get('txtMerchantMID');
        $convertToMerchant->partner_type_id = 3;    

        if (!$convertToMerchant->save()) {
            return response()->json(array(
                'success'   => false, 
                'msg'       => "Unable to convert prospect.", 
            ), 200);
        }else {
            return response()->json(array(
                'success'   => true, 
                'msg'       => "Prospect has been successfully converted to merchant.", 
            ), 200);
        }
    }
    public function getCalendarProfiles(){
        $create_by = "";
        $records = DB::raw("select u.id,u.first_name as fname,u.last_name as lname,u.username,u.email_address,ifnull(user_type_id,'') user_type_id
                    ,is_verified_email,is_verified_mobile,u.country_code,ifnull(us.description,'') as status_text 
                    ,ifnull(pc.company_name,'') as company_name           
                  from users u
                  left join user_statuses us on us.code = u.status
                  left join partner_companies pc on pc.partner_id=u.reference_id
                  where status<>'D'");
        if ($create_by != ""){
            $records .= DB::raw("and create_by='".$create_by."'");
        }
               
        $users = DB::select($records);
        $results = array();
        foreach($users as $r)
        {   
            if($r->user_type_id !== ""){
                $cmd = DB::raw("SELECT * FROM user_types WHERE status='A' and id in (".$r->user_type_id.")");
                $customs = DB::select($cmd);
                $r->user_types = $customs;
            } else {
                $r->user_types = array();   
            }
            $results[] = $r;
        }
        return response()->json(array('success' => true, 'users' => $results), 200);
    }
    public function getCalendarActivities(){
        $tasks = DB::select(DB::raw("select ca.*,ca2.attendees as parent_attendees,CONCAT(u.first_name,' ',u.last_name) as organizer,
            CONCAT(pc.first_name,' ',pc.last_name) as partner
            from calendar_activities ca 
            left join calendar_activities ca2 on ca.parent_id = ca2.id
            left join users u on u.id = ca2.user_id
            left join partners p on p.id = ca.partner_id
            left join partner_contacts pc on pc.partner_id = ca.partner_id
            where ca.status not in ('D','I','CF','DC') and ca.partner_id = " .Input::get('id')));
            // where ca.status not in ('D','I','CF','DC')"));
        return response()->json(array('success' => true, 'calendar' => $tasks), 200);
    }
    public function saveCalendarActivity(){
        DB::transaction(function(){
            $startDate = date('m/d/Y h:i:s A', strtotime(Input::get('calStart')));
            $endDate = date('m/d/Y h:i:s A', strtotime(Input::get('calEnd')));
            // Create Calendar Activity
            if (Input::get('calNew') == 1) {
                $calendarActivity = new CalendarActivity;
                $calendarActivity->user_id = auth()->user()->id;
                $calendarActivity->type = Input::get('calType');
                $calendarActivity->title = Input::get('calTitle');
                $calendarActivity->start_date = Input::get('calStart');
                $calendarActivity->end_date = Input::get('calEnd');
                $calendarActivity->start_time = Input::get('calStartTime');
                $calendarActivity->end_time = Input::get('calEndTime');
                $calendarActivity->agenda = Input::get('calAgenda');
                $calendarActivity->time_zone = Input::get('calTimez');
                $calendarActivity->reminder = Input::get('calRemind');
                $calendarActivity->attendees = Input::get('calAttend');
                $calendarActivity->location = Input::get('calLocation');
                $calendarActivity->frequency = Input::get('calFrequency');
                $calendarActivity->calendar_status = Input::get('calCalStatus');
                $calendarActivity->create_by = auth()->user()->username;
                $calendarActivity->status = Input::get('calStatus');
                $calendarActivity->partner_id = Input::get('calPartnerID');
                $calendarActivity->parent_id = -1;
                
                if (!$calendarActivity->save()) {
                    return response()->json(array(
                        'success' => false,
                        'message' => 'Unable to save calendar activity.'
                    ), 200);
                }
                // Input::get('calID') = $calendarActivity->id;
                $newCalID = $calendarActivity->id;
            }
            // Update Calendar Activity
            if(Input::get('calNew') == 0){
                $updateCalendarActivity = CalendarActivity::find(Input::get('calID'));//$calendarActivity->id
                $updateCalendarActivity->type = Input::get('calType');
                $updateCalendarActivity->title = Input::get('calTitle');
                $updateCalendarActivity->start_date = Input::get('calStart');
                $updateCalendarActivity->end_date = Input::get('calEnd');
                $updateCalendarActivity->start_time = Input::get('calStartTime');
                $updateCalendarActivity->end_time = Input::get('calEndTime');
                $updateCalendarActivity->agenda = Input::get('calAgenda');
                $updateCalendarActivity->time_zone = Input::get('calTimez');
                $updateCalendarActivity->reminder = Input::get('calRemind');
                $updateCalendarActivity->location = Input::get('calLocation');
                $updateCalendarActivity->frequency = Input::get('calFrequency');
                $updateCalendarActivity->calendar_status = Input::get('calCalStatus');
                $updateCalendarActivity->update_by = auth()->user()->username;
                $updateCalendarActivity->status = Input::get('calStatus');
                $updateCalendarActivity->remind_flag = 0;
                if (!(Input::get('calStatus') == 'C' || Input::get('calStatus') == 'D')) {
                    $updateCalendarActivity->attendees = Input::get('calAttend');
                }

                if(!$updateCalendarActivity->save()){
                    return response()->json(array(
                        'success' => false,
                        'message' => 'Unable to save calendar activity.'
                    ), 200);
                }
            }
            //
            if (Input::get('calStatus') == 'P') {
                $message ="Title: ".Input::get('calTitle')." | Location: ".Input::get('calLocation'). " | Start: ".$startDate.
                            " | End: ".$endDate." | Timezone: ".Input::get('calTimez');
                $newCalID = Input::get('calNew') == 0 ? Input::get('calID') : $newCalID;
                $attendees = json_decode(Input::get('calAttend'));
                foreach ($attendees as $a) {
                    $attend = explode(';',trim($a->value));
                    $calendarActivity = new CalendarActivity;
                    $calendarActivity->user_id = $attend[0];
                    $calendarActivity->type = Input::get('calType');
                    $calendarActivity->title = Input::get('calTitle');
                    $calendarActivity->start_date = Input::get('calStart');
                    $calendarActivity->end_date = Input::get('calEnd');
                    $calendarActivity->start_time = Input::get('calStartTime');
                    $calendarActivity->end_time = Input::get('calEndTime');
                    $calendarActivity->agenda = Input::get('calAgenda');
                    $calendarActivity->time_zone = Input::get('calTimez');
                    $calendarActivity->reminder = Input::get('calRemind');
                    $calendarActivity->location = Input::get('calLocation');
                    $calendarActivity->frequency = Input::get('calFrequency');
                    $calendarActivity->calendar_status = Input::get('calCalStatus');
                    $calendarActivity->create_by = auth()->user()->username;
                    $calendarActivity->status = 'I';
                    $calendarActivity->parent_id = $newCalID; //Input::get('calID');
                    $calendarActivity->partner_id = Input::get('calPartnerID');

                    if (!$calendarActivity->save()) {
                        return response()->json(array(
                            'success' => false,
                            'message' => 'Unable to save calendar activity.'
                        ), 200);
                    }
                    $id = $calendarActivity->id;
                    $username = DB::table('users')
                        ->select('username')
                        ->where('id',$attend[0])
                        ->first();
                    $subject = "You are invited in an activity";
                    $notification = new Notification;
                    $notification->partner_id = -1;
                    $notification->source_id = -1;
                    $notification->subject = $subject;
                    $notification->message = $message;
                    $notification->recipient = $username->username;
                    $notification->status = 'N';
                    $notification->create_by = auth()->user()->username;
                    $notification->redirect_url = '/calendar?activityID='.$id;
                    $notification->save();

                    //send enail
                    $email = DB::table('users')
                        ->select('email_address')
                        ->where('id',$attend[0])
                        ->first();
                    $email_address = $email->email_address;
                    $email_message = $message;
                    $email_subject = $subject;
                    $name = explode('(',$attend[2]);
                    
                    // Create Mail Notification
                    $data = array(
                        'email_address' => $email_address,
                        'first_name' => $name[0],
                        'last_name' => 'you are invited',
                        'email_message' => $email_message,
                        'email_subject' => $email_subject,
                    );
                    
                    Mail::send(['html'=>'mails.incominglead'],$data,function($message) use ($data){
                        $message->to($data['email_address'],$data['first_name']);
                        $message->subject('[GoETU] '.$data['email_subject']);
                        $message->from('no-reply@goetu.com');
                    });

                    if (Mail::failures()) {
                        return redirect('/calendar')->with('failed','Failed to send email.');
                    }  
                }

                $subject = "An Appointment has been Posted";
                $notification = new Notification;
                $notification->partner_id = -1;
                $notification->source_id = -1;
                $notification->subject = $subject;
                $notification->message = $message;
                $notification->recipient = auth()->user()->username;
                $notification->status = 'N';
                $notification->create_by = auth()->user()->username;
                $notification->redirect_url = '/calendar?activityID='.Input::get('calID');
                $notification->save();
            }
            //
            if(Input::get('calStatus') == 'C'){
                $attendees = json_decode(Input::get('calAttend'));
                foreach ($attendees as $a) {
                    $attend = explode(';',trim($a->value));
                    $id = Input::get('calID');
                    $username = DB::table('users')
                        ->select('username')
                        ->where('id',$attend[0])
                        ->first();
                    $subject = "Cancelled activity";
                    $message ="Title: ".Input::get('calTitle')." | Location: ".Input::get('calLocation'). " | Start: ".$startDate.
                    " | End: ".$endDate." | Timezone: ".Input::get('calTimez');
                    $notification = new Notification;
                    $notification->partner_id = -1;
                    $notification->source_id = -1;
                    $notification->subject = $subject;
                    $notification->message = $message;
                    $notification->recipient = $username->username;
                    $notification->status = 'N';
                    $notification->create_by = auth()->user()->username;
                    $notification->redirect_url = '/calendar?activityID='.$id;
                    $notification->save();
                }
            }
            if (Input::get('calStatus') == 'C' || Input::get('calStatus') == 'D') {
                $result = DB::table('calendar_activities')
                    ->where('parent_id',Input::get('calID'))
                    ->get();
                if (isset($result[0]->id)) {
                    $update = DB::table('calendar_activities')
                        ->where('parent_id', Input::get('calID'))
                        ->update(['status' => Input::get('calStatus')],['remind_flag' => 0]);
                    if (!$update) {
                        return response()->json(array(
                            'success' => false,
                            'message' => 'Unable to save calendar activity.'
                        ), 200);
                    }
                }
            }
            //
            if (Input::get('calStatus') == 'CF' || Input::get('calStatus') == 'DC') {
                $attendanceStatus = 'Declined';
                if (Input::get('calStatus') == 'CF') {
                    $attendanceStatus = 'Confirmed';
                }
                $parent_id = Input::get('calParentID');
                $result = DB::table('calendar_activities')
                    ->where('id',$parent_id)
                    ->get();
                if (isset($result[0]->id)) {
                    $attendees = json_decode($result[0]->attendees);
                    foreach ($attendees as $a) {
                        $attend = explode(';', trim($r->value));
                        if ($attend[0] == auth()->user()->id) {
                            $r->value = $attend[0]. ';' .$attendanceStatus. ';' .$attend[2];
                        }
                    }
                    $attendees = json_encode($attendees);

                    $updateCalendarActivity = CalendarActivity::find($parent_id);
                    $updateCalendarActivity->attendees = $attendees;
                    $updateCalendarActivity->remind_flag = 0;
                    if (!$updateCalendarActivity->save()) {
                        return response()->json(array(
                            'success' => false,
                            'message' => 'Unable to save calendar activity.'
                        ), 200);
                    }

                    $username = DB::table('users')
                        ->select('username')
                        ->where('id',$result[0]->user_id)
                        ->first();
                    $message ="Title: ".Input::get('calTitle')." | Location: ".Input::get('calLocation'). " | Start: ".$startDate.
                            " | End: ".$endDate." | Timezone: ".Input::get('calTimez');
                    $subject = auth()->user()->fname. ' ' .auth()->user()->lname. ' '.$attendanceStatus. ' Attendance on an appointment you set';
                    $notification = new Notification;
                    $notification->partner_id = -1;
                    $notification->source_id = -1;
                    $notification->subject = $subject;
                    $notification->message = $message;
                    $notification->recipient = $username->username;
                    $notification->status = 'N';
                    $notification->create_by = auth()->user()->username;
                    $notification->redirect_url = '/calendar?activityID='.$parent_id;
                    $notification->save();
                }
            }
        });
        return response()->json(array('success' => true, 'id' => Input::get('calID'),'message' => 'Calendar Activity Updated!'), 200);
    }
    public function saveCalendarReminder(){
        DB::transaction(function(){
            $updateCalendar = new CalendarActivity;
            $updateCalendar->reminder = Input::get('calRemind');
            $updateCalendar->frequency = Input::get('calFrequency');
            $updateCalendar->update_by = auth()->user()->username;
            if (!$updateCalendar->save()) {
                return response()->json(array('success' => false, 'message' => 'Unable to save calendar activity.'), 200);
            }
        });
        return response()->json(array('success' => false, 'id' => Input::get('calID'),'message' => 'Calendar Reminder Updated!'), 200);
    }
    public function uploadfile(Request $request){
        $logs = array();
        if($request->hasFile('fileUploadCSV')){
            $extension = $request->file('fileUploadCSV')->getClientOriginalExtension();//File::extension($request->file->getClientOriginalExtension());
            if ($extension == "csv") {
                $path = $request->file('fileUploadCSV')->storeAs(
                            'leads', $request->file('fileUploadCSV').'.'.$extension
                        );
                $data = Excel::load($request->file('fileUploadCSV'), function($reader) {})->get();
                //return $data; // will not proceed importing data
                $import_id = Partner::max('import_number');
                $import_id = $import_id == '' ? 1 : $import_id + 1;
                
                if(!empty($data) && $data->count()){
                    //Prelim
                    foreach ($data as $key => $value) {
                        $skip = false;
                        if ($value->partner_type == '' || strtolower($value->partner_type) != 'prospect') {
                            $logs[] =  "Skipping ".$value->partner_type.", invalid value for partner type.";
                            $skip = true;
                        } else {
                            $partner_type = DB::table('partner_types')->select('id')->where('name',strtoupper($value->partner_type))->first();
                            if (!$partner_type->id) {
                                $logs[] = "Skipping ".$value->partner_type." due to invalid partner_type.";
                                $skip = true;
                            }

                            $lead_id = DB::table('partners')->select(DB::raw('count(id)+1 max_id'))->where('partner_type_id',$partner_type->id)->first();
                            $partner_type_description = DB::table('partner_types')->select('name')->where('id',$partner_type->id)->first();
                            $upline = DB::table('partners')->select('id')->where('partner_id_reference',$value->upline)->first();
                            $country = isset($value->country) ? "United States" : $value->country;

                            if ($upline->id == '') {
                                $logs[] = "Skipping ".$value->dba." due to invalid upline.";
                                $skip = true;
                            }

                            if ($value->dba == '') {
                                $logs[] = "Skipping ".$value->dba." DBA must have a value.";
                                $skip = true;
                            }
                            if ($value->business_address_1 == '') {
                                $logs[] = "Skipping ".$value->dba." Business Address 1 must have a value.";
                                $skip = true;
                            }
                            /* if ($value->city == '') {
                                $logs[] = "Skipping ".$value->dba." City must have a value.";
                                $skip = true;
                            }
                            if ($value->state == '') {
                                $logs[] = "Skipping ".$value->dba." State must have a value.";
                                $skip = true;
                            } */
                            /* if ($value->country == '' || is_numeric($value->country)) {
                                $logs[] = "Skipping ".$value->dba." Country must have a value or must be valid.";
                                $skip = true;
                            }
                            if ($value->zip == '') {
                                $logs[] = "Skipping ".$value->dba." Zip must have a value.";
                                $skip = true;
                            } */
                            /** 
                            if ($value->phone_1 == '') {
                                $logs[] = "Skipping ".$value->dba." Business Phone 1 must have a value.";
                                $skip = true;
                            }
                            $pc2_exist = DB::table('partner_companies')->select('id')->where(DB::raw("ucase(phone1)"),$value->phone_1)->first();
                            if ($pc2_exist) {
                                $logs[] = "Skipping  ".$value->dba.", Business Phone 1 has already been used.";
                                $skip = true;
                            }

                            if ($value->mobile_number == '') {
                                $logs[] = "Skipping  ".$value->dba.", Mobile Number must have a value.";
                                $skip = true;
                            }
                            $pc3_exist = DB::table('partner_contacts')->select('id')->where(DB::raw("ucase(mobile_number)"),$value->mobile_number)->first();
                            if ($pc3_exist) {
                                $logs[] = "Skipping  ".$value->dba.", Mobile Number has already been used.";
                                $skip = true;
                            }
                            */
                            
                            $len = 12;
                            if (trim(strtolower($country)) == 'china') {
                                $len = 13;
                                if ($value->phone_1 != '') {
                                    if(!preg_match("/^[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->phone_1)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid phone 1.";
                                        $skip = true;
                                    }
                                }
                                if ($value->fax != '') {
                                    if(!preg_match("/^[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->fax)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid fax.";
                                        $skip = true;
                                    }
                                }
                                if ($value->mobile_number != '') {
                                    if(!preg_match("/^[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->mobile_number)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid mobile number.";
                                        $skip = true;
                                    }
                                }
                                if ($value->phone_2 != '') {
                                    if(!preg_match("/^[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->phone_2)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid phone 2.";
                                        $skip = true;
                                    }
                                }
                                if ($value->contact_phone_1 != '') {
                                    if(!preg_match("/^[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_phone_1)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid contact phone 1.";
                                        $skip = true;
                                    }
                                }
                                if ($value->contact_phone_2 != '') {
                                    if(!preg_match("/^[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_phone_2)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid contact phone 2.";
                                        $skip = true;
                                    }
                                }
                                if ($value->contact_fax != '') {
                                    if(!preg_match("/^[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_fax)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid contact fax.";
                                        $skip = true;
                                    }
                                }
                            } elseif (trim(strtolower($country)) == 'united states'
                                || trim(strtolower($country)) == 'philippines') {
                                if ($value->phone_1 != '') {
                                    if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->phone_1)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid phone 1.";
                                        $skip = true;
                                    }
                                }
                                if ($value->fax != '') {
                                    if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->fax)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid fax.";
                                        $skip = true;
                                    }
                                }
                                if ($value->mobile_number != '') {
                                    if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->mobile_number)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid mobile number.";
                                        $skip = true;
                                    }
                                }
                                if ($value->phone_2 != '') {
                                    if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->phone_2)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid phone 2.";
                                        $skip = true;
                                    }
                                }
                                if ($value->contact_phone_1 != '') {
                                    if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_phone_1)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid contact phone 1.";
                                        $skip = true;
                                    }
                                }
                                if ($value->contact_phone_2 != '') {
                                    if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_phone_2)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid contact phone 2.";
                                        $skip = true;
                                    }
                                }
                                if ($value->contact_fax != '') {
                                    if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_fax)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid contact fax.";
                                        $skip = true;
                                    }
                                }
                            }

                            

                            // Commented out for EZU Leads Upload; 
                            // details below was not provided

                            /* if ($value->email == '') {
                                $logs[] = "Skipping  ".$value->email.", Email must have a value.";
                                $skip = true;
                            }
                            $pc1_exist = DB::table('partner_companies')->select('id')->where(DB::raw("ucase(email)"),$value->email)->first();
                            if ($pc1_exist) {
                                $logs[] = "Skipping  ".$value->email.", Email has already been used.";
                                $skip = true;
                            }
                            
                            if ($value->ownership == '') {
                                $logs[] = "Skipping  ".$value->dba.", Ownership must have a value.";
                                $skip = true;
                            }
                            if ($value->first_name == '') {
                                $logs[] = "Skipping  ".$value->dba.", First Name must have a value.";
                                $skip = true;
                            }
                            if ($value->last_name == '') {
                                $logs[] = "Skipping  ".$value->dba.", Last Name must have a value.";
                                $skip = true;
                            }
                            if ($value->title == '') {
                                $logs[] = "Skipping  ".$value->dba.", Title must have a value.";
                                $skip = true;
                            } */

                            if($skip){goto skip;}

                            $insertPartnersData = new Partner;
                            $insertPartnersData->create_by                  = auth()->user()->username;
                            $insertPartnersData->update_by                  = auth()->user()->username;
                            $insertPartnersData->status                     = "A";
                            $insertPartnersData->partner_type_id            = $partner_type->id;
                            $insertPartnersData->original_partner_type_id   = $partner_type->id;
                            $insertPartnersData->parent_id                  = $upline->id;
                            $insertPartnersData->logo                       = "";
                            $insertPartnersData->partner_id_reference       = "";
                            $insertPartnersData->merchant_processor         = $value->current_processor != '' ? $value->current_processor : NULL;
                            $insertPartnersData->interested_products        = "";
                            $insertPartnersData->partner_status             = "New";
                            $insertPartnersData->import_number             = $import_id;
                            $insertPartnersData->business_type_code         = $value->business_industry == null || $value->business_industry == '' ? 
                                'OTHER' : 
                                $value->business_industry;

                            if (!$insertPartnersData->save()) {
                                $logs[] = "Unable to create partner."; 
                                goto skip;
                            }
                            $partner_id = $insertPartnersData->id;

                            $lead_id = substr($partner_type_description->name,0,1) .(10000+$lead_id->max_id); 
                            $update_partners = Partner::find($partner_id);
                            $update_partners->partner_id_reference = $lead_id;
                            if (!$update_partners->save()) {
                                $logs[] = "Unable to update partner id reference."; 
                                goto skip;
                            }

                            $country_code = DB::table('countries')
                                ->select('country_calling_code')
                                ->where('name',$country)
                                ->first();
                            $insert_partner_companies = new PartnerCompany;
                            $insert_partner_companies->partner_id           = $partner_id;
                            $insert_partner_companies->company_name         = trim($value->dba);
                            $insert_partner_companies->dba                  = $value->legal_name != '' ? trim($value->legal_name) : NULL;
                            $insert_partner_companies->country              = trim($country);
                            $insert_partner_companies->country_code         = $country_code->country_calling_code;
                            $insert_partner_companies->address1             = trim($value->business_address_1);
                            $insert_partner_companies->address2             = $value->business_address_2 != '' ? trim($value->business_address_2) : NULL;
                            $insert_partner_companies->city                 = trim($value->city);
                            $insert_partner_companies->state                = trim($value->state);
                            $insert_partner_companies->zip                  = trim($value->zip);
                            $insert_partner_companies->phone1               = substr($value->phone_1,0,$len);
                            $insert_partner_companies->phone2               = $value->phone_2 != '' ? substr($value->phone_2,0,$len) : NULL;
                            $insert_partner_companies->fax                  = $value->fax !=  '' ? '-' . substr($value->fax,0,$len) : NULL;
                            $insert_partner_companies->mobile_number        = substr($value->mobile_number,0,$len);
                            $insert_partner_companies->email                = $value->email;
                            $insert_partner_companies->ownership            = $value->ownership != '' ? trim($value->ownership) : NULL;

                            if (!$insert_partner_companies->save()) {
                                $logs[] = "Unable to create partner company."; 
                                goto skip;
                            }

                            $insert_partner_contacts = new PartnerContact;
                            $insert_partner_contacts->partner_id             = $partner_id;
                            $insert_partner_contacts->first_name             = $value->first_name != '' ? trim($value->first_name) : NULL;
                            $insert_partner_contacts->middle_name            = $value->middle_initial != '' ? substr($value->middle_initial,0,1) : NULL;
                            $insert_partner_contacts->last_name              = $value->last_name != '' ? trim($value->last_name) : NULL;
                            $insert_partner_contacts->position               = $value->title != '' ? trim($value->title) : NULL;
                            $insert_partner_contacts->country                = trim($country);
                            $insert_partner_contacts->country_code           = $country_code->country_calling_code;
                            $insert_partner_contacts->address1               = $value->contact_address_1 != '' ? trim($value->contact_address_1) : NULL;
                            $insert_partner_contacts->address2               = $value->contact_address_2 != '' ? trim($value->contact_address_2) : NULL;
                            $insert_partner_contacts->city                   = $value->contact_city != '' ? trim($value->contact_city) : NULL;
                            $insert_partner_contacts->state                  = $value->contact_state != '' ? trim($value->contact_state) : NULL;
                            $insert_partner_contacts->zip                    = $value->contact_zip != '' ? trim($value->contact_zip) : NULL;
                            $insert_partner_contacts->other_number           = $value->contact_phone_1 != '' ? substr($value->contact_phone_1,0,$len) : NULL;
                            $insert_partner_contacts->other_number_2         = $value->contact_phone_2 != '' ? substr($value->contact_phone_2,0,$len) : NULL;
                            $insert_partner_contacts->fax                    = $value->contact_fax != '' ? '-' . substr($value->contact_fax,0,$len) : NULL;
                            $insert_partner_contacts->mobile_number          = substr($value->mobile_number,0,$len); //isset($value->contact_mobile_number) ? '-' . substr($value->contact_mobile_number,0,$len) : '';
                            $insert_partner_contacts->email                  = $value->contact_email != '' ? $value->contact_email : NULL;
                            $insert_partner_contacts->is_original_contact    = 1;

                            if (!$insert_partner_contacts->save()) {
                                $logs[] = "Unable to create partner contacts."; 
                                goto skip;
                            }


                            $original_parent_id = $insertPartnersData->parent_id;
                            $creator_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id; 

                            if($original_parent_id != $creator_id){
                                $incomingLeads = new IncomingLead;
                                $incomingLeads->create_by = auth()->user()->username;
                                $incomingLeads->status = 'N';
                                $incomingLeads->assigned_id = $original_parent_id;
                                $incomingLeads->partner_id = $insertPartnersData->id;
                                $incomingLeads->partner_type_id = $insertPartnersData->partner_type_id;
                                $incomingLeads->creator_id = $creator_id;
                                $incomingLeads->previous_assigned_id = $creator_id;
                                $incomingLeads->save();
                                $insertPartnersData->parent_id = -1;
                                $insertPartnersData->save();

                                $recipient = DB::table('users')
                                    ->select('username')
                                    ->where('reference_id',$creator_id)
                                    ->first(); 
                                $recipient = $recipient == null ? -1 : $recipient->username;
                                $recipient1 = DB::select(DB::raw("select p.partner_id_reference as username from partners p left join partner_companies pc on p.id=pc.partner_id where p.id = ".$original_parent_id));
                                $recipients = [$recipient,$recipient1[0]->username];
                                $email_recipient = DB::table('partner_contacts')
                                    ->select('email')
                                    ->where('partner_id',$original_parent_id)
                                    ->first(); 
                                $email_message = "Request has been sent on incoming leads.";
                                $email_subject = $lead_id. ' - ' .  $insert_partner_contacts->first_name  . ' '. $insert_partner_contacts->last_name   . ' request for approval.';

                                for ($i=0; $i < sizeof($recipients); $i++) { 
                                    $notifications = new Notification;
                                    $notifications->partner_id = $original_parent_id;
                                    $notifications->source_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
                                    $notifications->recipient = $recipients[$i];
                                    $notifications->subject = $email_subject;
                                    $notifications->message = $email_message;
                                    $notifications->status = 'N';
                                    $notifications->create_by = auth()->user()->username;
                                    $notifications->redirect_url = '/leads/incoming';
                                    $notifications->save();
                                }
                                
                            }
                            
                        }
                    skip:
                    }
                }
            }
        } else {
            return response()->json(array('success' => false, 'message' => 'Please upload file.'), 200);
        }
        if (count($logs) > 0) {
            return response()->json(array('success' => true, 'message' => 'Error uploading file.', 'logs' => $logs), 200);
        }else {
            return response()->json(array('success' => true, 'message' => 'Successfully processed file.'), 200);
        }
    }
    public function advance_leads_prospects_search(Datatables $dt,$partner_type,$interested_products){
        // if ($partner_type == 'prospects') {
            $partner_type_id = 8;
        // }

        $result = Partner::partner_leads_prospects(-1,$partner_type_id,$interested_products);
        foreach ($result as $p) {
        $interested_products = '';  
            foreach ($p->interested_products as $i) {
                $interested_products .= $i->name .'; ';
                $p->interested_products = substr($interested_products,0,strlen($interested_products)-2);
            }
        }
        foreach ($result as $p) {
        $upline = '';
            foreach ($p->upline_partners as $u) {
                // $upline .= $u->first_name .' '. $u->last_name .' - '. $u->merchant_id;
                $upline .= $u->company_name .' - '. $u->merchant_id;
                $p->upline_partners = substr($upline,0,strlen($upline)-2);
            }
        }

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') !== false){
            return $dt::of($result)
                                ->editColumn('merchant_id', function ($result) {
                                    $mid='<a href="/prospects/details/profile/'.$result->partner_id.'">'.$result->merchant_id.'</a>';
                                    return $mid;
                                })
                                ->editColumn('phone1', function($result){
                                    return $result->company_country_code . $result->phone1; 
                                })
                                ->editColumn('mobile_number', function($result){
                                    return $result->contact_country_code . $result->mobile_number; 
                                })
                                /* ->editColumn('action', function($result){
                                    $deleteProspect = '<button type="button" class="btn btn-danger btn-sm" onclick="deleteProspect('.$result->partner_id.',\'D\')">Delete</button>';
                                    return $deleteProspect; 
                                }) */
                                // ->rawColumns(['merchant_id','phone1','mobile_number','action'])
                                ->rawColumns(['merchant_id','phone1','mobile_number'])
                                ->make(true);
        }else {
            return $dt::of($result)
                                ->editColumn('merchant_id', function ($result) {
                                    $mid='<a href="/prospects/details/profile/'.$result->partner_id.'">'.$result->merchant_id.'</a>';
                                    return $mid;
                                })
                                ->editColumn('phone1', function($result){
                                    return $result->company_country_code . $result->phone1; 
                                })
                                ->editColumn('mobile_number', function($result){
                                    return $result->contact_country_code . $result->mobile_number; 
                                })
                                ->rawColumns(['merchant_id','phone1','mobile_number'])
                                ->make(true);
        }
    }

    public function getInterestedProducts(){
        $partner_id = Input::get('partner_id');
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_product_id = '';
        }else {
            $parent_id = auth()->user()->reference_id;
            if ($parent_id == -1) {
                $parent_id = auth()->user()->reference_id;
                $partner_product = DB::table('partner_product_accesses')->where('partner_id',$parent_id)->first();
                $partner_product_id = $partner_product->product_access;
                if ($partner_product_id == '') {
                    $partner_product_id = -1;
                }
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    // $parent_product_ids = DB::raw("select distinct parent_id from products where id IN ($partner_product_id)");
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }else{
                $product_id = '';
                $products = PartnerProduct::get_partner_products($parent_id);  
                foreach($products as $r)
                {
                    $product_id = $product_id . $r->product_id . ",";
                }
                $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                if ($partner_product_id == ""){ //means no product access so we have to set it to -1 so they can't tag a single product
                    $partner_product_id = -1;    
                }     
                if ($partner_product_id != -1 && $partner_product_id != ""){
                    $partner_product_id = explode(',',$partner_product_id);
                    // $parent_product_ids = DB::raw("select distinct parent_id from products where id IN ($partner_product_id)");
                    $parent_product_ids = DB::table('products')->distinct()
                        ->whereIn('id',$partner_product_id)
                        ->get();

                    $product_id="";
                    foreach($parent_product_ids as $r)
                    {
                        $product_id = $product_id . $r->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }
        }

        $ip_id = DB::select("select interested_products as ip from partners where id=".$partner_id);

        $products = Product::api_get_products($partner_product_id,-1,-1,$ip_id[0]->ip == null ? -1 : $ip_id[0]->ip);

        $checkbox = ""; 
        if (count($products) > 0){
            foreach($products as $row){
                $checkbox .= '<tr><td>'.$row->name.'</td><td>'.$row->description.'</td><td><input class="btn btn-danger" type="checkbox" name="add_products[]" value="'.$row->id.'"></td></tr>';
            }
        }

        return response()->json(array('success' => true, 'products' => $checkbox,'message' => 'Products loaded!'), 200);
    }

    public function select_payment_frequencies()
    {
        $select = "";
        $frequency = PaymentFrequency::where('status','A')->orderBy('sequence')->get();
        foreach ($frequency as $f) {
            $select .= '<option value="'.$f->name.'"">'.$f->name.'</option>';
        }
        return array($select);
    }

    public function create_order($id,Request $request){
        DB::transaction(function() use ($id,$request){

            $details = $request->txtOrderDetails;
            $details = json_decode($details);
            $arr_of_pid = array();
            $batchID = ProductOrder::getBatchID();
            $billing_id="";
            foreach($details as $d){
                if (!in_array($d->main_pid,$arr_of_pid)) {
                    array_push($arr_of_pid,$d->main_pid);
                    $order = new ProductOrder;
                    // $order->product_id = $request->prodSelection;
                    $order->product_id = $d->main_pid;
                    $order->partner_id = $id;
                    $total_amt = 0;
                    $total_qty = 0;
                    foreach ($details as $detail) {
                        if($d->main_pid == $detail->main_pid){
                            $total_amt = $total_amt +$detail->amount;
                            $total_qty = $total_qty +$detail->qty;
                        }
                    }
                    $order->quantity = $total_qty;
                    $order->amount = $total_amt;
                    $order->status = 'Pending';
                    $order->save();

                    $sign_key = md5(microtime());
                    $sign_key = $sign_key . $order->id .md5(microtime());
                    $order->sign_code = $sign_key;
                    $order->create_by = auth()->user()->username;
                    $order->update_by = auth()->user()->username;
                    $order->batch_id = $batchID;
                    $order->billing_id = $billing_id;
                    $order->preferred_payment = $request->txtPreferredPayment;
                    $order->save();
                    
                    foreach ($details as $detail) {
                        if($d->main_pid == $detail->main_pid){
                            $orderDetail = new ProductOrderDetail;
                            $orderDetail->order_id = $order->id;
                            $orderDetail->product_id = $detail->product_id;
                            $orderDetail->amount = $detail->amount;
                            $orderDetail->quantity = $detail->qty;
                            $orderDetail->frequency = $detail->frequency;
                            $orderDetail->start_date = $detail->startdate;
                            $orderDetail->end_date = ($detail->enddate == "") ? '2999-01-01' : $detail->enddate;
                            $orderDetail->price = $detail->price;
                            $orderDetail->save();
                        }
                    }
                }
            }

            
            $new_pir = "M" . Input::get('txtPartnerReferenceID');

            $partner = Partner::find($id);
            $partner->update_by = auth()->user()->username;
            $partner->original_partner_id_reference = $partner->partner_id_reference;
            $partner->partner_id_reference = $new_pir;
            // $partner->status = 'P'; 
            $partner->partner_type_id = 3;
            $partner->merchant_status_id = MerchantStatus::BOARDING_ID;
            $partner->save();

            $dataPlain = [
                'partner_id' => $id,
                'create_by' => auth()->user()->username,
                'update_by' => auth()->user()->username,
            ];

            $data = [
                'country' => $partner->partner_company->country,
                'address' => $partner->partner_company->address,
                'city' => $partner->partner_company->city,
                'state' => $partner->partner_company->state,
                'zip' => $partner->partner_company->zip,
                'partner_id' => $id,
                'create_by' => auth()->user()->username,
                'update_by' => auth()->user()->username,
            ];

            PartnerDbaAddress::create($data);
            PartnerBillingAddress::create($data);
            PartnerMailingAddress::create($dataPlain);
            PartnerShippingAddress::create($dataPlain);

            $this->createUser($id);
            $partner->status = 'P'; 
            $partner->save();
        });

        return redirect('/merchants/details/'.$id.'/products#history')->with('success','Order has been created and Prospect converted to merchant!');
    }

    public function deleteProspect(){
        DB::transaction(function(){
            $updatePartner = Partner::find(Input::get('id'));
            $updatePartner->update_by = auth()->user()->username;
            $updatePartner->status = Input::get('status');
            if(!$updatePartner->save()){
                return response()->json(array('success' => false, 'msg' => 'Unable to remove prospect.'), 200);
            }
        });
        return response()->json(array('success' => true, 'msg' => 'Prospect successfully removed!'), 200);
    }

    public function convertToProspect(){
        $new_pir = "M" . Input::get('txtPartnerReferenceID');

        $convertToMerchant = Partner::find(Input::get('txtPartnerID'));
        $convertToMerchant->update_by = auth()->user()->username;
        $convertToMerchant->partner_id_reference = $new_pir;
        $convertToMerchant->status = 'A';
        $convertToMerchant->merchant_mid = Input::get('txtMerchantMID');
        $convertToMerchant->partner_type_id = 3;

        if (!$convertToMerchant->save()) {
            return response()->json(array(
                'success'   => false, 
                'msg'       => "Unable to convert prospect.", 
            ), 200);
        }else {
            $this->createUser(Input::get('txtPartnerID'));
            return response()->json(array(
                'success'   => true, 
                'msg'       => "Prospect has been successfully converted to merchant.", 
            ), 200);
        }
    }

    private function createUser($id)
    {
        $prospect = Partner::get_partner_info($id);
        $prospect = $prospect[0];
        $default_password = rand(1111111, 99999999);
        $default_encrypted_password = bcrypt($default_password);
        $country = Country::where('name',$prospect->country_name)->first();

        $user = new User;
        $user->username = $prospect->partner_id_reference;
        $user->password = $default_encrypted_password;
        $user->first_name = $prospect->first_name == null ? '' : $prospect->first_name; 
        $user->last_name = $prospect->last_name == null ? '' : $prospect->last_name;  
        $user->email_address = $prospect->email == null ? '' : $prospect->email; 
        $user->user_type_id = 8;
        $user->reference_id = $id;
        $user->status = 'A';
        $user->ein = '';
        $user->ssn = '';
        $user->city = $prospect->city == null ? '' : $prospect->city; 
        $user->state = $prospect->state == null ? '' : $prospect->state; 
        $user->country = $prospect->country_name;
        $user->zip = $prospect->zip;
        $user->business_address1 = $prospect->address1; 
        $user->mobile_number = $prospect->mobile_number;
        $user->business_phone1 = $prospect->phone1; 
        $user->extension = '';
        $user->mail_city = '';
        $user->mail_state = '';
        $user->mail_country = '';
        $user->mail_zip = '';
        $user->mail_address1 = '';
        $user->mail_address2 = '';
        $user->home_city = '';
        $user->home_state = '';
        $user->home_country = '';
        $user->home_zip = '';
        $user->home_address1 = '';
        $user->home_address2 = '';
        $user->home_landline = '';
        $user->country_code = $country->country_calling_code;
        $user->is_verified_email = 0;
        $user->is_verified_mobile = 0;

        $user->is_customer = -1;
        $user->is_agent =  -1;
        $user->is_merchant = 1;
        $user->is_iso =  -1;
        $user->is_admin =  -1;
        $user->is_partner =  1;

        $user->is_original_partner = 1;
        $user->create_by = auth()->user()->username;
        $user->update_by = auth()->user()->username;
        
        if ($prospect->parent_id != -1)
        {
            $user->company_id = Partner::get_upline_company($id);     
        } else {
            $user->company_id = -1;    
        }

        $user->save();

        $user_company = New UserCompany;
        $user_company->user_id = $user->id;
        $user_company->company_id = $user->company_id;
        $user_company->save();

        $user_type = New UserTypeReference;
        $user_type->user_id = $user->id;
        $user_type->user_type_id = $user->user_type_id;
        $user_type->save();

        if (isset($user->email_address)) {
            try {
                //send email
                $data = array(
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'password' => $default_password,
                    'email_address' => $user->email_address,
                    'username' => $user->username,
                );

                Mail::send(['html'=>'mails.accountcreation'],$data,function($message) use ($data){

                    $message->to($data['email_address'],$data['first_name'].' '.$data['last_name']);
                    $message->subject('[GoETU] Account Creation');
                    $message->from('no-reply@goetu.com');
                });

                if (Mail::failures()) {
                    return redirect('/merchants/create')->with('failed','Failed to send email.');
                } 
            } catch (Exception $e) {
                
            }

        } else {
            $mobile_number = $user->country_code.'-'.$user->mobile_number;
            $params = array(
                'user'      => 'GO3INFOTECH',
                'password'  => 'TA0828g3i',
                'sender'    => 'GoETU',
                'SMSText'   => 'Hi '.$user->first_name. ' ' .$user->last_name. ', welcome to GoETU Platform. Your password is: ' .$default_password,
                'GSM'       => str_replace("-","",$mobile_number),
            );
            $send_url = 'https://api2.infobip.com/api/v3/sendsms/plain?' . http_build_query($params);
            $send_response = file_get_contents($send_url);
        }  

    }

    public function summary($partner_id = null){
        $partner_access = -1;
        $id = $partner_id;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $access_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
            $partner_access = Partner::get_partners_access($access_id); 
        }

        if ($partner_access==""){$partner_access=$id;}

        $partner_info = Partner::get_partner_info($partner_id,false,"6,8",$partner_access);
        if (empty($partner_info)) {
            return redirect('/prospects')->with('failed','You have no access to that record.')->send();
        }

        // Profile
        // $partner_type = PartnerType::where([['status','A'],['included_in_leads',1]])->orderBy('sequence','asc')->get();
        $partner_type = PartnerType::get_partner_types(8, false,false,true);

        $country = Country::select('id','name','iso_code_2','iso_code_3')
            ->where('display_on_others', 1)
            ->get();

        $state = State::select('abbr as code','name')->where('country','US')->orderBy('name','asc')->get();

        $statePH = State::select('abbr as code','name')->where('country','PH')->orderBy('name','asc')->get();

        $stateCN = State::select('abbr as code','name')->where('country','CN')->orderBy('name','asc')->get();

        $ownership = Ownership::where('status','A')->orderBy('name','asc')->get();

        $partner_access=-1;
       
        if (strpos($admin_access, 'super admin access') === false){
            $reference_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
            $partner_access = Partner::get_partners_access();
        }

        if ($partner_access==""){$partner_access=$id;}
        $upline = Partner::get_upline_partner($id,$partner_access);

        $canEdit = 0;
        if (Access::hasPageAccess('prospect','edit',true)) {
            $canEdit = 1;
        }

        $canConvert = 0;
        if (Access::hasPageAccess('prospect','convert',true)) {
            $canConvert = 1;
        }

        // Note
        $is_agent = auth()->user()->is_agent;
        if ($is_agent > 0) {
            $is_admin = FALSE;
            $is_admin1 = 0;
        }else {
            $is_admin = TRUE;
            $is_admin1 = 1;
        }

        $comments = LeadComment::get_lead_comment($id,$is_admin1);
        $canAdd = 0;
        if (Access::hasPageAccess('prospect','add',true)) {
            $canAdd = 1;
        }

        $partner_status = DB::table('partner_statuses')
            ->select('name')
            ->where('status','A')
            ->orderBy('name','asc')
            ->get();
        $country_code = DB::table('countries')
                    ->select('country_calling_code')
                    ->where('name',$partner_info[0]->country_name)
                    ->first();
        $calling_code = $country_code->country_calling_code;

        $incoming_lead = IncomingLead::where('partner_id', $partner_id)->where('status','N')->first();
        $assigned_id = -1;
        if (isset($incoming_lead->assigned_id)) { 
            $assigned_id = $incoming_lead->assigned_id; 
        } 

        // $businessTypes = BusinessType::active()->orderBy('description')->get();
        $businessTypeGroups = Cache::get('business_types');
        $paymentProcessor = PaymentProcessor::active()->orderBy('name')->get();

        $isInternal = session('is_internal');

        $states = State::orderBy('abbr')->get();

        return view("prospects.details.summary", 
            compact('partner_id', 'businessTypeGroups', 'partner_info',
                'partner_type','country','state','statePH','stateCN','ownership',
                'canEdit','canConvert','upline','is_admin','canAdd',
                'partner_status','comments','calling_code','assigned_id',
                'paymentProcessor','isInternal','states'));
    }


}
