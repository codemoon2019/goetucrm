<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Models\AccessControlList;
use Cache;
use App\Models\Access;
use App\Models\UserType;
use App\Models\Product;
use App\Models\UserTypeProductAccess;
use App\Models\UserTemplate;
use App\Models\Partner;
use App\Models\User;
use App\Models\Country;
use App\Models\Division;
use DB;

class DivisionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:admin,division')->only('index', 'show');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $access = session('all_user_access');
        $partner_id = auth()->user()->reference_id;
        $parent_id = auth()->user()->company_id;


        return view("admin.divisions.list")->with(
            compact(
                'access'
            )
        );
    }

    public function data(Datatables $datatables)
    {
        $division = Division::where('status','A')->get();
        $new_division = array();
        foreach($division as $div){
            $new_division[] =array(
                'id' => $div->id,
                'name' => $div->name,
                'company' => $div->company_id == -1 ? 'NO COMPANY' : $div->company->company_name,
                'user' => $div->user_id == -1 ? 'NO ASSIGNED USER' : $div->user->first_name .' '.$div->user->last_name.' ('.$div->user->email_address.')',
                'country' => $div->country,
            );
        }
        return $datatables->collection($new_division)
                          ->editColumn('name', function ($division) {
                              return  $division['name'];

                          })
                         ->editColumn('company', function ($division) {
                              return  $division['company'];

                          })
                          ->editColumn('user', function ($division) {
                              return  $division['user'];

                          })
                          ->editColumn('country', function ($division) {
                              return  $division['country'];

                          })
                          ->addColumn('action', function ($division) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $reset="";
                                //$view='<a class="btn btn-default btn-sm" href="/admin/divisions/'.$division['id'].'">View</a>';
                                $view="";
                                $message="'Delete this Division?'";
                                $offline = "";
                                $edit = '<a href="/admin/divisions/'.$division['id'].'/edit" class="btn btn-primary btn-sm">Edit</a>';
                                $delete = '<a href="/admin/divisions/'.$division['id'].'/cancel" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('.$message.')">Delete</a>';
                                $delete = '<button onclick="deleteDivision('.$division['id'].')"" class="btn btn-danger btn-sm" >Delete</button>';
                                return $view.' '.$edit.' '.$delete;
                          })
                          ->rawColumns(['name', 'action'])
                          ->make(true);
    }


    public function create()
    {
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'division') === false){
             return redirect('/')->with('failed','No access to division.');
        } 
        $is_admin = false;
        if (strpos($admin_access, 'super admin access') === false){
            $companies = Partner::where('id', auth()->user()->company_id)->where('status','A')->get();
        } else {
            $is_admin = true;
            $companies = Partner::where('partner_type_id', 7)->where('status','A')->get();  
        }
        $country = Country::where('status','A')->get();
        return view('admin.divisions.create',compact('companies','is_admin','country'));   
    }


    public function store(Request $request)
    {
        DB::transaction(function() use ($request){
            $this->validate($request,[
                    'name' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
            ]);

            $division = new Division;
            $division->name = $request->input('name');
            $division->description = $request->input('description');
            $division->create_by = auth()->user()->username;
            $division->update_by = auth()->user()->username;
            $division->user_id = $request->pointPerson;
            $division->company_id = $request->company;
            $division->city = $request->input('city');
            $division->address = $request->input('address');
            $division->country = $request->input('country');
            $division->status = 'A';
            $division->save();

        });
        return redirect('/admin/divisions')->with('success','Division added');
    }

    public function show($id)
    {
        $division = Division::find($id);
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'division') === false){
             return redirect('/')->with('failed','No access to division.');
        } 

        return view('admin.divisions.show',compact('division'));
    }

    public function edit($id)
    {
        $division = Division::find($id);
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'division') === false){
             return redirect('/')->with('failed','No access to division.');
        } 

        $is_admin = false;
        if (strpos($admin_access, 'super admin access') === false){
            $companies = Partner::where('id', auth()->user()->company_id)->where('status','A')->get();
        } else {
            $is_admin = true;
            $companies = Partner::where('partner_type_id', 7)->where('status','A')->get();  
        }
        $country = Country::where('status','A')->get();
        $users = User::where('company_id',$division->company_id)->orderBy('first_name','asc')->get();
        return view('admin.divisions.edit',compact('division','companies','is_admin','users','country'));
    }

    public function update(Request $request,$id)
    {
        DB::transaction(function() use ($request,$id){
            $this->validate($request,[
                    'name' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
            ]);

            $division = Division::find($id);
            $division->name = $request->input('name');
            $division->description = $request->input('description');
            $division->create_by = auth()->user()->username;
            $division->update_by = auth()->user()->username;
            $division->status = 'A';
            $division->user_id = $request->pointPerson;
            $division->company_id = $request->company;
            $division->city = $request->input('city');
            $division->address = $request->input('address');
            $division->country = $request->input('country');
            $division->save();

        });
        return redirect('/admin/divisions')->with('success','Division added');
    }

    public function cancel(Request $request)
    {
        $id = $request->id;
        DB::transaction(function() use ($id){
            $division = Division::find($id);
            $division->status = 'D';
            $division->save();
        });
        return response()->json(array(
            'success'                   => true, 
            'msg'                       => "Division has been deleted!", 
        ), 200); 
    }

    public function load_users($id)
    {
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'division') === false){
            return array(
                'success' => false,
                'message' => 'no Access',            
            ); 
        } 

        $option = '<option value="-1" >NO AVAILABLE POINT PERSON</option> ';
        $users = User::where('company_id',$id)->orderBy('first_name','asc')->get();
        foreach ($users as $user) {
            $option .= '<option value="' . $user->id .  '" >' . $user->first_name . ' '. $user->last_name.' ('.$user->email_address.')' .'</option> ';
        }

        return array(
            'success' => true,
            'data' => $option,            
        ); 
    }


}
