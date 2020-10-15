<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WelcomeEmailTemplate;
use App\Models\SubTaskTemplateHeader;
use App\Models\SubTaskTemplateDetail;
use App\Models\ProductTemplateHeader;
use App\Models\ProductTemplateDetail;
use App\Models\Product;
use App\Models\ProductModule;
use App\Models\User;
use Yajra\Datatables\Datatables;
use Cache;
use Excel;
use App\Models\Access;

use Illuminate\Support\Facades\DB;

use App\Models\Partner;
use App\Models\PartnerCompany;
use App\Models\PartnerType;

use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\PaymentFrequency;
use App\Models\MarkUpType;

use App\Models\ProductPaymentType;

use App\Models\PartnerProduct;
use App\Models\PartnerProductModule;
use App\Models\PartnerProductAccess;
use App\Models\UserType;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function construct()
    {
        $this->middleware('access:products,welcome email')->only('wemail_view', 'wemail_create', 'wemail_edit');
    }

    public function index()
    {
		/* $product_info = DB::table('products')
            ->select('products.*',DB::raw("ifnull(partner_companies.company_name,'No Company') as company_name"), 'product_types.description as type')
			->leftJoin('partner_companies', 'partner_companies.partner_id', '=' ,'products.company_id')
			->leftJoin('product_types', 'products.product_type_id', '=' ,'product_types.id')
			->where([['products.status','=','A'],['parent_id','=','-1']])
			->get();

		$product_results = array();

		foreach ($product_info as $p) {
			$customs = DB::table('product_custom_fields')
				->where('product_id','=','$p["id"]')
				->get();

            $p->products = $customs;

			$product_status = DB::table('product_statuses')
				->select('id','name','description','sequence')
				->where([['status','=','A'],['product_id','=','$p["id"]']])
				->get();

            $p->product_status = $product_status;

			$product_results[] = $p;
        } */

        $canAdd = Access::hasPageAccess('product','add',true) ? true : false;
        $canView = Access::hasPageAccess('product', 'view', true) ? true : false;
        $canEdit = Access::hasPageAccess('product', 'edit', true) ? true : false;
        $canDelete = Access::hasPageAccess('product', 'delete', true) ? true : false;
        $canAccess = $canAdd || $canView || $canEdit || $canDelete;
        $isSuperAdmin = Access::hasPageAccess('admin', 'super admin access', true) ?
            true : false;

        if (!$canAccess) {
            return redirect('/')->with('failed','No Access in this page');
        }

        $companies = null;
        if ($isSuperAdmin) {
            $companies = Partner::with(['partner_company' => function ($query) {
                    $query->orderBy('company_name');
                }])
                ->where('partner_type_id', '7')
                ->get();
        }

        return view('products.list')->with(
            compact(
                'canView',
                'canEdit',
                'canDelete',
                'canAdd',
                'isSuperAdmin',
                'companies'
            )
        );
    }

    public function getProducts()
    {
        $companyId = isset(request()->companyId) ? request()->companyId : -1;

        $products = Product::with('partnerCompany', 'productType')
            ->where('status', 'A')
            ->where('parent_id', -1)
            ->whereCompany($companyId)
            ->get();

        $canView = Access::hasPageAccess('product', 'view', true) ? true : false;
        $canEdit = Access::hasPageAccess('product', 'edit', true) ? true : false;
        $canDelete = Access::hasPageAccess('product', 'delete', true) ? true : false;

        return datatables()->collection($products)
            ->editColumn('buy_rate', function($product) {
                return $product->buy_rate = number_format($product->buy_rate ,2,".",","); 
            })
            ->editColumn('productType', function($product) {
                return $product->productType->description; 
            })
            ->editColumn('companyOwner', function($product) {
                if (is_null($product->partnerCompany)) {
                    return 'No Company';
                }
                
                return $product->partnerCompany->company_name;
            })
            ->addColumn('actions', function($product) use ($canView, $canEdit, $canDelete) {
                $actions = '';

                if ($canView) {
                    $actions .= "<a href='/products/edit/{$product->id}?view=true'>
                                    <button type='button' class='btn btn-light btn-sm' style='font-size:10px'>View</button>
                                </a>";
                }
                            
                if ($canEdit) {
                    $actions .= "<a href='products/edit/{$product->id}'>
                                    <button type='button' class='btn btn-primary btn-sm' style='font-size:10px'>Edit</button>
                                </a>";
                }

                if ($canDelete) {
                    $actions .= "<button type='button' class='btn btn-danger btn-sm' id='deleteProduct' 
                                        onclick='deleteMainProduct({$product->id})' style='font-size:10px'>Delete</button>";
                }

                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create(){

        if (!Access::hasPageAccess('product','add',true)) {
            return redirect('/products')->with('failed','No Access in this page');
        }

        $company_info = DB::table('partners')
            ->select('partners.id as id', 'partner_companies.company_name as company_name')
            ->join('partner_companies', 'partner_companies.partner_id', '=', 'partners.id')
            ->where('partners.partner_type_id','=',7)
            ->whereIn('partners.status',['A','I'])
            ->get();

        $product_type_info = DB::table('product_types')
            ->where('status','=','A')
            ->get();

        return view("products.create", compact('company_info','product_type_info')); 
    }

    public function editProduct($product_id = null, Request $request){
        $viewOnly = false;
        if(isset($request->view)){
            $viewOnly = $request->view;
        }
        $product = new Product;

        $product_info = Product::find($product_id);

        $query = DB::table('partners')
            ->select('partners.id as id', 'partner_companies.company_name as company_name')
            ->join('partner_companies', 'partner_companies.partner_id', '=', 'partners.id')
            ->where('partners.partner_type_id','=',7)
            ->whereIn('partners.status',['A','I'])
            ->get();

        $company_info = $query;

        $company_infos = $query;

        $query1 = DB::table('product_types')
            ->where('status','=','A')
            ->get();

        $product_type_info = $query1;

        $product_type_infos = $query1;

        $product_category_info = DB::table('product_categories')
            ->where([['status','=','A'],['product_id','=',$product_id]])
            ->get();

        $product_category_results = array();
        foreach ($product_category_info as $p) {
            $sub_product_info = DB::table('products')
                ->where([['status','=','A'],['parent_id','=',$product_id],['product_category_id','=',$p->id]])
                ->get();

            $p->products = $sub_product_info;
            $product_category_results[] = $p;
        }

        $payment_type = ProductPaymentType::where('status','A')->get();
        return view("products.editProduct", compact('product_info','company_info','company_infos','product_type_info','product_type_infos','product_category_info','product_category_results','payment_type','viewOnly'));
    }



    public function getSubProducts($id)
    {
        $product_category_info = DB::table('product_categories')
            ->where([['status','=','A'],['product_id','=',$id]])
            ->get();

        $product_category_results = array();
        foreach ($product_category_info as $p) {
            $sub_product_info = DB::table('products')
                ->where([['status','=','A'],['parent_id','=',$id],['product_category_id','=',$p->id]])
                ->get();

            $p->products = $sub_product_info;
            $product_category_results[] = $p;
        }

        return Array('data' => $product_category_info);
    }

    public function createProduct(Request $request){
        $product = new Product();

        $product->name = $request->txtProductName;
        $product->description = $request->txtProductDescription;
        $product->parent_id = -1;
        $product->company_id = $request->txtProductOwner;
        $product->product_type_id = $request->txtProductType;
        $product->create_by = auth()->user()->username;
        $product->buy_rate = $request->txtSumBuyRate;
        $product->status = 'A';
        $product->single_selection = $request->txtSingleSelect;

        if ($request->hasFile('fileProductImage')) {
            $filename = pathinfo($request->fileProductImage->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $request->fileProductImage->getClientOriginalExtension();
            $filenameToStore = str_replace([" ", "#"], "", $filename) . '_' . time() . ".{$extension}";

            $storePath = Storage::disk('public')->putFileAs(
                'products/display_pictures', 
                $request->fileProductImage,
                $filenameToStore
            );

            $product->display_picture = $storePath;
        }
        
        if (!$product->save()) {
            return response()->json(array('success' => false, 'msg'=> "Unable to save product!"), 200);
        } else {
            $product->code = 'P' . sprintf('%08d', $product->id);
            $product->save();

            return response()->json(array('success' => true, 'msg'=> "Product created successfully!", 'last_insert_id'=> $product->id), 200);
        }
    }

    public function createProductCategory(Request $request){
        $productCategory = new ProductCategory();

        $productCategory->name = $request->txtProductCatName;
        $productCategory->description = $request->txtProductCatDescription;
        $productCategory->product_id = $request->txtProductID;
        $productCategory->create_by = auth()->user()->username;
        $productCategory->single_selection = $request->txtSingleSelect;
        $productCategory->is_required = $request->txtMandatory;

        if(!$productCategory->save()) {
            return response()->json(array(
                    'success' => false, 
                    'msg'=> "Unable to save product category!"
                ), 200);
        }else {
            return response()->json(array(
                    'success'               => true, 
                    'msg'                   => "Product Category created successfully!", 
                ), 200);
        }
    }

    public function createSubProduct(Request $request){
        $subProduct = new Product();

        $subProduct->name = $request->txtSubProductName;
        $subProduct->description = $request->txtSubProductDescription;
        $subProduct->parent_id = $request->txtParentProductID;
        $subProduct->create_by = auth()->user()->username;
        $subProduct->buy_rate = $request->txtSubProductCost;
        $subProduct->product_category_id = $request->txtSubProductGroup;
        $subProduct->product_type = $request->txtSubProductType;
        $subProduct->hide_field = 1;
        $subProduct->field_identifier = $request->txtSubProductIdentifier;
        $subProduct->product_payment_type = $request->txtPaymentType;
        $subProduct->is_payment = $request->txtPaymentType == -1 ? 0 : 1;
        $subProduct->status = 'A';
        $subProduct->item_id = $request->txtItemID;

        if ($request->hasFile('fileProductImage')) {
            $filename = pathinfo($request->fileProductImage->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $request->fileProductImage->getClientOriginalExtension();
            $filenameToStore = str_replace([" ", "#"], "", $filename) . '_' . time() . ".{$extension}";

            $storePath = Storage::disk('public')->putFileAs(
                'products/display_pictures', 
                $request->fileProductImage,
                $filenameToStore
            );

            $subProduct->display_picture = $storePath;
        }

        if(!$subProduct->save()) {
            return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Unable to save sub product!", 
            ), 200);
        }else {
            $subProduct->code = 'SP' . sprintf('%08d', $subProduct->id);
            $subProduct->save();

            $cost = DB::table('products')
                ->select(DB::raw('sum(buy_rate) as cost'))
                ->where([['parent_id','=',$request->txtParentProductID],['status','=','A']])
                ->first();

            $mainCost = Product::find($request->txtParentProductID);
            $mainCost->buy_rate = $cost->cost;

            if($mainCost->save()){
                return response()->json(array(
                    'success'                   => true, 
                    'msg'                       => "Sub Product created successfully!", 
                ), 200);
            }
        }
    }

    public function editMainProduct(Request $request){
        $cost = DB::table('products')
            ->select(DB::raw('sum(buy_rate) as cost'))
            ->where('parent_id','=',$request->txtEditProductID)
            ->first();

        $updateProduct = Product::find($request->txtEditProductID);
        $updateProduct->name = $request->txtEditProductName;
        $updateProduct->description = $request->txtEditProductDescription;
        $updateProduct->parent_id = -1;
        $updateProduct->company_id = $request->txtEditOwnerID;
        $updateProduct->product_type_id = $request->txtEditProductType;
        $updateProduct->update_by = auth()->user()->username;
        $updateProduct->buy_rate = $cost->cost;
        $updateProduct->status = 'A';
        $updateProduct->single_selection = $request->txtSingleSelect;

        if ($request->hasFile('fileProductImage')) {
            $filename = pathinfo($request->fileProductImage->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $request->fileProductImage->getClientOriginalExtension();
            $filenameToStore = str_replace([" ", "#"], "", $filename) . '_' . time() . ".{$extension}";

            $storePath = Storage::disk('public')->putFileAs(
                'products/display_pictures', 
                $request->fileProductImage,
                $filenameToStore
            );

            $updateProduct->display_picture = $storePath;
        }

        if ($updateProduct->save()) {
            return response()->json(array(
                'success'                   => true, 
                'msg'                       => "Product has been updated!", 
                'picture'                   => isset($storePath) ? $storePath : $updateProduct->display_picture,
            ), 200);
        }else {
             return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Unable to update products!", 
            ), 200);
        }
    }

    public function editProductCategory(Request $request){
        $updateProductCategory = ProductCategory::find($request->txtEditProductCatID);
        $updateProductCategory->name = $request->txtEditProductCatName;
        $updateProductCategory->description = $request->txtEditProductCatDescription;
        $updateProductCategory->update_by = auth()->user()->username;
        $updateProductCategory->single_selection = $request->txtSingleSelect;
        $updateProductCategory->is_required = $request->txtMandatory;

        if ($updateProductCategory->save()) {
            return response()->json(array(
                'success'                   => true, 
                'msg'                       => "Product Category has been updated!", 
            ), 200);
        }else {
            return response()->json(array(
                'success'                   => false, 
                'msg'                       => "Unable to update product category!", 
            ), 200);
        }
    }

    public function editSubProduct(Request $request){
        $hide=0;
        if (isset($request->txtSubProductHideField)){
            $hide=1;                    
        }

        $updateSubProduct = Product::find($request->txtEditSubProductID);
        $updateSubProduct->name = $request->txtEditSubProductName;
        $updateSubProduct->description = $request->txtEditSubProductDescription;
        $updateSubProduct->parent_id = $request->txtProductCatID;
        $updateSubProduct->update_by = auth()->user()->username;
        $updateSubProduct->buy_rate = $request->txtEditSubProductCost;
        $updateSubProduct->product_category_id = $request->txtEditOwnerID;
        $updateSubProduct->status = 'A';
        $updateSubProduct->product_type = $request->txtEditSubProductType;
        $updateSubProduct->hide_field = $hide;
        $updateSubProduct->field_identifier = $request->txtSubProductIdentifier;
        $updateSubProduct->product_payment_type = $request->txtEditPaymentType;
        $updateSubProduct->is_payment = $request->txtEditPaymentType == -1 ? 0 : 1;
        $updateSubProduct->item_id = $request->txtEditItemID;

        if ($request->hasFile('fileProductImage')) {
            $filename = pathinfo($request->fileProductImage->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $request->fileProductImage->getClientOriginalExtension();
            $filenameToStore = str_replace([" ", "#"], "", $filename) . '_' . time() . ".{$extension}";

            $storePath = Storage::disk('public')->putFileAs(
                'products/display_pictures', 
                $request->fileProductImage,
                $filenameToStore
            );

            $updateSubProduct->display_picture = $storePath;
        }

        if (!$updateSubProduct->save()) {
            return response()->json(array(
                    'success'                   => false, 
                    'msg'                       => "Unable to save sub product!", 
                ), 200);
        }else {
            $cost = DB::table('products')
                ->select(DB::raw('sum(buy_rate) as cost'))
                ->where([['parent_id','=',$request->txtProductCatID],['status','=','A']])
                ->first();

            $mainCost = Product::find($request->txtProductCatID);
            $mainCost->buy_rate = $cost->cost;

            if($mainCost->save()){
                return response()->json(array(
                    'success'                   => true, 
                    'msg'                       => "Sub Product has been save!", 
                ), 200);
            }
        }

    }

    public function deleteProduct(Request $request){
        $rec_count = DB::table('partner_products')
            ->select(DB::raw('count(id) as record_count'))
            ->where([['status','=','A'],['product_id','=',$request->product_id]])
            ->first();

        if ($rec_count->record_count > 0) {
            return response()->json(array(
                'success'       => false,
                'msg'           => "Unable to delete product. Product has already been used in product fees.",
            ),200);
        }

        $deleteProduct = Product::find($request->product_id);
        $deleteProduct->status = 'D';
        $deleteProduct->update_by = auth()->user()->username;

        if ($deleteProduct->save()) {
            if($deleteProduct->parent_id != -1){
                $cost = DB::table('products')
                    ->select(DB::raw('sum(buy_rate) as cost'))
                    ->where([['parent_id','=',$deleteProduct->parent_id],['status','=','A']])
                    ->first();

                $mainCost = Product::find($deleteProduct->parent_id);
                $mainCost->buy_rate = $cost->cost;

                if($mainCost->save()){
                    return response()->json(array(
                        'success'                   => true, 
                        'msg'                       => "Sub Product has been deleted!", 
                    ), 200);
                }                
            }else{
                return response()->json(array(
                    'success'                   => true, 
                    'msg'                       => "Product has been deleted!", 
                ), 200);                
            }
        }
    }

    public function deleteProductCategory(Request $request){
        $rec_count = DB::table('products')
            ->select(DB::raw('count(id) as record_count'))
            ->where([['product_category_id','=',$request->product_category_id],['status','=','A']])
            ->first();

        if ($rec_count->record_count > 0) {
            return response()->json(array(
                'success'   => false,
                'msg'       => "Unable to delete category. Category has already been used by a product."
            ),200);
        }

        $deleteProductCategory = ProductCategory::find($request->product_category_id);
        $deleteProductCategory->status = 'D';
        $deleteProductCategory->update_by = auth()->user()->username;

        if ($deleteProductCategory->save()) {
            return response()->json(array(
                'success'   => true, 
                'msg'       => "Product Category has been deleted", 
            ), 200);
        }
    }

    public function listTemplate(){
        $access = session('all_user_access');
        $product_access = isset($access['product']) ? $access['product'] : "";
        $has_wEmail = (strpos($product_access, 'welcome email') === false) ? false : true;
        $has_wFlow= (strpos($product_access, 'work flow') === false) ? false : true;;
        $has_pFee= (strpos($product_access, 'commission template') === false) ? false : true;;

        return view("products.listTemplate",compact('has_wEmail','has_wFlow','has_pFee'));
    }

    public function createTemplate(){
        return view("products.createTemplate");
    }

    public function editEmailTemplate(){
        return view("products.editEmailTemplate");
    }

    public function workFlowTemplate(){
        return view("products.workFlowTemplate");
    }


    public function productfee_create(){
       

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partnerList = Partner::where('partner_type_id',7)->where('id',auth()->user()->company_id)->get();
            $is_admin = false;

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
                $product_id=Array();
                foreach($parent_product_ids as $pp)
                {
                    $product_id[] =$pp->parent_id;
                }
            }

            $productList = Product::where('status','A')->whereIn('id',$product_id)->get();
            foreach($productList as $p){
                $p->subproducts = Product::get_child_products($p->id,auth()->user()->company_id);
                $categories = Array();
                foreach($p->subproducts as $s){
                    $categories[] = $s->product_category_id;
                    $s->modules = ProductModule::where('product_id',$s->id)->where('status','A')->get();
                    $s->buy_rate = $s->amount;

                }

                $p->categories = ProductCategory::whereIn('id',$categories)->get();               
            }

        }else{
            $partnerList = Partner::where('partner_type_id',7)->get();
            $productList = Product::where('status','A')->where('parent_id',-1)->get();
            $is_admin = true;
        }


        $productType = ProductType::where('status','A')->get();
        $frequency = PaymentFrequency::where('status','A')->orderBy('sequence')->get();
        $markUp = MarkUpType::where('status','A')->get();

        $viewOnly = false;
        $headername = "Product Template (Create)";
        $formUrl = "/products/template/productfee/store";
        return view("products.productTemplate",compact('productList','partnerList','productType','viewOnly','formUrl','headername','frequency','markUp','is_admin'));
    }

    public function productfee_edit($id){
        $productList = Product::where('status','A')->where('parent_id',-1)->get();
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partnerList = Partner::where('partner_type_id',7)->where('id',auth()->user()->company_id)->get();
            $data = ProductTemplateHeader::where('id',$id)->where('partner_id',auth()->user()->company_id)->first();
            $is_admin = false;

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
                $product_id=Array();
                foreach($parent_product_ids as $pp)
                {
                    $product_id[] =$pp->parent_id;
                }
            }

            $productList = Product::where('status','A')->whereIn('id',$product_id)->get();
            foreach($productList as $p){
                $p->subproducts = Product::get_child_products($p->id,auth()->user()->company_id);
                $categories = Array();
                foreach($p->subproducts as $s){
                    $categories[] = $s->product_category_id;
                    $s->modules = ProductModule::where('product_id',$s->id)->where('status','A')->get();
                    $s->buy_rate = $s->amount;

                }

                $p->categories = ProductCategory::whereIn('id',$categories)->get();               
            }

        }else{
            $partnerList = Partner::where('partner_type_id',7)->get();
            $productList = Product::where('status','A')->where('parent_id',-1)->get();
            $data = ProductTemplateHeader::find($id);
            $is_admin = true;
        }


        if(!isset($data)){
            return redirect('/products/listTemplate')->with('failed','Invalid Data');
        }

        $productType = ProductType::where('status','A')->get();
        $frequency = PaymentFrequency::where('status','A')->orderBy('sequence')->get();
        $markUp = MarkUpType::where('status','A')->get();

        $viewOnly = false;
        $headername = "Product Template (Edit)";
        $formUrl = "/products/template/productfee/update/".$id;
        
        $parentProduct = Array();
        $products = Array();
        foreach ($data->details as $detail) {
            if(!in_array($detail->product->parent_id, $parentProduct))
            {
                $parentProduct[] = $detail->product->parent_id;
            }
            // if($detail->cost_multiplier == 1){
            //     if($detail->cost_multiplier_type == 'percentage'){
            //         $detail->cost = $detail->buy_rate * ($detail->cost_multiplier_value/100);
            //     }else{
            //         $detail->cost = $detail->buy_rate * $detail->cost_multiplier_value;
            //     }
            // }else{
                $detail->cost = $detail->product->buy_rate;
            // }
        }
        foreach($parentProduct as $id){
           $products[] = Product::find($id);
        }

        return view("products.productTemplate",compact('products','data','productList','partnerList','productType','viewOnly','formUrl','headername','frequency','markUp','is_admin'));
    }

    public function productfee_view($id){
        $productList = Product::where('status','A')->where('parent_id',-1)->get();
        $partnerList = Partner::where('partner_type_id',7)->get();
        $productType = ProductType::where('status','A')->get();
        $frequency = PaymentFrequency::where('status','A')->orderBy('sequence')->get();
        $markUp = MarkUpType::where('status','A')->get();

        $viewOnly = true;
        $headername = "Product Template (View)";
        $formUrl = "";
        $data = ProductTemplateHeader::find($id);
        $parentProduct = Array();
        $products = Array();
        foreach ($data->details as $detail) {
            if(!in_array($detail->product->parent_id, $parentProduct))
            {
                $parentProduct[] = $detail->product->parent_id;
            }
        }
        foreach($parentProduct as $id){
           $products[] = Product::find($id);
        }
        $is_admin = true;
        return view("products.productTemplate",compact('products','data','productList','partnerList','productType','viewOnly','formUrl','headername','frequency','markUp','is_admin'));
    }

    public function wemail_create(){
        $productList = Product::where('status','A')->where('parent_id',-1)->get();
        $viewOnly = false;
        $headername = "Welcome Email Template (Create)";
        $formUrl = "/products/template/wemail/store";
        return view("products.welcomeEmailTemplate",compact('productList','viewOnly','formUrl','headername'));
    }

    public function wemail_view($id){
        $productList = Product::where('status','A')->where('parent_id',-1)->get();
        $viewOnly = true;
        $headername = "Welcome Email Template (View)";
        $formUrl = "";
        $data = WelcomeEmailTemplate::find($id);
        return view("products.welcomeEmailTemplate",compact('productList','viewOnly','data','formUrl','headername'));
    }

    public function wemail_edit($id){
        $productList = Product::where('status','A')->where('parent_id',-1)->get();
        $viewOnly = false;
        $headername = "Welcome Email Template (Edit)";
        $formUrl = "/products/template/wemail/update/".$id;
        $data = WelcomeEmailTemplate::find($id);
        return view("products.welcomeEmailTemplate",compact('productList','viewOnly','data','formUrl','headername'));
    }

    public function workflow_create(){
        // dd(auth()->user()->username);
        // dd(session('all_user_access'));
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        
        if (strpos($admin_access, 'super admin access') === false){
            $productList = Product::api_get_company_products(auth()->user()->company_id);
        } else {
            $productList = Product::where('status','A')
                ->where('parent_id', -1)
                ->get();
        }

        if (count($productList) == 0) {
            return redirect('/products/listTemplate')->with('failed',
                'No Product is Assigned to the Company');
        }

        $departments = UserType::isActive()
            ->isNonSystem()
            ->whereCompany( auth()->user()->company_id )
            ->get();

        $viewOnly = false;
        $headername = "Workflow Template (Create)";
        $formUrl = "/products/template/workflow/store";

        return view('products.workFlowTemplate', 
            compact(
                'productList',
                'viewOnly',
                'formUrl',
                'headername',
                'departments'
            )
        );
    }

    public function workflow_view($id){
        $data = SubTaskTemplateHeader::find($id);
        if(!isset($data)){
            return redirect('/products/listTemplate')->with('failed','Invalid Product Template');
        }
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $productList = Product::api_get_company_products(auth()->user()->company_id);
        }else{
            $productList = Product::where('status','A')->where('parent_id',-1)->get();
        }
        if(count($productList) == 0){
            return redirect('/products/listTemplate')->with('failed','No Product is Assigned to the Company');
        }
        // $valid = false;
        // foreach($productList as $p){
        //     if($data->id == $p->product_id){
        //         $valid = true;
        //     }
        // }
        // if(!$valid){
        //     return redirect('/products/listTemplate')->with('failed','Invalid Product Template');
        // }
        $viewOnly = true;
        $headername = "Workflow Template (View)";
        $formUrl = "/products/template/workflow/update/".$id;
        foreach ($data->details as $detail) {
          $detail->assignee = json_decode($detail->assignee);
          $detail->product_tags = json_decode($detail->product_tags);
        }
        $sub_product = Product::where('parent_id',$data->product_id)->get();
        $users = User::getUserPerProduct($data->product_id,auth()->user()->company_id);

        $departments = UserType::isActive()
            ->isNonSystem()
            ->whereCompany( auth()->user()->company_id )
            ->get();

        return view("products.workFlowTemplate",
            compact(
                'productList',
                'viewOnly',
                'data',
                'formUrl',
                'sub_product',
                'users',
                'headername',
                'departments'
            )
        );
    }

    public function workflow_edit($id){
        $data = SubTaskTemplateHeader::find($id);
        if(!isset($data)){
            return redirect('/products/listTemplate')->with('failed','Invalid Product Template');
        }
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $productList = Product::api_get_company_products(auth()->user()->company_id);
        }else{
            $productList = Product::where('status','A')->where('parent_id',-1)->get();
        }
        if(count($productList) == 0){
            return redirect('/products/listTemplate')->with('failed','No Product is Assigned to the Company');
        }

        // $valid = false;
        // foreach($productList as $p){
        //     if($data->id == $p->product_id){
        //         $valid = true;
        //     }
        // }
        // if(!$valid){
        //     return redirect('/products/listTemplate')->with('failed','Invalid Product Template');
        // }

        $viewOnly = false;
        $headername = "Workflow Template (Edit)";
        $formUrl = "/products/template/workflow/update/".$id;
        
        foreach ($data->details as $detail) {
          $detail->assignee = json_decode($detail->assignee);
          $detail->product_tags = json_decode($detail->product_tags);
        }
        $sub_product = Product::where('parent_id',$data->product_id)->get();
        $users = User::getUserPerProduct($data->product_id,auth()->user()->company_id);

        $departments = UserType::isActive()
            ->isNonSystem()
            ->whereCompany( auth()->user()->company_id )
            ->get();

        return view('products.workFlowTemplate', 
            compact(
                'productList',
                'viewOnly',
                'data',
                'formUrl',
                'sub_product',
                'users',
                'headername',
                'departments'
            )
        );
    }

    public function get_sub_products_and_users($id)
    {
        $product = Product::where('parent_id',$id)->get();
        $users = User::getUserPerProduct($id,auth()->user()->company_id);

        $fields = Array('sub_products' => $product,'users' => $users);
        return $fields;

    }


    public function productfee_data(Datatables $datatables)
    {
        $query = ProductTemplateHeader::where('status','A')->get();
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $query = ProductTemplateHeader::where('status','A')->where('partner_id',auth()->user()->company_id)->get();
        }else{
            $query = ProductTemplateHeader::where('status','A')->where('partner_id','<>','NULL')->get();
        }

        return $datatables->collection($query)
                          ->editColumn('type', function ($data) {
                              return  $data->product_type->description;
                          })
                          ->editColumn('company', function ($data) {
                                if($data->partner_id == -1)
                                {
                                    $company =  "All Company";
                                }else{
                                    $company =  $data->partner->partner_company->company_name;
                                }
                              return  $company;
                          })
                          ->editColumn('action', function ($data) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $view='<a class="btn btn-default btn-sm" href="/products/template/productfee/'.$data->id.'">View</a>';
                                $message="'Delete this Product Template?'";
                                //if(strpos($access['acl'], 'edit') !== false) {
                                   $edit = '<a href="/products/template/productfee/'.$data->id.'/edit" class="btn btn-primary btn-sm">Edit</a>';
                                //}
                                //if(strpos($access['acl'], 'delete') !== false) { onclick="/products/template/productfee/'.$data->id.'/delete" 
                                   $delete = '<button onclick="deleteProductFee('.$data->id.')"" class="btn btn-danger btn-sm" >Delete</button>';
                                //}
                                return $view.' '.$edit.' '.$delete;
                          })
                          ->rawColumns(['name','description','type', 'company','action'])
                          ->make(true);
    }

    public function workflow_data(Datatables $datatables)
    {
        
        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){;
            $query = SubTaskTemplateHeader::api_get_company_task_templates(auth()->user()->company_id);
        }else{
            $query = SubTaskTemplateHeader::api_get_company_task_templates(-1);
        }

        return $datatables->collection($query)
                          ->editColumn('product', function ($data) {
                              return  $data->product_name;
                          })
                          ->editColumn('action', function ($data) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $message="'Delete this Workflow Template?'";
                                //if(strpos($access['acl'], 'edit') !== false) {
                                   $edit = '<a href="/products/templates/workflow?product_id='.$data->product_id.'" class="btn btn-primary btn-sm">Edit</a>';
                                //}
                                //if(strpos($access['acl'], 'delete') !== false) {
                                   $delete = '<button onclick="deleteWorkFlow('.$data->id.')"" class="btn btn-danger btn-sm" >Delete</button>';
                                //}
                                return $edit.' '.$delete;
                          })
                          ->rawColumns(['name','description', 'product','action'])
                          ->make(true);
    }

    public function wemail_data(Datatables $datatables)
    {   
        $welcomeEmailTemplates = WelcomeEmailTemplate::where('status', 'A')
            ->get();

        return $datatables->collection($welcomeEmailTemplates)
                          ->editColumn('product', function ($data) {
                              return  $data->product->name;

                          })
                          ->editColumn('action', function ($data) {
                                $access = session('all_user_access');
                                $edit="";
                                $delete="";
                                $view='<a class="btn btn-default btn-sm" href="/products/template/wemail/'.$data->id.'">View</a>';
                                $message="'Delete this Email Template?'";
                                //if(strpos($access['acl'], 'edit') !== false) {
                                   $edit = '<a href="/products/template/wemail/'.$data->id.'/edit" class="btn btn-primary btn-sm">Edit</a>';
                                //}
                                //if(strpos($access['acl'], 'delete') !== false) {
                                   $delete = '<button onclick="deleteWemail('.$data->id.')"" class="btn btn-danger btn-sm" >Delete</button>';
                                //}
                                return $view.' '.$edit.' '.$delete;
                          })
                          ->rawColumns(['name', 'product','action'])
                          ->make(true);
    }

    public function storeWemail(Request $request)
    {
      $validatedData = $request->validate([
            'emailTemplateTitle' => 'required',
            'emailTemplateProduct' => 'required',
            'emailContent' => 'required',
        ]);

      $wEmail = new WelcomeEmailTemplate;
      $wEmail->name = $request->emailTemplateTitle;
      $wEmail->product_id = $request->emailTemplateProduct;
      $wEmail->description = $request->emailContent;
      $wEmail->status = 'A';
      $wEmail->create_by = auth()->user()->username;
      $wEmail->update_by = auth()->user()->username;
      $wEmail->save();

      return redirect('/products/listTemplate#welcome-email')->with('success','Welcome Email Template added');
    }

    public function updateWemail($id,Request $request)
    {
      $validatedData = $request->validate([
            'emailTemplateTitle' => 'required',
            'emailTemplateProduct' => 'required',
            'emailContent' => 'required'
        ]);

      $wEmail = WelcomeEmailTemplate::find($id);
      $wEmail->name = $request->emailTemplateTitle;
      $wEmail->product_id = $request->emailTemplateProduct;
      $wEmail->description = $request->emailContent;
      $wEmail->status = 'A';
      $wEmail->update_by = auth()->user()->username;
      $wEmail->save();

      return redirect('/products/listTemplate#welcome-email')->with('success','Welcome Email Template updated');
    }

    public function deleteWemail(Request $request)
    {
        $wEmail = WelcomeEmailTemplate::find($request->id);
        $wEmail->status = 'D';
        $wEmail->update_by = auth()->user()->username;
        $wEmail->save();

        return response()->json(array(
            'success'                   => true, 
            'msg'                       => "Welcome Email Template has been deleted!", 
        ), 200); 
    }

    public function storeWorkflow(Request $request)
    {
        DB::transaction(function() use ($request) {
            $details = $request->txtDetailList;
            $details = json_decode($details);

            $workFlow = new SubTaskTemplateHeader;
            $workFlow->name = $request->workFlowTemplateName;
            $workFlow->description = $request->workFlowTemplateDescription;
            $workFlow->remarks = $request->workFlowTemplateMainTask;
            $workFlow->days_to_complete = $request->txtDaysToCompleteH;
            $workFlow->status = 'A';
            $workFlow->create_by = auth()->user()->username;
            $workFlow->update_by = auth()->user()->username;
            $workFlow->product_id = $request->txtProduct;
            $workFlow->save();

            foreach ($details as $detail) {
                $workFlowDetail = new SubTaskTemplateDetail;
                $workFlowDetail->sub_task_id = $workFlow->id;
                $workFlowDetail->line_number = $detail->lineNo;
                $workFlowDetail->name = $detail->subTask;
                $workFlowDetail->description = '';
                $workFlowDetail->department_id = $detail->department_id == -1 ? null : $detail->department_id;
                $workFlowDetail->assignee = json_encode([]);
                $workFlowDetail->product_tags = json_encode($detail->product_tag);
                $workFlowDetail->days_to_complete = $detail->dtc;
                $workFlowDetail->prerequisite = $detail->sst;
                $workFlowDetail->link_condition = $detail->sstt;
                $workFlowDetail->save();
            }
        });

        return redirect('/products/listTemplate#workflow')->with('success', 
            $request->workFlowTemplateName . ' Workflow Template created');
    }

    public function updateWorkflow($id,Request $request)
    {
        DB::transaction(function() use ($id,$request){
              $details = $request->txtDetailList;
              $details = json_decode($details);

              $workFlow = SubTaskTemplateHeader::find($id);
              $workFlow->name = $request->workFlowTemplateName;
              $workFlow->description = $request->workFlowTemplateDescription;
              $workFlow->remarks = $request->workFlowTemplateMainTask;
              $workFlow->days_to_complete = $request->txtDaysToCompleteH;
              $workFlow->status = 'A';
              $workFlow->update_by = auth()->user()->username;
              $workFlow->product_id = $request->txtProduct;
              $workFlow->save();
              $deletedRows = SubTaskTemplateDetail::where('sub_task_id', $id)->delete();

              foreach ($details as $detail) {
                  $workFlowDetail = new SubTaskTemplateDetail;
                  $workFlowDetail->sub_task_id = $workFlow->id;
                  $workFlowDetail->line_number = $detail->lineNo;
                  $workFlowDetail->name = $detail->subTask;
                  $workFlowDetail->description = '';
                  $workFlowDetail->department_id = $detail->department_id == -1 ? null : $detail->department_id;
                  $workFlowDetail->assignee = json_encode([]);
                  $workFlowDetail->product_tags = json_encode($detail->product_tag);
                  $workFlowDetail->days_to_complete = $detail->dtc;
                  $workFlowDetail->prerequisite = $detail->sst;
                  $workFlowDetail->link_condition = $detail->sstt;
                  $workFlowDetail->save();
              }
          });
          return redirect('/products/listTemplate#workflow')->with('success',$request->workFlowTemplateName . ' Workflow Template updated');
    }
    
    public function deleteWorkflow(Request $request)
    {
        $workFlow = SubTaskTemplateHeader::find($request->id);
        $workFlow->subtaskTemplates()->delete();
        $workFlow->delete();

        return response()->json(array(
            'success'                   => true, 
            'msg'                       => "Work Flow Template has been deleted!", 
        ), 200); 
    }

    public function storeProductFee(Request $request)
    {
        DB::transaction(function() use ($request){
              $details = $request->txtDetail;
              $details = json_decode($details);

              $productTemplate = new ProductTemplateHeader;
              $productTemplate->template_partner_type_id = -1;
              $productTemplate->partner_id = $request->txtCompany;
              $productTemplate->name = $request->txtTemplateName;
              $productTemplate->description = ($request->txtTemplateDescription == null) ? "" :  $request->txtTemplateDescription;
              $productTemplate->status = 'A';
              $productTemplate->product_type_id = $request->txtProductType;
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
        });
        return redirect('/products/listTemplate')->with('success',$request->txtTemplateName . ' Product Fee Template created');
    }

    public function updateProductFee($id,Request $request)
    {
        DB::transaction(function() use ($id,$request){
              $details = $request->txtDetail;
              $details = json_decode($details);

              $productTemplate = ProductTemplateHeader::find($id);
              $productTemplate->template_partner_type_id = -1;
              $productTemplate->partner_id = $request->txtCompany;
              $productTemplate->name = $request->txtTemplateName;
              $productTemplate->description = ($request->txtTemplateDescription == null) ? "" :  $request->txtTemplateDescription;
              $productTemplate->status = 'A';
              $productTemplate->product_type_id = $request->txtProductType;
              $productTemplate->update_by = auth()->user()->username;
              $productTemplate->save();
              $deletedRows = ProductTemplateDetail::where('template_id', $id)->delete();

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

              if($request->flgAll == 1){
                $partner_id = Partner::get_downline_partner_ids($request->txtCompany);
                $partners = Partner::where('partner_type_id',$request->txtPartnerType)->where('status','A')->whereRaw('id in('.$partner_id.')')->get();
                foreach($partners as $partner){
                  $id = $partner->id;
                  $deletedRows = PartnerProduct::where('partner_id', $id)->delete();
                  $product_id = "";
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
                    $partnerProduct->create_by = auth()->user()->username;
                    $partnerProduct->update_by = auth()->user()->username;
                    $partnerProduct->cost_multiplier = $detail->cost_multiplier;
                    $partnerProduct->cost_multiplier_value = $detail->cost_multiplier_value;
                    $partnerProduct->cost_multiplier_type = $detail->cost_multiplier_type;
                    $partnerProduct->srp = $detail->srp;
                    $partnerProduct->mrp = $detail->mrp;
                    $partnerProduct->bonus = $detail->bonus;
                    $partnerProduct->bonus_type = $detail->bonus_type;
                    $partnerProduct->bonus_amount = $detail->bonus_amount;
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
                }

              }
        });
        return redirect('/products/listTemplate')->with('success',$request->txtTemplateName . ' Product Fee Template updated');
    }
    
    public function deleteProductFee(Request $request)
    {
        $workFlow = ProductTemplateHeader::find($request->id);
        $workFlow->status = 'D';
        $workFlow->update_by = auth()->user()->username;
        $workFlow->save();

        return response()->json(array(
            'success'                   => true, 
            'msg'                       => "Product Fee Template has been deleted!", 
        ), 200);  

    }

    public function updateSubModule($id,Request $request)
    {
        $result = DB::transaction(function() use ($id,$request){
            try{
                $details = $request->moduleDetails;
                $details = json_decode($details);
                $rows = ProductModule::where('product_id', $id)->update(['status' => 'D']);
                foreach ($details as $d) {
                    $productModule = ProductModule::where('name', $d->name)->first();
                    if(isset($productModule)){
                        $productModule->name = $d->name;
                        $productModule->type = $d->type;
                        // $productModule->format = $d->format;
                        $productModule->value = $d->value;
                        $productModule->status = 'A';                        
                        $productModule->save();  
                    }else{
                        $productModule = new ProductModule;
                        $productModule->product_id = $id;
                        $productModule->name = $d->name;
                        $productModule->type = $d->type;
                        // $productModule->format = $d->format;
                        $productModule->value = $d->value;
                        $productModule->status = 'A';
                        $productModule->save();                        
                    }
   
                }
                return Array('success' => true);
            } catch (\Exception $e) {
                return Array('success' => false, 'msg' => $e->getMessage());
            }
        });
    }


    public function getSubModules($id)
    {
        $data = ProductModule::where('product_id', $id)->where('status','A')->get();
        return Array('data' => $data);
    }

    public function getPartnerProducts(Request $request){
        $id = $request->id;
        if($id == -1){
            $partner_product_id = -1;  
        }else{
            $product_id="";
            $products = PartnerProduct::get_partner_products($id);
            foreach($products as $p)
            {
                $product_id = $product_id . $p->product_id . ",";
            }
            $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
            if ($partner_product_id != -1 && $partner_product_id != ""){
                $parent_product_ids = Product:: get_parent_product_id($partner_product_id);
                $product_id="";
                foreach($parent_product_ids as $r)
                {
                    $product_id = $product_id . $r->parent_id . ",";
                }
                $partner_product_id = substr($product_id, 0, strlen($product_id) - 1); 
            }
        }
        // $isSuperAdmin = Access::hasPageAccess('admin', 'super admin access', true) ?
        //     true : false;
        // if($isSuperAdmin){
        //     $products = Product::where('status','A')->orderBy('name')->get();
        // }else{
            $products = Product::whereRaw("id in({$partner_product_id })")->where('status','A')->orderBy('name')->get();
        // }
        
            
        // $products_temp = Product::api_get_products($partner_product_id, $id);
        return Array('success' =>true , 'products' => $products);
    }



    public function uploadfile(Request $request){
        $logs = array();

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        if($request->hasFile('fileUploadCSV')){
            $extension = $request->file('fileUploadCSV')->getClientOriginalExtension();
            if ($extension == "csv") {
                $path = $request->file('fileUploadCSV')->storeAs(
                            'partners', $request->file('fileUploadCSV').'.'.$extension
                        );
                $data = Excel::load($request->file('fileUploadCSV'), function($reader) {})->get();

                if(!empty($data) && $data->count()){
                    
                    foreach ($data as $key => $value) {
                        $skip = false;
                        if (!(strtolower($value->classification) == 'main' || strtolower($value->classification)  == 'sub')) {
                            $logs[] = "Skipping ".$value->classification.", invalid value for classification.";
                            $skip = true;
                        } else {

                            if ($value->name == '' || !isset($value->name)) {
                                $logs[] = "Skipping a line due to missing name.";
                                $skip = true;
                            }

                            if ($value->company == '' || !isset($value->company)) {
                                $logs[] = "Skipping Product ".$value->name." due to missing company.";
                                $skip = true;
                            }

                            $company = DB::table('partners')->select('id')->where('partner_type_id',7)->where('partner_id_reference',$value->company)->first();
                            if (!$company) {
                                $logs[] = "Skipping Product ".$value->name.", Invalid Company.";
                                $skip = true;
                            }

                            if (strpos($admin_access, 'super admin access') === false){
                                if($company->id != auth()->user()->company_id){
                                    $logs[] = "Skipping Product ".$value->name.", User is not under the defined Company.";
                                    goto skip;
                                }
                            }

                            if($skip){goto skip;}

                            if (strtolower($value->classification) == 'main'){
                                if ($value->product_type == '' || !isset($value->product_type)) {
                                    $logs[] = "Skipping Main Product ".$value->name." due to missing product type.";
                                    $skip = true;
                                }

                                $product_type = DB::table('product_types')->select('id')->where('description',$value->product_type)->first();
                                if (!$product_type) {
                                    $logs[] = "Skipping  Main Product ".$value->name.", Invalid Product Type.";
                                    $skip = true;
                                }



                                $exist = DB::table('products')->select('id')->where('name',$value->name)->where('company_id',$company->id)->where('status','A')->first();
                                if ($exist) {
                                    $logs[] = "Skipping  Main Product ".$value->name.", Product Already Exist.";
                                    $skip = true;
                                }

                                if($skip){goto skip;}

                                $product = new Product;
                                $product->name = $value->name;
                                $product->description = $value->description;
                                $product->product_type_id = $product_type->id;
                                $product->company_id = $company->id;
                                $product->buy_rate = 0;
                                $product->status = "A";
                                $product->create_by = auth()->user()->username;
                                if (!$product->save()) {
                                    $logs[] = "Unable to save product."; 
                                    goto skip;
                                }
                                $product->code = 'P' . sprintf('%08d', $product->id);
                                $product->save();
                            }

                            if (strtolower($value->classification) == 'sub'){

                                if ($value->main_product == '' || !isset($value->main_product)) {
                                    $logs[] = "Skipping Sub Product ".$value->name." due to missing Main Product.";
                                    $skip = true;
                                }

                                $main = DB::table('products')->select('id')->where('name',$value->main_product)->where('company_id',$company->id)->where('status','A')->first();
                                if (!$main) {
                                    $logs[] = "Skipping  Sub Product ".$value->name.", Invalid Main Product.";
                                    $skip = true;
                                }

                                if ($value->cost == '' || !is_numeric($value->cost)) {
                                    $logs[] = "Skipping Sub Product ".$value->name." due to invalid Cost.";
                                    $skip = true;
                                }

                                if ($value->category == '' || !isset($value->category)) {
                                    $logs[] = "Skipping Sub Product ".$value->name." due to missing Category.";
                                    $skip = true;
                                }

                                $exist = DB::table('products')->select('id')->where('name',$value->name)->where('parent_id',$main->id)->where('status','A')->first();
                                if ($exist) {
                                    $logs[] = "Skipping Sub Product ".$value->name.", Product Already Exist.";
                                    $skip = true;
                                }


                                if($skip){goto skip;}


                                $pc =  ProductCategory::where('name',$value->category)->where('product_id',$main->id)->first();
                                if(!isset($pc)){
                                    $pc =  new ProductCategory;
                                    $pc->name = $value->category;
                                    $pc->description = $value->category;
                                    $pc->product_id = $main->id;
                                    $pc->create_by = auth()->user()->username;
                                    $pc->status = "A";
                                    if (!$pc->save()) {
                                        $logs[] = "Unable to save category."; 
                                        goto skip;
                                    }
                                }

                                $product = new Product;
                                $product->name = $value->name;
                                $product->description = $value->description;
                                $product->parent_id = $main->id;
                                $product->item_id = $value->item_id;
                                $product->buy_rate = $value->cost;
                                $product->product_category_id = $pc->id;
                                $product->status = "A";
                                $product->create_by = auth()->user()->username;
                                if (!$product->save()) {
                                    $logs[] = "Unable to save product."; 
                                    goto skip;
                                }
                                $product->code = 'SP' . sprintf('%08d', $product->id);
                                $product->save();

                                $cost = DB::table('products')
                                    ->select(DB::raw('sum(buy_rate) as cost'))
                                    ->where('parent_id','=',$main->id)
                                    ->first();
                                $updateProduct = Product::find($main->id);
                                $updateProduct->update_by = auth()->user()->username;
                                $updateProduct->buy_rate = $cost->cost;
                                $updateProduct->save();

                            }   


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
