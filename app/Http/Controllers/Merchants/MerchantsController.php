<?php

namespace App\Http\Controllers\Merchants;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\BusinessType;
use App\Models\Drafts\DraftPartner;
use App\Models\Drafts\DraftPartnerAttachment;
use App\Models\LeadComment;
use App\Models\MerchantStatus;
use App\Models\Partner;
use App\Models\PartnerType;
use App\Models\PartnerCompany;
use App\Models\PartnerContact;
use App\Models\PartnerBillingAddress;
use App\Models\PartnerShippingAddress;
use App\Models\PartnerMailingAddress;
use App\Models\PartnerDbaAddress;
use App\Models\PartnerAttachment;
use App\Models\PartnerPaymentInfo;
use App\Models\PartnerSystem;
use App\Models\PartnerMid;
use App\Models\PaymentFrequency;
use App\Models\PaymentType;
use App\Models\Document;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductOrder;
use App\Models\ProductOrderDetail;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use App\Models\Ownership;
use App\Models\PartnerPaymentGateway;
use App\Models\PartnerProductAccess;
use App\Models\PartnerProduct;
use App\Models\SubTaskTemplateHeader;
use App\Models\SubTaskTemplateDetail;
use App\Models\SubTaskHeader;
use App\Models\SubTaskDetail;
use App\Models\TicketHeader;
use App\Models\WelcomeEmailTemplate;
use App\Models\UserType;
use Yajra\Datatables\Datatables;
use Cache;
use DB;
use Carbon\Carbon;
use File;
use Storage;
use PDF;
use Mail;
use DateTime;
use Excel;
use App\Models\OrderStatus;
use App\Models\ProductOrderComment;
use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoicePayment;
use App\Models\InvoiceFrequency;
use App\Models\InvoiceCommission;
use App\Models\BankAccountType;
use App\Models\EmailOnQueue;
use Illuminate\Support\Facades\Validator;

use CoPilot;
use Guesl\CardConnect\CoPilot\Models\Merchant;
use Guesl\CardConnect\CoPilot\Models\Demographic;
use Guesl\CardConnect\CoPilot\Models\Address;
use Guesl\CardConnect\CoPilot\Models\BankDetail;
use Guesl\CardConnect\CoPilot\Models\Bank;
use Guesl\CardConnect\CoPilot\Models\Ownership as Own;
use Guesl\CardConnect\CoPilot\Models\Owner;
use Guesl\CardConnect\CoPilot\Models\OwnerSiteUser;
use Guesl\CardConnect\CoPilot\Models\Order;
use Guesl\CardConnect\CoPilot\Models\OrderShippingDetail;
use Guesl\CardConnect\CoPilot\Models\Pricing;
use Guesl\CardConnect\CoPilot\Models\FlatPricing;
use Guesl\CardConnect\CoPilot\Models\Fee;
use Guesl\CardConnect\CoPilot\Models\BillingPlan;

use App\Contracts\Users\UserListService;
use App\Contracts\Workflow\WorkflowNotifyService;
use App\Models\Notification;
use App\Models\Language;
use App\Models\PartnerLanguage;
use App\Models\PartnerProfit;
use App\Models\PaymentProcessor;
use App\Models\UserCompany;
use App\Models\UserTypeReference;

use App\Models\UsZipCode;
use App\Models\PhZipCode;
use App\Models\CnZipCode;

use Illuminate\Support\Facades\Input;

class MerchantsController extends Controller
{
    protected $userListService;
    protected $workflowNotifyService;

    public function __construct(UserListService $userListService,
        WorkflowNotifyService $workflowNotifyService)
    {
        $this->userListService = $userListService;
        $this->workflowNotifyService = $workflowNotifyService;
    }

    public function index(){
        $advanceSearchLabel = 'Merchants';
    	$merchantSearch = true;
    	$statesPH = State::where('country','PH')->orderBy('name')->get();
    	$states = State::where('country','US')->orderBy('name')->get();
        $statesCN = State::where('country','CN')->orderBy('name')->get();
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;

        $pt_access = "";
        $pt_access .= isset($access['company']) ? "7," : "";
        $pt_access .= isset($access['iso']) ? "4," : "";
        $pt_access .= isset($access['sub iso']) ? "5," : "";
        $pt_access .= isset($access['agent']) ? "1," : "";
        $pt_access .= isset($access['sub agent']) ? "2," : "";
        $pt_access = ($pt_access == "") ? -1 : substr($pt_access, 0, strlen($pt_access) - 1); 

        if (auth()->user()->reference_id == -1) {
            $partner_id = auth()->user()->company_id;
        } else {
            $partner_id = auth()->user()->reference_id;
        }
        $partner_access="";
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($partner_id);
        }
        if ($partner_access==""){$partner_access=$partner_id;}
        $upline = Partner::get_downline_partner($partner_id,$partner_access,$pt_access);
        return view("merchants.list",compact('advanceSearchLabel','merchantSearch','statesPH','states','statesCN','canViewUpline','upline'));
    } 

    public function boardMerchant(){
        $advanceSearchLabel = 'Merchants';
        $merchantSearch = true;
        $statesPH = State::where('country','PH')->orderBy('name')->get();
        $states = State::where('country','US')->orderBy('name')->get();
        $statesCN = State::where('country','CN')->orderBy('name')->get();
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;
        return view("merchants.board",compact('advanceSearchLabel','merchantSearch','statesPH','states','statesCN','canViewUpline'));
    } 

    public function confirmMerchant($id) {

        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        if (strpos($merchantaccess, 'board merchant') === false){
            return Array('success' => false,'message' => 'No Access.');
        }

        DB::transaction(function() use ($id){
            $partner = Partner::find($id);
            $partner->status = 'C';
            $partner->merchant_status_id = MerchantStatus::FOR_APPROVAL_ID;
            $partner->confirmed_by = auth()->user()->username;
            $partner->save();
        });

        return Array('success' => true,'message' => 'Merchant has been boarded');
    }

    public function approveMerchant(){
        $advanceSearchLabel = 'Merchants';
        $merchantSearch = true;
        $statesPH = State::where('country','PH')->orderBy('name')->get();
        $states = State::where('country','US')->orderBy('name')->get();
        $statesCN = State::where('country','CN')->orderBy('name')->get();
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;
        return view("merchants.approve",compact('advanceSearchLabel','merchantSearch','statesPH','states','statesCN','canViewUpline'));
    } 

    public function finalizeMerchant($id) {

        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        if (strpos($merchantaccess, 'approve merchant') === false){
            return Array('success' => false,'message' => 'No Access.');
        }

        DB::transaction(function() use ($id){
            $partner = Partner::find($id);
            $partner->status = 'A';
            $partner->approved_by = auth()->user()->username;

            $order = $partner->productOrders->first();
            if (isset($partner->partner_company->email) && $order != null) {
                $agent = Partner::get_partner_info($partner->parent_id);   
                $agent = $agent[0];
                if ($agent->partner_type_description == 'AGENT' || $agent->partner_type_description == 'SUB AGENT') {
                    $agent_contact = "This email was sent on behalf of {$agent->first_name} with contact information below. <br> 
                        <p>
                            Agent Information <br>
                            Name: {$agent->first_name} {$agent->last_name} <br>
                            Email: {$agent->contact_email} <br>
                            Mobile: + {$agent->company_country_code}{$agent->mobile_number} <br>
                        </p> 
                        
                        <br><br>";
                } else {
                    $agent_contact = "This email was sent on behalf of {$agent->company_name} with contact information below.<br> 
                        <p>
                            Company Information <br>
                            Name: {$agent->company_name} <br>
                            Email: {$agent->email} <br>
                            Mobile: + {$agent->company_country_code}{$agent->phone1} <br>
                        </p>";
                }

                $link = "/merchants/{$order->id}/confirm_email";
                $email_address = $partner->partner_company->email;

                $data = array(
                    'link' => $link,
                    'merchant' => $partner,
                    'order' => $order,
                    'agent_contact' => $agent_contact,
                );

                Mail::send(['html'=>'mails.signature'], $data, function($message) use ($data, $partner){
                    $message->to($partner->partner_company->email ,$partner->partner_contact()->first_name.' '.$partner->partner_contact()->last_name);
                    $message->subject('[GoETU] Product Order'); 
                    $message->from('no-reply@goetu.com');
                });

                $order->status = 'PDF Sent';
                $order->date_sent = date('Y-m-d H:i:s');
                $order->save();

                $partner->merchant_status_id = MerchantStatus::LIVE_ID;
            } else {
                if ($order === null) {
                    $partner->merchant_status_id = MerchantStatus::BOARDED_ID;
                } else {
                    $partner->merchant_status_id = MerchantStatus::LIVE_ID;
                }
            }

            $partner->save();
        });      
        return Array('success' => true,'message' => 'Merchant has been approved');
    }

    public function declineMerchant(Request $request, $id)
    {
        if (Access::hasPageAccess('merchant', 'decline merchant', true)) {
            $partner = Partner::find($id);
            $partner->reason_of_action = $request->reason_of_action;
            $partner->merchant_status_id = MerchantStatus::DECLINED_ID;
            $partner->save();

            return [
                'success' => true,
                'message' => 'Merchant has been declined'
            ];
        }

        return [
            'success' => false,
            'message' => 'No Accecss'
        ];
    }

    public function draftMerchant(){
        $advanceSearchLabel = 'Merchants';
        $merchantSearch = true;
        $statesPH = State::where('country','PH')->orderBy('name')->get();
        $states = State::where('country','US')->orderBy('name')->get();
        $statesCN = State::where('country','CN')->orderBy('name')->get();
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;
        return view("merchants.draft",compact('advanceSearchLabel','merchantSearch','statesPH','states','statesCN','canViewUpline'));
    } 

    // public function dashboard($id){
    //     $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
    //     return view("merchants.details.dashboard",compact('id','merchant'));
    // }


    public function dashboard($id){
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        $partner_access=-1;
        $access = session('all_user_access');
       
        $isPartner = false;
        $isAdmin = true;

        if(auth()->user()->is_original_partner == 1){
            $partner_ids = Partner::get_merchant_uplines(auth()->user()->reference_id,$id);
            $isPartner = true;
        }else{
            $partner_ids = Partner::get_upline_partners_access($id);
        }

        $partner_ids = $partner_ids == "" ? -1 :$partner_ids.','.$id;
        $partners = Partner::where('status','A')->whereRaw('id in('.$partner_ids.')')->get();

        $js=$this->buildTree($partners,$isPartner);
        
        return view("merchants.details.dashboard",compact('js','merchant','id'));    
    }

    private function buildTree($partners,$isPartner){

        if($isPartner){
           foreach ($partners as $dep) {
                if($dep->id == auth()->user()->reference_id){
                    $dep->parent_id = -1;
                }
           } 
        }
        foreach ($partners as $dep) {
            $level = 0;
            $data = Partner::where('id',$dep->parent_id)->first();
            while(isset($data)){
                $level++;
                $data = Partner::where('id',$data->parent_id)->first();
            }
            $dep->level = $level;
        }
        $minLvl = 4;
        foreach ($partners as $dep) {
            if($minLvl > $dep->level){
               $minLvl =  $dep->level;
            }
        }

        $js = 'config = {
                        container: "#merchant-tree",
                        hideRootNode : true,
                        rootOrientation :"WEST",
                        nodeAlign : "CENTER",
                        node: {
                             HTMLclass: "nodeExample1"
                        },
                        animation: {
                            nodeAnimation: "easeOutBounce",
                            nodeSpeed: 700,
                            connectorsAnimation: "bounce",
                            connectorsSpeed: 700
                        },
                        connectors: {
                            type: "step"
                        },
                    };
                    hidden_parent = {text: { name: "CEO" }};';

        $addNode = "";
        
        foreach($partners->sortBy('level') as $key => $treeInfo){

            $leaderData = User::where('reference_id',$treeInfo->id)->where('is_original_partner',1)->first();
            $leader = isset($leaderData) ? $leaderData->first_name . ' ' . $leaderData->last_name : 'N/A';
            $partner_type = ($treeInfo->partner_type->name == 'ISO' || $treeInfo->partner_type->name == 'SUB ISO') ? $treeInfo->partner_type->name : ucfirst(strtolower($treeInfo->partner_type->name));

            $image = isset($leaderData) ? $leaderData->image : '/images/department.png';
            $node = 'node'.$treeInfo->id;
            $parent_node = ($treeInfo->level ==  $minLvl) ? 'hidden_parent' : 'node'.$treeInfo->parent_id;

            $access = session('all_user_access');
            $edit = "";
            $delete = "";
            if($treeInfo->partner_type->name == 'MERCHANT'){
                $edit = 'desc: { 
                            val: " ",
                            href: "/merchants/details/'.$treeInfo->id.'/profile",
                            target: "_self"
                        },';
            }else{
                $edit = 'desc: { 
                            val: " ",
                            href: "/partners/details/profile/'.$treeInfo->id.'/profileCompanyInfo",
                            target: "_self"
                        },';                
            }



            $js .= $node.' = {
                        parent: '. $parent_node.',
                        text: { name: "'.$treeInfo->partner_company->company_name.'" ,
                                title: "'.$partner_type .'",'.$edit . '},
                        image: "'.$image .'",
                        HTMLid: "'. $node.'",collapsed: false
                    };';   
        

            $addNode .= ','. $node;
        }

        $js .= 'tree_config = [
                    config,hidden_parent'.$addNode .'
                ];
                new Treant(tree_config);
                ';

        return $js;
    }

    public function profile($id){
    	$merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
    	if(!isset($merchant))
    	{
    		return redirect('/merchants')->with('failed','Cannot find merchant');
    	}
    	$country = Country::where('display_on_merchant', 1)->get();
    	$ownership = Ownership::get();
    	$statePH = State::where('country','PH')->orderBy('name')->get();
    	$stateUS = State::where('country','US')->orderBy('name')->get();
    	$stateCN = State::where('country','CN')->orderBy('name')->get();
        $bankAccountType = BankAccountType::where('status','A')->get();
    	$partner_contact = PartnerContact::where('partner_id',$id)->get();
    	$documents = Document::where('status','A')->get();
    	$partner_attachment = PartnerAttachment::where('partner_id',$id)->where('document_id',-2)->get();

    	$partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id);   
        }
        if ($partner_access==""){$partner_access=$id;}
    	$upline_partners =  Partner::get_upline_partner($id,$partner_access);  
    	$payment_gateways = PartnerPaymentGateway::where('partner_id',$id)->where('status','A')->get();
    	$partner_status = DB::table('partner_statuses')->select('name')->where('status','A')->orderBy('name','asc')->get();
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
    	$can_add = 1;
        $language = Language::where('status','A')->get();
        $businessTypeGroups = Cache::get('business_types');
        $systems = PartnerSystem::where('status','A')->get();

        // $isInternal = true;
        // if ((substr(auth()->user()->username,0,1) == 'C' 
        //     || substr(auth()->user()->username,0,1) != 'U')
        //     && auth()->user()->username != 'admin') {
        //     $isInternal = false;
        // }

        $isInternal = session('is_internal');
       
        $usCities = UsZipCode::select('city')->orderBy('city')->distinct()->get();
        $phCities = PhZipCode::select('city')->orderBy('city')->distinct()->get();
        $cnCities = CnZipCode::select('city')->orderBy('city')->distinct()->get();
        $states = State::orderBy('abbr')->get();

        $paymentProcessor = PaymentProcessor::active()->orderBy('name')->get();         

        return view("merchants.details.profile",
            compact('id','merchant','country','ownership','statePH','stateUS',
                'stateCN','partner_contact','documents','partner_attachment',
                'upline_partners','payment_gateways','can_add','partner_status',
                'comments','bankAccountType','language', 'businessTypeGroups',
                'systems','isInternal','states','paymentProcessor',
                'usCities','phCities','cnCities'));
    }

    public function products($id){
        $active_tab = "order-history";
        if(isset($_GET['tab'])) $active_tab = $_GET['tab'];
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canListOrder = (strpos($merchantaccess, 'order list') === false) ? false : true;
        $canCreateOrder = (strpos($merchantaccess, 'create order') === false) ? false : true;
        $canEditPaymentFrequency = (strpos($merchantaccess, 'order payment frequency edit') === false) ? false : true;

        if (!$canListOrder && !$canCreateOrder){
            return redirect('/merchants/details/'.$id.'/profile')->with('failed','No access to this page');
        }

    	$merchant = Partner::with('partner_company')->where('id',$id)->whereIn('partner_type_id',array(3,9))->first();
    	if($merchant->parent_id == -1){
            $partner_product_id = -1;  
    	}else{
    		$product_id="";
    		$products = PartnerProduct::get_partner_products($merchant->parent_id);
    		foreach($products as $p)
    		{
    			$product_id = $product_id . $p->product_id . ",";
    		}
    		$partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
    		if ($partner_product_id != -1 && $partner_product_id != ""){
    			$parent_product_ids = Product::	get_parent_product_id($partner_product_id);
                $product_id="";
                foreach($parent_product_ids as $r)
                {
                    $product_id = $product_id . $r->parent_id . ",";
                }
                $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
    		}
    	}
    		
		$products_temp = Product::api_get_products($partner_product_id, $id);
        $has_products = false;
        $products = array();
		foreach($products_temp as $p)
		{
            $p->subproducts = Product::get_child_products($p->id,$merchant->parent_id);
            $categories = Array();
            foreach($p->subproducts as $s){
            	$categories[] = $s->product_category_id;
            }
            $p->categories = ProductCategory::whereIn('id',$categories)->get();
            if(!empty($p->subproducts)){
                $products[] = $p;
                $has_products = true;
            }
		}
		
		$orders = ProductOrder::where('partner_id',$id)->orderBy('id','desc')->get();
		foreach($orders as $order)
		{
			switch ($order->status) {
				case 'Pending':
					$order->application_status = 'Pending Signature';
					break;
				case 'Signed':
					$order->application_status = 'Application Signed';
					break;
				default:
					$order->application_status = $order->status;
					break;
			}
			$order->invoiceDate = date("m/d/Y",strtotime($order->created_at));
            $order->task_status = ProductOrder::getCurrentTaskStatus($order->id);
		}

		$formOrderUrl = "/merchants/create_order/".$id;
		$formOrderEditUrl = "/merchants/update_order/".$id;

        $canSign = (strpos($merchantaccess, 'sign document') === false) ? false : true;
        $canProcessOrder = (strpos($merchantaccess, 'process order') === false) ? false : true;
        $payment_types = PaymentType::where('status','A')->orderBy('name','asc')->get();
        return view("merchants.details.products",compact('id','merchant','products','formOrderUrl','orders','formOrderEditUrl','has_products','canSign','canListOrder','canCreateOrder','canProcessOrder','canEditPaymentFrequency','active_tab','payment_types'));
    }

    public function rmaServicing($id){
    	$merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
        return view("merchants.details.rmaServicing",compact('id','merchant'));
    }

    public function billing($id){
    	$partner_id = isset(auth()->user()->reference_id) ? auth()->user()->reference_id : -1;

        // $partner_access = Partner::get_partners_access($partner_id);
        // if ($partner_access==""){$partner_access=$id;}

        // if ($partner_id==$id){
        //     $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8");
        // } else {
        //     $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        // }
        // if (count($partner_info)==0){
        //     return redirect('/')->with('failed','You have no access to that page.')->send();
        // }
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $viewInvoice = (strpos($merchantaccess, 'view invoice') === false) ? false : true;
        
        if (!$viewInvoice){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }


        $payment_types = PaymentType::where('status','A')->orderBy('name','asc')->get();
        $payment_methods = array();
        foreach($payment_types as $payment_type){
            $payment_details = PartnerPaymentInfo::get_payment_details($id, $payment_type->id);  
            $payment_methods[] = array(
                'id' => $payment_type->id,
                'name' =>  $payment_type->name,
                'details' =>$payment_details,
                'header' => explode("~",$payment_type['header_fields']),
                'body' => explode("~",$payment_type['header_values']),
            );  
        }
        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();

        $invoices = InvoiceHeader::where('partner_id',$id)->get();

        $recurring = InvoiceFrequency::where('partner_id',$id)->where('frequency','<>','One-Time')->get();

        $email = isset($merchant->partner_company->email) ? $merchant->partner_company->email : $merchant->partner_contact()->email;

        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canPay = strpos($merchantaccess, 'pay invoice') === false ? false : true;
        $canVoid = strpos($merchantaccess, 'void invoice') === false ? false : true;
        $canCharge = strpos($merchantaccess, 'update charges') === false ? false : true;
        $canCreate = strpos($merchantaccess, 'create invoice') === false ? false : true;
        return view("merchants.details.billing",compact('id','merchant','payment_types','payment_methods','invoices','recurring','email','canPay','canVoid','canCharge','canCreate'));
    }

    public function create(){
    	$statePH = State::where('country','PH')->orderBy('name')->get();
    	$stateUS = State::where('country','US')->orderBy('name')->get();
    	$stateCN = State::where('country','CN')->orderBy('name')->get();
        $bankAccountType = BankAccountType::where('status','A')->get();
    	$country = Country::where('display_on_merchant', 1)->get();
    	$ownership = Ownership::get();
        $documents = Document::where('status','A')->orderBy('sequence','asc')->get();

        $access = session('all_user_access');
        $pt_access = "";
        $pt_access .= isset($access['company']) ? "7," : "";
        $pt_access .= isset($access['iso']) ? "4," : "";
        $pt_access .= isset($access['sub iso']) ? "5," : "";
        $pt_access .= isset($access['agent']) ? "1," : "";
        $pt_access .= isset($access['sub agent']) ? "2," : "";
        $pt_access = ($pt_access == "") ? -1 : substr($pt_access, 0, strlen($pt_access) - 1); 
        // $partner_types = PartnerType::get_partner_types($pt_access);

        $partners_id = Partner::where('partner_id_reference',auth()->user()->username)->first();
        if (auth()->user()->reference_id == -1) {
            $partner_id = auth()->user()->company_id;
        } else if ($partners_id) {
            $partner_id = $partners_id->id;
        } else {
            $partner_id = auth()->user()->reference_id;
        }
        
        $partner_access="";
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($partner_id); // auth()->user()->reference_id
        }
        if ($partner_access==""){$partner_access=$partner_id;}
        $upline = Partner::get_downline_partner($partner_id,$partner_access,$pt_access);

        $is_internal = auth()->user()->is_original_partner == 0 ? true : false;
        $checkPartnerType = Partner::find(auth()->user()->reference_id);
        if(isset($checkPartnerType)){
            $is_internal = $checkPartnerType->partner_type->name == 'COMPANY' ? true : $is_internal;
        }


        /** Check user's department if system defined */
        $systemUser = false;
        $userTypeIds = explode(',', auth()->user()->user_type_id);
        foreach ($userTypeIds as $id) {
            if ( UserType::find($id)->create_by == 'SYSTEM' ) {
                $systemUser = true;
                break;
            }
        }

        $language = Language::where('status','A')->get();
        $ownerships = Ownership::where('status','A')->orderBy('name','asc')->get();

        $userDepartment = User::find(auth()->user()->id)->department->description;
        $formUrl = '/merchants/store';

        $userAccess = isset($access['draft applicants']) ? $access['draft applicants'] : "";
        $canSaveAsDraft = (strpos($userAccess, 'draft applicants list') === false) ? false : true;

        $systems = PartnerSystem::where('status','A')->get();

        $paymentProcessor = PaymentProcessor::active()->orderBy('name')->get();         

        $businessTypeGroups = Cache::get('business_types');

        $initialCities = UsZipCode::where('state_id', 1)->orderBy('city')->get();

        return view('merchants.create', compact('formUrl', 'statePH', 'stateUS', 
            'stateCN', 'country', 'ownership', 'documents', 'canSaveAsDraft', 
            'bankAccountType', 'systemUser','userDepartment','upline','is_internal',
            'language','ownerships', 'businessTypeGroups','systems', 'paymentProcessor',
            'initialCities'));
    } 

    public function merchant_data()
    {
        $partner_access=-1;
        $id = auth()->user()->reference_id; 
        
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
        $out_merchants=array();
        if ($partner_access==""){$partner_access=$id;}
        $pt_id="";
        if (Access::hasPageAccess('merchant','view',true)){
            $result1 = Partner::get_partners($partner_access,3,$id, -1, -1,"","","'A','I','T','V','P','C'",false);
            // $result2 = DraftPartner::get_draft_partners($partner_access,3,$id, -1, -1); // incomplete merchant application
			// $results = array_merge($result1,$result2);
            foreach ($result1 as $p) {
                $unverified="";
                $verify_mobile = Country::where('country_calling_code',$p->country_code)->first()->validate_number;
                if($verify_mobile==1)
                {
                    if ($p->is_verified_email==0 || $p->is_verified_mobile==0){
                        $unverified = ' <span class="badge badge-danger">unverified</span>';
                    }   
                }
                if ($canViewUpline) {
                    $upline = '';
                    $uplineRec = Partner::find($p->parent_id);
                    $upline .= $uplineRec->partner_company->company_name .' - <a href="/partners/details/profile/'.$p->parent_id.'/profileCompanyInfo">' . $uplineRec->partner_id_reference. '</a>';
                    // foreach ($p->upline_partners as $u) {
                    //     $upline .= $u->first_name .' '. $u->last_name .' > ' . $u->company_name .' - <a href="/partners/details/profile/'.$u->id.'/profileCompanyInfo">' . $u->merchant_id. '</a><br>';
                    //     $p->upline_partners = substr($upline,0,strlen($upline)-2);
                    // }
                }
                $incomplete = "";
                if($p->federal_tax_id == "" || $p->merchant_mid == "" || $p->credit_card_reference_id == ""
                    || $p->merchant_processor == "" || $p->company_name == "" || $p->dba == "" || $p->services_sold == ""
                    || $p->bank_name == "" || $p->bank_account_no == "" || $p->bank_routing_no == "" 
                    || $p->withdraw_bank_name == "" || $p->withdraw_bank_account_no == "" || $p->withdraw_bank_routing_no == ""
                    || $p->merchant_url == "" || $p->authorized_rep == "" || $p->IATA_no == "" || $p->tax_filing_name == ""){
                    $incomplete = ' <span title="Incomplete Merchant Info"><i class="fa fa-exclamation-triangle big-icon"></i></span> ';
                }
                $view = "";
                if($p->status == 'P'){
                    if (Access::hasPageAccess('merchant', 'board merchant',true)) {
                        $view .='<button class="btn btn-success btn-sm" onclick="boardMerchant('.$p->partner_id.')">Board</button>';
                    }
                    if (Access::hasPageAccess('merchant', 'decline merchant',true)) {
                        $view .= '&nbsp;&nbsp;';
                        $view .= '<button class="btn btn-warning btn-sm" onclick="declineMerchant('.$p->partner_id.')">Decline</button>';
                    }
                }
                if($p->status == 'C'){
                    if (Access::hasPageAccess('merchant', 'approve merchant',true)) {
                        $view .= '<button class="btn btn-success btn-sm" onclick="approveMerchant('.$p->partner_id.')">Approve</button>';
                    }
                    if (Access::hasPageAccess('merchant', 'decline merchant',true)) {
                        $view .= '&nbsp;&nbsp;';
                        $view .= '<button class="btn btn-warning btn-sm" onclick="declineMerchant('.$p->partner_id.')">Decline</button>';
                    }
                }

                $linkOpening = "<a href='/merchants/details/{$p->partner_id}/profile'>";
                $linkClosing = "</a>";

                /* if ($p->billing_status == 'Active') {
                    $status='<span style="color:green">Active</span>';
                } else {
                    $status='<span style="color:red">Cancelled</span>';
                } */

                $status = "";
                switch ($p->merchant_status_id) {
                    case MerchantStatus::BOARDED_ID:
                        $status = '<span style="color:green">Boarded</span>';
                        break;
                    
                    case MerchantStatus::LIVE_ID:
                        $status = '<span style="color:green">Live</span>';
                        break;

                    case MerchantStatus::CANCELLED_ID:
                        $status = '<span style="color:red">Cancelled</span>';
                        break;

                    case MerchantStatus::BOARDING_ID:
                        $status = '<span style="color:green">Boarding</span>';
                        break;
                    
                    case MerchantStatus::DECLINED_ID:
                        $status = '<span style="color:red">Declined</span>';
                        break;

                    case MerchantStatus::FOR_APPROVAL_ID:
                        $status = '<span style="color:green">For Approval</span>';
                        break;

                    
                }

                if ($p->merchant_status_id == MerchantStatus::LIVE_ID || $p->merchant_status_id == MerchantStatus::BOARDED_ID){
                    switch ($p->status) {
                        case 'A':
                            $status = '<span style="color:green">Live</span>';
                            break;
                        case 'V':
                            $status = '<span style="color:red">Cancelled</span>';
                            break;
                        case 'I':
                            $status = '<span style="color:red">Inactive</span>';
                            break;
                        case 'T':
                            $status = '<span style="color:red">Terminated</span>';
                            break;
                    }
                } 
                
                $PID = ''; 
                $order = ProductOrder::getPID($p->partner_id);
                foreach ($order as $o) {
                    $PID .= $o->PID . '<br>';
                }

                if ($canViewUpline) {
                    $out_merchants[] = array(
                        $p->partner_id_reference,
                        $upline,
                        $linkOpening . $incomplete .' '. $p->company_name . $linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $status,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $PID,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $p->country_code.$p->phone1,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                } else {
                    $out_merchants[] = array(
                        $p->partner_id_reference,
                        $linkOpening . $incomplete .' '. $p->company_name . $linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $status,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $PID ,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $p->country_code.$p->phone1,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                }
            }
        }
        return response()->json($out_merchants);   
    }

    public function merchant_board_data()
    {
        $partner_access=-1;
        $id = auth()->user()->reference_id; 
        
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
        $out_merchants=array();
        if ($partner_access==""){$partner_access=$id;}
        $pt_id="";
        if (Access::hasPageAccess('admin', 'super admin access', true) ||
            Access::hasPageAccess('merchant','view merchant boarding',true)){
            $result = Partner::get_partners($partner_access,3,$id, -1, -1,"","","'P'",false);
            foreach ($result as $p) {
                $unverified="";
                $verify_mobile = Country::where('country_calling_code',$p->country_code)->first()->validate_number;
                if($verify_mobile==1)
                {
                    if ($p->is_verified_email==0 || $p->is_verified_mobile==0){
                        $unverified = ' <span class="badge badge-danger">unverified</span>';
                    }   
                }
                $upline = '';
                $uplineRec = Partner::find($p->parent_id);
                $upline .= $uplineRec->partner_company->company_name .' - <a href="/partners/details/profile/'.$p->parent_id.'/profileCompanyInfo">' . $uplineRec->partner_id_reference. '</a>';

                // foreach ($p->upline_partners as $u) {
                //     $upline .= $u->first_name .' '. $u->last_name .' > ' . $u->company_name .' - <a href="/partners/details/profile/'.$u->id.'/profileCompanyInfo">' . $u->merchant_id. '</a><br>';
                //     $p->upline_partners = substr($upline,0,strlen($upline)-2);
                // }
                $incomplete = "";
                if($p->federal_tax_id == "" || $p->merchant_mid == "" || $p->credit_card_reference_id == ""
                    || $p->merchant_processor == "" || $p->company_name == "" || $p->dba == "" || $p->services_sold == ""
                    || $p->bank_name == "" || $p->bank_account_no == "" || $p->bank_routing_no == "" 
                    || $p->withdraw_bank_name == "" || $p->withdraw_bank_account_no == "" || $p->withdraw_bank_routing_no == ""
                    || $p->merchant_url == "" || $p->authorized_rep == "" || $p->IATA_no == "" || $p->tax_filing_name == ""){
                    $incomplete = ' <span title="Incomplete Merchant Info"><i class="fa fa-exclamation-triangle big-icon"></i></span> ';
                }
                
                $linkOpening = "<a href='/merchants/details/{$p->partner_id}/profile'>";
                $linkClosing = "</a>";
                $view="";
                if (Access::hasPageAccess('merchant', 'board merchant',true)) {
                    $view .='<button class="btn btn-success btn-sm" onclick="boardMerchant('.$p->partner_id.')">Board</button>';
                }
                if (Access::hasPageAccess('merchant', 'decline merchant',true)) {
                    $view .= '&nbsp;&nbsp;';
                    $view .= '<button class="btn btn-warning btn-sm" onclick="declineMerchant('.$p->partner_id.')">Decline</button>';
                }

                $status='<span style="color:blue">Boarding</span>';
                switch ($p->merchant_status_id) {
                    case MerchantStatus::BOARDING_ID:
                        $status = '<span style="color:green">Boarding</span>';
                        break;
                    
                    case MerchantStatus::DECLINED_ID:
                        $status = '<span style="color:red">Declined</span>';
                        break;
                }

                if ($canViewUpline) {
                    $out_merchants[] = array(
                        $p->partner_id_reference,
                        $upline,
                        $linkOpening . $incomplete .' '. $p->company_name . $linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $status,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $p->credit_card_reference_id,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $p->country_code.$p->phone1,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                } else {
                    $out_merchants[] = array(
                        $p->partner_id_reference,
                        $linkOpening . $incomplete .' '. $p->company_name . $linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $status,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $p->credit_card_reference_id,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $p->country_code.$p->phone1,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                }
            }
        }
        return response()->json($out_merchants);
    }

    public function merchant_approve_data()
    {
        $partner_access=-1;
        $id = auth()->user()->reference_id; 
        
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
        $out_merchants=array();
        if ($partner_access==""){$partner_access=$id;}
        $pt_id="";
        if (Access::hasPageAccess('merchant','view merchant approval',true)){
            $result = Partner::get_partners($partner_access,3,$id, -1, -1,"","","'C'",false);
            foreach ($result as $p) {
                $unverified="";
                $verify_mobile = Country::where('country_calling_code',$p->country_code)->first()->validate_number;
                if($verify_mobile==1)
                {
                    if ($p->is_verified_email==0 || $p->is_verified_mobile==0){
                        $unverified = ' <span class="badge badge-danger">unverified</span>';
                    }   
                }
                $upline = '';
                $uplineRec = Partner::find($p->parent_id);
                $upline .= $uplineRec->partner_company->company_name .' - <a href="/partners/details/profile/'.$p->parent_id.'/profileCompanyInfo">' . $uplineRec->partner_id_reference. '</a>';
                // foreach ($p->upline_partners as $u) {
                //     $upline .= $u->first_name .' '. $u->last_name .' > ' . $u->company_name .' - <a href="/partners/details/profile/'.$u->id.'/profileCompanyInfo">' . $u->merchant_id. '</a><br>';
                //     $p->upline_partners = substr($upline,0,strlen($upline)-2);
                // }
                $incomplete = "";
                if($p->federal_tax_id == "" || $p->merchant_mid == "" || $p->credit_card_reference_id == ""
                    || $p->merchant_processor == "" || $p->company_name == "" || $p->dba == "" || $p->services_sold == ""
                    || $p->bank_name == "" || $p->bank_account_no == "" || $p->bank_routing_no == "" 
                    || $p->withdraw_bank_name == "" || $p->withdraw_bank_account_no == "" || $p->withdraw_bank_routing_no == ""
                    || $p->merchant_url == "" || $p->authorized_rep == "" || $p->IATA_no == "" || $p->tax_filing_name == ""){
                    $incomplete = ' <span title="Incomplete Merchant Info"><i class="fa fa-exclamation-triangle big-icon"></i></span> ';
                }
                
                $linkOpening = "<a href='/merchants/details/{$p->partner_id}/profile'>";
                $linkClosing = "</a>";
                $view = "";
                if (Access::hasPageAccess('merchant', 'approve merchant',true)) {
                    $view .= '<button class="btn btn-success btn-sm" onclick="approveMerchant('.$p->partner_id.')">Approve</button>';
                }
                if (Access::hasPageAccess('merchant', 'decline merchant',true)) {
                    $view .= '&nbsp;&nbsp;';
                    $view .= '<button class="btn btn-warning btn-sm" onclick="declineMerchant('.$p->partner_id.')">Decline</button>';
                }
                $status='<span style="color:blue">For Approval</span>';
                switch ($p->merchant_status_id) {
                    case MerchantStatus::FOR_APPROVAL_ID:
                        $status = '<span style="color:green">For Approval</span>';
                        break;
                    
                    case MerchantStatus::DECLINED_ID:
                        $status = '<span style="color:red">Declined</span>';
                        break;
                }
                
                if ($canViewUpline) {
                    $out_merchants[] = array(
                        $p->partner_id_reference,
                        $upline,
                        $linkOpening . $incomplete .' '. $p->company_name . $linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $status,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $p->credit_card_reference_id,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $p->country_code.$p->phone1,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                } else {
                    $out_merchants[] = array(
                        $p->partner_id_reference,
                        $linkOpening . $incomplete .' '. $p->company_name . $linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $status,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $p->credit_card_reference_id,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $p->country_code.$p->phone1,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                }
            }
        }
        return response()->json($out_merchants);   

    }    

    public function merchant_draft_data()
    {
        $partner_access=-1;
        $id = auth()->user()->reference_id; 
        
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
        $out_merchants=array();
        if ($partner_access==""){$partner_access=$id;}
        $pt_id="";
        if (Access::hasPageAccess('draft applicants','draft applicants list',true)){
            $result = DraftPartner::get_draft_partners($partner_access,3,$id, -1, -1);
            foreach ($result as $p) {
                $unverified="";
                $verify_mobile = Country::where('country_calling_code',$p->country_code)->first()->validate_number;
                if($verify_mobile==1)
                {
                    if ($p->status != 'D') {
                        if ($p->is_verified_email==0 || $p->is_verified_mobile==0){
                            $unverified = ' <span class="badge badge-danger">unverified</span>';
                        }   
                    }
                }
                $upline = '';
                $uplineRec = Partner::find($p->parent_id);
                $upline .= $uplineRec->partner_company->company_name .' - <a href="/partners/details/profile/'.$p->parent_id.'/profileCompanyInfo">' . $uplineRec->partner_id_reference. '</a>';

                // foreach ($p->upline_partners as $u) {
                //     $upline .= $u->first_name .' '. $u->last_name .' > ' . $u->company_name .' - <a href="/partners/details/profile/'.$u->id.'/profileCompanyInfo">' . $u->merchant_id. '</a><br>';
                //     $p->upline_partners = substr($upline,0,strlen($upline)-2);
                // }
                $incomplete = "";
                if($p->federal_tax_id == "" || $p->merchant_mid == "" || $p->credit_card_reference_id == ""
                    || $p->merchant_processor == "" || $p->company_name == "" || $p->dba == "" || $p->services_sold == ""
                    || $p->bank_name == "" || $p->bank_account_no == "" || $p->bank_routing_no == "" 
                    || $p->withdraw_bank_name == "" || $p->withdraw_bank_account_no == "" || $p->withdraw_bank_routing_no == ""
                    || $p->merchant_url == "" || $p->authorized_rep == "" || $p->IATA_no == "" || $p->tax_filing_name == ""){
                    $incomplete = ' <span title="Incomplete Merchant Info"><i class="fa fa-exclamation-triangle big-icon"></i></span> ';
                }
                
                $linkOpening = "<a href='/drafts/draftMerchant/" . $p->partner_id . "/" . $p->partner_type_id . "/edit'>";
                $linkClosing = "</a>";
                $view = '<button class="btn btn-danger btn-sm" onclick="deleteDraftApplicant(' . $p->partner_id . ')" title="Delete"><i class="fa fa-trash"></i></button>';
                $status = '<span style="color:orange">Incomplete Merchant Application</span>';

                $phone = "";
                if ($p->phone1) {
                    $phone = $p->country_code . $p->phone1;
                }
                
                if ($canViewUpline) {
                    $out_merchants[] = array(
                        $upline,
                        $incomplete .' '. $p->company_name . '<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $linkOpening . $status . $linkClosing,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $p->credit_card_reference_id,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $phone,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                } else {
                    $out_merchants[] = array(
                        $incomplete .' '. $p->company_name . '<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $linkOpening . $status . $linkClosing,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $p->credit_card_reference_id,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $phone,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                }
            }
        }
        return response()->json($out_merchants);   

    }    

    // public function merchant_data(Datatables $datatables)
    // {
    //     $query = Partner::where('partner_type_id',3)->whereIn('status',array('A','I'))->get();
    //     return $datatables->collection($query)
    //                       ->editColumn('type', function ($data) {
    //                           return  $data->partner_type->name;
    //                       })
    //                       ->editColumn('partners', function ($data) {
    //                             if($data->parent_id == -1 || $data->parent_id == null){
    //                                 return 'Unassigned';
    //                             }
	   //                    	  	$upline_partner_access =  Partner::get_upline_partners_access($data->id); 
	   //                    	  	$up = "";
				// 	            if ($upline_partner_access != ""){
				// 	                $uplines = Partner::get_upline_partner_info($upline_partner_access,true);
				// 	                foreach ($uplines as $u) {
				// 	                	$up .= $u->first_name.' '.$u->last_name.' - '.$u->merchant_id.'<br>';
				// 	                }
				// 	            } else {
				// 	                $up = "";
				// 	            }
    //                           	return $up;
    //                       })
    //                       ->editColumn('merchant', function ($data) {
    //                           return  $data->partner_company->company_name;
    //                       })
    //                       ->editColumn('mid', function ($data) {
    //                           return  $data->merchant_mid;
    //                       })
    //                       ->editColumn('cid', function ($data) {
    //                           return  $data->credit_card_reference_id;
    //                       })
    //                       ->editColumn('contact', function ($data) {
	   //                        $unverified  = '';
    //                           if(isset($data->user()->is_verified_email))
    //                           {
    // 	                          if($data->user()->is_verified_email == 0 || $data->user()->is_verified_mobile == 0){
    // 	                          	$unverified = '<span class="label bg-gray">unverified</span>';
    // 	                          }
    //                           }
    //                           return  $data->partner_contact()->first_name.' '.$data->partner_contact()->last_name.' '.$unverified ;
    //                       })
    //                       ->editColumn('mobile', function ($data) {
    //                           return  $data->partner_company->country_code.$data->partner_contact()->mobile_number;
    //                       })
    //                       ->editColumn('email', function ($data) {
    //                           return  $data->partner_company->email;
    //                       })
    //                       ->editColumn('state', function ($data) {
    //                           return  $data->partner_company->state;
    //                       })
    //                       ->editColumn('url', function ($data) {
    //                           return  $data->merchant_url;
    //                       })
    //                       ->edfitColumn('action', function ($data) {
    //                             $message="'Delete this Merchant Template?'";
    //                             $view='<a class="btn btn-default btn-sm" href="/merchants/details/'.$data->id.'/profile">View</a>';
    //                             return $view;
    //                       })
    //                       ->rawColumns(['type','partners','merchant','mid','cid','contact','mobile','email','state','url','action'])
    //                       ->make(true);
        
    // }


    public function storeMerchant(Request $request)
    {
    	DB::transaction(function() use ($request, &$id, &$user){
	    	$partner = new Partner;
	    	$partner->partner_type_id = 3; 
	    	$partner->original_partner_type_id = 3;

	    	if ($request->selfAssign == 1) {
		    	$partner->parent_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;  
		    	$partner->original_parent_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id; 
	    	} else {
	            $partner->parent_id = $request->txtUplineId == null ? -1 : $request->txtUplineId;  
	            $partner->original_parent_id = $request->txtUplineId == null ? -1 : $request->txtUplineId; 	    		
            }
            
            /* $unpaid_switch = $request->txtTogBtnUnpaid == "on" ? 1 : 0;
            $paid_switch = $request->txtTogBtnPaid == "on" ? 1 : 0;
            $smtp_switch = $request->txtTogBtnSMTP == "on" ? 1 : 0; */
            $auto_switch = $request->txtTogBtnAutoEmailer == "on" ? 1 : 0;

            $max_count = Partner::where('partner_type_id',$partner->partner_type_id)->count() + 1;
            $partner_id_reference =  "M" . (100000+$max_count); 

            $partner->is_cc_client = $request->creditcardclient == "on" ? 1 : 0;
	    	$partner->logo = ''; 
	    	$partner->partner_id_reference = $partner_id_reference; 
	    	$partner->merchant_processor = $request->currentProccessor; 
	    	// $partner->merchant_mid = $request->txtMerchantMID; 
	    	// $partner->federal_tax_id = $request->txtFederalTaxID; 
	    	$partner->interested_products = ''; 
	    	$partner->partner_status = 'New'; 
	    	$partner->credit_card_reference_id = $request->txtCreditCardReferenceId;
	    	// $partner->services_sold = $request->txtServiceSold;
	    	// $partner->merchant_url = $request->txtMerchantURL;
	    	// $partner->authorized_rep = $request->txtAuthorizedRep;
	    	// $partner->IATA_no = $request->txtIATA;
	    	// $partner->tax_filing_name = $request->txtTaxName;

	    	// $partner->bank_account_no = $request->txtBankAccountNo;
	    	$partner->bank_routing_no = $request->txtBankRouting; //$request->txtRoutingNo;
            $partner->bank_name = $request->txtBankName;
            // $partner->bank_account_type_code = $request->txtBankAccountType;

            // $partner->withdraw_bank_account_no = $request->txtWBankAccountNo;
            // $partner->withdraw_bank_routing_no = $request->txtWRoutingNo;
            // $partner->withdraw_bank_name = $request->txtWBankName;
            // $partner->withdraw_bank_account_type_code = $request->txtWBankAccountType;

            // Fields for New Merchant Creation Form
            $partner->social_security_id = $request->txtSocialSecurityNumber;
            $partner->tax_id_number = $request->txtTaxIdNumber;
            $partner->bank_dda = $request->txtBankDDA;
            $partner->bank_address = $request->txtBankAddress;
            $partner->email_notifier = $request->txtEmailNotifier;
            /* $partner->email_unpaid_invoice = $unpaid_switch;
            $partner->email_paid_invoice = $paid_switch;
            $partner->smtp_settings = $smtp_switch; */
            $partner->auto_emailer = $auto_switch;
            $partner->billing_cycle = $request->txtBillingCycle;
            $partner->billing_month = $request->txtBillingMonth;
            $partner->billing_day = $request->txtBillingDay;
            // End Fields for New Merchant Creation Form


            $partner->business_type_code = $request->mcc;
	    	$partner->status = 'P';
	    	$partner->create_by = auth()->user()->username;
            $partner->update_by = auth()->user()->username;
            $partner->merchant_mid = $request->txtMID;
            $partner->merchant_url = $request->url;

            $partner->front_end_mid = $request->txtFrontEndMID;
            $partner->back_end_mid = $request->txtBackEndMID;
            $partner->reporting_mid = $request->txtReportingMID;

	    	$partner->save();
	    	$id = $partner->id;
            $parent_id = $partner->parent_id;

            if ($parent_id != -1)
            {
                $partner->company_id = Partner::get_upline_company($id);     
            } else {
                $partner->company_id = -1;    
            }

            $partner->merchant_status_id = MerchantStatus::BOARDING_ID;
            $partner->save();

            if($request->input('txtMID')!="")
            {
                $partnerMID = new PartnerMid;
                $partnerMID->partner_id = $partner->id;
                $partnerMID->mid = $request->input('txtMID');
                $partnerMID->system_id = $request->input('txtSystem');
                $partnerMID->create_by = auth()->user()->username;
                $partnerMID->save();
            }

            for ($x = 1; $x <= $request->midCtr; $x++) {
                if( $request->filled('txtMID'.$x)){
                    $partnerMID = new PartnerMid;
                    $partnerMID->partner_id = $partner->id;
                    $partnerMID->mid = $request->input('txtMID'.$x);
                    $partnerMID->system_id = $request->input('txtSystem'.$x);
                    $partnerMID->create_by = auth()->user()->username;
                    $partnerMID->save();
                }
            } 

            if (isset($request->languages)){
                foreach ($request->languages as $lang) {
                    $partnerLanguage = New PartnerLanguage;
                    $partnerLanguage->partner_id = $partner->id;
                    $partnerLanguage->language_id = $lang;
                    $partnerLanguage->save();
                } 
            }

	    	// Add Discussions
            /* if ($request->txtDiscussion != "") {
                $discussion = new LeadComment;
                $discussion->partner_id = $id;
                $discussion->comment = $request->txtDiscussion;
                $discussion->parent_id = -1;
                $discussion->create_by = auth()->user()->username;
                $discussion->user_id = auth()->user()->id;
                $discussion->attachment = "";
                $discussion->is_internal = 0;
                $discussion->lead_status = "";
                $discussion->save();
            } */

	    	if($request->txtCountry == 'United States')
	    	{
	    		$state = $request->txtState;
	    	}
	    	if($request->txtCountry == 'Philippines')
	    	{
	    		$state = $request->txtStatePH;
	    	}
	    	if($request->txtCountry == 'China')
	    	{
	    		$state = $request->txtStateCN;
	    	}

	    	/* if($request->txtDBACountry == 'United States')
	    	{
	    		$dba_state = $request->txtDBAState;
	    	}
	    	if($request->txtDBACountry == 'Philippines')
	    	{
	    		$dba_state = $request->txtDBAStatePH;
	    	}
	    	if($request->txtDBACountry == 'China')
	    	{
	    		$dba_state = $request->txtDBAStateCN;
	    	} */

	    	if($request->txtBillingCountry == 'United States')
	    	{
	    		$bill_state = $request->txtBillingState;
	    	}
	    	if($request->txtBillingCountry == 'Philippines')
	    	{
	    		$bill_state = $request->txtBillingStatePH;
	    	}
	    	if($request->txtBillingCountry == 'China')
	    	{
	    		$bill_state = $request->txtBillingStateCN;
	    	}

	    	/* if($request->txtShippingCountry == 'United States')
	    	{
	    		$ship_state = $request->txtShippingState;
	    	}
	    	if($request->txtShippingCountry == 'Philippines')
	    	{
	    		$ship_state = $request->txtShippingStatePH;
	    	}
	    	if($request->txtShippingCountry == 'China')
	    	{
	    		$ship_state = $request->txtShippingStateCN;
            } */
            
            if($request->txtMailingCountry == 'United States')
	    	{
	    		$mail_state = $request->txtMailingState;
	    	}
	    	if($request->txtMailingCountry == 'Philippines')
	    	{
	    		$mail_state = $request->txtMailingStatePH;
	    	}
	    	if($request->txtMailingCountry == 'China')
	    	{
	    		$mail_state = $request->txtMailingStateCN;
	    	}

	    	$country = Country::where('name',$request->txtCountry)->first();
	    	$partnerCompany = new PartnerCompany;
	    	$partnerCompany->partner_id = $id;
	    	$partnerCompany->company_name = $request->txtBusinessName; //$request->txtLegalBusinessName; //$request->txtCompanyName;
	    	$partnerCompany->business_name = $request->txtLegalBusinessName; //$crequest->txtBusinessName;
	    	// $partnerCompany->dba = $request->txtDBA;
            // $partnerCompany->country = $request->txtCountry;
            $partnerCompany->business_date = $request->txtBusinessDate;
	    	$partnerCompany->country_code = $country->country_calling_code;
            $partnerCompany->country = $request->txtCountry;
            $partnerCompany->address1 = $request->txtAddress;
            $partnerCompany->address2 = $request->txtAddress2;
            $partnerCompany->state = $state;
            $partnerCompany->city = $request->txtCity;
            $partnerCompany->zip = $request->txtZip;
                
	    	$partnerCompany->phone1 = $request->txtPhoneNumber; // $request->txtPhone1;
	    	$partnerCompany->phone2 = $request->txtPhoneNumber2; // $request->txtPhone2;
	    	// $partnerCompany->fax = $request->txtFax;
	    	$partnerCompany->mobile_number = $request->txtContactMobileNumber;
	    	$partnerCompany->email = $request->txtEmail;
	    	$partnerCompany->ownership = $request->txtOwnership;
            $partnerCompany->update_by = auth()->user()->username;
	    	$partnerCompany->save();

	    	/* $partnerDBA = new PartnerDbaAddress;
	    	$partnerDBA->partner_id = $id;
            $partnerDBA->country = $request->txtCountry;
            $partnerDBA->address = $request->txtAddress;
            $partnerDBA->city = $request->txtCity;
            $partnerDBA->state = $state;
            $partnerDBA->zip = $request->txtZip;
	    	$partnerDBA->create_by = auth()->user()->username;
	    	$partnerDBA->update_by = auth()->user()->username;
	    	$partnerDBA->save(); */

	    	/* $partnerBill = new PartnerBillingAddress;
	    	$partnerBill->partner_id = $id;
            $partnerBill->country = $request->txtCountry; 
            $partnerBill->address = $request->txtAddress;
            $partnerBill->city = $request->txtCity;
            $partnerBill->state = $state;
            $partnerBill->zip = $request->txtZip;	
	    	$partnerBill->create_by = auth()->user()->username;
	    	$partnerBill->update_by = auth()->user()->username;
	    	$partnerBill->save(); */

            /* $partnerShipping = new PartnerShippingAddress;
            $partnerShipping->partner_id = $id;
            
            if (isset($request->copy_to_shipping)) {
                $partnerShipping->country = $request->txtCountry; 
                $partnerShipping->address = $request->txtAddress;
                $partnerShipping->city = $request->txtCity;
                $partnerShipping->state = $state;
                $partnerShipping->zip = $request->txtZip;	
            } else {
                $partnerShipping->country = $request->txtShippingCountry;
                $partnerShipping->address = $request->txtShippingAddress;
                $partnerShipping->city = $request->txtShippingCity;
                $partnerShipping->state =  $request->txtShippingState;
                $partnerShipping->zip = $request->txtShippingZip;
            }
                    
            $partnerShipping->create_by = auth()->user()->username;
            $partnerShipping->update_by = auth()->user()->username;
            $partnerShipping->save(); */

            $partnerBilling = new PartnerBillingAddress;
            $partnerBilling->partner_id = $id;
            
            if (isset($request->copy_to_billing)) {
                $partnerBilling->country = $request->txtCountry; 
                $partnerBilling->address = $request->txtAddress;
                $partnerBilling->address2 = $request->txtAddress2;
                $partnerBilling->city = $request->txtCity;
                $partnerBilling->state = $state;
                $partnerBilling->zip = $request->txtZip;	
            } else {
                $partnerBilling->country = $request->txtBillingCountry;
                $partnerBilling->address = $request->txtBillingAddress;
                $partnerBilling->address2 = $request->txtBillingAddress2;
                $partnerBilling->city = $request->txtBillingCity;
                $partnerBilling->state =  $bill_state;
                $partnerBilling->zip = $request->txtBillingZip;
            }
                    
            $partnerBilling->create_by = auth()->user()->username;
            $partnerBilling->update_by = auth()->user()->username;
            $partnerBilling->save();

            $partnerMailing = new PartnerMailingAddress;
            $partnerMailing->partner_id = $id;

            if (isset($request->copy_to_mailing)) {
                $partnerMailing->country = $request->txtCountry; 
                $partnerMailing->address = $request->txtAddress;
                $partnerMailing->address2 = $request->txtAddress2;
                $partnerMailing->city = $request->txtCity;
                $partnerMailing->state = $state;
                $partnerMailing->zip = $request->txtZip;
            } else {
                $partnerMailing->country = $request->txtMailingCountry;
                $partnerMailing->address = $request->txtMailingAddress;
                $partnerMailing->address2 = $request->txtMailingAddress2;
                $partnerMailing->city = $request->txtMailingCity;
                $partnerMailing->state =  $mail_state;
                $partnerMailing->zip = $request->txtMailingZip;      
            }

            $partnerMailing->create_by = auth()->user()->username;
            $partnerMailing->update_by = auth()->user()->username;
            $partnerMailing->save();

	    	$partnerContact = new PartnerContact;
	    	$partnerContact->partner_id = $id;
	    	$partnerContact->first_name = $request->txtFirstName;
	    	$partnerContact->middle_name = $request->txtMiddleInitial;
	    	$partnerContact->last_name = $request->txtLastName;
	    	$partnerContact->position = $request->txtTitle;
	    	$partnerContact->country = '';
	    	$partnerContact->country_code =$country->country_calling_code;
	    	$partnerContact->address1 = $request->txtAddress;
	    	$partnerContact->address2 = '';
	    	$partnerContact->city = '';
	    	$partnerContact->state = '';
	    	$partnerContact->zip = '';
	    	// $partnerContact->other_number = $request->txtContactPhone1;
	    	// $partnerContact->other_number_2 = $request->txtContactPhone2;
	    	// $partnerContact->fax = $request->txtContactFax;
	    	$partnerContact->mobile_number = $request->txtContactMobileNumber;
            $partnerContact->email = $request->txtEmail; // $request->txtContactEmail;
	    	$partnerContact->is_original_contact = 1;
            $partnerContact->ssn = $request->txtSSN;
            // $partnerContact->issued_id = $request->txtIssuedID;
            $partnerContact->save();

            $details = $request->txtOtherHidden;
            if ($details) {
                $details = json_decode($details);
                foreach ($details as $d) {
                    if($request->input('txtFirstName'.$d) != "" && $request->input('txtLastName'.$d) != "")
                    {
                        $partnerContact = new PartnerContact;
                        $partnerContact->partner_id = $id;
                        $partnerContact->first_name = $request->input('txtFirstName'.$d);
                        $partnerContact->middle_name = $request->input('txtMiddleInitial'.$d);
                        $partnerContact->last_name = $request->input('txtLastName'.$d);
                        $partnerContact->position = $request->input('txtTitle'.$d);
                        $partnerContact->country = '';
                        $partnerContact->country_code =$country->country_calling_code;
                        $partnerContact->address1 ='';
                        $partnerContact->address2 = '';
                        $partnerContact->city = '';
                        $partnerContact->state = '';
                        $partnerContact->zip = '';
                        // $partnerContact->other_number = $request->input('txtContactPhone1'.$d);
                        // $partnerContact->other_number_2 = $request->input('txtContactPhone2'.$d);
                        // $partnerContact->fax = $request->input('txtContactFax'.$d);
                        $partnerContact->mobile_number = $request->input('txtContactMobileNumber'.$d);
                        // $partnerContact->email = $request->input('txtContactEmail'.$d);
                        $partnerContact->is_original_contact = 0;
                        $partnerContact->ssn = $request->input('txtSSN'.$d);
                        $partnerContact->save();    
                    }
                }
            }

            if ($request->txtDraftFile) {
                $draftPartnerAttachment = DraftPartnerAttachment::where('draft_partner_id', $request->txtDraftPartnerId)->get();
                foreach ($draftPartnerAttachment as $file) {
                    $attachment = new PartnerAttachment;
                    $attachment->partner_id = $id;
                    $attachment->name = $file->document_name;
                    $attachment->document_image = $file->document_image;
                    $attachment->document_id = $file->document_id;
                    $attachment->create_by = auth()->user()->username;
                    $attachment->update_by = auth()->user()->username;
                    $attachment->status = 'A';
                    $attachment->save();
                }
            }

            $documents = Document::where('status','A')->orderBy('name','asc')->get();
            foreach ($documents as $document) {
                if ($request->file('fileUpload'.$document->id)!== null){
                    $thefile = File::get($request->file('fileUpload'.$document->id));
                    $fileNameWithExt = $request->file('fileUpload'.$document->id)->getClientOriginalName();
                    $fileName = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                    $extension = $request->file('fileUpload'.$document->id)->getClientOriginalExtension();
                    $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;

                    Storage::disk('merchant_attachment')->put($filenameToStore,$thefile);

                    $attachment = new PartnerAttachment;
                    $attachment->partner_id = $id;
                    $attachment->name = $document->name;
                    $attachment->document_image = $filenameToStore;
                    $attachment->document_id = $document->id;
                    $attachment->create_by = auth()->user()->username;
                    $attachment->update_by = auth()->user()->username;
                    $attachment->status = 'A';
                    $attachment->save();
                }
            }

            $details1 = $request->txtOtherHidden1;
            if ($details1) {
                $details = json_decode($details1);
                foreach ($details as $d) {
                    if ($request->file('fileUploadOthers'.$d)!== null){
                        $thefile = File::get($request->file('fileUploadOthers'.$d));
                        $fileNameWithExt = $request->file('fileUploadOthers'.$d)->getClientOriginalName();
                        $fileName = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                        $extension = $request->file('fileUploadOthers'.$d)->getClientOriginalExtension();
                        $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
        
                        Storage::disk('attachment')->put($filenameToStore,$thefile);
        
                        $attachment = new PartnerAttachment;
                        $attachment->partner_id = $id;
                        $attachment->name = $request->input('OthersDescription'.$d);
                        $attachment->document_image = $filenameToStore;
                        $attachment->document_id = -2;
                        $attachment->create_by = auth()->user()->username;
                        $attachment->update_by = auth()->user()->username;
                        $attachment->status = 'A';
                        $attachment->save();
                    }
                }
            }

            if (isset($request->txtDraftPartnerId)) {
                $draft = DraftPartner::find($request->txtDraftPartnerId);
                $draft->is_stored_to_partners = 1;
                $draft->save();
            }

            
            /* if($request->txtPGName1 != "")
            {
                $paymentGateway = new PartnerPaymentGateway;
                $paymentGateway->partner_id = $id;
                $paymentGateway->name = $request->txtPGName1;
                $paymentGateway->key = $request->txtPGKey1;
                $paymentGateway->status = 'A';
                $paymentGateway->create_by = auth()->user()->username;
                $paymentGateway->update_by = auth()->user()->username;
                $paymentGateway->save(); 
            }

	    	if($request->txtPGName2 != "")
	    	{
		    	$paymentGateway = new PartnerPaymentGateway;
		    	$paymentGateway->partner_id = $id;
		    	$paymentGateway->name = $request->txtPGName2;
		    	$paymentGateway->key = $request->txtPGKey2;
		    	$paymentGateway->status = 'A';
	            $paymentGateway->create_by = auth()->user()->username;
	            $paymentGateway->update_by = auth()->user()->username;
		    	$paymentGateway->save(); 
	    	} 
	    	if($request->txtPGName3 != "")
	    	{
		    	$paymentGateway = new PartnerPaymentGateway;
		    	$paymentGateway->partner_id = $id;
		    	$paymentGateway->name = $request->txtPGName3;
		    	$paymentGateway->key = $request->txtPGKey3;
		    	$paymentGateway->status = 'A';
	            $paymentGateway->create_by = auth()->user()->username;
	            $paymentGateway->update_by = auth()->user()->username;
		    	$paymentGateway->save(); 	    		
	    	}  */

	    	//ADD MERCHANT USER'S CREATION
	    	$max_count = Partner::where('partner_type_id',3)->count() + 1;
	    	//$username = 'M'.(10000+$max_count);
            $username = $partner_id_reference;
	    	$partner_type = PartnerType::find(3);
	    	$default_password = rand(1111111, 99999999);
	    	$default_encrypted_password = bcrypt($default_password);

	    	$user = new User;
	    	$user->username = $username;
	    	$user->password = $default_encrypted_password;
	    	$user->first_name = $request->txtFirstName;
	    	$user->last_name = $request->txtLastName;
	    	$user->email_address = $request->txtEmail;
	    	$user->user_type_id = $partner_type->user_type_id;
	    	$user->reference_id = $id;
	    	$user->status = 'A';
	    	$user->ein = '';
	    	$user->ssn = '';
	    	$user->city = $request->txtCity;
	    	$user->state = $state;
	    	$user->country = $request->txtCountry;
	    	$user->zip = $request->txtZip;
	    	$user->business_address1 = $request->txtAddress; //$request->txtAddress1;
	    	// $user->business_address2 = $request->txtAddress2;
	    	// $user->fax = $request->txtFax;
	    	$user->mobile_number = $request->txtContactMobileNumber;
	    	$user->business_phone1 = $request->txtPhoneNumber; // $request->txtPhone1;
	    	$user->business_phone2 = $request->txtPhoneNumber2; // $request->txtPhone2;
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
            
            if ($parent_id != -1)
            {
                $user->company_id = Partner::get_upline_company($id);     
            } else {
                $user->company_id = -1;    
            }
            if ($request->hasFile("profileUpload")) {
                $attachment = $request->file('profileUpload');
                $fileName = pathinfo($attachment->getClientOriginalName(),PATHINFO_FILENAME);
                $extension = $attachment->getClientOriginalExtension();
                $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
                $storagePath = Storage::disk('public')->putFileAs('user_profile', $attachment,  $filenameToStore, 'public');
                $user->image = '/storage/user_profile/'.$filenameToStore;
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
                    'SMSText'   => 'Hi '.$user->first_name. ' ' .$user->last_name. ', welcome to GoETU Platform. Your username is :'.$user->username.' and password is: ' .$default_password,
                    'GSM'       => str_replace("-","",$mobile_number),
                );
                $send_url = 'https://api2.infobip.com/api/v3/sendsms/plain?' . http_build_query($params);
                $send_response = file_get_contents($send_url);
            }        
		
            return $id;
        });
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";

        // if (strpos($merchantaccess, 'create order') === false){
        //     return redirect('/merchants/details/'.$id.'/profile')->with('success','Merchant Created');
        // }else{
        //     return redirect('/merchants/details/'.$id.'/products#corder')->with('success','Merchant Created');
        // }

        return redirect('/merchants/details/'.$id.'/products?tab=create-order')->with([
            'success' => 'Merchant Created',
            'newUsername' => $user->username,
            'newUserId' => $user->id,
            'newEmail' => $user->email_address,
            'newFullName' => $user->first_name . ' ' . $user->last_name,
            'newImg' => $user->image,
        ]);
    }

    public function updateMerchantInfo($id,Request $request)
    {
    	DB::transaction(function() use ($id,$request){
	    	$partner = Partner::find($id);
	    	$partner->parent_id = $request->txtUplineId == null ? $partner->parent_id : $request->txtUplineId;
	    	$partner->merchant_processor = $request->currentProcessor; // $request->txtProcessor; 
	    	$partner->merchant_mid = $request->txtMerchantMID; 
	    	$partner->federal_tax_id = $request->txtFederalTaxID; 
	    	$partner->credit_card_reference_id = $request->txtCreditCardReferenceId;
	    	$partner->services_sold = $request->txtServiceSold;
	    	$partner->merchant_url = $request->txtMerchantURL;
	    	$partner->authorized_rep = $request->txtAuthorizedRep;
	    	$partner->IATA_no = $request->txtIATA;
	    	$partner->tax_filing_name = $request->txtTaxName;
            $partner->bank_account_no = $request->txtBankAccountNo;
            $partner->business_type_code = $request->mcc;
            // $partner->bank_routing_no = $request->txtBankRouting; // $request->txtRoutingNo;

            if(isset($request->txtBankRouting)){
                $partner->bank_routing_no  = strpos($request->txtBankRouting ,'X') === false ? $request->txtBankRouting: $partner->bank_routing_no ;
            }else{
                $partner->bank_routing_no = '';
            }
            

            if($partner->merchant_status_id == MerchantStatus::LIVE_ID || $partner->merchant_status_id == MerchantStatus::BOARDED_ID){
                $partner->status = $request->txtPartnerStatus;
                if($request->txtPartnerStatus == 'V' || $request->txtPartnerStatus == 'T'){
                    $request->billing_status = 'Cancelled';
                }else{
                    $request->billing_status = 'Active';
                }                
            }

            $partner->bank_name = $request->txtBankName;
            $partner->bank_account_type_code = $request->txtBankAccountType;

            $partner->withdraw_bank_account_no = $request->txtWBankAccountNo;
            $partner->withdraw_bank_routing_no = $request->txtWRoutingNo;
            $partner->withdraw_bank_name = $request->txtWBankName;
            $partner->withdraw_bank_account_type_code = $request->txtWBankAccountType;
            $partner->update_by = auth()->user()->username;
            // New Fields
	    	$partner->social_security_id = $request->txtSocialSecurityNumber;
	    	$partner->tax_id_number = $request->txtTaxIdNumber;
            $partner->bank_dda = $request->txtBankDDA;
            $partner->bank_address = $request->txtBankAddress;
            // End New Fields

            $partner->is_cc_client = $request->creditcardclient == "on" ? 1 : 0;
            $partner->merchant_url = $request->url;

            $partner->front_end_mid = $request->txtFrontEndMID;
            $partner->back_end_mid = $request->txtBackEndMID;
            $partner->reporting_mid = $request->txtReportingMID;
            
	    	$partner->save();

            PartnerLanguage::where('partner_id', $id)->delete();
            if (isset($request->languages)){
                foreach ($request->languages as $lang) {
                    $partnerLanguage = New PartnerLanguage;
                    $partnerLanguage->partner_id = $partner->id;
                    $partnerLanguage->language_id = $lang;
                    $partnerLanguage->save();
                } 
            }

            $partner->company_id =  Partner::get_upline_company($id);   
            $partner->save();

	    	$partnerCompany = PartnerCompany::where('partner_id',$id)->first();
	    	$partnerCompany->company_name = $request->txtBusinessName; //$request->txtLegalBusinessName; // $request->txtCompanyName;
	    	$partnerCompany->business_name = $request->txtLegalBusinessName; //$request->txtBusinessName;
	    	$partnerCompany->dba = $request->txtDBA;
	    	$partnerCompany->ownership = $request->txtOwnership;
	    	$partnerCompany->business_date = $request->txtBusinessDate;
	    	$partnerCompany->update_by = auth()->user()->username;
	    	$partnerCompany->save();

            $user = User::where('reference_id',$id)->where('is_original_partner',1)->first();
            if (isset($user->id))
            {
                if ($request->hasFile("profileUpload")
                    && $request->file('profileUpload') != $user->image) {
                    // delete old profile pic
                    Storage::disk('public')->delete(substr($user->image, 8));

                    $attachment = $request->file('profileUpload');
                    $fileName = pathinfo($attachment->getClientOriginalName(),PATHINFO_FILENAME);
                    $extension = $attachment->getClientOriginalExtension();
                    $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
                    $storagePath = Storage::disk('public')->putFileAs('user_profile', $attachment,  $filenameToStore, 'public');
                    $user->image = '/storage/user_profile/'.$filenameToStore;
                }

                $user->company_id =  Partner::get_upline_company($id);   
                $user->save();
            }

		});
    	return redirect('/merchants/details/'.$id.'/profile#businfo')->with('success','Merchant Updated');

    }

	public function updateMerchantAddress($id,Request $request) {
        
        DB::transaction(function() use ($request, &$id, &$user){
	    	if($request->txtCountry == 'United States')
	    	{
	    		$state = $request->txtState;
	    	}
	    	if($request->txtCountry == 'Philippines')
	    	{
	    		$state = $request->txtStatePH;
	    	}
	    	if($request->txtCountry == 'China')
	    	{
	    		$state = $request->txtStateCN;
	    	}

	    	/* if($request->txtDBACountry == 'United States')
	    	{
	    		$dba_state = $request->txtDBAState;
	    	}
	    	if($request->txtDBACountry == 'Philippines')
	    	{
	    		$dba_state = $request->txtDBAStatePH;
	    	}
	    	if($request->txtDBACountry == 'China')
	    	{
	    		$dba_state = $request->txtDBAStateCN;
	    	} */

	    	if($request->txtBillingCountry == 'United States')
	    	{
	    		$bill_state = $request->txtBillingState;
	    	}
	    	if($request->txtBillingCountry == 'Philippines')
	    	{
	    		$bill_state = $request->txtBillingStatePH;
	    	}
	    	if($request->txtBillingCountry == 'China')
	    	{
	    		$bill_state = $request->txtBillingStateCN;
            }

            if($request->txtMailingCountry == 'United States')
	    	{
	    		$mail_state = $request->txtMailingState;
	    	}
	    	if($request->txtMailingCountry == 'Philippines')
	    	{
	    		$mail_state = $request->txtMailingStatePH;
	    	}
	    	if($request->txtMailingCountry == 'China')
	    	{
	    		$mail_state = $request->txtMailingStateCN;
	    	} 

	    	/* if($request->txtShippingCountry == 'United States')
	    	{
	    		$ship_state = $request->txtShippingState;
	    	}
	    	if($request->txtShippingCountry == 'Philippines')
	    	{
	    		$ship_state = $request->txtShippingStatePH;
	    	}
	    	if($request->txtShippingCountry == 'China')
	    	{
	    		$ship_state = $request->txtShippingStateCN;
	    	} */

            $country = Country::where('name',$request->txtCountry)->first();
            
            /* $unpaid_switch = $request->txtTogBtnUnpaidPro == "on" ? 1 : 0;
            $paid_switch = $request->txtTogBtnPaidPro == "on" ? 1 : 0;
            $smtp_switch = $request->txtTogBtnSMTPPro == "on" ? 1 : 0; */
            $auto_switch = $request->txtTogBtnAutoPro == "on" ? 1 : 0;

	    	$partner = Partner::where('id',$id)->first();
            $partner->email_notifier = $request->txtEmailNotifier;
            /* $partner->email_unpaid_invoice = $unpaid_switch;
            $partner->email_paid_invoice = $paid_switch;
            $partner->smtp_settings = $smtp_switch; */
            $partner->auto_emailer = $auto_switch;
            $partner->billing_cycle = $request->txtBillingCycle;
            $partner->billing_month = $request->txtBillingMonth;
            $partner->billing_day = $request->txtBillingDay;
	    	$partner->update_by = auth()->user()->username;
	    	$partner->save();

	    	$partnerCompany = PartnerCompany::where('partner_id',$id)->first();
	    	$partnerCompany->country = $request->txtCountry;
	    	$partnerCompany->country_code = $country->country_calling_code;
	    	$partnerCompany->address1 = $request->txtAddress;
	    	$partnerCompany->address2 = $request->txtAddress2;
	    	$partnerCompany->zip = $request->txtZip;
	    	$partnerCompany->state = isset($state) ? $state : $partnerCompany->state;
            $partnerCompany->city = isset($request->txtCity) ? $request->txtCity : $partnerCompany->city;
            /* if($request->txtCopyDBA == 'true'){
		    	$partnerCompany->country = $request->txtDBACountry;
		    	$partnerCompany->address1 = $request->txtDBAAddress1;
		    	$partnerCompany->address2 = $request->txtDBAAddress2;
		    	$partnerCompany->city = $request->txtDBACity;
		    	$partnerCompany->state = $dba_state;
		    	$partnerCompany->zip = $request->txtDBAZip;
	    	}else{
	    		$partnerCompany->country = $request->txtCountry;
		    	$partnerCompany->address1 = $request->txtAddress1;
		    	$partnerCompany->address2 = $request->txtAddress2;
		    	$partnerCompany->city = $request->txtCity;
		    	$partnerCompany->state = $state;
		    	$partnerCompany->zip = $request->txtZip;
            } */

	    	$partnerCompany->phone1 = $request->txtPhoneNumber; // $request->txtPhone1;
	    	$partnerCompany->phone2 = $request->txtPhoneNumber2; // $request->txtPhone2;
            // $partnerCompany->fax = $request->txtFax;
	    	$partnerCompany->mobile_number = $request->txtContactMobileNumber;
	    	$partnerCompany->email = $request->txtEmail;
            $partnerCompany->update_by = auth()->user()->username;
	    	$partnerCompany->save();

	    	/* s$partnerDBA = PartnerDbaAddress::where('partner_id',$id)->first();
	    	if($request->txtCopyDBA == 'true'){
		    	$partnerDBA->country = $request->txtCountry;
		    	$partnerDBA->address =  $request->txtAddress; // $request->txtAddress1;
		    	// $partnerDBA->address2 = $request->txtAddress2;  
		    	$partnerDBA->city = $request->txtCity;
		    	$partnerDBA->state = $state;
		    	$partnerDBA->zip = $request->txtZip;
	    	} else {
	    		$partnerDBA->country = $request->txtDBACountry;
		    	$partnerDBA->address = $request->txtDBAAddress1;
		    	$partnerDBA->address2 = $request->txtDBAAddress2;
		    	$partnerDBA->city = $request->txtDBACity;
		    	$partnerDBA->state = $dba_state;
		    	$partnerDBA->zip = $request->txtDBAZip;
	    	}
	    	$partnerDBA->create_by = auth()->user()->username;
	    	$partnerDBA->update_by = auth()->user()->username;
	    	$partnerDBA->save(); */

	    	$partnerBill = PartnerBillingAddress::where('partner_id',$id)->first();
			/* if($request->txtCopyBill == 'true'){
		    	$partnerBill->country = $request->txtCountry; //$request->txtDBACountry;
		    	$partnerBill->address = $request->txtAddress; //$request->txtDBAAddress1;
		    	// $partnerBill->address2 = $request->txtDBAAddress2;
		    	$partnerBill->city = $request->txtCity; //$request->txtDBACity;
		    	$partnerBill->state = $state; //$dba_state;
		    	$partnerBill->zip = $request->txtZip; //$request->txtDBAZip;    		
	    	}else{ */
	    		$partnerBill->country = $request->txtBillingCountry;
		    	$partnerBill->address = $request->txtBillingAddress1;
		    	$partnerBill->address2 = $request->txtBillingAddress2;
		    	$partnerBill->zip = $request->txtBillingZip;
		    	$partnerBill->state = isset($bill_state) ? $bill_state : $partnerBill->state;
		    	$partnerBill->city = isset($request->txtBillingCity) ? $request->txtBillingCity : $partnerBill->city;
	    	// }
	    	$partnerBill->update_by = auth()->user()->username;
	    	$partnerBill->save();

	    	/* $partnerShip = PartnerShippingAddress::where('partner_id',$id)->first();
	    	if($request->txtCopyShip == 'true'){
		    	$partnerShip->country = $request->txtShippingCountry; //$request->txtDBaCountry;
		    	$partnerShip->address = $request->txtShippingAddress; //$request->txtDBaAddress1;
		    	// $partnerShip->address2 = $request->txtDBAAddress2;
		    	$partnerShip->city = $request->txtShippingCity;  //$request->txtDBACity;
		    	$partnerShip->state = $request->txtShippingState;  //$dba_state;
		    	$partnerShip->zip = $request->txtShippingZip; //$request->txtDBAZip;    		
	    	} else {
		    	$partnerShip->country = $request->txtShippingCountry;
		    	$partnerShip->address = $request->txtShippingAddress1;
		    	$partnerShip->address2 = $request->txtShippingAddress2;
		    	$partnerShip->city = $request->txtShippingCity;
		    	$partnerShip->state = $ship_state;
		    	$partnerShip->zip = $request->txtShippingZip;
		    }
	    	$partnerShip->update_by = auth()->user()->username;
	    	$partnerShip->save(); */

            $partnerMail = PartnerMailingAddress::where('partner_id',$id)->first();
            if(!isset($partnerMail)){
                $partnerMail = new PartnerMailingAddress;
                $partnerMail->partner_id = $id;
            }
            $partnerMail->country = $request->txtMailingCountry;
            $partnerMail->address = $request->txtMailingAddress;
            $partnerMail->address2 = $request->txtMailingAddress2; 
            $partnerMail->zip = $request->txtMailingZip;         
            $partnerMail->state = isset($mail_state) ? $mail_state : $partnerMail->state;
            $partnerMail->city = isset($request->txtMailingCity) ? $request->txtMailingCity : $partnerMail->city;
            $partnerMail->update_by = auth()->user()->username;
            $partnerMail->save();

            $user = User::where('reference_id',$id)->first();
            if(isset($user)){
                $user->email_address = $request->txtEmail;
                $user->save();
            }


        });
        
        return redirect('/merchants/details/'.$id.'/profile#adrs')->with([
            'success' => 'Merchant Updated',
            'newUsername' => $user->username,
            'newUserId' => $user->id,
            'newEmail' => $user->email_address,
            'newFullName' => $user->first_name . ' ' . $user->last_name,
            'newImg' => $user->image,
        ]);

    }

    
    public function merchant_contact_info($id)
    {
        $columns = ['dob', 'business_acquired_date', 'id_exp_date'];
        $contact = PartnerContact::find($id);
        foreach ($columns as $column) {
            if ($contact->$column !== null) {
                $contact->$column = Carbon::parse($contact->$column)->format('m/d/Y');
            } 
        }

        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        if (strpos($merchantaccess, 'display complete ssn and bank info') === false){
            $contact->ssn =  'XXX-XX-'.substr($contact->ssn,7,4);
        }

        return $contact;
    }


    public function get_mid_details($id)
    {
        $mid = PartnerMid::find($id);
        $mid->format = $mid->system->mid_format;
        return $mid;
    }

    public function updatePartnerMID($id,Request $request)
    {
        DB::transaction(function() use ($id,$request){
            if($request->midID == -1){
                $mid = new PartnerMid;
                $mid->partner_id =$id;
                $mid->system_id =$request->txtSystem;
                $mid->mid =$request->txtMIDVal;
                $mid->create_by = auth()->user()->username;  
            }else{
                $mid = PartnerMid::find($request->midID);
                $mid->partner_id =$id;
                $mid->system_id =$request->txtSystem;
                $mid->mid =$request->txtMIDVal;
                $mid->update_by = auth()->user()->username;              
            }

            $mid->save();

        });
        $partner = Partner::find($id);
        if($partner->partner_type_id == 3){
            return redirect('/merchants/details/'.$id.'/profile#mid')->with('success','MID Updated');
        }else{
            return redirect('/merchants/branchDetails/'.$id.'/profile#mid')->with('success','MID Updated');
        }
        

    }

	public function updateMerchantContact($id,Request $request){
    	DB::transaction(function() use ($id, $request){
            $access = session('all_user_access');
            $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
            $canVerifySSN = strpos($merchantaccess, 'verify contact ssn') === false ? false : true;

    		if($request->contID == -1)
    		{
		    	$partnerContact = new PartnerContact;
		    	$partnerContact->partner_id = $id;
		    	$partnerContact->first_name = $request->txtFirstName;
		    	$partnerContact->middle_name = $request->txtMiddleInitial;
		    	$partnerContact->last_name = $request->txtLastName;
		    	$partnerContact->position = $request->txtTitle;
		    	$partnerContact->other_number = $request->txtContactPhone1;
		    	$partnerContact->other_number_2 = $request->txtContactPhone2;
		    	$partnerContact->fax = $request->txtContactFax;
		    	$partnerContact->mobile_number = $request->txtContactMobile;
		    	$partnerContact->email = $request->txtContactEmail;

		    	$partnerContact->dob = $request->txtDOB != null ? date('Y-m-d', strtotime($request->txtDOB)) : null;
		    	$partnerContact->business_acquired_date = $request->txtDateAcquired != null ? date('Y-m-d', strtotime($request->txtDateAcquired)) : null;
                $partnerContact->id_exp_date = $request->txtExpDate != null ? date('Y-m-d', strtotime($request->txtExpDate)) : null;
                
		    	$partnerContact->ssn = isset($request->txtSSN) ? $request->txtSSN : '';
		    	$partnerContact->issued_id = $request->txtIssuedID;
		    	$partnerContact->ownership_percentage =$request->txtPercentageOwnership;

		    	$partnerContact->address1 ='';
		    	$partnerContact->address2 = '';
		    	$partnerContact->city = '';
		    	$partnerContact->state = '';
		    	$partnerContact->zip = '';
		    	$partnerContact->country = '';
		    	$partnerContact->country_code =$request->contCallCode;
		    	$partnerContact->is_original_contact = 0;
                if($canVerifySSN){
                    $partnerContact->ssn_verified = isset($request->verifySSN) ? 1 : 0;
                }

		    	$partnerContact->save();   			

    		}else{
		    	$partnerContact = PartnerContact::find($request->contID);
		    	$partnerContact->first_name = $request->txtFirstName;
		    	$partnerContact->middle_name = $request->txtMiddleInitial;
		    	$partnerContact->last_name = $request->txtLastName;
		    	$partnerContact->position = $request->txtTitle;
		    	$partnerContact->other_number = $request->txtContactPhone1;
		    	$partnerContact->other_number_2 = $request->txtContactPhone2;
		    	$partnerContact->fax = $request->txtContactFax;
		    	$partnerContact->mobile_number = $request->txtContactMobile;
		    	$partnerContact->email = $request->txtContactEmail;

		    	$partnerContact->dob = $request->txtDOB ? date('Y-m-d', strtotime($request->txtDOB)) : null;
		    	$partnerContact->business_acquired_date = $request->txtDateAcquired ? date('Y-m-d', strtotime($request->txtDateAcquired)) : null;
                $partnerContact->id_exp_date = $request->txtExpDate ? date('Y-m-d', strtotime($request->txtExpDate)) : null;
                
                if(isset($request->txtSSN)){
                    $partnerContact->ssn = strpos($request->txtSSN,'X') === false ? $request->txtSSN : $partnerContact->ssn ;
                }else{
                    $partnerContact->ssn = '';
                }
		    	


		    	$partnerContact->issued_id = $request->txtIssuedID;
		    	$partnerContact->ownership_percentage =$request->txtPercentageOwnership;
                if($canVerifySSN){
                    $partnerContact->ssn_verified = isset($request->verifySSN) ? 1 : 0;
                }

		    	$partnerContact->save();      			
            }
            
            if($request->isOrigCon == 1){
                $user = User::where('reference_id',$id)->where('status','A')->first();
                if(isset($user)){
                    $user->first_name = $request->txtFirstName;
                    $user->last_name = $request->txtLastName;
                    $user->mobile_number = $request->txtContactMobile;
                    // $user->email_address = $request->txtContactEmail;
                    $user->save();

                    return redirect('/merchants/details/'.$id.'/profile#ownrinf')->with([
                        'success' => 'Contacts Updated',
                        'newUsername' => $user->username,
                        'newUserId' => $user->id,
                        'newEmail' => $user->email_address,
                        'newFullName' => $user->first_name . ' ' . $user->last_name,
                        'newImg' => $user->image,
                    ]);
                }

            }
            
		});
    	return redirect('/merchants/details/'.$id.'/profile#ownrinf')->with('success','Contacts Updated');
    }

	public function updateMerchantAttachment(Request $request){
		$id = $request->txtDocumentPartnerId;
    	DB::transaction(function() use ($id,$request){

            $thefile = File::get($request->file('fileUploadAttachment'));
            $fileNameWithExt = $request->file('fileUploadAttachment')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
            $extension = $request->file('fileUploadAttachment')->getClientOriginalExtension();
            $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
            Storage::disk('merchant_attachment')->put($filenameToStore,$thefile);

            $name = strtoupper($request->txtUploadAttachment);
            $docu_name = $request->txtUploadAttachment;
            $docu_id = $request->txtDocumentId;
            if ($request->txtDocumentId < 0) {
                $docu = DB::select(DB::raw("SELECT * FROM documents WHERE name = '".$name."'"));
                if ($docu) {
                    $docu_name  = $docu[0]->name;
                    $docu_id    = $docu[0]->id;
                }
            }

            $attachment = new PartnerAttachment;
            $attachment->partner_id = $id;
            $attachment->name = $docu_name; //$request->txtUploadAttachment;
            $attachment->document_id = $docu_id; //$request->txtDocumentId;
            $attachment->document_image = $filenameToStore;
            $attachment->create_by = auth()->user()->username;
            $attachment->update_by = auth()->user()->username;
            $attachment->status = 'A';

            $attachment->save(); 

		});
    	return redirect('/merchants/details/'.$id.'/profile#atch')->with('success','Attachments Updated');
    }

    public function merchant_payment_gateway($id)
    {
        $gateway = PartnerPaymentGateway::find($id);
        return $gateway;
    }

	public function updateMerchantPaymentGateway($id,Request $request){
    	DB::transaction(function() use ($id,$request){
    		if($request->pgID == -1)
    		{
		    	$partnerContact = new PartnerPaymentGateway;
		    	$partnerContact->partner_id = $id;
		    	$partnerContact->name = $request->txtPGName;
		    	$partnerContact->key = $request->txtPGKey;
		    	$partnerContact->status = 'A';
	            $partnerContact->create_by = auth()->user()->username;
	            $partnerContact->update_by = auth()->user()->username;
		    	$partnerContact->save();   			

    		}else{
		    	$partnerContact = PartnerPaymentGateway::find($request->pgID);
		    	$partnerContact->name = $request->txtPGName;
		    	$partnerContact->key = $request->txtPGKey;
		    	$partnerContact->update_by = auth()->user()->username;
		    	$partnerContact->save();      			
    		}

		});
    	return redirect('/merchants/details/'.$id.'/profile#paygate')->with('success','Payment Gateway Updated');
    }
    public function addComment(Request $request){
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/merchants/details/'.$request->txtPartnerId.'/profile#dis')
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function() use ($request){
            $partner_id = $request->txtPartnerId;
            $sTempfile = "";
            $is_internal = 0;
            $is_public = 0;
            $partner_status = "";
            if (!empty($request->txtPartnerStatus)){$partner_status=$request->txtPartnerStatus;}

            if ($partner_status != "") {
                $updatePartner = Partner::find($partner_id);
                $updatePartner->partner_status = $partner_status;
                $updatePartner->update_by = auth()->user()->username;
                $updatePartner->save();
            }

            $leadComment = new LeadComment;
            $leadComment->partner_id = $request->txtPartnerId;
            $leadComment->comment = $request->comment;
            $leadComment->parent_id = $request->txtParentId;
            $leadComment->create_by = auth()->user()->username;
            $leadComment->user_id = auth()->user()->id;
            $leadComment->attachment = $sTempfile;
            $leadComment->is_internal = $is_internal;
            $leadComment->lead_status = $partner_status;
            $leadComment->save();
        });    
        return redirect('/merchants/details/'.$request->txtPartnerId.'/profile#dis')->with('success','Comment has been posted!');
    }
    public function addSubComment(Request $request){
        $this->validate($request,[
                'sub_comment' => 'required',
        ]);

        DB::transaction(function() use ($request){
            $comment_id = $request->txtParentId;
            $sTempFileName ="";
            $is_internal = 0;
            $is_public = 0;
            $partner_status = "";
            if (!empty($request->txtPartnerStatusSub)){$partner_status=$request->txtPartnerStatusSub;}

            if ($partner_status != "") {
                $updatePartner = Partner::find($request->txtPartnerId1);
                $updatePartner->partner_status = $partner_status;
                $updatePartner->update_by = auth()->user()->username;
                $updatePartner->save();
            }

            $leadComment = new LeadComment;
            $leadComment->partner_id = $request->txtPartnerId1;
            $leadComment->comment = $request->sub_comment;
            $leadComment->parent_id = $request->txtParentId;
            $leadComment->create_by = auth()->user()->username;
            $leadComment->user_id = auth()->user()->id;
            $leadComment->attachment = $sTempFileName;
            $leadComment->lead_status = $partner_status;
            $leadComment->save();
        });
        return redirect('/merchants/details/'.$request->txtPartnerId1.'/profile#dis')->with('success','Sub Comment has been posted!');
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

    public function create_order($id,Request $request)
    {
        $involvedStatuses = [
            MerchantStatus::BOARDING_ID,
            MerchantStatus::FOR_APPROVAL_ID,
        ];

        return DB::transaction(function() use ($id,$request, $involvedStatuses) {
            $partner = Partner::find($id);
            $partner->billing_status = 'Active';

            if ($partner->merchant_status_id == MerchantStatus::BOARDED_ID) {
                $partner->merchant_status_id = MerchantStatus::LIVE_ID;
            }

            $partner->save();

			$details = $request->txtOrderDetails;
            $details = json_decode($details);
            $arr_of_pid = array();
            $batchID = ProductOrder::getBatchID();
            $billing_id="";
            if (isset($request->billing_id)) $billing_id= $request->billing_id;
            foreach ($details as $d) {
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

            /** Send Order Form Email */
            $merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',array(3,9))->first();
            if($merchant->partner_company->email == "" || $merchant->partner_company->email == null ){
                //NO EMAIL DEFINED
            }else{
                

                if (!in_array($merchant->merchant_status_id, $involvedStatuses)) {
                    if (isset($merchant->partner_company->email)) {
                        $agent = Partner::get_partner_info($merchant->parent_id);   
                        $agent =  $agent[0];
                        if ($agent->partner_type_description == 'AGENT' || $agent->partner_type_description == 'SUB AGENT') {
                            $agent_contact = "This email was sent on behalf of {$agent->first_name} with contact information below. <br> 
                                <p>
                                    Agent Information <br>
                                    Name: {$agent->first_name} {$agent->last_name} <br>
                                    Email: {$agent->contact_email} <br>
                                    Mobile: + {$agent->company_country_code}{$agent->mobile_number} <br>
                                </p> 
                                
                                <br><br>";
                        } else {
                            $agent_contact = "This email was sent on behalf of {$agent->company_name} with contact information below.<br> 
                                <p>
                                    Company Information <br>
                                    Name: {$agent->company_name} <br>
                                    Email: {$agent->email} <br>
                                    Mobile: + {$agent->company_country_code}{$agent->phone1} <br>
                                </p>";
                        }

                        $link = "/merchants/{$order->id}/confirm_email";
                        $email_address = $merchant->partner_company->email;

                        $data = array(
                            'link' => $link,
                            'merchant' => $merchant,
                            'order' => $order,
                            'agent_contact' => $agent_contact,
                        );

                        Mail::send(['html'=>'mails.signature'], $data, function($message) use ($data, $merchant){
                            $message->to($merchant->partner_company->email ,$merchant->partner_contact()->first_name.' '.$merchant->partner_contact()->last_name);
                            $message->subject('[GoETU] Product Order'); 
                            $message->from('no-reply@goetu.com');
                        });

                        $order->status = 'PDF Sent';
                        $order->date_sent = date('Y-m-d H:i:s');
                        $order->save();
                    }
                }
                
                /** Send Welcome Email */
                $wemail = WelcomeEmailTemplate::where('product_id',$order->product_id)->where('status','A')->first();
                if(isset($wemail)){
                    $merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',array(3,9))->first();
                    $email_address = $merchant->partner_company->email;

                    Mail::send([], [], function ($message) use ($merchant,$wemail){
                        $message->to($merchant->partner_company->email ,$merchant->partner_contact()->first_name.' '.$merchant->partner_contact()->last_name);
                        $message->subject('[GoETU] Welcome to GoETU Applications');
                        $message->setBody($wemail->description, 'text/html');
                        $message->from('no-reply@goetu.com');
                    });
                }
            }

            if (in_array($merchant->merchant_status_id, $involvedStatuses)) {
                if($merchant->partner_type_id == 3){
                    return redirect("/merchants/details/{$id}/profile")
                        ->with('success', 'Order has been created!');                    
                }else{
                    return redirect("/merchants/branchDetails/{$id}/profile")
                        ->with('success', 'Order has been created!');                      
                }

            }

            if($merchant->partner_type_id == 3){
                return redirect('/merchants/details/'.$id.'/products#history')
                    ->with('success','Order has been created!');                   
            }else{
                return redirect("/merchants/branchDetails/{$id}/products#history")
                    ->with('success', 'Order has been created!');                      
            }

        });
    }


    public function update_order($id,Request $request){
        DB::transaction(function() use ($id,$request){
			$details = $request->txtOrderDetailsEdit;
			$details = json_decode($details);
			$order_id = $request->txtOrderId;
			$order = ProductOrder::find($order_id);
			$total_amt = 0;
			$total_qty = 0;
			foreach ($details as $detail) {
				$total_amt = $total_amt +$detail->amount;
				$total_qty = $total_qty +$detail->qty;
			}
			$order->quantity = $total_qty;
			$order->amount = $total_amt;
			$order->status = 'Pending';
            $order->update_by = auth()->user()->username;

			$order->save();

			$deletedRows = ProductOrderDetail::where('order_id', $order_id)->delete();
			foreach ($details as $detail) {
				$orderDetail = new ProductOrderDetail;
				$orderDetail->order_id = $order_id;
				$orderDetail->product_id = $detail->product_id;
				$orderDetail->amount = $detail->amount;
				$orderDetail->quantity = $detail->qty;
				$orderDetail->frequency = $detail->frequency;
                $orderDetail->start_date = $detail->startdate;
                $orderDetail->end_date = ($detail->enddate == "") ? '2999-01-01' : $detail->enddate;
                $orderDetail->price = $detail->price;
				$orderDetail->save();
			}
        });
        return redirect('/merchants/details/'.$id.'/products#history')->with('success','Order has been updated!');
    }

    public function getOrder($id){
    	$order = ProductOrder::find($id);
    	$order->productname = $order->product->name;

    	$select = "";
		$frequency = PaymentFrequency::where('status','A')->orderBy('sequence')->get();
		foreach ($frequency as $f) {
			$select .= '<option value="'.$f->name.'"">'.$f->name.'</option>';
		}

    	foreach($order->details as $detail)
    	{
    		$detail->productname = $detail->product->code.' - '.$detail->product->name;
            $detail->start_date = date("Y-m-d",strtotime($detail->start_date));
            $detail->end_date = date("Y-m-d",strtotime($detail->end_date));
    		$detail->select = $select;
            $detail->picture = $detail->product->display_picture == 'products/display_pictures/default.jpg' ? '' : url("storage/{$detail->product->display_picture}");
    	}
    	return $order;
    }
    public function advance_merchants_search($country, $state, $status){
        $partner_access=-1;
        $id = auth()->user()->reference_id; 
        
        $ex_state = explode(',',$state);
        $final_state="";
        foreach ($ex_state as $exs){
            $final_state.= "'".$exs."',";    
        }
        if (strpos($final_state, ',') !== false) {
            $final_state = substr($final_state,0,strlen($final_state)-1);;      
        }

        $search = " AND pc.state IN (".$final_state.") AND pc.country = '".$country."'";
        $search1 = " AND dp.business_state IN (".$final_state.") AND dp.business_country = '".$country."'";

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
        
        if ($partner_access==""){$partner_access=$id;}
        $pt_id="";
        $out_merchants=array();
        if (Access::hasPageAccess('merchant','view',true)){
            $result = $status == 'A' ? Partner::get_partners($partner_access,3,$id, -1, -1,"",$search) : Partner::get_partners($partner_access,3,$id, -1, -1,"",$search,"'{$status}'",false);

            /* $result1 = Partner::get_partners($partner_access,3,$id, -1, -1,"",$search,"'A','I','T','V','P','C'");
            $result2 = DraftPartner::get_draft_partners($partner_access,3,$id, -1, -1,"",$search1); // incomplete merchant application
			$results = array_merge($result1,$result2); */

            foreach ($result as $p) {
                $unverified="";
                $verify_mobile = Country::where('country_calling_code',$p->country_code)->first()->validate_number;
                if($verify_mobile==1)
                {
                    if ($p->is_verified_email==0 || $p->is_verified_mobile==0){
                        $unverified = ' <span class="badge badge-danger">unverified</span>';
                    }   
                }

                $upline = '';
                $uplineRec = Partner::find($p->parent_id);
                $upline .= $uplineRec->partner_company->company_name .' - <a href="/partners/details/profile/'.$p->parent_id.'/profileCompanyInfo">' . $uplineRec->partner_id_reference. '</a>';
                $view='';

                if($p->status == 'P'){
                    if (Access::hasPageAccess('merchant', 'board merchant',true)) {
                        $view .='<button class="btn btn-success btn-sm" onclick="boardMerchant('.$p->partner_id.')">Board</button>';
                    }
                    if (Access::hasPageAccess('merchant', 'decline merchant',true)) {
                        $view .= '&nbsp;&nbsp;';
                        $view .= '<button class="btn btn-warning btn-sm" onclick="declineMerchant('.$p->partner_id.')">Decline</button>';
                    }
                }
                if($p->status == 'C'){
                    if (Access::hasPageAccess('merchant', 'approve merchant',true)) {
                        $view .= '<button class="btn btn-success btn-sm" onclick="approveMerchant('.$p->partner_id.')">Approve</button>';
                    }
                    if (Access::hasPageAccess('merchant', 'decline merchant',true)) {
                        $view .= '&nbsp;&nbsp;';
                        $view .= '<button class="btn btn-warning btn-sm" onclick="declineMerchant('.$p->partner_id.')">Decline</button>';
                    }
                }
                if ($p->status == 'D') {
                    $linkOpening = "<a href='/drafts/draftMerchant/" . $p->partner_id . "/" . $p->partner_type_id . "/edit'>";
                    $linkClosing = "</a>";
                    // $view = '<button class="btn btn-danger btn-sm" onclick="deleteDraftApplicant(' . $p->partner_id . ')" title="Delete"><i class="fa fa-trash"></i></button>';
                    $view = '<button class="btn btn-danger btn-sm" onclick="deleteDraftApplicant(' . $p->partner_id . ')" title="Delete">Delete</button>';
                } else {
                    $linkOpening = "<a href='/merchants/details/{$p->partner_id}/profile'>";
                    $linkClosing = "</a>";
                }

                $incomplete = "";
                if($p->federal_tax_id == "" || $p->merchant_mid == "" || $p->credit_card_reference_id == ""
                    || $p->merchant_processor == "" || $p->company_name == "" || $p->dba == "" || $p->services_sold == ""
                    || $p->bank_name == "" || $p->bank_account_no == "" || $p->bank_routing_no == "" 
                    || $p->withdraw_bank_name == "" || $p->withdraw_bank_account_no == "" || $p->withdraw_bank_routing_no == ""
                    || $p->merchant_url == "" || $p->authorized_rep == "" || $p->IATA_no == "" || $p->tax_filing_name == ""){
                    $incomplete = ' <span title="Incomplete Merchant Info"><i class="fa fa-exclamation-triangle big-icon"></i></span> ';
                }

                
                $linkOpening = "<a href='/merchants/details/{$p->partner_id}/profile'>";
                $linkClosing = "</a>";

                $status = "";
                switch ($p->merchant_status_id) {
                    case MerchantStatus::BOARDED_ID:
                        $status = '<span style="color:green">Boarded</span>';
                        break;
                    
                    case MerchantStatus::LIVE_ID:
                        $status = '<span style="color:green">Live</span>';
                        break;

                    case MerchantStatus::CANCELLED_ID:
                        $status = '<span style="color:red">Cancelled</span>';
                        break;

                    case MerchantStatus::BOARDING_ID:
                        $status = '<span style="color:green">Boarding</span>';
                        break;
                    
                    case MerchantStatus::DECLINED_ID:
                        $status = '<span style="color:red">Declined</span>';
                        break;

                    case MerchantStatus::FOR_APPROVAL_ID:
                        $status = '<span style="color:green">For Approval</span>';
                        break;

                }

                if($p->merchant_status_id == MerchantStatus::LIVE_ID || $p->merchant_status_id == MerchantStatus::BOARDED_ID){
                    switch ($p->status) {
                        case 'A':
                            $status = '<span style="color:green">Live</span>';
                            break;
                        case 'V':
                            $status = '<span style="color:red">Cancelled</span>';
                            break;
                        case 'I':
                            $status = '<span style="color:red">Inactive</span>';
                            break;
                        case 'T':
                            $status = '<span style="color:red">Terminated</span>';
                            break;
                    }
                }
                
                /* if ($p->status == 'D') {
                    $status = '<span style="color:orange">Incomplete Merchant Application</span>';
                } */

                $PID = ''; 
                $order = ProductOrder::getPID($p->partner_id);
                foreach ($order as $o) {
                    $PID .= $o->PID . '<br>';
                }

                if ($canViewUpline) {
                    $out_merchants[] = array(
                        $p->partner_id_reference,
                        $upline,
                        $linkOpening . $incomplete .' '. $p->company_name . $linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $status,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $PID,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $p->country_code.$p->phone1,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                } else {
                    $out_merchants[] = array(
                        $p->partner_id_reference,
                        $linkOpening . $incomplete .' '. $p->company_name . $linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->country_code.$p->phone1).'</label>',
                        $status,
                        $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                        $PID,
                        $p->first_name.' '.$p->last_name.$unverified,
                        $p->country_code.$p->phone1,
                        $p->email,
                        $p->state,
                        $p->merchant_url,
                        $view
                    );    
                }
	        }
        }



        
        return response()->json($out_merchants); 
  //   	return $dt::of($result)
		// ->editColumn('contact', function($result){
		// 	$unverified="";
  //           if ($result->is_verified_email==0 || $result->is_verified_mobile==0){
  //               $unverified = $result->first_name.' '.$result->last_name .' <span class="label bg-gray">unverified</span>';
  //           }
  //           return $unverified;
		// })
  //       ->editColumn('phone1', function($result){
  //           return $result->country_code.$result->phone1;
  //       })
  //       ->editColumn('action', function ($result) {
  //           $message="'Delete this Merchant Template?'";
  //           $view='<a class="btn btn-default btn-sm" href="/merchants/details/'.$result->partner_id.'/profile">View</a>';
  //           return $view;
  //       })
  //       ->rawColumns(['contact','phone1','action'])
  //       ->make(true);
    }

    public function workflow($id,$order_id)
    {
        return redirect("/merchants/{$id}/product-orders/{$order_id}/workflow");

        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        if(strpos($access['merchant'], 'work flow') === false){
            return redirect('/merchants/details/'.$id.'/products#history')->with('failed','No access for this module');
        }

    	$merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();
    	$order = ProductOrder::find($order_id);
    	$noTemplate = false;

        $subtask = SubTaskHeader::with(['details.productOrderComments' => function($query) {
                $query->whereHas('viewers', function($query) {
                    $isSuperAdmin = Access::hasPageAccess('admin', 'super admin access', true);
                    $isOwner = Access::hasPageAccess('admin', 'owner', true);
            
                    if ( !($isSuperAdmin || $isOwner) ) {
                        $query->where('user_id', auth()->user()->id);
                    }
                });
            }])
            ->with('details.ticketHeaders')
            ->with('details.department')
            ->where('order_id',$order_id)
            ->first();

    	if ( !isset($subtask) ) {
            $template = SubTaskTemplateHeader::where('product_id', $order->product_id)
                            ->where('status', 'A')
                            ->first();

    		if (isset($template)) {
    			$this->createSubTask($id, $order_id, $template);
    			$subtask = SubTaskHeader::where('order_id', $order_id)->first();
    		} else {
    			$noTemplate = true;
    		}
    	}

    	if ($noTemplate) {
    		return redirect('/merchants/details/'.$id.'/products#history')->with('failed','There is no template defined for this product!');
    	}

    	$categories = Array();
    	foreach($order->details as $detail)
    	{
    		array_push($categories,$detail->product->category->name);
    	}
    	foreach($subtask->details as $detail)
    	{
    		$user = User::whereIn('id',json_decode($detail->assignee))->get();
    		$userList = "";
    		foreach($user as $u){
				$userList .= $u->first_name.' '.$u->last_name." (".$u->department->description.") / ";
    		}
            if (strlen(trim($userList)) > 0){
                $userList = substr($userList, 0, strlen($userList) - 2);    
            }
    		$detail->userList = $userList;
    		$detail->assignee = json_decode($detail->assignee);
    	}

    	$categories = array_unique($categories);
    	$headername = 'Order # '.$order_id. ' | ' .$order->product->name . ' | ' . $merchant->partner_company->company_name;
    	$formUrl = "";

    	$users = User::getUserPerProduct($order->product_id,auth()->user()->company_id);
        $status = OrderStatus::get();

        $comments = ProductOrderComment::where('product_order_id',$order_id)->where('parent_id',-1)->orderBy('created_at','desc')->get();
        foreach($comments as $comment)
        {
            $comment->sub_comments = ProductOrderComment::where('parent_id',$comment->id)->orderBy('created_at','asc')->get();
            $attachment_users = array();
            if($comment->attachment_access_ids!="") $attachment_users = json_decode($comment->attachment_access_ids);
            if(auth()->user()->username==$comment->create_by || in_array(auth()->user()->id, $attachment_users))
            {
                foreach($comment->sub_comments as $sub){
                     $sub->files = "";
                    foreach(json_decode($sub->attachment) as $files){
                        $sub->files .= '<br><a href="/storage/merchant_attachment/'.$files.'">'.$files.'</a>';
                    }
                }
            }
            $comment->files = "";
            $attachment_users = array();
            if($comment->attachment_access_ids!="") $attachment_users = json_decode($comment->attachment_access_ids);
            if(auth()->user()->username==$comment->create_by || in_array(auth()->user()->id, $attachment_users))
            {
                foreach(json_decode($comment->attachment) as $files){
                    $comment->files .= '<br><a href="/storage/merchant_attachment/'.$files.'">'.$files.'</a>';
                }
            }
        }

        $user = User::find(auth()->user()->id);
        $isAgent = $user->reference_id > 0 && $user->partner->partner_type_id == 1;
        
        $voidCount = SubTaskDetail::where('sub_task_id', $subtask->id)
            ->where('status', 'V')
            ->count();

        $completedCount = SubTaskDetail::where('sub_task_id', $subtask->id)
            ->where('status', 'C')
            ->count();

        $allCount = SubTaskDetail::where('sub_task_id', $subtask->id)->count();
        $allCompleted = $allCount - ($voidCount + $completedCount) == 0 ? true : false;

        $departments = UserType::isActive()
            ->isNonSystem()
            ->whereCompany( Product::find($order->product_id)->company_id )
            ->orderBy('description')
            ->get();

        $subTaskDetailGroups = $subtask->details->groupBy('department_id');

        return view("merchants.workflow.workFlow")->with(
            compact(
                'id',
                'merchant',
                'headername',
                'formUrl',
                'subtask',
                'subTaskDetailGroups',
                'order',
                'categories',
                'users',
                'status',
                'allCompleted',
                'departments',
                'isAgent'
            )
        );
    }

    public function comment(Request $request, $orderId, $subTaskDetailId)
    {
        $validation = Validator::make($request->all(), [
            'comment' => 'required'
        ]);

        if ( $validation->fails() ) {
            return response($validation->errors(), 400);
        }

        $order = ProductOrder::find($orderId);
        $productOrderComment = ProductOrderComment::create([
            'product_order_id' => $orderId,
            'parent_id' => isset($request->parent_id) ? $request->parent_id : null,
            'product_id' => $order->product_id,
            'partner_id' => $order->partner_id,
            'comment' => $request->comment,
            'create_by' => auth()->user()->username,
            'user_id' => auth()->user()->id,
            'status' => 'A',
            'comment_status' => $request->order_status,
            'sub_task_detail_id' => $subTaskDetailId
        ]);

        $order->product_status = $request->txtOrderStatus;
        $order->save();

        return response(null, 201);
    }

    public function unlinkTaskToTicket(Request $request)
    {
        $ticketHeader = TicketHeader::find($request->ticket_header_id);
        $ticketHeader->sub_task_detail_id = null;
        $ticketHeader->save();

        return response(null, 200);
    }

    public function update_subtask(Request $request)
    {
    	$userList = "";
        $id = $request->txtSubTaskID;
    	DB::transaction(function() use ($id,$request){
    		$detail = SubTaskDetail::where('sub_task_id',$id)->where('task_no',$request->txtTaskNo)->first();
    		$detail->name = $request->txtTaskName;
    		$detail->assignee = json_encode(explode(",",$request->txtTaskAssignee));
            $detail->due_date = date("Y-m-d",strtotime($request->txtDueOn));
            $detail->department_id = $request->department_id == -1 ? null : $request->department_id;
            $detail->update_by = auth()->user()->username;
            
            $productOrder = ProductOrder::find($detail->subTaskHeader->order_id);
            $this->workflowNotifyService->notifyOnAssign($productOrder, $detail);

    		$detail->save();

    		$max = SubTaskDetail::where('sub_task_id',$id)->orderBy('due_date', 'desc')->first();
    		$subtask = SubTaskHeader::find($id);
    		$subtask->due_date = $max->due_date;
    		$subtask->save();
    	});

    	$users = User::whereIn('id',explode(",",$request->txtTaskAssignee))->get();
    	foreach ($users as $u) {
    		$userList .= $u->first_name . ' ' . $u->last_name . ' ('.$u->department->description.') / ';
    	}
		if (strlen(trim($userList)) > 0){
            $userList = substr($userList, 0, strlen($userList) - 2);    
        }
        $max = SubTaskDetail::where('sub_task_id',$id)->orderBy('due_date', 'desc')->first();
        $due = date_format(new DateTime($max->due_date),"m/d/Y");
    	return Array('success' => true, 'user' => $userList, 'due' => $due);
    }

    public function add_subtask(Request $request)
    {
        $id = $request->txtSubTaskID;

    	$result = DB::transaction(function() use ($id, $request) {
            try {
                $maxline = SubTaskDetail::where('sub_task_id',$id)->orderBy('link_number', 'desc')->first();
                $maxtaskNo = SubTaskDetail::where('sub_task_id',$id)->orderBy('task_no', 'desc')->first();

                $detail = new SubTaskDetail;
                $detail->name = $request->txtTaskName;
                $detail->sub_task_id = $id;
                $detail->link_number = $maxline == null ? 1 : $maxline->link_number + 1;
                $detail->task_no = $maxtaskNo == null ? 1 : $maxtaskNo->task_no + 1;
                $detail->name = $request->txtTaskName;
                $detail->assignee = $request->txtTaskAssignee == null ? 
                    '[]' : 
                    json_encode(explode(",",$request->txtTaskAssignee));

                $detail->due_date = date("Y-m-d",strtotime($request->txtDueOn));
                $detail->department_id = $request->department_id == -1 ? 
                    null : 
                    $request->department_id;

                $detail->update_by = auth()->user()->username;
                $detail->save();

                $max = SubTaskDetail::where('sub_task_id',$id)->orderBy('due_date', 'desc')->first();
                $subTaskHeader = SubTaskHeader::find($id);
                $subTaskHeader->due_date = $max->due_date;
                $subTaskHeader->save();

                /** 
                 * Notify Point Person
                 * Notify Users with Edit Workflow Assignee Access 
                 **/

                $merchantId = $subTaskHeader->productOrder->partner_id;
                $orderId = $subTaskHeader->order_id;
                $companyId = Product::find($subTaskHeader->productOrder->product_id)
                    ->company_id;

                $userTypeIds = UserType::isNonSystem()
                    ->whereCompany( $companyId )
                    ->whereHas('resources', function($query) {
                        $query->where('resource_id', 283);
                    })
                    ->pluck('id')
                    ->toArray();

                $users = $this->userListService->getUsersWhereUserTypeIn($userTypeIds);
                
                $userEmails = [];
                $usernames = [];
                foreach ($users as $user) {
                    $usernames[] = $user->username;

                    if ($user->email_address && $user->workflow_email === true) {
                        $userEmails[] = $user->email_address;
                    }
                }

                if ($request->department_id != -1) {
                    $pointPersonUserType = UserType::with('departmentHead:id,username,email_address')
                        ->find($request->department_id);

                    if (isset($pointPersonUserType->departmentHead)) {
                        if (isset($pointPersonUserType->email_address)) {
                            $userEmails[] = $pointPersonUserType->departmentHead->email_address;
                        }

                        $usernames[] = $pointPersonUserType->departmentHead->username;
                    }
                }

                $message  = "New SubTask Added. Start assigning this to users.";
                $timestamp = date('Y-m-d H:i:s');
                $notificationData = [];
                $usernames = array_unique($usernames);
                
                foreach ($usernames as $username) {
                    $notificationData[] = [
                        'partner_id' => -1,
                        'source_id' => -1,
                        'subject' => 'New Subtask Added',
                        'message' => $message,
                        'status' => 'N',
                        'create_by' => auth()->user()->username,
                        'update_by' => auth()->user()->username,
                        'redirect_url' => "/merchants/workflow/{$merchantId}/{$orderId}",
                        'recipient' => $username,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ];
                }
            
                Notification::insert($notificationData);


                $data = [
                    'email' => (object) [
                        'subject' => 'New Subtask Added', 
                        'body'=> 
                            "<a href='" . url('/') . "/merchants/workflow/{$merchantId}/{$orderId}'>" .
                                "New Subtask Added. <br/>" .
                                "Start assigning this to users." .
                            "</a>"
                    ]
                ];

                $emailBody = view("mails.basic4", $data)->render();
                $userEmails = implode(',', array_unique($userEmails));
                if ($userEmails != '') {
                    $emailOnQueue = new EmailOnQueue;
                    $emailOnQueue->subject = 'New Subtask Added';
                    $emailOnQueue->body =  $emailBody;
                    $emailOnQueue->email_address = $userEmails;
                    $emailOnQueue->create_by = auth()->user()->username;
                    $emailOnQueue->is_sent = 0;
                    $emailOnQueue->sent_date = null;
                    $emailOnQueue->save();
                }
                
                return Array('success' => true);
            
            } catch (\Exception $e) {
                return Array('success' => false, 'msg' => $e->getMessage());
            }
        });
        
    	return $result;
    }

    public function add_subtask_comment(Request $request)
    {
        $validationRules = [
            'txtComment' => 'required',
            'commentFile.*' => 'nullable|file|max:5000',
            'commentFileSub.*' => 'nullable|file|max:5120'
        ];

        $validationCustomMessages = [
            'commentFile.*' => 'File may not be greater than 5mb',
            'commentFileSub.*.max' => 'File may not be greater than 5mb',
        ];

        $request->validate($validationRules, $validationCustomMessages);

        $result = DB::transaction(function() use ($request){
            try{
                $order = ProductOrder::find($request->txtCommentOrderID);
                if(!isset($order))
                {
                    return redirect('/merchants')->with('failed','Order not Found!');
                }
                $files = Array();
                if($request->txtCommentParentId == -1)
                {
                    $fileStr = 'commentFile';
                }else{
                    $fileStr = 'commentFileSub';
                }

                if ($request->file($fileStr)!== null){
                    foreach($request->file($fileStr) as $file){
                        $thefile = File::get($file);
                        $fileNameWithExt = $file->getClientOriginalName();
                        $fileName = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();
                        $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
                        Storage::disk('merchant_attachment')->put($filenameToStore,$thefile);
                        array_push($files,$filenameToStore);
                    }
                }

                $detail = new ProductOrderComment;
                $detail->product_order_id = $order->id;
                $detail->parent_id = $request->txtCommentParentId;
                $detail->product_id = $order->product_id;
                $detail->partner_id = $order->partner_id;
                $detail->comment = $request->txtComment;
                $detail->create_by = auth()->user()->username;
                $detail->user_id = auth()->user()->id;
                $detail->status = 'A';
                $detail->comment_status = $request->txtOrderStatus;
                $detail->attachment = json_encode($files);

                if (isset($request->txtVisibilityAssignee)){
                    $detail->attachment_access_ids = json_encode($request->txtVisibilityAssignee);        
                }

                $detail->save();

                $order->product_status = $request->txtOrderStatus;
                $order->save();

                
                return redirect('/merchants/workflow/'.$order->partner_id.'/'.$order->id)->with('success','Comment has been Added!');
            } catch (\Exception $e) {
                return redirect('/merchants')->with('failed',$e->getMessage());
            }
        });
        return $result;
    }


    public function update_subtask_status(Request $request)
    {
        $id = $request->txtSubTaskID;   
        $result =  DB::transaction(function() use ($id,$request){
            try {
                $detail = SubTaskDetail::where('sub_task_id',$id)->where('task_no',$request->txtTaskNo)->first();
                $detail->status = $request->txtSubTaskStatus;
                $detail->update_by = auth()->user()->username;
                $detail->save();

                if ($request->txtSubTaskStatus == 'C') {
                    $productOrder = ProductOrder::find($detail->subTaskHeader->order_id);
                    $subTaskDetails = SubTaskDetail::where('sub_task_id', $detail->sub_task_id)
                        ->where('prerequisite', $detail->task_no)
                        ->get();

                    foreach ($subTaskDetails as $subTaskDetail) {
                        $this->workflowNotifyService->notifyOnCompletion(
                            $productOrder, $detail, $subTaskDetail);
                    }
                }

                return Array('success' => true);
            } catch (\Exception $e) {
                return Array('success' => false, 'msg' => $e->getMessage());
            }
        });

        return $result;
    }

    public function markAllTaskAsCompleted(Request $request, $partnerId, $orderId)
    {
        SubTaskDetail::where('sub_task_id', $request->sub_task_id)
            ->where('status', '<>', 'V')
            ->where('assignee', '<>', '[]')
            ->update([
                'status' => 'C',
                'update_by' => auth()->user()->username
            ]);

        return redirect("/merchants/workflow/{$partnerId}/{$orderId}");
    }



    private function createSubTask($id,$order_id,$template){
		DB::transaction(function() use ($id,$order_id,$template){
			$subtask = new SubTaskHeader;
			$subtask->order_id = $order_id;
			$subtask->name = $template->name;
			$subtask->description = $template->description;
			$subtask->remarks = $template->remarks;
			$subtask->days_to_complete = $template->days_to_complete;
			$subtask->status = 'A';
			$subtask->create_by = auth()->user()->username;
			$subtask->update_by = auth()->user()->username;
			$subtask->save();
			$ctr = 1;
			$userList = Array();
            $details = Array();
			$templateDetail = SubTaskTemplateDetail::where('sub_task_id',$template->id)->get();
			foreach ($templateDetail as $detail) {
				$product_tags = json_decode($detail->product_tags);
				$po = ProductOrderDetail::whereIn('product_id',$product_tags)->where('order_id',$order_id)->get();
				if($po->count() > 0){
                    $userList =  array_merge($userList , json_decode($detail->assignee));

                    $assignees = [];
                    if ($detail->department_id != null) {
                        if (isset($detail->department->departmentHead)) {
                            $assignees[] = $detail->department->departmentHead->id;
                        }
                    }

					$details[] = array(
	                    'sub_task_id' => $subtask->id,
	                    'line_number' => $ctr,
	                    'task_no' => $detail->line_number,
	                    'name' => $detail->name,
                        'assignee' => json_encode($assignees),
                        'department_id' => $detail->department_id,
	                    'prerequisite' => $detail->prerequisite,
	                    'product_tags' => '',
	                    'days_to_complete' => $detail->days_to_complete,
	                    'link_condition' => $detail->link_condition,
	                    'start_date' => date('Y-m-d H:i:s'),
	                    'due_date' => date('Y-m-d H:i:s'),
	                );
						$ctr++;
				}
			}
	        $due_date = date("m/d/Y");
	        $due_date2 = date("m/d/Y");
			foreach ($details as $detail)
			{
	        	if ($detail['prerequisite'] == "" || $detail['prerequisite'] == 0){
	        		$due_date = $this->addDayswithdate($due_date,$detail['days_to_complete']);
	        		$detail['due_date'] = $due_date;
	        	}else{
	        		$linked = false;
	        		foreach ($details as $s)
					{
	        			if($detail['prerequisite'] == $s['task_no'])
	        			{
	        				if ($detail['link_condition'] == 'Start')
	        				{
	        					$due_date2 = $this->addDayswithdate($s['due_date'],$detail['days_to_complete'] - $s['days_to_complete'] );
	        					$detail['start_date'] = $s['start_date'];
	        				}

	        				if ($detail['link_condition'] == 'Completion' || $detail['link_condition'] == 'Due Date')
	        				{
								$due_date2 = $this->addDayswithdate($s['due_date'],$detail['days_to_complete']);
								$detail['start_date'] = $s['due_date'];
	        				}
	        				
	        				$detail['due_date'] = $due_date2;
	        				if ($due_date2 > $due_date)
	        				{
	        					$due_date = $due_date2;
	        				}
	        				$linked = true;
	        			}
					}
	        		if (!$linked)
	        		{
	        			$due_date = $this->addDayswithdate($due_date,$detail['days_to_complete']);
	        			$detail['due_date'] = $due_date;
	        			$detail['prerequisite'] = 0;
	        		}
	        	}
			}

			foreach ($details as $s)
			{
				$subtaskDetail = new SubTaskDetail;
				$subtaskDetail->sub_task_id = $s['sub_task_id'];
				$subtaskDetail->link_number = $s['line_number'];
				$subtaskDetail->task_no = $s['task_no'];
				$subtaskDetail->name = $s['name'];
				$subtaskDetail->department_id = $s['department_id'];
				$subtaskDetail->assignee = $s['assignee'];
				$subtaskDetail->prerequisite = $s['prerequisite'];
				$subtaskDetail->product_tags ='';
				$subtaskDetail->days_to_complete =$s['days_to_complete'];
				$subtaskDetail->link_condition =$s['link_condition'];
				$subtaskDetail->due_date = date("Y-m-d",strtotime($s['due_date']));
				$subtaskDetail->status = '';
				$subtaskDetail->update_by = auth()->user()->username;
                $subtaskDetail->save();
		    }

		    $subtask->user_list = json_encode(array_unique($userList));
		    $max = SubTaskDetail::where('sub_task_id', $subtask->id)->max('due_date');
		    $subtask->due_date =  $max;
            $subtask->save();
            

            $productOrder = ProductOrder::with('subTaskHeader.subTaskDetails')
                ->find($order_id);

            $users = $this->userListService->getUsersWithWorkflowAssignAccess(
                $productOrder->product->company_id);

            $this->workflowNotifyService->notifyOnCreate($productOrder, $users);
		});
    }

	private function addDayswithdate($date,$days){
	    $date = strtotime("+".$days." days", strtotime($date));
	    return  date("m/d/Y", $date);
	}

	public function orderPreview($id){
		$html = $this->createPDFHtml($id);
		return PDF::loadHTML($html)->setPaper('a4', 'portrait')->setWarnings(false)->save(public_path().'/pdf/order_preview_'.$id.'.pdf')->stream('order_preview_'.$id.'.pdf');
	}

	public function orderSign($id){
		$order = ProductOrder::find($id);
		if(!isset($order)){
    		return redirect('/merchants')->with('failed','Cannot find order!');
    	}
    	if($order->status == "Application Signed"){
    		return redirect('/merchants')->with('failed','Application already signed!');
    	}

		$merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',array(3,9))->first();
		$headername = 'Order # '.$id. ' | ' .$order->product->name . ' | ' . $merchant->partner_company->company_name;
		$pdfUrl = '/merchants/'.$id.'/order_preview';
		return view("merchants.sign.appsign",compact('merchant','headername','pdfUrl'));
	}

	public function orderSigned($id,Request $request){
        $result = DB::transaction(function() use ($id,$request){
            try{
    			$order = ProductOrder::find($id);
    			$order->signature = $request->txtImage;
    			$order->status = 'Application Signed';
                $order->date_signed = date('Y-m-d H:i:s');
                $order->save();

                $invoice = new InvoiceHeader;
                $invoice->order_id =  $order->id;
                $invoice->partner_id =  $order->partner_id;
                $invoice->invoice_date =  date('Y-m-d'); 
                $invoice->due_date =  date('y:m:d', strtotime("+10 days"));
                $invoice->total_due =  $order->amount;
                $invoice->reference =  $order->product->name;
                $invoice->create_by =  'System';
                $invoice->status =  'U';
                $invoice->remarks =  'Created from Order #'.$order->id;
                $invoice->save();

                // $paymentInfo = PartnerPaymentInfo::where('partner_id',$order->partner_id)->where('is_default_payment',1)->first();
                $paymentInfo = PaymentType::where('name',$order->preferred_payment)->first();
                $paymentId = isset($paymentInfo) ? $paymentInfo->id : 2;

                $invoicePayment = new InvoicePayment;
                $invoicePayment->invoice_id = $invoice->id;
                $invoicePayment->payment_type_id =  $paymentId;
                $invoicePayment->save();
                $lineCtr = 0;
                $hasCC = false;
                foreach($order->details as $detail){
                    $lineCtr++;

                    $invoiceFrequency = new InvoiceFrequency;
                    $invoiceFrequency->order_id = $order->id;
                    $invoiceFrequency->partner_id = $order->partner_id;
                    $invoiceFrequency->product_id = $detail->product_id;
                    $invoiceFrequency->frequency = $detail->frequency;
                    $invoiceFrequency->register_date = date('Y-m-d'); 
                    $invoiceFrequency->bill_date = $detail->start_date;
                    $invoiceFrequency->start_date = $detail->start_date;
                    $invoiceFrequency->end_date =  $detail->end_date;
                    $invoiceFrequency->due_date =  date('y:m:d', strtotime( $detail->start_date. "+10 days"));
                    $invoiceFrequency->amount =  $detail->amount;
                    $invoiceFrequency->status =  'Active';
                    $invoiceFrequency->save();

                    $invoiceDetail = new InvoiceDetail;
                    $invoiceDetail->invoice_id = $invoice->id;
                    $invoiceDetail->order_id = $order->id; 
                    $invoiceDetail->line_number = $lineCtr;
                    $invoiceDetail->product_id = $detail->product_id;
                    $invoiceDetail->description = $order->product->name;
                    $invoiceDetail->amount =  $detail->amount;
                    $invoiceDetail->quantity =  $detail->quantity;
                    $invoiceDetail->invoice_frequency_id =  $invoiceFrequency->id;
                    // $invoiceDetail->cost = $detail->price;
                    //get actual cost
                    $partner = Partner::find($order->partner_id);
                    if($partner->partner_type_id == 3){
                        $productCost = PartnerProduct::where('partner_id',$partner->parent_id)->where('product_id',$detail->product_id)->first();
                    }else{
                        $partner = Partner::find($partner->parent_id);
                        $productCost = PartnerProduct::where('partner_id',$partner->parent_id)->where('product_id',$detail->product_id)->first();
                    }
                    
                    $invoiceDetail->cost = $productCost->buy_rate;

                    $invoiceDetail->save();

                    $pCheck = Product::find($detail->product_id);
                    if ($pCheck->name == "CardPointe"){
                        $hasCC = true;
                    }
                }

                $partnerType = Partner::find($order->partner_id);

                if($hasCC){
                    $msg = $this->coPilotMerchantSave($order->partner_id,'create');
                    if( $msg != 'success'){
                        DB::rollback();
                        if($partnerType->partner_type_id == 3){
                           return redirect('/merchants/details/'.$order->partner_id.'/products#history')->with('failed',$msg );; 
                        }else{
                           return redirect('/merchants/branchDetails/'.$order->partner_id.'/products#history')->with('failed',$msg );
                        }

                    }
                }

                $accountingDept = UserType::where('description', 'Accounting')
                    ->where('company_id', $order->partner->company_id)
                    ->first();

                if (isset($accountingDept) && $accountingDept->users->count() > 0) {
                    $users = $accountingDept->users;

                    $emailAddresses = [];
                    $usernames = [];
                    foreach ($users as $user) {
                        $emailAddresses[] = $user->email_address;
                        $usernames[] = $user->username;
                    }

                    if (! empty($emailAddresses)) {
                        $emailData = [
                            'action' => 'signed',
                            'productOrder' => $order,
                            'user' => User::find(auth()->id()),
                        ];

                        $emailBody = view("mails.productOrder", $emailData)->render();
                        $emailAddresses = implode(',', $emailAddresses);
                        $emailOnQueue = new EmailOnQueue;
                        $emailOnQueue->subject = "{$order->status} - {$order->partner->partnerCompany->company_name} - Product Order No. {$order->id}";
                        $emailOnQueue->body =  $emailBody;
                        $emailOnQueue->email_address = $emailAddresses;
                        $emailOnQueue->create_by = auth()->user()->username;
                        $emailOnQueue->is_sent = 0;
                        $emailOnQueue->sent_date = null;
                        $emailOnQueue->save();
                    }

                    $u = auth()->user()->full_name;
                    $timestamp = date('Y-m-d H:i:s');
                    $data = array();
                    foreach ($usernames as $username) {
                        $data[] = [
                            'partner_id' => -1,
                            'source_id' => -1,
                            'subject' => "{$order->status} - {$order->partner->partnerCompany->company_name} - Product Order No. {$order->id}",
                            'message' => "Product Order {$order->id} has been signed",
                            'status' => 'N',
                            'create_by' => auth()->user()->username,
                            'update_by' => auth()->user()->username,
                            'redirect_url' => "/merchants/details/{$order->partner_id}/products#history",
                            'recipient' => $username,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp
                        ];
                    }

                    Notification::insert($data);
                }

                if($partnerType->partner_type_id == 3){
                   return redirect('/merchants/details/'.$order->partner_id.'/products#history')->with('success','Order has been signed!'); 
                }else{
                   return redirect('/merchants/branchDetails/'.$order->partner_id.'/products#history')->with('success','Order has been signed!');
                }
                
            } catch (\Exception $e) {
                return redirect('/merchants')->with('failed',$e->getMessage());
            }
        });
        return $result;
	}

    public function processOrder($id){
        $result = DB::transaction(function() use ($id){
            try{
                $order = ProductOrder::find($id);
                $order->signature = '';
                $order->status = 'Application Signed';
                $order->date_signed = date('Y-m-d H:i:s');
                $order->save();

                $invoice = new InvoiceHeader;
                $invoice->order_id =  $order->id;
                $invoice->partner_id =  $order->partner_id;
                $invoice->invoice_date =  date('Y-m-d'); 
                $invoice->due_date =  date('y:m:d', strtotime("+10 days"));
                $invoice->total_due =  $order->amount;
                $invoice->reference =  $order->product->name;
                $invoice->create_by =  'System';
                $invoice->status =  'U';
                $invoice->remarks =  'Created from Order #'.$order->id;
                $invoice->save();

                // $paymentInfo = PartnerPaymentInfo::where('partner_id',$order->partner_id)->where('is_default_payment',1)->first();

                $paymentInfo = PaymentType::where('name',$order->preferred_payment)->first();
                $paymentId = isset($paymentInfo) ? $paymentInfo->id : 2;
                

                $invoicePayment = new InvoicePayment;
                $invoicePayment->invoice_id = $invoice->id;
                $invoicePayment->payment_type_id =  $paymentId;
                $invoicePayment->save();
                $lineCtr = 0;
                $hasCC = false;
                foreach($order->details as $detail){
                    $lineCtr++;

                    $invoiceFrequency = new InvoiceFrequency;
                    $invoiceFrequency->order_id = $order->id;
                    $invoiceFrequency->partner_id = $order->partner_id;
                    $invoiceFrequency->product_id = $detail->product_id;
                    $invoiceFrequency->frequency = $detail->frequency;
                    $invoiceFrequency->register_date = date('Y-m-d'); 
                    $invoiceFrequency->bill_date = $detail->start_date;
                    $invoiceFrequency->start_date = $detail->start_date;
                    $invoiceFrequency->end_date =  $detail->end_date;
                    $invoiceFrequency->due_date =  date('y:m:d', strtotime( $detail->start_date. "+10 days"));
                    $invoiceFrequency->amount =  $detail->amount;
                    $invoiceFrequency->status =  'Active';
                    $invoiceFrequency->save();

                    $invoiceDetail = new InvoiceDetail;
                    $invoiceDetail->invoice_id = $invoice->id;
                    $invoiceDetail->order_id = $order->id; 
                    $invoiceDetail->line_number = $lineCtr;
                    $invoiceDetail->product_id = $detail->product_id;
                    $invoiceDetail->description = $order->product->name;
                    $invoiceDetail->amount =  $detail->amount;
                    $invoiceDetail->quantity =  $detail->quantity;
                    $invoiceDetail->invoice_frequency_id =  $invoiceFrequency->id;
                    // $invoiceDetail->cost = $detail->price;
                    //get actual cost
                    $partner = Partner::find($order->partner_id);
                    if($partner->partner_type_id == 3){
                        $productCost = PartnerProduct::where('partner_id',$partner->parent_id)->where('product_id',$detail->product_id)->first();
                    }else{
                        $partner = Partner::find($partner->parent_id);
                        $productCost = PartnerProduct::where('partner_id',$partner->parent_id)->where('product_id',$detail->product_id)->first();
                    }

                    $invoiceDetail->cost = $productCost->buy_rate ?? 0;
                    $invoiceDetail->save();

                    $pCheck = Product::find($detail->product_id);
                    if ($pCheck->name == "CardPointe"){
                        $hasCC = true;
                    }
                }

                $partnerType = Partner::find($order->partner_id);

                if($hasCC){
                    $msg = $this->coPilotMerchantSave($order->partner_id,'create');
                    if( $msg != 'success'){
                        DB::rollback();
                        if($partnerType->partner_type_id == 3){
                           // return redirect('/merchants/details/'.$order->partner_id.'/products#history')->with('failed',$msg );
                           return Array('message' => 'failed','redirect' => '/merchants/details/'.$order->partner_id.'/products#history');
                        }else{
                           return Array('message' => 'failed','redirect' => '/merchants/branchDetails/'.$order->partner_id.'/products#history');
                        }

                    }
                }

                $accountingDept = UserType::where('description', 'Accounting')
                    ->where('company_id', $order->partner->company_id)
                    ->first();

                if (isset($accountingDept) && $accountingDept->users->count() > 0) {
                    $users = $accountingDept->users;

                    $emailAddresses = [];
                    $usernames = [];
                    foreach ($users as $user) {
                        $emailAddresses[] = $user->email_address;
                        $usernames[] = $user->username;
                    }

                    if (! empty($emailAddresses)) {
                        $emailData = [
                            'action' => 'processed',
                            'productOrder' => $order,
                            'user' => User::find(auth()->id()),
                        ];

                        $emailBody = view("mails.productOrder", $emailData)->render();
                        $emailAddresses = implode(',', $emailAddresses);
                        $emailOnQueue = new EmailOnQueue;
                        $emailOnQueue->subject = "{$order->status} - {$order->partner->partnerCompany->company_name} - Product Order No. {$order->id}";
                        $emailOnQueue->body =  $emailBody;
                        $emailOnQueue->email_address = $emailAddresses;
                        $emailOnQueue->create_by = auth()->user()->username;
                        $emailOnQueue->is_sent = 0;
                        $emailOnQueue->sent_date = null;
                        $emailOnQueue->save();
                    }

                    $u = auth()->user()->full_name;
                    $timestamp = date('Y-m-d H:i:s');
                    $data = array();
                    foreach ($usernames as $username) {
                        $data[] = [
                            'partner_id' => -1,
                            'source_id' => -1,
                            'subject' => "{$order->status} - {$order->partner->partnerCompany->company_name} - Product Order No. {$order->id}",
                            'message' => "Product Order {$order->id} has been processed",
                            'status' => 'N',
                            'create_by' => auth()->user()->username,
                            'update_by' => auth()->user()->username,
                            'redirect_url' => "/merchants/details/{$order->partner_id}/products#history",
                            'recipient' => $username,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp
                        ];
                    }

                    Notification::insert($data);
                }

                if($partnerType->partner_type_id == 3){
                   // return redirect('/merchants/details/'.$order->partner_id.'/products#history')->with('success','Order has been signed!'); 
                   return Array('message' => 'success','redirect' => '/merchants/details/'.$order->partner_id.'/products#history');
                }else{
                   return Array('message' => 'success','redirect' => '/merchants/branchDetails/'.$order->partner_id.'/products#history');
                }

            


            } catch (\Exception $e) {
                return Array('message' => $e->getMessage());
            }
        });
        return $result;
    }

    public function coPilotMerchantSave($id,$action){
        CoPilot::createAccessToken();
        $merchant = new Merchant;
        if(!($action == 'create' || $action == 'update')){
            return 'Invalid action';
        }
        if($action == 'create')
        {
            $merchantData = Partner::where('id',$id)->whereIn('partner_type_id',array(3,9))->where('copilot_merchant_id',0)->first();      
        }else{
            $merchantData = Partner::where('id',$id)->whereIn('partner_type_id',array(3,9))->first();
        }
        if(!isset($merchantData))
        {   
            return 'success';
        }
        
        $merchant->setakaBusinessName($merchantData->partner_company->company_name);
        $merchant->setCustPrimaryAcctFlg(false);
        $merchant->setDbaName($merchantData->partner_company->dba);
        $merchant->setLegalBusinessName($merchantData->partner_company->company_name);
        $merchant->settaxFilingMethod($merchantData->partner_company->ownership == 'INDIVSOLE' ? 'SSN' : 'EIN');
        $merchant->settaxFilingName($merchantData->tax_filing_name);
        // $merchant->settaxFilingName('TESTTT');

        $demographic = new Demographic();
        $demographic->setMerchantTimeZone('ET');
        $demographic->setwebsiteAddress($merchantData->merchant_url);

        $businessAddress = new Address();
        $businessAddress->setAddress1($merchantData->partner_company->address1);
        $businessAddress->setCity($merchantData->partner_company->city);
        $businessAddress->setZip($merchantData->partner_company->zip);

        $demographic->setBusinessAddress($businessAddress);

        $mailingAddress = new Address();
        $mailingAddress->setAddress1($merchantData->partner_billing->address);
        $mailingAddress->setCity($merchantData->partner_billing->city);
        $mailingAddress->setZip($merchantData->partner_billing->zip);

        $demographic->setMailingAddress($mailingAddress);

        $merchant->setDemographic($demographic);

        $bankDetail = new BankDetail();
        $depositBank = new Bank();
        $depositBank->setBankAcctNum($merchantData->bank_account_no);
        $depositBank->setBankRoutingNum($merchantData->bank_routing_no);
        $depositBank->setBankAcctTypeCd($merchantData->bank_account_type_code);
        $depositBank->setBankName($merchantData->bank_name);

        $withdrawalBank = new Bank();
        $withdrawalBank->setBankAcctNum($merchantData->withdraw_bank_account_no);
        $withdrawalBank->setBankRoutingNum($merchantData->withdraw_bank_routing_no);
        $withdrawalBank->setBankAcctTypeCd($merchantData->withdraw_bank_account_type_code);
        $withdrawalBank->setBankName($merchantData->withdraw_bank_name);

        $bankDetail->setDepositBank($depositBank);
        $bankDetail->setWithdrawalBank($withdrawalBank);
        $merchant->setBankDetail($bankDetail);


        $ownership = new Own();
        $owner = new Owner();
        $owner->setOwnerAddress(
            (new Address())
                ->setAddress1($merchantData->partner_contact()->address1)
                ->setCity($merchantData->partner_contact()->city)
                ->setZip($merchantData->partner_contact()->zip)
        );
        $owner->setOwnerEmail($merchantData->partner_company->email);
        $owner->setOwnerName($merchantData->partner_contact()->first_name . ' ' . $merchantData->partner_contact()->last_name);
        $owner->setOwnerPhone(ltrim($merchantData->partner_contact()->other_number,'-'));
        $owner->setOwnerMobilePhone(ltrim($merchantData->partner_contact()->mobile_number,'-'));
        $owner->setOwnerSSN($merchantData->partner_contact()->ssn);
        $owner->setOwnerTitle("OWNER");

        $ownership->setOwner($owner);
        $ownership->setOwnershipTypeCd($merchantData->partner_company->ownership);
        $ownership->setDriversLicenseNumber($merchantData->partner_contact()->issued_id);
        $ownership->setDriversLicenseStateCd($merchantData->partner_company->state);

        $merchant->setOwnership($ownership);
        $merchant->setSalesCode(env('COPILOT_SALES_CODE'));
        $merchant->setWebLeadFlg(false);

        $pricing = new Pricing;
        $flatPricing = new FlatPricing;
        $flatPricing->setAmexEsaQualDiscountPct(0);
        $flatPricing->setAmexOptBlueQualDiscountPct(0);
        $flatPricing->setDiscoverQualCreditDiscountPct(0);
        $flatPricing->setMastercardQualCreditDiscountPct(0);
        $flatPricing->setVisaQualCreditDiscountPct(0);
        $object = json_decode(json_encode($flatPricing->toArray()), FALSE);
        $pricing->setFlatPricing($object);

        $merchant->setPricing($pricing);

        $fee = new Fee;
        $fee->setAchBatchFee(0);
        $fee->setAddressVerifFee(0);
        $fee->setAnnualMembershipFee(0);
        $fee->setAppFee(0);
        $fee->setAuthFee(0);
        $fee->setChargebackFee(0);
        $fee->setDataBreachFee(0);
        $fee->setDdaRejectFee(0);
        $fee->setEarlyCancelFee(0);
        $fee->setMinProcessFee(0);
        $fee->setMonthlyEquipmentRentalFee(0);
        $fee->setPciAnnualFee(0);
        $fee->setPciNonComplianceFee(0);
        $fee->setRegProdMonthlyFee(0);
        $fee->setRegProdMonthlyFee(0);
        $fee->setRetrievalFee(0);
        $fee->setStatementFee(0);
        $fee->setTransactionFee(0);
        $fee->setVoiceAuthFee(0);
        $fee->setWirelessActivationFee(0);
        $fee->setWirelessFee(0);
        $fee->setDuesAndAssessmentsFlg(true);
        $fee->setPassthruInterchgCostsFlg(true);

        $merchant->setFee($fee);

        $templateID =  1050;

        try{
            if($action == 'create')
            {
                $copilot =  CoPilot::createMerchant($templateID, $merchant, null);
                $merchantData->copilot_merchant_id = $copilot['merchantId'];
                $merchantData->save();
            }else{
                $copilot =  CoPilot::updateMerchant($merchantData->copilot_merchant_id, $merchant, null);
            }
            return 'success';
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }

	private function createPDFHtml($id){
		$order = ProductOrder::find($id);
        $user = User::where('username',$order->create_by)->first();
		if(!isset($order)){
    		return redirect('/merchants')->with('failed','Cannot find order!');
    	}
		$merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',Array(3,9))->first();
		$detailHtml= "";
		foreach($order->details as $detail){
			$detailHtml .= '<tr>
								<td>'.$detail->product->name.'</td>
								<td>'.$detail->frequency.'</td>
								<td style="text-align: right;">'.$detail->quantity.'</td>
								<td style="text-align: right;">$ '.$detail->amount.'</td>
							</tr>';
		}
		
		$sig = "";
		$sigDate="";
        $sendDate=isset($order->date_sent) ? $order->date_sent->format('m/d/Y') : "";

		if($order->status == "Application Signed"){
			$data = explode(',', $order->signature);
			$sig = '<img class="imported" src="' . $order->signature . '" height="50" width="200"></img>';
			$sigDate = $order->date_signed->format('m/d/Y');
		}

		$html = '<!Doctype>
					<html>
						<head>
							<meta charset="utf-8" />
							<meta name="viewport" content="width=device-width, initial-scale=1" />
							
							<title>
								GoETU Order Preview
							</title>
							<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
							<style type="text/css">
								.float-right{ float: right; }
								.text-right{ text-align: right; }
								.text-center{ text-align: center; }
								table {
									width: 100%;
								}
								table td{
								    border: 1px solid #000;
								}
								table .no-bordered{
									border: 0px;
								}
								.row-title{
								    background: #000;
								    color: #fff;
								    font-weight: bold;
								    text-align: center;
								    text-transform: uppercase;
								}
								.sub{
									color: #000;
									background: #c7c7c7;
								}
								.no-border td{
									border-color: #fff;
								}
							</style>
							</head>
							<body>
								<table class="table">
									<tr class="no-border">
										<td><img src="images/goetu.jpg" alt="Go3Sutdio"  height="50" width="200"/></td>
										<td colspan="3">
											<p class="text-right">
												<strong>Merchant Application Setup Form & Agreement</strong><br/>
												50 Broad St., Suite 1701, New York, NY 10004<br/>
												T:(888)377-381 &nbsp;&nbsp; F: (888)406-0777 &nbsp;&nbsp; E:support@go3solutions.com
											</p>
										</td>
									</tr>
								</table>
								<table class="table">
									<tr class="row-title"><td colspan="4">Product Name</td></tr>
									<tr class="text-center"><td colspan="4"><h4><strong>'.$order->product->name.'</strong></h4></td></tr>
									<tr><td colspan="4">&nbsp;</td></tr>

									<tr class="row-title">
										<td>Fees</td>
										<td>Frequency</td>
										<td>Quantity</td>
										<td>Amount</td>
									</tr>'.$detailHtml.'

									<tr>
										<td colspan="3">
											<span class="float-right">
												<strong>TOTAL: </strong>
											</span>
										</td>
										<td style="text-align: right;">$ '.number_format($order->amount,2,".",",").'</td>
									</tr>

									<tr class="row-title"><td colspan="4">Payment Information</td></tr>
                                    <tr><td colspan="4"><strong>Preferred Payment.:&nbsp;&nbsp;&nbsp;&nbsp;'.$order->preferred_payment.'</strong></td></tr>
									<tr><td colspan="4"><strong>Account No.:&nbsp;&nbsp;&nbsp;&nbsp;'.$merchant->bank_account_no.'</strong></td></tr>
									<tr><td colspan="4"><strong>Routing No.:&nbsp;&nbsp;&nbsp;&nbsp;'.$merchant->bank_routing_no.'</strong></td></tr>
									<tr><td colspan="4">***Please provide a copy of a voided check with this application***</td></tr>

									<tr class="row-title"><td colspan="4">Agent Information</td></tr>
									<tr>
                                        <td colspan="2"><strong>Agent Name:'.$user->first_name.' '.$user->last_name.'</strong></td>
                                        <td colspan="2"><strong>Contact No:'.$user->country_code.$user->mobile_number.'</strong></td>
									</tr>

								</table>
								<br><br>
								<table class="table">
									<tr class="row-title"><td colspan="4">Confirmation</td></tr>
									<tr >
										<td colspan="4" height="100" valign="top">
											<strong>Signature:</strong><br>'.$sig.'<br>
											<strong>Printed Name:&nbsp;&nbsp;&nbsp; '.$merchant->partner_contact()->first_name.' '. $merchant->partner_contact()->middle_name .' '.$merchant->partner_contact()->last_name.'</strong><br>
                                            <strong>Date Sent: &nbsp;&nbsp;&nbsp;'.$sendDate.'</strong><br>
											<strong>Date Signed: &nbsp;&nbsp;&nbsp;'.$sigDate.'</strong><br>
										</td>
									</tr>
								</table>
							</body>
						</html>';

		return $html;
	}


	public function sendEmailOrder($id){
        $order = ProductOrder::find($id);
		if(!isset($order)){
    		return Array('message' => 'Cannot find Order!');
    	}
    	if($order->status == "Application Signed"){
    		return Array('message' => 'Application already signed!');
    	}
        $merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',array(3,9))->first();
        if (!isset($merchant->partner_company->email)) {
            if($merchant->partner_type_id == 3){
                return Array('message' => 'The merchant has no email address and the system will not be able to send the PDF for signing. The signing should be done via GOETU platform. Thank you!','redirect' => '/merchants/details/'.$order->partner_id.'/products');
            }else{
                return Array('message' => 'The merchant has no email address and the system will not be able to send the PDF for signing. The signing should be done via GOETU platform. Thank you!','redirect' => '/merchants/branchDetails/'.$order->partner_id.'/products');
            }

        }
		$agent = Partner::get_partner_info($merchant->parent_id);   
		$agent = $agent[0];
        if ($agent->partner_type_description == 'AGENT' || $agent->partner_type_description == 'SUB AGENT')
        {
        	$agent_contact = "This email was sent on behalf of {$agent->first_name} with contact information below.<br> 
				<p>
				Agent Information<br>
				Name: {$agent->first_name} {$agent->last_name}<br>
				Email: {$agent->contact_email}<br>
				Mobile: + {$agent->company_country_code}{$agent->mobile_number}<br>
				</p><br>
				<br>";
        }
        else
        {
        	$agent_contact = "This email was sent on behalf of {$agent->company_name} with contact information below.<br> 
				<p>
				Company Information<br>
				Name: {$agent->company_name}<br>
				Email: {$agent->email}<br>
				Mobile: + {$agent->company_country_code}{$agent->phone1}<br>
				</p>";
        }
        // $link = "/appsign/{$order->sign_code}/sign";
        $link = "/merchants/{$order->id}/confirm_email";
        $email_address = $merchant->partner_company->email;


        // return view("mails.signature",compact('link','merchant','order','agent_contact'));

        $data = array(
            'link' => $link,
            'merchant' => $merchant,
            'order' => $order,
            'agent_contact' => $agent_contact,
        );

        if($merchant->partner_company->email == "" || $merchant->partner_company->email == null ){
            return Array('message' => 'No email address defined for this merchant','redirect' => '/merchants/details/'.$order->partner_id.'/products');
        }

        Mail::send(['html'=>'mails.signature'],$data,function($message) use ($data,$merchant){

            $message->to($merchant->partner_company->email ,$merchant->partner_contact()->first_name.' '.$merchant->partner_contact()->last_name);
            $message->subject('[GoETU] Product Order');
            $message->from('no-reply@goetu.com');
        });

        if (Mail::failures()) {
            return Array('message' => 'Error Sending Mail!');
        }

        DB::transaction(function() use ($id){
			$order = ProductOrder::find($id);
			$order->status = 'PDF Sent';
            $order->date_sent = date('Y-m-d H:i:s');
			$order->save();
        });

        
        if($merchant->partner_type_id == 3){
            return Array('message' => 'success','redirect' => '/merchants/details/'.$order->partner_id.'/products');
        }else{
            return Array('message' => 'success','redirect' => '/merchants/branchDetails/'.$order->partner_id.'/products');
        }

        

	}


	public function sendWelcomeEmail($id){
        $order = ProductOrder::find($id);
		if(!isset($order)){
    		return Array('message' => 'Cannot find Order!');
    	}
        $wemail = WelcomeEmailTemplate::where('product_id',$order->product_id)->where('status','A')->first();
		if(!isset($wemail)){
    		return Array('message' => 'Cannot find Welcome Email Template for this product!');
    	}
		$merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',array(3,9))->first();
        $email_address = $merchant->partner_company->email;

        if($merchant->partner_company->email == "" || $merchant->partner_company->email == null ){
            return Array('message' => 'No email address defined for this merchant');
        }

		Mail::send([], [], function ($message) use ($merchant,$wemail){
			$message->to($merchant->partner_company->email ,$merchant->partner_contact()->first_name.' '.$merchant->partner_contact()->last_name);
            $message->subject('[GoETU] Welcome to GoETU Applications');
            $message->setBody($wemail->description, 'text/html');
            $message->from('no-reply@goetu.com');
		});


        if (Mail::failures()) {
            return Array('message' => 'Error Sending Mail!');
        }

        return Array('message' => 'Email Sent!');

	}

	public function updateMechantPaymentMethod($id,Request $request){
        $message="";
        $partner = Partner::find($id);
        if($request->txtPaymentType == 2)
        {
            $payment_method = PartnerPaymentInfo::where('partner_id',$id)->where('payment_type_id',2)->where('status','A')->get();
            if(count($payment_method)>0){
                if($partner->partner_type_id == 3){
                    return redirect('/merchants/details/'.$id.'/billing#pm')->with('failed','You are only allowed to create one CASH payment method.');
                }else{
                    return redirect('/merchants/branchDetails/'.$id.'/billing#pm')->with('failed','You are only allowed to create one CASH payment method.');
                }         
            }     
        }

        DB::transaction(function() use ($id,$request){
            $partner = Partner::find($id);
            $is_default_payment=0;
            if(isset($request->chkSetAsDefault)) $is_default_payment =1;
            
            if($request->paymentMethodId == -1)
            {
                $paymentMethod = new PartnerPaymentInfo;
                $paymentMethod->partner_id = $id;
                $paymentMethod->payment_type_id = $request->txtPaymentType;
                $paymentMethod->is_default_payment = $is_default_payment;
                $paymentMethod->bank_name = $request->txtBankName == null ? "" : $request->txtBankName;
                $paymentMethod->routing_number = $request->txtRoutingNumber == null ? "" : $request->txtRoutingNumber;
                $paymentMethod->bank_account_number = $request->txtBankAccountNumber == null ? "" : $request->txtBankAccountNumber;
                $paymentMethod->status = 'A';
                $paymentMethod->create_by = auth()->user()->username;
                $paymentMethod->update_by = auth()->user()->username;
                $paymentMethod->save();        

                if ($is_default_payment==1){
                    PartnerPaymentInfo::where('partner_id', '=', $id)->where('id','<>',$paymentMethod->id)->update(array('is_default_payment' => 0));
                }    
                $message = "Payment Method was successfully added";

                $accountingDept = UserType::where('description', 'Accounting')
                    ->where('company_id', $partner->company_id)
                    ->first();

                if (isset($accountingDept) && $accountingDept->users->count() > 0) {
                    $users = $accountingDept->users;

                    $emailAddresses = [];
                    $usernames = [];
                    foreach ($users as $user) {
                        $emailAddresses[] = $user->email_address;
                        $usernames[] = $user->username;
                    }

                    if (! empty($emailAddresses)) {
                        $emailData = [
                            'email' => (object) [
                                'subject' => "{$partner->partnerCompany->company_name} Payment Method", 
                                'body'=> "Payment method with bank name of {$paymentMethod->bank_name} has been added"
                            ]
                        ];

                        $emailBody = view("mails.basic", $emailData)->render();
                        $emailAddresses = implode(',', $emailAddresses);
                        $emailOnQueue = new EmailOnQueue;
                        $emailOnQueue->subject = "{$partner->partnerCompany->company_name} Payment Method";
                        $emailOnQueue->body =  $emailBody;
                        $emailOnQueue->email_address = $emailAddresses;
                        $emailOnQueue->create_by = auth()->user()->username;
                        $emailOnQueue->is_sent = 0;
                        $emailOnQueue->sent_date = null;
                        $emailOnQueue->save();
                    }

                    $u = auth()->user()->full_name;
                    $timestamp = date('Y-m-d H:i:s');
                    $data = array();
                    foreach ($usernames as $username) {
                        $data[] = [
                            'partner_id' => -1,
                            'source_id' => -1,
                            'subject' => "{$partner->partnerCompany->company_name} Payment Method",
                            'message' => "Payment method with bank name of {$paymentMethod->bank_name} has been added",
                            'status' => 'N',
                            'create_by' => auth()->user()->username,
                            'update_by' => auth()->user()->username,
                            'redirect_url' => "/merchants/details/{$partner->id}/billing#pm",
                            'recipient' => $username,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp
                        ];
                    }

                    Notification::insert($data);
                }
            }else{
                $paymentMethod = PartnerPaymentInfo::find($request->paymentMethodId);
                $paymentMethod->payment_type_id = $request->txtPaymentType;
                $paymentMethod->is_default_payment = $is_default_payment;
                $paymentMethod->bank_name = $request->txtBankName;
                $paymentMethod->routing_number = $request->txtRoutingNumber;
                $paymentMethod->bank_account_number = $request->txtBankAccountNumber;
                $paymentMethod->update_by = auth()->user()->username;
                $paymentMethod->save();     

                if ($is_default_payment==1){
                    PartnerPaymentInfo::where('partner_id', '=', $id)->where('id','<>',$paymentMethod->id)->update(array('is_default_payment' => 0));
                }    
                $message = "Payment method_exists(object, method_name) was successfully updated";           

                $accountingDept = UserType::where('description', 'Accounting')
                    ->where('company_id', $partner->company_id)
                    ->first();

                if (isset($accountingDept) && $accountingDept->users->count() > 0) {
                    $users = $accountingDept->users;

                    $emailAddresses = [];
                    $usernames = [];
                    foreach ($users as $user) {
                        $emailAddresses[] = $user->email_address;
                        $usernames[] = $user->username;
                    }

                    if (! empty($emailAddresses)) {
                        $emailData = [
                            'email' => (object) [
                                'subject' => "{$partner->partnerCompany->company_name} Payment Method", 
                                'body'=> "Payment method with bank name of {$paymentMethod->bank_name} has been updated"
                            ]
                        ];

                        $emailBody = view("mails.basic", $emailData)->render();
                        $emailAddresses = implode(',', $emailAddresses);
                        $emailOnQueue = new EmailOnQueue;
                        $emailOnQueue->subject = "{$partner->partnerCompany->company_name} Payment Method";
                        $emailOnQueue->body =  $emailBody;
                        $emailOnQueue->email_address = $emailAddresses;
                        $emailOnQueue->create_by = auth()->user()->username;
                        $emailOnQueue->is_sent = 0;
                        $emailOnQueue->sent_date = null;
                        $emailOnQueue->save();
                    }

                    $u = auth()->user()->full_name;
                    $timestamp = date('Y-m-d H:i:s');
                    $data = array();
                    foreach ($usernames as $username) {
                        $data[] = [
                            'partner_id' => -1,
                            'source_id' => -1,
                            'subject' => "{$partner->partnerCompany->company_name} Payment Method",
                            'message' => "Payment method with bank name of {$paymentMethod->bank_name} has been updated",
                            'status' => 'N',
                            'create_by' => auth()->user()->username,
                            'update_by' => auth()->user()->username,
                            'redirect_url' => "/merchants/details/{$partner->id}/billing#pm",
                            'recipient' => $username,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp
                        ];
                    }

                    Notification::insert($data);
                }
            }
        });

        if($partner->partner_type_id == 3){
            return redirect('/merchants/details/'.$id.'/billing#pm')->with('success',$message);
        }else{
            return redirect('/merchants/branchDetails/'.$id.'/billing#pm')->with('success',$message);
        }  
        
        
    }

    public function payment_method($id)
    {
        $payment_method  = PartnerPaymentInfo::find($id);
        return response()->json($payment_method);
    }

    public function cancel_payment_method($id, $partner_id)
    {
        $partner = Partner::find($partner_id);
        $paymentMethod = PartnerPaymentInfo::find($id);
        $paymentMethod->status = 'I';
        $paymentMethod->update_by = auth()->user()->username;
        $paymentMethod->save();

        $accountingDept = UserType::where('description', 'Accounting')
            ->where('company_id', $partner->company_id)
            ->first();

        if (isset($accountingDept) && $accountingDept->users->count() > 0) {
            $users = $accountingDept->users;

            $emailAddresses = [];
            $usernames = [];
            foreach ($users as $user) {
                $emailAddresses[] = $user->email_address;
                $usernames[] = $user->username;
            }

            if (! empty($emailAddresses)) {
                $emailData = [
                    'email' => (object) [
                        'subject' => "{$partner->partnerCompany->company_name} Payment Method", 
                        'body'=> "Payment method with bank name of {$paymentMethod->bank_name} has been deleted"
                    ]
                ];

                $emailBody = view("mails.basic", $emailData)->render();
                $emailAddresses = implode(',', $emailAddresses);
                $emailOnQueue = new EmailOnQueue;
                $emailOnQueue->subject = "{$partner->partnerCompany->company_name} Payment Method";
                $emailOnQueue->body =  $emailBody;
                $emailOnQueue->email_address = $emailAddresses;
                $emailOnQueue->create_by = auth()->user()->username;
                $emailOnQueue->is_sent = 0;
                $emailOnQueue->sent_date = null;
                $emailOnQueue->save();
            }

            $u = auth()->user()->full_name;
            $timestamp = date('Y-m-d H:i:s');
            $data = array();
            foreach ($usernames as $username) {
                $data[] = [
                    'partner_id' => -1,
                    'source_id' => -1,
                    'subject' => "{$partner->partnerCompany->company_name} Payment Method",
                    'message' => "Payment method with bank name of {$paymentMethod->bank_name} has been deleted",
                    'status' => 'N',
                    'create_by' => auth()->user()->username,
                    'update_by' => auth()->user()->username,
                    'redirect_url' => "/merchants/details/{$partner->id}/billing#pm",
                    'recipient' => $username,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ];
            }

            Notification::insert($data);
        }
        
        return redirect('/merchants/details/'.$paymentMethod->partner_id.'/billing#pm')->with('success','Payment Method was successfully  deleted.');
    }

    public function getInvoice($id){
        $invoice = InvoiceHeader::find($id);

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

        $invoice->merchant = $invoice->partner->partner_contact()->first_name.' '. $invoice->partner->partner_contact()->middle_name .' '.$invoice->partner->partner_contact()->last_name;
        return $invoice;
    }

    public function getRecurring($id){
        $frequency = InvoiceFrequency::find($id);
        $list = array();
        $invoices = InvoiceDetail::where('invoice_frequency_id',$id)->get();
        foreach($invoices as $inv){
            $list[] = $inv->invoice_id;
        }
        $frequency->start_date = date('Y-m-d', strtotime($frequency->start_date));
        $frequency->end_date = date('Y-m-d', strtotime($frequency->end_date));
        $frequency->invoices = InvoiceHeader::whereIn('id',$list)->get();
        foreach($frequency->invoices as $inv){
            $inv->status = $inv->status_code->description;
            $inv->invoice_date = date('m/d/Y', strtotime($inv->invoice_date));
        }
        return $frequency;
    }    

    public function updateRecurring(Request $request){
        DB::transaction(function() use ($request){
            $frequency = InvoiceFrequency::find($request->txtFrequencyId);
            $frequency->end_date = $request->recEnd;
            $frequency->status = $request->txtFrequencyStatus;
            $frequency->save();

            if($request->txtFrequencyStatus == 'Active'){           
                $partner = Partner::find($request->txtFrequencyPID);
                $partner->billing_status = 'Active';
                $partner->save();
            }

        });
        return redirect('/merchants/details/'.$request->txtFrequencyPID.'/billing#recInv')->with('success','Recurring Invoice Updated');
    }

    public function createInvoice($id,Request $request){
        DB::transaction(function() use ($id,$request){
            $partner = Partner::find($id);
            $invoice = new InvoiceHeader;
            $invoice->order_id =  -1;
            $invoice->partner_id =  $id;
            $invoice->invoice_date =  date('Y-m-d',strtotime($request->txtInvoiceDate)); 
            $invoice->due_date =  date('Y-m-d',strtotime($request->txtInvoiceDueDate)); 
            $invoice->total_due =   $request->txtTotalDue;
            $invoice->reference =  'Manual Invoice';
            $invoice->create_by =  'System';
            $invoice->status =  'U';
            $invoice->remarks =  $request->txtNotes;
            $invoice->save();

            $invoicePayment = new InvoicePayment;
            $invoicePayment->invoice_id = $invoice->id;
            $invoicePayment->payment_type_id =  $request->newInvoicePaymentType;
            $invoicePayment->save();
            $lineCtr = 0;

            $details = json_decode($request->txtInvoiceDetailList);

            foreach($details as $detail){
                $lineCtr++;
                $invoiceDetail = new InvoiceDetail;
                $invoiceDetail->invoice_id = $invoice->id;
                $invoiceDetail->order_id = -1; 
                $invoiceDetail->line_number = $lineCtr;
                $invoiceDetail->product_id = -1;
                $invoiceDetail->description = $detail->description;
                $invoiceDetail->amount =  $detail->amount;
                $invoiceDetail->quantity =  1;
                $invoiceDetail->invoice_frequency_id =  -1;
                $invoiceDetail->cost =  $detail->amount;
                $invoiceDetail->save();
            }

            $accountingDept = UserType::where('description', 'Accounting')
                ->where('company_id', $partner->company_id)
                ->first();

            if (isset($accountingDept) && $accountingDept->users->count() > 0) {
                $users = $accountingDept->users;

                $emailAddresses = [];
                $usernames = [];
                foreach ($users as $user) {
                    $emailAddresses[] = $user->email_address;
                    $usernames[] = $user->username;
                }

                if (! empty($emailAddresses)) {
                    $emailData = [
                        'email' => (object) [
                            'subject' => "Invoice {$invoice->id}", 
                            'body'=> "New invoice created"
                        ]
                    ];

                    $emailBody = view("mails.basic", $emailData)->render();
                    $emailAddresses = implode(',', $emailAddresses);
                    $emailOnQueue = new EmailOnQueue;
                    $emailOnQueue->subject = "Invoice {$invoice->id}";
                    $emailOnQueue->body =  $emailBody;
                    $emailOnQueue->email_address = $emailAddresses;
                    $emailOnQueue->create_by = auth()->user()->username;
                    $emailOnQueue->is_sent = 0;
                    $emailOnQueue->sent_date = null;
                    $emailOnQueue->save();
                }

                $u = auth()->user()->full_name;
                $timestamp = date('Y-m-d H:i:s');
                $data = array();
                foreach ($usernames as $username) {
                    $data[] = [
                        'partner_id' => -1,
                        'source_id' => -1,
                        'subject' => "Invoice {$invoice->id}",
                        'message' => "New invoice created",
                        'status' => 'N',
                        'create_by' => auth()->user()->username,
                        'update_by' => auth()->user()->username,
                        'redirect_url' => "/merchants/details/{$partner->id}/billing",
                        'recipient' => $username,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ];
                }

                Notification::insert($data);
            }
        });
        $partner = Partner::find($id);
        if($partner->partner_type_id == 3){
            return redirect('/merchants/details/'.$id.'/billing')->with('success','Invoice successfully created.');
        }else{
            return redirect('/merchants/branchDetails/'.$id.'/billing')->with('success','Invoice successfully created.');
        }
        
    }

    public function voidInvoice(Request $request){
        DB::transaction(function() use ($request){
            $invoice = InvoiceHeader::find($request->txtInvoiceId);
            $invoice->status = 'C';
            $invoice->update_by = auth()->user()->username;
            $invoice->save();

            $partner = Partner::find($invoice->partner_id);
            $accountingDept = UserType::where('description', 'Accounting')
                ->where('company_id', $partner->company_id)
                ->first();

            if (isset($accountingDept) && $accountingDept->users->count() > 0) {
                $users = $accountingDept->users;

                $emailAddresses = [];
                $usernames = [];
                foreach ($users as $user) {
                    $emailAddresses[] = $user->email_address;
                    $usernames[] = $user->username;
                }

                if (! empty($emailAddresses)) {
                    $emailData = [
                        'email' => (object) [
                            'subject' => "Invoice {$invoice->id}", 
                            'body'=> "Invoice has been voided"
                        ]
                    ];

                    $emailBody = view("mails.basic", $emailData)->render();
                    $emailAddresses = implode(',', $emailAddresses);
                    $emailOnQueue = new EmailOnQueue;
                    $emailOnQueue->subject = "Invoice {$invoice->id}";
                    $emailOnQueue->body =  $emailBody;
                    $emailOnQueue->email_address = $emailAddresses;
                    $emailOnQueue->create_by = auth()->user()->username;
                    $emailOnQueue->is_sent = 0;
                    $emailOnQueue->sent_date = null;
                    $emailOnQueue->save();
                }

                $u = auth()->user()->full_name;
                $timestamp = date('Y-m-d H:i:s');
                $data = array();
                foreach ($usernames as $username) {
                    $data[] = [
                        'partner_id' => -1,
                        'source_id' => -1,
                        'subject' => "Invoice {$invoice->id}",
                        'message' => "Invoice has been voided",
                        'status' => 'N',
                        'create_by' => auth()->user()->username,
                        'update_by' => auth()->user()->username,
                        'redirect_url' => "/merchants/details/{$partner->id}/billing",
                        'recipient' => $username,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ];
                }

                Notification::insert($data);
            }
        });
        return Array('success' => true);
    }

    public function payInvoice(Request $request){
        DB::transaction(function() use ($request){

            $invoice = InvoiceHeader::find($request->txtInvoiceId);
            if($request->txtInvoiceStatus == 'P'){
                if($request->paymentAmount == $request->txtPending){
                    if($request->invpayment == 1){
                        if($invoice->status == "H"){
                            $status = 'P';
                        }else{
                            $status = 'S';
                        }
                    }else{
                        $status = 'P';                        
                    }
                }else{
                    $status = 'L';
                }
            }else{
                 $status = 'U';
            }


            $invoice->status = $status;
            $invoice->update_by = auth()->user()->username;
            $invoice->save();
            
            if($request->txtInvoiceStatus == 'P'){
                $invoicePayment = InvoicePayment::find($request->txtInvoiceId);
                $invoicePayment->payment_type_id = $request->invpayment;
                $invoicePayment->payment_amount = $invoicePayment->payment_amount == null ? 0 : $invoicePayment->payment_amount;
                $invoicePayment->payment_amount = $invoicePayment->payment_amount + $request->paymentAmount;
                $invoicePayment->save();      
                $user = User::where('reference_id', $request->txtPartnerId)->first();
                $company = PartnerCompany::where('partner_id', $user->company_id)->first();
                
                $data = array(
                    'id' => $request->txtInvoiceId,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'username' => $user->username,
                    'title' => '[GoETU] Product Invoice', //. $invoice->reference . ' - ' . $invoice->remarks,
                    'company_name' => $company->company_name,
                    'company_address1' => $company->address1,
                    'company_city' => $company->city,
                    'company_state' => $company->state,
                    'company_zip' => $company->zip,
                    'company_phone1' => '+' . $company->country_code . $company->phone1,
                    'invoice_name' => 'invoice_preview_'.$request->txtInvoiceId.'.pdf'
                );
                $email_body =  view('mails.product-invoice', $data)->render();
                $this->viewInvoice($data['id']);
                if (isset($user->email_address)) {
                    
                    $email_on_queue = new EmailOnQueue;
                    $email_on_queue->subject = $data['title'];
                    $email_on_queue->body = $email_body;
                    $email_on_queue->email_address = $user->email_address;
                    $email_on_queue->ticket_header_id = -1;
                    $email_on_queue->ticket_detail_id = -1;
                    $email_on_queue->create_by = auth()->user()->username;
                    $email_on_queue->is_sent = 0;
                    $email_on_queue->sent_date = null;
                    $email_on_queue->invoice_header_id = $data['id'];
                    
                    if ($email_on_queue->save()) {
                        // $invoiceHeader = InvoiceHeader::find($request->txtInvoiceId);
                        // $invoiceHeader->is_exported = 1;
                        // $invoiceHeader->save();
                    }
                }

                //COMMISSION COMPUTE    
                $query = "select p.id,p.merchant_mid,pc.company_name,pt.name as payment_type,
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
                            where ih.id = {$request->txtInvoiceId} order by p.parent_id";

                $records = DB::select(DB::raw($query));

                foreach ($records as $rec) {
                    $partner_id = $rec->partner_id;
                    $buyrate = $rec->price;
                    $uplineComm = 0;
                    $directUpline = true;
                    while($partner_id >= 0){
                        $cmd = "select prod.is_split_percentage,prod.upline_percentage,prod.downline_percentage,
                                prod.cost_multiplier,prod.cost_multiplier_value,prod.cost_multiplier_type,prod.mark_up_type_id,
                                case prod.mark_up_type_id when 4 then prod.other_buy_rate else prod.downline_buy_rate end as buyrate,
                                pc.company_name,p.parent_id,pt.name as partnerType,pp.id,pr.buy_rate as orig_price,main_pr.company_id,prod.buy_rate as cost
                                from partners p
                                inner join partners pp on p.parent_id = pp.id
                                inner join partner_types pt on pp.partner_type_id = pt.id
                                inner join partner_companies pc on pc.partner_id = pp.id
                                inner join partner_products prod on  pp.id = prod.partner_id
                                inner join products pr on prod.product_id = pr.id
                                inner join products main_pr on pr.parent_id = main_pr.id
                                where p.id = {$partner_id} and prod.product_id = {$rec->sub_id}";
                        $com = collect(\DB::select(DB::raw($cmd)))->first();
                        if(isset($com)){
                            if($directUpline){
                                $cost = $rec->cost;
                            }else{
                                $cost = $com->cost;
                            }
                            
                            $cmd="select id from cross_selling_agents where partner_id = {$com->company_id} and agent_id = {$com->id} and status = 'A'";
                            $agent_check = collect(\DB::select(DB::raw($cmd)))->first();
                            if(isset($agent_check)){
                                $income = $buyrate - $com->orig_price;
                            }else{
                                $income = $buyrate - $cost;
                            }

                            $income = $income * $rec->quantity;
                            $comm1 = 0;$comm2 = 0;$comm3 = 0;

                            if($com->is_split_percentage == 1){
                                $comm1 = ($income * $com->downline_percentage)/100;
                            }else{
                                $comm1 = $income;
                            }

                            if(isset($agent_check)){
                                $comm3 = 0;
                            }else{
                                if($rec->is_cc_client == 1 && $directUpline){
                                    $comm3 = ($cost * 0.3 * $rec->quantity); 
                                }
                                if($rec->is_cc_client == 0 && $directUpline){
                                    $comm3 = ($cost * 0.15 * $rec->quantity); 
                                }                                
                            }

                            $commission = $comm1 + $comm2 + $uplineComm + $comm3;
                            $commission = $commission > 0 ? $commission : 0;
                            
                            if($com->is_split_percentage == 1){
                                $uplineComm = ($income * $com->upline_percentage)/100;
                            }else{
                                $uplineComm = 0;
                            }

                            $invoiceCommission = new InvoiceCommission;
                            $invoiceCommission->invoice_id = $request->txtInvoiceId;
                            $invoiceCommission->product_id = $rec->sub_id;
                            $invoiceCommission->partner_id = $partner_id;
                            $invoiceCommission->sales = $buyrate * $rec->quantity;
                            $invoiceCommission->withoutMarkUp = $cost * $rec->quantity;
                            $invoiceCommission->withoutMarkUpCommission = $comm3;
                            $invoiceCommission->markUp = $income;
                            $invoiceCommission->markUpCommission = $comm1;
                            $invoiceCommission->totalCommission = $commission;
                            $invoiceCommission->directUpline = $directUpline ? 1 : 0;
                            $invoiceCommission->save();

                            $buyrate = $com->cost;  
                            if(isset($agent_check)){
                                $partner_id = -1;
                            }else{
                                $partner_id = $com->parent_id;
                            }

                        }else{
                            $partner_id = -1;
                        }    
                        $directUpline = false; 

                    }
                }
                //END COMMISSION COMPUTE

                //PROFITS COMPUTE   

                //END PROFITS COMPUTE

            }else{
                $invoicePayment = InvoicePayment::find($request->txtInvoiceId);
                $invoicePayment->payment_amount = 0;
                $invoicePayment->save();      

                $deletedRows = InvoiceCommission::where('invoice_id', $request->txtInvoiceId)->delete();
            }

            $partner = Partner::find($invoice->partner_id);
            $accountingDept = UserType::where('description', 'Accounting')
                ->where('company_id', $partner->company_id)
                ->first();

            if (isset($accountingDept) && $accountingDept->users->count() > 0) {
                $users = $accountingDept->users;

                $emailAddresses = [];
                $usernames = [];
                foreach ($users as $user) {
                    $emailAddresses[] = $user->email_address;
                    $usernames[] = $user->username;
                }

                if (! empty($emailAddresses)) {
                    $emailData = [
                        'email' => (object) [
                            'subject' => "Invoice {$invoice->id}", 
                            'body'=> "Invoice has been paid"
                        ]
                    ];

                    $emailBody = view("mails.basic", $emailData)->render();
                    $emailAddresses = implode(',', $emailAddresses);
                    $emailOnQueue = new EmailOnQueue;
                    $emailOnQueue->subject = "Invoice {$invoice->id}";
                    $emailOnQueue->body =  $emailBody;
                    $emailOnQueue->email_address = $emailAddresses;
                    $emailOnQueue->create_by = auth()->user()->username;
                    $emailOnQueue->is_sent = 0;
                    $emailOnQueue->sent_date = null;
                    $emailOnQueue->save();
                }

                $u = auth()->user()->full_name;
                $timestamp = date('Y-m-d H:i:s');
                $data = array();
                foreach ($usernames as $username) {
                    $data[] = [
                        'partner_id' => -1,
                        'source_id' => -1,
                        'subject' => "Invoice {$invoice->id}",
                        'message' => "Invoice has been paid",
                        'status' => 'N',
                        'create_by' => auth()->user()->username,
                        'update_by' => auth()->user()->username,
                        'redirect_url' => "/merchants/details/{$partner->id}/billing",
                        'recipient' => $username,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ];
                }

                Notification::insert($data);
            }
        });
        return Array('success' => true);
    }

    public function viewInvoice($id){

        $invoice = InvoiceHeader::find($id);
        $detailHtml= "";
        foreach($invoice->details as $detail){
            if($detail->product_id == -1){
                $detail->productname = $detail->description;
                $detail->category = "Not Applicable"; 
            }else{
                $detail->productname = $detail->product->name;
                $detail->category = $detail->product->category->name;          
            }

            $detailHtml .= '<tr>
                                <td>'.$detail->category.'</td>
                                <td colspan="2">'.$detail->productname.'</td>
                                <td style="text-align: right;">$ '.$detail->amount.'</td>
                            </tr>';
        }

        $html = '<!Doctype>
                    <html>
                        <head>
                            <meta charset="utf-8" />
                            <meta name="viewport" content="width=device-width, initial-scale=1" />
                            
                            <title>
                                GoETU Invoice Preview
                            </title>
                            <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
                            <style type="text/css">
                                .float-right{ float: right; }
                                .text-right{ text-align: right; }
                                .text-center{ text-align: center; }
                                table {
                                    width: 100%;
                                }
                                table td{
                                    border: 1px solid #000;
                                }
                                table .no-bordered{
                                    border: 0px;
                                }
                                .row-title{
                                    background: #000;
                                    color: #fff;
                                    font-weight: bold;
                                    text-align: center;
                                    text-transform: uppercase;
                                }
                                .sub{
                                    color: #000;
                                    background: #c7c7c7;
                                }
                                .no-border td{
                                    border-color: #fff;
                                }
                            </style>
                            </head>
                            <body>
                                <table class="table">
                                    <tr class="no-border">
                                        <td><img src="images/goetu.jpg" alt="Go3Sutdio"  height="50" width="200"/></td>
                                        <td colspan="3">
                                            <p class="text-right">
                                                <strong>Merchant Invoice</strong><br/>
                                                50 Broad St., Suite 1701, New York, NY 10004<br/>
                                                T:(888)377-381 &nbsp;&nbsp; F: (888)406-0777 &nbsp;&nbsp; E:support@go3solutions.com
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                                <table class="table ">
                                    <tr class="row-title"><td colspan="5">Invoice Information</td></tr>
                                    <tr class="no-border">
                                        <td width="20%">Client Name:</td>
                                        <td>'.$invoice->partner->partner_contact()->first_name.' '. $invoice->partner->partner_contact()->middle_name .' '.$invoice->partner->partner_contact()->last_name.'</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td width="20%">Total Due:</td>
                                        <td>'.$invoice->total_due.' USD</td>
                                    </tr>
                                    <tr class="no-border">
                                        <td width="20%">Invoice Date:</td>
                                        <td>'. date_format(new DateTime($invoice->invoice_date),"m/d/Y") .'</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td width="20%">Payment Method:</td>
                                        <td>'.$invoice->payment->type->name.'</td>
                                    </tr>
                                    <tr class="no-border">
                                        <td width="20%">Due Date:</td>
                                        <td>'. date_format(new DateTime($invoice->due_date),"m/d/Y") .'</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td width="20%">Status:</td>
                                        <td><b>'.$invoice->status_code->description.'</b></td>
                                    </tr>
                                </table>
                                <table class="table">
                                    
                                    <tr class="row-title">
                                        <td>Category</td>
                                        <td  colspan="2">Product</td>
                                        <td>Amount</td>
                                    </tr>'.$detailHtml.'

                                    <tr>
                                        <td colspan="3">
                                            <span class="float-right">
                                                <strong>TOTAL: </strong>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">$ '.number_format($invoice->total_due,2,".",",").'</td>
                                    </tr>

                                    <tr class="row-title sub"><td colspan="4">ACH Payment</td></tr>
                                    <tr><td colspan="4">***Please provide a copy of a voided check with this application***</td></tr>

                                    

                                </table>
                                
                            </body>
                        </html>';

        return PDF::loadHTML($html)->setPaper('a4', 'portrait')->setWarnings(false)->save(public_path().'/pdf/invoice_preview_'.$id.'.pdf')->stream('invoice_preview_'.$id.'.pdf');

    }

    /**
     *
     * Search merchants filter
     * @param $type
     * @param string $type_value
     * @return mixed
     */
    public function search($type, $type_value="",$status)
    {
        //if(request()->expectsJson()) {

            $query = Partner::where('partner_type_id', 3)->with('partner_company');

            switch ($type) {
                case "name":
                    $query->ofPartnerCompany($type_value);
                    break;
                case "phone":
                    $query->ofPartnerCompany(null,$type_value);
                    break;
                case "mid":
                    $query->ofMID($type_value);
                    break;
                case "cid":
                    $query->ofCID($type_value);
                    break;
                case "murl":
                    $query->OfMerchantUrl($type_value);
                    break;
                case "dba":
                    $query->ofPartnerCompany(null,null,null,null,$type_value);
                    break;
                case "upline":
                    $query->OfPID($type_value);
                default:

                    break;

            }
            $userType = session('user_type_desc');
            $partner_access=-1;
            $id = auth()->user()->reference_id; 

            $access = session('all_user_access');
            $admin_access = isset($access['admin']) ? $access['admin'] : "";

            $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
            $canViewUpline = (strpos($merchantaccess, 'view upline') === false) ? false : true;

            if (strpos($admin_access, 'super admin access') === false){
                $partner_access = Partner::get_partners_access($id);      
            }

            $out_merchants=array();
            if ($partner_access==""){$partner_access=$id;}
            $pt_id="";
            if ($partner_access != "-1") $query->whereIn('id',explode(",",$partner_access));
            // if($status == 'A'){
            //    $finalQuery = $query->whereIn('status', ['A','I','T'])->get(); 
            // }
            // if($status == 'P'){
            //    $finalQuery = $query->whereIn('status', ['P'])->get(); 
            // }     
            // if($status == 'C'){
            //    $finalQuery = $query->whereIn('status', ['C'])->get(); 
            // } 

            $out_merchants=array();
            if ($partner_access==""){$partner_access=$id;}
            $pt_id="";
            if (Access::hasPageAccess('merchant','view',true)){
                foreach ($query->get() as $p) {
                    $unverified="";
                    $verify_mobile = Country::where('country_calling_code',$p->partner_company->country_code)->first()->validate_number;
                    if($verify_mobile==1)
                    {
                        if(isset($p->user()->is_verified_email))
                        {
                            if ($p->user()->is_verified_email == 0 || $p->user()->is_verified_mobile == 0) {
                                $unverified = ' <span class="badge badge-danger">unverified</span>';
                            }
                        }
                    }
                    if ($canViewUpline) {
                        $upline = '';
                        $upline_partner_access = Partner::get_upline_partners_access($p->id);
                        if ($upline_partner_access != "") {
                            $uplineRec = Partner::find($p->parent_id);
                            $upline .= $uplineRec->partner_company->company_name .' - <a href="/partners/details/profile/'.$p->parent_id.'/profileCompanyInfo">' . $uplineRec->partner_id_reference. '</a>';
                        }
                    }
                    $view='<a class="btn btn-primary btn-sm" href="/merchants/details/'.$p->id.'/profile">View</a>';

                    if($p->billing_status == 'Active'){
                        $status='<span style="color:green">Active</span>';
                    }else{
                        $status='<span style="color:red">Cancelled</span>';
                    }

                    $view='';

                    // if($p->status == 'P'){
                    //     if (Access::hasPageAccess('merchant', 'board merchant',true)) {
                    //         $view .='<button class="btn btn-success btn-sm" onclick="boardMerchant('.$p->id.')">Board</button>';
                    //     }
                    //     if (Access::hasPageAccess('merchant', 'decline merchant',true)) {
                    //         $view .= '&nbsp;&nbsp;';
                    //         $view .= '<button class="btn btn-warning btn-sm" onclick="declineMerchant('.$p->id.')">Decline</button>';
                    //     }
                    // }
                    // if($p->status == 'C'){
                    //     if (Access::hasPageAccess('merchant', 'approve merchant',true)) {
                    //         $view .= '<button class="btn btn-success btn-sm" onclick="approveMerchant('.$p->id.')">Approve</button>';
                    //     }
                    //     if (Access::hasPageAccess('merchant', 'decline merchant',true)) {
                    //         $view .= '&nbsp;&nbsp;';
                    //         $view .= '<button class="btn btn-warning btn-sm" onclick="declineMerchant('.$p->id.')">Decline</button>';
                    //     }
                    // }

                    $incomplete = "";
                    if($p->federal_tax_id == "" || $p->merchant_mid == "" || $p->credit_card_reference_id == ""
                        || $p->merchant_processor == "" || $p->company_name == "" || $p->dba == "" || $p->services_sold == ""
                        || $p->bank_name == "" || $p->bank_account_no == "" || $p->bank_routing_no == "" 
                        || $p->withdraw_bank_name == "" || $p->withdraw_bank_account_no == "" || $p->withdraw_bank_routing_no == ""
                        || $p->merchant_url == "" || $p->authorized_rep == "" || $p->IATA_no == "" || $p->tax_filing_name == ""){
                        $incomplete = ' <span title="Incomplete Merchant Info"><i class="fa fa-exclamation-triangle big-icon"></i></span> ';
                    }

                    
                    $linkOpening = "<a href='/merchants/details/{$p->id}/profile'>";
                    $linkClosing = "</a>";

                    switch ($p->merchant_status_id) {
                        case MerchantStatus::BOARDED_ID:
                            $status = '<span style="color:green">Boarded</span>';
                            break;
                        
                        case MerchantStatus::LIVE_ID:
                            $status = '<span style="color:green">Live</span>';
                            break;

                        case MerchantStatus::CANCELLED_ID:
                            $status = '<span style="color:red">Cancelled</span>';
                            break;

                        case MerchantStatus::BOARDING_ID:
                            $status = '<span style="color:green">Boarding</span>';
                            break;
                        
                        case MerchantStatus::DECLINED_ID:
                            $status = '<span style="color:red">Declined</span>';
                            break;

                        case MerchantStatus::FOR_APPROVAL_ID:
                            $status = '<span style="color:green">For Approval</span>';
                            break;

                    }

                    if($p->merchant_status_id == MerchantStatus::LIVE_ID || $p->merchant_status_id == MerchantStatus::BOARDED_ID){
                        switch ($p->status) {
                            case 'A':
                                $status = '<span style="color:green">Live</span>';
                                break;
                            case 'V':
                                $status = '<span style="color:red">Cancelled</span>';
                                break;
                            case 'I':
                                $status = '<span style="color:red">Inactive</span>';
                                break;
                            case 'T':
                                $status = '<span style="color:red">Terminated</span>';
                                break;
                        }
                    }

                    $PID = ''; 
                    $order = ProductOrder::getPID($p->id);
                    foreach ($order as $o) {
                        $PID .= $o->PID . '<br>';
                    }


                    if ($canViewUpline) {
                        $out_merchants[] = array(
                            $p->partner_id_reference,
                            $upline,
                                $linkOpening.$incomplete.' '.$p->partner_company->company_name.$linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->partner_company->country_code.$p->partner_company->mobile_number).'</label>',
                            $status,
                            $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                            $PID,
                                $p->partnerContact->first_name.' '.$p->partnerContact->last_name.$unverified,
                                $p->partner_company->country_code.$p->partner_company->mobile_number,
                                $p->partner_company->email,
                                $p->partner_company->state,
                            $p->merchant_url,$view
                        );    
                    } else {
                        $out_merchants[] = array(
                            $p->partner_id_reference,
                                $linkOpening.$incomplete.' '.$p->partner_company->company_name.$linkClosing.'<label style="display:none;>'.str_replace('-', '', $p->partner_company->country_code.$p->partner_company->mobile_number).'</label>',
                            $status,
                            $p->merchant_mid == '' ? $p->merchant_mid : ($linkOpening . $p->merchant_mid . $linkClosing),
                            $PID,
                                $p->partnerContact->first_name.' '.$p->partnerContact->last_name.$unverified,
                                $p->partner_company->country_code.$p->partner_company->mobile_number,
                                $p->partner_company->email,
                                $p->partner_company->state,
                            $p->merchant_url,$view
                        );    
                    }
                }
            }
            return response()->json($out_merchants); 
        //}
    }

    public function merchantPurchase($id,Datatables $datatables){
        $partner_id = Partner::get_downline_partner_ids($id);
        $userType = session('user_type_desc');
        $query = Partner::whereIn('partner_type_id',array(3,9))->where('status','A')->whereRaw('id in('.$partner_id.')')->get();

        return $datatables->collection($query)
                          ->editColumn('monthTotalU', function ($data) {
                                $invoices = InvoiceHeader::where('status','U')->whereMonth('invoice_date',Carbon::now()->format('m'))->whereYear('invoice_date',Carbon::now()->format('Y'))->where('partner_id',$data->id)->get();
                                $total = 0;
                                foreach ($invoices as $invoice) {
                                    $total = $total + $invoice->total_due;
                                }
                                return '$ '.number_format((float)$total, 2, '.', '');

                          })
                          ->editColumn('yearTotalU', function ($data) {
                                $invoices = InvoiceHeader::where('status','U')->whereYear('invoice_date',Carbon::now()->format('Y'))->where('partner_id',$data->id)->get();
                                $total = 0;
                                foreach ($invoices as $invoice) {
                                    $total = $total + $invoice->total_due;
                                }
                                return '$ '.number_format((float)$total, 2, '.', '');
                          })
                          ->editColumn('overallTotalU', function ($data) {
                                $invoices = InvoiceHeader::where('status','U')->where('partner_id',$data->id)->get();
                                $total = 0;
                                foreach ($invoices as $invoice) {
                                    $total = $total + $invoice->total_due;
                                }
                                return '$ '.number_format((float)$total, 2, '.', '');
                          })
                          ->editColumn('monthTotal', function ($data) {
                                $invoices = InvoiceHeader::where('status','P')->whereMonth('invoice_date',Carbon::now()->format('m'))->whereYear('invoice_date',Carbon::now()->format('Y'))->where('partner_id',$data->id)->get();
                                $total = 0;
                                foreach ($invoices as $invoice) {
                                    $total = $total + $invoice->total_due;
                                }
                                return '$ '.number_format((float)$total, 2, '.', '');

                          })
                          ->editColumn('yearTotal', function ($data) {
                                $invoices = InvoiceHeader::where('status','P')->whereYear('invoice_date',Carbon::now()->format('Y'))->where('partner_id',$data->id)->get();
                                $total = 0;
                                foreach ($invoices as $invoice) {
                                    $total = $total + $invoice->total_due;
                                }
                                return '$ '.number_format((float)$total, 2, '.', '');
                          })
                          ->editColumn('overallTotal', function ($data) {
                                $invoices = InvoiceHeader::where('status','P')->where('partner_id',$data->id)->get();
                                $total = 0;
                                foreach ($invoices as $invoice) {
                                    $total = $total + $invoice->total_due;
                                }
                                return '$ '.number_format((float)$total, 2, '.', '');
                          })
                          ->rawColumns(['merchant','monthTotalU','yearTotalU','overallTotalU','monthTotal','yearTotal','overallTotal'])
                          ->make(true);
    }

    public function confirmPreview($id){
		$html = $this->createConfirmPDFHtml($id);
		return PDF::loadHTML($html)->setPaper('a4', 'portrait')->setWarnings(false)->save(public_path().'/pdf/confirm_page_'.$id.'.pdf')->stream('confirm_page'.$id.'.pdf');
	}

    public function confirmPage($id){
		$order = ProductOrder::find($id);
		if(!isset($order)){
    		return redirect('/merchants')->with('failed','Cannot find order!');
    	}
    	if($order->status == "Application Signed"){
    		return redirect('/merchants')->with('failed','Application already signed!');
    	}

        $partner_id = $order->partner_id;
		$merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',array(3,9))->first();
        $headername = 'Order # '.$id. ' | ' .$order->product->name . ' | ' . $merchant->partner_company->company_name;
        $confirmUrl = '/merchants/'.$id.'/confirm_preview';

        $confirmationMessage = 'By confirming, you accept the agreement';
        if (auth()->user()->username !== $merchant->partner_id_reference) {
            $confirmationMessage = "I, ".auth()->user()->first_name." ".auth()->user()->last_name;
            $confirmationMessage .= " will be accountable of all the merchant’s payable in case merchant disagrees with the company’s Merchant Agreement. ";
        }
        if($merchant->partner_type_id == 3){
            return view("merchants.sign.confirm_page", compact('confirmationMessage', 'merchant','headername','confirmUrl','id','partner_id'));
        }else{
            return view("branch.sign.confirm_page", compact('confirmationMessage', 'merchant','headername','confirmUrl','id','partner_id'));
        }
		
    }

    public function confirmEmail($id){
        $order = ProductOrder::find($id);
        $sign_code = $order->sign_code;
		if(!isset($order)){
    		return redirect('/merchants')->with('failed','Cannot find order!');
    	}
    	if($order->status == "Application Signed"){
    		return redirect('/merchants')->with('failed','Application already signed!');
    	}

        $partner_id = $order->partner_id;
		$merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',array(3,9))->first();
        $headername = 'Order # '.$id. ' | ' .$order->product->name . ' | ' . $merchant->partner_company->company_name;
        $confirmUrl = '/merchants/'.$id.'/confirm_preview';
		return view("merchants.sign.confirm_email",compact('merchant','headername','confirmUrl','sign_code','partner_id'));
    }
    
    private function createConfirmPDFHtml($id){
		$order = ProductOrder::find($id);
        $user = User::where('username',$order->create_by)->first();
		if(!isset($order)){
    		return redirect('/merchants')->with('failed','Cannot find order!');
    	}
        $merchant = Partner::where('id',$order->partner_id)->whereIn('partner_type_id',array(3,9))->first();
        $name = PartnerCompany::where('partner_id',$order->partner_id)->first();
        $company = isset($name->legal_name) ? $name->legal_name : $name->dba;
        $product = Product::find($order->product_id);
		/*$detailHtml= "";
		foreach($order->details as $detail){
			$detailHtml .= '<tr>
								<td>'.$detail->product->name.'</td>
								<td>'.$detail->frequency.'</td>
								<td style="text-align: right;">'.$detail->quantity.'</td>
								<td style="text-align: right;">$ '.$detail->amount.'</td>
							</tr>';
		}
		
		$sig = "";
		$sigDate="";

		if($order->status == "Application Signed"){
			$data = explode(',', $order->signature);
			$sig = '<img class="imported" src="' . $order->signature . '" height="50" width="200"></img>';
			$sigDate = date_format($order->updated_at,"m/d/Y");
		} */

        $html = '<!Doctype>
        <html>
            <head>
                <meta charset="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                
                <title>
                    Confirmation Page
                </title>
                <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
                <style type="text/css">
                    body{ font-size: 10px; }
                    .float-right{ float: right; }
                    .text-right{ text-align: right; }
                    .text-center{ text-align: center; }
                    .text-left{ text-align: left; }
                    table {
                        width: 100%;
                    }
                    table td{
                        border: 1px solid #000;
                    }
                    table td .no-bordered{
                        border: 0px;
                    }
                    table .no-bordered{
                        border: 0px;
                    }
                    .row-title{
                        background: #737a84;
                        color: #fff;
                        font-weight: bold;
                        text-align: center;
                        text-transform: uppercase;
                    }
                    .sub{
                        color: #000;
                        background: #c7c7c7;
                    }
                    .no-border td{
                        border-color: #fff;
                    }
                    .indented {
                        padding-left: 15px;
                    }
                    .underlined {
                        text-decoration: underline;
                    }
                </style>
                </head>
                <body>
                    <table class="table no-bordered">
                        <tr class="row-title"><td colspan="4">Confirmation Page</td></tr>
                        <tr class="text-left"><td colspan="4" class="no-bordered">
                        <p>Please read the Program Guide in its entirety. It describes the terms under which we will provide merchant processing Services to you. </p>
                        <p>From time to time you may have questions regarding the contents of your Agreement with Bank and/or Processor or the contents of your Agreement with TeleCheck. 
                        The following information summarizes portions of your Agreement in order to assist you in answering some of the questions we are most commonly asked. </p>
                        </td></tr>
                    </table>
                    <hr>
                    <table class="table no-bordered">
                        <tr class="text-left">
                            <td class="no-bordered">
                                <table>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>1. Your Discount Rates are assessed on transactions that qualify for
                                            certain reduced interchange rates imposed by MasterCard, Visa,
                                            Discover and PayPal. Any transactions that fail to qualify for these
                                            reduced rates will be charged an additional fee (see Section 25 of the
                                            Program Guide). </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>2. We may debit your bank account (also referred to as your Settlement
                                            Account) from time to time for amounts owed to us under the Agreement.
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>3. There are many reasons why a Chargeback may occur. When they
                                            occur we will debit your settlement funds or Settlement Account. For a
                                            more detailed discussion regarding Chargebacks see Section 14 of the
                                            Your Payments Acceptance Guide or see the applicable provisions of the
                                            TeleCheck Solutions Agreement.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>4. If you dispute any charge or funding, you must notify us within 60
                                            days of the date of the statement where the charge or funding appears
                                            for Card Processing or within 30 days of the date of a TeleCheck
                                            transaction.</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="no-bordered">
                                <table>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>5. The Agreement limits our liability to you. For a detailed description
                                            of the limitation of liability see Section 27, 37.3, and 39.10 of the Card
                                            General Terms; or Section 17 of the TeleCheck Solutions Agreement.
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>6. W e have assumed cer tain risks by agreeing to provide you with Card
                                            processing or check services. Accordingly, we may take certain actions to
                                            mitigate our risk, including termination of the Agreement, and/or hold
                                            monies otherwise payable to you (see Card Processing General Terms in
                                            Section 30, Term; Events of Default and Section 31, Reserve Account; Security
                                            Interest), (see TeleCheck Solutions Agreement in Section 7), under certain
                                            circumstances.
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>7. By execut ing this Agreement with us you are authorizing us and our
                                            Affiliates to obtain financial and credit information regarding your business
                                            and the signers and guarantors of the Agreement until all your obligations to
                                            us and our Affiliates are satisfied.
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>8. The Agreement contains a provision that in the event you terminate the
                                            Agreement prior to the expiration of your initial five (5) year term, you will
                                            be responsible for the payment of an early termination fee as set forth in Part
                                            IV, A.3 under “Additional Fee Information” and Section 16.2 of the TeleCheck
                                            Solutions Agreement.
                                            .</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>9. If you lease equipment from Processor, it is important that you review
                                            Section 1 in Third Party Agreements. Bank is not a party to this Agreement.
                                            THIS IS A NON-CANCELABLE LEASE FOR THE FULL TERM INDICATED.
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <table>
                        <tr class="text-left">
                            <td class="no-bordered">
                                <span>10. Card Organization Disclosure</span>
                            </td>
                        </tr>
                        <tr class="text-left">
                            <td class="no-bordered">
                                <p class="indented">Visa and MasterCard Member Bank Information:  Wells Fargo Bank, N.A. </p>
                                <p class="indented">The Bank’s mailing address is P.O. Box 6079, Concord, CA 94524, and its phone number is 1-844-284-6843.</p>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr class="text-left">
                            <td class="no-bordered">
                                <table>
                                    <tr><td class="no-bordered indented">Important Member Bank Responsibilities:</td></tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>a) The Bank is the only entity approved to extend acceptance of Visa and MasterCard products directly to a merchant.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>b) The Bank must be a principal (signer) to the Agreement.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>c) The Bank is responsible for educating merchants on pertinent Visa
                                            and MasterCard rules with which merchants must comply; but this
                                            information may be provided to you by Processor.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>d) The Bank is responsible for and must provide settlement funds to the merchant.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>e) The Bank is responsible for all funds held in reserve that are derived from settlement.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>f) The Bank is the ultimate authority should a merchant have any
                                            problems with Visa or MasterCard products (however, Processor
                                            also will assist you with any such problems).</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="no-bordered">
                                <table>
                                    <tr><td class="no-bordered indented">Important Member Bank Responsibilities:</td></tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>a) Ensure compliance with Cardholder data security and storage requirements.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>b) Maintain fraud and Chargebacks below Card Organization thresholds.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>c) Review and understand the terms of the Merchant Agreement.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>d) Comply with Card Organization Rules and applicable law and regulations</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>e) Retain a signed copy of this Disclosure Page.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>f) You may download “Visa Regulations” from Visa’s website at:
                                            https://usa.visa.com/support/merchant.html.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>g) You may download “MasterCard Regulations” from MasterCard’s website at:
                                            http://www.mastercard.com/us/merchant /support /rules.html.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="no-bordered indented">
                                            <span>h) You may download “American Express Merchant Operating Guide” from
                                            American Express’ website at: www.americanexpress.com/merchantopguide.</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    
                    <br><br>

                    <p>Print Client’s Business Legal Name: <span class="underlined"> ' . $company .  ' </span></p>
                    <p>By its signature below, Client acknowledges that it has received the Merchant Processing Application, Program Terms and Conditions [ version
                    CardCoN2104(ia)] consisting of 50 pages [ including this Confirmat ion Page and t he applicable Third Party Agreement(s)] . Int erchange
                    Qualification Matrix and American Express Program Pricing (version IQM.MVD.S17.1 or __________________), and Interchange Schedule. </p>
                    <p>Client further acknowledges reading and agreeing to all terms in the Program Terms and Conditions. Upon receipt of a signed facsimile or
                    original of this Confirmation Page by us, Client’s Application will be processed.</p>
                    <p>NO ALTERATIONS OR STRIKE-OUTS TO THE PROGRAM TERMS AND CONDITIONS W ILL BE ACCEPTED.</p>
                    
                </body>
            </html>';


        $htmlPOS = '<!Doctype>
        <html>
            <head>
                <meta charset="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                
                <title>
                    Confirmation Page
                </title>
                <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
                <style type="text/css">
                    body{ font-size: 10px; }
                    .float-right{ float: right; }
                    .text-right{ text-align: right; }
                    .text-center{ text-align: center; }
                    .text-left{ text-align: left; }
                    table {
                        width: 100%;
                    }
                    table td{
                        border: 1px solid #000;
                    }
                    table td .no-bordered{
                        border: 0px;
                    }
                    table .no-bordered{
                        border: 0px;
                    }
                    .row-title{
                        background: #737a84;
                        color: #fff;
                        font-weight: bold;
                        text-align: center;
                        text-transform: uppercase;
                    }
                    .sub{
                        color: #000;
                        background: #c7c7c7;
                    }
                    .no-border td{
                        border-color: #fff;
                    }
                    .indented {
                        padding-left: 15px;
                    }
                    .underlined {
                        text-decoration: underline;
                    }
                </style>
                </head>
                <body>
                    <table class="table no-bordered">
                        <tr class="row-title"><td colspan="4">GOETU POS MERCHANT ACCEPTANCE</td></tr>
                        <tr class="text-left"><td colspan="4" class="no-bordered">
                        <p><b>NOTE: Use of the app is free for 1 year only for the Version 1. Fees will be collected for after the first year or after GOETU POS Version 2 upgrade. Please contact your agent if you wish to update your app. Thank you. </b></p>
                        <p>MERCHANT AGREED AND ACCEPT: I have read and agree to the terms of this agreement and understand that the initial version of the GOETU POS application is FREE for 1 year ONLY – meaning that no payment will be collected for the FIRST YEAR or if I wish to upgrade the version. App download is only for IOS OS Versions 12.0.0 and up ONLY. The representative(s) identified have the authority to execute this Agreement.  </p>
                        </td></tr>
                    </table>
                    <hr>
                </body>
            </html>';

        if($product->name == 'GOETU Salon POS'){
            return $htmlPOS;
        }
		return $html;
    }
    
    public function invoices_management() {
        $partner_id = isset(auth()->user()->reference_id) ? auth()->user()->reference_id : -1;

        $partner_access = Partner::get_partners_access($partner_id);
        if ($partner_access==""){$partner_access=$id;}

        if ($partner_id != -1){
            $partner_info = Partner::get_partner_info($partner_id,false,"1,2,3,4,5,6,7,8");
        } else {
            $partner_info = Partner::get_partner_info($partner_id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $payment_types = PaymentType::where('status','A')->orderBy('name','asc')->get();
        
        $merchant = Partner::where('id',$partner_id)->whereIn('partner_type_id',array(3,9))->first();

        $invoices = InvoiceHeader::where('partner_id',$partner_id)->where('status','P')->get();

        return view("merchants.invoices",compact('partner_id','merchant','payment_types','invoices'));
    }

    public function cancelMerchant(Request $request, $id) {
        DB::transaction(function() use ($request, $id) {
            $partner = Partner::find($id);
            $partner->billing_status = 'Cancelled';
            $partner->merchant_status_id = MerchantStatus::CANCELLED_ID;
            $partner->reason_of_action = $request->reason_of_action;
            $partner->save();

            InvoiceFrequency::where('partner_id',$id)
                ->update([
                    'status' => 'Inactive',
                    'update_by' => auth()->user()->username
                ]);
        });      

        return Array('success' => true);
    }
    
    public function orders(){
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canListOrder = (strpos($merchantaccess, 'order list') === false) ? false : true;

        if (!$canListOrder){
            return redirect('/processFlow')->with('failed','No access to this page');
        }
        $canSign = (strpos($merchantaccess, 'sign document') === false) ? false : true;
        $canProcessOrder = (strpos($merchantaccess, 'process order') === false) ? false : true;
        return view("merchants.details.orders",compact('canSign','canProcessOrder','access'));
    }

    public function orders_data(){

        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canListOrder = (strpos($merchantaccess, 'order list') === false) ? false : true;

        if (!$canListOrder){
            return redirect('/processFlow')->with('failed','No access to this page');
        }

        $partner_access=-1;
        $id = auth()->user()->reference_id; 

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
  
        if ($partner_access != "-1") 
        {
            $orders = ProductOrder::with('partnerCompany')
                    ->with(['partner' => function($query) {
                        $query->with('partner_parent')
                            ->with('partner_company_parent')
                            ->get();
                    }])->whereIn('partner_id',explode(",",$partner_access))->get();
        } else {
            $orders = ProductOrder::with('partnerCompany')
                    ->with(['partner' => function($query) {
                        $query->with('partner_parent')
                        ->with('partner_company_parent')
                        ->get();
                    }])->get();
        }

        $canSign = (strpos($merchantaccess, 'sign document') === false) ? false : true;
        $canProcessOrder = (strpos($merchantaccess, 'process order') === false) ? false : true;

        $out_orders = array();
        $i=0;
  
        foreach($orders as $order)
        {
            switch ($order->status) {
                case 'Pending':
                    $order->application_status = 'Pending Signature';
                    break;
                case 'Signed':
                    $order->application_status = 'Application Signed';
                    break;
                default:
                    $order->application_status = $order->status;
                    break;
            }
            $order->task_status = ProductOrder::getCurrentTaskStatus($order->id);

            $order->invoiceDate = date("m/d/Y",strtotime($order->created_at));

            $task = '<a href="\merchants\workflow\\'.$order->partner_id.'\\'.$order->id.'">
                            <i class="fa fa-comment"></i></a>';
            $edit = '';
            $sign = '';
            $process_order = '';
            $resend_to_merchant = '';

            if($order->status == 'Pending') 
            {
                $edit = '<a href="javascript:void(0);" onclick="showOrder('.$order->id.')">
                                <i class="fa fa-cog"></i></a>';
            }
            $preview = '<a target="_blank" href="\merchants\\'.$order->id.'\order_preview" >
                            <i class="fa fa-file-pdf-o"></i></a>';
            if($canSign)
            {
                if ($order->status == 'Pending' || $order->status == 'PDF Sent')
                {
                    $sign = '<a href="\merchants\\'.$order->id.'\confirm_page">
                                            <i class="fa fa-pencil"></i></a>';    
                }                    
            }

            if($canProcessOrder)
            {
                if ($order->status == 'Pending' || $order->status == 'PDF Sent')
                {
                    $process_order = '<a href="javascript:void(0);" onclick="processOrder('.$order->id.')">
                                                <i class="fa fa-exchange"></i></a>';    
                }
            }

            if($order->status == 'Pending' || $order->status == 'PDF Sent')
            {
                if(strpos($access['merchant'], 'request signature') !== false){ 
                    $resend_to_merchant = '<a href="javascript:void(0);" onclick="SendEmail('.$order->id.','."'{$order->partnerCompany->email}'".');"><i class="fa fa-send"></i></a>';
                } else {
                    $resend_to_merchant = '';
                }
            }

            if(strpos($access['merchant'], 'welcome email') !== false){ 
                $resend_welcome = '<a href="javascript:void(0);" class="sendWelcomeEmail"  onclick="SendWelcomeEmail('.$order->id.','."'{$order->partnerCompany->email}'".');"><i class="fa fa-send"></i></a>';
            } else {
                $resend_welcome = '';    
            }

            $product_status = '<label style="color: '.$order->task_status['color'].'"> '.$order->task_status['status'].' </label>';
            
            if(strpos($access['merchant'], 'order billing id') === false){ 
                $out_orders[] = array(
                    $task,
                    $order->invoiceDate,
                    $order->partner->partner_company_parent->company_name,
                    $order->partnerCompany->company_name,
                    sprintf('%07d', $order->batch_id),
                    $order->id,
                    $order->product->name,
                    $order->application_status,
                    $product_status,
                    $edit,
                    $preview,
                    $sign, 
                    $process_order,
                    $resend_to_merchant,
                    $resend_welcome,
                );  
            } else {
                $out_orders[] = array(
                    $task,
                    $order->invoiceDate,
                    $order->partner->partner_company_parent->company_name ?? 'No Partner',
                    $order->partnerCompany->company_name,
                    sprintf('%07d', $order->batch_id),
                    $order->id,
                    $order->billing_id,
                    $order->product->name,
                    $order->application_status,
                    $product_status,
                    $edit,
                    $preview,
                    $sign, 
                    $process_order,
                    $resend_to_merchant,
                    $resend_welcome,
                );  

            }
 
            if(session('partner_type_id')==Partner::MERCHANT_ID)
            {
                unset($out_orders[$i][2]);
                unset($out_orders[$i][3]);
                $out_orders[$i] = array_values($out_orders[$i]);    
            }
           
            if(strpos($access['merchant'], 'work flow') === false)
            {
                unset($out_orders[$i][0]);
                $out_orders[$i] = array_values($out_orders[$i]);
            } 
           
           
            $i++;
        }

        return response()->json($out_orders); 

        

    
    }


    public function invoices()
    {
        $payment_types = PaymentType::where('status','A')->orderBy('name','asc')->get();
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canListInvoice = (strpos($merchantaccess, 'view invoice') === false) ? false : true;

        if (!$canListInvoice){
            return redirect('/processFlow')->with('failed','No access to this page');
        }
        $canPay = strpos($merchantaccess, 'pay invoice') === false ? false : true;
        $canVoid = strpos($merchantaccess, 'void invoice') === false ? false : true;
        $canCharge = strpos($merchantaccess, 'update charges') === false ? false : true;
        $canCreate = strpos($merchantaccess, 'create invoice') === false ? false : true;


        $partner_access=-1;
        $id = auth()->user()->reference_id; 

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
  
        if ($partner_access != "-1") 
        {
            $invoices = InvoiceHeader::with('partnerCompany')
                    ->with(['partner' => function($query) {
                        $query->with('partner_parent')
                            ->with('partner_company_parent')
                            ->get();
                    }])->whereIn('partner_id',explode(",",$partner_access))->get();
        } else {
            $invoices = InvoiceHeader::with('partnerCompany')
                    ->with(['partner' => function($query) {
                        $query->with('partner_parent')
                        ->with('partner_company_parent')->get();
                    }])->get();
        }


        return view("merchants.details.allinvoices",compact('payment_types','canPay','canVoid','canCharge','canCreate','invoices'));
    }


    public function invoices_data()
    {

        $partner_access=-1;
        $id = auth()->user()->reference_id; 

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
  
        if ($partner_access != "-1") 
        {
            $invoices = InvoiceHeader::with('partnerCompany')
                    ->with(['partner' => function($query) {
                        $query->with('partner_parent')
                            ->with('partner_company_parent')
                            ->get();
                    }])->whereIn('partner_id',explode(",",$partner_access))->get();
        } else {
            $invoices = InvoiceHeader::with('partnerCompany')
                    ->with(['partner' => function($query) {
                        $query->with('partner_parent')
                        ->with('partner_company_parent')->get();
                    }])->get();
        }

        $out_invoices = array();

        foreach($invoices as $invoice)
        {
            $status="";
            if($invoice->status_code->description == "Unpaid" || $invoice->status_code->description == "Voided")
            {
                $status = '<label style="color:red">'.$invoice->status_code->description.'</label>';  
            } else {
                $status = '<label style="color:green">'.$invoice->status_code->description.'</label>';      
            }
            $view = '<a class="btn btn-default btn-sm" href="javascript:void(0);" onclick="showInvoice('.$invoice->id.','.$invoice->partner_id.')">View</a>';

            $out_invoices[] = array(
                $invoice->id,
                date("m/d/Y",strtotime($invoice->invoice_date)),
                $invoice->partner->partner_company_parent->company_name ?? 'No Partner',
                $invoice->partnerCompany->company_name,
                $invoice->reference,
                date("m/d/Y",strtotime($invoice->due_date)),
                $invoice->total_due,
                $invoice->payment->type->name,
                $status,
                $view 

                
            );   
        
        }


        return response()->json($out_invoices); 
        
    }

    public function getCityState($zip) {
        $url = "https://zip.getziptastic.com/v2/US/". $zip;
        $address_info = file_get_contents($url);
        $json = json_decode($address_info, true);
        return $arrReturn = array(
            "success" => true,
            "city" => $json['city'], 
            "state" => $json['state'], 
            "abbr" => $json['state_short']
        );
    }

    public function workflows() {
        $access = session('all_user_access');
        $merchantaccess = isset($access['merchant']) ? $access['merchant'] : "";
        $canListWorkflow = (strpos($merchantaccess, 'work flow') === false) ? false : true;

        if (!$canListWorkflow){
            return redirect('/processFlow')->with('failed','No access to this page');
        }
        return view("merchants.details.workflows");    
    }

    public function workflow_data() {
        $partner_access=-1;
        $id = auth()->user()->reference_id; 

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
 
        if ($partner_access != -1) 
        {
            $tasks = SubTaskHeader::with(['productOrder' => function($query) use ($partner_access) {
                        $query->with(['partner' => function($query) {
                            $query->with('partner_parent')
                            ->with('partner_company_parent')
                            ->get();
                        }])->whereIn('partner_id',explode(",",$partner_access))->get();
                    }])->has('productOrder')->get(); 

        } else {
            $tasks = SubTaskHeader::with(['productOrder' => function($query) {
                $query->with(['partner' => function($query) {
                    $query->with('partner_parent')
                    ->with('partner_company_parent')
                    ->get();
                }])->get();
            }])->get();
        }
        $out_tasks = array();

        foreach($tasks as $task)
        {
            if(isset($task->productOrder))
            {
                $view = '<a class="btn btn-default btn-sm" href="\merchants\\'.$task->productOrder->partner_id.'\product-orders\\'.$task->productOrder->id.'\workflow">View</a>';
                $out_tasks[] = array(
                    $task->id,
                    $task->created_at->format('m/d/Y'),
                    $task->productOrder->partner->partner_company_parent->company_name ?? 'No Partner',
                    $task->productOrder->partnerCompany->company_name,
                    $task->name,
                    $task->completion_ratio,
                    $view   
                );   
            }
        
        }


        return response()->json($out_tasks); 

        
    }

    public function uploadfile(Request $request){
        $logs = array();
        if($request->hasFile('fileUploadCSV')){
            $extension = $request->file('fileUploadCSV')->getClientOriginalExtension();//File::extension($request->file->getClientOriginalExtension());
            if ($extension == "csv") {
                $path = $request->file('fileUploadCSV')->storeAs(
                            'merchants', $request->file('fileUploadCSV').'.'.$extension
                        );
                $data = Excel::load($request->file('fileUploadCSV'), function($reader) {})->get();
                // return $data; // will not proceed importing data
                $import_id = Partner::max('import_number');
                $import_id = $import_id == '' ? 1 : $import_id + 1;
                if(!empty($data) && $data->count()){
                    //Prelim
                    foreach ($data as $key => $value) {
                        $skip = false;
                        if ($value->partner_type == '' || strtolower($value->partner_type) != 'merchant') {
                            $logs[] = "Skipping ".$value->partner_type.", invalid value for partner type.";
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
                            
                            if(isset($upline))
                            {
                                if ($upline->id == '') {
                                    $logs[] = "Skipping ".$value->dba." due to invalid upline.";
                                    $skip = true;
                                }
                            } else {
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

                            // if ($value->city == '') {
                            //     $logs[] = "Skipping ".$value->dba." City must have a value.";
                            //     $skip = true;
                            // }
                            // if ($value->state == '') {
                            //     $logs[] = "Skipping ".$value->dba." State must have a value.";
                            //     $skip = true;
                            // }
                            /* if ($value->country == '' || is_numeric($value->country)) {
                                $logs[] = "Skipping ".$value->dba." Country must have a value or must be valid.";
                                $skip = true;
                            } */
                            // if ($value->zip == '') {
                            //     $logs[] = "Skipping ".$value->dba." Zip must have a value.";
                            //     $skip = true;
                            // }

                            /* if ($value->phone_1 == '') {
                                $logs[] = "Skipping ".$value->dba." Business Phone 1 must have a value.";
                                $skip = true;
                            } */
                            // $pc2_exist = DB::table('partner_companies')->select('id')->where(DB::raw("ucase(phone1)"),$value->phone_1)->first();
                            // if ($pc2_exist) {
                            //     $logs[] = "Skipping  ".$value->dba.", Business Phone 1 has already been used.";
                            //     $skip = true;
                            // }

                            if ($value->mobile_number == '' && $value->email == '') {
                                $logs[] = "Skipping  ".$value->dba.", Mobile Number/Email must have a value.";
                                $skip = true;
                            }

                            // if ($value->mobile_number != '') {
                            //     $pc3_exist = DB::table('partner_companies')->select('id')->where(DB::raw("ucase(mobile_number)"),'-'.$value->mobile_number)->first();
                            //     if ($pc3_exist) {
                            //         $logs[] = "Skipping  ".$value->dba.", Mobile Number has already been used.";
                            //         $skip = true;
                            //     }
                            // }

                            // if ($value->email != '') {
                            //     $pc1_exist = DB::table('partner_companies')->select('id')->where(DB::raw("ucase(email)"),$value->email)->first();
                            //     if ($pc1_exist) {
                            //         $logs[] = "Skipping  ".$value->dba.", Email has already been used.";
                            //         $skip = true;
                            //     }
                            // }

                            $len = 12;
                            if (trim(strtolower($country)) == 'china') {
                                $len = 13;
                                if ($value->phone_1 != '') {
                                    if(!preg_match("/^[0-9]{11}$/", $value->phone_1)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid phone 1.";
                                        $skip = true;
                                    }
                                }
                                if ($value->fax != '') {
                                    if(!preg_match("/^[0-9]{11}$/", $value->fax)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid fax.";
                                        $skip = true;
                                    }
                                }
                                if ($value->mobile_number != '') {
                                    if(!preg_match("/^[0-9]{11}$/", $value->mobile_number)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid mobile number.";
                                        $skip = true;
                                    }
                                }
                                if ($value->phone_2 != '') {
                                    if(!preg_match("/^[0-9]{11}$/", $value->phone_2)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid phone 2.";
                                        $skip = true;
                                    }
                                }
                                if ($value->contact_phone_1 != '') {
                                    if(!preg_match("/^[0-9]{11}$/", $value->contact_phone_1)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid contact phone 1.";
                                        $skip = true;
                                    }
                                }
                                if ($value->contact_phone_2 != '') {
                                    if(!preg_match("/^[0-9]{11}$/", $value->contact_phone_2)) {
                                        $logs[] = "Skipping  ".$value->dba.", invalid contact phone 2.";
                                        $skip = true;
                                    }
                                }
                                if ($value->contact_fax != '') {
                                    if(!preg_match("/^[0-9]{11}$/", $value->contact_fax)) {
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
                            }  */

                            if($skip){goto skip;}

                            $insertPartnersData = new Partner;
                            $insertPartnersData->create_by                  = auth()->user()->username;
                            $insertPartnersData->update_by                  = auth()->user()->username;
                            $insertPartnersData->status                     = "P";
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

                            $insertPartnersData->tax_id_number        = $value->tax_id_number != '' ? $value->tax_id_number : NULL;
                            $insertPartnersData->front_end_mid        = $value->front_end_mid != '' ? $value->front_end_mid : NULL;     
                            $insertPartnersData->back_end_mid        = $value->back_end_mid != '' ? $value->back_end_mid : NULL;  
                            $insertPartnersData->reporting_mid        = $value->reporting_mid != '' ? $value->reporting_mid : NULL;   
                            $insertPartnersData->pricing_type        = $value->pricing_type != '' ? $value->pricing_type : NULL;   
                            $insertPartnersData->business_type_code        = $value->mcc_code != '' ? $value->mcc_code : NULL;  

                            $insertPartnersData->merchant_mid        = isset($value->mid) ? $value->mid : NULL; 
                            $insertPartnersData->bank_routing_no        = isset($value->bank_routing) ? $value->bank_routing : NULL;   
                            $insertPartnersData->bank_dda        = isset($value->bank_dda) ? $value->bank_dda : NULL;  
                            $insertPartnersData->merchant_url        = isset($value->website) ? $value->website : NULL;  

                            if (!$insertPartnersData->save()) {
                                $logs[] = "Unable to create partner."; 
                                goto skip;
                            }
                            $partner_id = $insertPartnersData->id;
                            $parent_id = $insertPartnersData->parent_id;

                            $lead_id = substr($partner_type_description->name,0,1) .(100000+$lead_id->max_id); 
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

                            $max_count = Partner::where('partner_type_id',3)->count() + 1;
                            $username = $lead_id;
                            $partner_type = PartnerType::find(3);
                            $default_password = rand(1111111, 99999999);
                            $default_encrypted_password = bcrypt($default_password);

                            $user = new User;
                            $user->username = $username;
                            $user->password = $default_encrypted_password;
                            $user->first_name = $value->first_name != '' ? trim($value->first_name) : '';
                            $user->last_name =  $value->last_name != '' ? trim($value->last_name) : '';
                            $user->email_address = $value->email;
                            $user->user_type_id = $partner_type->user_type_id;
                            $user->reference_id = $partner_id;
                            $user->status = 'A';
                            $user->ein = '';
                            $user->ssn = '';
                            $user->city = trim($value->city);
                            $user->state = trim($value->state);
                            $user->country =  trim($country);
                            $user->zip = trim($value->zip);
                            $user->business_address1 = trim($value->business_address_1);
                            $user->mobile_number = substr($value->mobile_number,0,$len);
                            $user->business_phone1 = substr($value->phone_1,0,$len);
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
                            $user->country_code = $country_code->country_calling_code;
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
                            
                            if ($parent_id != -1)
                            {
                                $user->company_id = Partner::get_upline_company($partner_id);     
                            } else {
                                $user->company_id = -1;    
                            }

                            if (!$user->save()) {
                                $logs[] = "Unable to create merchant user."; 
                                goto skip;
                            }

                            $user_company = New UserCompany;
                            $user_company->user_id = $user->id;
                            $user_company->company_id = $user->company_id;
                            $user_company->save();

                            $user_type = New UserTypeReference;
                            $user_type->user_id = $user->id;
                            $user_type->user_type_id = $user->user_type_id;
                            $user_type->save();

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


    public function branch($id){

        $merchant = Partner::where('id',$id)->where('partner_type_id',3)->first();

        return view("merchants.details.branch",compact('id','merchant'));
    }


}

