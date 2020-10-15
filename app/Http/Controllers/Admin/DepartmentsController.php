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
use App\Models\PartnerProduct;
use App\Models\Division;

use App\Models\AccessTemplateHeader;

class DepartmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('access:admin,department')->only('index', 'show');
        $this->middleware('access:admin,add')->only('create', 'store');
        $this->middleware('access:admin,edit')->only('edit', 'update');
        $this->middleware('access:admin,delete')->only('cancel');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $definedGroup = false;

        $departmentSearch = true;
        $advanceSearchLabel = 'Departments';
        $access = session('all_user_access');
        $admin_access = isset($access['users']) ? $access['users'] : '';
        $partner_id = auth()->user()->reference_id;
        $parent_id = auth()->user()->company_id;

        if(strpos($access['admin'], 'department full access') !== false) {
            $js = $this->buildTreeAdmin();
            $company = "Admin";
            $departments = UserType::where('status','A')->where('create_by','<>','SYSTEM')->get();
            $is_partner = 0;    
            $companies = Partner::get_downline_partner($partner_id,$parent_id,7);
        } else {
            $js = $this->buildTree();
            $company = Partner::find(auth()->user()->company_id);
            $company = isset($company) ? $company->partner_company->company_name : "No Assigned Company";
            $departments = UserType::where('status','A')->where('create_by','<>','SYSTEM')->where('company_id',auth()->user()->company_id)->get();
            $is_partner = 1;    
            $companies = Partner::get_downline_partner($partner_id,$parent_id,7);
        }

        $userList = "";
        foreach($departments as $dep)
        {
            $userIDs = "";
            $users = User::getUsersByDepartment($dep->id);
            foreach ($users as $user){
                $userIDs .= $user->id.",";
            }
            $users = User::whereIn('id',explode(",",  $userIDs))->orderBy('first_name','asc')->get();
            $names = "";
            foreach ($users as $user){
                $names .= $user->first_name." ".$user->last_name."<br>";
            }
            $userList .= '<input type="hidden" id="user'.$dep->id.'" value="'.$names.'">';
        }


        return view("admin.departments.list")->with(
            compact(
                'definedGroup',
                'js',
                'company',
                'userList',
                'departmentSearch',
                'advanceSearchLabel',
                'departments',
                'is_partner',
                'companies'
            )
        );
    }

    private function buildTree(){
        $departments = UserType::where('status','A')->where('create_by','<>','SYSTEM')->where('company_id',auth()->user()->company_id)->get();
        
        foreach ($departments as $dep) {
            $level = 0;
            $data = UserType::where('id',$dep->parent_id)->first();
            while(isset($data)){
                $level++;
                $data = UserType::where('id',$data->parent_id)->first();
            }
            $dep->level = $level;
        }

        $js = 'config = {
                        container: "#department-tree",
                        hideRootNode : true,
                        rootOrientation :"WEST",
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

        $division = Division::where('company_id',auth()->user()->company_id)->where('status','A')->get();
        foreach ($division as $d ){
            $js .= 'division'.$d->id.' = {parent: hidden_parent, text: { name: "'.$d->name.'" },image: "/images/department.png", HTMLid: "division'. $d->id.'"};';
             $addNode .= ",division{$d->id}";
        }


        foreach( $departments->sortBy('level') as $key => $treeInfo){

            $leaderData = User::where('id',$treeInfo->head_id)->first();
            $leader = isset($leaderData) ? $leaderData->first_name . ' ' . $leaderData->last_name : 'N/A';
            $image = isset($leaderData) ? $leaderData->image : '/images/department.png';
            $node = 'node'.$treeInfo->id;
            $parent_node = 'node'.$treeInfo->parent_id;
            $access = session('all_user_access');
            $edit = "";
            $delete = "";
            if(strpos($access['admin'], 'edit') !== false) {
               $edit = 'desc: { 
                            val: " ",
                            href: "/admin/departments/'.$treeInfo->id.'/edit",
                            target: "_self"
                        },';
            }
            if(strpos($access['admin'], 'delete') !== false) {
               $delete = 'contact: { 
                            val: " ",
                            href: "/admin/departments/'.$treeInfo->id.'/cancel",
                            target: "_self"
                        },';
            }


            if($treeInfo->parent_id == -1){
                $pNode = ($treeInfo->division_id == -1) ? 'hidden_parent' : 'division'.$treeInfo->division_id;
                $js .= $node.' = {
                            parent: '.$pNode.',
                            text: { name: "'.$treeInfo->description.'", 
                                    title: "'.$leader .'",'.$edit . $delete . '},
                            image: "'.$image .'",
                            HTMLid: "'. $node.'",
                        };';                
            }else{
                $js .= $node.' = {
                            parent: '. $parent_node.',
                            text: { name: "'.$treeInfo->description.'" ,
                                    title: "'.$leader .'",'.$edit . $delete . '},
                            image: "'.$image .'",
                            HTMLid: "'. $node.'",
                        };';   
            }

            $addNode .= ','. $node;
        }

        $js .= 'tree_config = [
                    config,hidden_parent'.$addNode .'
                ];
                new Treant(tree_config);
                ';

        return $js;

    }


    private function buildTreeAdmin(){

        $departments = UserType::where('status','A')->where('create_by','<>','SYSTEM')->get();
        foreach ($departments as $dep) {
            $level = 0;
            $data = UserType::where('id',$dep->parent_id)->first();
            while(isset($data)){
                $level++;
                $data = UserType::where('id',$data->parent_id)->first();
            }
            $dep->level = $level;
        }

        $js = 'config = {
                        container: "#department-tree",
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
        $companies = UserType::getAllCompanyWithDepartment();
        foreach ($companies as $c) {
            if($c->company_id == -1){
                $js .= 'companyX = {parent: hidden_parent, text: { name: "No Company" },image: "/images/company.png", HTMLid: "companyX"};';
                $addNode .= ",companyX";
            }else{
                $js .= 'company'.$c->company_id.' = {parent: hidden_parent, text: { name: "'.$c->company_name.'" },image: "/images/company.png", HTMLid: "company'. $c->company_id.'"};';
                $addNode .= ",company{$c->company_id}";
                $division = Division::where('company_id',$c->company_id)->where('status','A')->get();
                foreach ($division as $d ){
                    $js .= 'division'.$d->id.' = {parent: company'.$c->company_id.', text: { name: "'.$d->name.'" },image: "/images/department.png", HTMLid: "division'. $d->id.'"};';
                     $addNode .= ",division{$d->id}";
                }

            }
        }
        
        foreach($departments->sortBy('level') as $key => $treeInfo){

            $leaderData = User::where('id',$treeInfo->head_id)->first();
            $leader = isset($leaderData) ? $leaderData->first_name . ' ' . $leaderData->last_name : 'N/A';
            $image = isset($leaderData) ? $leaderData->image : '/images/department.png';
            $node = 'node'.$treeInfo->id;
            $parent_node = 'node'.$treeInfo->parent_id;
            $company_node = ($treeInfo->company_id == -1) ? 'companyX' : 'company'.$treeInfo->company_id;
            $company_node = ($treeInfo->division_id == -1) ? $company_node : 'division'.$treeInfo->division_id;

            $access = session('all_user_access');
            $edit = "";
            $delete = "";
            if(strpos($access['admin'], 'edit') !== false) {
               $edit = 'desc: { 
                            val: " ",
                            href: "/admin/departments/'.$treeInfo->id.'/edit",
                            target: "_self"
                        },';
            }
            if(strpos($access['admin'], 'delete') !== false) {
               $delete = 'contact: { 
                            val: " ",
                            href: "/admin/departments/'.$treeInfo->id.'/cancel",
                            target: "_self"
                        },';
            }


            if($treeInfo->parent_id == -1){
                $js .= $node.' = {
                            parent: '. $company_node .',
                            text: { name: "'.$treeInfo->description.'", 
                                    title: "'.$leader .'",'.$edit . $delete . '},
                            image: "'.$image .'",
                            HTMLid: "'. $node.'",
                        };';                
            }else{
                $js .= $node.' = {
                            parent: '. $parent_node.',
                            text: { name: "'.$treeInfo->description.'" ,
                                    title: "'.$leader .'",'.$edit . $delete . '},
                            image: "'.$image .'",
                            HTMLid: "'. $node.'",
                        };';   
            }

            $addNode .= ','. $node;
        }

        $js .= 'tree_config = [
                    config,hidden_parent'.$addNode .'
                ];
                new Treant(tree_config);
                ';

        return $js;

    }


    private function explodeTree($array, $delimiter = '_', $baseval = false)
    {
        if(!is_array($array)) return false;
        $splitRE   = '/' . preg_quote($delimiter, '/') . '/';
        $returnArr = array();
        foreach ($array as $key => $val) {
            // Get parent parts and the current leaf
            $parts  = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
            $leafPart = array_pop($parts);

            // Build parent structure
            // Might be slow for really deep and large structures
            $parentArr = &$returnArr;
            foreach ($parts as $part) {
                if (!isset($parentArr[$part])) {
                    $parentArr[$part] = array();
                } elseif (!is_array($parentArr[$part])) {
                    if ($baseval) {
                        $parentArr[$part] = array('__base_val' => $parentArr[$part]);
                    } else {
                        $parentArr[$part] = array();
                    }
                }
                $parentArr = &$parentArr[$part];
            }

            // Add the final part to the structure
            if (empty($parentArr[$leafPart])) {
                $parentArr[$leafPart] = $val;
            } elseif ($baseval && is_array($parentArr[$leafPart])) {
                $parentArr[$leafPart]['__base_val'] = $val;
            }
        }
        return $returnArr;
    }
        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $new_acl=array();
        $access = session('all_user_access');
        if(strpos($access['admin'], 'department full access') !== false) {
            $acls = AccessControlList::getResourceGroups();
        } else {
            $acls = AccessControlList::getResourceGroups(auth()->user()->user_type_id);
        }
        foreach($acls as $acl){
            if(strpos($access['admin'], 'department full access') !== false) {
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
        //$products = Product::where('status','A')->where('parent_id',-1)->orderBy('name','asc')->get();
        
        if(strpos($access['admin'], 'super admin') !== false) {
            $templateList = AccessTemplateHeader::where('status','A')->orderBy('name')->get();
        } else {
            $templateList = AccessTemplateHeader::where('status','A')->whereIn('company_id',Array(-1,auth()->user()->company_id))->orderBy('name')->get();
        }
        foreach ($templateList as $list) {
            $accesses = AccessTemplateHeader::getAllResourceAccessByGroup($list->id);
            $resource_id="";
            foreach($accesses as $data) {
                $resource_id .= $data->id .",";
            }
            if(strlen($resource_id) >0){
                $resource_id = substr($resource_id, 0, strlen($resource_id) - 1);
            }
            $list->access = $resource_id;
        }


        if(strpos($access['admin'], 'department full access') !== false) {
            $isfullaccess = true;
            $companies = Partner::where('partner_type_id',7)->where('status','A')->get();
        }else{
            $isfullaccess = false;
            $companies = Partner::where('id',auth()->user()->company_id)->get();
        }

        if (strpos($access['admin'], 'super admin access') !== false){
            $products = Product::where('status','A')->where('parent_id',-1)->orderBy('name','asc')->get(); 
        }else{   
            $product_id="";
            $products = PartnerProduct::get_partner_products(auth()->user()->company_id);
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
            $products = Product::where('status','A')->whereIn('id',explode(",", $partner_product_id))->orderBy('name','asc')->get();
        }

        // $division = Division::where('status','A')->get();

        return view('admin.departments.create',compact('acls','products','companies','isfullaccess','templateList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'description' => 'required',
            'access' => 'required',
        ]);

        $user_type = new UserType;
        $user_type->description = $request->input('description');
        $user_type->created_at = date('Y-m-d H:i:s');
        $user_type->create_by = auth()->user()->username;
        $user_type->update_by = '';
        $user_type->status = 'A';
        $user_type->partner_type_access= '';
        $user_type->company_id = $request->company;
        $user_type->parent_id =  $request->depHead;
        $user_type->head_id = -1;
        $user_type->display_name = $request->display_name;
        $user_type->division_id = $request->division;
        $user_type->is_chat_support = isset($request->chkIsChatSupport) ? 1 : -1;
        $user_type->color =$request->depColor;
        $user_type->save();
        $user_type_id = $user_type->id;

        $products = $request->products;
        if (strlen($products) > 0){
            $products = substr($products, 0, strlen($products) - 1); 
            $products = explode(",", $products);

            foreach ($products as $product){
                $productAccess = new UserTypeProductAccess;
                $productAccess->product_id = $product;
                $productAccess->user_type_id = $user_type_id;
                $productAccess->create_by = auth()->user()->username;
                $productAccess->save(); 
            }   
        }
        

        $accesses = substr($request->access, 0, strlen($request->access) - 1);
        $accesses = explode(",", $accesses);
        foreach($accesses as $access) {
            $resource_data = AccessControlList::getResourcesViaResourceGroupAccess($access);
            foreach ($resource_data as $resource){
                $accessRights = new Access;
                $accessRights->resource_id = $resource->id;
                $accessRights->user_type_id = $user_type_id;
                $accessRights->save(); 
            }
        }

        return redirect('/admin/departments')->with('success','Department was successfully added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $new_acl=array();
        $access = session('all_user_access');
        if(strpos($access['admin'], 'department full access') !== false) {
            $department = UserType::find($id);
            $acls = AccessControlList::getResourceGroups();
        } else {
            $department = UserType::where('id',$id)->where('company_id',auth()->user()->company_id)->first();
            if(!isset($department) || $department->create_by == 'SYSTEM'){
                return redirect('/admin/departments')->with('failed','You have no access to that page.');
            }
            $acls = AccessControlList::getResourceGroups(auth()->user()->user_type_id);
        }
        foreach($acls as $acl){
            if(strpos($access['admin'], 'department full access') !== false) {
                $department_access = AccessControlList::getResourceGroupAccess($acl->id);
            } else {
                $department_access = AccessControlList::getResourceGroupAccess($acl->id,auth()->user()->user_type_id);
            }
            
            $new_acl[] =array(
                'id' => $acl->id,
                'name' => $acl->name,
                'department_access' =>  $department_access,
            );
        }

        $access = AccessControlList::getAllResourceAccessByGroup($id);
        $resource_id="";
        foreach($access as $data) {
            $resource_id .= $data->id .",";
        }
        if(strlen($resource_id) >0){
            $resource_id = substr($resource_id, 0, strlen($resource_id) - 1);
        }
        $department_access = explode(',', $resource_id);
        //dd($access);
        
        $products = Product::where('status','A')->where('parent_id',-1)->orderBy('name','asc')->get();
        $products_access = UserTypeProductAccess::where('user_type_id',$id)->get();

        $products_id="";
        foreach($products_access as $product_access) {
            $products_id .= $product_access->product_id .",";
        }
        if(strlen($products_id) >0){
            $products_id = substr($products_id, 0, strlen($products_id) - 1);
        }
        $products_access = explode(',', $products_id);
        $acls = $new_acl;

        $company = Partner::where('id',$department->company_id)->first();
        $company = isset($company->partner_company->company_name) ? $company->partner_company->company_name : "No Company";
        $departments = UserType::where('status','=','A')->where('id','<>',$department->id)->where('create_by','<>','SYSTEM')->where('company_id',$department->company_id)->orderBy('description','asc')->get();

        $parent_department = UserType::find($department->parent_id);
        $userIDs = "";
        $users = User::getUsersByDepartment($id);
        foreach ($users as $user){
            $userIDs .= $user->id.",";
        }

        $users = User::whereIn('id',explode(",",  $userIDs))->orderBy('first_name','asc')->get();
        $division = Division::where('status','A')->where('company_id',$department->company_id)->get();

        return view('admin.departments.show',compact('department','acls','department_access','products','products_access','company','departments','users','division'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $access = session('all_user_access');
        if(strpos($access['admin'], 'department full access') !== false) {
            $department = UserType::find($id);
            $acls = AccessControlList::getResourceGroups();
        } else {
            $department = UserType::where('id',$id)->where('company_id',auth()->user()->company_id)->first();
            if(!isset($department) || $department->create_by == 'SYSTEM'){
                return redirect('/admin/departments')->with('failed','You have no access to that page.');
            }
            $acls = AccessControlList::getResourceGroups(auth()->user()->user_type_id);
        }
        foreach($acls as $acl){
            if(strpos($access['admin'], 'department full access') !== false) {
                $department_access = AccessControlList::getResourceGroupAccess($acl->id);
            } else {
                $department_access = AccessControlList::getResourceGroupAccess($acl->id,auth()->user()->user_type_id);
            }
            
            $new_acl[] =array(
                'id' => $acl->id,
                'name' => $acl->name,
                'description' => $acl->description,
                'department_access' =>  $department_access,
            );
        }

        $templateList = AccessTemplateHeader::where('status','A')->whereIn('company_id',Array(-1,$department->company_id))->orderBy('name')->get();
        foreach ($templateList as $list) {
            $accesses = AccessTemplateHeader::getAllResourceAccessByGroup($list->id);
            $resource_id="";
            foreach($accesses as $data) {
                $resource_id .= $data->id .",";
            }
            if(strlen($resource_id) >0){
                $resource_id = substr($resource_id, 0, strlen($resource_id) - 1);
            }
            $list->access = $resource_id;
        }

        $access = AccessControlList::getAllResourceAccessByGroup($id);
        $resource_id="";
        foreach($access as $data) {
            $resource_id .= $data->id .",";
        }
        if(strlen($resource_id) >0){
            $resource_id = substr($resource_id, 0, strlen($resource_id) - 1);
        }
        $department_access = explode(',', $resource_id);
        //dd($access);
        
        // $products = Product::where('status','A')->where('parent_id',-1)->orderBy('name','asc')->get();

        $admin_access = session('all_user_access');

        if (strpos($admin_access['admin'], 'super admin access') !== false){
            $products = Product::where('status','A')->where('parent_id',-1)->orderBy('name','asc')->get(); 
        }else{   
            $product_id="";
            $products = PartnerProduct::get_partner_products(auth()->user()->company_id);
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
            $products = Product::where('status','A')->whereIn('id',explode(",", $partner_product_id))->orderBy('name','asc')->get();
        }
        
        $products_access = UserTypeProductAccess::where('user_type_id',$id)->get();

        $products_id="";
        foreach($products_access as $product_access) {
            $products_id .= $product_access->product_id .",";
        }
        if(strlen($products_id) >0){
            $products_id = substr($products_id, 0, strlen($products_id) - 1);
        }
        $products_access = explode(',', $products_id);
        $acls=$new_acl;
        $company = Partner::where('id',$department->company_id)->first();
        $company = isset($company->partner_company->company_name) ? $company->partner_company->company_name : "No Company";
        $departments = UserType::where('status','=','A')->where('id','<>',$department->id)->where('create_by','<>','SYSTEM')->where('company_id',$department->company_id)->orderBy('description','asc')->get();


        $userIDs = "";
        $users = User::getUsersByDepartment($id);
        foreach ($users as $user){
            $userIDs .= $user->id.",";
        }

        $users = User::whereIn('id',explode(",",  $userIDs))->orderBy('first_name','asc')->get();
        $division = Division::where('status','A')->where('company_id',$department->company_id)->get();

        return view('admin.departments.edit',compact('department','acls','department_access','products','products_access','company','departments','users','division','templateList'));
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
            'description' => 'required',
            'access' => 'required',
        ]);

        $user_type = UserType::find($id);
        $user_type->description = $request->input('description');
        $user_type->updated_at = date('Y-m-d H:i:s');
        $user_type->update_by = auth()->user()->username;;
        $user_type->parent_id = $request->depHead;
        $user_type->head_id = $request->pointPerson;
        $user_type->display_name = $request->display_name;
        $user_type->division_id = $request->division;
        $user_type->is_chat_support = isset($request->chkIsChatSupport) ? 1 : -1;
        $user_type->color =$request->depColor;
        $user_type->save();

        UserTypeProductAccess::where('user_type_id', $id)->delete();
        $products = $request->products;

        if (strlen($products) > 0){
            $products = substr($products, 0, strlen($products) - 1); 
            $products = explode(",", $products);
            foreach ($products as $product){
                $productAccess = new UserTypeProductAccess;
                $productAccess->product_id = $product;
                $productAccess->user_type_id = $id;
                $productAccess->create_by = auth()->user()->username;
                $productAccess->save(); 
            }   
        }

        UserTemplate::where('user_type_id', $id)->delete();
        $accesses = substr($request->access, 0, strlen($request->access) - 1);
        $accesses = explode(",", $accesses);

        foreach($accesses as $access) {
            $resource_data = AccessControlList::getResourcesViaResourceGroupAccess($access);
            foreach ($resource_data as $resource){
                $accessRights = new Access;
                $accessRights->resource_id = $resource->id;
                $accessRights->user_type_id = $id;
                $accessRights->save(); 
            }
        }

        return redirect('/admin/departments')->with('success','Department was successfully updated');
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

    public function cancel($id)
    {
        $resource = UserType::find($id);
        $checkChild = UserType::where('parent_id',$id)->get();
        if($checkChild->count() > 0){
            return redirect('/admin/departments')->with('failed','Department is set as a head. This cannot be deleted');
        }

        $resource->status = 'I';
        $resource->save();
        return redirect('/admin/departments')->with('success','Department was successfully deleted');
   
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $resource = UserType::find($id);
        $checkChild = UserType::where('parent_id',$id)->get();
        if($checkChild->count() > 0){
            return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Department is set as a head. This cannot be deleted", 
            ), 200); 

        }

        $resource->status = 'I';
        $resource->save();

        return response()->json(array(
            'success'                   => true, 
            'msg'                       => "Department has been deleted!", 
        ), 200); 
    }

    public function department_data(Datatables $datatables)
    {
        $companyId = isset( request()->companyId ) ? request()->companyId : -1;

        $departmentIds = -1;
        if ($companyId != -1) {
            $departmentIds = isset( request()->departmentId ) ? request()->departmentId : -1;
        }

        $access = session('all_user_access');
        if(strpos($access['admin'], 'department full access') !== false) {
            $query = UserType::where('status','=','A')
                        ->whereCompany($companyId)
                        ->whereDepartmentIn($departmentIds)
                        ->where('create_by','<>','SYSTEM')
                        ->get();
        } else {
            $query = UserType::where('status','=','A')
                        ->where('company_id', auth()->user()->company_id)
                        ->whereDepartmentIn($departmentIds)
                        ->get();
        }
        foreach($query as $q){
            $q->parent = !isset($q->parent->description) ? "No Assigned Parent Department" : $q->parent->description;
            $q->companyname = !isset($q->company->partner_company->company_name) ? "No Assigned Company" : $q->company->partner_company->company_name;
            $q->division = !isset($q->division->name) ? "No Assigned Division" : $q->division->name;
        }

        return $datatables->collection($query)
                          ->editColumn('color', function ($group) {
                              $dot = '<span class="dot" style=" height: 20px;width: 20px;border-radius: 50%;display: inline-block;background-color:'.$group->color.'"></span>';
                              return $dot;
                          })
                          ->editColumn('division', function ($group) {
                              return $group->division;

                          })
                          ->editColumn('name', function ($group) {
                              return '<a>' . $group->description . '</a>';

                          })
                          ->editColumn('head', function ($group) {
                              return  $group->parent;
                          })
                          ->editColumn('company', function ($group) {
                              return  $group->companyname;
                          })
                          ->addColumn('action', function ($group) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $view='<a class="btn btn-default btn-sm" href="/admin/departments/'.$group->id.'">View</a>';
                                $message="'Delete this department?'";
                                if(strpos($access['admin'], 'edit') !== false) {
                                   $edit = '<a href="/admin/departments/'.$group->id.'/edit" class="btn btn-primary btn-sm">Edit</a>';
                                    /**
                                    * Remove view button when user has edit access
                                    */
                                    $view="";
                                }
                                if(strpos($access['admin'], 'delete') !== false) {
                                   $delete = '<a href="/admin/departments/'.$group->id.'/cancel" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('.$message.')">Delete</a>';
                                   $delete = '<button onclick="deleteDepartment('.$group->id.')"" class="btn btn-danger btn-sm" >Delete</button>';
                                }
                                return $view.' '.$edit.' '.$delete;
                          })
                          ->rawColumns(['color','description', 'action'])
                          ->make(true);

    }

    public function system_group()
    {
        $definedGroup = true;
        return view("admin.departments.list",compact('definedGroup'));
    }

    public function system_group_data(Datatables $datatables)
    {
        $access = session('all_user_access');

        if(strpos($access['admin'], 'department full access') !== false) {
            $query = UserType::where('status','=','A')->where('create_by','=','SYSTEM')->get();
        } 

        return $datatables->collection($query)
                          ->editColumn('name', function ($group) {
                              return '<a>' . $group->description . '</a>';

                          })
                          ->addColumn('action', function ($group) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $view='<a class="btn btn-default btn-sm" href="/admin/system-group/'.$group->id.'/view">View</a>';
                                $message="'Delete this department?'";
                                if(strpos($access['admin'], 'edit') !== false) {
                                   $edit = '<a href="/admin/system-group/'.$group->id.'/edit" class="btn btn-primary btn-sm">Edit</a>';
                                    /**
                                    * Remove view button when user has edit access
                                    */
                                    $view="";
                                }
                                return $view.' '.$edit;
                          })
                          ->rawColumns(['description', 'action'])
                          ->make(true);

    }

    public function system_group_edit($id)
    {
        $department = UserType::find($id);
        $access = session('all_user_access');
        if(strpos($access['admin'], 'department full access') !== false) {
            $acls = AccessControlList::getResourceGroups();
        } else {
            return redirect('/admin/departments')->with('failed','You have no access to that page.');
        }

        if(strpos($access['admin'], 'super admin') !== false) {
            $templateList = AccessTemplateHeader::where('status','A')->orderBy('name')->get();
        } else {
            $templateList = AccessTemplateHeader::where('status','A')->whereIn('company_id',Array(-1,auth()->user()->company_id))->orderBy('name')->get();
        }

        foreach ($templateList as $list) {
            $accesses = AccessTemplateHeader::getAllResourceAccessByGroup($list->id);
            $resource_id="";
            foreach($accesses as $data) {
                $resource_id .= $data->id .",";
            }
            if(strlen($resource_id) >0){
                $resource_id = substr($resource_id, 0, strlen($resource_id) - 1);
            }
            $list->access = $resource_id;
        }
        
        foreach($acls as $acl){
            if(strpos($access['admin'], 'department full access') !== false) {
                $department_access = AccessControlList::getResourceGroupAccess($acl->id);
            } else {
                $department_access = AccessControlList::getResourceGroupAccess($acl->id,auth()->user()->user_type_id);
            }
            
            $new_acl[] =array(
                'id' => $acl->id,
                'name' => $acl->name,
                'description' => $acl->description,
                'department_access' =>  $department_access,
            );
        }

        $access = AccessControlList::getAllResourceAccessByGroup($id);
        $resource_id="";
        foreach($access as $data) {
            $resource_id .= $data->id .",";
        }
        if(strlen($resource_id) >0){
            $resource_id = substr($resource_id, 0, strlen($resource_id) - 1);
        }
        $department_access = explode(',', $resource_id);
        //dd($access);
        
        $products = Product::where('status','A')->where('parent_id',-1)->orderBy('name','asc')->get();
        $products_access = UserTypeProductAccess::where('user_type_id',$id)->get();

        $products_id="";
        foreach($products_access as $product_access) {
            $products_id .= $product_access->product_id .",";
        }
        if(strlen($products_id) >0){
            $products_id = substr($products_id, 0, strlen($products_id) - 1);
        }
        $products_access = explode(',', $products_id);
        $acls=$new_acl;

        return view('admin.systemgroups.edit',compact('templateList','department','acls','department_access','products','products_access'));
    }


    public function system_group_view($id)
    {
        $department = UserType::find($id);
        $new_acl=array();
        $access = session('all_user_access');
        if(strpos($access['admin'], 'department full access') !== false) {
            $acls = AccessControlList::getResourceGroups();
        } else {
            return redirect('/admin/departments')->with('failed','You have no access to that page.');
        }

        foreach($acls as $acl){
            if(strpos($access['admin'], 'department full access') !== false) {
                $department_access = AccessControlList::getResourceGroupAccess($acl->id);
            } else {
                $department_access = AccessControlList::getResourceGroupAccess($acl->id,auth()->user()->user_type_id);
            }
            
            $new_acl[] =array(
                'id' => $acl->id,
                'name' => $acl->name,
                'department_access' =>  $department_access,
            );
        }

        $access = AccessControlList::getAllResourceAccessByGroup($id);
        $resource_id="";
        foreach($access as $data) {
            $resource_id .= $data->id .",";
        }
        if(strlen($resource_id) >0){
            $resource_id = substr($resource_id, 0, strlen($resource_id) - 1);
        }
        $department_access = explode(',', $resource_id);
        //dd($access);
        
        $products = Product::where('status','A')->where('parent_id',-1)->orderBy('name','asc')->get();
        $products_access = UserTypeProductAccess::where('user_type_id',$id)->get();

        $products_id="";
        foreach($products_access as $product_access) {
            $products_id .= $product_access->product_id .",";
        }
        if(strlen($products_id) >0){
            $products_id = substr($products_id, 0, strlen($products_id) - 1);
        }
        $products_access = explode(',', $products_id);
        $acls = $new_acl;
        return view('admin.systemgroups.show',compact('department','acls','department_access','products','products_access'));
    }

    public function system_group_update(Request $request, $id)
    {
        $this->validate($request,[
            'description' => 'required',
            'access' => 'required',
        ]);

        $user_type = UserType::find($id);
        $user_type->description = $request->input('description');
        $user_type->updated_at = date('Y-m-d H:i:s');
        $user_type->update_by = auth()->user()->username;
        $user_type->display_name = $request->display_name;
        $user_type->save();

        UserTypeProductAccess::where('user_type_id', $id)->delete();
        $products = $request->products;

        if (strlen($products) > 0){
            $products = substr($products, 0, strlen($products) - 1); 
            $products = explode(",", $products);
            foreach ($products as $product){
                $productAccess = new UserTypeProductAccess;
                $productAccess->product_id = $product;
                $productAccess->user_type_id = $id;
                $productAccess->create_by = auth()->user()->username;
                $productAccess->save(); 
            }   
        }

        UserTemplate::where('user_type_id', $id)->delete();
        $accesses = substr($request->access, 0, strlen($request->access) - 1);
        $accesses = explode(",", $accesses);
        foreach($accesses as $access) {
            $resource_data = AccessControlList::getResourcesViaResourceGroupAccess($access);
            foreach ($resource_data as $resource){
                $accessRights = new Access;
                $accessRights->resource_id = $resource->id;
                $accessRights->user_type_id = $id;
                $accessRights->save(); 
            }
        }

        return redirect('/admin/system-group')->with('success','Group was successfully updated');
    }

    public function company_department_data($id)
    {
        $company = UserType::where('company_id',$id)->where('create_by','<>','SYSTEM')->where('status','A')->get();
        $option  = '<option value="-1" >NO ASSIGNED DEPARTMENT</option> ';
        foreach ($company as $dep) {
            $option .= '<option value="' . $dep->id .  '" >' . $dep->description .'</option> ';
        }

        $division = Division::where('company_id',$id)->where('status','A')->get();
        $option2  = '<option value="-1" >NO ASSIGNED DIVISION</option> ';
        foreach ($division as $dep) {
            $option2 .= '<option value="' . $dep->id .  '" >' . $dep->name .'</option> ';
        }


        $option3 = array();
        $templateList = AccessTemplateHeader::where('status','A')->whereIn('company_id',Array(-1,$id))->orderBy('name')->get();
        foreach ($templateList as $list) {
            $accesses = AccessTemplateHeader::getAllResourceAccessByGroup($list->id);
            $resource_id="";
            foreach($accesses as $data) {
                $resource_id .= $data->id .",";
            }
            if(strlen($resource_id) >0){
                $resource_id = substr($resource_id, 0, strlen($resource_id) - 1);
            }
            $option3[] = array(
                $list->name,
               '<input class="btn btn-success btn-sm" onclick="loadACLTemplate(\''.$resource_id.'\')" type="button" value="Load">'
            );  
        }


        return array(
            'success' => true,
            'data' => $option,        
            'data2' => $option2, 
            'data3' => $option3,     
        ); 
    }

    public function department_lead_data($id)
    {
        $department = UserType::find($id);
        $userIDs = "";
        while(isset($department)){
            $users = User::getUsersByDepartment($department->id);
            foreach ($users as $user){
                $userIDs .= $user->id.",";
            }
            $department = UserType::find($department->parent_id);
        }

        $option = '<option value="-1" >NO AVAILABLE POINT PERSON</option> ';
        $users = User::whereIn('id',explode(",",  $userIDs))->orderBy('first_name','asc')->get();
        foreach ($users as $user) {
            $option .= '<option value="' . $user->id .  '" >' . $user->first_name . ' '. $user->last_name.' ('.$user->email_address.')' .'</option> ';
        }

        return array(
            'success' => true,
            'data' => $option,            
        ); 
    }

}
