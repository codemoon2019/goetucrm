<?php

namespace App\Http\Controllers\Partners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\DepartmentService;

use App\Models\PartnerType;
use App\Models\Partner;
use App\Models\Ownership;
use App\Models\Country;
use App\Models\State;
use App\Models\PartnerAttachment;
use App\Models\PartnerCompany;
use App\Models\PartnerContact;
use App\Models\PartnerBillingAddress;
use App\Models\PartnerMailingAddress;
use App\Models\User;
use App\Models\UserType;
use App\Models\Document;
use App\Models\Access;
use App\Models\PartnerProduct;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductModule;
use App\Models\ProductCategory;
use App\Models\PaymentFrequency;
use App\Models\PaymentType;
use App\Models\MarkUpType;
use App\Models\ProductTemplateHeader;
use App\Models\ProductTemplateDetail;
use App\Models\PartnerPaymentGateway;
use App\Models\PartnerProductAccess;
use App\Models\PartnerPaymentInfo;
use App\Models\PaymentProcessor;
use App\Models\TicketHeader;
use App\Models\TicketStatus;
use App\Models\TicketFilter;
use App\Models\PartnerProductModule;
use App\Models\Drafts\DraftPartner;
use App\Models\Drafts\DraftPartnerAttachment;
use Yajra\Datatables\Datatables;
use DB;
use File;
use Storage;
use Validator;
use Mail;
use Excel;
use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoicePayment;
use App\Models\InvoiceFrequency;
use App\Models\Commission;
use App\Models\CrossSellingAgent;
use App\Models\UsZipCode;
use App\Models\PhZipCode;
use App\Models\CnZipCode;
use App\Models\UserCompany;
use App\Models\UserTypeReference;
use Carbon\Carbon;

use App\Services\Tickets\TicketSetup;

class PartnersController extends Controller
{
    protected $departmentService;

	public function __construct(DepartmentService $departmentService)
    {
        $this->middleware('auth');
        $this->departmentService = $departmentService;
    }


    public function index()
    {   
        return redirect('/partners/management');
    }

    public function management(){
        $cities = UsZipCode::select('city','state_id','is_primary_city')
        ->distinct()
        ->orderBy('is_primary_city', 'desc')
        ->get()
        ->toArray();
        dd($cities);
        
        $id = auth()->user()->reference_id;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $is_admin = true;
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id); // auth()->user()->reference_id
            $is_admin = false;
        }
        if ($partner_access==""){$partner_access=$id;}
        // $pt_access = session('partner_type_access');
        // if ($pt_access=="") {
            $pt_access = "";
            $pt_access .= isset($access['company']) ? "7," : "";
            $pt_access .= isset($access['iso']) ? "4," : "";
            $pt_access .= isset($access['sub iso']) ? "5," : "";
            $pt_access .= isset($access['agent']) ? "1," : "";
            $pt_access .= isset($access['sub agent']) ? "2," : "";
            $pt_access = ($pt_access == "") ? "" : substr($pt_access, 0, strlen($pt_access) - 1); 
        // }

        $partner_types = PartnerType::get_partner_types($pt_access);
        $active_partner_tab="";
        $partner_details=array();
        foreach($partner_types as $partner_type){
            if(session('user_type_desc')!=$partner_type->name)
            {
                if ($active_partner_tab=="") $active_partner_tab = $partner_type->name;
            }
            $partner_details[] = array(
                'id' => $partner_type->id,
                'name' =>  $partner_type->name,

            );
        }
        $advanceSearchLabel = "Partners";
        $partnerSearch = true;
        $statesPH = State::where('country','PH')->orderBy('abbr')->get();
        $states = State::where('country','US')->orderBy('abbr')->get();
        $statesCN = State::where('country','CN')->orderBy('abbr')->get();
        return view("partners.management.list",compact('advanceSearchLabel','active_partner_tab','partner_details','partner_types','partner_details','partnerSearch','states','statesPH','statesCN','is_admin'));    
    }


    public function managementTree(){
        $id = auth()->user()->reference_id;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
       $isPartner = false;
        if (strpos($admin_access, 'super admin access') === false){
            if(auth()->user()->is_original_partner == 1){
                $partner_ids = Partner::get_downline_partner_ids(auth()->user()->reference_id);
                $isPartner = true;
            }else{
                $partner_ids = Partner::get_downline_partner_ids(auth()->user()->company_id);
            }
            $partners = Partner::where('status','A')->whereRaw('id in('.$partner_ids.')')->get();
        }else{
            $partners = Partner::where('status','A')->whereIn('partner_type_id',array(1,2,3,4,5,7))->get();
        }
        $js=$this->buildTree($partners,$isPartner);
        
        return view("partners.management.tree",compact('js'));    
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


        $js = 'config = {
                        container: "#partner-tree",
                        hideRootNode : true,
                        rootOrientation :"WEST",
                        nodeAlign : "CENTER",
                        node: {
                            collapsable: true,
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
            $parent_node = ($treeInfo->parent_id == -1) ? 'hidden_parent' : 'node'.$treeInfo->parent_id;

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
                        HTMLid: "'. $node.'",collapsed: true
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

    public function create()
    {
        $c1 = Access::hasPageAccess('company','add',true);
        $c2 = Access::hasPageAccess('iso','add',true);
        $c3 = Access::hasPageAccess('sub iso','add',true);
        $c4 = Access::hasPageAccess('agent','add',true);
        $c5 = Access::hasPageAccess('sub agent','add',true);
        $access = session('all_user_access');
        $userAccess = isset($access['draft applicants']) ? $access['draft applicants'] : "";
        $canSaveAsDraft = (strpos($userAccess, 'draft applicants list') === false) ? false : true;

        if ($c1 || $c2 || $c3 || $c4 || $c5) {
            $pt_access = "";
            $pt_access .= $c1 ? "7," : "";
            $pt_access .= $c2 ? "4," : "";
            $pt_access .= $c3 ? "5," : "";
            $pt_access .= $c4 ? "1," : "";
            $pt_access .= $c5 ? "2," : "";
            $pt_access = ($pt_access == "") ? "" : substr($pt_access, 0, strlen($pt_access) - 1); 
            $partner_types = PartnerType::get_partner_types($pt_access);
            $ownerships = Ownership::where('status','A')->orderBy('name','asc')->get();
            $countries = Country::where('status','A')->where('display_on_partner', 1)/* ->orderBy('name','asc') */->get();
            $documents = Document::where('status','A')->orderBy('sequence','asc')->get();

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

            $userDepartment = User::find(auth()->user()->id)->department->description;

            $paymentProcessor = PaymentProcessor::active()->orderBy('name')->get();         
            
            $initialCities = UsZipCode::where('state_id', 1)->orderBy('city')->get();

            $businessTypeGroups = Cache::get('business_types');

            return view('partners.create', compact('partner_types', 'canSaveAsDraft', 
                'ownerships', 'countries', 'documents', 'systemUser', 'userDepartment',
                'is_internal', 'paymentProcessor', 'initialCities','businessTypeGroups'));

        } else {
            return redirect('/')->with('failed', 'You have no access to that page.');
        }
    }

    public function store(Request $request) {
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        DB::transaction(function() use ($request, &$id, &$user){
            
            //Partners Table
            $partner = new Partner;
            $partner->partner_type_id = $request->txtPartnerTypeId; 
            $partner->original_partner_type_id = $request->txtPartnerTypeId;
            /* $partner->parent_id = $request->txtUplineId == null ? auth()->user()->reference_id : $request->txtUplineId;  
            $partner->original_parent_id = $request->txtUplineId == null ? auth()->user()->reference_id : $request->txtUplineId;  */
            if($request->selfAssign == 1){
                $partner->parent_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;  
                $partner->original_parent_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id; 
            }else{
                $partner->parent_id = $request->txtUplineId == null ? auth()->user()->reference_id : $request->txtUplineId;  
                $partner->original_parent_id = $request->txtUplineId == null ? auth()->user()->reference_id : $request->txtUplineId;     		
            }
            $partner->logo = ''; 
            $partner->partner_id_reference = ''; 
            $partner->merchant_processor = ''; 
            $partner->merchant_mid = ''; 
            $partner->federal_tax_id = ''; 
            $partner->interested_products = ''; 
            $partner->partner_status = 'New'; 
            $partner->services_sold = '';
            $partner->merchant_url = '';
            $partner->authorized_rep = '';
            $partner->IATA_no = '';
            $partner->tax_filing_name = '';
            $partner_type = PartnerType::where('id',$partner->partner_type_id)->first();
            $max_count = Partner::where('partner_type_id',$partner->partner_type_id)->count() + 1;
            $partner_id_reference =  $partner_type->initial . (100000+$max_count); 
            $partner->partner_id_reference = $partner_id_reference;
            $partner->status = 'A';
            $partner->create_by = auth()->user()->username;
            $partner->update_by = auth()->user()->username;

            if ($request->txtPartnerTypeId != '1') {
                $partner->credit_card_reference_id = $request->txtCreditCardReference;
            } else {
                /* $unpaid_switch = $request->txtTogBtnUnpaid == "on" ? 1 : 0;
                $paid_switch = $request->txtTogBtnPaid == "on" ? 1 : 0;
                $smtp_switch = $request->txtTogBtnSMTP == "on" ? 1 : 0; */

                // Fields for New Merchant Creation Form
                $partner->social_security_id = $request->txtSocialSecurityNumber;
                $partner->tax_id_number = $request->txtTaxID;
                $partner->bank_name = $request->txtBankName;
                $partner->bank_routing_no = $request->txtBankRouting;
                $partner->bank_dda = $request->txtBankDDA;
                $partner->bank_address = $request->txtBankAddress;
                $partner->email_notifier = $request->txtEmailNotifier;
                /* $partner->email_unpaid_invoice = $unpaid_switch;
                $partner->email_paid_invoice = $paid_switch;
                $partner->smtp_settings = $smtp_switch; */
                $partner->billing_cycle = $request->txtBillingCycle;
                $partner->billing_month = $request->txtBillingMonth;
                $partner->billing_day = $request->txtBillingDay;
                // End Fields for New Merchant Creation Form
            }

            $partner->tax_id_number = $request->txtTaxID;
            $partner->front_end_mid = $request->txtFrontEndMID;
            $partner->back_end_mid = $request->txtBackEndMID;
            $partner->reporting_mid = $request->txtReportingMID;
            $partner->pricing_type = $request->txtPricingType;

            $partner->save();
            $id = $partner->id;
            $parent_id = $partner->parent_id;
            

            if ($request->txtPartnerTypeId ==7)
            {
                $partner->company_id = $id;  
            } else {
                if ($partner->parent_id != -1)
                {
                    $partner->company_id = Partner::get_upline_company($id);     
                } else {
                    $partner->company_id = -1;    
                }

            }
            $partner->save();

            //PartnerCompany
            $partnerCompany = new PartnerCompany;
            $partnerCompany->partner_id = $id;
            
            //if ($request->txtPartnerTypeId != '1') {
                $country = Country::where('name',$request->txtCountry)->first();
                $partnerCompany->company_name = $request->txtCompanyName;
                $partnerCompany->dba = $request->txtDBA;
                $partnerCompany->business_date = $request->txtBusinessDate;
                $partnerCompany->website = $request->txtWebsite;
                $partnerCompany->country = $request->txtCountry;
                $partnerCompany->address1 = $request->txtBusinessAddress1;
                $partnerCompany->address2 = $request->txtBusinessAddress2;
                $partnerCompany->city = $request->txtCity;
                $partnerCompany->state = $request->txtState;
                $partnerCompany->zip = $request->txtBusinessZip;
                $partnerCompany->phone1 = $request->txtBusinessPhone1;
                $partnerCompany->phone2 = $request->txtBusinessPhone2;
                $partnerCompany->extension = $request->txtExtension1;
                $partnerCompany->extension_2 = $request->txtExtension2;
                $partnerCompany->extension_3 = $request->txtExtension3;
                $partnerCompany->fax = $request->txtFax;
                $partnerCompany->mobile_number = $request->txtContactMobile1_1;
                $partnerCompany->email = $request->txtEmail;
                $partnerCompany->ownership = $request->txtOwnership;
                $partnerCompany->ssn = $request->txtSSN;
            // } else {
            //     $country = Country::where('name',$request->txtCountryAgent)->first();
            //     $partnerCompany->company_name = $request->txtBusinessName; //$request->txtLegalBusinessName; //$request->txtCompanyName;
            //     $partnerCompany->business_name = $request->txtLegalBusinessName;; //$request->txtBusinessName;
            //     $partnerCompany->country = $request->txtCountryAgent;
            //     $partnerCompany->address1 = $request->txtAddressAgent;
            //     $partnerCompany->state = $request->txtStateAgent;
            //     $partnerCompany->city = $request->txtCityAgent;
            //     $partnerCompany->zip = $request->txtZipAgent;
            //     $partnerCompany->phone1 = $request->txtPhoneNumber;
            //     $partnerCompany->mobile_number = $request->txtContactMobileNumber1;
	    	//     $partnerCompany->email = $request->txtEmailAgent;
            // }
            
            $partnerCompany->country_code = $country->country_calling_code ;
            $partnerCompany->update_by = auth()->user()->username;
            $partnerCompany->save();

            //PartnerContact
		    $dob = $request->txtContactDOB1 != null ? date('Y-m-d', strtotime($request->txtContactDOB1)) : null;
            
            $partnerContact = new PartnerContact;
            $partnerContact->partner_id = $id;
            
            //if ($request->txtPartnerTypeId != '1') {
                $country = Country::where('name',$request->txtContactCountry1)->first();
                $partnerContact->first_name = $request->txtContactFirstName1;
                $partnerContact->middle_name = $request->txtContactMiddleInitial1;
                $partnerContact->last_name = $request->txtContactLastName1;
                $partnerContact->position = $request->txtContactTitle1;
                $partnerContact->country = $request->txtContactCountry1;
                $partnerContact->address1 = $request->txtContactHomeAddress1_1;
                $partnerContact->address2 = $request->txtContactHomeAddress1_2;
                $partnerContact->ownership_percentage = $request->txtOwnershipPercentage1;
                $partnerContact->ssn = $request->txtContactSSN1;
                $partnerContact->city = $request->txtContactCity1;
                $partnerContact->state = $request->txtContactState1;
                $partnerContact->zip =  $request->txtContactZip1;
                $partnerContact->other_number = $request->txtContactPhone1_1;
                $partnerContact->other_number_2 = $request->txtContactPhone1_2;
                $partnerContact->fax = $request->txtContactFax1;
                $partnerContact->mobile_number = $request->txtContactMobile1_1;
                $partnerContact->mobile_number_2 = $request->txtContactMobile1_2;
                $partnerContact->email = $request->txtContactEmail1;
            // } else {
            //     $country = Country::where('name',$request->txtCountryAgent)->first();
            //     $partnerContact->first_name = $request->txtContactFirstNameAgent;
            //     $partnerContact->middle_name = $request->txtContactMiddleInitialAgent;
            //     $partnerContact->last_name = $request->txtContactLastNameAgent;
            //     $partnerContact->address1 = $request->txtAddressAgent;
            //     $partnerContact->country = $request->txtCountryAgent;
            //     $partnerContact->state = $request->txtStateAgent;
            //     $partnerContact->city = $request->txtCityAgent;
            //     $partnerContact->zip =  $request->txtZipAgent;
    	    // 	$partnerContact->mobile_number = $request->txtContactMobileNumberAgent;
            //     $partnerContact->email = $request->txtEmailAgent;
            //     $partnerContact->ssn = $request->txtSSNAgent;
            // }
            
            $partnerContact->country_code =$country->country_calling_code;
            $partnerContact->dob = $dob;
            $partnerContact->is_original_contact = 1;
            $partnerContact->save();

            $details = $request->txtOtherHidden;
            if ($details) {
                $details = json_decode($details);
                foreach ($details as $d) {
                    if($request->input('txtContactFirstName'.$d) != "" 
                        && $request->input('txtContactLastName'.$d) != "") {

                        $partnerContact = new PartnerContact;
                        $partnerContact->partner_id = $id;
                        
                        //if ($request->txtPartnerTypeId != '1') {
                            $country = Country::where('name',$request->input('txtContactCountry'.$d))->first();
                            $partnerContact->first_name = $request->input('txtContactFirstName'.$d);
                            $partnerContact->middle_name = $request->input('txtContactMiddleInitial'.$d);
                            $partnerContact->last_name = $request->input('txtContactLastName'.$d);
                            $partnerContact->position = $request->input('txtContactTitle'.$d);
                            $partnerContact->ownership_percentage = $request->input('txtOwnershipPercentage'.$id);
                            $partnerContact->ssn = $request->input('txtContactSSN'.$d);
                            $partnerContact->country = $request->input('txtContactCountry'.$d);
                            $partnerContact->address1 = $request->input('txtContactHomeAddress'.$d.'_1');
                            $partnerContact->address2 = $request->input('txtContactHomeAddress'.$d.'_2');
                            $partnerContact->city = $request->input('txtContactCity'.$d);
                            $partnerContact->state = $request->input('txtContactState'.$d);
                            $partnerContact->zip = $request->input('txtContactZip'.$d);
                            $partnerContact->other_number = $request->input('txtContactPhone'.$d.'_1');
                            $partnerContact->other_number_2 = $request->input('txtContactPhone'.$d.'_2');
                            $partnerContact->fax = $request->input('txtContactFax'.$d);
                            $partnerContact->mobile_number = $request->input('txtContactMobile'.$d.'_1');
                            $partnerContact->mobile_number_2 = $request->input('txtContactMobile'.$d.'_2');
                            $partnerContact->email = $request->input('txtContactEmail'.$d);
                        // } else {
                        //     $country = Country::where('name',$request->txtCountryAgent)->first();
                        //     $partnerContact->first_name = $request->input('txtContactFirstNameAgent'.$d);
                        //     $partnerContact->middle_name = $request->input('txtContactMiddleInitialAgent'.$d);
                        //     $partnerContact->last_name = $request->input('txtContactLastNameAgent'.$d);
                        //     $partnerContact->address1 = $request->txtAddressAgent;
                        //     $partnerContact->country = $request->txtCountryAgent;
                        //     $partnerContact->state = $request->txtStateAgent;
                        //     $partnerContact->city = $request->txtCityAgent;
                        //     $partnerContact->zip =  $request->txtZipAgent;
                        //     $partnerContact->mobile_number = $request->input('txtContactMobileNumberAgent'.$d);
                        //     $partnerContact->email = $request->txtEmailAgent;
                        //     $partnerContact->ssn = $request->input('txtSSNAgent'.$d);
                        // }
                        
                        $partnerContact->country_code =$country->country_calling_code;
                        $partnerContact->is_original_contact = 0;
                        $partnerContact->save();        
                    }
                }
            }


            //PartnerMailingAddress
            $partnerMailingAddress = new PartnerMailingAddress;
            $partnerMailingAddress->partner_id = $id;
            //if ($request->txtPartnerTypeId != '1') {
                // if($request->chkSameAsBusiness){
                if($request->copy_to_mailing == 'on'){
                    $country = Country::where('name',$request->txtCountry)->first();
                    $partnerMailingAddress->country = $request->txtCountry;
                    $partnerMailingAddress->address = $request->txtBusinessAddress1;
                    $partnerMailingAddress->address2 = $request->txtBusinessAddress2;
                    $partnerMailingAddress->city = $request->txtCity;
                    $partnerMailingAddress->state = $request->txtState;
                    $partnerMailingAddress->zip = $request->txtBusinessZip;
                } else {
                    $country = Country::where('name',$request->txtMailingCountry)->first();
                    $partnerMailingAddress->country = $request->txtMailingCountry;
                    $partnerMailingAddress->address = isset($request->txtMailingAddress1) ? $request->txtMailingAddress1 : "";
                    $partnerMailingAddress->address2 = $request->txtMailingAddress2;
                    $partnerMailingAddress->city = $request->txtMailingCity;
                    $partnerMailingAddress->state = $request->txtMailingState;
                    $partnerMailingAddress->zip = $request->txtMailingZip;
                }
            // } else {
            //     $country = Country::where('name',$request->txtCountryAgent)->first();
            //     $partnerMailingAddress->country = $request->txtCountryAgent;
            //     $partnerMailingAddress->address = $request->txtAddressAgent;
            //     $partnerMailingAddress->city = $request->txtCityAgent;
            //     $partnerMailingAddress->state = $request->txtStateAgent;
            //     $partnerMailingAddress->zip = $request->txtBusinessZipAgent;
            // }
            
            $partnerMailingAddress->country_code = $country->country_calling_code;
            $partnerMailingAddress->create_by = auth()->user()->username;
            $partnerMailingAddress->update_by = auth()->user()->username;
            $partnerMailingAddress->save();

            //PartnerBillingAddress
            $partnerBillingAddress = new PartnerBillingAddress;
            $partnerBillingAddress->partner_id = $id;
            //if ($request->txtPartnerTypeId != '1') {
                // if($request->chkSameAsBusinessBilling){
                if($request->copy_to_billing == 'on'){
                    $country = Country::where('name',$request->txtCountry)->first();
                    $partnerBillingAddress->country = $request->txtCountry;
                    $partnerBillingAddress->address = $request->txtBusinessAddress1;
                    $partnerBillingAddress->address2 = $request->txtBusinessAddress2;
                    $partnerBillingAddress->city = $request->txtCity;
                    $partnerBillingAddress->state = $request->txtState;
                    $partnerBillingAddress->zip = $request->txtBusinessZip;
                } else {
                    $country = Country::where('name',$request->txtBillingCountry)->first();
                    $partnerBillingAddress->country = $request->txtBillingCountry;
                    $partnerBillingAddress->address = isset($request->txtBillingAddress1) ? $request->txtBillingAddress1 : "";
                    $partnerBillingAddress->address2 = $request->txtBillingAddress2;
                    $partnerBillingAddress->city = $request->txtBillingCity;
                    $partnerBillingAddress->state = $request->txtBillingState;
                    $partnerBillingAddress->zip = $request->txtBillingZip;
                }
            // } else {
            //     $country = Country::where('name',$request->txtCountryAgent)->first();
            //     $partnerBillingAddress->country = $request->txtCountryAgent;
            //     $partnerBillingAddress->address = $request->txtAddressAgent;
            //     $partnerBillingAddress->city = $request->txtCityAgent;
            //     $partnerBillingAddress->state = $request->txtStateAgent;
            //     $partnerBillingAddress->zip = $request->txtBusinessZipAgent;
            // }
            
            $partnerBillingAddress->country_code = $country->country_calling_code;
            $partnerBillingAddress->create_by = auth()->user()->username;
            $partnerBillingAddress->update_by = auth()->user()->username;
            $partnerBillingAddress->save();

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

            //Attachments
            $documents = Document::where('status','A')->orderBy('name','asc')->get();
            foreach ($documents as $document) {
                if ($request->file('fileUpload'.$document->id)!== null){
                    $thefile = File::get($request->file('fileUpload'.$document->id));
                    $fileNameWithExt = $request->file('fileUpload'.$document->id)->getClientOriginalName();
                    $fileName = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                    $extension = $request->file('fileUpload'.$document->id)->getClientOriginalExtension();
                    $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;

                    Storage::disk('attachment')->put($filenameToStore,$thefile);

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

            /* if ($request->file('fileUploadOthers')!== null){
                $thefile = File::get($request->file('fileUploadOthers'));
                $fileNameWithExt = $request->file('fileUploadOthers')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                $extension = $request->file('fileUploadOthers')->getClientOriginalExtension();
                $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;

                Storage::disk('attachment')->put($filenameToStore,$thefile);

                $attachment = new PartnerAttachment;
                $attachment->partner_id = $id;
                $attachment->name = $request->OthersDescription;
                $attachment->document_image = $filenameToStore;
                $attachment->document_id = -2;
                $attachment->create_by = auth()->user()->username;
                $attachment->update_by = auth()->user()->username;
                $attachment->status = 'A';
                $attachment->save();
            } */

            if (isset($request->txtDraftPartnerId)) {
                $draft = DraftPartner::find($request->txtDraftPartnerId);
                $draft->is_stored_to_partners = 1;
                $draft->save();
            }
            

            //USER'S CREATION
            $country = Country::where('name',$request->txtCountry)->first();
            $username = $partner_id_reference;
            $default_password = rand(1111111, 99999999);
            $default_encrypted_password = bcrypt($default_password);
            //dd($request);
            $user = new User;
            $user->username = $username;
            $user->password = $default_encrypted_password;
            //if ($request->txtPartnerTypeId != '1') { 
                $user->first_name = $request->txtContactFirstName1;
                $user->last_name = $request->txtContactLastName1;
                $user->country = $request->txtCountry;
                $user->email_address = $request->txtEmail;
                $user->mobile_number = $request->txtContactMobile1_1;
            // } else {
            //     $user->first_name = $request->txtContactFirstNameAgent;
            //     $user->last_name = $request->txtContactLastNameAgent;
            //     $user->country = $request->txtCountryAgent;
            //     $user->email_address = $request->txtEmailAgent;
            //     $user->mobile_number = $request->txtContactMobileNumberAgent;
            // }
            $user->user_type_id = $partner_type->user_type_id;
            $user->reference_id = $id;
            $user->status = 'A';
            $user->ein = '';
            $user->ssn = '';
            $user->city = $request->txtCity;
            $user->state = $request->txtState;
            $user->zip = $request->txtZip;
            $user->business_address1 = $request->txtBusinessAddress1;
            $user->business_address2 = $request->txtBusinessAddress2;
            $user->fax = $request->txtFax;
            $user->business_phone1 = $request->txtBusinessPhone1;
            $user->business_phone2 = $request->txtBusinessPhone2;
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
            $user->is_merchant = -1;
            $user->is_iso =  -1;
            $user->is_admin =  -1;
            $user->is_partner =  1;
            $user->is_original_partner =  1;
            if ($request->txtPartnerTypeId ==7)
            {
                $user->company_id = $id;  
            } else {
                if ($parent_id != -1)
                {
                    $user->company_id = Partner::get_upline_company($id);     
                } else {
                    $user->company_id = -1;    
                }

            }
            if ($request->hasFile("profileUpload")) {
                $attachment = $request->file('profileUpload');
                $fileName = pathinfo($attachment->getClientOriginalName(),PATHINFO_FILENAME);
                $extension = $attachment->getClientOriginalExtension();
                $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
                $storagePath = Storage::disk('public')->putFileAs('user_profile', $attachment,  $filenameToStore, 'public');
                $user->image = '/storage/user_profile/'.$filenameToStore;
            }
            $user->create_by = auth()->user()->username;
            $user->update_by = auth()->user()->username;

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
                //send email
                $data = array(
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'password' => $default_password,
                    'email_address' => $user->email_address,
                    'username' => $user->username,
                );

                //dd($email);
                Mail::send(['html'=>'mails.accountcreation'],$data,function($message) use ($data){

                    $message->to($data['email_address'],$data['first_name'].' '.$data['last_name']);
                    $message->subject('[GoETU] Account Creation');
                    $message->from('no-reply@goetu.com');
                });

                if (Mail::failures()) {
                    return redirect('/partners/create')->with('failed','Failed to send email.');
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


            if ($request->txtPartnerTypeId == 7) {
                /** @todo Change, sloppy code */
                $departmentId = $this
                    ->departmentService
                    ->createDefaultDepartments($partner->id); 

                $ticketSetup = new TicketSetup;
                $ticketSetup->setup($partner->id, $departmentId);
            }

            return $id;

        });
        return redirect('/partners/details/'.$id.'/products')->with([
            'success' => 'Partner Created. Credentials for user has been sent to registered email/mobile number.',
            'newUsername' => $user->username,
            'newUserId' => $user->id,
            'newEmail' => $user->email_address,
            'newFullName' => $user->first_name . ' ' . $user->last_name,
            'newImg' => $user->image,
        ]);
    
    }

    public function dashboard($id)
    {
        $partner_id = auth()->user()->reference_id;
        $partner_access = Partner::get_partners_access($partner_id);
        if ($partner_access==""){$partner_access=$id;}

        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];

        return view("partners.details.dashboard",compact('id','partner_info'));
    }

    public function profile($id)
    {
        return view("partners.details.profile");
    }

    public function products($id)
    {

        $partner_id = auth()->user()->reference_id;
        $partner_access = Partner::get_partners_access($partner_id);
        if ($partner_access==""){$partner_access=$id;}

        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];
        $access = session('all_user_access');
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        if (strpos($user_access, 'edit') !== false){
             $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
             $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $partner = Partner::find($id);
        if(!isset($partner))
        {
            return redirect('/partners/management')->with('failed','Partner not found.');
        }
        $parent_id = $partner->parent_id;
        
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $userType = session('user_type_desc');
        $is_admin = false;

        if (strpos($admin_access, 'super admin access') !== false){
            $partner_product_id=""; 
            $is_admin = true;   
        }

        // if($partner->partner_type_id != Partner::COMPANY_ID)
        // {
            if ($parent_id == -1){    
                $partner_product =  $partner->partner_product_access($id);
                $partner_product_id = (!isset($partner_product)) ? -1 :  $partner_product->product_access;
            } else {   
                $product_id="";
                $products = PartnerProduct::get_partner_products($parent_id);
                foreach($products as $p)
                {
                    $product_id = $product_id . $p->product_id . ",";
                }
                $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                if ($partner_product_id == "" || $partner_product_id == -1){
                    $partner_product_id = -1;    
                }else{
                    $parent_product_ids = Product::get_parent_product_id($partner_product_id);
                    $product_id="";
                    foreach($parent_product_ids as $pp)
                    {
                        $product_id = $product_id . $pp->parent_id . ",";
                    }
                    $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
                }
            }       
        // }  
        
        $productList = Product::api_get_products($partner_product_id, $id, 1);

        if(strpos($admin_access, 'super admin access') !== false && $parent_id == -1){
            $productList = Product::where('status','A')->where('parent_id',-1)->get();
        }
        
        $is_original_user=0;
        if (auth()->user()->reference_id==$id){
            $is_original_user=1;
        }
        foreach($productList as $p){
            if($parent_id == -1)
            {
                $p->categories = Product::find($p->id)->categories;
                $p->subproducts = Product::find($p->id)->subproducts;
                foreach($p->subproducts as $s){
                    $s->amount = number_format((float)$s->buy_rate, 2, '.', '');
                }
            }else{
                $p->subproducts = Product::get_child_products($p->id,$parent_id);
                $categories = Array();
                foreach($p->subproducts as $s){
                    $categories[] = $s->product_category_id;
                    $s->modules = ProductModule::where('product_id',$s->id)->where('status','A')->get();
                }
                $p->categories = ProductCategory::whereIn('id',$categories)->get();           
            }
        }
        $frequency = PaymentFrequency::where('status','A')->orderBy('sequence')->get();
        $markUp = MarkUpType::where('status','A')->get();
        $partner_products = PartnerProduct::where('partner_id', $id)->get();
        $parentProduct = Array();
        $products = Array();
        $company_id = Partner::get_top_upline_partner($id);        
        
        if(strpos($admin_access, 'super admin access') !== false && $parent_id == -1){
            $product_templates = ProductTemplateHeader::whereIn('partner_id', Array($company_id,-1))->where('status','A')->get();
        }else{
            $template_id = Partner::getProductTemplateID($partner_product_id);
            $product_templates = ProductTemplateHeader::whereIn('partner_id', Array($company_id,-1))->where('status','A')->whereRaw("id in ({$template_id})")->get();
        }

        foreach ($partner_products as $detail) {
            if(!in_array($detail->product->parent_id, $parentProduct))
            {
                $parentProduct[] = $detail->product->parent_id;
            }
            $detail->modules = PartnerProductModule::where('partner_id',$detail->partner_id)->where('product_id',$detail->product_id)->get();
            if($is_original_user==0){
                $cost = $detail->buy_rate;
            }else{
                $cost = ($detail->split_type=="Second Buy Rate") ? $detail->other_buy_rate : $detail->downline_buy_rate;
            }
 
            // if($detail->cost_multiplier == 1){
            //     if($detail->cost_multiplier_type == 'percentage'){
            //         $detail->cost = $cost * ($detail->cost_multiplier_value/100);
            //     }else{
            //         $detail->cost = $cost  * $detail->cost_multiplier_value;
            //     }
            // }else{
                $detail->cost = $cost ;
            // }
        }
        foreach($parentProduct as $prod){
           $products[] = Product::find($prod);
        }

        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        $query_date = date('Y-m-d');
        $date1 = str_replace('-', '/', $query_date);
        $startDate = date('m/01/Y', strtotime($query_date));
        $endDate = date('m/t/Y', strtotime($query_date));

        $formUrl = "/partners/details/".$id."/updateProduct";
        return view("partners.details.products",compact('products','productList','frequency','markUp','formUrl','partner_products','userType','product_templates','is_original_user','id','partner','partner_info','startDate','endDate','is_admin','isInternal'));

    }

    public function commissions($id)
    {
        $partner_id = auth()->user()->reference_id;
        $partner_access = Partner::get_partners_access($partner_id);
        if ($partner_access==""){$partner_access=$id;}

        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];
        $products = Product::get_partner_products($id);
        return view("partners.details.commission",compact('partner_info','id','products'));
    }

    public function getCommission($partner_id,$id)
    {
        return Commission::where('partner_id',$partner_id)->where('product_id',$id)->first();
    }

    public function updateCommission(Request $request)
    {
        if($request->applyAll == 'true'){
            $product = Product::where('parent_id',$request->productId)->get();
            foreach($product as $p){
                $commission = Commission::where('partner_id',$request->partnerId)->where('product_id',$p->id)->first();
                if(!isset($commission)){
                    $commission = new Commission;
                }
                $commission->partner_id = $request->partnerId;
                $commission->product_id = $p->id;
                $commission->type = $request->commissionType;
                $commission->commission_fixed = $request->fixedCommission;
                $commission->commission_based = $request->commissionBased;
                $commission->updated_by = auth()->user()->username; 
                $commission->save();   
            }            
        }else{
            $commission = Commission::where('partner_id',$request->partnerId)->where('product_id',$request->productId)->first();
            if(!isset($commission)){
                $commission = new Commission;
            }
            $commission->partner_id = $request->partnerId;
            $commission->product_id = $request->productId;
            $commission->type = $request->commissionType;
            $commission->commission_fixed = $request->fixedCommission;
            $commission->commission_based = $request->commissionBased;
            $commission->updated_by = auth()->user()->username; 
            $commission->save();            
        }


    }


    public function agents($id){
        $partner_id = auth()->user()->reference_id;
        $partner_access = Partner::get_partners_access($partner_id);
        //dd($partner_access);
        if ($partner_access==""){$partner_access=$id;}

        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        }
        //dd($partner_info);
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];

        $access = session('all_user_access');
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        if (strpos($user_access, 'edit') !== false){
             $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
             $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $partner_access = Partner::get_partners_access($id);

        $user_type_id = User::where('reference_id',$id)->first();
        $partners_pt = Partner::find($partner_id);
        //create partner type access based on user type
        $all_user_access = Access::generateAllUserAccess($user_type_id->user_type_id); 
        if (!isset($partners_pt)) {
            $partner_type_id = -1;
        } else {
            $partner_type_id = $partners_pt->partner_type_id;    
        }
        $partner_type_access="";
        $user_types = UserType::where('create_by','SYSTEM')->where('status','A')->get();
        foreach($user_types as $user_type){
            if (isset($all_user_access[strtolower($user_type->description)])){
                if(strpos($all_user_access[strtolower($user_type->description)], 'view') !== false){
                    $pt_id = PartnerType::where('name',$user_type->description)->get();
                    if ($pt_id[0]->id  != $partner_type_id) $partner_type_access = $partner_type_access .  $pt_id[0]->id . ",";
                }        
            }
        }

        if (strlen($partner_type_access)>0) $partner_type_access = substr($partner_type_access, 0, strlen($partner_type_access) - 1); 
        //dd(session('all_user_access'));

        $partner_types = PartnerType::get_partner_types($partner_type_access);
        $active_partner_tab="";
        $partner_details=array();
        foreach($partner_types as $partner_type){
            $active_partner_tab = $partner_type->id.$partner_type->name;
            $custom_partners = array();
            $partners = Partner::get_partners($partner_access,$partner_type->id,$partner_id, -1, -1); 
            foreach($partners as $partner){
                $upline_partner_text = array();
                $upline_partner_access = Partner::get_upline_partners_access($partner->partner_id);   
                if ($upline_partner_access != ""){
                    $upline_partner_text = Partner::get_upline_partner_info($upline_partner_access,true);
                }

                $custom_partners[] = array(
                        'partner_type' => $partner->partner_type,
                        'dba'  => $partner->dba,
                        'partner_id'  => $partner->partner_id,
                        'company_name'  => $partner->company_name,
                        'email'  => $partner->email,
                        'phone1'  => $partner->phone1,
                        'phone2'  => $partner->phone2,
                        'state' => $partner->state,
                        'country_name'  => $partner->country_name,
                        'country_code'  => $partner->country_code,
                        'first_name'  => $partner->first_name,
                        'last_name'  => $partner->last_name,
                        'upline_partners'  => $upline_partner_text,
                    );

            }
            $partner_details[] = array(
                'id' => $partner_type->id,
                'name' =>  $partner_type->name,
                'display_name' =>  $partner_type->display_name,
                'partner_details' => $custom_partners,

            );
        }

        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        $is_admin = true;
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false) $is_admin = false;
        return view("partners.details.agents",compact('partner_info','partner_details','id','partner_types','active_partner_tab','isInternal','is_admin'));
    }

    public function merchants($id){
        $partner_id = auth()->user()->reference_id;
        $partner_access = Partner::get_partners_access($partner_id);
        if ($partner_access==""){$partner_access=$id;}

        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];
        $access = session('all_user_access');
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        if (strpos($user_access, 'edit') !== false){
             $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
             $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        
        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        $merchants = Partner::get_merchants_by_product_access($id);
        return view("partners.details.merchants",compact('id','partner_info','merchants','partner_info','isInternal'));
    }

    public function users($id){
        $partner_id = auth()->user()->reference_id;
        $partner_access = Partner::get_partners_access($partner_id);
        if ($partner_access==""){$partner_access=$id;}

        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];
        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        return view("partners.details.users",compact('id','partner_info','isInternal'));
    }

    public function user_data($id,Datatables $datatables)
    {
        $access = session('all_user_access');
        $users = User::getAllUsers($id);    
        $new_users = array();
        foreach($users as $user){
            $departments = User::getDepartmentsByID($user->user_type_id);
            $department_text="";
            foreach($departments as $department => $value){
                $department_text .= $value->description .'<br>';     
            }
            if (strlen($department_text)>4){
                 $department_text = substr($department_text, 0, strlen($department_text)-4);  
            }
            $new_users[] =array(
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email_address,
                'departments' =>  $department_text,
                'status' => $user->status_text,
                'country' => $user->country,
                'company' => $user->company,
                'is_online' => $user->is_online,
            );
        }
        return $datatables->collection($new_users)
                          ->editColumn('first_name', function ($user) {
                              return '<a>' . $user['first_name'] . '</a>';

                          })
                          ->addColumn('action', function ($user) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $reset="";
                                $view='<a class="btn btn-default btn-sm" href="/admin/users/'.$user['id'].'">View</a>';
                                $message="'Delete this User?'";
                                $offline = "";
                                if(strpos($access['users'], 'edit') !== false) {
                                   $edit = '<a href="/admin/users/'.$user['id'].'/edit" class="btn btn-primary btn-sm">Edit</a>';
                                   /**
                                    * Remove view button when user has edit access
                                    */
                                    $view="";
                                }
                                if(strpos($access['users'], 'delete') !== false) {
                                   $delete = '<a href="/admin/users/'.$user['id'].'/cancel" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('.$message.')">Delete</a>';
                                }
                                if(strpos($access['users'], 'reset') !== false) {
                                   $message="'Reset password?'";
                                   $reset = '<a href="/admin/users/'.$user['id'].'/reset" class="btn btn-success btn-sm" 
                                   onclick="return confirm('.$message.')">Reset</a>';
                                }
                                if(strpos($access['users'], 'set as offline') !== false && $user['is_online'] == 1) {
                                    $offline = '<a href="/admin/users/'.$user['id'].'/offline" class="btn btn-warning btn-sm">Set as Offline</a>';
                                }
                                return $view.' '.$edit.' '.$delete.' '.$reset.' '.$offline;
                          })
                          ->rawColumns(['first_name', 'action','departments'])
                          ->make(true);
    }


    public function viewTickets($id){
        $partner_id = auth()->user()->reference_id;
        $partner_access = Partner::get_partners_access($partner_id);
        if ($partner_access==""){$partner_access=$id;}

        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $has_admin_access =0;
        $access = session('all_user_access');
        if (isset($all_user_access['ticketing'])){
            if(strpos($all_user_access['ticketing'], 'admin access') !== false){
                $has_admin_access =1;
            }        
        }
        //dd($tickets);
        if ($has_admin_access==1){
            $ticket_filters = TicketFilter::where('status','A')->orderBy('sequence','asc')->get();               
        } else {
            $ticket_filters = TicketFilter::where('status','A')->where('is_admin',0)->orderBy('sequence','asc')->get();          
        }
        $partner_info = $partner_info[0];
        $access = session('all_user_access');
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        if (strpos($user_access, 'edit') !== false){
             $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
             $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        return view("partners.details.viewTickets",compact('id','partner_info','tickets','ticket_filters','partner_info'));
    }

    public function refreshTicketList($filter, $id){
        $partner_access = Partner::get_partners_access($id);
        $params = array(
            'code' => $filter,
            'add_query' => " and h.partner_id IN ({$partner_access})"
        );
        $tickets = TicketHeader::get_ticket_list($params);
        $list = array(); 
        if (count($tickets) > 0){
            foreach ($tickets as $ticket) {
                $assignee="";
                foreach ($ticket->users as $ticket_user) {
                    $assignee .= $ticket_user->name . '<br>';
                    $list[] = array(
                        $ticket->subject,
                        $ticket->requestor,
                        $ticket->merchant,
                        $ticket->product_name,
                        $ticket->department_name,
                        $assignee,
                        $ticket->ticket_status,
                        $ticket->ticket_priority,
                    );    
                }
            }
        }
        return $list;
    }

    public function billing($id){
        $partner_id = isset(auth()->user()->reference_id) ? auth()->user()->reference_id : -1;
        $partner_access = Partner::get_partners_access($partner_id);
        if ($partner_access==""){$partner_access=$id;}

        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,3,4,5,6,7,8",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];
        $access = session('all_user_access');
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        if (strpos($user_access, 'edit') !== false){
             $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
             $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        
        $isInternal = session('is_internal');
         
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
        return view("partners.details.billing",compact('id','partner_info','payment_types','payment_methods','isInternal'));
    }

    public function getStateByCountry($country)
    {
        $states = State::where('country',$country)->orderBy('name','asc')->get();
        $country = Country::where('iso_code_2',$country)->get();
        return response()->json(array(
                'states' => $states,
                'country' => $country
            ));
    }

    public function getUplineListByPartnerTypeId($id)
    {
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
        $upline = Partner::get_downline_partner($partner_id,$partner_access,$id);
        return response()->json($upline);
    }

    public function validateField($table, $field, $value, $id, $includeStatus, $prefix)
    {
        //dd($table. $field. $value. $id. $includeStatus);
        $includeStatus = $includeStatus === 'true'? true: false;
        $prefix = $prefix === 'empty'? '' : $prefix;
        $alreadyExist = Access::checkIfProfileExist($table, $field, $value, $id, $includeStatus, $prefix);
        return response()->json($alreadyExist);
    }

    public function getPartnersData()
    {
        $access = session('all_user_access');
        $partners=array();

        $partner_id = auth()->user()->reference_id;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $is_admin = true;
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($partner_id); // auth()->user()->reference_id
            $is_admin = false;
        }

        if ($partner_access==""){$partner_access=$partner_id;}   

        $partner_types = PartnerType::get_partner_types(session('partner_type_access'));
        $out_partners = array();
        //dd($partner_types);
        foreach($partner_types as $partner_type){
            if (isset($access[strtolower($partner_type->name)])){
                if(strpos($access[strtolower($partner_type->name)], 'view') !== false){
                    $partners = Partner::get_partners($partner_access,$partner_type->id,$partner_id, -1, -1); 
                    $draftPartners = DraftPartner::get_draft_partners($partner_access,$partner_type->id,$partner_id, -1, -1);
                    $partners = array_merge($partners, $draftPartners);
                    foreach ($partners as $partner) {
                        $partner_company_name = $partner->company_name . ' - ' . '<a href="/partners/details/profile/'.$partner->partner_id.'/profileCompanyInfo">' . $partner->partner_id_reference . '</a>';
                        // $partner_type_desc='<a href="/partners/details/profile/'.$partner->partner_id.'/profileCompanyInfo"> <button type="button" class="btn btn-info btn-sm">View</button> </a>';
                        $upline_partners = "";
                        $unverified="";
                        $verify_mobile = optional(Country::where('country_calling_code',$partner->country_code)->first())->validate_number ?? 0;
                        if($verify_mobile==1)
                        {
                            if ($partner->status != 'D') {
                                if ($partner->is_verified_email==0 || $partner->is_verified_mobile==0) {
                                    $unverified = ' <span class="badge badge-danger">unverified</span>';
                                }
                            }
                        }
                        $status="";
                        if($partner->status == 'I'){
                            $status = ' <span class="badge badge-danger">inactive</span>';
                        }
                        if($partner->status == 'T'){
                            $status = ' <span class="badge badge-danger">terminated</span>';
                        }
                        $view = "";
                        if ($partner->status == 'D') {
                            // $status = ' <span class="badge badge-warning">draft</span>';
                            $view = '<button class="btn btn-danger btn-sm" onclick="deleteDraftApplicant(' . $partner->partner_id . ')" title="Delete"><i class="fa fa-trash"></i></button>&nbsp;';
                            $status = '<a href="/drafts/draftPartners/' . $partner->partner_id . '/' . $partner->partner_type_id . '/edit">Incomplete Partner Application</a>';
                        }
                        $phone = "";
                        if ($partner->phone1) {
                            $phone = $partner->country_code.$partner->phone1;
                        }

                        if ($partner_type->id == 7)
                        {
                            $out_partners[$partner_type->id][] = array(
                                
                                $view . $partner_company_name . $status,
                                $partner->first_name.' '.$partner->last_name.$unverified.'<label style="display:none;>'.str_replace('-', '', $partner->country_code.$partner->phone1).'</label>',
                                $phone,
                                $partner->email,
                                $partner->state,
                                // $partner_type_desc,
                            );
                        } else {
                            foreach ($partner->upline_partners as $row3) {
                                $upline_partners.= $row3->company_name. ' - ' . '<a href="/partners/details/profile/'.$row3->id.'/profileCompanyInfo">' . $row3->merchant_id. '</a><br>';    
                            }
                            if($is_admin){
                                $out_partners[$partner_type->id][] = array(
                                    
                                    $upline_partners,
                                    $view . $partner_company_name.$status,
                                    $partner->first_name.' '.$partner->last_name.$unverified.'<label style="display:none;>'.str_replace('-', '', $partner->country_code.$partner->phone1).'</label>',
                                    $phone,
                                    $partner->email,
                                    $partner->state,
                                    // $partner_type_desc,
                                );                                  
                            }else{
                                $out_partners[$partner_type->id][] = array(
                                
                                $view . $partner_company_name.$status,
                                $partner->first_name.' '.$partner->last_name.$unverified.'<label style="display:none;>'.str_replace('-', '', $partner->country_code.$partner->phone1).'</label>',
                                $phone,
                                $partner->email,
                                $partner->state,
                                // $partner_type_desc,
                            );  
                            }
   
                        } 
                    }
                              
                }        
            }
        }
        return response()->json($out_partners);
       
    }

    public function profileCompanyInfo($id){
        $partner_id = auth()->user()->reference_id;
        $is_original_user=0;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false) $partner_access = Partner::get_partners_access($partner_id); 
        if ($partner_access=="") $partner_access=$id;
        //dd($partner_id.'-'.$id);
        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7");
            $is_original_user=1;
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7",$partner_access);    
        }

        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $partner_info = $partner_info[0];
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        $canEditStatus = false;
        if (strpos($user_access, 'edit') !== false){
            $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
            $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        if (strpos($user_access, 'edit partner status') !== false) {
            $canEditStatus = true;
        }

        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }
        
        $upline_partner_type_access = PartnerType::where('id',$partner_info->partner_type_id)->first();
        $uplines = Partner::get_upline_partner($id,$partner_access,$upline_partner_type_access->upline);
        $ownerships = Ownership::where('status','A')->orderBy('name','asc')->get();
        $countries = Country::where('status','A')->where('display_on_partner', 1)->/* orderBy('name','asc')-> */get();
        $documents = Document::where('status','A')->orderBy('name','asc')->get();
        $usCities = UsZipCode::select('city')->orderBy('city')->distinct()->get();
        $phCities = PhZipCode::select('city')->orderBy('city')->distinct()->get();
        $cnCities = CnZipCode::select('city')->orderBy('city')->distinct()->get();
        $businessTypeGroups = Cache::get('business_types');

        return view("partners.details.profile.companyInfo",compact('ownerships','countries','documents',
            'partner_info','id','is_original_user','uplines','canEditStatus','isInternal','businessTypeGroups',
            'usCities','phCities','cnCities'));
    }

    public function companyInfoUpdate(Request $request, $id)
    {   
        DB::transaction(function() use ($request, $id){
            
            //Partners Table
            $partner = Partner::find($id);
            if (isset($request->txtUplineId))
            {
                $partner->parent_id = $request->txtUplineId == null ? auth()->user()->reference_id : $request->txtUplineId;      
            }
            if ($request->txtPartnerType != '1') {
                $partner->credit_card_reference_id = $request->txtCreditCardReference;
            } else {
                /* $unpaid_switch = $request->txtTogBtnUnpaid == "on" ? 1 : 0;
                $paid_switch = $request->txtTogBtnPaid == "on" ? 1 : 0;
                $smtp_switch = $request->txtTogBtnSMTP == "on" ? 1 : 0; */

                $partner->social_security_id = $request->txtSocialSecurityNumber;
                $partner->tax_id_number = $request->txtTaxIDNumber;
                $partner->bank_name = $request->txtBankName;
                $partner->bank_routing_no = $request->txtBankRouting;
                $partner->bank_dda = $request->txtBankDDA;
                $partner->bank_address = $request->txtBankAddress;
                /* $partner->email_unpaid_invoice = $unpaid_switch;
                $partner->email_paid_invoice = $paid_switch;
                $partner->smtp_settings = $smtp_switch; */
                $partner->email_notifier = $request->txtEmailNotifier;
                $partner->billing_cycle = $request->txtBillingCycle;
                $partner->billing_month = $request->txtBillingMonth;
                $partner->billing_day = $request->txtBillingDay;
            }
            if (isset($request->txtPartnerStatus))
            {
                $partner->status = $request->txtPartnerStatus;
            }
            $partner->update_by = auth()->user()->username; 

            $partner->tax_id_number = $request->txtTaxIDNumber;
            $partner->front_end_mid = $request->txtFrontEndMID;
            $partner->back_end_mid = $request->txtBackEndMID;
            $partner->reporting_mid = $request->txtReportingMID;
            $partner->pricing_type = $request->txtPricingType;

            $partner->save();

            $partner->company_id =  Partner::get_upline_company($id);   
            $partner->save();

            $user = User::where('reference_id',$id)->where('is_original_partner',1)->first();
            $user->company_id =  Partner::get_upline_company($id);   
            $user->save();
            
            //PartnerCompany
            $partnerCompany = PartnerCompany::where('partner_id',$id)->first();
            if ($request->txtPartnerType != '1') {
                $country = Country::where('name',$request->txtCountry)->first();
                $partnerCompany->company_name = $request->txtCompanyName;
                $partnerCompany->dba = $request->txtDBA;
                $partnerCompany->business_date = $request->txtBusinessDate;
                $partnerCompany->website = $request->txtWebsite;
                $partnerCompany->country = $request->txtCountry;
                $partnerCompany->address1 = $request->txtBusinessAddress1;
                $partnerCompany->address2 = $request->txtBusinessAddress2;
                $partnerCompany->city = isset($request->txtCity) ? $request->txtCity : $partnerCompany->city;
                $partnerCompany->state = isset($request->txtState) ? $request->txtState : $partnerCompany->state;
                $partnerCompany->zip = $request->txtBusinessZip;
                $partnerCompany->phone1 = $request->txtBusinessPhone1;
                $partnerCompany->phone2 = $request->txtBusinessPhone2;
                $partnerCompany->extension = $request->txtExtension1;
                $partnerCompany->extension_2 = $request->txtExtension2;
                $partnerCompany->extension_3 = $request->txtExtension3;
                $partnerCompany->fax = $request->txtFax;
                $partnerCompany->mobile_number = $request->txtContactMobile1_1;
                $partnerCompany->email = $request->txtEmail;
                $partnerCompany->ownership = $request->txtOwnership;
                $partnerCompany->ssn = $request->txtSSN;
            } else {
                $country = Country::where('name',$request->txtCountryAgent)->first();
                $partnerCompany->company_name = $request->txtBusinessName; //$request->txtLegalBusinessName;
                $partnerCompany->business_name = $request->txtLegalBusinessName; //$request->txtBusinessName;
                $partnerCompany->address1 = $request->txtAddressAgent;
                $partnerCompany->country = $request->txtCountryAgent;
                $partnerCompany->state = isset($request->txtStateAgent) ? $request->txtStateAgent : $partnerCompany->state;
                $partnerCompany->city = isset($request->txtCityAgent) ? $request->txtCityAgent : $partnerCompany->city;
                $partnerCompany->zip = $request->txtZipAgent;
                $partnerCompany->phone1 = $request->txtPhoneNumber;
                $partnerCompany->email = $request->txtEmailAgent;
            }
            $partnerCompany->country_code = $country->country_calling_code;
            $partnerCompany->update_by = auth()->user()->username;
            $partnerCompany->save();


            //PartnerMailingAddress
            $partnerMailingAddress = PartnerMailingAddress::where('partner_id',$id)->first();
            if(!isset($partnerMailingAddress)){
                 $partnerMailingAddress = New PartnerMailingAddress;
                 $partnerMailingAddress->create_by = auth()->user()->username;
            }
            $partnerMailingAddress->partner_id = $id;
            if ($request->txtPartnerType != '1') {
                if($request->chkSameAsBusiness){
                    $country = Country::where('name',$request->txtCountry)->first();
                    $partnerMailingAddress->country = $request->txtCountry;
                    $partnerMailingAddress->address = $request->txtBusinessAddress1;
                    $partnerMailingAddress->address2 = $request->txtBusinessAddress2;
                    $partnerMailingAddress->city = isset($request->txtCity) ? $request->txtCity : $partnerMailingAddress->city;
                    $partnerMailingAddress->state = isset($request->txtState) ? $request->txtState : $partnerMailingAddress->state;
                    $partnerMailingAddress->zip = $request->txtBusinessZip;
                } else {
                    $country = Country::where('name',$request->txtMailingCountry)->first();
                    $partnerMailingAddress->country = $request->txtMailingCountry;
                    $partnerMailingAddress->address = isset($request->txtMailingAddress1) ? $request->txtMailingAddress1 : '';
                    $partnerMailingAddress->address2 = $request->txtMailingAddress2;
                    $partnerMailingAddress->city = isset($request->txtMailingCity) ? $request->txtMailingCity : $partnerMailingAddress->city;
                    $partnerMailingAddress->state = isset($request->txtMailingState) ? $request->txtMailingState : $partnerMailingAddress->state;
                    $partnerMailingAddress->zip = $request->txtMailingZip;
                }
            } else {
                $country = Country::where('name',$request->txtCountryAgent)->first();
                $partnerMailingAddress->country = $request->txtCountryAgent;
                $partnerMailingAddress->country_code = $country->country_calling_code;
                $partnerMailingAddress->address = $request->txtAddressAgent;
                $partnerMailingAddress->city = isset($request->txtStateAgent) ? $request->txtStateAgent : $partnerMailingAddress->state; 
                $partnerMailingAddress->state = isset($request->txtCityAgent) ? $request->txtCityAgent : $partnerMailingAddress->city;
                $partnerMailingAddress->zip = $request->txtZipAgent;
            }
            $partnerMailingAddress->country_code =$country->country_calling_code;
            $partnerMailingAddress->update_by = auth()->user()->username;
            $partnerMailingAddress->save();

            //PartnerBillingAddress
            $partnerBillingAddress = PartnerBillingAddress::where('partner_id',$id)->first();

            if (!isset($partnerBillingAddress)) {
                $partnerBillingAddress = new PartnerBillingAddress;
                $partnerBillingAddress->create_by = auth()->user()->username;
            }

            $partnerBillingAddress->partner_id = $id;

            if ($request->txtPartnerType != '1') {
                if ($request->chkSameAsBusinessBilling) {
                    $country = Country::where('name',$request->txtCountry)->first();
                    $partnerBillingAddress->country = $request->txtCountry;
                    $partnerBillingAddress->address = $request->txtBusinessAddress1;
                    $partnerBillingAddress->address2 = $request->txtBusinessAddress2;
                    $partnerBillingAddress->city = isset($request->txtCity) ? $request->txtCity : $partnerBillingAddress->city;
                    $partnerBillingAddress->state = isset($request->txtState) ? $request->txtState : $partnerBillingAddress->state;
                    $partnerBillingAddress->zip = $request->txtBusinessZip;
                } else {
                    $country = Country::where('name',$request->txtBillingCountry)->first();
                    $partnerBillingAddress->country = $request->txtBillingCountry;
                    $partnerBillingAddress->address = isset($request->txtBillingAddress1) ? $request->txtBillingAddress1 : '';
                    $partnerBillingAddress->address2 = $request->txtBillingAddress2;
                    $partnerBillingAddress->city = isset($request->txtBillingCity) ? $request->txtBillingCity : $partnerBillingAddress->city;
                    $partnerBillingAddress->state = isset($request->txtBillingState) ? $request->txtBillingState : $partnerBillingAddress->state;
                    $partnerBillingAddress->zip = $request->txtBillingZip;
                }
            } else {
                $country = Country::where('name',$request->txtCountryAgent)->first();
                $partnerBillingAddress->country = $request->txtCountryAgent;
                $partnerBillingAddress->country_code = $country->country_calling_code;
                $partnerBillingAddress->address = $request->txtAddressAgent;
                $partnerBillingAddress->city = isset($request->txtCityAgent) ? $request->txtCityAgent : $partnerBillingAddress->city;
                $partnerBillingAddress->state = isset($request->txtStateAgent) ? $request->txtStateAgent : $partnerBillingAddress->state;
                $partnerBillingAddress->zip = $request->txtZipAgent;
            }
            $partnerBillingAddress->country_code = $country->country_calling_code;
            $partnerBillingAddress->update_by = auth()->user()->username;
            $partnerBillingAddress->save();

            $user = User::where('reference_id',$id)->where('status','A')->first();
            if(isset($user)){
                if ($request->txtPartnerType != '1') {
                    $user->email_address = $request->txtEmail;
                    $user->country = $request->txtCountry;
                } else {
                    $user->email_address = $request->txtEmailAgent;
                    $user->country = $request->txtCountryAgent;
                }

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
                
                $user->country_code = $country->country_calling_code;
                $user->save();
            }

        });

        return redirect('/partners/details/profile/'.$id.'/profileCompanyInfo')->with('success','Company profile updated');
    }

    public function profileContactList($id){
        $partner_id = auth()->user()->reference_id;
        $is_original_user=0;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false) $partner_access = Partner::get_partners_access(-1); 
        if ($partner_access=="") $partner_access=$id;
        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7");
            $is_original_user=1;
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7",$partner_access);    
        }

        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        if (strpos($user_access, 'edit') !== false){
             $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
             $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        $partner_contacts = PartnerContact::where('partner_id',$id)->get();
        return view('partners.details.profile.contactList',compact('partner_info','partner_contacts','id','isInternal'));
    }

    public function profileContactListEdit($id, $contact_id){
        
        $partner_id = auth()->user()->reference_id;
        $is_original_user=0;
        $is_new = 0;
        if ($contact_id==-1) $is_new = 1;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false) $partner_access = Partner::get_partners_access(-1); 
        if ($partner_access=="") $partner_access=$id;
        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7");
            $is_original_user=1;
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7",$partner_access);    
        }

        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        if (strpos($user_access, 'edit') !== false){
             $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
             $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        $contact_info = PartnerContact::get_contact_info($contact_id,$id,$partner_access);
        $countries = Country::where('status','A')->where('display_on_partner', 1)/* ->orderBy('name','asc') */->get();
        $usCities = UsZipCode::select('city')->orderBy('city')->distinct()->get();
        $phCities = PhZipCode::select('city')->orderBy('city')->distinct()->get();
        $cnCities = CnZipCode::select('city')->orderBy('city')->distinct()->get();

        return view('partners.details.profile.contactListEdit',compact('partner_info','id',
            'countries','contact_info','is_new','contact_id','isInternal',
            'usCities','phCities','cnCities'));
    }

    public function profileContactListUpdate(Request $request, $id, $contactId){
        //dd($request);
        //PartnerContact
        /* $this->validate($request,[
                'email_address' => 'required|string|email|unique:partner_contacts,email,'.$contactId,
                'mobile_number' => 'required|string|max:255|unique:partner_contacts,mobile_number,'.$contactId,
        ]); */
        $country = Country::where('name',$request->txtContactCountry1)->first();
        $dob = $request->txtContactDOB1 != null ? date("Y-m-d", strtotime($request->txtContactDOB1)) : null;
        $partnerContact = PartnerContact::find($contactId);
        $partnerContact->first_name = $request->txtContactFirstName1;
        $partnerContact->middle_name = $request->txtContactMiddleInitial1;
        $partnerContact->last_name = $request->txtContactLastName1;
        $partnerContact->position = $request->txtContactTitle1;
        $partnerContact->country = $request->txtContactCountry1;
        $partnerContact->country_code =$country->country_calling_code;
        $partnerContact->address1 =$request->txtContactHomeAddress1_1;
        $partnerContact->address2 = $request->txtContactHomeAddress1_2;
        $partnerContact->city = $request->txtContactCity1 == $partnerContact->city ? $partnerContact->city : $request->txtContactCity1;
        $partnerContact->state = $request->txtContactState1 == $partnerContact->state ? $partnerContact->state : $request->txtContactState1;
        $partnerContact->zip =  $request->txtContactZip1;
        $partnerContact->other_number = $request->txtContactPhone1_1;
        $partnerContact->other_number_2 = $request->txtContactPhone1_2;
        $partnerContact->fax = $request->txtContactFax1;
        $partnerContact->mobile_number = $request->mobile_number;
        $partnerContact->mobile_number_2 = $request->txtContactMobile1_2;
        $partnerContact->email = $request->email_address;
        $partnerContact->ownership_percentage = $request->txtOwnershipPercentage1;
        if($request->txtContactSSN1 != ""){
            $partnerContact->ssn = $request->txtContactSSN1;
        }
        $partnerContact->dob = $dob;
        $partnerContact->mobile_number_2 = $request->txtContactMobile1_2;
        $partnerContact->save();
        
        
        if($partnerContact->is_original_contact==1)
        {
            if (isset($partnerContact->partner_id)) {
                $user = User::where('reference_id',$partnerContact->partner_id)->where('is_original_partner',1)->first();
                if(isset($user)){
                    $user->first_name        = $request->txtContactFirstName1;
                    $user->last_name         = $request->txtContactLastName1;
                    $user->ssn               = $request->txtContactSSN1;
                    // $user->country           = $request->txtContactCountry1;
                    $user->dob               = $dob;
                    $user->mobile_number     = $request->mobile_number;
                    $user->fax               = $request->txtContactFax1;
                    $user->email_address     = $request->email_address;

                    $user->home_address1     = $request->txtContactHomeAddress1_1;
                    $user->home_address2     = $request->txtContactHomeAddress1_2;

                    $user->home_city         = $request->txtContactCity1 == $user->home_city ? $user->home_city : $request->txtContactCity1;
                    $user->home_state        = $request->txtContactState1 == $user->home_state ? $user->home_state : $request->txtContactState1;
                    $user->home_zip          = $request->txtContactZip1;
                    $user->home_country      = $request->txtContactCountry1;
                    $user->save();

                    return redirect('/partners/details/profile/'.$id.'/profileContactList')->with([
                        'success','Contact updated',
                        'newUsername' => $user->username,
                        'newUserId' => $user->id,
                        'newEmail' => $user->email_address,
                        'newFullName' => $user->first_name . ' ' . $user->last_name,
                        'newImg' => $user->image,
                    ]);
                }
            }
        }


        return redirect('/partners/details/profile/'.$id.'/profileContactList')->with('success','Contact Updated');
    }

    public function profileContactListCreate($id){
        
        $partner_id = auth()->user()->reference_id;
        $is_original_user=0;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false) $partner_access = Partner::get_partners_access(-1); 
        if ($partner_access=="") $partner_access=$id;
        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7");
            $is_original_user=1;
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7",$partner_access);    
        }

        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        if (strpos($user_access, 'edit') !== false){
             $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
             $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        $countries = Country::where('status','A')->where('display_on_partner', 1)/* ->orderBy('name','asc') */->get();
        return view('partners.details.profile.contactListCreate',compact('partner_info','id','countries','isInternal'));
    }

    public function profileContactListStore(Request $request, $id){
        //dd($request);
        //PartnerContact
        $country = Country::where('name',$request->txtContactCountry1)->first();
        $dob = date("Y-m-d", strtotime($request->txtContactDOB1));
        $partnerContact = new PartnerContact;
        $partnerContact->partner_id = $id;
        $partnerContact->first_name = $request->txtContactFirstName1;
        $partnerContact->middle_name = $request->txtContactMiddleInitial1;
        $partnerContact->last_name = $request->txtContactLastName1;
        $partnerContact->position = $request->txtContactTitle1;
        $partnerContact->country = $request->txtContactCountry1;
        $partnerContact->country_code =$country->country_calling_code;
        $partnerContact->address1 =$request->txtContactHomeAddress1_1;
        $partnerContact->address2 = $request->txtContactHomeAddress1_2;
        $partnerContact->city = $request->txtContactCity1;
        $partnerContact->state = $request->txtContactState1;
        $partnerContact->zip =  $request->txtContactZip1;
        $partnerContact->other_number = $request->txtContactPhone1_1;
        $partnerContact->other_number_2 = $request->txtContactPhone1_2;
        $partnerContact->fax = $request->txtContactFax1;
        $partnerContact->mobile_number = $request->txtContactMobile1_1;
        $partnerContact->mobile_number_2 = $request->txtContactMobile1_2;
        $partnerContact->email = $request->txtContactEmail1;
        $partnerContact->ownership_percentage = $request->txtOwnershipPercentage1;
        $partnerContact->ssn = $request->txtContactSSN1;
        $partnerContact->dob = $dob;
        $partnerContact->mobile_number_2 = $request->txtContactMobile1_2;
        $partnerContact->save();
        return redirect('/partners/details/profile/'.$id.'/profileContactList')->with('success','Contact Updated');
    }

    public function profileAttachments($id){
        $partner_id = auth()->user()->reference_id;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false) $partner_access = Partner::get_partners_access(-1); 
        if ($partner_access=="") $partner_access=$id;
        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7",$partner_access);    
        }
        $attachments = Partner::get_partner_attachment($id);
        //dd($attachments);
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        if (strpos($user_access, 'edit') !== false){
             $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
             $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        return view('partners.details.profile.attachments',compact('attachments','id','partner_info','isInternal'));
    }

    public function updateProduct($id,Request $request)
    {
        DB::transaction(function() use ($id,$request){

              $details = $request->txtDetail;
              $details = json_decode($details);
              $product_id = "";
              if($request->asTemplate == 0)
              {
                  $deletedRows = PartnerProduct::where('partner_id', $id)->delete();
                  foreach ($details as $detail) {
                    $partnerProduct = new PartnerProduct;
                    $partnerProduct->partner_id = $id;
                    $partnerProduct->product_id = $detail->product_id;
                    $partnerProduct->buy_rate = $detail->cost;
                    $partnerProduct->payment_frequency = $detail->frequency;
                    $partnerProduct->days_before_due_date = 0;
                    $partnerProduct->price_rule_type_id = 0;
                    $partnerProduct->mark_up_value = 0;
                    $partnerProduct->price_value_min = 0;
                    $partnerProduct->price_value_max = 0;
                    $partnerProduct->due_date = "";
                    $partnerProduct->status = 'A';
                    $partnerProduct->mark_up_type_id = ($detail->split_type == "First Buy Rate") ? 3 : 4;
                    $partnerProduct->split_type = $detail->split_type;
                    $partnerProduct->is_split_percentage = ($detail->split_percentage == "YES") ? 1 : 0;
                    $partnerProduct->other_buy_rate = $detail->second_buyrate;
                    $partnerProduct->downline_buy_rate = $detail->buyrate;
                    $partnerProduct->upline_percentage = $detail->upline_percent;
                    $partnerProduct->downline_percentage = $detail->downline_percent;
                    $partnerProduct->pricing_option = $detail->pricing_option;
                    $partnerProduct->price = $detail->price;
                    $partnerProduct->commission_type = $detail->commission_type;
                    $partnerProduct->commission_fixed = ($detail->commission_type == "fixed") ? $detail->commission : 0;
                    $partnerProduct->commission_based = ($detail->commission_type == "based") ? $detail->commission : "";
                    $partnerProduct->cost_multiplier = $detail->cost_multiplier;
                    $partnerProduct->cost_multiplier_value = $detail->cost_multiplier_value;
                    $partnerProduct->cost_multiplier_type = $detail->cost_multiplier_type;
                    $partnerProduct->srp = $detail->srp;
                    $partnerProduct->mrp = $detail->mrp;
                    $partnerProduct->bonus = $detail->bonus;
                    $partnerProduct->bonus_type = $detail->bonus_type;
                    $partnerProduct->bonus_amount = $detail->bonus_amount;

                    $partnerProduct->create_by = auth()->user()->username;
                    $partnerProduct->update_by = auth()->user()->username;
                    $partnerProduct->save();

                    $deletedRows = PartnerProductModule::where('partner_id', $id)->where('product_id',$detail->product_id)->delete();
                    $modules = json_decode($detail->product_module);
                    foreach ($modules as $m) {
                        $pm = new PartnerProductModule;
                        $pm->partner_id = $id;
                        $pm->product_id = $detail->product_id;
                        $pm->product_module_id = $m->id;
                        $pm->name = $m->name;
                        $pm->value = $m->value;
                        $pm->type = $m->type;
                        $pm->status = $m->status;
                        $pm->save();
                    }

                    $product_id = $product_id . $detail->product_id . ",";
                  }

                    if (strlen($product_id) > 0){
                        $product_id = substr($product_id, 0, strlen($product_id) - 1);    
                    }

                    $product_access = PartnerProductAccess::where('partner_id',$id)->first();
                    if(isset($product_access))
                    {
                         $product_access = PartnerProductAccess::find($product_access->id);
                         $product_access->partner_id = $id;
                         $product_access->product_access = $product_id;
                         $product_access->save();
                    }else{
                         $product_access = new PartnerProductAccess;
                         $product_access->partner_id = $id;
                         $product_access->product_access = $product_id;
                         $product_access->save();
                    }

              }else{
                  $productTemplate = new ProductTemplateHeader;
                  $productTemplate->template_partner_type_id = -1;
                  $company_id = Partner::get_top_upline_partner($id);
                  $productTemplate->partner_id = $company_id;
                  $productTemplate->name = $request->txtTemplateName;
                  $productTemplate->description = ($request->txtTemplateDescription == null) ? "" :  $request->txtTemplateDescription;
                  $productTemplate->status = 'A';
                  $productTemplate->product_type_id = 1;
                  $productTemplate->create_by = auth()->user()->username;
                  $productTemplate->update_by = auth()->user()->username;
                  $productTemplate->save();

                  foreach ($details as $detail) {
                    $productTemplateDetail = new ProductTemplateDetail;
                    $productTemplateDetail->template_id = $productTemplate->id;
                    $productTemplateDetail->product_id = $detail->product_id;
                    $productTemplateDetail->buy_rate = $detail->cost;
                    $productTemplateDetail->payment_frequency = $detail->frequency;
                    $productTemplateDetail->days_before_due_date = 0;
                    $productTemplateDetail->due_date = "";
                    $productTemplateDetail->status = 'A';
                    $productTemplateDetail->mark_up_type_id = ($detail->split_type == "First Buy Rate") ? 3 : 4;
                    $productTemplateDetail->split_type = $detail->split_type;
                    $productTemplateDetail->is_split_percentage = ($detail->split_percentage == "YES") ? 1 : 0;
                    $productTemplateDetail->other_buy_rate = $detail->second_buyrate;
                    $productTemplateDetail->downline_buy_rate = $detail->buyrate;
                    $productTemplateDetail->upline_percentage = $detail->upline_percent;
                    $productTemplateDetail->downline_percentage = $detail->downline_percent;
                    $productTemplateDetail->pricing_option = $detail->pricing_option;
                    $productTemplateDetail->price = $detail->price;
                    $productTemplateDetail->commission_type = $detail->commission_type;
                    $productTemplateDetail->commission_fixed = ($detail->commission_type == "fixed") ? $detail->commission : 0;
                    $productTemplateDetail->commission_based = ($detail->commission_type == "based") ? $detail->commission : "";
                    $productTemplateDetail->modules = $detail->product_module;
                    $productTemplateDetail->cost_multiplier = $detail->cost_multiplier;
                    $productTemplateDetail->cost_multiplier_value = $detail->cost_multiplier_value;
                    $productTemplateDetail->cost_multiplier_type = $detail->cost_multiplier_type;
                    $productTemplateDetail->srp = $detail->srp;
                    $productTemplateDetail->mrp = $detail->mrp;
                    $productTemplateDetail->bonus = $detail->bonus;
                    $productTemplateDetail->bonus_type = $detail->bonus_type;
                    $productTemplateDetail->bonus_amount = $detail->bonus_amount;
                    
                    $productTemplateDetail->create_by = auth()->user()->username;
                    $productTemplateDetail->update_by = auth()->user()->username;
                    $productTemplateDetail->save();
                  }
              }

        });
        return redirect('/partners/details/'.$id.'/products')->with('success','Commission and Rates updated');
    }

    public function getTemplate($id)
    {
        $details =  ProductTemplateDetail::where('template_id',$id)->get();
        foreach ($details as $d) {
            $d->product_name = $d->product->name;
            $d->main_product = $d->product->parent_id;
            $d->main_product_name = $d->product->mainproduct->name;
            $d->frequency_id = $d->frequency->id;

            $cost = $d->buy_rate;
 
            if($d->cost_multiplier == 1){
                if($d->cost_multiplier_type == 'percentage'){
                    $d->cost = $cost * ($d->cost_multiplier_value/100);
                }else{
                    $d->cost = $cost  * $d->cost_multiplier_value;
                }
            }else{
                $d->cost = $cost ;
            }
            $d->cost = number_format((float)$d->cost, 2, '.', '');
            $d->buy_rate = number_format((float)$d->buy_rate, 2, '.', '');
            $d->other_buy_rate = number_format((float)$d->other_buy_rate, 2, '.', '');
            $d->downline_buy_rate = number_format((float)$d->downline_buy_rate, 2, '.', '');
            $d->upline_percentage = number_format((float)$d->upline_percentage, 2, '.', '');
            $d->downline_percentage = number_format((float)$d->downline_percentage, 2, '.', '');
        }

        return $details;
    }

    public function updatePartnerAttachment(Request $request){
        $id = $request->txtDocumentPartnerId;
        DB::transaction(function() use ($id,$request){

            $thefile = File::get($request->file('fileUploadAttachment'));
            $fileNameWithExt = $request->file('fileUploadAttachment')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
            $extension = $request->file('fileUploadAttachment')->getClientOriginalExtension();
            $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
            Storage::disk('attachment')->put($filenameToStore,$thefile);

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
        return redirect('/partners/details/profile/'.$id.'/profileAttachments')->with('success','Attachments Updated');
    }


    public function profilePaymentGateway($id){

        $partner_id = auth()->user()->reference_id;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false) $partner_access = Partner::get_partners_access(-1); 
        if ($partner_access=="") $partner_access=$id;
        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];

        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        $payment_gateways = PartnerPaymentGateway::where('partner_id',$id)->where('status','A')->get();
        return view('partners.details.profile.paymentGateway',compact('payment_gateways','id','partner_info','isInternal'));
    }

    public function updatePartnerPaymentGateway($id,Request $request){
        $message="";
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
                $message = "Payment Gateway was successfully added";
            }else{
                $partnerContact = PartnerPaymentGateway::find($request->pgID);
                $partnerContact->name = $request->txtPGName;
                $partnerContact->key = $request->txtPGKey;
                $partnerContact->update_by = auth()->user()->username;
                $partnerContact->save();     
                $message = "Payment Gateway was successfully updated";           
            }

        });
        return redirect('/partners/details/profile/'.$id.'/profilePaymentGateway')->with('success',$message);
    }

    public function advancePartnersSearch(Datatables $dt,$type,$country,$state){
        // return session('all_user_access');
        $partner_access=-1;
        $id = auth()->user()->reference_id; 
        $partner_type = PartnerType::select(DB::raw('lower(name) as name'))->where('id',$type)->first();
        $search = " AND pc.state = '".$state."' AND pc.country = '".$country."'";

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access($id);      
        }
        
        if ($partner_access==""){$partner_access=$id;}
        $pt_id="";
        if (Access::hasPageAccess($partner_type->name,'view',true)){
            $result = Partner::get_partners($partner_access,$type,$id, -1, -1,"",$search);
            foreach ($result as $p) {
            $upline = '';
                foreach ($p->upline_partners as $u) {
                    $upline .= $u->first_name .' '. $u->last_name .' - '. '<a href="/partners/details/profile/'.$u->id.'/profileCompanyInfo">' . $u->merchant_id. '</a><br>';
                    $p->upline_partners = substr($upline,0,strlen($upline)-2);
                }
            }
        }
        if($type == 7){
            return $dt::of($result)
                                ->editColumn('company_name', function($result){
                                    $partner_type_desc='<a href="/partners/details/profile/'.$result->partner_id.'/profileCompanyInfo">'.$result->company_name.'</a>'.'<label style="display:none;>'.str_replace('-', '', $result->country_code.$result->phone1).'</label>';
                                    return $partner_type_desc;
                                })
                                ->editColumn('contact', function($result){
                                    $unverified="";
                                    $verify_mobile = optional(Country::where('country_calling_code',$result->country_code)->first())->validate_number ?? 0;
                                    if($verify_mobile==1)
                                    {
                                        if ($result->is_verified_email==0 || $result->is_verified_mobile==0){
                                            $unverified = $result->first_name.' '.$result->last_name .' <span class="badge badge-danger">unverified</span>';
                                        }
                                    }
                                    return $unverified;
                                })
                                ->editColumn('phone1', function($result){
                                    return $result->country_code.$result->phone1;
                                })
                                ->rawColumns(['company_name','contact','phone1'])
                                ->make(true);
        }else{
            return $dt::of($result)
                                ->editColumn('company_name', function($result){
                                    $partner_type_desc='<a href="/partners/details/profile/'.$result->partner_id.'/profileCompanyInfo">'.$result->company_name.'</a>'.'<label style="display:none;>'.str_replace('-', '', $result->country_code.$result->phone1).'</label>';
                                    return $partner_type_desc;
                                })
                                ->editColumn('partners', function($result){
                                    $upline = $result->upline_partners;
                                    return $upline;
                                })
                                ->editColumn('contact', function($result){
                                    $unverified="";
                                    $verify_mobile = optional(Country::where('country_calling_code',$result->country_code)->first())->validate_number ?? 0;
                                    if($verify_mobile==1)
                                    {
                                        if ($result->is_verified_email==0 || $result->is_verified_mobile==0){
                                            $unverified = $result->first_name.' '.$result->last_name .' <span class="badge badge-danger">unverified</span>';
                                        }
                                    }
                                    return $unverified;
                                })
                                ->editColumn('phone1', function($result){
                                    return $result->country_code.$result->phone1;
                                })
                                ->rawColumns(['company_name','partners','contact','phone1'])
                                ->make(true);            
        }

    }

     public function updatePartnerPaymentMethod($id,Request $request){
        $message="";
        
        DB::transaction(function() use ($id,$request){
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
            }

        });
        return redirect('/partners/details/billing/'.$id)->with('success',$message);
    }

    public function payment_method($id)
    {
        $payment_method  = PartnerPaymentInfo::find($id);
        return response()->json($payment_method);
    }

    public function cancel_payment_method($id, $partner_id)
    {
        $paymentMethod = PartnerPaymentInfo::find($id);
        $paymentMethod->status = 'I';
        $paymentMethod->update_by = auth()->user()->username;
        $paymentMethod->save();
        return redirect('/partners/details/billing/'.$partner_id)->with('success','Payment Method was successfully  deleted.');
    }

    public function loadPartnerTypes($id)
    { 
        $session_partner_type_access = explode(",",session('partner_type_access_view'));
        $partner_type_access = Access::get_table_field_by_value('partner_types','upline','id',$id); 
        $partner_type_access = explode(",",$partner_type_access);
        $partner_type_access_intersect=array_intersect($session_partner_type_access,$partner_type_access);
        
        // if (session('partner_type_id') != -1) array_push($partner_type_access_intersect, session('partner_type_id'));
        if (session('partner_type_id_not_parent') != -1) array_push($partner_type_access_intersect, session('partner_type_id_not_parent'));
        $partner_types = PartnerType::whereIn('id',$partner_type_access_intersect)->get();   
        $option = ""; 
        if (count($partner_types) > 0){
             foreach($partner_types as $partner_type){
                if ($partner_type->id == 7) {
                    $option = '<option value="' . $partner_type->id .  '">' . $partner_type->name . '</option> ' . $option;
                    continue;
                } 

                $option .= '<option value="' . $partner_type->id .  '">' . $partner_type->name . '</option> ';
            }
        }

        return response()->json($option);
    }

    public function merchantPurchase($id,Datatables $datatables){
        $partner_id = Partner::get_downline_partner_ids($id);
        $userType = session('user_type_desc');
        $query = Partner::where('partner_type_id',3)->where('status','A')->whereRaw('id in('.$partner_id.')')->get();

        return $datatables->collection($query)
                          ->editColumn('merchant', function ($data) {
                              return  '<a href="/merchants/details/'.$data->id.'/billing" >'.$data->partner_company->company_name.'</a>';
                          })
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

    public function resendEmailVerification($id){
        $partner = Partner::find($id);
        if(!isset($partner)){
            return Array('message' => 'Cannot find Partner Account');
        }

        $user = User::where('reference_id',$id)->first();
        if(!isset($user)){
            return Array('message' => 'Cannot find User Account');
        }

        $default_password = rand(1111111, 99999999);
        $default_encrypted_password = bcrypt($default_password);
        $user->password = $default_encrypted_password;
        $user->save();

        $data = array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'password' => $default_password,
            'email_address' => $user->email_address,
            'username' => $user->username,
        );

        Mail::send(['html'=>'mails.emailverification'],$data,function($message) use ($data){

            $message->to($data['email_address'],$data['first_name'].' '.$data['last_name']);
            $message->subject('[GoETU] Account Email Verification');
            $message->from('no-reply@goetu.com');
        });

        if (Mail::failures()) {
            return Array('message' => 'Failed sending email');
        }

        return Array('message' => 'Email verification sent!');
    }

    public function generate_order_list(Datatables $dt,$id,$startdate,$enddate){
        $date1 = explode(' ', $startdate);
        $startDate = $date1[2] . '-' . $date1[0] .'-'. $date1[1];
        $date2 = explode(' ', $enddate);
        $endDate = $date2[2] . '-' . $date2[0] .'-'. $date2[1];

        $partner_access = $partner_access = Partner::get_partners_access($id);

        $result = ProductOrder::getApplicationList($startDate,$endDate,$partner_access);

        return $dt::of($result)
            ->editColumn('', function($result){
                $workflow='<a target="_blank" href="/merchants/workflow/'.$result->partner_id.'/'.$result->order_id.'"><i class="fa fa-comment"></i> </a>';
                return $workflow;
            })
            ->editColumn('merchant_mid', function($result){
                $mid = $result->merchant_mid;
                return $mid;
            })
            ->editColumn('company_name', function($result){
                $company = $result->company_name;
                return $company;
            })
            ->editColumn('date', function($result){
                return $result->create_date;
            })
            ->editColumn('order_id', function($result){
                return $result->order_id;
            })
            ->editColumn('product', function($result){
                return $result->name;
            })
            ->editColumn('application_status', function($result){
                return $result->status;
            })
            ->editColumn('product_status', function($result){
                return $result->product_status;
            })
            ->editColumn('agent', function($result){
                return $result->product_status;
            })
            ->editColumn('view', function($result){
                return '<a target="_blank" href="/merchants/'.$result->order_id.'/order_preview" ><i class="fa fa-file-pdf-o"></i> </a>';
            })
            ->rawColumns(['','merchant_mid','company_name','date','order_id','product','application_status','product_status','agent','view'])
            ->make(true);
    }


    public function crossSellingAgent($id){

        $partner_id = auth()->user()->reference_id;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false) $partner_access = Partner::get_partners_access(-1); 
        if ($partner_access=="") $partner_access=$id;
        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7");
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7",$partner_access);    
        }
        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        $partner_info = $partner_info[0];


        $isInternal = session('is_internal');
        if (!session('is_internal')) {
            return redirect("/partners/details/profile/{$id}/profileOverview");
        }

        $agents = CrossSellingAgent::get_cross_selling_agents($id);
        return view('partners.details.crossselling',compact('agents','id','partner_info','isInternal'));
    }

    public function removeCrossSellingAgent($company_id,$agent_id){
        $agent = CrossSellingAgent::where('partner_id',$company_id)->where('agent_id',$agent_id)->first();
        if(!isset($agent)){
            $agent = new CrossSellingAgent;
        }

        $agent->partner_id = $company_id;
        $agent->agent_id = $agent_id;
        $agent->status = 'D';
        $agent->save();
        return Array('message' => 'Removed Agent as Cross Selling');
    }

    public function addCrossSellingAgent($company_id,$agent_id){
        $agent = CrossSellingAgent::where('partner_id',$company_id)->where('agent_id',$agent_id)->first();
        if(!isset($agent)){
            $agent = new CrossSellingAgent;
        }

        $agent->partner_id = $company_id;
        $agent->agent_id = $agent_id;
        $agent->status = 'A';
        $agent->save();
        return Array('message' => 'Agent Added as Cross Selling');

    }


    public function uploadfile(Request $request){
        $logs = array();
        if($request->hasFile('fileUploadCSV')){
            $extension = $request->file('fileUploadCSV')->getClientOriginalExtension();//File::extension($request->file->getClientOriginalExtension());
            if ($extension == "csv") {
                $path = $request->file('fileUploadCSV')->storeAs(
                            'partners', $request->file('fileUploadCSV').'.'.$extension
                        );
                $data = Excel::load($request->file('fileUploadCSV'), function($reader) {})->get();
                // return $data; // will not proceed importing data
                $import_id = Partner::max('import_number');
                $import_id = $import_id == '' ? 1 : $import_id + 1;
                if(!empty($data) && $data->count()){
                    //Prelim
                    foreach ($data as $key => $value) {
                        $skip = false;
                        if (!(strtolower($value->partner_type) == 'iso' || strtolower($value->partner_type)  == 'sub iso' || strtolower($value->partner_type)  == 'agent' || strtolower($value->partner_type)  == 'sub agent')) {
                            $logs[] = "Skipping ".$value->partner_type.", invalid value for partner type.";
                            $skip = true;
                        } else {

                            if(Access::hasPageAccess('iso','add',false) && strtolower($value->partner_type) == 'iso'){
                                $logs[] = "Skipping ".$value->dba." no access to create this partner type.";
                                $skip = true;
                            }

                            if(Access::hasPageAccess('sub iso','add',false) && strtolower($value->partner_type) == 'sub iso'){
                                $logs[] = "Skipping ".$value->dba." no access to create this partner type.";
                                $skip = true;
                            }

                            if(Access::hasPageAccess('agent','add',false) && strtolower($value->partner_type) == 'agent'){
                                $logs[] = "Skipping ".$value->dba." no access to create this partner type.";
                                $skip = true;
                            }

                            if(Access::hasPageAccess('sub agent','add',false) && strtolower($value->partner_type) == 'sub agent'){
                                $logs[] = "Skipping ".$value->dba." no access to create this partner type.";
                                $skip = true;
                            }

                            $partner_type = DB::table('partner_types')->select('id')->where('name',strtoupper($value->partner_type))->first();
                            if (!$partner_type->id) {
                                $logs[] = "Skipping ".$value->dba." due to invalid partner_type.";
                                $skip = true;
                            }

                            $lead_id = DB::table('partners')->select(DB::raw('count(id)+1 max_id'))->where('partner_type_id',$partner_type->id)->first();
                            $partner_type_description = DB::table('partner_types')->select('name')->where('id',$partner_type->id)->first();
                            $country = isset($value->country) ? "United States" : $value->country;

                            if (strtolower($value->partner_type) != 'company'){

                                if (strtolower($value->partner_type) == 'iso'){
                                    $upline = DB::table('partners')->select('id')->where('partner_id_reference',$value->upline)->whereIn('partner_type_id',Array(7))->first();
                                }
                                if (strtolower($value->partner_type) == 'sub iso'){
                                    $upline = DB::table('partners')->select('id')->where('partner_id_reference',$value->upline)->whereIn('partner_type_id',Array(4,7))->first();
                                }
                                if (strtolower($value->partner_type) == 'agent'){
                                    $upline = DB::table('partners')->select('id')->where('partner_id_reference',$value->upline)->whereIn('partner_type_id',Array(4,5,7))->first();
                                }
                                if (strtolower($value->partner_type) == 'sub agent'){
                                    $upline = DB::table('partners')->select('id')->where('partner_id_reference',$value->upline)->whereIn('partner_type_id',Array(1,4,5,7))->first();
                                }
                                if (!isset($upline->id)) {
                                    $logs[] = "Skipping ".$value->dba." due to invalid parent.";
                                    $skip = true;
                                }else{
                                    $uplineId = $upline->id;
                                }
                            }else{
                                $uplineId = -1;
                            }
                    

                            if (!isset($upline->id)) {
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

                            if($skip){goto skip;}

                            $insertPartnersData = new Partner;
                            $insertPartnersData->create_by                  = auth()->user()->username;
                            $insertPartnersData->update_by                  = auth()->user()->username;
                            $insertPartnersData->status                     = "A";
                            $insertPartnersData->partner_type_id            = $partner_type->id;
                            $insertPartnersData->original_partner_type_id   = $partner_type->id;
                            $insertPartnersData->parent_id                  = $uplineId;
                            $insertPartnersData->logo                       = "";
                            $insertPartnersData->partner_id_reference       = "";
                            $insertPartnersData->merchant_processor         = $value->current_processor != '' ? $value->current_processor : NULL;
                            $insertPartnersData->interested_products        = "";
                            $insertPartnersData->partner_status             = "New";
                            $insertPartnersData->import_number             = $import_id;

                            $insertPartnersData->tax_id_number        = $value->tax_id_number != '' ? $value->tax_id_number : NULL;
                            $insertPartnersData->front_end_mid        = $value->front_end_mid != '' ? $value->front_end_mid : NULL;     
                            $insertPartnersData->back_end_mid        = $value->back_end_mid != '' ? $value->back_end_mid : NULL;  
                            $insertPartnersData->reporting_mid        = $value->reporting_mid != '' ? $value->reporting_mid : NULL;   
                            $insertPartnersData->pricing_type        = $value->pricing_type != '' ? $value->pricing_type : NULL;                      

                            if (!$insertPartnersData->save()) {
                                $logs[] = "Unable to create partner."; 
                                goto skip;
                            }
                            $partner_id = $insertPartnersData->id;
                            $parent_id = $insertPartnersData->parent_id;

                            if(strtolower($value->partner_type) == 'sub iso'){
                                $partner_id_reference = 'SI' .(100000+$lead_id->max_id); 
                            }elseif(strtolower($value->partner_type) == 'sub agent'){
                                $partner_id_reference = 'SA' .(100000+$lead_id->max_id); 
                            }else{
                                $partner_id_reference = substr($partner_type_description->name,0,1) .(100000+$lead_id->max_id); 
                            }
                            $update_partners = Partner::find($partner_id);
                            $update_partners->partner_id_reference = $partner_id_reference;
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


                            $max_count = Partner::where('partner_type_id',$partner_type->id)->count() + 1;
                            $username = $partner_id_reference;
                            $partner_type = PartnerType::find($partner_type->id);
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
                                $logs[] = "Unable to create partner user."; 
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

    public function profileOverview($id){
        $partner_id = auth()->user()->reference_id;
        $is_original_user=0;
        $partner_access=-1;
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false) $partner_access = Partner::get_partners_access($partner_id); 
        if ($partner_access=="") $partner_access=$id;
        //dd($partner_id.'-'.$id);
        if ($partner_id==$id){
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7");
            $is_original_user=1;
        } else {
            $partner_info = Partner::get_partner_info($id,false,"1,2,4,5,7",$partner_access);    
        }

        if (count($partner_info)==0){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }

        $partner_info = $partner_info[0];
        $user_access = isset($access[strtolower($partner_info->partner_type_description)]) ? $access[strtolower($partner_info->partner_type_description)] : "";

        $canAccess = false;
        $canEditStatus = false;
        if (strpos($user_access, 'edit') !== false){
            $canAccess = true;
        }
        if (strpos($user_access, 'view') !== false){
            $canAccess = true;
        }
        if (!$canAccess){
            return redirect('/')->with('failed','You have no access to that page.')->send();
        }
        if (strpos($user_access, 'edit partner status') !== false) {
            $canEditStatus = true;
        }

        $isInternal = session('is_internal');
        // if (!session('is_internal')) {
        //     return redirect("/partners/details/profile/{$id}/profileOverview");
        // }
        
        $upline_partner_type_access = PartnerType::where('id',$partner_info->partner_type_id)->first();
        $uplines = Partner::get_upline_partner($id,$partner_access,$upline_partner_type_access->upline);
        $ownerships = Ownership::where('status','A')->orderBy('name','asc')->get();
        $countries = Country::where('status','A')->where('display_on_partner', 1)->/* orderBy('name','asc')-> */get();
        $documents = Document::where('status','A')->orderBy('name','asc')->get();

        $states = State::orderBy('abbr')->get();

        return view("partners.details.profile.overview",
            compact('ownerships','countries','documents','partner_info','id',
                'is_original_user','uplines','canEditStatus','isInternal','states'));
    }

    /* public function getCityByState($state)
    {
        $cities = UsZipCode::select('city','state_id')
            ->where('state_id', $state)
            ->distinct()
            ->orderBy('city')
            ->get();
        
        return response()->json(array(
            'cities' => $cities,
        ));
    } */

}
