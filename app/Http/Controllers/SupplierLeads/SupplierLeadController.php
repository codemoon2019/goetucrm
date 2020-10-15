<?php

namespace App\Http\Controllers\SupplierLeads;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierLeads\CreateSupplierLeadRequest;
use App\Http\Requests\SupplierLeads\EditSupplierLeadRequest;
use App\Http\Requests\SupplierLeads\EditSupplierLeadContactRequest;
use App\Http\Requests\SupplierLeads\EditSupplierLeadProductRequest;
use App\Models\Country;
use App\Models\Partner;
use App\Models\PartnerType;
use App\Models\SupplierLead;
use App\Models\SupplierLeadContact;
use App\Models\SupplierLeadProduct;
use App\Models\User;
use App\Models\UserType;
use App\Models\UsZipCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Excel;

class SupplierLeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:supplier leads,view')->only('index');
        $this->middleware('access:supplier leads,create')->only('create', 'store');
    }
    
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $supplierLeads = SupplierLead::whereCompany($companyId)->get();
        return view('supplierLeads.index')->with([
            'supplierLeads' => $supplierLeads
        ]);
    }

    public function create()
    {
        $countries = Country::with(['states' => function($query) {
                $query->select('id', 'name', 'abbr', 'country')
                      ->orderBy('name');
            }])
            ->where('display_on_others', 1)
            ->get();

        $partner_type = PartnerType::get_partner_types('6', false,false,true);
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
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

        $systemUser = false;
        $userTypeIds = explode(',', auth()->user()->user_type_id);
        foreach ($userTypeIds as $id) {
            if (UserType::find($id)->create_by == 'SYSTEM') {
                $systemUser = true;
                break;
            }
        }

        $businessTypeGroups = Cache::get('business_types');
        $userDepartment = User::find(auth()->user()->id)->department->description;

        $initialCities = UsZipCode::where('state_id', 1)->orderBy('city')->get();

        return view('supplierLeads.create')->with([
            'businessTypeGroups' => $businessTypeGroups,
            'countries' => $countries,
            'partner_type' => $partner_type,
            'systemUser' => $systemUser,
            'userDepartment' => $userDepartment,
            'upline_partner_type' => $upline_partner_type,
            'initialCities' => $initialCities,
        ]);
    }

    public function show($id)
    {
        $supplierLead = SupplierLead::find($id);
        $countries = Country::with(['states' => function($query) {
            $query->select('id', 'name', 'abbr', 'country')
                  ->orderBy('name');
            }])
            ->where('display_on_others', 1)
            ->get();
            
        $businessTypeGroups = Cache::get('business_types');
        
        if (!session('is_internal')) {
            return redirect("/supplier-leads/{$id}/summary");
        }
        
        return view('supplierLeads.show')->with([
            'businessTypeGroups' => $businessTypeGroups,
            'countries' => $countries,
            'supplierLead' => $supplierLead,
            'isInternal' => session('is_internal'),
        ]);
    }

    public function showContacts($id)
    {
        $supplierLead = SupplierLead::with('contacts')->find($id);

        if (!session('is_internal')) {
            return redirect("/supplier-leads/{$id}/summary");
        }

        return view('supplierLeads.showContacts')->with([
            'supplierLead' => $supplierLead,
            'isInternal' => session('is_internal'),
        ]);
    }

    public function showProducts($id)
    {
        $supplierLead = SupplierLead::with('products')->find($id);

        if (!session('is_internal')) {
            return redirect("/supplier-leads/{$id}/summary");
        }

        return view('supplierLeads.showProducts')->with([
            'supplierLead' => $supplierLead,
            'isInternal' => session('is_internal'),
        ]);
    }

    public function showOverview($id)
    {
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        $partner_access=-1;

        $supplierLead = SupplierLead::with('contacts')
            ->with('partner')
            ->with('country')
            ->with('state')
            ->find($id);
            
        if (strpos($admin_access, 'super admin access') === false){
            $reference_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
            $partner_access = Partner::get_partners_access($reference_id);
        }
        if ($partner_access==""){$partner_access=$id;}
        $upline = Partner::get_upline_partner($id,$partner_access);
        
        $businessTypeGroups = Cache::get('business_types');
            
        return view('supplierLeads.showOverview')->with([
            'supplierLead' => $supplierLead,
            'upline' => $upline,
            'isInternal' => session('is_internal'),
            'businessTypeGroups' => $businessTypeGroups,
        ]);
    }

    public function store(CreateSupplierLeadRequest $request)
    {
        DB::beginTransaction();
        try {
            $supplierLead = SupplierLead::create([
                'doing_business_as' => $request->doing_business_as,
                'business_name' => $request->business_name,
                'business_type_code' => $request->mcc,
                
                'business_address' => $request->business_address,
                'business_address_2' => $request->business_address_2,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city' => $request->city,
                'zip' => $request->zip,

                'business_phone' => $request->business_phone,
                'extension' => $request->extension,
                'fax' => $request->fax,
                
                'business_phone_2' => $request->business_phone_2,
                'extension_2' => $request->extension_2,
                
                'business_email' => $request->business_email,

                'partner_id' => $request->partner_id,
            ]);

            $supplierLead->contacts()->createMany($request->contacts);
            $supplierLead->products()->createMany($request->products);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with([
                'error' => 'There was an error processing your request'
            ]);
        }

        $message = 'Successfully added Supplier Lead';
        if (isset($request->create_another)) {
            return redirect(route('supplierLeads.create'))->with([
                'success' => $message
            ]);
        }
        
        return redirect(route('supplierLeads.index'))->with([
            'success' => $message
        ]);

    }

    public function update(EditSupplierLeadRequest $request, $id)
    {
        $supplierLead = SupplierLead::find($id);
        $supplierLead->update([
            'doing_business_as' => $request->doing_business_as,
            'business_name' => $request->business_name,
            'business_type_code' => $request->mcc,
            
            'business_address' => $request->business_address,
            'business_address_2' => $request->business_address_2,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'city' => $request->city,
            'zip' => $request->zip,

            'business_phone' => $request->business_phone,
            'extension' => $request->extension,
            'fax' => $request->fax,
            
            'business_phone_2' => $request->business_phone_2,
            'extension_2' => $request->extension_2,
            
            'business_email' => $request->business_email,
        ]);

        return redirect()->back()->with([
            'success' => 'Successfully udpated Supplier Lead'
        ]);
    }

    public function updateContacts(EditSupplierLeadContactRequest $request, $id)
    {
        $processedIds = [];
        $supplierLead = SupplierLead::find($id);
        foreach ($request->contacts as $contact) {
            if (isset($contact['id'])) {
                $supplierLeadContact = SupplierLeadContact::find($contact['id']);
            } else {
                $supplierLeadContact = new SupplierLeadContact;
                $supplierLeadContact->supplier_lead_id = $id;
            }

            $supplierLeadContact->first_name = $contact['first_name'];
            $supplierLeadContact->middle_name = $contact['middle_name'];
            $supplierLeadContact->last_name = $contact['last_name'];
            $supplierLeadContact->position = $contact['position'];
            $supplierLeadContact->contact_phone = $contact['contact_phone'];
            $supplierLeadContact->contact_phone_2 = $contact['contact_phone_2'];
            $supplierLeadContact->fax = $contact['fax'];
            $supplierLeadContact->mobile = $contact['mobile'];
            $supplierLeadContact->save();

            $processedIds[] = $supplierLeadContact->id;
        }

        $supplierLead->contacts()
            ->whereNotIn('id', $processedIds)
            ->each(function($contact) {
                $contact->delete();
            });

        return redirect()->back()->with([
            'success' => 'Successfully updated Contacts'
        ]);
    }

    public function updateProducts(EditSupplierLeadProductRequest $request, $id)
    {
        $processedIds = [];
        $supplierLead = SupplierLead::find($id);
        foreach ($request->products as $product) {
            if (isset($product['id'])) {
                $supplierLeadProduct = SupplierLeadProduct::find($product['id']);
            } else {
                $supplierLeadProduct = new SupplierLeadProduct;
            }

            $supplierLeadProduct->name = $product['name'];
            $supplierLeadProduct->price = $product['price'];
            $supplierLeadProduct->description = $product['description'];
            $supplierLeadProduct->supplier_lead_id = $supplierLead->id;
            $supplierLeadProduct->save();

            $processedIds[] = $supplierLeadProduct->id;
        }

        $supplierLead->products()
            ->whereNotIn('id', $processedIds)
            ->each(function($product) {
                $product->delete();
            });

        return redirect()->back()->with([
            'success' => 'Successfully updated Products'
        ]);
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

                if(!empty($data) && $data->count()){
                    //Prelim
                    foreach ($data as $key => $value) {
                        $skip = false;
                        if ($value->dba == '') {
                            $logs[] = "Skipping ".$value->dba." DBA must have a value.";
                            $skip = true;
                        }

                        $exist = DB::table('supplier_leads')->select('id')->where('doing_business_as',$value->dba)->first();
                        if ($exist) {
                            $logs[] = "Skipping ".$value->dba." Supplier Leads Record already exist.";
                            $skip = true;
                            goto skip;
                        }


                        $upline = DB::table('partners')->select('id')->where('partner_id_reference',$value->upline)->whereIn('partner_type_id',Array(1,2,4,5,7))->first();
                        if (!$upline) {
                            $logs[] = "Skipping ".$value->dba." due to invalid upline.";
                            $skip = true;
                        }

                        $businessType = DB::table('business_types')->select('mcc')->where('mcc',$value->mcc)->first();
                        if (!$businessType) {
                            $logs[] = "Skipping ".$value->dba." due to invalid MCC.";
                            $skip = true;
                        }

                        if ($value->country == '' || is_numeric($value->country)) {
                            $logs[] = "Skipping ".$value->dba." Country must have a value or must be valid.";
                            $skip = true;
                        }
                        $country = DB::table('countries')->select('id','iso_code_2','country_calling_code')->where('name',$value->country)->first();
                        if (!$country) {
                            $logs[] = "Skipping ".$value->dba." due to invalid Country.";
                            $skip = true;
                        }

                        if ($value->business_address_1 == '') {
                            $logs[] = "Skipping ".$value->dba." Business Address 1 must have a value.";
                            $skip = true;
                        }

                        if ($value->city == '') {
                            $logs[] = "Skipping ".$value->dba." City must have a value.";
                            $skip = true;
                        }
                        if ($value->state == '') {
                            $logs[] = "Skipping ".$value->dba." State must have a value.";
                            $skip = true;
                        }

                        $state = DB::table('states')->select('id')->where('abbr',$value->state)->where('country',$country->iso_code_2)->first();
                        if (!$state) {
                            $logs[] = "Skipping ".$value->dba." due to invalid State.";
                            $skip = true;
                        }

                        if ($value->zip == '') {
                            $logs[] = "Skipping ".$value->dba." Zip must have a value.";
                            $skip = true;
                        }

                        if ($value->business_phone_1 == '') {
                            $logs[] = "Skipping ".$value->dba." Business Phone 1 must have a value.";
                            $skip = true;
                        }

                        if ($value->first_name == '') {
                            $logs[] = "Skipping ".$value->dba." First Name must have a value.";
                            $skip = true;
                        }

                        if ($value->last_name == '') {
                            $logs[] = "Skipping ".$value->dba." Last Name must have a value.";
                            $skip = true;
                        }

                        if ($value->position == '') {
                            $logs[] = "Skipping ".$value->dba." Position must have a value.";
                            $skip = true;
                        }

                        if ($value->contact_mobile == '') {
                            $logs[] = "Skipping ".$value->dba." Contact Mobile must have a value.";
                            $skip = true;
                        }

                        if ($value->product == '') {
                            $logs[] = "Skipping ".$value->dba." Product must have a value.";
                            $skip = true;
                        }
                        if ($value->price == ''  || !is_numeric($value->price)) {
                            $logs[] = "Skipping ".$value->dba." Price must have a value.";
                            $skip = true;
                        }
                        if ($value->product_description == '') {
                            $logs[] = "Skipping ".$value->dba." Product Description must have a value.";
                            $skip = true;
                        }

                        $len = 12;
                        if (trim(strtolower($value->country)) == 'china') {
                            $len = 13;
                            if(!preg_match("/^[0-9]{11}$/", $value->business_phone_1)) {
                                $logs[] = "Skipping  ".$value->dba.", invalid business phone 1 format.";
                                $skip = true;
                            }
                            if ($value->fax != '') {
                                if(!preg_match("/^[0-9]{11}$/", $value->fax)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid fax format.";
                                    $skip = true;
                                }
                            }

                            if ($value->business_phone_2 != '') {
                                if(!preg_match("/^[0-9]{11}$/", $value->business_phone_2)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid business phone 2 format.";
                                    $skip = true;
                                }
                            }
                            if ($value->contact_mobile != '') {
                                if(!preg_match("/^[0-9]{11}$/", $value->contact_mobile)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid contact mobile format.";
                                    $skip = true;
                                }
                            }
                            if ($value->contact_phone_1 != '') {
                                if(!preg_match("/^[0-9]{11}$/", $value->contact_phone_1)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid contact phone 1 format.";
                                    $skip = true;
                                }
                            }
                            if ($value->contact_phone_2 != '') {
                                if(!preg_match("/^[0-9]{11}$/", $value->contact_phone_2)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid contact phone 2 format.";
                                    $skip = true;
                                }
                            }
                            if ($value->contact_fax != '') {
                                if(!preg_match("/^[0-9]{11}$/", $value->contact_fax)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid contact fax format.";
                                    $skip = true;
                                }
                            }
                        } elseif (trim(strtolower($value->country)) == 'united states'
                            || trim(strtolower($value->country)) == 'philippines') {
                            if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->business_phone_1)) {
                                $logs[] = "Skipping  ".$value->dba.", invalid business phone 1 format.";
                                $skip = true;
                            }
                            if ($value->fax != '') {
                                if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->fax)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid fax format.";
                                    $skip = true;
                                }
                            }
                            if ($value->contact_mobile != '') {
                                if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_mobile)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid contact mobile format.";
                                    $skip = true;
                                }
                            }
                            if ($value->business_phone_2 != '') {
                                if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->business_phone_2)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid business phone 2 format.";
                                    $skip = true;
                                }
                            }
                            if ($value->contact_phone_1 != '') {
                                if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_phone_1)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid contact phone 1 format.";
                                    $skip = true;
                                }
                            }
                            if ($value->contact_phone_2 != '') {
                                if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_phone_2)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid contact phone 2 format.";
                                    $skip = true;
                                }
                            }
                            if ($value->contact_fax != '') {
                                if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $value->contact_fax)) {
                                    $logs[] = "Skipping  ".$value->dba.", invalid contact fax format.";
                                    $skip = true;
                                }
                            }
                        }

                        if($skip){goto skip;}

                        $lead = new SupplierLead;
                        $lead->doing_business_as = $value->dba;
                        $lead->business_name = $value->legal_name;
                        $lead->business_type_code = $value->mcc;
                        $lead->business_address = $value->business_address_1;
                        $lead->business_address_2 = $value->business_address_2;
                        $lead->country_id = $country->id;
                        $lead->state_id = $state->id;
                        $lead->city = $value->city;
                        $lead->zip = $value->zip;
                        $lead->business_phone = $value->business_phone_1;
                        $lead->business_phone_2 = $value->business_phone_2;
                        $lead->fax = $value->fax;
                        $lead->business_email = $value->email;
                        $lead->status = "A";
                        $lead->partner_id = $upline->id;

                        if (!$lead->save()) {
                            $logs[] = "Unable to create supplier lead."; 
                            goto skip;
                        }

                        $leadContact = new SupplierLeadContact;
                        $leadContact->first_name = $value->first_name;
                        $leadContact->middle_name = $value->middle_name;
                        $leadContact->last_name = $value->last_name;
                        $leadContact->mobile = $value->contact_mobile;
                        $leadContact->contact_phone = $value->contact_phone_1;
                        $leadContact->contact_phone_2 = $value->contact_phone_2;
                        $leadContact->fax = $value->contact_fax;
                        $leadContact->supplier_lead_id = $lead->id;
                        $leadContact->status = "A";

                        if (!$leadContact->save()) {
                            $logs[] = "Unable to create supplier lead contact."; 
                            goto skip;
                        }

                        $leadProduct = new SupplierLeadProduct;
                        $leadProduct->name = $value->product;
                        $leadProduct->price = $value->price;
                        $leadProduct->description = $value->product_description;
                        $leadProduct->supplier_lead_id = $lead->id;
                        $leadProduct->status = "A";

                        if (!$leadProduct->save()) {
                            $logs[] = "Unable to create supplier lead product."; 
                            goto skip;
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

}