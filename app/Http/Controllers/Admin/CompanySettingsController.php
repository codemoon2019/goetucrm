<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Partner;
use App\Models\ACHConfiguration;
use App\Models\Access;
use App\Models\PartnerType;
use App\Models\TrainingAccess;
use App\Models\PartnerProduct;
use App\Models\Training;
use App\Models\TrainingModule;

use DB;
class CompanySettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:admin,company settings')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partners = Partner::get_partners(-1,7,-1, -1, -1);
        return view("admin.companysettings.list",compact('partners'));
    }

    public function configuration_menu($id)
    {
        return view("admin.companysettings.menu",compact('id'));
    }

    public function ach_info($id)
    {
        $ach  = ACHConfiguration::where('partner_id',$id)->first();
        return response()->json($ach);
    }

    public function ach_update($id,Request $request){
        $message="";
        DB::transaction(function() use ($id,$request){
            if($request->achID == "" || $request->achID == -1)
            {
                $ach = new ACHConfiguration;
                $ach->partner_id = $id;
                $ach->sftp_address = $request->SFTPAddress;
                $ach->sftp_user = $request->SFTPUsername;
                $ach->sftp_password = $request->SFTPPassword;
                $ach->pay_to = $request->PayTo;
                $ach->pay_token = $request->PayToken;
                $ach->create_by = auth()->user()->username;
                $ach->save();            
                $message = "ACH was successfully added";
            }else{
                $ach = ACHConfiguration::find($request->achID);
                $ach->partner_id = $id;
                $ach->sftp_address = $request->SFTPAddress;
                $ach->sftp_user = $request->SFTPUsername;
                $ach->sftp_password = $request->SFTPPassword;
                $ach->pay_to = $request->PayTo;
                $ach->pay_token = $request->PayToken;
                $ach->update_by = auth()->user()->username;
                $ach->save();         
                $message = "ACH was successfully updated";           
            }

        });
        return redirect('/admin/company_settings/configuration_menu/'.$id)->with('success',$message);
    }

    public function training_access($id)
    {
        $partner_types = PartnerType::where('included_in_training',1)->orderBy('sequence')->get();
        $active_partner_tab="";
        $partner_details=array();
        foreach($partner_types as $partner_type){
            if ($active_partner_tab=="") $active_partner_tab = $partner_type->name; 
            $training_accesses = TrainingAccess::where('partner_id',$id)->where('partner_type_id',$partner_type->id)->where('has_access',1)->get();
            $access_id=""; 
            foreach($training_accesses as $training_access)
            {
                $access_id = $access_id . $training_access->training_id.'-'.$training_access->module_code.',';
            }
            if (strpos($access_id, ',') !== false) {
                $access_id = substr($access_id, 0, strlen($access_id) - 1);         
            }

            $training_access = explode(',', $access_id);
            $partner_details[] = array(
                'id' => $partner_type->id,
                'name' =>  $partner_type->name,
                'training_access' => $training_access,


            );
        }  
        //dd($partner_details); 
        $product_id="";
        $products = PartnerProduct::get_partner_products($id);
        foreach($products as $p)
        {
            $product_id = $product_id . $p->parent_id . ",";
        }
        if (strpos($product_id, ',') !== false) {
            $product_id = substr($product_id, 0, strlen($product_id) - 1);          
        }

        $new_trainings = array();
        $partner_product_id = explode(',', $product_id);
        $trainings = Training::whereIn('product_id',$partner_product_id)->orderBy('name')->get();
        //dd($trainings);
        foreach ($trainings as $training) {
            $modules = TrainingModule::where('training_id',$training->id)->orderBy('name')->get();
            $new_trainings[] = array(
                    'id' => $training->id,
                    'name' => $training->name,
                    'modules' => $modules,
                );
        }
        //dd($partner_details);
        return view("admin.companysettings.trainingaccess",compact('id','partner_types','active_partner_tab','partner_details','new_trainings'));
    }

    public function training_update(Request $request, $id)
    {
        $this->validate($request,[
                'training_access' => 'required',
        ]);

        TrainingAccess::where('partner_id', $id)->delete();
        $accesses = substr($request->training_access, 0, strlen($request->training_access) - 1);
        $accesses = explode(",", $accesses);
        foreach($accesses as $access) {
            $training_access_info = explode('-',  $access);
            $training_access = new TrainingAccess;
            $training_access->partner_id = $id;
            $training_access->partner_type_id = $training_access_info[0];
            $training_access->training_id = $training_access_info[1];
            $training_access->module_code = $training_access_info[2];
            $training_access->has_access = 1;
            $training_access->update_by = auth()->user()->username;
            $training_access->save();
       }
       return redirect('/admin/company_settings/'.$id.'/training_access')->with('success','Training access was successfully updated');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
