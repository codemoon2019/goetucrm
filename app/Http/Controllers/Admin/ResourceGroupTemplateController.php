<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

use App\Models\ResourceGroup;
use App\Models\ResourceGroupAccess;
use App\Models\Resource;

use App\Models\AccessTemplateHeader;
use App\Models\AccessTemplateDetail;
use App\Models\Partner;
use App\Models\AccessControlList;

use Illuminate\Support\Facades\DB;

class ResourceGroupTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        // $this->middleware('access:admin,super admin access');
    }

    public function index()
    {
        $access = session('all_user_access');
        $partner_id = auth()->user()->reference_id;
        $parent_id = auth()->user()->company_id;


        return view("admin.templates.list")->with(
            compact(
                'access'
            )
        );
    }

    public function create()
    {
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if (strpos($admin_access, 'access rights template') === false){
             return redirect('/')->with('failed','No access to template.');
        } 

        $is_admin = false;
        if (strpos($admin_access, 'super admin access') === false){
            $companies = Partner::where('id', auth()->user()->company_id)->where('status','A')->get();
        } else {
            $is_admin = true;
            $companies = Partner::where('partner_type_id', 7)->where('status','A')->get();  
        }


        $new_acl=array();
        if(strpos($admin_access, 'department full access') !== false) {
            $acls = AccessControlList::getResourceGroups();
        } else {
            $acls = AccessControlList::getResourceGroups(auth()->user()->user_type_id);
        }
        foreach($acls as $acl){
            if(strpos($admin_access, 'department full access') !== false) {
                $group_access = AccessControlList::getResourceGroupAccess($acl->id);
            } else {
                $group_access = AccessControlList::getResourceGroupAccess($acl->id,auth()->user()->user_type_id);
            }
            
            $new_acl[] =array(
                'id' => $acl->id,
                'name' => $acl->name,
                'description' => $acl->description,
                'group_access' =>  $group_access,
            );
        }
        $acls = $new_acl;

        return view('admin.templates.create',compact('companies','is_admin','acls'));   
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $template = new AccessTemplateHeader;
        $template->name = $request->input('name');
        $template->create_by = auth()->user()->username;
        $template->update_by = '';
        $template->status = 'A';
        $template->company_id = $request->company;
        $template->save();

        $accesses = substr($request->access, 0, strlen($request->access) - 1);
        $accesses = explode(",", $accesses);
        foreach($accesses as $access) {
            $resource_data = AccessControlList::getResourcesViaResourceGroupAccess($access);
            foreach ($resource_data as $resource){
                $accessRights = new AccessTemplateDetail;
                $accessRights->resource_id = $resource->id;
                $accessRights->header_id = $template->id;
                $accessRights->save(); 
            }
        }

        return redirect('/admin/group-templates')->with('success','Template was successfully added');
    }

    public function edit($id)
    {
        $template = AccessTemplateHeader::find($id);
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

        $new_acl=array();
        if(strpos($admin_access, 'department full access') !== false) {
            $acls = AccessControlList::getResourceGroups();
        } else {
            $acls = AccessControlList::getResourceGroups(auth()->user()->user_type_id);
        }
        foreach($acls as $acl){
            if(strpos($admin_access, 'department full access') !== false) {
                $group_access = AccessControlList::getResourceGroupAccess($acl->id);
            } else {
                $group_access = AccessControlList::getResourceGroupAccess($acl->id,auth()->user()->user_type_id);
            }
            
            $new_acl[] =array(
                'id' => $acl->id,
                'name' => $acl->name,
                'description' => $acl->description,
                'group_access' =>  $group_access,
            );
        }
        $acls = $new_acl;

        $access = AccessTemplateHeader::getAllResourceAccessByGroup($id);
        $resource_id="";
        foreach($access as $data) {
            $resource_id .= $data->id .",";
        }
        if(strlen($resource_id) >0){
            $resource_id = substr($resource_id, 0, strlen($resource_id) - 1);
        }
        $department_access = explode(',', $resource_id);
        // dd($department_access);

        return view('admin.templates.edit',compact('department_access','template','acls','companies','is_admin'));
    }

    public function update($id,Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $template = AccessTemplateHeader::find($id);
        $template->name = $request->input('name');
        $template->update_by = auth()->user()->username;
        $template->status = 'A';
        $template->company_id = $request->company;
        $template->save();

        AccessTemplateDetail::where('header_id', $id)->delete();
        $accesses = substr($request->access, 0, strlen($request->access) - 1);
        $accesses = explode(",", $accesses);
        foreach($accesses as $access) {
            $resource_data = AccessControlList::getResourcesViaResourceGroupAccess($access);
            foreach ($resource_data as $resource){
                $accessRights = new AccessTemplateDetail;
                $accessRights->resource_id = $resource->id;
                $accessRights->header_id = $template->id;
                $accessRights->save(); 
            }
        }

        return redirect('/admin/group-templates/'.$id.'/edit')->with('success','Template was successfully updated');
    }

    public function cancel(Request $request)
    {
        $id = $request->id;
        DB::transaction(function() use ($id){
            $template = AccessTemplateHeader::find($id);
            $template->status = 'D';
            $template->save();
        });
        return response()->json(array(
            'success'                   => true, 
            'msg'                       => "Template has been deleted!", 
        ), 200); 
    }


    public function data(Datatables $datatables)
    {

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') !== false){
            $data = AccessTemplateHeader::where('status','A')->get();
        }else{
            $data = AccessTemplateHeader::where('status','A')->where('company_id',auth()->user()->company_id)->get();
        }
        $new_data = array();
        foreach($data as $div){
            $new_data[] =array(
                'id' => $div->id,
                'name' => $div->name,
                'company' => $div->company_id == -1 ? 'All Company' : $div->company->company_name,
            );
        }
        return $datatables->collection($new_data)
                          ->editColumn('name', function ($template) {
                              return  $template['name'];

                          })
                         ->editColumn('company', function ($template) {
                              return  $template['company'];

                          })
                          ->addColumn('action', function ($template) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $view="";
                                $message="'Delete this Template?'";
                                $offline = "";
                                $edit = '<a href="/admin/group-templates/'.$template['id'].'/edit" class="btn btn-primary btn-sm">Edit</a>';
                                $delete = '<a href="/admin/group-templates/'.$template['id'].'/cancel" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('.$message.')">Delete</a>';
                                $delete = '<button onclick="deleteTemplate('.$template['id'].')"" class="btn btn-danger btn-sm" >Delete</button>';
                                return $view.' '.$edit.' '.$delete;
                          })
                          ->rawColumns(['name', 'action'])
                          ->make(true);
    }

}
