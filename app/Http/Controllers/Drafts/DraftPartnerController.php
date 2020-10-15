<?php

namespace App\Http\Controllers\Drafts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Country;
use App\Models\Document;
use App\Models\Drafts\DraftPartner;
use App\Models\Drafts\DraftPartnerContact;
use App\Models\Drafts\DraftPartnerAttachment;
use App\Models\Drafts\DraftPartnerLanguage;
use App\Models\Drafts\DraftLeadComment;
use App\Models\Language;
use App\Models\Ownership;
use App\Models\Partner;
use App\Models\PartnerType;
use App\Models\Product;
use App\Models\State;
use App\Models\User;
use App\Models\UserType;
use App\Models\PartnerProduct;
use DB;
use Storage;
use File;
use App\Models\PartnerSystem;
use App\Models\DraftPartnerMid;
use App\Models\BusinessType;
use App\Models\UsZipCode;
use App\Models\PaymentProcessor;

class DraftPartnerController extends Controller
{
    public function index() 
    {
        $partnerAccess = -1;
        $id = auth()->user()->reference_id;
        $access = session('all_user_access');
        $adminAccess = isset($access['admin']) ? $access['admin'] : "";
        
        if (strpos($adminAccess, 'super admin access') === false){
            $partnerAccess = Partner::get_partners_access($id);
        }

        $partnerAccess = $partnerAccess == "" ? $id : $partnerAccess;
        $pt_access = "";
        $pt_access .= isset($access['company']) ? "7," : "";
        $pt_access .= isset($access['iso']) ? "4," : "";
        $pt_access .= isset($access['sub iso']) ? "5," : "";
        $pt_access .= isset($access['agent']) ? "1," : "";
        $pt_access .= isset($access['sub agent']) ? "2," : "";
        $pt_access .= isset($access['merchant']) ? "3," : "";
        $pt_access .= isset($access['lead']) ? "6," : "";
        $pt_access .= isset($access['prospect']) ? "8," : "";
        $pt_access = ($pt_access == "") ? "" : substr($pt_access, 0, strlen($pt_access) - 1); 

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

        $downline_partner_ids = explode(',', Partner::get_downline_partner_ids($id));
        $values = array_values($downline_partner_ids); 
        $order = join(", ",$values); 

        $draftApplicants = DraftPartner::with('draftPartnerContacts')
            ->with('partnerType')
            ->whereRaw(' parent_id IN (' . $order . ')')
            ->where('is_stored_to_partners', 0)
            ->get();

        return view('draft.list', compact('draftApplicants'));
    }

    public function store(Request $request) 
    {
        DB::transaction(function() use ($request){
            if (isset($request->txtDraftPartnerId)) {
                $draftPartner = DraftPartner::find($request->txtDraftPartnerId);
                $draftPartner->partner_type_id = $request->txtPartnerTypeId == null ? $request->partnerType : $request->txtPartnerTypeId;
                if ($request->selfAssign == 1 || $request->assigntome == 'on') {
                    $draftPartner->parent_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;  
                } else if ($request->txtPartnerTypeId == 6
                    || $request->txtPartnerTypeId == 8) {
                    $draftPartner->parent_id = $request->assignee == null ? auth()->user()->reference_id : $request->assignee;  
                } else {
                    $draftPartner->parent_id = $request->txtUplineId == null ? auth()->user()->reference_id : $request->txtUplineId;  
                }
                $state = '';
                if ($request->txtPartnerTypeId == 6
                    || $request->txtPartnerTypeId == 8) {
                    if($request->country == 'United States'){
                        $state = $request->txtState;
                    }
                    if($request->country == 'Philippines'){
                        $state = $request->txtStatePH;
                    }
                    if($request->country == 'China'){
                        $state = $request->txtStateCN;
                    }
                }
                if ($request->txtPartnerTypeId == 2
                    || $request->txtPartnerTypeId == 4
                    || $request->txtPartnerTypeId == 5
                    || $request->txtPartnerTypeId == 7) {
                    // Non-Agent Information
                    $draftPartner->ownership = $request->txtOwnership;
                    $draftPartner->company_name = $request->txtCompanyName;
                    $draftPartner->dba = $request->txtDBA;
                    $draftPartner->business_date = $request->txtBusinessDate;
                    $draftPartner->credit_card_reference_id = $request->txtCreditCardReference;
                    $draftPartner->website = $request->txtWebsite;
                    $draftPartner->merchant_processor = $request->currentProcessor;
                    // Non-Agent Business Address
                    $draftPartner->business_address1 = $request->txtBusinessAddress1;
                    $draftPartner->business_address2 = $request->txtBusinessAddress2;
                    $draftPartner->business_country = $request->txtCountry;
                    $draftPartner->business_state = $request->txtState;
                    $draftPartner->business_city = $request->txtCity;
                    $draftPartner->business_zip = $request->txtBusinessZip;
                    // Non-Agent Billing Address
                    // if ($request->chkSameAsBusinessBilling) {
                    if ($request->copy_to_billing == 'on') {
                        $draftPartner->billing_address = $request->txtBusinessAddress1;
                        $draftPartner->billing_address2 = $request->txtBusinessAddress2;
                        $draftPartner->billing_country = $request->txtCountry;
                        $draftPartner->billing_state = $request->txtState;
                        $draftPartner->billing_city = $request->txtCity;
                        $draftPartner->billing_zip = $request->txtBusinessZip;
                    } else {
                        $draftPartner->billing_address = $request->txtBillingAddress1;
                        $draftPartner->billing_address2 = $request->txtBillingAddress2;
                        $draftPartner->billing_country = $request->txtBillingCountry;
                        $draftPartner->billing_state = $request->txtBillingState;
                        $draftPartner->billing_city = $request->txtxtBillingCity;
                        $draftPartner->billing_zip = $request->txtBillingZip;
                    }
                    // Non-Agent Mailing Address
                    // if ($request->chkSameAsBusiness) {
                    if ($request->copy_to_mailing == 'on') {
                        $draftPartner->mailing_address = $request->txtBusinessAddress1;
                        $draftPartner->mailing_address2 = $request->txtBusinessAddress2;
                        $draftPartner->mailing_country = $request->txtMailingCountry;
                        $draftPartner->mailing_state = $request->txtState;
                        $draftPartner->mailing_city = $request->txtCity;
                        $draftPartner->mailing_zip = $request->txtBusinessZip;
                    } else {
                        $draftPartner->mailing_address = $request->txtMailingAddress1;
                        $draftPartner->mailing_address2 = $request->txtMailingAddress2;
                        $draftPartner->mailing_country = $request->txtMailingCountry;
                        $draftPartner->mailing_state = $request->txtMailingState;
                        $draftPartner->mailing_city = $request->txtMailingCity;
                        $draftPartner->mailing_zip = $request->txtMailingZip;
                    }
                    // Non-Agent Contact Information
                    $draftPartner->phone1 = $request->txtBusinessPhone1;
                    $draftPartner->phone2 = $request->txtBusinessPhone2;
                    $draftPartner->extension = $request->txtExtension1;
                    $draftPartner->extension_2 = $request->txtExtension2;
                    $draftPartner->partner_email = $request->txtEmail;
                    $draftPartner->partner_fax = $request->txtFax;
                    $draftPartner->extension_3 = $request->txtExtension3;
                    // Non-Agent Bank Information
                    $draftPartner->bank_name = $request->txtBankName;
                    $draftPartner->bank_routing_no = $request->txtBankRouting;
                    $draftPartner->bank_dda = $request->txtBankDDA;
                    $draftPartner->bank_address = $request->txtBankAddress;
                } else if ($request->txtPartnerTypeId == 3 || $request->txtPartnerTypeId == 9) {
                    // Merchant Information
                    $draftPartner->merchant_id = $request->txtMID;
                    $draftPartner->business_name = $request->txtLegalBusinessName;
                    $draftPartner->company_name = $request->txtBusinessName;
                    $draftPartner->tax_id_number = $request->txtTaxIdNumber;
                    $draftPartner->ownership = $request->txtOwnership;
                    $draftPartner->bank_name = $request->txtBankName;
                    $draftPartner->bank_routing_no = $request->txtBankRouting;
                    $draftPartner->bank_dda = $request->txtBankDDA;
                    $draftPartner->is_cc_client = $request->creditcardclient == 'off' ? 0 : 1;
                    $draftPartner->merchant_processor = $request->currentProcessor;
                    $draftPartner->business_date = $request->txtBusinessDate;
                    // Merchant Business Address
                    $draftPartner->business_address1 = $request->txtAddress;
                    $draftPartner->business_address2 = $request->txtAddress2;
                    $draftPartner->business_country = $request->txtCountry;
                    $draftPartner->business_state = $request->txtState;
                    $draftPartner->business_city = $request->txtCity;
                    $draftPartner->business_zip = $request->txtZip;
                    // Merchant Shipping Address
                    /* if ($request->copy_to_shipping == 'on') {
                        $draftPartner->shipping_address = $request->txtAddress;
                        $draftPartner->shipping_country = $request->txtCountry;
                        $draftPartner->shipping_state = $request->txtState;
                        $draftPartner->shipping_city = $request->txtCity;
                        $draftPartner->shipping_zip = $request->txtZip;
                    } else {
                        $draftPartner->shipping_address = $request->txtShippingAddress;
                        $draftPartner->shipping_country = $request->txtShippingCountry;
                        $draftPartner->shipping_state = $request->txtShippingState;
                        $draftPartner->shipping_city = $request->txtShippingCity;
                        $draftPartner->shipping_zip = $request->txtShippingZip;
                    } */
                    // Merchant Billing Address
                    if ($request->copy_to_billing == 'on') {
                        $draftPartner->billing_address = $request->txtAddress;
                        $draftPartner->billing_address2 = $request->txtAddress2;
                        $draftPartner->billing_country = $request->txtCountry;
                        $draftPartner->billing_state = $request->txtState;
                        $draftPartner->billing_city = $request->txtCity;
                        $draftPartner->billing_zip = $request->txtZip;
                    } else {
                        $draftPartner->billing_address = $request->txtBillingAddress1;
                        $draftPartner->billing_address2 = $request->txtBillingAddress2;
                        $draftPartner->billing_country = $request->txtBillingCountry;
                        $draftPartner->billing_state = $request->txtBillingState;
                        $draftPartner->billing_city = $request->txtBillingCity;
                        $draftPartner->billing_zip = $request->txtBillingZip;
                    }
                    // Merchant Mailing Address
                    if ($request->copy_to_mailing == 'on') {
                        $draftPartner->mailing_address = $request->txtAddress;
                        $draftPartner->mailing_address2 = $request->txtAddress2;
                        $draftPartner->mailing_country = $request->txtCountry;
                        $draftPartner->mailing_state = $request->txtState;
                        $draftPartner->mailing_city = $request->txtCity;
                        $draftPartner->mailing_zip = $request->txtZip;
                    } else {
                        $draftPartner->mailing_address = $request->txtMailingAddress1;
                        $draftPartner->mailing_address2 = $request->txtMailingAddress2;
                        $draftPartner->mailing_country = $request->txtMailingCountry;
                        $draftPartner->mailing_state = $request->txtMailingState;
                        $draftPartner->mailing_city = $request->txtMailingCity;
                        $draftPartner->mailing_zip = $request->txtMailingZip;
                    }
                    // Merchant Contact Information
                    /* $draftPartner->phone1 = $request->txtPhoneNumber;
                    $draftPartner->email_notifier = $request->txtEmailNotifier;
                    $draftPartner->partner_email = $request->txtEmail;
                    $draftPartner->auto_emailer = $request->txtTogBtnAutoEmailer == 'off' ? 0 : 1;
                    $draftPartner->merchant_url = $request->url; */
                    // Merchant Contact Information
                    $draftPartner->phone1 = $request->txtBusinessPhone1;
                    $draftPartner->phone2 = $request->txtBusinessPhone2;
                    $draftPartner->extension = $request->txtExtension1;
                    $draftPartner->extension_2 = $request->txtExtension2;
                    $draftPartner->partner_email = $request->txtEmail;
                    $draftPartner->partner_fax = $request->txtFax;
                    $draftPartner->extension_3 = $request->txtExtension3;
                    // Merchant Bank Information
                    $draftPartner->bank_name = $request->txtBankName;
                    $draftPartner->bank_routing_no = $request->txtBankRouting;
                    $draftPartner->bank_dda = $request->txtBankDDA;
                    $draftPartner->bank_address = $request->txtBankAddress;
                } else if ($request->txtPartnerTypeId == 1) {
                    // Agent Information
                    $draftPartner->business_name = $request->txtLegalBusinessName;
                    $draftPartner->company_name = $request->txtBusinessName;
                    $draftPartner->tax_id_number = $request->txtTaxIdNumber;
                    $draftPartner->bank_name = $request->txtBankName;
                    $draftPartner->bank_routing_no = $request->txtBankRouting;
                    $draftPartner->bank_dda = $request->txtBankDDA;
                    $draftPartner->email_notifier = $request->txtEmailNotifier;
                    $draftPartner->billing_cycle = $request->txtBillingCycle;
                    $draftPartner->billing_month = $request->txtBillingMonth;
                    $draftPartner->billing_day = $request->txtBillingDay;
                    // Agent Business Address
                    $draftPartner->business_address1 = $request->txtAddressAgent;
                    $draftPartner->business_country = $request->txtCountryAgent;
                    $draftPartner->business_state = $request->txtStateAgent;
                    $draftPartner->business_city = $request->txtCityAgent;
                    $draftPartner->business_zip = $request->txtZipAgent;
                    // Agent Contact Information
                    $draftPartner->phone1 = $request->txtPhoneNumber;
                    $draftPartner->extension = $request->txtContactMobileNumber1;
                    $draftPartner->partner_email = $request->txtEmailAgent;
                } else {
                    // Leads & Prospects Information
                    $draftPartner->merchant_processor = $request->currentProcessor;
                    $draftPartner->dba = $request->dba;
                    $draftPartner->ownership = $request->ownership;
                    $draftPartner->company_name = $request->legalName;
                    // Leads & Prospects Business Address
                    $draftPartner->business_address1 = $request->businessAddress1;
                    $draftPartner->business_address2 = $request->businessAddress2;
                    $draftPartner->business_country = $request->country;
                    $draftPartner->business_state = $state;
                    $draftPartner->business_city = $request->city;
                    $draftPartner->business_zip = $request->zip;
                    // Leads & Prospects Contact Information
                    $draftPartner->phone1 = $request->businessPhone1;
                    $draftPartner->phone2 = $request->businessPhone2;
                    $draftPartner->extension = $request->extension1;
                    $draftPartner->extension_2 = $request->extension2;
                    $draftPartner->partner_email = $request->txtPartnerTypeId == 6 ? $request->txtEmailLead : $request->txtEmailPros;
                    $draftPartner->partner_fax = $request->fax;
                    $interested_products = '';
                    if ($request->product_access) {
                        $interested_products = implode(',',$request->product_access);
                    }
                    $draftPartner->interested_products = $interested_products;
                }
                $draftPartner->create_by = auth()->user()->username;
                $draftPartner->update_by = auth()->user()->username;

                $draftPartner->tax_id_number = $request->txtTaxID;
                $draftPartner->front_end_mid = $request->txtFrontEndMID;
                $draftPartner->back_end_mid = $request->txtBackEndMID;
                $draftPartner->reporting_mid = $request->txtReportingMID;
                $draftPartner->pricing_type = $request->txtPricingType;

                if ($request->txtPartnerTypeId == 3 
                    || $request->txtPartnerTypeId == 9
                    || $request->txtPartnerTypeId == 6
                    || $request->txtPartnerTypeId == 8) {
                    $draftPartner->business_type_code = $request->mcc;
                }

                // Save profile picture
                if ($request->hasFile("profileUpload")
                    && $request->file('profileUpload') != $draftPartner->image) {
                    // delete old profile pic
                    Storage::disk('public')->delete(substr($draftPartner->image, 8));
                    
                    $attachment = $request->file('profileUpload');
                    $fileName = pathinfo($attachment->getClientOriginalName(),PATHINFO_FILENAME);
                    $extension = $attachment->getClientOriginalExtension();
                    $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
                    $storagePath = Storage::disk('public')->putFileAs('user_profile', $attachment,  $filenameToStore, 'public');
                    $draftPartner->image = '/storage/user_profile/'.$filenameToStore;
                }

                // if (!
                $draftPartner->save();
                // ) {
                    // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                // }
            
                DraftPartnerMid::where('partner_id',$draftPartner->id)->delete();

                $partnerMID = new DraftPartnerMid;
                $partnerMID->partner_id = $draftPartner->id;
                $partnerMID->mid = $request->input('txtMID');
                $partnerMID->system_id = $request->input('txtSystem');
                $partnerMID->create_by = auth()->user()->username;
                $partnerMID->save();

                for ($x = 1; $x <= $request->midCtr; $x++) {
                    if( $request->filled('txtMID'.$x)){
                        $partnerMID = new DraftPartnerMid;
                        $partnerMID->partner_id = $draftPartner->id;
                        $partnerMID->mid = $request->input('txtMID'.$x);
                        $partnerMID->system_id = $request->input('txtSystem'.$x);
                        $partnerMID->create_by = auth()->user()->username;
                        $partnerMID->save();
                    }
                } 


                $id =  $request->txtDraftPartnerId; // $draftPartner->id;
                
                if ($request->languages) {
                    if (count($request->languages) > 0) {
                        $draftPartnerLanguage = new DraftPartnerLanguage;
                        foreach ($request->languages as $key => $value) {
                            $draftPartnerLanguage->draft_partner_id = $id;
                            $draftPartnerLanguage->draft_language_id = $value;
                            $draftPartnerLanguage->save();
                        }
                    }
                }
    
                $draftPartnerContact = DraftPartnerContact::find($request->txtDraftPartnerId);
                $draftPartnerContact->draft_partner_id = $id;
                if ($request->txtPartnerTypeId == 2
                    || $request->txtPartnerTypeId == 4
                    || $request->txtPartnerTypeId == 5
                    || $request->txtPartnerTypeId == 7) {
                    // Non-Agent Partner Contact Info
                    $dob = $request->txtContactDOB1 != null ? date('Y-m-d', strtotime($request->txtContactDOB1)) : null;
                    $draftPartnerContact->first_name = $request->txtContactFirstName1;
                    $draftPartnerContact->last_name = $request->txtContactLastName1;
                    $draftPartnerContact->middle_name = $request->txtContactMiddleInitial1;
                    $draftPartnerContact->position = $request->txtContactTitle1;
                    $draftPartnerContact->ssn = $request->txtContactSSN1;
                    $draftPartnerContact->ownership_percentage = $request->txtOwnershipPercentage1;
                    $draftPartnerContact->dob = $dob;
                    // Non-Agent Partner Contact Address
                    $draftPartnerContact->contact_address1 = $request->txtContactHomeAddress1_1;
                    $draftPartnerContact->contact_address2 = $request->txtContactHomeAddress1_2;
                    $draftPartnerContact->contact_country = $request->txtContactCountry1;
                    $draftPartnerContact->contact_state = $request->txtContactState1;
                    $draftPartnerContact->contact_city = $request->txtContactCity1;
                    $draftPartnerContact->contact_zip = $request->txtContactZip1;
                    // Non-Agent Personal Contact Info
                    $draftPartnerContact->other_number = $request->txtContactPhone1_1;
                    $draftPartnerContact->other_number_2 = $request->txtContactPhone1_2;
                    $draftPartnerContact->contact_fax = $request->txtContactFax1;
                    $draftPartnerContact->mobile_number = $request->txtContactMobile1_1;
                    $draftPartnerContact->mobile_number_2 = $request->txtContactMobile1_2;
                    $draftPartnerContact->contact_email = $request->txtContactEmail1;
                } else if ($request->txtPartnerTypeId == 3 || $request->txtPartnerTypeId == 9) {
                    // Merchant Contact Info
                    $draftPartnerContact->first_name = $request->txtFirstName;
                    $draftPartnerContact->last_name = $request->txtLastName;
                    $draftPartnerContact->middle_name = $request->txtMiddleInitial;
                    $draftPartnerContact->position = $request->txtTitle;
                    $draftPartnerContact->ssn = $request->txtSSN;
                    $draftPartnerContact->mobile_number = $request->txtContactMobileNumber;
                } else if ($request->txtPartnerTypeId == 1) { 
                    // Agent Partner Contact Info
                    $draftPartnerContact->first_name = $request->txtContactFirstNameAgent;
                    $draftPartnerContact->last_name = $request->txtContactLastNameAgent;
                    $draftPartnerContact->middle_name = $request->txtContactMiddleInitialAgent;
                    $draftPartnerContact->ssn = $request->txtSSNAgent;
                    $draftPartnerContact->mobile_number = $request->txtContactMobileNumberAgent;
                } else {
                    // Leads & Prospects Contact Info
                    $draftPartnerContact->first_name = $request->fname;
                    $draftPartnerContact->last_name = $request->lname;
                    $draftPartnerContact->middle_name = $request->mname;
                    $draftPartnerContact->position = $request->title;
                    $draftPartnerContact->other_number = $request->cphone1;
                    $draftPartnerContact->other_number_2 = $request->cphone2;
                    $draftPartnerContact->mobile_number = $request->mobileNumber;
                    $draftPartnerContact->contact_fax = $request->contactFax;
                }
                $draftPartnerContact->is_original_contact = 1;
                // if (!
                $draftPartnerContact->save();
                // ) {
                    // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                // }
                // Other Contacts
                $details = $request->txtOtherHidden;
                if ($details) {
                    $details = json_decode($details);
                    foreach ($details as $d) {
                        if($request->input('txtContactCountry'.$d) != "") { 
                            $draftPartnerContact = new DraftPartnerContact;
                            $draftPartnerContact->draft_partner_id = $id;
                            if ($request->txtPartnerTypeId == 2
                                || $request->txtPartnerTypeId == 4
                                || $request->txtPartnerTypeId == 5
                                || $request->txtPartnerTypeId == 7) {
                                // Non-Agent Partner Contact Info
                                $dob = $request->input('txtContactDOB'.$d) != null ? date('Y-m-d', strtotime($request->input('txtContactDOB'.$d))) : null;
                                $draftPartnerContact->first_name = $request->input('txtContactFirstName'.$d);
                                $draftPartnerContact->last_name = $request->input('txtContactLastName'.$d);
                                $draftPartnerContact->middle_name = $request->input('txtContactMiddleInitial'.$d);
                                $draftPartnerContact->position = $request->input('txtContactTitle'.$d);
                                $draftPartnerContact->ssn = $request->input('txtContactSSN'.$d);
                                $draftPartnerContact->ownership_percentage = $request->input('txtOwnershipPercentage'.$d);
                                $draftPartnerContact->dob = $dob;
                                // Non-Agent Partner Contact Address
                                $draftPartnerContact->contact_address1 = $request->input('txtContactHomeAddress'.$d.'_1');
                                $draftPartnerContact->contact_address2 = $request->input('txtContactHomeAddress'.$d.'_2');
                                $draftPartnerContact->contact_country = $request->input('txtContactCountry'.$d);
                                $draftPartnerContact->contact_state = $request->input('txtContactState'.$d);
                                $draftPartnerContact->contact_city = $request->input('txtContactCity'.$d);
                                $draftPartnerContact->contact_zip = $request->input('txtContactZip'.$d);
                                // Non-Agent Personal Contact Info
                                $draftPartnerContact->other_number = $request->input('txtContactPhone'.$d.'_1');
                                $draftPartnerContact->other_number_2 = $request->input('txtContactPhone'.$d.'_2');
                                $draftPartnerContact->contact_fax = $request->input('txtContactFax'.$d);
                                $draftPartnerContact->mobile_number = $request->input('txtContactMobile'.$d.'_1');
                                $draftPartnerContact->mobile_number_2 = $request->input('txtContactMobile'.$d.'_2');
                                $draftPartnerContact->contact_email = $request->input('txtContactEmail'.$d);
                            } else if ($request->txtPartnerTypeId == 3 || $request->txtPartnerTypeId == 9) {
                                // Merchant Contact Information
                                $draftPartnerContact->first_name = $request->input('txtFirstName'.$d);
                                $draftPartnerContact->last_name = $request->input('txtLastName'.$d);
                                $draftPartnerContact->middle_name = $request->input('txtMiddleInitial'.$d);
                                $draftPartnerContact->position = $request->input('txtTitle'.$d);
                                $draftPartnerContact->ssn = $request->input('txtSSN'.$d);
                                $draftPartnerContact->mobile_number = $request->input('txtContactMobileNumber'.$d);
                            } else {
                                // Agent Partner Contact Information
                                $draftPartnerContact->first_name = $request->input('txtContactFirstNameAgent'.$d);
                                $draftPartnerContact->last_name = $request->input('txtContactLastNameAgent'.$d);
                                $draftPartnerContact->middle_name = $request->input('txtContactMiddleInitialAgent'.$d);
                                $draftPartnerContact->ssn = $request->input('txtSSNAgent'.$d);
                                $draftPartnerContact->mobile_number = $request->input('txtContactMobileNumberAgent'.$d);
                            } 
                            $draftPartnerContact->is_original_contact = 0;
                            // if (!
                            $draftPartnerContact->save();
                            // ) {
                                // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                            // }
                        }
                    }
                }
                // attachments
                $documents = Document::where('status','A')->orderBy('id','asc')->get();
                foreach ($documents as $document) {
                    if ($request->hasFile('fileUpload'.$document->id)){
                        $thefile = File::get($request->file('fileUpload'.$document->id));
                        $fileNameWithExt = $request->file('fileUpload'.$document->id)->getClientOriginalName();
                        $fileName = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                        $extension = $request->file('fileUpload'.$document->id)->getClientOriginalExtension();
                        $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
    
                        Storage::disk('attachment')->put($filenameToStore,$thefile);
    
                        $draftPartnerAttachment = new DraftPartnerAttachment;
                        $draftPartnerAttachment->draft_partner_id = $id;
                        $draftPartnerAttachment->document_name = $document->name;
                        $draftPartnerAttachment->document_image = $filenameToStore;
                        $draftPartnerAttachment->document_id = $document->id;
                        // if (!
                        $draftPartnerAttachment->save();
                        // ) {
                            // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                        // }
                    }
                }
    
                // other attachments
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
            
                            $draftPartnerAttachment = new DraftPartnerAttachment;
                            $draftPartnerAttachment->draft_partner_id = $id;
                            $draftPartnerAttachment->document_name = $request->input('OthersDescription'.$d);
                            $draftPartnerAttachment->document_image = $filenameToStore;
                            $draftPartnerAttachment->document_id = 7;
                            // if (!
                            $draftPartnerAttachment->save();
                            // ) {
                                // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                            // }
                        }
                    }
                }
    
                // Draft Lead Comment
                if ($request->note) {
                    $leadsComment = DraftLeadComment::where('draft_partner_id', $request->txtDraftPartnerId)->first();
                    $leadsComment->draft_partner_id = $id;
                    $leadsComment->comment = $request->note;
                    $leadsComment->parent_id = -1;
                    $leadsComment->create_by = auth()->user()->username;
                    $leadsComment->user_id = auth()->user()->id;
                    $leadsComment->save();
                }
            } else {
                $draftPartner = new DraftPartner;
                $draftPartner->partner_type_id = $request->txtPartnerTypeId == null ? $request->partnerType : $request->txtPartnerTypeId;
                if ($request->selfAssign == 1) {
                    $draftPartner->parent_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;  
                } else if ($request->txtPartnerTypeId == 6
                    || $request->txtPartnerTypeId == 8) {
                    $draftPartner->parent_id = $request->assignee == null ? auth()->user()->reference_id : $request->assignee;  
                } else {
                    $draftPartner->parent_id = $request->txtUplineId == null ? auth()->user()->reference_id : $request->txtUplineId;  
                }
                $state = '';
                if ($request->txtPartnerTypeId == 6
                    || $request->txtPartnerTypeId == 8) {
                    if($request->country == 'United States'){
                        $state = $request->txtState;
                    }
                    if($request->country == 'Philippines'){
                        $state = $request->txtStatePH;
                    }
                    if($request->country == 'China'){
                        $state = $request->txtStateCN;
                    }
                }
                if ($request->txtPartnerTypeId == 2
                    || $request->txtPartnerTypeId == 4
                    || $request->txtPartnerTypeId == 5
                    || $request->txtPartnerTypeId == 7) {
                    // Non-Agent Information
                    $draftPartner->ownership = $request->txtOwnership;
                    $draftPartner->company_name = $request->txtCompanyName;
                    $draftPartner->dba = $request->txtDBA;
                    $draftPartner->business_date = $request->txtBusinessDate;
                    $draftPartner->credit_card_reference_id = $request->txtCreditCardReference;
                    $draftPartner->website = $request->txtWebsite;
                    $draftPartner->merchant_processor = $request->currentProcessor;
                    // Non-Agent Business Address
                    $draftPartner->business_address1 = $request->txtBusinessAddress1;
                    $draftPartner->business_address2 = $request->txtBusinessAddress2;
                    $draftPartner->business_country = $request->txtCountry;
                    $draftPartner->business_state = $request->txtState;
                    $draftPartner->business_city = $request->txtCity;
                    $draftPartner->business_zip = $request->txtBusinessZip;
                    // Non-Agent Billing Address
                    // if ($request->chkSameAsBusinessBilling) {
                    if ($request->copy_to_billing == 'on') {
                        $draftPartner->billing_address = $request->txtBusinessAddress1;
                        $draftPartner->billing_address2 = $request->txtBusinessAddress2;
                        $draftPartner->billing_country = $request->txtCountry;
                        $draftPartner->billing_state = $request->txtState;
                        $draftPartner->billing_city = $request->txtCity;
                        $draftPartner->billing_zip = $request->txtBusinessZip;
                    } else {
                        $draftPartner->billing_address = $request->txtBillingAddress1;
                        $draftPartner->billing_address2 = $request->txtBillingAddress2;
                        $draftPartner->billing_country = $request->txtBillingCountry;
                        $draftPartner->billing_state = $request->txtBillingState;
                        $draftPartner->billing_city = $request->txtxtBillingCity;
                        $draftPartner->billing_zip = $request->txtBillingZip;
                    }
                    // Non-Agent Mailing Address
                    // if ($request->chkSameAsBusiness) {
                    if ($request->copy_to_mailing == 'on') {
                        $draftPartner->mailing_address = $request->txtBusinessAddress1;
                        $draftPartner->mailing_address2 = $request->txtBusinessAddress2;
                        $draftPartner->mailing_country = $request->txtMailingCountry;
                        $draftPartner->mailing_state = $request->txtState;
                        $draftPartner->mailing_city = $request->txtCity;
                        $draftPartner->mailing_zip = $request->txtBusinessZip;
                    } else {
                        $draftPartner->mailing_address = $request->txtMailingAddress1;
                        $draftPartner->mailing_address2 = $request->txtMailingAddress2;
                        $draftPartner->mailing_country = $request->txtMailingCountry;
                        $draftPartner->mailing_state = $request->txtMailingState;
                        $draftPartner->mailing_city = $request->txtMailingCity;
                        $draftPartner->mailing_zip = $request->txtMailingZip;
                    }
                    // Non-Agent Contact Information
                    $draftPartner->phone1 = $request->txtBusinessPhone1;
                    $draftPartner->phone2 = $request->txtBusinessPhone2;
                    $draftPartner->extension = $request->txtExtension1;
                    $draftPartner->extension_2 = $request->txtExtension2;
                    $draftPartner->partner_email = $request->txtEmail;
                    $draftPartner->partner_fax = $request->txtFax;
                    $draftPartner->extension_3 = $request->txtExtension3;
                    // Non-Agent Bank Information
                    $draftPartner->bank_name = $request->txtBankName;
                    $draftPartner->bank_routing_no = $request->txtBankRouting;
                    $draftPartner->bank_dda = $request->txtBankDDA;
                    $draftPartner->bank_address = $request->txtBankAddress;
                } else if ($request->txtPartnerTypeId == 3 || $request->txtPartnerTypeId == 9) {
                    // Merchant Information
                    $draftPartner->merchant_id = $request->txtMID;
                    $draftPartner->business_name = $request->txtLegalBusinessName;
                    $draftPartner->company_name = $request->txtBusinessName;
                    $draftPartner->tax_id_number = $request->txtTaxIdNumber;
                    $draftPartner->ownership = $request->txtOwnership;
                    $draftPartner->bank_name = $request->txtBankName;
                    $draftPartner->bank_routing_no = $request->txtBankRouting;
                    $draftPartner->bank_dda = $request->txtBankDDA;
                    $draftPartner->is_cc_client = $request->creditcardclient == 'off' ? 0 : 1;
                    $draftPartner->merchant_processor = $request->currentProcessor;
                    $draftPartner->business_date = $request->txtBusinessDate;
                    // Merchant Business Address
                    $draftPartner->business_address1 = $request->txtAddress;
                    $draftPartner->business_address2 = $request->txtAddress2;
                    $draftPartner->business_country = $request->txtCountry;
                    $draftPartner->business_state = $request->txtState;
                    $draftPartner->business_city = $request->txtCity;
                    $draftPartner->business_zip = $request->txtZip;
                    // Merchant Shipping Address
                    /* if ($request->copy_to_shipping == 'on') {
                        $draftPartner->shipping_address = $request->txtAddress;
                        $draftPartner->shipping_country = $request->txtCountry;
                        $draftPartner->shipping_state = $request->txtState;
                        $draftPartner->shipping_city = $request->txtCity;
                        $draftPartner->shipping_zip = $request->txtZip;
                    } else {
                        $draftPartner->shipping_address = $request->txtShippingAddress;
                        $draftPartner->shipping_country = $request->txtShippingCountry;
                        $draftPartner->shipping_state = $request->txtShippingState;
                        $draftPartner->shipping_city = $request->txtShippingCity;
                        $draftPartner->shipping_zip = $request->txtShippingZip;
                    } */
                    // Merchant Billing Address
                    if ($request->copy_to_billing == 'on') {
                        $draftPartner->billing_address = $request->txtAddress;
                        $draftPartner->billing_address2 = $request->txtAddress2;
                        $draftPartner->billing_country = $request->txtCountry;
                        $draftPartner->billing_state = $request->txtState;
                        $draftPartner->billing_city = $request->txtCity;
                        $draftPartner->billing_zip = $request->txtZip;
                    } else {
                        $draftPartner->billing_address = $request->txtBillingAddress1;
                        $draftPartner->billing_address2 = $request->txtBillingAddress2;
                        $draftPartner->billing_country = $request->txtBillingCountry;
                        $draftPartner->billing_state = $request->txtBillingState;
                        $draftPartner->billing_city = $request->txtBillingCity;
                        $draftPartner->billing_zip = $request->txtBillingZip;
                    }
                    // Merchant Mailing Address
                    if ($request->copy_to_mailing == 'on') {
                        $draftPartner->mailing_address = $request->txtAddress;
                        $draftPartner->mailing_address2 = $request->txtAddress2;
                        $draftPartner->mailing_country = $request->txtCountry;
                        $draftPartner->mailing_state = $request->txtState;
                        $draftPartner->mailing_city = $request->txtCity;
                        $draftPartner->mailing_zip = $request->txtZip;
                    } else {
                        $draftPartner->mailing_address = $request->txtMailingAddress1;
                        $draftPartner->mailing_address2 = $request->txtMailingAddress2;
                        $draftPartner->mailing_country = $request->txtMailingCountry;
                        $draftPartner->mailing_state = $request->txtMailingState;
                        $draftPartner->mailing_city = $request->txtMailingCity;
                        $draftPartner->mailing_zip = $request->txtMailingZip;
                    }
                    // Merchant Contact Information
                    /* $draftPartner->phone1 = $request->txtPhoneNumber;
                    $draftPartner->email_notifier = $request->txtEmailNotifier;
                    $draftPartner->partner_email = $request->txtEmail;
                    $draftPartner->auto_emailer = $request->txtTogBtnAutoEmailer == 'off' ? 0 : 1; */
                    // Merchant Contact Information
                    $draftPartner->phone1 = $request->txtBusinessPhone1;
                    $draftPartner->phone2 = $request->txtBusinessPhone2;
                    $draftPartner->extension = $request->txtExtension1;
                    $draftPartner->extension_2 = $request->txtExtension2;
                    $draftPartner->partner_email = $request->txtEmail;
                    $draftPartner->partner_fax = $request->txtFax;
                    $draftPartner->extension_3 = $request->txtExtension3;
                    // Merchant Bank Information
                    $draftPartner->bank_name = $request->txtBankName;
                    $draftPartner->bank_routing_no = $request->txtBankRouting;
                    $draftPartner->bank_dda = $request->txtBankDDA;
                    $draftPartner->bank_address = $request->txtBankAddress;
                } else if ($request->txtPartnerTypeId == 1) {
                    // Agent Information
                    $draftPartner->business_name = $request->txtLegalBusinessName;
                    $draftPartner->company_name = $request->txtBusinessName;
                    $draftPartner->tax_id_number = $request->txtTaxIdNumber;
                    $draftPartner->bank_name = $request->txtBankName;
                    $draftPartner->bank_routing_no = $request->txtBankRouting;
                    $draftPartner->bank_dda = $request->txtBankDDA;
                    $draftPartner->email_notifier = $request->txtEmailNotifier;
                    $draftPartner->billing_cycle = $request->txtBillingCycle;
                    $draftPartner->billing_month = $request->txtBillingMonth;
                    $draftPartner->billing_day = $request->txtBillingDay;
                    // Agent Business Address
                    $draftPartner->business_address1 = $request->txtAddressAgent;
                    $draftPartner->business_country = $request->txtCountryAgent;
                    $draftPartner->business_state = $request->txtStateAgent;
                    $draftPartner->business_city = $request->txtCityAgent;
                    $draftPartner->business_zip = $request->txtZipAgent;
                    // Agent Contact Information
                    $draftPartner->phone1 = $request->txtPhoneNumber;
                    $draftPartner->extension = $request->txtContactMobileNumber1;
                    $draftPartner->partner_email = $request->txtEmailAgent;
                } else {
                    // Leads & Prospects Information
                    $draftPartner->merchant_processor = $request->currentProcessor;
                    $draftPartner->dba = $request->dba;
                    $draftPartner->ownership = $request->ownership;
                    $draftPartner->company_name = $request->legalName;
                    // Leads & Prospects Business Address
                    $draftPartner->business_address1 = $request->businessAddress1;
                    $draftPartner->business_address2 = $request->businessAddress2;
                    $draftPartner->business_country = $request->country;
                    $draftPartner->business_state = $state;
                    $draftPartner->business_city = $request->city;
                    $draftPartner->business_zip = $request->zip;
                    // Leads & Prospects Contact Information
                    $draftPartner->phone1 = $request->businessPhone1;
                    $draftPartner->phone2 = $request->businessPhone2;
                    $draftPartner->extension = $request->extension1;
                    $draftPartner->extension_2 = $request->extension2;
                    $draftPartner->partner_email = $request->txtPartnerTypeId == 6 ? $request->txtEmailLead : $request->txtEmailPros;
                    $draftPartner->partner_fax = $request->fax;
                    $interested_products = '';
                    if ($request->product_access) {
                        $interested_products = implode(',',$request->product_access);
                    }
                    $draftPartner->interested_products = $interested_products;
                }
                $draftPartner->create_by = auth()->user()->username;
                $draftPartner->update_by = auth()->user()->username;

                $draftPartner->tax_id_number = $request->txtTaxID;
                $draftPartner->front_end_mid = $request->txtFrontEndMID;
                $draftPartner->back_end_mid = $request->txtBackEndMID;
                $draftPartner->reporting_mid = $request->txtReportingMID;
                $draftPartner->pricing_type = $request->txtPricingType;


                if ($request->txtPartnerTypeId == 3 
                    || $request->txtPartnerTypeId == 9
                    || $request->txtPartnerTypeId == 6
                    || $request->txtPartnerTypeId == 8) {
                    $draftPartner->business_type_code = $request->mcc;
                }

                // Save profile picture
                if ($request->hasFile("profileUpload")) {
                    $attachment = $request->file('profileUpload');
                    $fileName = pathinfo($attachment->getClientOriginalName(),PATHINFO_FILENAME);
                    $extension = $attachment->getClientOriginalExtension();
                    $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
                    $storagePath = Storage::disk('public')->putFileAs('user_profile', $attachment,  $filenameToStore, 'public');
                    $draftPartner->image = '/storage/user_profile/'.$filenameToStore;
                }
                // if (!
                $draftPartner->save();
                // ) {
                    // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                // }
        
                $partnerMID = new DraftPartnerMid;
                $partnerMID->partner_id = $draftPartner->id;
                $partnerMID->mid = $request->input('txtMID');
                $partnerMID->system_id = $request->input('txtSystem');
                $partnerMID->create_by = auth()->user()->username;
                $partnerMID->save();

                for ($x = 1; $x <= $request->midCtr; $x++) {
                    if( $request->filled('txtMID'.$x)){
                        $partnerMID = new DraftPartnerMid;
                        $partnerMID->partner_id = $draftPartner->id;
                        $partnerMID->mid = $request->input('txtMID'.$x);
                        $partnerMID->system_id = $request->input('txtSystem'.$x);
                        $partnerMID->create_by = auth()->user()->username;
                        $partnerMID->save();
                    }
                } 

                $id =  $draftPartner->id;
                
                if ($request->languages) {
                    if (count($request->languages) > 0) {
                        $draftPartnerLanguage = new DraftPartnerLanguage;
                        foreach ($request->languages as $key => $value) {
                            $draftPartnerLanguage->draft_partner_id = $id;
                            $draftPartnerLanguage->draft_language_id = $value;
                            $draftPartnerLanguage->save();
                        }
                    }
                }
    
                $draftPartnerContact = new DraftPartnerContact;
                $draftPartnerContact->draft_partner_id = $id;
                if ($request->txtPartnerTypeId == 2
                    || $request->txtPartnerTypeId == 4
                    || $request->txtPartnerTypeId == 5
                    || $request->txtPartnerTypeId == 7) {
                    // Non-Agent Partner Contact Info
                    $dob = $request->txtContactDOB1 != null ? date('Y-m-d', strtotime($request->txtContactDOB1)) : null;
                    $draftPartnerContact->first_name = $request->txtContactFirstName1;
                    $draftPartnerContact->last_name = $request->txtContactLastName1;
                    $draftPartnerContact->middle_name = $request->txtContactMiddleInitial1;
                    $draftPartnerContact->position = $request->txtContactTitle1;
                    $draftPartnerContact->ssn = $request->txtContactSSN1;
                    $draftPartnerContact->ownership_percentage = $request->txtOwnershipPercentage1;
                    $draftPartnerContact->dob = $dob;
                    // Non-Agent Partner Contact Address
                    $draftPartnerContact->contact_address1 = $request->txtContactHomeAddress1_1;
                    $draftPartnerContact->contact_address2 = $request->txtContactHomeAddress1_2;
                    $draftPartnerContact->contact_country = $request->txtContactCountry1;
                    $draftPartnerContact->contact_state = $request->txtContactState1;
                    $draftPartnerContact->contact_city = $request->txtContactCity1;
                    $draftPartnerContact->contact_zip = $request->txtContactZip1;
                    // Non-Agent Personal Contact Info
                    $draftPartnerContact->other_number = $request->txtContactPhone1_1;
                    $draftPartnerContact->other_number_2 = $request->txtContactPhone1_2;
                    $draftPartnerContact->contact_fax = $request->txtContactFax1;
                    $draftPartnerContact->mobile_number = $request->txtContactMobile1_1;
                    $draftPartnerContact->mobile_number_2 = $request->txtContactMobile1_2;
                    $draftPartnerContact->contact_email = $request->txtContactEmail1;
                } else if ($request->txtPartnerTypeId == 3 || $request->txtPartnerTypeId == 9) {
                    // Merchant Contact Info
                    $draftPartnerContact->first_name = $request->txtFirstName;
                    $draftPartnerContact->last_name = $request->txtLastName;
                    $draftPartnerContact->middle_name = $request->txtMiddleInitial;
                    $draftPartnerContact->position = $request->txtTitle;
                    $draftPartnerContact->ssn = $request->txtSSN;
                    $draftPartnerContact->mobile_number = $request->txtContactMobileNumber;
                } else if ($request->txtPartnerTypeId == 1) { 
                    // Agent Partner Contact Info
                    $draftPartnerContact->first_name = $request->txtContactFirstNameAgent;
                    $draftPartnerContact->last_name = $request->txtContactLastNameAgent;
                    $draftPartnerContact->middle_name = $request->txtContactMiddleInitialAgent;
                    $draftPartnerContact->ssn = $request->txtSSNAgent;
                    $draftPartnerContact->mobile_number = $request->txtContactMobileNumberAgent;
                } else {
                    // Leads & Prospects Contact Info
                    $draftPartnerContact->first_name = $request->fname;
                    $draftPartnerContact->last_name = $request->lname;
                    $draftPartnerContact->middle_name = $request->mname;
                    $draftPartnerContact->position = $request->title;
                    $draftPartnerContact->other_number = $request->cphone1;
                    $draftPartnerContact->other_number_2 = $request->cphone2;
                    $draftPartnerContact->mobile_number = $request->mobileNumber;
                    $draftPartnerContact->contact_fax = $request->contactFax;
                }
                $draftPartnerContact->is_original_contact = 1;
                // if (!
                $draftPartnerContact->save();
                // ) {
                    // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                // }
                // Other Contacts
                $details = $request->txtOtherHidden;
                if ($details) {
                    $details = json_decode($details);
                    foreach ($details as $d) {
                        if($request->input('txtContactCountry'.$d) != "") { 
                            $draftPartnerContact = new DraftPartnerContact;
                            $draftPartnerContact->draft_partner_id = $id;
                            if ($request->txtPartnerTypeId == 2
                                || $request->txtPartnerTypeId == 4
                                || $request->txtPartnerTypeId == 5
                                || $request->txtPartnerTypeId == 7) {
                                // Non-Agent Partner Contact Info
                                $dob = $request->input('txtContactDOB'.$d) != null ? date('Y-m-d', strtotime($request->input('txtContactDOB'.$d))) : null;
                                $draftPartnerContact->first_name = $request->input('txtContactFirstName'.$d);
                                $draftPartnerContact->last_name = $request->input('txtContactLastName'.$d);
                                $draftPartnerContact->middle_name = $request->input('txtContactMiddleInitial'.$d);
                                $draftPartnerContact->position = $request->input('txtContactTitle'.$d);
                                $draftPartnerContact->ssn = $request->input('txtContactSSN'.$d);
                                $draftPartnerContact->ownership_percentage = $request->input('txtOwnershipPercentage'.$d);
                                $draftPartnerContact->dob = $dob;
                                // Non-Agent Partner Contact Address
                                $draftPartnerContact->contact_address1 = $request->input('txtContactHomeAddress'.$d.'_1');
                                $draftPartnerContact->contact_address2 = $request->input('txtContactHomeAddress'.$d.'_2');
                                $draftPartnerContact->contact_country = $request->input('txtContactCountry'.$d);
                                $draftPartnerContact->contact_state = $request->input('txtContactState'.$d);
                                $draftPartnerContact->contact_city = $request->input('txtContactCity'.$d);
                                $draftPartnerContact->contact_zip = $request->input('txtContactZip'.$d);
                                // Non-Agent Personal Contact Info
                                $draftPartnerContact->other_number = $request->input('txtContactPhone'.$d.'_1');
                                $draftPartnerContact->other_number_2 = $request->input('txtContactPhone'.$d.'_2');
                                $draftPartnerContact->contact_fax = $request->input('txtContactFax'.$d);
                                $draftPartnerContact->mobile_number = $request->input('txtContactMobile'.$d.'_1');
                                $draftPartnerContact->mobile_number_2 = $request->input('txtContactMobile'.$d.'_2');
                                $draftPartnerContact->contact_email = $request->input('txtContactEmail'.$d);
                            } else if ($request->txtPartnerTypeId == 3 || $request->txtPartnerTypeId == 9) {
                                // Merchant Contact Information
                                $draftPartnerContact->first_name = $request->input('txtFirstName'.$d);
                                $draftPartnerContact->last_name = $request->input('txtLastName'.$d);
                                $draftPartnerContact->middle_name = $request->input('txtMiddleInitial'.$d);
                                $draftPartnerContact->position = $request->input('txtTitle'.$d);
                                $draftPartnerContact->ssn = $request->input('txtSSN'.$d);
                                $draftPartnerContact->mobile_number = $request->input('txtContactMobileNumber'.$d);
                            } else {
                                // Agent Partner Contact Information
                                $draftPartnerContact->first_name = $request->input('txtContactFirstNameAgent'.$d);
                                $draftPartnerContact->last_name = $request->input('txtContactLastNameAgent'.$d);
                                $draftPartnerContact->middle_name = $request->input('txtContactMiddleInitialAgent'.$d);
                                $draftPartnerContact->ssn = $request->input('txtSSNAgent'.$d);
                                $draftPartnerContact->mobile_number = $request->input('txtContactMobileNumberAgent'.$d);
                            } 
                            $draftPartnerContact->is_original_contact = 0;
                            // if (!
                            $draftPartnerContact->save();
                            // ) {
                                // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                            // }
                        }
                    }
                }
                // attachments
                $documents = Document::where('status','A')->orderBy('id','asc')->get();
                foreach ($documents as $document) {
                    if ($request->hasFile('fileUpload'.$document->id)){
                        $thefile = File::get($request->file('fileUpload'.$document->id));
                        $fileNameWithExt = $request->file('fileUpload'.$document->id)->getClientOriginalName();
                        $fileName = pathinfo($fileNameWithExt,PATHINFO_FILENAME);
                        $extension = $request->file('fileUpload'.$document->id)->getClientOriginalExtension();
                        $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
    
                        Storage::disk('attachment')->put($filenameToStore,$thefile);
    
                        $draftPartnerAttachment = new DraftPartnerAttachment;
                        $draftPartnerAttachment->draft_partner_id = $id;
                        $draftPartnerAttachment->document_name = $document->name;
                        $draftPartnerAttachment->document_image = $filenameToStore;
                        $draftPartnerAttachment->document_id = $document->id;
                        // if (!
                        $draftPartnerAttachment->save();
                        // ) {
                            // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                        // }
                    }
                }
    
                // other attachments
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
            
                            $draftPartnerAttachment = new DraftPartnerAttachment;
                            $draftPartnerAttachment->draft_partner_id = $id;
                            $draftPartnerAttachment->document_name = $request->input('OthersDescription'.$d);
                            $draftPartnerAttachment->document_image = $filenameToStore;
                            $draftPartnerAttachment->document_id = 7;
                            // if (!
                            $draftPartnerAttachment->save();
                            // ) {
                                // return $result = array('success' => false, 'message' => 'Error on saving draft!');
                            // }
                        }
                    }
                }
    
                // Draft Lead Comment
                if ($request->note) {
                    $leadsComment = new DraftLeadComment;
                    $leadsComment->draft_partner_id = $id;
                    $leadsComment->comment = $request->note;
                    $leadsComment->parent_id = -1;
                    $leadsComment->create_by = auth()->user()->username;
                    $leadsComment->user_id = auth()->user()->id;
                    $leadsComment->save();
                }
            }
        });

        return $result = array('success' => true, 'message' => 'Draft Saved!');
    }

    public function deleteDraftApplicant(Request $request) 
    {
        $id = $request->partner_id;
        $update = DraftPartner::find($id);
        if ($update->delete()) {
            return $result = array('success' => true, 'message' => 'Applicant deleted!');
        } else {
            return $result = array('success' => false, 'message' => 'Error on deleting applicant!');    
        }
    }

    public function draftBranch($id, $type_id) 
    {
        $c1 = Access::hasPageAccess('branch','add',true);
        $access = session('all_user_access');

        if ($c1) {
            $draft = DraftPartner::with('draftPartnerLanguage')
                ->with('draftPartnerContacts')
                ->with('draftPartnerAttachments')
                ->where('id', $id)
                ->where('partner_type_id', 9)
                ->firstOrFail();

            $ownerships = Ownership::where('status','A')->orderBy('name','asc')->get();
            $countries = Country::where('status','A')->where('display_on_merchant', 1)->get();
            $documents = Document::where('status','A')
                ->whereIn('id', [1,2,6])
                ->whereNotIn('id', [7])
                ->orderBy('sequence','asc')
                ->get();
            $states = State::where('country','US')->orderBy('abbr')->get();
            $language = Language::where('status','A')->get();
    
            $is_internal = auth()->user()->is_original_partner == 0 ? true : false;
            $checkPartnerType = Partner::find(auth()->user()->reference_id);
            if(isset($checkPartnerType)){
                $is_internal = $checkPartnerType->partner_type->name == 'COMPANY' ? true : $is_internal;
            }
    
            $systemUser = false;
            $userTypeIds = explode(',', auth()->user()->user_type_id);
            foreach ($userTypeIds as $id) {
                if ( UserType::find($id)->create_by == 'SYSTEM' ) {
                    $systemUser = true;
                    break;
                }
            }
    
            $userDepartment = User::find(auth()->user()->id)->department->description;
    
            $partners_id = Partner::where('partner_id_reference',auth()->user()->username)->first();
            if (auth()->user()->reference_id == -1) {
                $partner_id = auth()->user()->company_id;
            } else if ($partners_id) {
                $partner_id = $partners_id->id;
            } else {
                $partner_id = auth()->user()->reference_id;
            }
    
            $partner_access="";
            $admin_access = isset($access['admin']) ? $access['admin'] : "";
        
            if (strpos($admin_access, 'super admin access') === false){
                $partner_access = Partner::get_partners_access($id);
            }
            if ($partner_access==""){$partner_access=$partner_id;}
            $upline = Partner::get_downline_partner($partner_id,$partner_access,3);
    
            $formUrl = '/merchants/branchStore';

            $hasFiles = count($draft->draftPartnerAttachments) > 0 ? 1: 0;

            $access = session('all_user_access');
            $userAccess = isset($access['draft applicants']) ? $access['draft applicants'] : "";
            $canSaveAsDraft = (strpos($userAccess, 'draft applicants list') === false) ? false : true;

            $businessTypeGroups = Cache::get('business_types'); 
            $mid = DraftPartnerMid::where('partner_id',$draft->id)->get();
            $systems = PartnerSystem::where('status','A')->get();
            $initialCities = UsZipCode::where('state_id', 1)->orderBy('city')->get();

            return view('draft.editBranch', compact('upline',  
                'ownerships', 'countries', 'documents', 'systemUser', 'language',
                'userDepartment','is_internal','draft','states', 'formUrl', 
                'hasFiles', 'canSaveAsDraft', 'businessTypeGroups','mid','systems',
                'initialCities'));
        } else {
            return redirect('/')->with('failed', 'You have no access to that page.');
        }
    }

    public function draftMerchant($id, $type_id) 
    {
        $c1 = Access::hasPageAccess('merchant','add',true);
        $access = session('all_user_access');

        if ($c1) {
            $draft = DraftPartner::with('draftPartnerLanguage')
                ->with('draftPartnerContacts')
                ->with('draftPartnerAttachments')
                ->where('id', $id)
                ->where('partner_type_id', 3)
                ->firstOrFail();

            $ownerships = Ownership::where('status','A')->orderBy('name','asc')->get();
            $countries = Country::where('status','A')->where('display_on_merchant', 1)->get();
            $documents = Document::where('status','A')
                ->whereIn('id', [1,2,6])
                ->whereNotIn('id', [7])
                ->orderBy('sequence','asc')
                ->get();
            $states = State::where('country','US')->orderBy('abbr')->get();
            $language = Language::where('status','A')->get();
    
            $is_internal = auth()->user()->is_original_partner == 0 ? true : false;
            $checkPartnerType = Partner::find(auth()->user()->reference_id);
            if(isset($checkPartnerType)){
                $is_internal = $checkPartnerType->partner_type->name == 'COMPANY' ? true : $is_internal;
            }
    
            $systemUser = false;
            $userTypeIds = explode(',', auth()->user()->user_type_id);
            foreach ($userTypeIds as $id) {
                if ( UserType::find($id)->create_by == 'SYSTEM' ) {
                    $systemUser = true;
                    break;
                }
            }
    
            $userDepartment = User::find(auth()->user()->id)->department->description;
    
            $pt_access = "";
            $pt_access .= isset($access['company']) ? "7," : "";
            $pt_access .= isset($access['iso']) ? "4," : "";
            $pt_access .= isset($access['sub iso']) ? "5," : "";
            $pt_access .= isset($access['agent']) ? "1," : "";
            $pt_access .= isset($access['sub agent']) ? "2," : "";
            $pt_access = ($pt_access == "") ? "" : substr($pt_access, 0, strlen($pt_access) - 1); 
            $partner_types = PartnerType::get_partner_types($pt_access);
    
            $partners_id = Partner::where('partner_id_reference',auth()->user()->username)->first();
            if (auth()->user()->reference_id == -1) {
                $partner_id = auth()->user()->company_id;
            } else if ($partners_id) {
                $partner_id = $partners_id->id;
            } else {
                $partner_id = auth()->user()->reference_id;
            }
    
            $partner_access="";
            $admin_access = isset($access['admin']) ? $access['admin'] : "";
        
            if (strpos($admin_access, 'super admin access') === false){
                $partner_access = Partner::get_partners_access($id);
            }
            if ($partner_access==""){$partner_access=$partner_id;}
            $upline = Partner::get_downline_partner($partner_id,$partner_access,$pt_access);
    
            $formUrl = '/merchants/store';

            $hasFiles = count($draft->draftPartnerAttachments) > 0 ? 1: 0;

            $access = session('all_user_access');
            $userAccess = isset($access['draft applicants']) ? $access['draft applicants'] : "";
            $canSaveAsDraft = (strpos($userAccess, 'draft applicants list') === false) ? false : true;
            $systems = PartnerSystem::where('status','A')->get();
            $mid = DraftPartnerMid::where('partner_id',$draft->id)->get();
            $businessTypeGroups = Cache::get('business_types');
            $initialCities = UsZipCode::where('state_id', 1)->orderBy('city')->get();
            $paymentProcessor = PaymentProcessor::active()->orderBy('name')->get(); 

            return view('draft.editMerchant', compact('partner_types', 'upline',  
                'ownerships', 'countries', 'documents', 'systemUser', 'language',
                'userDepartment','is_internal','draft','states', 'formUrl', 'hasFiles',
                'canSaveAsDraft','systems','mid','businessTypeGroups','initialCities',
                'paymentProcessor'));
        } else {
            return redirect('/')->with('failed', 'You have no access to that page.');
        }
    }

    public function draftLeadProspect($id, $type_id) 
    {
        $c1 = Access::hasPageAccess('lead','add',true);
        $c2 = Access::hasPageAccess('prospect','add',true);
        $access = session('all_user_access');

        if ($c1 || $c2) {
            $draft = DraftPartner::with('draftLeadComment')
                ->with('draftPartnerContacts')
                ->where('id', $id)
                ->firstOrFail();

            $ownerships = Ownership::where('status','A')->orderBy('name','asc')->get();
            $countries = Country::where('status','A')->where('display_on_others', 1)->get();
            $documents = Document::where('status','A')->orderBy('sequence','asc')->get();
            $states = State::select('abbr as code','name')
                ->where('country','US')
                ->orderBy('abbr')
                ->get();
            $statePH = State::select('abbr as code','name')
                ->where('country','PH')
                ->orderBy('name','asc')
                ->get();
            $stateCN = State::select('abbr as code','name')
                ->where('country','CN')
                ->orderBy('name','asc')
                ->get();
    
            $is_internal = auth()->user()->is_original_partner == 0 ? true : false;
            $checkPartnerType = Partner::find(auth()->user()->reference_id);
            if(isset($checkPartnerType)){
                $is_internal = $checkPartnerType->partner_type->name == 'COMPANY' ? true : $is_internal;
            }
    
            $systemUser = false;
            $userTypeIds = explode(',', auth()->user()->user_type_id);
            foreach ($userTypeIds as $id) {
                if ( UserType::find($id)->create_by == 'SYSTEM' ) {
                    $systemUser = true;
                    break;
                }
            }
    
            $partner_type = PartnerType::get_partner_types($type_id, false,false,true);
    
            $admin_access = isset($access['admin']) ? $access['admin'] : "";
    
            $id = -1;
            if (strpos($admin_access, 'super admin access') !== false) {
                $partner_product_id = '';
            } else {
                $parent_id = auth()->user()->reference_id;
                if ($parent_id == -1) {
                    $parent_id = auth()->user()->reference_id;
                    $partner_product = DB::table('partner_product_accesses')
                        ->where('partner_id',$parent_id)->first();
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
                    if ($partner_product_id == ""){ 
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
    
            $access="";
            if (strpos($admin_access, 'super admin access') === false) { 
                $access = session('all_user_access');
                $pt_access = "";
                $pt_access .= isset($access['company']) ? "7," : "";
                $pt_access .= isset($access['iso']) ? "4," : "";
                $pt_access .= isset($access['sub iso']) ? "5," : "";
                $pt_access .= isset($access['agent']) ? "1," : "";
                $pt_access .= isset($access['sub agent']) ? "2," : "";
                $access = ($pt_access == "") ? "" : substr($pt_access, 0, strlen($pt_access) - 1); 
            }
    
            $upline_partner_type = PartnerType::get_partner_types($access, true);
    
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
            
            $interested_products = " ";
            if ($draft->interested_products) {
                $interested_products = explode(',', $draft->interested_products);
            }

            $access = session('all_user_access');
            $userAccess = isset($access['draft applicants']) ? $access['draft applicants'] : "";
            $canSaveAsDraft = (strpos($userAccess, 'draft applicants list') === false) ? false : true;

            $parent = Partner::select('partner_type_id')->where('id', $draft->parent_id)->first();
            $parentType = $draft->parent_id != -1 ? $parent->partner_type_id : "";

            $businessTypeGroups = Cache::get('business_types'); 
            $initialCities = UsZipCode::where('state_id', 1)->orderBy('city')->get();

            return view('draft.editLeadProspect', compact('partner_type', 
                'upline_partner_type', 'ownerships', 'states', 'countries',
                'products', 'systemUser', 'userDepartment', 'statePH', 'stateCN',
                'interested_products', 'draft', 'canSaveAsDraft', 'parentType',
                'businessTypeGroups','initialCities'));
        } else {
            return redirect('/')->with('failed', 'You have no access to that page.');
        }
    }

    public function draftPartners($id, $type_id) 
    {
        $c1 = Access::hasPageAccess('company','add',true);
        $c2 = Access::hasPageAccess('iso','add',true);
        $c3 = Access::hasPageAccess('sub iso','add',true);
        $c4 = Access::hasPageAccess('agent','add',true);
        $c5 = Access::hasPageAccess('sub agent','add',true);
        $access = session('all_user_access');

        if ($c1 || $c2 || $c3 || $c4 || $c5) {
            $draft = DraftPartner::with('draftPartnerContacts')
                ->with('draftPartnerAttachments')
                ->where('id', $id)
                ->firstOrFail();
    
            $ownerships = Ownership::where('status','A')->orderBy('name','asc')->get();
            $countries = Country::where('status','A')->where('display_on_partner', 1)->get();
            $documents = Document::where('status','A')
                ->whereNotIn('id', [7])
                ->orderBy('sequence','asc')
                ->get();
            $states = State::where('country','US')->orderBy('abbr')->get();
    
            $is_internal = auth()->user()->is_original_partner == 0 ? true : false;
            $checkPartnerType = Partner::find(auth()->user()->reference_id);
            if(isset($checkPartnerType)){
                $is_internal = $checkPartnerType->partner_type->name == 'COMPANY' ? true : $is_internal;
            }
    
            $systemUser = false;
            $userTypeIds = explode(',', auth()->user()->user_type_id);
            foreach ($userTypeIds as $id) {
                if ( UserType::find($id)->create_by == 'SYSTEM' ) {
                    $systemUser = true;
                    break;
                }
            }
    
            $userDepartment = User::find(auth()->user()->id)->department->description;
    
            $pt_access = "";
            $pt_access .= $c1 ? "7," : "";
            $pt_access .= $c2 ? "4," : "";
            $pt_access .= $c3 ? "5," : "";
            $pt_access .= $c4 ? "1," : "";
            $pt_access .= $c5 ? "2," : "";
            $pt_access = ($pt_access == "") ? "" : substr($pt_access, 0, strlen($pt_access) - 1); 
            $partner_types = PartnerType::get_partner_types($pt_access);

            $hasFiles = count($draft->draftPartnerAttachments) > 0 ? 1: 0;

            $access = session('all_user_access');
            $userAccess = isset($access['draft applicants']) ? $access['draft applicants'] : "";
            $canSaveAsDraft = (strpos($userAccess, 'draft applicants list') === false) ? false : true;
            
            $parent = Partner::select('partner_type_id')->where('id', $draft->parent_id)->first();
            $parentType = $draft->parent_id != -1 ? $parent->partner_type_id : "";
            $initialCities = UsZipCode::where('state_id', 1)->orderBy('city')->get();

            return view('draft.editPartner', compact('partner_types', 
                'ownerships', 'countries', 'documents', 'systemUser', 'userDepartment',
                'is_internal','draft','states', 'hasFiles', 'canSaveAsDraft', 'parentType',
                'initialCities'));
        } else {
            return redirect('/')->with('failed', 'You have no access to that page.');
        }
    }
}
