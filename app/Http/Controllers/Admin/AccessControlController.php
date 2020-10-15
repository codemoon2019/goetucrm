<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Models\AccessControlList;
use Cache;
use App\Models\Access;

class AccessControlController extends BaseController
{
    public function __construct()
    {
        $this->middleware('access:admin,module')->only('index', 'show');
        $this->middleware('access:admin,add')->only('create', 'store');
        $this->middleware('access:admin,edit')->only('edit', 'update');
        $this->middleware('access:admin,delete')->only('destroy');
    }

    public function home(){
        return view("admin.home");
    }

    public function index()
    {
        return view("admin.acl.list");    
    }

    public function create()
    {
        $resource_groups = AccessControlList::getResourceGroups();
        
        return view("admin.acl.create")->with( compact('resource_groups') );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $resource = AccessControlList::find($id);
        $resource_groups = AccessControlList::getResourceGroups();
        return view('admin.acl.show',compact('resource','resource_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
                'name' => 'required',
                'resource' => 'required',
                'module_category' => 'required',
                'module_access' => 'required'
        ]);

        $resources = new AccessControlList;
        $resources->resource = $request->input('resource');
        $resources->description = $request->input('name');
        $resources->created_at = date('Y-m-d H:i:s');
        $resources->deleted = 0;
        $resources->create_by = auth()->user()->username;
        $resources->resource_group_id = $request->input('module_category');
        $resources->resource_group_access_id = $request->input('module_access');
        
        $resources->save();

        return redirect('/admin/acl')->with('success','ACL added');
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $resource = AccessControlList::find($id);
        //dd($resource); 
        $resource_groups = AccessControlList::getResourceGroups();
        return view('admin.acl.edit',compact('resource','resource_groups'));
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
            'name' => 'required',
            'resource' => 'required',
            'module_category' => 'required',
            'module_access' => 'required'
        ]);

        $resource = AccessControlList::find($id);
        $resource->resource = $request->input('resource');
        $resource->description = $request->input('name');
        $resource->updated_at = date('Y-m-d H:i:s');
        $resource->update_by = auth()->user()->username;
        $resource->resource_group_id = $request->input('module_category');
        $resource->resource_group_access_id = $request->input('module_access');
        
        $resource->save();

        return redirect('/admin/acl')->with('success','ACL updated');
    }


    public function cancel($id)
    {
        $resource = AccessControlList::find($id);
        $resource->deleted = 1;
        $resource->save();
        return redirect('/admin/acl')->with('success','ACL deleted');
    }


    public function delete(Request $request)
    {
        $id = $request->id;
        $resource = AccessControlList::find($id);
        $resource->deleted = 1;
        $resource->save();

        return response()->json(array(
            'success'                   => true, 
            'msg'                       => "ACL has been deleted!", 
        ), 200); 
    }

    public function data(Datatables $datatables)
    {
        $query = AccessControlList::getAllResources();
        return $datatables->collection($query)
                          ->editColumn('description', function ($acl) {
                              return '<a>' . $acl->description . '</a>';

                          })
                          ->addColumn('action', function ($acl) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $view='<a class="btn btn-default btn-sm" href="/admin/acl/'.$acl->id.'">View</a>';
                                $message="'Delete this ACL?'";
                                if(strpos($access['admin'], 'edit') !== false) {
                                   $edit = '<a href="/admin/acl/'.$acl->id.'/edit" class="btn btn-primary btn-sm">Edit</a>';
                                }
                                if(strpos($access['admin'], 'delete') !== false) {
                                   $delete = '<a href="/admin/acl/'.$acl->id.'/cancel" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('.$message.')">Delete</a>';
                                   $delete = '<button onclick="deleteACL('.$acl->id.')"" class="btn btn-danger btn-sm" >Delete</button>';
                                }
                                return $view.' '.$edit.' '.$delete;
                          })
                          ->rawColumns(['description', 'action'])
                          ->make(true);

    }

    public function get_resource_group_access($id)
    {
        $acls = AccessControlList::getResourceGroupAccess($id);
        return response()->json($acls);
    }
}