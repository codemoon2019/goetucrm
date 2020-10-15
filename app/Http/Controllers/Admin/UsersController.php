<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Mail;

use Cache;
use DB;

use App\Models\Access;
use App\Models\User;
use App\Models\UserType;
use App\Models\AccessControlList;
use App\Models\Country;
use App\Models\PartnerCompany;
use App\Models\PartnerContact;
use App\Models\Partner;
use App\Models\PartnerType;
use App\Models\UserCompany;
use App\Models\UserTypeReference;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:users,view')->only('index', 'show');
        $this->middleware('access:users,reset')->only('reset');
        $this->middleware('access:users,add')->only('create', 'store');
        $this->middleware('access:users,edit')->only('edit', 'update');
        $this->middleware('access:users,delete')->only('cancel');
    }

    public function index() 
    {
        $userSearch = true;
        $advanceSearchLabel = "Users";
        $access = session('all_user_access');
        $admin_access = isset($access['users']) ? $access['users'] : "";
        $partner_id = auth()->user()->reference_id;
        $parent_id = auth()->user()->company_id;
       
        if (strpos($admin_access, 'full access') === false){
            $departments = UserType::where('status', 'A')
                ->isNonSystem()
                ->where('company_id', $parent_id)
                ->orderBy('description')
                ->get();
            $companies = Partner::get_downline_partner($partner_id,$parent_id,7);
            $is_partner = 1;
        } else {
            $departments = UserType::where('status', 'A')
                ->isNonSystem()
                ->orderBy('description')
                ->get();
            $companies = Partner::get_downline_partner($partner_id,$parent_id,7);
            $is_partner = 0;    
        }


        return view("admin.users.list",
            compact(
                'departments',
                'userSearch',
                'advanceSearchLabel',
                'companies',
                'is_partner'
            )
        );
    }

    public function create()
    {
        $id=-1;
        if(isset($_GET['id']))
        {
            $id=$_GET['id'];
        }
        $countries = Country::where('status','A')->where('display_on_users', 1)/* ->orderBy('name','asc') */->get();
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'super admin access') === false){
            $departments = UserType::where('status','=','A')->where('company_id',auth()->user()->company_id)->orderBy('description')->get();
            $companies = Partner::where('id', auth()->user()->company_id)->where('status','A')->get();
            $is_partner = 1;
        } else {
            $departments = UserType::where('status','=','A')->orderBy('description')->get();
            $companies = Partner::where('partner_type_id', 7)->where('status','A')->get();
            $is_partner = 0;    
        }

        return view('admin.users.create',compact('departments','countries','is_partner','companies','id'));
        
    }

    public function companyList()
    {
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'super admin access') === false){
            $companies = Partner::where('id', auth()->user()->company_id)->where('status','A')->get();
        } else {
            $companies = Partner::where('partner_type_id', 7)->where('status','A')->get();  
        }
        return Array('data' => $companies);
    }

    public function departmentList(Request $request)
    {
        $departments = UserType::where('status','=','A')->where('company_id',$request->id)->orderBy('description')->get();
        return Array('data' => $departments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   

        DB::transaction(function() use ($request, &$user){
            $this->validate($request,[
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email_address' => 'nullable|string|email|max:255|unique:users,email_address,D,status',
                    'password' => 'required|string|min:6|confirmed',
                    'mobile_number' => 'nullable|string|max:255|unique:users,mobile_number,D,status',
                    'direct_office_number' => 'nullable',
                    'direct_office_number_extension' => 'required_with:direct_office_number',
                    'dob' => 'required',
                    'departments' => 'required',
            ]);
            $departments = substr($request->departments, 0, strlen($request->departments) - 1);
            $dob = date("Y-m-d", strtotime($request->input('dob')));
            $country = Country::where('name',$request->input('txtCountry'))->first();
            $user = new User;
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email_address = $request->input('email_address');
            $user->password = bcrypt($request->input('password'));
            $user->user_type_id = -1;
            $user->mobile_number = $request->input('mobile_number');
            $user->business_phone1 = $request->input('direct_office_number');
            $user->extension = $request->input('direct_office_number_extension');
            $user->status = $request->input('status');
            $user->dob = $dob;
            $user->country = $request->input('txtCountry');
            $user->country_code = $country->country_calling_code;
            $user->created_at = date('Y-m-d H:i:s');
            $user->create_by = auth()->user()->username;
            $user->username = time();
            

            $user->save();
            $user_id = $user->id;

            if (isset($request->companies)) {
                foreach ($request->companies as $company) {
                    $user_company = New UserCompany;
                    $user_company->user_id = $user_id;
                    $user_company->company_id = $company;
                    $user->reference_id = $company;
                    $user->save();
                    $user_company->save();
                } 
            } else {
                $user_company = New UserCompany;
                $user_company->user_id = $user_id;
                $user_company->company_id = auth()->user()->company_id;
                $user->reference_id = auth()->user()->company_id;
                $user->save();
                $user_company->save();
            }            

            $departments = explode(',', $departments);
            foreach ($departments as $department) {
                $user_type = New UserTypeReference;
                $user_type->user_id = $user_id;
                $user_type->user_type_id = $department;
                $user_type->save();
            }             

            $username = 'U1'.sprintf('%07d', $user_id);
            $user = User::find($user_id);
            $user->username = $username;

            if ($request->hasFile("profileImage")) {
                $attachment = $request->file('profileImage');
                $fileName = pathinfo($attachment->getClientOriginalName(),PATHINFO_FILENAME);
                $extension = $attachment->getClientOriginalExtension();
                $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
                $storagePath = Storage::disk('public')->putFileAs('user_profile', $attachment,  $filenameToStore, 'public');
                $user->image = '/storage/user_profile/'.$filenameToStore;
            }


            $user->save();

            if (isset($user->email_address)) {
                $data = array(
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'password' => $request->input('password'),
                    'email_address' => $user->email_address,
                    'username' => $user->username,
                );
    
                Mail::send(['html'=>'mails.accountcreation'],$data,function($message) use ($data){
    
                    $message->to($data['email_address'],$data['first_name'].' '.$data['last_name']);
                    $message->subject('[GoETU] Account Creation');
                    $message->from('no-reply@goetu.com');
                });
    
                if (Mail::failures()) {
                    return redirect('/admin/users')->with('failed','Failed to send email.');
                }
            } else {
                $mobile_number = $user->country_code.'-'.$user->mobile_number;
                $default_password = $request->input('password');
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

        });

        return redirect('/admin/users')->with([
            'success' => 'User added',
            'newUsername' => $user->username,
            'newUserId' => $user->id,
            'newEmail' => $user->email_address,
            'newFullName' => $user->first_name . ' ' . $user->last_name,
            'newImg' => $request->hasFile("profileImage") ? $user->image : '/images/agent.png',
        ]);
    }

    public function edit($id)
    {
        $user = User::find($id);
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $departments = UserType::where('status','=','A')->where('company_id',auth()->user()->company_id)->orderBy('description')->get();
            $companies = Partner::where('id', auth()->user()->company_id)->where('status','A')->get();
            $is_partner = 1;
        } else {
            $departments = UserType::where('status','=','A')->orderBy('description')->get();
            $companies = Partner::where('partner_type_id', 7)->where('status','A')->get();
            $is_partner = 0;    
        }

        $countries = Country::where('status','A')->where('display_on_users', 1)/* ->orderBy('name','asc') */->get();
        return view('admin.users.edit',compact('departments','user','countries','is_partner','companies'));
    }

    public function show($id)
    {
        $user = User::find($id);
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
       
        if (strpos($admin_access, 'super admin access') === false){
            $departments = UserType::where('status','=','A')->where('company_id',auth()->user()->company_id)->orderBy('description')->get();
            $companies = Partner::where('id', auth()->user()->company_id)->where('status','A')->get();
            $is_partner = 1;
        } else {
            $departments = UserType::where('status','=','A')->orderBy('description')->get();
            $companies = Partner::where('partner_type_id', 7)->where('status','A')->get();
            $is_partner = 0;    
        }
        $countries = Country::where('status','A')->where('display_on_users', 1)/* ->orderBy('name','asc') */->get();
        return view('admin.users.show',compact('departments','user','countries','is_partner','companies'));
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
        $this->validate($request,[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email_address' => 'nullable|string|email|max:255|unique:users,email_address,'.$id,
            'mobile_number' => 'nullable|string|max:255|unique:users,mobile_number,'.$id,
            'direct_office_number' => 'nullable',
            'direct_office_number_extension' => 'required_with:direct_office_number',
            'dob' => 'required',
        ]);

        $departments = substr($request->departments, 0, strlen($request->departments) - 1);
        
        $dob = date("Y-m-d", strtotime($request->input('dob')));
        $country = Country::where('name',$request->input('txtCountry'))->first();

        $user = User::find($id);

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email_address = $request->input('email_address');

        // if ($user->department->create_by != 'SYSTEM') {
        //    $user->user_type_id = $departments; 
        // }

        $user->mobile_number = $request->input('mobile_number');
        $user->business_phone1 = $request->input('direct_office_number');
        $user->extension = $request->input('direct_office_number_extension');
        $user->status = $request->input('status');
        $user->dob = $dob;
        $user->country = $request->input('txtCountry');
        $user->country_code = $country->country_calling_code;
        $user->updated_at = date('Y-m-d H:i:s');
        $user->update_by = auth()->user()->username;
        
        // if (isset($request->company)) {
        //     $user->company_id = $request->company;
        //     if($user->department->create_by!=='SYSTEM')
        //     {
        //         $user->reference_id = $request->company;    
        //     } 
        // } else {
        //     if($user->department->create_by!=='SYSTEM')
        //     {
        //         $user->reference_id = auth()->user()->reference_id;  
        //     }   
        // }

        if ($request->hasFile("profileImage")) {
            $attachment = $request->file('profileImage');
            $fileName = pathinfo($attachment->getClientOriginalName(),PATHINFO_FILENAME);
            $extension = $attachment->getClientOriginalExtension();
            $filenameToStore = str_replace(" ", "", $fileName).'_'. time() . '.'.$extension;
            $storagePath = Storage::disk('public')->putFileAs('user_profile', $attachment,  $filenameToStore, 'public');
            $user->image = '/storage/user_profile/'.$filenameToStore;
        }

        $user->save();

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'super admin access') === false){
            if($request->isSystemGroup == "0"){
                if (isset($request->companies)) {
                    foreach ($request->companies as $company) {
                        $deletedRows = UserCompany::where('user_id', $id)->where('company_id',$company)->delete();
                        $ut = UserType::where('company_id',$company)->get();
                        foreach($ut as $u){
                            $deletedRows = UserTypeReference::where('user_id', $id)->where('user_type_id',$u->id)->delete();
                        }
                    }
                    foreach ($request->companies as $company) {
                        $user_company = New UserCompany;
                        $user_company->user_id = $id;
                        $user_company->company_id = $company;
                        $user_company->save();
                    } 
                } else {
                    $deletedRows = UserCompany::where('user_id', $id)->where('company_id',auth()->user()->company_id)->delete();
                    $ut = UserType::where('company_id',auth()->user()->company_id)->get();
                    foreach($ut as $u){
                        $deletedRows = UserTypeReference::where('user_id', $id)->where('user_type_id',$u->id)->delete();
                    }
                    $user_company = New UserCompany;
                    $user_company->user_id = $id;
                    $user_company->company_id = auth()->user()->company_id;
                    $user_company->save();
                }            
            
                $departments = explode(',', $departments);
                foreach ($departments as $department) {
                    $user_type = New UserTypeReference;
                    $user_type->user_id = $id;
                    $user_type->user_type_id = $department;
                    $user_type->save();
                }  
            }

        }else{
            if($request->isSystemGroup == "0"){
                $deletedRows = UserCompany::where('user_id', $id)->delete();
                if (isset($request->companies)) {
                    foreach ($request->companies as $company) {
                        $user_company = New UserCompany;
                        $user_company->user_id = $id;
                        $user_company->company_id = $company;
                        $user_company->save();
                    } 
                } else {
                    $user_company = New UserCompany;
                    $user_company->user_id = $id;
                    $user_company->company_id = auth()->user()->company_id;
                    $user_company->save();
                }            
            
                $deletedRows = UserTypeReference::where('user_id', $id)->delete();
                $departments = explode(',', $departments);
                foreach ($departments as $department) {
                    $user_type = New UserTypeReference;
                    $user_type->user_id = $id;
                    $user_type->user_type_id = $department;
                    $user_type->save();
                }  
            }

        }
        


        if ( isset($user->reference_id) && $user->is_original_partner == 1) {
            /*$partner = PartnerCompany::where('partner_id',$user->reference_id)->first();
            if(isset($partner)){
                $partner->email = $request->input('email_address');
                $partner->save();
            }*/
            
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
        
        return redirect('/admin/users')->with([
            'success' => 'User updated',
            'newUsername' => $user->username,
            'newUserId' => $user->id,
            'newEmail' => $user->email_address,
            'newFullName' => $user->first_name . ' ' . $user->last_name,
            'newImg' => $request->hasFile("profileImage") ? $user->image : '/images/agent.png',
        ]);
    }

    public function reset(Request $request)
    {
        $id = $request->id;
        $via="";
        $new_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 10);
        
        $user = User::find($id);
        $user->password = bcrypt($new_password);
        $user->save();

        if (preg_match("/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/",$user->email_address)) {
            $data = array(
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'password' => $new_password,
                'email_address' => $user->email_address,
                'username' => $user->username,
            );

            Mail::send(['html'=>'mails.resetpassword'],$data,function($message) use ($data){

                $message->to($data['email_address'],$data['first_name'].' '.$data['last_name']);
                $message->subject('[GoETU] Password Reset');
                $message->from('no-reply@goetu.com');
            });

            if (Mail::failures()) {
                return redirect('/admin/users')->with('failed','Failed to send email.');
            }
            $via="email";
        } else {
            $mobile_number = $user->country_code.'-'.$user->mobile_number;
            $params = array(
                'user'      => 'GO3INFOTECH',
                'password'  => 'TA0828g3i',
                'sender'    => 'GoETU',
                'SMSText'   => 'Hi '.$user->first_name. ' ' .$user->last_name. ', GoETU Password Reset. Your password is: ' .$new_password,
                'GSM'       => str_replace("-","",$mobile_number),
            );
            $send_url = 'https://api2.infobip.com/api/v3/sendsms/plain?' . http_build_query($params);
            $send_response = file_get_contents($send_url);

            if (!($send_response != '') || 
                strstr($send_response, '<status>0</status>') === false) {
                return redirect('/login')->with('failed','Failed to send message.');
            }
            $via="mobile number";
        }
        // return redirect('/admin/users')->with('success','New password was successfully sent to '.$via.'.');
        return response()->json(array(
            'success'                   => true, 
            'msg'                       => 'New password was successfully sent to '.$via.'.', 
        ), 200); 

    }

    public function cancel($id)
    {
        $user = User::find($id);
        $user->status = 'D';
        $user->save();

        return redirect('/admin/users')->with('success','User was successfully deleted');
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        $user->status = 'D';
        $user->save();

        return response()->json(array(
            'success'                   => true, 
            'msg'                       => "User has been deleted!", 
        ), 200); 
    }


    public function data(Datatables $datatables)
    {
        $access = session('all_user_access');
        // if (strpos($access['users'], 'full access') !== false) {
        //     $users = User::getAllUsers();
        // } else {
        //     $users = User::getAllUsers(auth()->user()->company_id);    
        // }
        
        $new_users = array();
        $users = User::whereRaw("LEFT(username, 1) = 'U'")->get();
        foreach($users as $user){
           $proceed = true;
           $company_text = "";
           if (strpos($access['users'], 'full access') === false) {
                $proceed = false;
                foreach($user->companies as $company){
                    if($company->company_id == auth()->user()->company_id){
                        $proceed = true;
                        $company_text .= $company->company_detail->company_name.'<br>';
                        break;
                    }
                }
           }else{
                foreach($user->companies as $company){
                    if(isset($company->company_detail->company_name)){
                        $company_text .= $company->company_detail->company_name.'<br>';
                    }
                }
           }

           if($proceed){
                $department_text="";
                if (strpos($access['users'], 'full access') === false) {
                    foreach($user->departments as $dep){
                        if(isset($dep->user_type->description)){
                            if($dep->user_type->company_id == auth()->user()->company_id || $dep->user_type->create_by == 'SYSTEM'){
                                $department_text .= $dep->user_type->description.'<br>';
                            }
                        }
                    }
                }else{
                    foreach($user->departments as $dep){
                        if(isset($dep->user_type->description)){
                            $department_text .= $dep->user_type->description.'<br>';
                        }
                    }
                }
                switch ($user->status) {
                    case 'A':
                        $status = 'Active';
                        break;
                    case 'I':
                        $status = 'Inactive';
                        break;
                    default:
                        $status = 'Terminated';
                        break;
                }                    

                $new_users[] =array(
                    'id' => $user->id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email_address,
                    'departments' =>  $department_text,
                    'status' => $status,
                    'country' => $user->country,
                    'company' => $company_text,
                    'is_online' => $user->is_online,
                );            
           }

        }
        return $datatables->collection($new_users)
                          ->editColumn('first_name', function ($user) {
                              return '<a>' . $user['first_name'] . '</a>';

                          })
                          ->editColumn('status', function ($user) use ($access) {
                              $status = $user['status'];
                              if(strpos($access['users'], 'activate switch') !== false && 
                                ($user['status'] == 'Active' || $user['status'] == 'Inactive')) {
                                $stat = $user['status'] == 'Active' ? "A" : "I";
                                $status = '<label class="switch switch-active">';
                                if ($user['status'] == 'Active') {
                                    $status .= '<input type="checkbox" checked data-url="users" data-uid="'.$user['id'].'" data-stat="'.$stat.'" onchange="return activate(this)">';
                                } else if ($user['status'] == 'Inactive') {
                                    $status .= '<input type="checkbox" data-url="users" data-uid="'.$user['id'].'" data-stat="'.$stat.'" onchange="return activate(this)">';
                                }
                                $status .= '<div class="slider slider-rec btn">
                                    <span class="on">Active</span><span class="off">Inactive</span>
                                    </div>
                                    </label>';
                            }
                            return $status;
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
                                   $edit = '<a href="/admin/users/'.$user['id'].'/edit" class="btn btn-info btn-sm mr-1 mb-1">Edit</a>';
                                   /**
                                    * Remove view button when user has edit access
                                    */
                                    $view="";
                                }
                                if(strpos($access['users'], 'delete') !== false) {
                                   $delete = '<a href="/admin/users/'.$user['id'].'/cancel" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('.$message.')">Delete</a>';
                                   $delete = '<button onclick="deleteUser('.$user['id'].')"" class="btn btn-danger btn-sm" >Delete</button>';
                                }
                                if(strpos($access['users'], 'reset') !== false) {
                                   $message="'Reset password?'";
                                   $reset = '<a href="/admin/users/'.$user['id'].'/reset" class="btn btn-primary btn-sm mb-1" 
                                   onclick="return confirm('.$message.')">Reset</a>';
                                   $reset = '<button onclick="resetPassword('.$user['id'].')"" class="btn btn-primary btn-sm mb-1" >Reset Password</button>';
                                }
                                if(strpos($access['users'], 'set as offline') !== false && $user['is_online'] == 1) {
                                    $offline = '<a href="/admin/users/'.$user['id'].'/offline" class="btn btn-warning btn-sm">Set as Offline</a>';
                                    $offline = '<button onclick="setAsOffline('.$user['id'].')"" class="btn btn-warning btn-sm mb-1" >Set as Offline</button>';
                                }
                                return "{$view}{$edit}{$delete}{$reset}{$offline}";
                          })
                          ->rawColumns(['first_name', 'status', 'action','company', 'departments'])
                          ->make(true);
    }

    public function advance_data_search(Datatables $datatables, $id, $company_id, $system_user) 
    {
        if ($id != "-1"){
            $id = substr($id, 0, strlen($id)-1);     
        }
        $access = session('all_user_access');
        // if (strpos($access['users'], 'full access') !== false) {
        //     $users = User::advancedSearchByDepartments($id,$company_id,$system_user);
        // } else {
        //     $users = User::advancedSearchByDepartments($id,auth()->user()->company_id,$system_user);    
        // }
        //dd($users);
        $new_users = array();
        $users = User::whereRaw("LEFT(username, 1) = 'U'")->get();
        foreach($users as $user){
           $proceed = false;
           $company_text = "";
           if (strpos($access['users'], 'full access') === false) {
                foreach($user->companies as $company){
                    if($company->company_id == auth()->user()->company_id){
                        $proceed = true;
                        $company_text .= $company->company_detail->company_name.'<br>';
                        break;
                    }
                }
           }else{
                foreach($user->companies as $company){
                    if($company_id == "-1"){
                        $proceed = true;
                        if(isset($company->company_detail->company_name)){
                            $company_text .= $company->company_detail->company_name.'<br>';
                        }
                    }else{
                        if($company->company_id == $company_id){
                            $proceed = true;
                            $company_text .= $company->company_detail->company_name.'<br>';
                        }
                    }

                }
           }

           if($proceed){
                $proceed = false;
                $department_text="";
                if( $id  == "-1"){
                    $proceed = true;
                    foreach($user->departments as $dep){
                        if(isset($dep->user_type->description)){
                            $department_text .= $dep->user_type->description.'<br>';
                        }
                    }
                }else{
                    $dep_ids = explode(",",$id);
                    foreach($user->departments as $dep){
                        if(in_array($dep->user_type_id, $dep_ids)){
                             $proceed = true;
                            $department_text .= $dep->user_type->description.'<br>';
                        }
                    }
                }
            }


           if($proceed){

                switch ($user->status) {
                    case 'A':
                        $status = 'Active';
                        break;
                    case 'I':
                        $status = 'Inactive';
                        break;
                    default:
                        $status = 'Terminated';
                        break;
                }                    

                $new_users[] =array(
                    'id' => $user->id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email_address,
                    'departments' =>  $department_text,
                    'status' => $status,
                    'country' => $user->country,
                    'company' => $company_text,
                    'is_online' => $user->is_online,
                );            
           }
        }
        return $datatables->collection($new_users)
                          ->editColumn('first_name', function ($user) {
                              return '<a>' . $user['first_name'] . '</a>';

                          })
                          ->editColumn('status', function ($user) use ($access) {
                            $status = $user['status'];
                            if(strpos($access['users'], 'activate switch') !== false && 
                              ($user['status'] == 'Active' || $user['status'] == 'Inactive')) {
                              $stat = $user['status'] == 'Active' ? "A" : "I";
                              $status = '<label class="switch switch-active">';
                              if ($user['status'] == 'Active') {
                                  $status .= '<input type="checkbox" checked data-url="system-accounts" data-uid="'.$user['id'].'" data-stat="'.$stat.'" onchange="return activate(this)">';
                              } else if ($user['status'] == 'Inactive') {
                                  $status .= '<input type="checkbox" data-url="system-accounts" data-uid="'.$user['id'].'" data-stat="'.$stat.'" onchange="return activate(this)">';
                              }
                              $status .= '<div class="slider slider-rec btn">
                                  <span class="on">Active</span><span class="off">Inactive</span>
                                  </div>
                                  </label>';
                            }
                            return $status;
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
                                   $edit = '<a href="/admin/users/'.$user['id'].'/edit" class="btn btn-info btn-sm mr-1 mb-1">Edit</a>';
                                    /**
                                    * Remove view button when user has edit access
                                    */
                                    $view="";
                                }
                                if(strpos($access['users'], 'delete') !== false) {
                                   $delete = '<a href="/admin/users/'.$user['id'].'/cancel" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('.$message.')">Delete</a>';
                                   $delete = '<button onclick="deleteUser('.$user['id'].')"" class="btn btn-danger btn-sm" >Delete</button>';
                                }
                                if(strpos($access['users'], 'reset') !== false) {
                                   $message="'Reset password?'";
                                   $reset = '<a href="/admin/users/'.$user['id'].'/reset" class="btn btn-primary btn-sm mb-1" 
                                   onclick="return confirm('.$message.')">Reset</a>';
                                   $reset = '<button onclick="resetPassword('.$user['id'].')"" class="btn btn-primary btn-sm mb-1" >Reset Password</button>';
                                }
                                if(strpos($access['users'], 'set as offline') !== false && $user['is_online'] == 1) {
                                    $offline = '<a href="/admin/users/'.$user['id'].'/offline" class="btn btn-warning btn-sm">Set as Offline</a>';
                                    $offline = '<button onclick="setAsOffline('.$user['id'].')"" class="btn btn-warning btn-sm mb-1" >Set as Offline</button>';
                                }
                                return "{$view}{$edit}{$delete}{$reset}{$offline}";
                          })
                          ->rawColumns(['first_name', 'status', 'action','company','departments'])
                          ->make(true);
    }

    public function profile(){
        return view("admin.users.profile");
    }


    public function send_account_email()
    {
        $users = User::where('id','>',1)->get();
        foreach($users as $user){
            $user->password = bcrypt('goetu2018!');
            $user->save();

            $data = array(
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'password' => 'goetu2018!',
                'email_address' => $user->email_address,
                'username' => $user->username,
            );

            Mail::send(['html'=>'mails.accountcreation'],$data,function($message) use ($data){

                $message->to($data['email_address'],$data['first_name'].' '.$data['last_name']);
                $message->subject('[GoETU] Account Creation');
                $message->from('no-reply@goetu.com');
            });

            if (Mail::failures()) {
                return 'Error sending email for user: '.$user->email_address;
            }

        }
        return "Done sending user account creation emails";       
    }

    public function system_users() 
    {
        $systemUserSearch = true;
        $advanceSearchLabel = "System Users";
        $access = session('all_user_access');
        $admin_access = isset($access['users']) ? $access['users'] : "";
        $partner_id = auth()->user()->reference_id;
        $parent_id = auth()->user()->company_id;
       

        $departments = UserType::where('status', 'A')
            ->where('create_by', 'SYSTEM')
            ->orderBy('description')
            ->get();
        $is_partner = 0;    


        return view("admin.users.listsystem",
            compact(
                'departments',
                'systemUserSearch',
                'advanceSearchLabel',
                'is_partner'
            )
        );
    }

    public function system_data(Datatables $datatables)
    {
        $access = session('all_user_access');
        if (strpos($access['users'], 'full access') !== false) {
            $users = User::getAllSystemUsers();
        } else {
            $users = User::getAllSystemUsers(auth()->user()->company_id);    
        }
        
        $new_users = array();
        foreach($users as $user){
            //print_r($user->user_type_id);
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
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email_address,
                'departments' =>  $department_text,
                'country' => $user->country,
                'status' => $user->status_text,
                'company' => $user->company,
                'is_online' => $user->is_online,
            );
        }
        return $datatables->collection($new_users)
                          ->editColumn('first_name', function ($user) {
                              return '<a>' . $user['first_name'] . '</a>';

                          })
                          ->editColumn('status', function ($user) use ($access) {
                            $status = $user['status'];
                            if(strpos($access['users'], 'activate switch') !== false && 
                              ($user['status'] == 'Active' || $user['status'] == 'Inactive')) {
                              $stat = $user['status'] == 'Active' ? "A" : "I";
                              $status = '<label class="switch switch-active">';
                              if ($user['status'] == 'Active') {
                                  $status .= '<input type="checkbox" checked data-url="system-accounts" data-uid="'.$user['id'].'" data-stat="'.$stat.'" onchange="return activate(this)">';
                              } else if ($user['status'] == 'Inactive') {
                                  $status .= '<input type="checkbox" data-url="system-accounts" data-uid="'.$user['id'].'" data-stat="'.$stat.'" onchange="return activate(this)">';
                              }
                              $status .= '<div class="slider slider-rec btn">
                                  <span class="on">Active</span><span class="off">Inactive</span>
                                  </div>
                                  </label>';
                            }
                            return $status;
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
                                   $edit = '<a href="/admin/users/'.$user['id'].'/edit" class="btn btn-info btn-sm mr-1 mb-1">Edit</a>';   
                                    /**
                                    * Remove view button when user has edit access
                                    */
                                    $view="";
                                }
                                if(strpos($access['users'], 'delete') !== false) {
                                   $delete = '<a href="/admin/users/'.$user['id'].'/cancel" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('.$message.')">Delete</a>';
                                   $delete = '<button onclick="deleteUser('.$user['id'].')"" class="btn btn-danger btn-sm" >Delete</button>';
                                }
                                if(strpos($access['users'], 'reset') !== false) {
                                   $message="'Reset password?'";
                                   $reset = '<a href="/admin/users/'.$user['id'].'/reset" class="btn btn-primary btn-sm mb-1" 
                                   onclick="return confirm('.$message.')">Reset</a>';
                                   $reset = '<button onclick="resetPassword('.$user['id'].')"" class="btn btn-primary btn-sm mb-1" >Reset Password</button>';
                                }
                                if(strpos($access['users'], 'set as offline') !== false && $user['is_online'] == 1) {
                                    $offline = '<a href="/admin/users/'.$user['id'].'/offline" class="btn btn-warning btn-sm">Set as Offline</a>';
                                    $offline = '<button onclick="setAsOffline('.$user['id'].')"" class="btn btn-warning btn-sm mb-1" >Set as Offline</button>'; 
                                }
                                return "{$view}{$edit}{$delete}{$reset}{$offline}";
                          })
                          ->rawColumns(['first_name', 'status', 'action','departments'])
                          ->make(true);
    }

    public function offline(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        $user->is_online = 0;
        $user->save();

        return response()->json(array(
            'success'                   => true, 
            'msg'                       => "User has been set as offline", 
        ), 200); 
    }

    public function activate($id, $status, $url)
    {   
        $message = $status == 'A' ? 'deactivated' : 'activated';

        $user = User::find($id);
        $user->status = $status == 'A' ? 'I' : 'A' ;
        $user->save();

        return redirect("/admin/{$url}")->with("success","User was successfully {$message}.");
    }

    public function changeCompany(Request $request)
    {

        $check = User::find(auth()->user()->id);

        //ADD FOR MULTI COMPANY
        $oldCompanyID = auth()->user()->company_id;
        $oldRefID = auth()->user()->reference_id;
        auth()->user()->company_id = $request->txtChangeCompany;
        $check->company_id = $request->txtChangeCompany;

        $user_type = "";
        foreach($check->departments as $dep){
            if($dep->user_type->company_id ==  $check->company_id ){
                 $user_type .= $dep->user_type_id.",";
                 if($dep->user_type->create_by != "SYSTEM"){
                    auth()->user()->reference_id = $check->company_id;
                    $check->reference_id = $check->company_id;
                 }
            }
        }

        if($user_type == ""){
                auth()->user()->company_id = $oldCompanyID;
                auth()->user()->reference_id = $oldRefID;

                return response()->json(array(
                    'success'       => false, 
                    'message'           => "Unable to change. No assigned department on the selected company!", 
                ), 200);
        }
        $user_type = substr($user_type, 0, strlen($user_type) - 1);
        $check->user_type_id = $user_type;
        auth()->user()->user_type_id = $user_type;
        $check->save();

        $user = auth()->user();
        //Generate cache for access rights
        $ids = explode(",",$user->user_type_id);
        $all_user_access = Access::generateAllUserAccess($user->user_type_id);

        //create partner type access
        $partner_type_all_access="";
        $user_types = UserType::where('create_by','SYSTEM')->where('status','A')->get();
        foreach($user_types as $user_type){
            if (isset($all_user_access[strtolower($user_type->description)])){
                if(strpos($all_user_access[strtolower($user_type->description)], 'add') !== false){
                    $id = PartnerType::where('name',$user_type->description)->get();
                    $partner_type_all_access = $partner_type_all_access .  $id[0]->id . ",";
                }        
            }
        }

        if (strlen($partner_type_all_access) > 0){
            $partner_type_all_access = substr($partner_type_all_access, 0, strlen($partner_type_all_access) - 1);         
        }

        //create partner type access excluding leads and prospects
        $partner_type_access="";
        $user_types = UserType::where('create_by','SYSTEM')->where('status','A')->where('description','NOT LIKE','LEAD')->where('description','NOT LIKE','PROSPECT')->where('description','NOT LIKE','Merchant')->get();
        foreach($user_types as $user_type){
            if (isset($all_user_access[strtolower($user_type->description)])){
                if(strpos($all_user_access[strtolower($user_type->description)], 'add') !== false){
                    $id = PartnerType::where('name',$user_type->description)->get();
                    $partner_type_access = $partner_type_access .  $id[0]->id . ",";
                }        
            }
        }

        //create partner type access excluding leads and prospects
        $partner_type_access_view="";
        $user_types = UserType::where('create_by','SYSTEM')->where('status','A')->where('description','NOT LIKE','LEAD')->where('description','NOT LIKE','PROSPECT')->where('description','NOT LIKE','Merchant')->get();
        foreach($user_types as $user_type){
            if (isset($all_user_access[strtolower($user_type->description)])){
                if(strpos($all_user_access[strtolower($user_type->description)], 'view') !== false){
                    $id = PartnerType::where('name',$user_type->description)->get();
                    $partner_type_access_view = $partner_type_access_view .  $id[0]->id . ",";
                }        
            }
        }

        if (strlen($partner_type_access) > 0){
            $partner_type_access = substr($partner_type_access, 0, strlen($partner_type_access) - 1);         
        }

        $userType = UserType::find($user->user_type_id);
        $user_type_display = "";
        if ($userType->create_by!=="SYSTEM") $user_type_display =$userType->display_name;

        $permissions = Access::getPermissions($user->user_type_id);
        $permissions = array_flip($permissions);  

        $partner_type = Partner::with('partner_company')->find($user->reference_id);
        $not_parent = Partner::with('partner_company')->find($user->reference_id);
        $partner_type_not_parent = -1;
        if (isset($not_parent->partner_type_id))
        {
            $partner_type_not_parent = $user->reference_id == -1 ? -1 : $not_parent->partner_type_id;
        }
        $partner_type_id = -1;
        $company_name = "NO COMPANY";
        if (isset($partner_type->partner_company->company_name)) $company_name=$partner_type->partner_company->company_name;
        if (isset($partner_type->partner_type_id)) $partner_type_id = $partner_type->partner_type_id;

        $user_check = User::where('id',$user->id)->isInternal()->first();   
        $is_internal = true;
        if (!isset($user_check)) {
            $is_internal = false;
        }
        
        session(['partner_type_all_access' => $partner_type_all_access]);
        session(['partner_type_access' => $partner_type_access]);
        session(['partner_type_access_view' => $partner_type_access_view]);
        session(['all_user_access' => $all_user_access]);
        session(['user_type_desc' => $userType->description]);
        session(['user_type_display' => $user_type_display]);
        session(['permissions' => $permissions]);
        session(['partner_type_id' => $partner_type_id]);
        session(['company_name' => $company_name]);
        session(['partner_type_not_parent' => $partner_type_not_parent]);
        session(['is_internal' => $is_internal]);

        return response()->json(array(
            'success'       => true, 
            'message'           => "Company Updated!", 
        ), 200);

    }


}
