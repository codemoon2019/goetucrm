<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/**
 *  Administrative Layouts
 */
Route::get("/countryziplist", "ApiController@countryZipList");
//Route::get('/', 'HomeController@index');
//Route::get('/home', 'HomeController@index');
Route::get('/company/sales/{id}', 'HomeController@companySales');
Route::post('/company/partner/receipts/', 'HomeController@getPartnerReceipts');
Route::get('/company/partnertype/', 'HomeController@getPartnerTypes');
Route::post('/company/dashbord_data/{info}', 'HomeController@getDashData');
Route::post('/company/merchant_dashbord_data/{info}', 'HomeController@getMerchantDashData');
Route::get('/company/invoice_data', 'HomeController@getInvoiceData');
Route::get('/company/invoice_volume_data', 'HomeController@getInvoiceVolumeData');
Route::get('/company/invoice_details/{id}', 'HomeController@getInvoiceDetails');
Route::post('/company/partner_dashboard_data/{info}', 'HomeController@getPartnerDashData');
// Route::post('/company/partnertype/', 'HomeController@getPartners');
// Route::post('/company/products/', 'HomeController@getProducts');
// Route::post('/company/product/sale/', 'HomeController@getProductSale');
// Route::post('/company/product/sale/bar', 'HomeController@getProductSaleBar');
Route::post('/company/product/receipts/', 'HomeController@getProductReceipts');
Route::post('/user-profile/{id}/update', 'HomeController@userProfileUpdate');
Route::get('/user-profile', 'HomeController@userProfile');
Route::get('/merchant-list/{id}', 'HomeController@getMerchants');
Route::post('/forgot-password','\App\Http\Controllers\Auth\LoginController@passwordReset');
Route::get('/appsign/{key}/sign', 'AppSignController@appsign');
Route::post('/appsign/{key}/sign', 'AppSignController@appsigned');
Route::get('/appsign/{key}/preview', 'AppSignController@orderPreview');
Route::post('/request_code', '\App\Http\Controllers\Auth\LoginController@request_code');
Route::post('/verify_code', '\App\Http\Controllers\Auth\LoginController@verify_code');

Route::middleware(['auth', 'updateLastActivity', 'analytics'])->group(function() {
    Route::namespace("Admin")->prefix("admin")->name("admin.")->group(function () {
        Route::get('/acl/data', 'AccessControlController@data');
        Route::get('/acl/get_resource_group_access/{id}', 'AccessControlController@get_resource_group_access');
        Route::get('/acl/{id}/cancel', 'AccessControlController@cancel');
        Route::get('/ACLDelete', 'AccessControlController@delete');

        Route::get('/departments/department_data', 'DepartmentsController@department_data');
        Route::get('/departments/{id}/cancel', 'DepartmentsController@cancel');
        Route::get('/departmentDelete', 'DepartmentsController@delete');

        Route::get('/users/data', 'UsersController@data');
        // Route::get('/users/{id}/reset', 'UsersController@reset');
        Route::get('/UserResetPassword', 'UsersController@reset');
        Route::get('/users/{id}/cancel', 'UsersController@cancel');
        Route::get('/users/{id}/{status}/{url}/activate', 'UsersController@activate');
        // Route::get('/users/{id}/offline', 'UsersController@offline');
        Route::get('/UserSetOffline', 'UsersController@offline');
        Route::get('/users/profile', 'UsersController@profile');
        Route::get('/users/{id}/{company_id}/{system_user}/advance_data_search', 'UsersController@advance_data_search');
        Route::put('/users/{id}/update','UsersController@update');
        Route::get('/system-accounts','UsersController@system_users');
        Route::get('/users/system-data', 'UsersController@system_data');
        Route::get('/UserDelete', 'UsersController@delete');

        Route::get('/users/companyList', 'UsersController@companyList');
        Route::get('/users/departmentList', 'UsersController@departmentList');
        Route::post('/users/changeCompany', 'UsersController@changeCompany');



        Route::get('/company_settings/configuration_menu/{id}', 'CompanySettingsController@configuration_menu');
        Route::get('/company_settings/{id}/ach_info', 'CompanySettingsController@ach_info');
        Route::post('/company_settings/{id}/ach_update', 'CompanySettingsController@ach_update');
        Route::get('/company_settings/{id}/training_access', 'CompanySettingsController@training_access');
        Route::post('/company_settings/{id}/training_update', 'CompanySettingsController@training_update');

        Route::get("/system-group", "DepartmentsController@system_group");
        Route::get('/departments/system_group_data', 'DepartmentsController@system_group_data');
        Route::get('/departments/company_department_data/{id}', 'DepartmentsController@company_department_data');
        Route::get('/departments/department_lead_data/{id}', 'DepartmentsController@department_lead_data');
        Route::get("/system-group/{id}/edit", "DepartmentsController@system_group_edit");
        Route::get('/system-group/{id}/view', 'DepartmentsController@system_group_view');
        Route::put('/system-group/{id}/update', 'DepartmentsController@system_group_update');

        Route::resource("/acl", "AccessControlController");
        Route::resource("/departments", "DepartmentsController");
        Route::resource("/users", "UsersController");
        Route::resource("/company", "CompanyController");
        Route::resource("/company_settings", "CompanySettingsController");
        Route::get("/api-send-account-email", "UsersController@send_account_email");

        Route::get('/group-templates/data', 'ResourceGroupTemplateController@data');
        Route::get('/group-templates/create', 'ResourceGroupTemplateController@create');
        Route::get('/group-templates/{id}/edit', 'ResourceGroupTemplateController@edit');
        Route::get('/group-templates/deleteTemplate', 'ResourceGroupTemplateController@cancel');
        Route::put('/group-templates/{id}/update','ResourceGroupTemplateController@update');
        Route::resource("/group-templates", "ResourceGroupTemplateController");

        Route::get('/divisions/data', 'DivisionsController@data');
        Route::put('/divisions/{id}/update','DivisionsController@update');
        Route::get('/divisionsDelete', 'DivisionsController@cancel');
        Route::get('/divisions/load_users/{id}', 'DivisionsController@load_users');
        Route::resource('/divisions', 'DivisionsController');

        /** Banners */
        Route::get('/banners', 'BannerController@index')->name('banners.index');
        Route::get('/banners/create', 'BannerController@create')->name('banners.create');
        Route::post('/banners', 'BannerController@store')->name('banners.store');
        Route::delete('/banners', 'BannerController@destroyMany')->name('banners.destroyMany');
        Route::get('/banners/{id}/edit', 'BannerController@edit')->name('banners.edit');
        Route::put('/banners/{id}', 'BannerController@update')->name('banners.update');
        Route::post('/banners/{id}/close', 'BannerController@close')->name('banners.close');

        /** System Detailed Access (Resource Groups) */
        Route::get('/dev-access', 'ResourceGroupController@index')->name('resourcegroup.index');
        Route::get('/dev-access/create', 'ResourceGroupController@create')->name('resourcegroup.create');
        Route::post('/dev-access', 'ResourceGroupController@store')->name('resourcegroup.store');
        Route::get('/dev-access/{id}/createaccess', 'ResourceGroupController@createAccess')->name('resourcegroup.createaccess');
        Route::post('/dev-access/{id}/createaccess', 'ResourceGroupController@storeAccess')->name('resourcegroup.storeaccess');
        Route::get('/dev-access/{id}/edit', 'ResourceGroupController@edit')->name('resourcegroup.edit');
        Route::get('/dev-access/{id}/editaccess', 'ResourceGroupController@editAccess')->name('resourcegroup.editaccess');
        Route::put('/dev-access/{id}/editaccess', 'ResourceGroupController@updateAccess')->name('resourcegroup.update');
        Route::put('/dev-access/{id}', 'ResourceGroupController@update')->name('resourcegroup.update');
        Route::post('/dev-access/{id}/close', 'ResourceGroupController@close')->name('resourcegroup.close');

        Route::get('/suggestions', 'SuggestionController@suggestion');
        Route::get("/suggestions/updateStarred", "SuggestionController@updateStarred");
        Route::post("/suggestions/updateAsRead", "SuggestionController@updateAsRead");
        Route::post("/suggestions/updateAsUnread", "SuggestionController@updateAsUnread");
        Route::get("/suggestions/getInfo/{id}", "SuggestionController@getInfo");


    });
    
    Route::namespace("Partners")->prefix("partners")->name("partners.")->group(function () {
        /* Partner Details */
        Route::get("/details/dashboard/{id}", "PartnersController@dashboard");
        Route::get("/details/dashboard/merchant-purchase/{id}", "PartnersController@merchantPurchase");
        //    Route::get("/details/profile/","PartnersController@profile");
        Route::get("/details/profile/{id}/profileOverview","PartnersController@profileOverview");
        Route::get("/details/profile/{id}/profileCompanyInfo","PartnersController@profileCompanyInfo");
        Route::match(['put', 'patch'], '/details/profile/companyInfoUpdate/{id}','PartnersController@companyInfoUpdate');
        Route::get("/details/profile/{id}/profileContactList","PartnersController@profileContactList");
        Route::get("/details/profile/profileContactList/edit/{id}/{contactId}","PartnersController@profileContactListEdit");
        Route::match(['put', 'patch'],"/details/profile/profileContactList/update/{id}/{contactId}","PartnersController@profileContactListUpdate");
        Route::get("/details/profile/profileContactList/create/{id}","PartnersController@profileContactListCreate");
        Route::match(['put', 'patch'],"/details/profile/profileContactList/store/{id}","PartnersController@profileContactListStore");

        Route::get("/details/crossselling/{id}","PartnersController@crossSellingAgent");
        Route::get("/removeCrossSellingAgent/{company_id}/{agent_id}","PartnersController@removeCrossSellingAgent");
        Route::get("/addCrossSellingAgent/{company_id}/{agent_id}","PartnersController@addCrossSellingAgent");

        Route::get("/details/profile/{id}/profileAttachments","PartnersController@profileAttachments");
        Route::get("/details/profile/{id}/profilePaymentGateway","PartnersController@profilePaymentGateway");
        Route::get("/details/products","PartnersController@products");
        Route::get("/details/agents/{id}","PartnersController@agents");
        Route::get("/details/users/{id}","PartnersController@users");     
        Route::get("/details/cross_merchants/{id}","PartnersController@merchants");
        Route::get("/details/viewTickets/{id}","PartnersController@viewTickets");
        Route::get("/details/billing/{id}","PartnersController@billing");
        Route::get("/details/{id}/products","PartnersController@products");
        Route::get("/details/{id}/commissions","PartnersController@commissions");
        Route::get("/get_commission_detail/{partner_id}/{id}","PartnersController@getCommission");
        Route::post("/update_commission","PartnersController@updateCommission");
        Route::get("/details/{id}/getTemplate","PartnersController@getTemplate");
        Route::get("/details/{id}/payment_method","PartnersController@payment_method");
        Route::get("/details/payment_method/{partner_id}/{id}/cancel","PartnersController@cancel_payment_method");
        Route::post("/details/{id}/updateProduct","PartnersController@updateProduct");

        Route::get('/users/data/{id}', 'PartnersController@user_data');

        Route::get("/getStateByCountry/{country}","PartnersController@getStateByCountry");
        Route::get("/getCityByState/{state}","PartnersController@getCityByState");
        Route::get("/getUplineListByPartnerTypeId/{id}","PartnersController@getUplineListByPartnerTypeId");
        Route::get("/validateField/{table}/{field}/{value}/{id}/{status}/{prefix}","PartnersController@validateField");
        Route::get("/getPartnersData","PartnersController@getPartnersData");
        Route::post("/upload_attachment","PartnersController@updatePartnerAttachment");
        Route::post("/updatepaymentgateway/{id}","PartnersController@updatePartnerPaymentGateway");
        Route::post("/updatepaymentmethod/{id}","PartnersController@updatePartnerPaymentMethod");
        Route::get("/advance_partners_search/{type}/{country}/{state}","PartnersController@advancePartnersSearch");
        Route::get("/refreshTicketList/{filter}/{id}","PartnersController@refreshTicketList");
        Route::get("/loadPartnerTypes/{id}","PartnersController@loadPartnerTypes");
        Route::get("/resend-email-verification/{id}","PartnersController@resendEmailVerification");
        Route::get("/generate_order_list/{id}/{startdate}/{enddate}","PartnersController@generate_order_list");
        Route::get("/management","PartnersController@management");
        Route::get("/management-treeview","PartnersController@managementTree");
        Route::resource("/","PartnersController");

        Route::get('/agent-applicants', 'AgentApplicantController@index');
        Route::get('/agent-applicants/get', 'AgentApplicantController@getAgentApplicants');
        Route::post('/agent-applicants/approve', 'AgentApplicantController@approve');
        Route::post('/agent-applicants/disapprove', 'AgentApplicantController@disapprove');
        Route::post('/agent-applicants/restore', 'AgentApplicantController@restore');

        Route::post("/uploadfile", "PartnersController@uploadfile");
    });
    Route::namespace("Products")->prefix("products")->name("products.")->group(function () {
        /* Create -> Products */
        Route::post("/createProduct", "ProductsController@createProduct");
        Route::post("/createProductCategory", "ProductsController@createProductCategory");
        Route::post("/createSubProduct", "ProductsController@createSubProduct");
        /* Edit -> Products */
        Route::get('/edit/{product_id}', 'ProductsController@editProduct');
        Route::post("/editMainProduct", "ProductsController@editMainProduct");
        Route::post("/editProductCategory", "ProductsController@editProductCategory");
        Route::post("/editSubProduct", "ProductsController@editSubProduct");

        /* Delete -> Products*/
        Route::get("/deleteProduct", "ProductsController@deleteProduct");
        Route::get("/deleteProductCategory", "ProductsController@deleteProductCategory");

        Route::get("/listTemplate", "ProductsController@listTemplate");
        Route::get("/listTemplate/productfee_data", "ProductsController@productfee_data");
        Route::get("/listTemplate/workflow_data", "ProductsController@workflow_data");
        Route::get("/listTemplate/wemail_data", "ProductsController@wemail_data");
        Route::get("/template/wemail/create", "ProductsController@wemail_create");
        Route::get("/template/wemail/{id}", "ProductsController@wemail_view");
        Route::get("/template/wemail/{id}/edit", "ProductsController@wemail_edit");
        
        Route::get("/template/workflow/create", "ProductsController@workflow_create");
        Route::get("/template/workflow/{id}", "ProductsController@workflow_view");
        Route::get("/template/workflow/{id}/edit", "ProductsController@workflow_edit");
        Route::get("/template/workflow/get_user_sub_products/{id}", "ProductsController@get_sub_products_and_users");
        Route::post("/template/workflow/store", "ProductsController@storeWorkflow");
        Route::post("/template/workflow/update/{id}", "ProductsController@updateWorkflow");

        Route::get("/template/deleteWorkflow", "ProductsController@deleteWorkflow");

        Route::get("/template/productfee/create", "ProductsController@productfee_create");
        Route::get("/template/productfee/{id}", "ProductsController@productfee_view");
        Route::get("/template/productfee/{id}/edit", "ProductsController@productfee_edit");

        Route::post("/template/productfee/store", "ProductsController@storeProductFee");
        Route::post("/template/productfee/update/{id}", "ProductsController@updateProductFee");
        Route::get("/template/deleteProductFee", "ProductsController@deleteProductFee");
        Route::get("/createTemplate", "ProductsController@createTemplate");
        // Route::get("/editEmailTemplate","ProductsController@editEmailTemplate");
        // Route::get("/workFlowTemplate","ProductsController@workFlowTemplate");

        Route::post("/template/wemail/store", "ProductsController@storeWemail");
        Route::post("/template/wemail/update/{id}", "ProductsController@updateWemail");
        Route::get("/template/deleteWemail", "ProductsController@deleteWemail");

        Route::post("/update_sub_module/{id}", "ProductsController@updateSubModule");
        Route::get("/get_sub_modules/{id}", "ProductsController@getSubModules");

        Route::get("/getPartnerProducts", "ProductsController@getPartnerProducts");
        Route::get("/getSubProducts/{id}", "ProductsController@getSubProducts");

        Route::resource("/", "ProductsController");
        Route::get("/getProducts", "ProductsController@getProducts");


        Route::get('/templates/workflow', 'WorkflowTemplateController@show')->name('templates.workflow.show');
        Route::post('/templates/workflow', 'WorkflowTemplateController@save')->name('templates.workflow.save');
        Route::get('/templates/workflow/{productId}', 'WorkflowTemplateController@getProductWorkflowTemplate');
        Route::post("/uploadfile", "ProductsController@uploadfile");
    });

    Route::namespace("Inventory")->prefix("inventory")->name("inventory.")->group(function () {
        Route::get("/purchaseorder", "InventoryController@purchaseorder");
        Route::get("/receivingpurchaseorder", "InventoryController@receivingpurchaseorder");

        Route::resource("/", "InventoryController");
    });

    Route::namespace("Merchants")->prefix("merchants")->name("merchants.")->group(function () {

        /**
         * Search merchant
         */
        Route::get("/search/{type}/{type_value}/{status}", "MerchantsController@search");
        Route::post("/uploadfile", "MerchantsController@uploadfile");
        
        Route::get("/board_merchant", "MerchantsController@boardMerchant");
        Route::get("/merchant_board_data","MerchantsController@merchant_board_data");
        Route::get("/confirm_merchant/{id}","MerchantsController@confirmMerchant");
        Route::get("/approve_merchant", "MerchantsController@approveMerchant");
        Route::get("/merchant_approve_data","MerchantsController@merchant_approve_data");
        Route::get("/finalize_merchant/{id}", "MerchantsController@finalizeMerchant");
        Route::post("/{id}/decline", "MerchantsController@declineMerchant");
        Route::get("/draft_merchant", "MerchantsController@draftMerchant");
        Route::get("/merchant_draft_data","MerchantsController@merchant_draft_data");

        Route::get("/details/{id}/dashboard", "MerchantsController@dashboard");
        Route::get("/details/dashboard/merchant-purchase/{id}", "MerchantsController@merchantPurchase");
        Route::get("/details/{id}/profile", "MerchantsController@profile");
        Route::get("/details/{id}/products", "MerchantsController@products");
        Route::get("/details/{id}/rmaServicing", "MerchantsController@rmaServicing");
        Route::get("/details/{id}/billing", "MerchantsController@billing");
        Route::get("/details/orders", "MerchantsController@orders");
        Route::get("/details/orders_data", "MerchantsController@orders_data");
        Route::get("/details/billing", "MerchantsController@invoices");
        Route::get("/details/invoices_data", "MerchantsController@invoices_data");
        Route::get("/details/{id}/brlist", "MerchantsController@branch");

        // Route::get("/workflow/{id}/{order_id}", "MerchantsController@workflow");

        /*
        |----------------------------------------------------------------------
        | Workflow
        |----------------------------------------------------------------------
        */
        Route::get("/{merchantId}/product-orders/{productOrderId}/workflow", "WorkflowController@showWorkflow");
        Route::post("/subtasks/{subtaskId}", "WorkflowController@changeSubtaskStatus");
        Route::post("/tasks/{taskId}/subtasks", "WorkflowController@createSubtask")->name('subtasks.add');

        Route::get("/workflow/{id}/{order_id}", "MerchantsController@workflow");
        Route::post("/workflow/{partner_id}/{order_id}", "MerchantsController@markAllTaskAsCompleted");
        Route::get("/workflow", "MerchantsController@workflows");
        Route::get("/workflow_data", "MerchantsController@workflow_data");

        Route::post("details/profile/addComment", "MerchantsController@addComment");
        Route::post("details/profile/addSubComment", "MerchantsController@addSubComment");

        Route::get("/merchant_data","MerchantsController@merchant_data");
        Route::get("/merchant_contact_info/{id}","MerchantsController@merchant_contact_info");
        Route::get("/merchant_payment_gateway/{id}","MerchantsController@merchant_payment_gateway");
        Route::get("/get_order_details/{id}","MerchantsController@getOrder");
        Route::get("/get_invoice_details/{id}","MerchantsController@getInvoice");
        Route::get("/get_recurring_details/{id}","MerchantsController@getRecurring");
        Route::post("/update_recurring_invoice","MerchantsController@updateRecurring");
        Route::get("/invoice/view/{id}","MerchantsController@viewInvoice");
        Route::get("/select_payment_frequencies","MerchantsController@select_payment_frequencies");
        Route::get("{id}/order_preview","MerchantsController@orderPreview");
        Route::get("{id}/order_sign","MerchantsController@orderSign");
        Route::post("{id}/order_sign","MerchantsController@orderSigned");
        Route::get("{id}/process_order","MerchantsController@processOrder");
        Route::get("/sendEmailOrder/{id}","MerchantsController@sendEmailOrder");
        Route::get("/sendWelcomeEmail/{id}","MerchantsController@sendWelcomeEmail");
        Route::get("/create","MerchantsController@create");
        Route::post("/store","MerchantsController@storeMerchant");
        Route::post("/updateinfo/{id}","MerchantsController@updateMerchantInfo");
        Route::post("/updateaddress/{id}","MerchantsController@updateMerchantAddress");
        Route::post("/updatecontact/{id}","MerchantsController@updateMerchantContact");
        Route::post("/updatepaymentgateway/{id}","MerchantsController@updateMerchantPaymentGateway");
        Route::post("/upload_attachment","MerchantsController@updateMerchantAttachment");
        Route::post("/create_order/{id}","MerchantsController@create_order");
        Route::get("/advance_merchants_search/{country}/{state}/{status}","MerchantsController@advance_merchants_search");
        Route::post("/update_order/{id}","MerchantsController@update_order");
        Route::post("/update_subtask","MerchantsController@update_subtask");
        Route::post("/update_subtask_status","MerchantsController@update_subtask_status");

        Route::get("/get_mid_details/{id}","MerchantsController@get_mid_details");
        Route::post("/updatepartnermid/{id}","MerchantsController@updatePartnerMID");


        Route::post("/add_subtask","MerchantsController@add_subtask");
        Route::post("/workflowComment","MerchantsController@add_subtask_comment");
        Route::post("/void_invoice","MerchantsController@voidInvoice");
        Route::post("/pay_invoice","MerchantsController@payInvoice");
        Route::post("/create_invoice/{id}","MerchantsController@createInvoice");
        Route::get("/","MerchantsController@list");
        Route::post("/updatepaymentmethod/{id}","MerchantsController@updateMechantPaymentMethod");
        Route::get("/details/{id}/payment_method","MerchantsController@payment_method");
        Route::get("/details/payment_method/{partner_id}/{id}/cancel","MerchantsController@cancel_payment_method");
        Route::get("{id}/confirm_preview","MerchantsController@confirmPreview");
        Route::get("{id}/confirm_page","MerchantsController@confirmPage");
        Route::get("{id}/confirm_email","MerchantsController@confirmEmail");
        Route::post("/cancel_merchant/{id}","MerchantsController@cancelMerchant");
        Route::get("/getCityState/{zip}","MerchantsController@getCityState");
        Route::resource("/","MerchantsController");

        //BRANCHES
        Route::get("/branch","BranchController@index");
        Route::get("/branch_data","BranchController@branch_data");
        Route::get("/merchant_branch_data/{id}","BranchController@merchant_branch_data");
        Route::get("/branchSearch/{type}/{type_value}/{status}", "BranchController@search");
        Route::get("/branchCreate","BranchController@create");
        Route::post("/branchStore","BranchController@storeBranch");
        Route::post("/branch/update_recurring_invoice","BranchController@updateRecurring");

        Route::post("/updateBranchInfo/{id}","BranchController@updateBranchInfo");
        Route::post("/updateBranchAddress/{id}","BranchController@updateBranchAddress");
        Route::post("/updateBranchContact/{id}","BranchController@updateBranchContact");
        Route::post("/updateBranchPaymentgateway/{id}","BranchController@updateBranchPaymentgateway");
        Route::post("/uploadBranchAttachment","BranchController@uploadBranchAttachment");


        Route::get("/branchDetails/{id}/dashboard", "BranchController@dashboard");
        Route::get("/branchDetails/dashboard/merchant-purchase/{id}", "BranchController@merchantPurchase");
        Route::get("/branchDetails/{id}/profile", "BranchController@profile");
        Route::get("/branchDetails/{id}/products", "BranchController@products");
        Route::get("/branchDetails/{id}/rmaServicing", "BranchController@rmaServicing");
        Route::get("/branchDetails/{id}/billing", "BranchController@billing");
        Route::get("/branchDetails/orders", "BranchController@orders");
        Route::get("/branchDetails/orders_data", "BranchController@orders_data");
        Route::get("/branchDetails/billing", "BranchController@invoices");
        Route::get("/branchDetails/invoices_data", "BranchController@invoices_data");

        // Route::get("/board_branch", "BranchController@boardBranch");
        // Route::get("/branch_board_data","BranchController@branch_board_data");
        // Route::get("/approve_branch", "BranchController@approveBranch");
        // Route::get("/branch_approve_data","BranchController@branch_approve_data");
        Route::get("/draft_branch", "BranchController@draftBranch");
        Route::get("/branch_draft_data","BranchController@branch_draft_data");

        
        Route::get("/confirm_branch/{id}","BranchController@confirmBranch");
        Route::get("/finalize_branch/{id}", "BranchController@finalizeBranch");
        Route::post("/{id}/declineBranch", "BranchController@declineBranch");

        Route::get("/branch_contact_info/{id}","BranchController@branch_contact_info");

        Route::post("/branchDetails/profile/addComment", "BranchController@addComment");
        Route::post("/branchDetails/profile/addSubComment", "BranchController@addSubComment");

        Route::get("/branchWorkflow/{id}/{order_id}", "BranchController@workflow");
        Route::post("/branchWorkflow/{partner_id}/{order_id}", "BranchController@markAllTaskAsCompleted");

        Route::get("/advance_branch_search/{country}/{state}/{status}","BranchController@advance_branch_search");


        //COPILOT
        Route::get("/details/{id}/cardconnect","CardConnectController@cardconnect");
        Route::get("/copilot_merchant/{id}/{action}","CardConnectController@coPilotMerchantSave");
        Route::get("/copilot_get_merchant/{id}/{action}","CardConnectController@coPilotMerchantRetrieve");
        Route::get("/copilot_get_equipments/{supplierCode}/{typeCode}/{pageNumber}/{pageSize}","CardConnectController@coPilotEquipmentRetrieve");
        Route::get("/copilot_list_equipments/{supplierCode}/{typeCode}","CardConnectController@coPilotEquipmentList");
        Route::post("/copilot_create_order/{id}","CardConnectController@coPilotCreateOrder");
        Route::get("/copilot_get_order/{id}","CardConnectController@coPilotGetOrder");
        Route::post("/copilot_update_order/{id}","CardConnectController@coPilotUpdateOrder");
        Route::get("/copilot_cancel_order/{id}/{orderId}","CardConnectController@coPilotCancelOrder");
        Route::get("/copilot_list_orders/{id}","CardConnectController@coPilotOrderList");
        Route::post("/copilot_create_billing/{id}","CardConnectController@coPilotCreateBillPlan");
        Route::post("/copilot_update_owner/{id}","CardConnectController@coPilotUpdateOwner");
        Route::get("/copilot_request_signature/{id}","CardConnectController@coPilotRequestSignature");
        Route::get("/cardpoint","CardConnectController@test");
        Route::post("/cardpoint/save_profile/{id}","CardConnectController@cardPointSaveProfile");
        Route::get("/cardpoint/get_profile/{id}/{profileId}","CardConnectController@cardPointGetProfile");
        Route::get("/cardpoint/delete_profile/{id}/{profileId}","CardConnectController@cardPointDeleteProfile");

        Route::get("/invoices","MerchantsController@invoices_management");
        
        Route::post('/unlinkTaskToTicket', 'MerchantsController@unlinkTaskToTicket');
        Route::prefix('/workflow/{productOrderId}/{subTaskDetailId}/comments')->group(function () {
            Route::get("/", "ProductOrderCommentController@index");
            Route::post("/", "ProductOrderCommentController@store");
            Route::get("/viewers", "ProductOrderCommentController@indexViewers");
            Route::post("/{commentId}", "ProductOrderCommentController@updateViewers");
        });
    });
    Route::namespace("Leads")->prefix('leads')->name('leads.')->group(function () {
        Route::get("/createLeadProspect", "LeadsProspectsController@create");
        Route::post("/createLeadProspect", "LeadsProspectsController@createLeadProspect");
        Route::get("/loadUplineLIst", "LeadsProspectsController@loadUplineLIst");
        Route::get("/getCountryCallingCode", "LeadsProspectsController@getCountryCallingCode");

        Route::post("/uploadfile", "LeadsProspectsController@uploadfile");

        Route::post("/interestedProducts/addInterestedProduct", "LeadsProspectsController@addInterestedProduct");
        Route::get("/interestedProducts/deleteInterestedProduct", "LeadsProspectsController@deleteInterestedProduct");
        Route::get("/interestedProducts/getInterestedProducts", "LeadsProspectsController@getInterestedProducts");

        Route::get("/details/profile/{partner_id}", "LeadsProspectsController@profile");
        Route::post("details/profile/updateLeadProspect", "LeadsProspectsController@updateLeadProspect");
        Route::post("details/profile/addComment", "LeadsProspectsController@addComment");
        Route::post("details/profile/addSubComment", "LeadsProspectsController@addSubComment");
        // Route::post("details/profile/convertToMerchant", "LeadsProspectsController@convertToMerchant");
        Route::post("details/profile/convertToProspect", "LeadsProspectsController@convertToProspect");

        Route::post("details/contact/updateContact", "LeadsProspectsController@updateContact");
        Route::get("/details/contact/{partner_id}", "LeadsProspectsController@contact");

        Route::get("/details/interested/{partner_id}", "LeadsProspectsController@interested");

        Route::get("/details/applications/{partner_id}", "LeadsProspectsController@application");
        // Route::get("/select_payment_frequencies","LeadsProspectsController@select_payment_frequencies");

        Route::post("/details/appointment/saveCalendarActivity", "LeadsProspectsController@saveCalendarActivity");
        Route::post("/details/appointment/saveCalendarReminder", "LeadsProspectsController@saveCalendarReminder");
        Route::get("/details/appointment/getCalendarProfiles", "LeadsProspectsController@getCalendarProfiles");
        Route::get("/details/appointment/getCalendarActivities", "LeadsProspectsController@getCalendarActivities");
        Route::get("/details/appointment/{partner_id}", "LeadsProspectsController@appointment");

        Route::get("/details/summary/{partner_id}", "LeadsProspectsController@summary");

        Route::get("/incoming/updateIncomingLeadRequest", "LeadsProspectsController@updateIncomingLeadRequest");
        Route::get("/incoming", "LeadsProspectsController@incoming");
        Route::get("/advance_leads_prospects_search/{type}/{products}", "LeadsProspectsController@advance_leads_prospects_search");
        Route::post("/deleteLead","LeadsProspectsController@deleteLead");

        Route::get("/getAppointments/{partner_id}", "LeadsProspectsController@getAppointments");

        Route::resource("/", "LeadsProspectsController");
    });
    Route::namespace("Prospects")->prefix('prospects')->name('prospects.')->group(function () {
        Route::get("/createLeadProspect", "ProspectsController@create");
        Route::post("/createLeadProspect", "ProspectsController@createLeadProspect");
        Route::get("/loadUplineLIst", "ProspectsController@loadUplineLIst");
        Route::get("/getCountryCallingCode", "ProspectsController@getCountryCallingCode");

        Route::post("/uploadfile", "ProspectsController@uploadfile");

        Route::post("/interestedProducts/addInterestedProduct", "ProspectsController@addInterestedProduct");
        Route::get("/interestedProducts/deleteInterestedProduct", "ProspectsController@deleteInterestedProduct");
        Route::get("/interestedProducts/getInterestedProducts", "ProspectsController@getInterestedProducts");

        Route::get("/details/profile/{partner_id}", "ProspectsController@profile");
        Route::post("details/profile/updateLeadProspect", "ProspectsController@updateLeadProspect");
        Route::post("details/profile/addComment", "ProspectsController@addComment");
        Route::post("details/profile/addSubComment", "ProspectsController@addSubComment");
        Route::post("details/profile/convertToMerchant", "ProspectsController@convertToMerchant");

        Route::post("details/contact/updateContact", "ProspectsController@updateContact");
        Route::get("/details/contact/{partner_id}", "ProspectsController@contact");

        Route::get("/details/interested/{partner_id}", "ProspectsController@interested");

        Route::post("details/applications/create_order/{id}","ProspectsController@create_order");
        Route::get("/details/applications/{partner_id}", "ProspectsController@application");
        Route::get("/select_payment_frequencies","ProspectsController@select_payment_frequencies");

        Route::post("/details/appointment/saveCalendarActivity", "ProspectsController@saveCalendarActivity");
        Route::post("/details/appointment/saveCalendarReminder", "ProspectsController@saveCalendarReminder");
        Route::get("/details/appointment/getCalendarProfiles", "ProspectsController@getCalendarProfiles");
        Route::get("/details/appointment/getCalendarActivities", "ProspectsController@getCalendarActivities");
        Route::get("/details/appointment/{partner_id}", "ProspectsController@appointment");

        Route::get("/details/summary/{partner_id}", "ProspectsController@summary");

        Route::get("/incoming/updateIncomingLeadRequest", "ProspectsController@updateIncomingLeadRequest");
        Route::get("/incoming", "ProspectsController@incoming");
        Route::get("/advance_leads_prospects_search/{type}/{products}", "ProspectsController@advance_leads_prospects_search");
        Route::post("/deleteProspect","ProspectsController@deleteProspect");
        Route::resource("/", "ProspectsController");
    });

    Route::namespace("Tickets")->prefix("tickets")->name("tickets.")->group(function () {
        Route::get('/faq', 'TicketFaqController@show')->name('faq.show');
        Route::get('/faq/edit', 'TicketFaqController@edit')->name('faq.edit');
        Route::post('/faq/edit', 'TicketFaqController@save')->name('faq.save');

        Route::resource("/", "TicketController");
        Route::get("/create/{productId}/dependencies", "TicketController@createDependencies");
        Route::get("/list-ajax","TicketController@indexAjax");
        Route::post('/store-ajax', 'TicketController@storeAjax')->name('storeAjax');
        Route::post("/submitTicketReply/{ticketId}/{status}", "TicketController@submitTicketReply");

        Route::get("/{id}/edit", "TicketController@edit");
        Route::put("/{id}/update", "TicketController@update");
        Route::get("/{id}/show", "TicketController@show");
        Route::post("/{id}/store-comment", "TicketController@storeComment");
        Route::post("/update-status", "TicketController@updateStatus");
        Route::post("/filter", "TicketController@filter");
        Route::post("/product", "TicketController@filterProduct");
        Route::post('/merge', "TicketController@merge");
        Route::get("/partner-users/{partnerTypeId}", "TicketController@partnerUsers");
        Route::post("/assignees", "TicketController@getAssignees");

        Route::get('/ticket-details', 'TicketController@getTicketReplies');
        Route::get('/ticket-reply-templates/{id}', 'TicketController@getReplyTemplate');

        Route::get("/adminTicket", "TicketController@adminTicket");
        Route::get("/adminInternal", "TicketController@adminInternal");
        Route::get("/internal", "TicketController@getInternalTickets");
        Route::get("/admin", "TicketController@getPartnerOrMerchantTickets");
        Route::get("/getDepartments", "TicketController@getDepartments");
        Route::post("/assignToMeTickets", "TicketController@assignToMeTickets");
        Route::post("/assignTickets", "TicketController@assignTickets");
        Route::post("/deleteTickets", "TicketController@deleteTickets");
        Route::post("/mergeTickets", "TicketController@mergeTickets");

        Route::get("/create-ticket-via-email", "TicketController@createTicketsViaEmail");
        Route::get("/reply-template", "TicketController@listReplyTemplate");
        Route::get("/reply-template/{id}/edit", "TicketController@editReplyTemplate");
        Route::get("/reply-template-data", "TicketController@getReplyTemplateData");
        Route::get("/reply-template-create", "TicketController@createTemplateData");
        Route::post("/reply-template-store", "TicketController@storeTemplateData");
        Route::post("/reply-template/update/{id}", "TicketController@updateReplyTemplate");
        Route::get("/reply-template/{id}/delete", "TicketController@deleteReplyTemplate");
        Route::get("/getUsersByDepartment/{department}","TicketController@getUsersByDepartment");

        Route::get('/productOrders', 'TicketController@indexProductOrders');
        Route::get('/subTaskDetails/{productOrderId}', 'TicketController@indexSubTaskDetails');

    });

    Route::namespace("Billing")->prefix("billing")->name("billing.")->group(function () {
        Route::get("/report_detail", "ReportsController@report_detail");
        Route::get("/report_payout", "ReportsController@report_payout");
        Route::get("/report_summary", "ReportsController@report_summary");
        Route::get("/comm_report", "ReportsController@comm_report");
        Route::get("/report_ccsr", "ReportsController@report_ccsr");
        Route::get("/report_cscr", "ReportsController@report_cscr");
        Route::get("/report_itl", "ReportsController@report_itl");
        Route::get("/report_mcr", "ReportsController@report_mcr");
        Route::get("/report_pl", "ReportsController@report_pl");
        Route::get("/report_ms", "ReportsController@report_ms");
        Route::get("/report-ms-generate/{month}/{year}", "ReportsController@report_ms_generate");
        Route::get("/report-ms-export/{month}/{year}", "ReportsController@report_ms_export");

        Route::get("/ach_report", "ReportsController@ach_report");

        Route::get("/ach-generate-report/{from}/{to}", "ReportsController@ach_generate_report");
        Route::get("/ach-generate-residual/{from}/{to}", "ReportsController@ach_generate_residual");

        Route::get("/ach-export-report/{from}/{to}", "ReportsController@ach_export_report");
        Route::get("/ach-export-residual/{from}/{to}", "ReportsController@ach_export_residual");

        Route::get("/report_new_partner", "ReportsController@report_new_partner");
        Route::get("/report_new_partner/{partnerId}/{type}/{from}/{to}/{export}", "ReportsController@generate_report_new_partner");
        Route::get("/report_new_partner_data/{partnerId}/{type}/{from}/{to}/{partnerType}/{status}", "ReportsController@new_partner_data");
        Route::get("/report_new_partner_data_export/{partnerId}/{type}/{from}/{to}/{partnerType}/{status}", "ReportsController@new_partner_data_export");
        Route::post("/report_new_partner_product_data/{productId}/{partnerId}/{type}/{from}/{to}", "ReportsController@new_partner_product_data");
        Route::post("/report_new_partner_graph_data/{partnerId}/{type}/{from}/{to}", "ReportsController@new_partner_graph_data");

        Route::get("/report_new_business", "ReportsController@report_new_business");
        Route::get("/report_new_business/{type}/{from}/{to}/{export}", "ReportsController@generate_report_new_business");

        Route::get("/report_product", "ReportsController@report_product");
        Route::get("/report_product/{id}/{type}/{from}/{to}/{export}", "ReportsController@generate_report_product");

        Route::get("/report_branches", "ReportsController@report_branches");
        Route::get("/report_branches/{id}/{type}/{from}/{to}/{export}", "ReportsController@generate_report_branches");

        Route::get("/report_commission", "ReportsController@report_commission");
        Route::get("/commission-generate-report/{from}/{to}", "ReportsController@commission_generate_report");
        Route::get("/commission-export-report/{from}/{to}", "ReportsController@commission_export_report");

        Route::get("/report_commission_detailed", "ReportsController@report_commission_detailed");
        Route::get("/commission-generate-report-detailed/{from}/{to}", "ReportsController@commission_generate_report_detailed");
        Route::get("/commission-export-report-detailed/{from}/{to}", "ReportsController@commission_export_report_detailed");

        Route::get("/report_export_log", "ReportsController@report_export_log");
        Route::get("/export-log-generate-report/{from}/{to}", "ReportsController@export_log_generate_report");

        Route::get("/report_billing", "ReportsController@report_billing");
        Route::get("/getInvoiceList/{id}/{status}", "ReportsController@getInvoiceList");
        Route::get("/report_billing_data/{status}", "ReportsController@export_report_billing_data");

        Route::get("/report", "ReportsController@index");
        Route::resource("/", "ReportsController");
    });

    Route::namespace("Calendar")->prefix("calendar")->name("calendar.")->group(function () {
        Route::get("/getCalendarProfiles", "CalendarController@getCalendarProfiles");
        Route::get("/getCalendarActivities", "CalendarController@getCalendarActivities");
        Route::post("/saveCalendarActivity", "CalendarController@saveCalendarActivity");
        Route::post("/saveCalendarReminder", "CalendarController@saveCalendarReminder");

        Route::get("/getGClient/{code}", "CalendarController@getGClient");
        Route::get("/getOClient", "CalendarController@getOClient");

        Route::get("/googleCallback", "CalendarController@googleCallback");
        Route::get("/outlookCallback", "CalendarController@outlookCallback");

        Route::resource("/", "CalendarController");
    });

    Route::namespace("Training")->prefix("training")->name("training.")->group(function () {
        Route::get("/training_list", "TrainingController@list");
        Route::get("/training_module/{id}", "TrainingController@module");
        Route::get("/setup", "TrainingController@setup");
        Route::get("/setupCreate", "TrainingController@setupCreate");
        Route::get("/setupEdit/{id}", "TrainingController@setupEdit");
        Route::get("/accessControl", "TrainingController@accessControl");
        Route::get("/accessControlEdit", "TrainingController@accessControlEdit");
        Route::post("/createModule", "TrainingController@createModule");
        Route::post("/updateModule/{id}", "TrainingController@updateModule");
        Route::resource("/", "TrainingController");
    });

    Route::namespace("Vendors")->prefix("vendors")->name("vendors.")->group(function () {
        Route::get("/details/profile", "VendorsController@profile");
        Route::get("/details/contacts", "VendorsController@contacts");
        Route::get("/details/products", "VendorsController@products");
        Route::resource("/", "VendorsController");
    });

    Route::namespace("Extras")->prefix("extras")->name("extras.")->group(function () {
        Route::get("/notification", "ExtrasController@notification");
        Route::get("/notification/updateStarred", "ExtrasController@updateStarred");
        Route::get("/notification/tagAndRedirect", "ExtrasController@tagAndRedirect");
        Route::post("/notification/updateAsRead", "ExtrasController@updateAsRead");
        Route::post("/notification/updateAsUnread", "ExtrasController@updateAsUnread");
        Route::get("/chatCenter", "ExtrasController@chatCenter");
        Route::get("/friendRequest", "ExtrasController@friendRequest");
        Route::get("/chats/getPreloadUsers", "ExtrasController@addUsers");
        Route::get("/chats/addToGroup", "ExtrasController@addToGroup");
        Route::get("/chats/addAsContact", "ExtrasController@addAsContact");
        Route::post("/chats/sendFriendRequest", "ExtrasController@sendFriendRequest");
        Route::post("/chats/acceptRequest", "ExtrasController@acceptRequest");
        Route::post("/chats/declineRequest", "ExtrasController@declineRequest");

        Route::get("/changePassword", "ExtrasController@changePassword");
        Route::post("/updatePassword/{id}", "ExtrasController@updatePassword");

        Route::get("/search", "ExtrasController@generalSearch");

        Route::get('/users/me/settings/edit', 'ExtrasController@editSettings')->name('user.settings.edit');
        Route::post('/users/me/settings/edit', 'ExtrasController@updateSettings')->name('user.settings.update');

        Route::post("/suggestion", "ExtrasController@createSuggestion");
        Route::get("/getCityAndState","ExtrasController@getCityAndState");
    });

    Route::namespace("Api")->prefix("api")->name("api.")->group(function () {

        Route::get("/create-payment-csv", "ApiController@createPaymentCSV");
    
    });

    Route::namespace("Drafts")->prefix("drafts")->name("drafts.")->group(function () {
        Route::get("/draftMerchant/{id}/{type_id}/edit","DraftPartnerController@draftMerchant");
        Route::get("/draftBranch/{id}/{type_id}/edit","DraftPartnerController@draftBranch");
        Route::get("/draftLeadProspect/{id}/{type_id}/edit","DraftPartnerController@draftLeadProspect");
        Route::get("/draftPartners/{id}/{type_id}/edit","DraftPartnerController@draftPartners");
        Route::post("/store", "DraftPartnerController@store");
        Route::post("/deleteDraftApplicant", "DraftPartnerController@deleteDraftApplicant");
        Route::resource("/", "DraftPartnerController");
    });

    Route::namespace('Analytics')->prefix('admin/analytics')->name('analytics.')->group(function() {
        Route::get('/', 'AnalyticsController@index')->name('index');
        Route::get('/users', 'AnalyticsController@users')->name('users.index');
        Route::get('/users/{id}', 'AnalyticsController@user')->name('users.show');
    });


    /*
    |--------------------------------------------------------------------------
    | Developers
    |--------------------------------------------------------------------------
    */
    Route::namespace('Developers')->prefix('developers')->name('developers.')->group(function() {
        /*
        |----------------------------------------------------------------------
        | Api Keys
        |----------------------------------------------------------------------
        */
        Route::prefix('api-keys')->name('apiKeys.')->group(function() {
            Route::get('/', 'ApiKeyController@index')->name('index');
            Route::post('/', 'ApiKeyController@store')->name('store');
            Route::delete('/{id}', 'ApiKeyController@destroy')->name('destroy');
        });

        /*
        |----------------------------------------------------------------------
        | Api Documentation
        |----------------------------------------------------------------------
        */
        Route::prefix('api-documentations')->name('apiDocumentations.')->group(function() {
            Route::get('/', 'ApiDocumentationController@index')->name('index');
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::namespace('Reports')->prefix('reports')->name('reports.')->group(function() {
        /*
        |----------------------------------------------------------------------
        | User Activity
        |----------------------------------------------------------------------
        */
        Route::namespace('UserActivities')->prefix('user-activities')->name('userActivities.')->group(function() {
            Route::get('/', 'ReportController@index')->name('index');
            Route::post('/report', 'ReportController@report')->name('show');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Supplier Leads
    |--------------------------------------------------------------------------
    */
    Route::namespace('SupplierLeads')->prefix('supplier-leads')->name('supplierLeads.')->group(function() {
        Route::get('/create', 'SupplierLeadController@create')->name('create');
        Route::get('/', 'SupplierLeadController@index')->name('index');
        Route::post('/', 'SupplierLeadController@store')->name('store');
        Route::get('/{id}/summary', 'SupplierLeadController@showOverview')->name('show.overview');
        Route::get('/{id}/contacts', 'SupplierLeadController@showContacts')->name('show.contacts');
        Route::get('/{id}/products', 'SupplierLeadController@showProducts')->name('show.products');
        Route::get('/{id}', 'SupplierLeadController@show')->name('show');
        Route::put('/{id}/contacts', 'SupplierLeadController@updateContacts')->name('update.contacts');
        Route::put('/{id}/products', 'SupplierLeadController@updateProducts')->name('update.products');
        Route::put('/{id}', 'SupplierLeadController@update')->name('update');
        Route::post("/uploadfile", "SupplierLeadController@uploadfile");
    });

    Route::get('/processFlow', 'ProcessFlowController@index');

    /*
    |--------------------------------------------------------------------------
    | Company Settings - Ticket Configuration
    |--------------------------------------------------------------------------
    */
    Route::namespace('CompanySettings')->prefix('admin/company_settings/ticket-config')->name('admin.ticketConfig.')->group(function() {
        Route::get('/companies/{companyId}', 'TicketConfigController@showConfiguration')->name('show');
        Route::post('/products/clone', 'TicketConfigController@cloneConfiguration')->name('products.clone');
        
        Route::prefix('/companies/{companyId}/products/{productId}/')->name('products.')->group(function() {
            Route::post('/ticket-issue-types', 'TicketConfigController@storeTicketIssueType')->name('ticketIssueType.store');
            Route::post('/ticket-reasons', 'TicketConfigController@storeTicketReason')->name('ticketReason.store');
        });

        Route::prefix('/products/{productId}/')->name('products.')->group(function() {
            Route::get('/', 'TicketConfigController@getTicketConfiguration')->name('show');
            Route::delete('ticket-issue-types/{titId}', 'TicketConfigController@deleteTicketIssueType')->name('ticketIssueType.delete');
            Route::delete('ticket-reasons/{trId}', 'TicketConfigController@deleteTicketReason')->name('ticketReason.delete');
        });

        
    });
});

Auth::routes();

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');