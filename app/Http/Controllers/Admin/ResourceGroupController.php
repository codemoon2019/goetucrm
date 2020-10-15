<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\ResourceGroup;
use App\Models\ResourceGroupAccess;
use App\Models\Resource;

use Illuminate\Support\Facades\DB;

class ResourceGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('access:admin,super admin access');
    }

    public function index()
    {
        $resourceGroups = ResourceGroup::active()->orderBy('name')->get();
        return view('admin.resourcegroup.index')->with([
            'resourceGroups' => $resourceGroups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.resourcegroup.create');    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createAccess($id)
    {
        $resourceGroup = ResourceGroup::find($id);
        return view('admin.resourcegroup.createaccess')->with([
            'resourceGroup' => $resourceGroup,
        ]);;    
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
            'name' => 'nullable|string|max:255|unique:resource_groups,name,I,status',
        ]);
        DB::beginTransaction();
        try {
            $resourceGroup = new ResourceGroup;
            $resourceGroup->name = $request->name;
            $resourceGroup->description = $request->description;
            $resourceGroup->created_at = date('Y-m-d H:i:s'); 
            $resourceGroup->create_by = auth()->user()->username; 
            $resourceGroup->save();
            DB::commit();

            $message = 'Resource Group successfully added';
            return redirect(route('admin.resourcegroup.edit',$resourceGroup->id))->with([
                'success' => $message
            ]);
            
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect(route('admin.resourcegroup.create'))->with([
                'failed' => 'There was an error processing you request. ' . 
                            'Please try again later!'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAccess(Request $request, $id)
    {
        // $this->validate($request,[
        //     'name' => 'nullable|string|max:255|unique:resource_group_accesses,name,null,resource_group_id,'.$id,
        // ]);

        // $this->validate($request, [
        //     'name' => 'required|name|not_in:'.$user->email,
        // ]);
        DB::beginTransaction();
        try {
            $resourceGroupAccess = new resourceGroupAccess;
            $resourceGroupAccess->resource_group_id = $id;
            $resourceGroupAccess->name = $request->name;
            $resourceGroupAccess->description = $request->description;
            $resourceGroupAccess->created_at = date('Y-m-d H:i:s'); 
            $resourceGroupAccess->create_by = auth()->user()->username; 
            $resourceGroupAccess->save();

            $resource = new Resource;
            $resource->resource = $request->name;
            $resource->description = $request->name;
            $resource->resource_group_id = $id;
            $resource->resource_group_access_id = $resourceGroupAccess->id;
            $resource->create_by = auth()->user()->username; 
            $resource->save();

            DB::commit();

            $message = 'Resource Group successfully added';
            return redirect(route('admin.resourcegroup.edit',$id))->with([
                'success' => $message
            ]);
            
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect(route('admin.resourcegroup.create'))->with([
                'failed' => 'There was an error processing you request. ' . 
                            'Please try again later!'
            ]);
        }
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
        $resourceGroup = ResourceGroup::with(['resourceGroupAccess' => function ($q) {
            $q->orderBy('name', 'asc');}])->find($id);

        //dd($resourceGroup->resourceGroupAccess);
        return view('admin.resourcegroup.edit')->with([
            'resourceGroup' => $resourceGroup,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editAccess($id)
    {
        $access = ResourceGroupAccess::find($id);
        return view('admin.resourcegroup.editaccess')->with([
            'access' => $access,
        ]);
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
        DB::beginTransaction();
        try {
            $resourceGroup = ResourceGroup::find($id);
            $resourceGroup->name = $request->name;
            $resourceGroup->description = $request->description;
            $resourceGroup->updated_at = date('Y-m-d H:i:s'); 
            $resourceGroup->update_by = auth()->user()->username; 
            $resourceGroup->save();
            DB::commit();

            $message = 'Resource Group successfully updated';
            return redirect(route('admin.resourcegroup.edit',$id))->with([
                'success' => $message
            ]);
            
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect(route('admin.resourcegroup.edit',$id))->with([
                'failed' => 'There was an error processing you request. ' . 
                            'Please try again later!'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAccess(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $access = ResourceGroupAccess::find($id);
            $access->update([
                'name' => $request->name,
                'description' => $request->description,
                'updated_at' => date('Y-m-d H:i:s'),
                'update_by' => auth()->user()->username,
            ]);

            DB::commit();

            $message = 'Access successfully updated';
            return redirect(route('admin.resourcegroup.edit',$access->resource_group_id))->with([
                'success' => $message
            ]);
            
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect(route('admin.resourcegroup.editaccess',$id))->with([
                'failed' => 'There was an error processing you request. ' . 
                            'Please try again later!'
            ]);
        }
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
