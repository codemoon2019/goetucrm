<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use App\Models\Access;
use App\Models\Partner;

class ProcessFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        /**
         Products
         */
        $canAddProduct = Access::hasPageAccess('product','add',true) ? true : false;
        $canViewProduct = Access::hasPageAccess('product', 'view', true) ? true : false;
        $hasWorkflow = Access::hasPageAccess('product', 'work flow', true) ? true : false;
        $hasProductFee = Access::hasPageAccess('product', 'commission template', true) ? true : false;
        $canAccessProduct = $canAddProduct || $canViewProduct || $hasWorkflow || $hasProductFee;

        /**
         Partners
         */
        $c1 = Access::hasPageAccess('company','add',true) ? true : false;
        $c2 = Access::hasPageAccess('iso','add',true) ? true : false;
        $c3 = Access::hasPageAccess('sub iso','add',true) ? true : false;
        $c4 = Access::hasPageAccess('agent','add',true) ? true : false;
        $c5 = Access::hasPageAccess('sub agent','add',true) ? true : false;
        $canAddPartner = $c1 || $c2 || $c3 || $c4 || $c5;
        $v1 = Access::hasPageAccess('company','view',true) ? true : false;
        $v2 = Access::hasPageAccess('iso','view',true) ? true : false;
        $v3 = Access::hasPageAccess('sub iso','view',true) ? true : false;
        $v4 = Access::hasPageAccess('agent','view',true) ? true : false;
        $v5 = Access::hasPageAccess('sub agent','view',true) ? true : false;
        $canViewPartner = $v1 || $v2 || $v3 || $v4 || $v5;
        $canViewDepartment = Access::hasPageAccess('admin', 'department', true) ? true : false;
        $canViewUser = Access::hasPageAccess('users', 'view', true) ? true : false;
        $canAccessPartners = $canAddPartner || $canViewPartner || $canViewDepartment || $canViewUser;

        /**
         Merchants
         */
        $canCreateMerchant = Access::hasPageAccess('merchant','add',true) ? true : false;
        $canCreateOrder = Access::hasPageAccess('merchant','create order',true) ? true : false; 
        $canViewOrder = Access::hasPageAccess('merchant','order list',true) ? true : false;
        $canViewWorkFlow = Access::hasPageAccess('merchant','work flow',true) ? true : false;
        $canViewBilling = Access::hasPageAccess('merchant','view invoice',true) ? true : false;
        $canAccessMerchants = $canCreateMerchant || $canCreateOrder || $canViewOrder || $canViewWorkFlow || $canViewBilling;

        /**
         Reports
         */
        $canAccessReport = Access::hasPageAccess('billing','view',true) ? true : false;


        /**
         Leads
         */
        $canCreateLead = Access::hasPageAccess('lead','add',true) ? true : false;
        $canViewLead = Access::hasPageAccess('lead','view',true) ? true : false;
        $canAccessLeads = $canCreateLead || $canViewLead;

        /**
         Prospects
         */
        $canCreateProspect = Access::hasPageAccess('prospect','add',true) ? true : false;
        $canViewProspect = Access::hasPageAccess('prospect','view',true) ? true : false;
        $canAccessProspects = $canCreateProspect || $canViewProspect;


        /**
         Tickets
         */
        $canCreateTicket = Access::hasPageAccess('ticketing','add',true) ? true : false;
        $canViewTicket = Access::hasPageAccess('ticketing','view',true) ? true : false;
        $canAccessTickets = $canCreateTicket || $canViewTicket;

        /**
         Calendar
         */
        $canAccessCalendar = Access::hasPageAccess('my calendar','view',true) ? true : false;

        /**
         Training
         */
        $canAccessTraining = Access::hasPageAccess('training','view',true) ? true : false;

        /**
         Merchant's List for Order
         */
        $partner_access=-1;

        $access = session('all_user_access');
        $admin_access = isset($access['admin']) ? $access['admin'] : "";
        if (strpos($admin_access, 'super admin access') === false){
            $partner_access = Partner::get_partners_access(auth()->user()->reference_id);      
        }
        $merchants = Partner::get_partners($partner_access,3,auth()->user()->reference_id, -1, -1,"","");

        return view("admin.processflow",compact('canAddProduct','canViewProduct','hasWorkflow','hasProductFee','canAccessProduct','canAddPartner','canViewPartner','canViewDepartment','canViewUser','canAccessPartners','canCreateMerchant','canCreateOrder','canViewOrder','canViewWorkFlow','canViewBilling','canAccessMerchants','canAccessReport','canCreateLead','canViewLead','canAccessLeads','canCreateProspect','canViewProspect','canAccessProspects','canCreateTicket','canViewTicket','canAccessTickets','canAccessCalendar','canAccessTraining','merchants'));


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
