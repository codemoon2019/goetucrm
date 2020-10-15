@extends('layouts.app')

@php
$access = session('all_user_access');
$merchantaccess = isset($access['branch']) ? $access['branch'] : "";

if (strpos($merchantaccess, 'edit') === false){
$canEdit = false;
}else{
$canEdit = true;
}

if (strpos($merchantaccess, 'verify contact ssn') === false){
$canVerifySSN = false;
}else{
$canVerifySSN = true;
}

if (strpos($merchantaccess, 'board branch') === false){
$canBoard = false;
}else{
$canBoard = true;
}

if (strpos($merchantaccess, 'approve branch') === false){
$canApprove = false;
}else{
$canApprove = true;
}

if (strpos($merchantaccess, 'display complete ssn and bank info') === false){
$maskInfo = true;
}else{
$maskInfo = false;
}


@endphp

@section('style')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
<style type="text/css">
    .custom-fl-right {
        float: right;
    }
    .ta-right {
        text-align: right;
    }
    #post-comment select {
        display: inline-block;
        width: auto;
        height: 32px;
        padding: 4px 8px;
    }
    #post-comment .custom-fl-right,
    .comment-post-reply .custom-fl-right {
        margin-top: 5px;
    }
    .comment-view {
        margin-top: 0px !important;
    }
    .comment-view a {
        display: inline-block;
        vertical-align: top;
        padding: 1px 4px;
        background-color: #FFFFFF;
        margin-right: 5px;
        box-shadow: 0px 1px 4px #CDCDCD;
    }
    #comment-list .comment {
        margin: 10px 0;
        width: 100%;
        box-sizing: border-box;
        padding: 10px;
        background-color: #FFFFFF;
    }
    #comment-list .comment .comment-block {
        margin: 0 10px;
        padding: 10px 0;
        border-bottom: 1px solid #D7D7D7;
    }
    #comment-list .comment .comment-reply {
        margin-left: 30px;
        display: none;
    }
    #comment-list .comment .comment-block .comment-author {
        font-weight: 600;
    }
    #comment-list .comment .comment-block .comment-date {
        color: #3A3A3A;
    }
    #comment-list .comment .comment-block .comment-desc {
        padding-top: 6px;
    }
    #comment-list .comment .comment-options {
        padding: 10px;
    }
    #comment-list .comment .comment-options a {
        margin-right: 10px;
    }
    #comment-list .comment .comment-options a.showless,
    #comment-list .comment .comment-options a.cancelreply {
        display: none;
    }
    #comment-list .comment .comment-post-reply {
        display: none;
        margin: 0 10px;
        padding-top: 10px;
    }
    .discussion {
        box-shadow: 0px 0px 2px #E6E6E6;
    }
    .ticket-img-xs {
        box-shadow: 0 0 2.5px #000000;
        height: 20px;
        width: 20px;
        border: 2px solid #ffffff;
        border-radius: 50%;
    }
    p {
		margin-left: 24px;
	}
	.box-title {
		padding: 10px 15px;
		font-size: 22px !important;
	}
	.box-body {
		padding: 25px;
	}
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Branch
            <!-- <small>Dito tayo magpapasok ng different pages</small> -->
        </h1>
        <ol class="breadcrumb">
            <li><a href="/">Dashboard</a></li>
            <li><a href="/merchants/branch">Branch</a></li>
            <li class="active">{{$merchant->partner_company->company_name}}</li>
        </ol>
        <div class="dotted-hr"></div>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">
        <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
            @if ($merchant->merchant_status_id == App\Models\MerchantStatus::CANCELLED_ID || $merchant->merchant_status_id == App\Models\MerchantStatus::DECLINED_ID)
                <h3 style="color: red">{{ $merchant->partner_company->company_name . ' - ' . $merchant->merchantStatus->description }}</h3>
                <h5>Reason of Action: {{ $merchant->reason_of_action }}</h5>
            @else
                <h3>
                    <span>{{ $merchant->partner_company->company_name }}</span>

                    @if ($merchant->merchant_status_id == App\Models\MerchantStatus::BOARDING_ID)
                        <span class="badge badge-primary">For Boarding</span> 
                    @elseif($merchant->merchant_status_id == App\Models\MerchantStatus::FOR_APPROVAL_ID)
                        <span class="badge badge-success">For Approval</span> 
                    @endif
                </h3>
            @endif

            <a href="/merchants/branch" class="btn btn-default pull-right" style="margin-top: -40px">Back to Branches</a>
        </div>
        <div class="nav-tabs-custom">
            @include("branch.details.branchtabs")
            <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                <li><a href="#overview" id="ovview" data-toggle="tab" aria-expanded="true">Summary</a></li>
                @if($isInternal)
                <li class="active"><a href="#business-info" id="businfo" data-toggle="tab" aria-expanded="true">Business
                        Information</a></li>
                <li class=""><a href="#addresses" id="adrs" data-toggle="tab" aria-expanded="false">Addresses</a></li>
                <li class=""><a href="#owners-info" id="ownrinf" data-toggle="tab" aria-expanded="false">Owner's
                        Information</a></li>
                <li class=""><a href="#mid-list" id="mid" data-toggle="tab" aria-expanded="false">MID</a></li>
                <li class=""><a href="#attachments" id="atch" data-toggle="tab" aria-expanded="false">Attachments</a></li>
                <li class=""><a href="#payment-gateway" id="paygate" data-toggle="tab" aria-expanded="false">Payment
                        Gateway</a></li>
                @endif
                <li class=""><a href="#discussion" id="dis" data-toggle="tab" aria-expanded="false">Notes</a></li>
            </ul>

            <div class="tab-content no-padding">
            <div class="tab-pane" id="overview">
                    <div class="row">
                        <!-- Company Information -->
                        <div class="col-md-5">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h2 class="box-title">Company Information</h2>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <strong><i class="fa fa-level-up margin-r-5"></i> Upline</strong>

                                                <p class="text-muted">
                                                    @if($merchant->parent_id > 0)
                                                        @foreach($upline_partners as $up)
                                                            @if($merchant->parent_id == $up->parent_id)
                                                                {{ $up->partner_id_reference }} - {{ $up->company_name }} - {{ $up->upline_partner }}
                                                            @endif
                                                        @endforeach
                                                    @else($merchant->parent_id)
                                                        No Assigned
                                                    @endif
                                                    <br>
                                                </p>
                                                
                                            </div>
                                            <div class="form-group">
                                                <strong><i class="fa fa-building margin-r-5"></i> DBA</strong>

                                                <p class="text-muted">
                                                    @isset($merchant->partner_company->company_name)
                                                        {{ $merchant->partner_company->company_name }}	
                                                    @endisset
                                                    <br>								
                                                </p>
                                                
                                            </div>
                                            <div class="form-group">
                                                <strong><i class="fa fa-industry margin-r-5"></i> Business Industry - MCC</strong>

                                                <p class="text-muted">
                                                    @foreach ($businessTypeGroups as $groupName => $businessTypes)
                                                        @foreach ($businessTypes as $item)
                                                            @if($item->mcc == $merchant->business_type_code)  {{ $groupName }} - {{ $item->description }} - {{ $item->mcc }} @endif
                                                        @endforeach
                                                    @endforeach
                                                    <br>
                                                </p>

                                                
                                            </div>
                                            <div class="form-group">
                                                <strong><i class="fa fa-book margin-r-5"></i> Legal Business Name</strong>

                                                <p class="text-muted">
                                                    @isset($merchant->partner_company->business_name)
                                                        {{ $merchant->partner_company->business_name }}
                                                    @endisset
                                                    <br>
                                                </p>

                                                
                                            </div>
                                            <div class="form-group">
                                                <strong><i class="fa fa-flag margin-r-5"></i> Ownership</strong>

                                                <p class="text-muted">
                                                    @isset($merchant->partner_company->ownership)
                                                        @foreach($ownership as $item)
                                                            @if($merchant->partner_company->ownership == $item->code)
                                                                {{ $item->name }}
                                                            @endif
                                                        @endforeach
                                                    @endisset
                                                    <br>									
                                                </p>

                                            </div>
                                            <div class="form-group">
                                                <strong><i class="fa fa-sticky-note margin-r-5"></i> Tax ID Number</strong>

                                                <p class="text-muted" id="taxIDNumber">
                                                    @isset($merchant->tax_id_number)
                                                        {{ $merchant->tax_id_number }}	
                                                    @endisset
                                                    <br>								
                                                </p>
                                                
                                            </div>
                                            <div class="form-group">
                                                <strong><i class="fa fa-phone margin-r-5"></i> Business Phone 1</strong>

                                                <p class="text-muted">
                                                    @isset($merchant->partner_company->phone1)
                                                        {{ $merchant->partner_company->country_code . $merchant->partner_company->phone1 }}	
                                                    @endisset
                                                    <br>								
                                                </p>

                                            </div>
                                            <div class="form-group">
                                                <strong><i class="fa fa-envelope margin-r-5"></i> Email Address</strong>

                                                <p class="text-muted">
                                                    @isset($merchant->partner_company->email)
                                                        {{ $merchant->partner_company->email }}	
                                                    @endisset
                                                    <br>								
                                                </p>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <select id="optAddress" class="form-control">
                                        <option value='businessAdd'>Business Address Information</option>
                                        <option value='mailingAdd'>Mailing Address Information</option>
                                        <option value='shippingAdd'>Billing Address Information</option>
                                    </select>
                                </div>
                                <!-- Business Address -->	
                                <div class="box-body" id="businessAdd">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <strong> Business Address</strong>

                                                <div class="d-flex flex-row" style="margin-left: 24px;padding: 10px 10px 10px 0;">
                                                    <span class="text-muted">
                                                    @isset($merchant->partner_company->address1)
                                                        {{ $merchant->partner_company->address1 }}
                                                    @endisset
                                                    <br>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between" style="margin-left: 24px;">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_company->city)
                                                            {{ $merchant->partner_company->city }}
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class=" text-muted align-self-center">(City)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_company->state)
                                                            @foreach($states as $item)
                                                                @if($item->abbr == $merchant->partner_company->state)
                                                                    {{ $item->name }} ({{ $item->abbr }})
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(State)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_company->country)
                                                            @foreach($country as $item)
                                                                @if($item->name == $merchant->partner_company->country)
                                                                    {{ $item->name }} ({{ $item->iso_code_2 }})
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(Country)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_company->zip)
                                                            {{ $merchant->partner_company->zip }}
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(Zip)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Mailing Address -->
                                <div class="box-body hide" id="mailingAdd">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <strong> Mailing Address</strong>

                                                <div class="d-flex flex-row" style="margin-left: 24px;padding: 10px 10px 10px 0;">
                                                    <span class="text-muted">
                                                    @isset($merchant->partner_mailing->address)
                                                        {{ $merchant->partner_mailing->address }}	
                                                    @endisset
                                                    <br>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between" style="margin-left: 24px;">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_mailing->city)
                                                            {{ $merchant->partner_mailing->city }}
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class=" text-muted align-self-center">(City)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_mailing->state)
                                                            @foreach($states as $item)
                                                                @if($item->abbr == $merchant->partner_mailing->state)
                                                                    {{ $item->name }} ({{ $item->abbr }})
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(State)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_mailing->country)
                                                            @foreach($country as $item)
                                                                @if($item->name == $merchant->partner_mailing->country)
                                                                    {{ $item->name }} ({{ $item->iso_code_2 }})
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(Country)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_mailing->zip)
                                                            {{ $merchant->partner_mailing->zip }}
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(Zip)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Shipping Address -->
                                <div class="box-body hide" id="shippingAdd">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <strong> Billing Address</strong>

                                                <div class="d-flex flex-row" style="margin-left: 24px;padding: 10px 10px 10px 0;">
                                                    <span class="text-muted">
                                                        @isset($merchant->partner_billing->address)
                                                            {{ $merchant->partner_billing->address }}   
                                                        @endisset
                                                        <br>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between" style="margin-left: 24px;">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_billing->city)
                                                            {{ $merchant->partner_billing->city }}
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class=" text-muted align-self-center">(City)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_billing->state)
                                                            @foreach($states as $item)
                                                                @if($item->abbr == $merchant->partner_billing->state)
                                                                    {{ $item->name }} ({{ $item->abbr }})
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(State)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_billing->country)
                                                            @foreach($country as $item)
                                                                @if($item->name == $merchant->partner_billing->country)
                                                                    {{ $item->name }} ({{ $item->iso_code_2 }})
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(Country)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($merchant->partner_billing->zip)
                                                            {{ $merchant->partner_billing->zip }}
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(Zip)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Contact Person Information -->	
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h2 class="box-title">Contact Person Information</h2>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <strong> Contact Person</strong>
                                                <div class="d-flex justify-content-between" style="margin-left: 24px;padding: 10px 10px 10px 0;">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($partner_contact[0]->position)
                                                            {{ $partner_contact[0]->position }}	
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class=" text-muted align-self-center">(Title/Position)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @if($partner_contact[0]->first_name != "" || $partner_contact[0]->middle_name != "" || $partner_contact[0]->last_name != "")
                                                            {{ $partner_contact[0]->first_name }}  {{ $partner_contact[0]->middle_name }}  {{ $partner_contact[0]->last_name }}	
                                                        @endif
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(Full name)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($partner_contact[0]->mobile_number)
                                                            {{ $merchant->partner_company->country_code . $partner_contact[0]->mobile_number }}	
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(Mobile number)</small>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted">
                                                        @isset($partner_contact[0]->email)
                                                            {{ $partner_contact[0]->email }}	
                                                        @endisset
                                                        <br>
                                                        </span>
                                                        <small class="text-muted align-self-center">(Email address)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($isInternal)
                <div class="tab-pane active" id="business-info">
                    <form id="frmMerchantInfo" name="frmMerchantInfo" method="post" enctype="multipart/form-data"
                        action="/merchants/updateBranchInfo/{{$id}}">
                        {{ csrf_field() }}
                        <div class="row mb-plus-20">
                            <div class="row-header">
                                <h3 class="title">Information</h3>
                            </div>
                            <div class="col-md-3">
                                <div class="circle circle-update">
                                    <img class="profile-pic" id="partnerImg" src="{{ $merchant->connectedUser->image != '' ? $merchant->connectedUser->image : '' }}" width="100%">
                                </div>
                                <div class="p-image p-image-update">
                                    <i class="fa fa-camera upload-button"></i>
                                    <input class="file-upload" id="profileUpload" name="profileUpload" type="file" accept="image/*"/>
                                </div>
                            </div>
                            <div class="col-md-9 mt-5">
                                <div class="row">
                                    @if($merchant->merchant_status_id == App\Models\MerchantStatus::LIVE_ID)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status:</label>
                                            @if($merchant->status == 'T')
                                            <select class="form-control select2" id="txtPartnerStatus" name="txtPartnerStatus" disabled>
                                                <option value="T">Terminated</option>
                                            </select>
                                            @else
                                            <select class="form-control select2" id="txtPartnerStatus" name="txtPartnerStatus">
                                                <option value="A" @if($merchant->status == 'A') selected @endif>Active</option>
                                                <option value="I" @if($merchant->status == 'I') selected @endif>Inactive </option>
                                                <option value="V" @if($merchant->status == 'V') selected @endif>Cancelled</option>
                                                <option value="T" @if($merchant->status == 'T') selected @endif>Terminated</option>
                                            </select>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Owner:</label>
                                            <select class="form-control select2" id="txtUplineId" name="txtUplineId"  @if($merchant->parent_id != -1 || $merchant->parent_id != "") disabled @endif>
                                                @foreach($upline_partners as $up)
                                                <option data-image="{{ $up->image }}" value="{{ $up->parent_id }}" @if($merchant->parent_id == $up->parent_id)
                                                    selected="selected" @endif>&nbsp;{{ $up->partner_id_reference }} -
                                                    {{ $up->company_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Merchant Source:</label>
                                            <input type="text" class="form-control" name="" id="" value="{{$merchant->create_by}}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            <!-- New Form  -->
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>DBA / Business Name: <span class="required">*</span></label>
                                        <!-- Should be company_name since Business Name is required -->
                                        <input type="text" class="form-control" id="txtBusinessName" name="txtBusinessName"
                                            value="{{$merchant->partner_company->company_name}}" placeholder="Enter Business Name"> 
                                        <span id="txtBusinessName-error" style="color:red"><small></small></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Ownership:<span class="required">*</span></label>
                                        <select class="form-control" id="txtOwnership" name="txtOwnership">
                                            @foreach($ownership as $s)
                                                <option value="{{$s->code}}" @if($merchant->partner_company->ownership == $s->code) selected @endif>{{$s->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-3 pr-0">
                                <label for="business_industry">Business Industry<span class="required"></span></label>
                                <select name="business_industry" id="business_industry" class="form-control select2">
                                    @foreach ($businessTypeGroups as $groupName => $businessTypes)
                                    <optgroup label="{{ $groupName }}">
                                        @foreach ($businessTypes as $businessType)
                                        <option value="{{ $businessType->mcc }}" {{ $merchant->business_type_code == $businessType->mcc ? 'selected' : ''}}>
                                            {{ $businessType->description }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>

                                <span id="business_industry-error" 
                                        class="business_industry-error hidden" style="color:red"><small></small></span>
                                </div>

                                <div class="form-group col-md-1 pl-0 text-center">
                                <label for="mcc">MCC<span class="required"></span></label>
                                <input type="text" id="mcc" name="mcc" class="form-control" style="border-left: 0px; text-align: center">
                                <span id="mcc-error" class="mcc-error" style="color:red"><small></small></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Preferred Language: </label>
                                        <select class="js-example-basic-single form-control" name="languages[]" multiple>
                                            @foreach($language as $l)  
                                                <option value="{{$l->id}}" {{ $merchant->language->contains('language_id',$l->id) ? 'selected' : '' }}>{{$l->name}}</option>
                                            @endforeach
                                        </select>
    
                                    </div>
                                </div>
    
                                <div class="col-lg-4 col-md-4 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Website / Url: </label>
                                         <input type="text" id="url" name="url" class="form-control" value="{{ $merchant->merchant_url }}">
                                    </div>
                                </div>
    
                                <div class="col-lg-4 col-md-4 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Pricing Type: </label>
                                        <input type="text" class="form-control" id="txtPricingType" value="{{$merchant->pricing_type}}" name="txtPricingType"
                                            placeholder="Enter Pricing Type">
                                    </div>
                                </div>

                            <div class="col-lg-4 col-md-6 col-sm-12 sm-col">
                                <div class="form-group">
                                    <label>Legal Business Name: </label>
                                    <!-- Should be business_name since Business Name is required -->
                                    <input type="text" class="form-control" id="txtLegalBusinessName" name="txtLegalBusinessName"
                                        value="{{$merchant->partner_company->business_name}}" placeholder="Enter Legal Business Name">
                                </div>
                            </div>

                            </div>
                        </div>
                            <!-- <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                <div class="form-group">
                                    <label>Social Security Number: </label>
                                    <input type="text" class="form-control" id="txtSocialSecurityNumber" value="{{$merchant->social_security_id}}"
                                        name="txtSocialSecurityNumber" placeholder="Enter Social Security Number">
                                </div>
                            </div> -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="checkbox" class="creditcardclient" name="creditcardclient" @if($merchant->is_cc_client == 1) checked @endif>
                                    Set as Credit Card Client
                                </div>
                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-12 sm-col" style="display: none">
                                <div class="form-group">
                                    <label>Tax ID Number: </label>
                                    <input type="text" class="form-control" id="txtTaxIdNumber" value="{{$merchant->tax_id_number}}" name="txtTaxIdNumber"
                                        placeholder="Enter Tax ID Number">
                                </div>
                            </div>

                            <div class="col-md-12 sm-col">
                                <div class="row-header">
                                    <h3 class="title">Bank Information</h3>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Bank Name:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBankName" id="txtBankName" placeholder="Bank Name" value="{{ $merchant->bank_name }}">
                                </div>
                            </div>

                            <div class="col-lg- col-md-6 col-sm-12 ">
                                <div class="form-group">
                                    <label>Bank Routing:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBankRouting" id="txtBankRouting"
                                        placeholder="Bank Routing" @if($maskInfo) value="XXXXXX{{ substr($merchant->bank_routing_no,strlen($merchant->bank_routing_no) - 3,3) }}"  @else value="{{$merchant->bank_routing_no}}" @endif>
                                </div>
                            </div>

                            <div class="col-lg- col-md-6 col-sm-12 ">
                                <div class="form-group">
                                    <label>Confirm Bank Routing:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBankRoutingConfirmation" id="txtBankRoutingConfirmation" placeholder="Confirm Bank Routing" @if($maskInfo) value="XXXXXX{{ substr($merchant->bank_routing_no,strlen($merchant->bank_routing_no) - 3,3) }}"  @else value="{{$merchant->bank_routing_no}}" @endif>
                                    <span id="txtBankRoutingConfirmation-error" style="color: red;"><small></small></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 ">
                                <div class="form-group">
                                    <label>Bank DDA:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBankDDA" id="txtBankDDA"
                                        placeholder="Bank DDA" value="{{$merchant->bank_dda}}">
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 ">
                                <div class="form-group">
                                    <label>Confirm Bank DDA:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBankDDAConfirmation" id="txtBankDDAConfirmation" placeholder="Confirm Bank DDA" value="{{ $merchant->bank_dda }}">
                                    <span id="txtBankDDAConfirmation-error" style="color: red;"><small></small></span>
                                </div>
                            </div>


                            <div class="col-md-12 sm-col">
                                <div class="row-header">
                                    <h3 class="title">MID</h3>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Front End MID:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtFrontEndMID" id="txtFrontEndMID" placeholder="Front End MID" value="{{ $merchant->front_end_mid }}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Back End MID:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBackEndMID" id="txtBackEndMID" placeholder="Back End MID" value="{{ $merchant->back_end_mid }}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Reporting MID:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtReportingMID" id="txtReportingMID" placeholder="Reporting MID" value="{{ $merchant->reporting_mid }}">
                                </div>
                            </div>


                            <!-- End of New Form -->
                            <!-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Federal Tax ID:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtFederalTaxID" id="txtFederalTaxID" value="{{$merchant->federal_tax_id}}" placeholder="Enter Federal Tax ID">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Merchant MID:</label>
                                        <input type="text" class="form-control" name="txtMerchantMID" id="txtMerchantMID" value="{{$merchant->merchant_mid}}" placeholder="Enter Merchant MID">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Credit Card Reference ID:</label>
                                        <input type="text" class="form-control" name="txtCreditCardReferenceId" id="txtCreditCardReferenceId" value="{{$merchant->credit_card_reference_id}}" placeholder="Enter Credit Card Reference ID">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Current Processor:</label>
                                        <input type="text" class="form-control" name="txtProcessor" id="txtProcessor" value="{{$merchant->merchant_processor}}" placeholder="Enter Processor">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>DBA:<span class="required">*</span></label>
                                        <input type="text" class="form-control" name="txtCompanyName" id="txtCompanyName" value="{{$merchant->partner_company->company_name}}" placeholder="Enter DBA">
                                        <span id="txtCompanyName-error" style="color:red"><small></small></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Legal Name (Business Name):<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtDBA" id="txtDBA" value="{{$merchant->partner_company->dba}}" placeholder="Enter Legal Name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>List Type of Business/Products/Services Sold and How (be Specific):</label>
                                        <textarea class="form-control" name="txtServiceSold" id="txtServiceSold" >{{$merchant->services_sold}}</textarea>
                                    </div>
                                </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Deposit Bank Name:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBankName" id="txtBankName" placeholder="Bank Name" value="{{$merchant->bank_name}}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Deposit Bank Account No:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBankAccountNo" id="txtBankAccountNo" placeholder="Bank Account No." value="{{$merchant->bank_account_no}}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Deposit Routing No:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtRoutingNo" id="txtRoutingNo" placeholder="Routing No." value="{{$merchant->bank_routing_no}}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Deposit Account Type:<span class="required"></span></label>
                                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" id="txtBankAccountType" name="txtBankAccountType" tabindex="-1" aria-hidden="true">
                                        @foreach($bankAccountType as $account)
                                            <option value="{{$account->code}}" @if($merchant->bank_account_type_code == $account->code) selected @endif   >{{$account->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Withdrawal Bank Name:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtWBankName" id="txtWBankName" placeholder="Bank Name" value="{{$merchant->withdraw_bank_name}}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Withdrawal Bank Account No:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtWBankAccountNo" id="txtWBankAccountNo" placeholder="Bank Account No." value="{{$merchant->withdraw_bank_account_no}}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Withdrawal Routing No:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtWRoutingNo" id="txtWRoutingNo" placeholder="Routing No." value="{{$merchant->withdraw_bank_routing_no}}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Withdrawal Account Type:<span class="required"></span></label>
                                   <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" id="txtWBankAccountType" name="txtWBankAccountType" tabindex="-1" aria-hidden="true">
                                        @foreach($bankAccountType as $account)
                                            <option value="{{$account->code}}" @if($merchant->withdraw_bank_account_type_code == $account->code) selected @endif>{{$account->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Domain Name:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtMerchantURL" id="txtMerchantURL" value="{{$merchant->merchant_url}}" placeholder="Enter Domain Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Authorized Business Rep:</label>
                                        <input type="text" class="form-control" id="txtAuthorizedRep" name="txtAuthorizedRep" value="{{$merchant->authorized_rep}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>IATA/ARC Number:</label>
                                        <input type="text" class="form-control" name="txtIATA" id="txtIATA" value="{{$merchant->IATA_no}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tax Filing Name (as it appears on your income tax return) :<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtTaxName" id="txtTaxName" value="{{$merchant->tax_filing_name}}">
                                    </div>
                                </div> -->
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    @if ($merchant->merchant_status_id == App\Models\MerchantStatus::BOARDING_ID || $merchant->merchant_status_id == App\Models\MerchantStatus::FOR_APPROVAL_ID)
                                        @hasAccess('merchant', 'decline merchant')
                                            <button type="button" class="btn btn-warning" onclick="declineBranch({{$id}})">Decline Branch</button>
                                        @endhasAccess
                                    @endif

                                    @if($merchant->status == 'P' && $canBoard)
                                        <button type="button" class="btn btn-primary" onclick="boardMerchant({{$id}})">Board
                                            Branch</button>
                                    @elseif($merchant->status == 'C' && $canApprove)
                                        <button type="button" class="btn btn-primary" onclick="approveMerchant({{$id}})">Approve
                                            Branch</button>
                                    @endif
                                    @if($canEdit)
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="addresses">
                    <form id="frmMerchantAddress" name="frmMerchantAddress" method="post" enctype="multipart/form-data"
                        action="/merchants/updateBranchAddress/{{$id}}">
                        {{ csrf_field() }}
                        <input type="hidden" id="txtCopyDBA" name="txtCopyDBA">
                        <input type="hidden" id="txtCopyBill" name="txtCopyBill">
                        <input type="hidden" id="txtCopyShip" name="txtCopyShip">
                        <input type="hidden" id="txtContactMobileNum" name="txtContactMobileNum" value="{{ isset($partner_contact[0]->mobile_number) ? 1 : -1}}">
                        <!-- <input type="hidden" id="txtTogBtnUnpaidPro" name="txtTogBtnUnpaidPro" value="{{ $merchant->email_unpaid_invoice == 1 ? 'on' : 'off'  }}">
                        <input type="hidden" id="txtTogBtnPaidPro" name="txtTogBtnPaidPro" value="{{ $merchant->email_paid_invoice == 1 ? 'on' : 'off'  }}">
                        <input type="hidden" id="txtTogBtnSMTPPro" name="txtTogBtnSMTPPro" value="{{ $merchant->smtp_settings == 1 ? 'on' : 'off'  }}"> -->
                        <input type="hidden" id="txtTogBtnAutoPro" name="txtTogBtnAutoPro" value="{{ $merchant->auto_emailer == 1 ? 'on' : 'off'  }}">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Address</h3>
                            </div>
                            <div class="col-md-8">
                                <div class="nav-tabs-custom mb-none">
                                    <ul class="nav nav-tabs">
                                        <li id="" class="active"><a href="#DBA" data-toggle="tab" aria-expanded="false">Business Physical
                                                Address</a></li>
<!--                                         <li id="" class="active"><a href="#DBA" data-toggle="tab" aria-expanded="false">DBA Address</a></li>
                                            <li class="legal_tab"><a href="#Legal" data-toggle="tab" aria-expanded="false">Legal Address</a></li> -->
                                            <li id="bill_tab"><a href="#Billing" data-toggle="tab" aria-expanded="true">Billing Address</a></li>
                                            <!-- <li id="ship_tab"><a href="#Shipping" data-toggle="tab" aria-expanded="false">Shipping Address</a></li> -->
                                            <li id="mail_tab"><a href="#Mailing" data-toggle="tab" aria-expanded="false">Mailing Address</a></li>
                                    </ul>
                                </div>
                                <div class="tab-content" style="padding:10px;border-left: 1px solid #ddd;border-bottom: 1px solid #ddd;border-right: 1px solid #ddd;border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
                                    <!-- start tab content for DBA Address -->

                                    <!-- New Form -->
                                    <div class="tab-pane active" id="DBA">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address: <span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="txtAddress" name="txtAddress"
                                                        value="{{$merchant->partner_company->address1}}" place></small></span>
                                                    <span id="txtAddress-error" style="color:red"><small></small></span>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address2: <span class="required"></span></label>
                                                    <input type="text" class="form-control" id="txtAddress2" name="txtAddress2"
                                                        value="{{$merchant->partner_company->address2}}" placeholder="Enter Address 2"></small></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="country">Country:<span class="required">*</span></label>
                                                    <select class="form-control s2-country"
                                                        style="width: 100%;" id="txtCountry" name="txtCountry" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($country as $c)
                                                            @if($c->iso_code_2 == 'US')
                                                                <option value="{{$c->name}}" 
                                                                    data-abbr="{{ $c->iso_code_2 }}"data-code="{{$c->country_calling_code}}"
                                                                    @if($merchant->partner_company->country == $c->name)
                                                                    selected @endif>{{$c->name}}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="zip">Zip:<span class="required">*</span></label>
                                                <input type="text" class="form-control" id="txtZip" name="txtZip" value="{{$merchant->partner_company->zip}}"
                                                    placeholder="Enter zip">
                                                <span id="txtZip-error" style="color:red"><small></small></span>
                                                @include('incs.zipHelpNote')
                                            </div>
                                            <div class="col-md-6" id="state_us" style="display: block;">
                                                <div class="form-group">
                                                    <label for="state">State:<span class="required">*</span></label>
                                                    <select class="form-control s2-state"
                                                        style="width: 100%;" id="txtState" name="txtState" tabindex="-1"
                                                        aria-hidden="true" disabled>
                                                        @foreach($stateUS as $s)
                                                        <option value="{{$s->abbr}}" data-code="{{$s->id}}"
                                                            @if($merchant->partner_company->state == $s->abbr) selected @endif>
                                                            {{$s->name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6" id="state_ph" style="display:none;">
                                                <div class="form-group">
                                                    <label for="state">Province:<span class="required">*</span></label>
                                                    <select class="form-control"
                                                        style="width: 100%;" id="txtStatePH" name="txtStatePH" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($statePH as $s)
                                                        <option value="{{$s->abbr}}" @if($merchant->partner_company->state
                                                            == $s->abbr) selected @endif>{{$s->abbr}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6" id="state_cn" style="display:none;">
                                                <div class="form-group">
                                                    <label for="state">Province:<span class="required">*</span></label>
                                                    <select class="form-control"
                                                        style="width: 100%;" id="txtStateCN" name="txtStateCN" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($stateCN as $s)
                                                        <option value="{{$s->abbr}}" @if($merchant->partner_company->state
                                                            == $s->abbr) selected @endif>{{$s->abbr}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="city">City:<span class="required">*</span></label>
                                                    {{-- <input type="text" class="form-control" id="txtCity" name="txtCity"
                                                        value="{{$merchant->partner_company->city}}" placeholder="Enter city"> --}}
                                                    <select name="txtCity" id="txtCity" class="form-control select2" disabled>
                                                            <option value="{{ $merchant->partner_company->city }}" selected>{{ $merchant->partner_company->city }}</option>
                                                    </select>
                                                    <span id="txtCity-error" style="color:red"><small></small></span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="tab-pane" id="Billing">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address: </label>
                                                    <input type="text" class="form-control" id="txtBillingAddress" name="txtBillingAddress"
                                                        value="{{$merchant->partner_billing->address OR ''}}" ></small></span>
                                                    <span id="txtBillingAddress-error" style="color:red"><small></small></span>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address2: <span class="required"></span></label>
                                                    <input type="text" class="form-control" id="txtBillingAddress2" name="txtBillingAddress2"
                                                        value="{{$merchant->partner_billing->address2}}" placeholder="Enter Address 2"></small></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="country">Country:</label>
                                                    <select class="form-control s2-country"
                                                        style="width: 100%;" id="txtBillingCountry" name="txtBillingCountry" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($country as $c)
                                                            @if($c->iso_code_2 == 'US')
                                                                <option value="{{$c->name}}" data-abbr="{{ $c->iso_code_2 }}"
                                                                    data-code="{{$c->country_calling_code}}"
                                                                    @if(isset($merchant->partner_billing->country)) 
                                                                    @if($merchant->partner_billing->country == $c->name)
                                                                    selected @endif @endif>{{$c->name}}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="zip">Zip:</label>
                                                <input type="text" class="form-control" id="txtBillingZip" name="txtBillingZip" value="{{$merchant->partner_billing->zip OR ''}}"
                                                    placeholder="Enter zip">
                                                <span id="txtBillingZip-error" style="color:red"><small></small></span>
                                                @include('incs.zipHelpNote')
                                            </div>
                                            <div class="col-md-6" id="bill_state_us" style="display: block;">
                                                <div class="form-group">
                                                    <label for="state">State:</label>
                                                    <select class="form-control s2-state"
                                                        style="width: 100%;" id="txtBillingState" name="txtBillingState" tabindex="-1"
                                                        aria-hidden="true" disabled>
                                                        @foreach($stateUS as $s)
                                                        <option value="{{$s->abbr}}" data-code="{{$s->id}}"
                                                            @if(isset($merchant->partner_billing->state)) 
                                                            @if($merchant->partner_billing->state == $s->abbr) selected @endif 
                                                            @endif>
                                                            {{$s->name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6" id="bill_state_ph" style="display:none;">
                                                <div class="form-group">
                                                    <label for="state">Province:</label>
                                                    <select class="form-control"
                                                        style="width: 100%;" id="txtBillingStatePH" name="txtBillingStatePH" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($statePH as $s)
                                                        <option value="{{$s->abbr}}" @if(isset($merchant->partner_billing->state)) @if($merchant->partner_billing->state
                                                            == $s->abbr) selected @endif @endif>{{$s->abbr}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6" id="bill_state_cn" style="display:none;">
                                                <div class="form-group">
                                                    <label for="state">Province:</label>
                                                    <select class="form-control"
                                                        style="width: 100%;" id="txtBillingStateCN" name="txtBillingStateCN" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($stateCN as $s)
                                                        <option value="{{$s->abbr}}" @if(isset($merchant->partner_billing->state)) @if($merchant->partner_billing->state
                                                            == $s->abbr) selected @endif @endif>{{$s->abbr}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="city">City:</label>
                                                    {{-- <input type="text" class="form-control" id="txtShippingCity" name="txtShippingCity"
                                                        value="{{$merchant->partner_shipping->city OR ''}}" placeholder="Enter city"> --}}
                                                    <select name="txtBillingCity" id="txtBillingCity" class="form-control select2" disabled>
                                                            <option value="{{ $merchant->partner_billing->city }}" selected>{{ $merchant->partner_billing->city }}</option>
                                                    </select>
                                                    <span id="txtShippingCity-error" style="color:red"><small></small></span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="tab-pane" id="Mailing">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address: </label>
                                                    <input type="text" class="form-control" id="txtMailingAddress" name="txtMailingAddress"
                                                        value="{{ $merchant->partner_mailing->address or ''}}" place></small></span>
                                                    <span id="txtMailingAddress-error" style="color:red"><small></small></span>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address2: <span class="required"></span></label>
                                                    <input type="text" class="form-control" id="txtMailingAddress2" name="txtMailingAddress2"
                                                        value="{{$merchant->partner_mailing->address2}}" placeholder="Enter Address 2"></small></span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="country">Country:</label>
                                                    <select class="form-control s2-country"
                                                        style="width: 100%;" id="txtMailingCountry" name="txtMailingCountry" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($country as $c)
                                                            @if($c->iso_code_2 == 'US')
                                                                <option value="{{$c->name}}" data-abbr="{{ $c->iso_code_2 }}"
                                                                    data-code="{{$c->country_calling_code}}"
                                                                    @if(isset($merchant->partner_mailing->country)) 
                                                                    @if($merchant->partner_mailing->country == $c->name)
                                                                    selected @endif @endif>{{$c->name}}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="zip">Zip:</label>
                                                <input type="text" class="form-control" id="txtMailingZip" name="txtMailingZip" value="{{$merchant->partner_mailing->zip or ''}}"
                                                    placeholder="Enter zip">
                                                <span id="txtMailingZip-error" style="color:red"><small></small></span>
                                                @include('incs.zipHelpNote')
                                            </div>
                                            <div class="col-md-6" id="state_mail_us" style="display: block;">
                                                <div class="form-group">
                                                    <label for="state">State:</label>
                                                    <select class="form-control s2-state"
                                                        style="width: 100%;" id="txtMailingState" name="txtMailingState" tabindex="-1"
                                                        aria-hidden="true" disabled>
                                                        @foreach($stateUS as $s)
                                                        <option value="{{$s->abbr}}" data-code="{{$s->id}}"
                                                            @if(isset($merchant->partner_mailing->state)) 
                                                            @if($merchant->partner_mailing->state == $s->abbr) selected @endif 
                                                            @endif>
                                                            {{$s->name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6" id="state_mail_ph" style="display:none;">
                                                <div class="form-group">
                                                    <label for="state">Province:</label>
                                                    <select class="form-control"
                                                        style="width: 100%;" id="txtMailingStatePH" name="txtMailingStatePH" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($statePH as $s)
                                                        <option value="{{$s->abbr}}" @if(isset($merchant->partner_mailing->state)) @if($merchant->partner_mailing->state
                                                            == $s->abbr) selected @endif @endif>{{$s->abbr}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6" id="state_mail_cn" style="display:none;">
                                                <div class="form-group">
                                                    <label for="state">Province:</label>
                                                    <select class="form-control"
                                                        style="width: 100%;" id="txtMailingStateCN" name="txtMailingStateCN" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($stateCN as $s)
                                                        <option value="{{$s->abbr}}" @if(isset($merchant->partner_mailing->state)) @if($merchant->partner_mailing->state
                                                            == $s->abbr) selected @endif @endif>{{$s->abbr}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="city">City:</label>
                                                    {{-- <input type="text" class="form-control" id="txtMailingCity" name="txtMailingCity"
                                                        value="{{$merchant->partner_mailing->city or ''}}" placeholder="Enter city"> --}}
                                                    <select name="txtMailingCity" id="txtMailingCity" class="form-control select2" disabled>
                                                            <option value="{{ $merchant->partner_mailing->city }}" selected>{{ $merchant->partner_mailing->city }}</option>
                                                    </select>
                                                    <span id="txtMailingCity-error" style="color:red"><small></small></span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- End of New Form -->
                                </div><br>
                                <div class="tab-content" style="padding:10px;border: 1px solid #ddd;border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;" hidden>
                                    <div class="tab-pane-active">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Billing Cycle: <span class="required"></span></label>
                                                    <select class="form-control select2 select2-hidden-accessible"
                                                        style="width: 100%;" id="txtBillingCycle" name="txtBillingCycle"
                                                        tabindex="-1" aria-hidden="true">
                                                        <option value="Monthly" @if($merchant->billing_cycle ==
                                                            'Monthly') selected @endif>Monthly</option>
                                                        <option value="Annually" @if($merchant->billing_cycle ==
                                                            'Annually') selected @endif>Annually</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Month: <span class="required"></span></label>
                                                    <select class="form-control select2 select2-hidden-accessible"
                                                        style="width: 100%;" id="txtBillingMonth" name="txtBillingMonth"
                                                        tabindex="-1" aria-hidden="true">
                                                        <option value="January" @if($merchant->billing_month ==
                                                            'January') selected @endif>January</option>
                                                        <option value="February" @if($merchant->billing_month ==
                                                            'February') selected @endif>February</option>
                                                        <option value="March" @if($merchant->billing_month == 'March')
                                                            selected @endif>March</option>
                                                        <option value="April" @if($merchant->billing_month == 'April')
                                                            selected @endif>April</option>
                                                        <option value="May" @if($merchant->billing_month == 'May')
                                                            selected @endif>May</option>
                                                        <option value="June" @if($merchant->billing_month == 'June')
                                                            selected @endif>June</option>
                                                        <option value="July" @if($merchant->billing_month == 'July')
                                                            selected @endif>July</option>
                                                        <option value="August" @if($merchant->billing_month ==
                                                            'August') selected @endif>August</option>
                                                        <option value="September" @if($merchant->billing_month ==
                                                            'September') selected @endif>September</option>
                                                        <option value="October" @if($merchant->billing_month ==
                                                            'October') selected @endif>October</option>
                                                        <option value="November" @if($merchant->billing_month ==
                                                            'November') selected @endif>November</option>
                                                        <option value="December" @if($merchant->billing_month ==
                                                            'December') selected @endif>December</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>On what day? <span class="required"></span></label>
                                                    <select class="form-control select2 select2-hidden-accessible"
                                                        style="width: 100%;" id="txtbillingDay" name="txtBillingDay"
                                                        tabindex="-1" aria-hidden="true">
                                                        <option value="1" @if($merchant->billing_day == '1') selected
                                                            @endif>1st</option>
                                                        <option value="2" @if($merchant->billing_day == '2') selected
                                                            @endif>2nd</option>
                                                        <option value="3" @if($merchant->billing_day == '3') selected
                                                            @endif>3rd</option>
                                                        <option value="4" @if($merchant->billing_day == '4') selected
                                                            @endif>4th</option>
                                                        <option value="5" @if($merchant->billing_day == '5') selected
                                                            @endif>5th</option>
                                                        <option value="6" @if($merchant->billing_day == '6') selected
                                                            @endif>6th</option>
                                                        <option value="7" @if($merchant->billing_day == '7') selected
                                                            @endif>7th</option>
                                                        <option value="8" @if($merchant->billing_day == '8') selected
                                                            @endif>8th</option>
                                                        <option value="9" @if($merchant->billing_day == '9') selected
                                                            @endif>9th</option>
                                                        <option value="10" @if($merchant->billing_day == '10') selected
                                                            @endif>10th</option>
                                                        <option value="11" @if($merchant->billing_day == '11') selected
                                                            @endif>11th</option>
                                                        <option value="12" @if($merchant->billing_day == '12') selected
                                                            @endif>12th</option>
                                                        <option value="13" @if($merchant->billing_day == '13') selected
                                                            @endif>13th</option>
                                                        <option value="14" @if($merchant->billing_day == '14') selected
                                                            @endif>14th</option>
                                                        <option value="15" @if($merchant->billing_day == '15') selected
                                                            @endif>15th</option>
                                                        <option value="16" @if($merchant->billing_day == '16') selected
                                                            @endif>16th</option>
                                                        <option value="17" @if($merchant->billing_day == '17') selected
                                                            @endif>17th</option>
                                                        <option value="18" @if($merchant->billing_day == '18') selected
                                                            @endif>18th</option>
                                                        <option value="19" @if($merchant->billing_day == '19') selected
                                                            @endif>19th</option>
                                                        <option value="20" @if($merchant->billing_day == '20') selected
                                                            @endif>20th</option>
                                                        <option value="21" @if($merchant->billing_day == '21') selected
                                                            @endif>21st</option>
                                                        <option value="22" @if($merchant->billing_day == '22') selected
                                                            @endif>22nd</option>
                                                        <option value="23" @if($merchant->billing_day == '23') selected
                                                            @endif>23th</option>
                                                        <option value="24" @if($merchant->billing_day == '24') selected
                                                            @endif>24th</option>
                                                        <option value="25" @if($merchant->billing_day == '25') selected
                                                            @endif>25th</option>
                                                        <option value="26" @if($merchant->billing_day == '26') selected
                                                            @endif>26th</option>
                                                        <option value="27" @if($merchant->billing_day == '27') selected
                                                            @endif>27th</option>
                                                        <option value="28" @if($merchant->billing_day == '28') selected
                                                            @endif>28th</option>
                                                        <option value="29" @if($merchant->billing_day == '29') selected
                                                            @endif>29th</option>
                                                        <option value="30" @if($merchant->billing_day == '30') selected
                                                            @endif>30th</option>
                                                        <option value="31" @if($merchant->billing_day == '31') selected
                                                            @endif>31st</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-4 mt-plus-20">
                                    <div class="form-group">
                                        <label>Business Phone 1: <span class="required">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label for="BusinessPhone">1</label>
                                            </div>
                                            <input type="text" class="form-control number-only" id="txtPhone1" name="txtPhone1" value="{{$merchant->partner_company->phone1}}" placeholder="Enter phone 1">
                                        </div>
                                        <span id="txtPhone1-error" style="color:red"><small></small></span>
                                    </div>
                                    <div class="form-group">
                                        <label>Business Phone 2:</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label for="BusinessPhone">1</label>
                                            </div>
                                            <input type="text" class="form-control number-only" id="txtPhone2" name="txtPhone2" value="{{$merchant->partner_company->phone2}}" placeholder="Enter phone 2">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone2">Fax:</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label for="BusinessPhone">1</label>
                                            </div>
                                            <input type="text" class="form-control number-only" id="txtFax" name="txtFax" value="{{$merchant->partner_company->fax}}" placeholder="Enter Fax">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Business Email
                                            <small>(must be valid)</small> :
                                            <span class="required">@if(!isset($partner_contact[0]->mobile_number)) * @endif</span></label>
                                        <div class="input-group">
                                            @if($merchant->partner_company->email)
                                                <label  class="input-group-addon"><a href="javascript:void(0)" onclick="verifyEmail({{$id}})">Verify</a></label>
                                            @endif
                                            <input type="text" class="form-control" id="txtEmail" name="txtEmail" value="{{$merchant->partner_company->email}}" placeholder="Enter email" onblur="validateData('partner_companies','email',this,'-1','false','empty', 'Business email already been used by other partners');"> -->
                            <!-- validateData('users','email_address',this,'-1','false','empty', 'Business email already been used by other users'); -->
                            <!-- </div>
                                        <span id="txtEmail-error" style="color:red;"><small></small></span></br>
                                        <span class="error" style="color:orange;"><small>Note: If left blank, Merchant must have at least the Contact Person's Mobile Number.</small></span>
                                    </div>
                                </div> -->
                            <div class="col-lg-4 col-md-12 sm-col">
                                <div class="tab-content" style="padding:10px;border: 1px solid #ddd;border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
                                    <div class="form-group">
                                        <label>Phone Number: <span class="required"></span></label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label for="BusinessPhone">1</label>
                                            </div>
                                            <input type="text" class="form-control number-only" id="txtPhoneNumber"
                                                name="txtPhoneNumber" placeholder="Enter Phone Number" value="{{$merchant->partner_company->nd_phone1}}">
                                        </div>
                                        <span id="txtPhoneNumber-error" style="color:red"><small></small></span>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Notifier:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtEmailNotifier" id="txtEmailNotifier"
                                            placeholder="Enter Email Notifier" value="{{$merchant->email_notifier}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email
                                            <small>(must be valid)</small> :
                                            <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="txtEmail" name="txtEmail"
                                            placeholder="Enter Email" value="{{$merchant->partner_company->email}}"
                                            onblur="validateData('users','email_address',this,{{$merchant->id}},'true','reference_', 'Email address already been used by other users'); validateData('partner_companies','email',this,{{$merchant->id}},'false','partner_', 'Email address already been used by other partners');">
                                        <span id="txtEmail-error" style="color:red"><small></small></span>
                                    </div>
                                    <div class="form-group" style="display:none;">
                                        <label>Email Unpaid Invoice: </label>
                                        <label class="switch switch-unpaid">
                                            <input type="checkbox" id="togBtnUnpaid" @if($merchant->email_unpaid_invoice
                                            == 1) checked @endif>
                                            <div class="slider round">
                                                <!--ADDED HTML -->
                                                <span class="on">On</span><span class="off">Off</span>
                                                <!--END-->
                                            </div>
                                        </label>
                                    </div>
                                    <div class="form-group" style="display:none;">
                                        <label>Email Paid Invoice: </label>
                                        <label class="switch switch-paid">
                                            <input type="checkbox" id="togBtnPaid" @if($merchant->email_paid_invoice ==
                                            1) checked @endif>
                                            <div class="slider round">
                                                <!--ADDED HTML -->
                                                <span class="on">On</span><span class="off">Off</span>
                                                <!--END-->
                                            </div>
                                        </label>
                                    </div>
                                    <div class="form-group" style="display:none;">
                                        <label>SMTP Settings: </label>
                                        <label class="switch switch-smtp">
                                            <input type="checkbox" id="togBtnSMTP" @if($merchant->smtp_settings == 1)
                                            checked @endif>
                                            <div class="slider round">
                                                <!--ADDED HTML -->
                                                <span class="on">Custom</span><span class="off">Default</span>
                                                <!--END-->
                                            </div>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <h5>Merchant Settings</h5><hr>
                                        <label>Auto Emailer: </label>
                                        <label class="switch switch-auto">
                                            <input type="checkbox" id="togBtnAutoEmailer" @if($merchant->auto_emailer == 1)
                                            checked @endif>
                                            <div class="slider round">
                                                <!--ADDED HTML -->
                                                <span class="on">On</span><span class="off">Off</span>
                                                <!--END-->
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

<!--                             <div class="col-md-4 mt-plus-20">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select class="form-control">
                                        <option>Active</option>
                                    </select>
                                </div>
                            </div> -->
                            @if($canEdit)
                            <div class="col-md-12" style="padding-top:10px">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-primary">Save</a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="owners-info">
                    <div class="row">
                        <div class="row-header">
                            <h3 class="title">Owners Information</h3>
                            @if($canEdit)
                            <div class="mini-drp-input pull-right mt-minus-40">
                                <select class="form-control">
                                    <option>Create New</option>
                                </select>
                                <button type="button" onclick="createContact();" class="btn btn-primary">GO</button>
                            </div>
                            @endif
                        </div>
                        <table class="table datatables table-striped">
                            <thead>
                                <th>Title</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Mobile Number</th>
                                <th>Email Address</th>
                                @if (auth()->user()->is_original_partner != 1)
                                <th>SSN</th>
                                @endif
                                <th>Position</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach($partner_contact as $pc)
                                <tr>
                                    <td>{{$pc->position}}</td>
                                    <td>{{$pc->last_name}}</td>
                                    <td>{{$pc->first_name}}</td>
                                    <td>{{$pc->country_code}}{{$pc->mobile_number}}</td>
                                    <td>{{$pc->email}}</td>
                                     @if (auth()->user()->is_original_partner != 1)
                                    <td> @if($maskInfo) XXX-XX-{{ substr($pc->ssn,7,4) }} @else {{substr($pc->ssn,7,4)}} @endif @if($pc->ssn_verified == 0) <span class="badge badge-danger">unverified</span> @endif</td>
                                    @endif
                                    <td>{{$pc->position}}</td>
                                    <td>@if($canEdit)<button type="button" onclick="editBranchContact({{$pc->id}});" class="btn btn-default btn-sm">Edit</button>@endif</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="tab-pane" id="attachments">
                    <div class="row">
                        <div class="row-header">
                            <h3 class="title">Attachments</h3>
                        </div>
                    </div>
                    <div class="content">
                        <div class="box-group" id="accordion">

                            @foreach($documents as $doc)
                            <div class="panel box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="box-title"> {{$doc->name}} </h4>
                                    <div class="box-tools pull-right">
                                        <a href="#collapseDoc{{$doc->id}}" class="btn-circle btn-circle-collapse in"
                                            data-toggle="collapse" data-parent="#accordion">
                                            <i class="fa fa-minus"></i>
                                        </a>
                                    </div>
                                </div>
                                <div id="collapseDoc{{$doc->id}}" class="panel-collapse collapse">
                                    <div class="box-body">
                                        <table class="table datatables table-condense table-striped">
                                            <thead>
                                                <td>Document Name</td>
                                                <td>Image</td>
                                                <td>Created By</td>
                                                <td>Create Date</td>
                                            </thead>
                                            <tbody>

                                                @foreach($doc->partner_attachment($id) as $pa)
                                                <tr>
                                                    <td>{{$pa->name}}</td>
                                                    <td><a target="_blank" href="/storage/merchant_attachment/{{$pa->document_image}}"
                                                            class="summary-attachment"><i class="fa fa-file"></i></a></td>
                                                    <td>{{$pa->create_by}}</td>
                                                    <td>{{$pa->created_at}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($canEdit)
                                    <div class="box-footer">
                                        <a href="#" onclick="UploadAttachment(-1,{{$doc->id}},'{{$doc->name}}');"><i
                                                class="fa fa-plus-circle"></i> Add File</a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach


                            @foreach($partner_attachment as $doc)
                            <div class="panel box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="box-title"> {{$doc->name}} </h4>
                                    <div class="box-tools pull-right">
                                        <a href="#collapse{{$doc->id}}" class="btn-circle btn-circle-collapse in"
                                            data-toggle="collapse" data-parent="#accordion">
                                            <i class="fa fa-minus"></i>
                                        </a>
                                    </div>
                                </div>
                                <div id="collapse{{$doc->id}}" class="panel-collapse collapse">
                                    <div class="box-body">
                                        <table class="table datatables table-condense table-striped">
                                            <thead>
                                                <td>Document Name</td>
                                                <td>Image</td>
                                                <td>Created By</td>
                                                <td>Create Date</td>
                                            </thead>
                                            <tbody>

                                                @foreach($doc->where('partner_id',$id)->where('name',$doc->name)->get()
                                                as $pa)
                                                <tr>
                                                    <td>{{$pa->name}}</td>
                                                    <td><a target="_blank" href="/storage/merchant_attachment/{{$pa->document_image}}"
                                                            class="summary-attachment"><i class="fa fa-file"></i></a></td>
                                                    <td>{{$pa->create_by}}</td>
                                                    <td>{{$pa->created_at}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="box-footer">
                                        <a href="#" onclick="UploadAttachment(-1,-1,'{{$doc->name}}');"><i class="fa fa-plus-circle"></i>
                                            Add File</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                        </div>
                        @if($canEdit)
                        <a href="#" onclick="UploadAttachment(-1,-2,'');"><i class="fa fa-plus-circle"></i> Upload New
                            File</a>
                        @endif
                    </div>
                </div>
                <div class="tab-pane" id="payment-gateway">
                    <div class="row">
                        <div class="row-header">
                            <h3 class="title">Payment Gateway</h3>
                            @if($canEdit)
                            <div class="mini-drp-input pull-right mt-minus-40">
                                <select class="form-control">
                                    <option>Create New</option>
                                </select>
                                <button type="button" onclick="createPaymentGateway();" class="btn btn-primary">GO</button>
                            </div>
                            @endif
                        </div>
                        <table class="table datatables table-striped">
                            <thead>
                                <th>Name</th>
                                <th>Key</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach($payment_gateways as $pc)
                                <tr>
                                    <td>{{$pc->name}}</td>
                                    <td>{{$pc->key}}</td>
                                    <td><button type="button" onclick="editPaymentGateway({{$pc->id}});" class="btn btn-default btn-sm">Edit</button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                <div class="tab-pane" id="discussion">
                    <!-- Your Page Content Here -->
                    <div class="col-sm-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Notes</h3>
                            </div>
                            <div class="box-body">
                                <div class="modal-body">
                                    <form id="frmComment{{$id}}" name="frmComment{{$id}}" method="post" enctype="multipart/form-data"
                                        action="/merchants/branchDetails/profile/addComment">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="txtPartnerId" id="txtPartnerId" value="{{$id}}" />
                                        <input type="hidden" name="txtParentId" id="txtParentId" value="-1" />

                                        <div id="post-comment">
                                            <div class="form-group">
                                                <div class="custom-fl-right comment-view">
                                                    <a href="#" class="cv-showall" onclick="showAllReplies(); return false;"
                                                        title="Show All"><i class="fa fa-navicon"></i></a>
                                                    <a href="#" class="cv-showless" onclick="hideAllReplies(); return false;"
                                                        title="Show Less"><i class="fa fa-minus"></i></a>
                                                </div>
                                                @if($can_add==1)
                                                <textarea name="comment" name="comment" class="form-control custom-textarea"
                                                    placeholder="Type here..." rows="6"></textarea>
                                                <!-- <input type="file" name="file{$id}" id="file{$id}" class="inputfile" data-multiple-caption="files selected" multiple /> -->
                                                @endif
                                            </div>
                                            <div class="form-group ta-right">
                                                @if($can_add==1)
                                                <div style="display: none;">
                                                <label for="state">Status:</label>
                                                <select id="txtPartnerStatus" name="txtPartnerStatus">
                                                    @foreach ($partner_status as $item)
                                                    <option value="{{$item->name}}" @if($item->name ==
                                                        $merchant->partner_status) selected="selected"
                                                        @endif>{{$item->name}}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                    @foreach($comments as $comment)
                                    <div id="comment-list">
                                        <div class="comment discussion" id="comment{{$comment->comment_id}}">
                                            <div class="comment-block comment-main">
                                                <span class="comment-author">{{$comment->first_name}}
                                                    {{$comment->last_name}}</span> |
                                                <span class="comment-date">{{\Carbon\Carbon::parse($comment->created_at)->format('Y-m-d
                                                    g:i:s A')}}</span>
                                                <span class="comment-author" style="text-transform: uppercase;display: none;">|
                                                    {{$comment->lead_status}}</span>
                                                <div class="comment-desc">
                                                    {{$comment->comment}}
                                                </div>
                                            </div>
                                            @if(count($comment->sub_comments)>0)
                                            @foreach($comment->sub_comments as $sub)
                                            <div class="comment-block comment-reply" style="display:none;">
                                                <span class="comment-author">{{$sub->first_name}} {{$sub->last_name}}</span>
                                                |
                                                <span class="comment-date">{{\Carbon\Carbon::parse($sub->created_at)->format('Y-m-d
                                                    g:i:s A')}}</span>
                                                <span class="comment-author" style="text-transform: uppercase;display: none;">|
                                                    {{$sub->lead_status}}</span>
                                                <div class="comment-desc">
                                                    {{$sub->comment}}
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif
                                            <form name="frmSubComment{{$comment->comment_id}}" id="frmSubComment{{$comment->comment_id}}"
                                                method="post" action="/merchants/branchDetails/profile/addSubComment">
                                                {{ csrf_field() }}
                                                <div class="comment-post-reply" id="divCommentPostReply{{$comment->comment_id}}"
                                                    name="divCommentPostReply{{$comment->comment_id}}" style="display:none;">
                                                    @if($can_add==1)
                                                    <div class="form-group">
                                                        <input type="hidden" name="txtPartnerId1" id="txtPartnerId1"
                                                            value="{{$comment->partner_id}}" />
                                                        <input type="hidden" name="txtParentId" id="txtParentId" value="{{$comment->comment_id}}" />
                                                        <textarea name="sub_comment" id="sub_comment" class="form-control custom-textarea"
                                                            placeholder="Type here..." rows="2"></textarea>
                                                        <!-- <input type="file" name="file{{$comment->comment_id}}" id="file{{$comment->comment_id}}" class="inputfile" data-multiple-caption="files selected" multiple style="display:none;" /> -->
                                                        <div class="custom-fl-right">
                                                            <div style="display: none;">
                                                            <label for="state">Status:</label>
                                                            <select id="txtPartnerStatusSub" name="txtPartnerStatusSub">
                                                                @foreach($partner_status as $item)
                                                                <option value="{{$item->name}}" @if($item->name ==
                                                                    $merchant->partner_status) selected="selected"
                                                                    @endif>{{$item->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Save</button>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </form>
                                            <div class="comment-options">
                                                @if($can_add==1)
                                                <a href="#" id="addreply{{$comment->comment_id}}" name="addreply{{$comment->comment_id}}"
                                                    class="addreply" onClick="addReply('{{$comment->comment_id}}'); return false;"><i
                                                        class="fa fa-reply"></i> Add Reply</a>
                                                @endif
                                                <a href="#" id="cancelreply{{$comment->comment_id}}" name="cancelreply{{$comment->comment_id}}"
                                                    class="cancelreply" onClick="cancelReply('{{$comment->comment_id}}'); return false;"
                                                    style="display:none;"><i class="fa fa-times"></i> Cancel Reply</a>
                                                @if(count($comment->sub_comments) > 0)
                                                <a href="#" class="showall" name="showall{{$comment->comment_id}}" id="showall{{$comment->comment_id}}"
                                                    onclick="showAllSpecific('{{$comment->comment_id}}'); return false;">
                                                    ({{count($comment->sub_comments)}})Show All
                                                </a>
                                                @endif
                                                <a href="#" class="showless" name="showless{{$comment->comment_id}}" id="showless{{$comment->comment_id}}"
                                                    onclick="hideAllSpecific('{{$comment->comment_id}}'); return false;"
                                                    style="display:none;">Show Less</a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($isInternal)
                <div class="tab-pane" id="mid-list">
                    <div class="row">
                        <div class="row-header">
                            <h3 class="title">MID Lists</h3>
                            @if($canEdit)
                            <div class="mini-drp-input pull-right mt-minus-40">
                                <button type="button" onclick="createMID();" class="btn btn-primary">New MID</button>
                            </div>
                            @endif
                        </div>
                        <table class="table datatables table-striped">
                            <thead>
                                <th>System</th>
                                <th>MID</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach($merchant->partner_mid as $mid)
                                <tr>
                                    <td>{{$mid->system->name}}</td>
                                    <td>{{$mid->mid}}</td>
                                    <td>@if($canEdit)<button type="button" onclick="editMID({{$mid->id}});" class="btn btn-default btn-sm">Edit</button>@endif</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                @endif
                
            </div>
    </section>
    <!-- /.content -->
</div>

<div id="modalUploadAttachment" class="modal" role="dialog">
    <form role="form" name="frmRegisterAttachment" id="frmRegisterAttachment" method="post" enctype="multipart/form-data"
        action="{{ url("/merchants/uploadBranchAttachment") }}">
        {{ csrf_field() }}
        <input type="hidden" id="txtAttachmentId" name="txtAttachmentId">
        <input type="hidden" id="txtDocumentId" name="txtDocumentId">
        <input type="hidden" id="txtDocumentName" name="txtDocumentName">
        <input type="hidden" id="txtDocumentPartnerId" name="txtDocumentPartnerId" value="{{$id}}">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attachment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>

                </div>
                <div class="modal-body">

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="divUploadAttachment" style="display:none">
                                    <label>Document Name:</label>
                                    <input type="text" id="txtUploadAttachment" name="txtUploadAttachment" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Select file:</label>
                                    <input type="file" id="fileUploadAttachment" name="fileUploadAttachment" accept="application/pdf,image/x-png,image/jpeg"/>
                                </div>

                                <button class="btn btn-sm btn-danger clear-input" data-file_id="fileUploadAttachment">Clear Input</butto>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSaveAttachment" name="btnSaveAttachment" class="btn btn-primary">Upload</button>
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </form>
</div>


<div class="modal fade" id="editContact" role="dialog">
    <div class="modal-dialog" role="document" style="max-width:800px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Owner Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmContactInfo" name="frmContactInfo" method="post" enctype="multipart/form-data" action="/merchants/updateBranchContact/{{$id}}">
                    {{ csrf_field() }}
                    <input type="hidden" class="form-control" id="contID" name="contID">
                    <input type="hidden" class="form-control" id="isOrigCon" name="isOrigCon">
                    <input type="hidden" class="form-control" id="contCallCode" name="contCallCode" value="{{$merchant->partner_company->country_code}}">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>First Name: <span class="required">*</span></label>
                                    <input type="text" class="form-control alpha" id="txtFirstName" name="txtFirstName"
                                        placeholder="Enter First Name">
                                    <span id="txtFirstName-error" style="color:red"><small></small></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Middle Initial:</label>
                                    <input type="text" class="form-control alpha" id="txtMiddleInitial" name="txtMiddleInitial"
                                        placeholder="MI" maxlength="1">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Last Name: <span class="required">*</span></label>
                                    <input type="text" class="form-control alpha" id="txtLastName" name="txtLastName"
                                        placeholder="Enter Last Name">
                                    <span id="txtLastName-error" style="color:red"><small></small></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Date of Birth: <span class="required">*</span></label>
                                    <input type="text" class="form-control integer-only" id="txtDOB" name="txtDOB" placeholder="MM/DD/YYYY">
                                    <span id="txtDOB-error" style="color:red"><small></small></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Title:<span class="required"></span></label>
                                    <input type="text" class="form-control alpha" id="txtTitle" name="txtTitle">
                                </div>
                            </div>
                            @if (auth()->user()->is_original_partner != 1)
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>SSN: <span class="required"></span></label>
                                    <input type="text" class="form-control integer-only" id="txtSSN" name="txtSSN">
                                </div>
                                @if($canVerifySSN)
                                <div class="form-group">
                                    <input type="checkbox" class="form-control" id="verify-ssn" name="verifySSN" style="display: inline-block;margin-right: 10px"><label for="verify-ssn">Verify SSN</label>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Date Business Acquired: <span class="required"></span></label>
                                    <input type="text" class="form-control integer-only" id="txtDateAcquired" name="txtDateAcquired"
                                        placeholder="MM/DD/YYYY">
                                    <span id="txtDateAcquired-error" style="color:red"><small></small></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Percentage of Ownership (%):<span class="required"></span></label>
                                    <input type="text" class="form-control" id="txtPercentageOwnership" name="txtPercentageOwnership"
                                        maxlength="3" style="width:20%" onkeypress="validate_numeric_input(event);"
                                        onblur="convertToFloat(this);">
                                    <span id="txtPercentageOwnership-error" style="color:red"><small></small></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Driver's License No. / Identification Card No.: <span class="required"></span></label>
                                    <input type="text" class="form-control" id="txtIssuedID" name="txtIssuedID">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Expiration Date:</label>
                                    <input type="text" class="form-control integer-only" id="txtExpDate" name="txtExpDate"
                                        placeholder="MM/DD/YYYY">
                                    <span id="txtExpDate-error" style="color:red"><small></small></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Phone 1: <span class="required"></span></label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <label for="ContactPhone">{{$merchant->partner_company->country_code}}</label>
                                        </div>
                                        <input type="text" class="form-control number-only" id="txtContactPhone1" name="txtContactPhone1"">
                                        </div>
                                    </div>
                                </div>
                                <div class="
                                            col-sm-6">
                                        <div class="form-group">
                                            <label>Phone 2: </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <label for="ContactPhone">{{$merchant->partner_company->country_code}}</label>
                                                </div>
                                                <input type="text" class="form-control number-only" id="txtContactPhone2"
                                                    name="txtContactPhone2"">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="
                                                    row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Mobile (must be valid):<span class="required" id="mobileNumber"></span></label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <label for="ContactPhone">{{$merchant->partner_company->country_code}}</label>
                                                            </div>
                                                            <input type="text" class="form-control number-only" id="txtContactMobile"
                                                                name="txtContactMobile" onblur="validateData('users','mobile_number',this,{{$merchant->id}},'true','reference_', 'Mobile Number already been used by other users'); validateData('partner_contacts','mobile_number',this,{{$merchant->id}},'false','partner_', 'Mobile number already been used by other contacts');">
                                                        </div>
                                                        <span id="txtContactMobile-error" style="color:red"><small></small></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Fax:</label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <label for="ContactPhone">{{$merchant->partner_company->country_code}}</label>
                                                            </div>
                                                            <input type="text" class="form-control number-only" id="txtContactFax"
                                                                name="txtContactFax"">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="
                                                                row">
                                                            <div class="col-sm-4 sm-col">
                                                                <div class="form-group">
                                                                    <label>Email (must be valid):<span class="required"></span></label>
                                                                    <input type="text" class="form-control" id="txtContactEmail"
                                                                        name="txtContactEmail">
                                                                    <span id="txtContactEmail-error" style="color:red"><small></small></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-close"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary btn-save" id="btnSave">Save</button>
                                                </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editPaymentGateway" role="dialog">
        <div class="modal-dialog" role="document" style="max-width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Gateway</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmPaymentGateway" name="frmPaymentGateway" method="post" enctype="multipart/form-data"
                        action="/merchants/updateBranchPaymentgateway/{{$id}}">
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control" id="pgID" name="pgID">
                        <div class="row">
                            <div class="col-sm-5 sm-col">
                                <div class="form-group">
                                    <label>Name: <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="txtPGName" name="txtPGName" placeholder="Enter Name">
                                </div>
                            </div>
                            <div class="col-sm-5 sm-col">
                                <div class="form-group">
                                    <label>Key: <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="txtPGKey" name="txtPGKey" placeholder="Enter Key">
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-save">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editMID" role="dialog">
        <div class="modal-dialog" role="document" style="max-width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">MID</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmEditMID" name="frmEditMID" method="post" enctype="multipart/form-data"
                        action="/merchants/updatepartnermid/{{$id}}">
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control" id="midID" name="midID">
                        <div class="row">
                            <div class="col-sm-5 sm-col">
                                <div class="form-group">
                                    <label>System: <span class="required">*</span></label>
                                    <select name="txtSystem" id="txtSystem" class="form-control txtSystem" data-id="">
                                        @foreach ($systems as $s)
                                            <option value="{{ $s->id }}" data-format="{{ $s->mid_format }}">
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-5 sm-col">
                                <div class="form-group">
                                    <label>Name: <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="txtMIDVal" name="txtMIDVal" placeholder="Enter Key">
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-save">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>


    @endsection
    @section('script')
    <script src="{{ config(' app.cdn ') . '/js/clearInput.js' . '?v=' . config(' app.version ') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/merchants/details.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config(' app.cdn ') . '/js/merchants/newFieldValidation.js' . '?v=' . config(' app.version ') }}"></script>
    <script>
        $('.datatables').dataTable();
        arr = window.location.href.split('#');
        if (arr[1] != undefined) {
            $('#' + arr[1]).trigger('click');
        }

        var str = $('#taxIDNumber').text().trim();
        if (str != '') {
            var rep = str.replace(/[\d.\-](?=[\d.\-]{4})/g, "X");
            var index = 2;
            var newTaxStr = rep.substr(0, index) + '-' + rep.substr(index + 1);
            $('#taxIDNumber').text(newTaxStr);
        }

        function boardMerchant(id) {
            if (!confirm('This will board the current branch. Proceed?')) {
                return false;
            }

            $.getJSON('/merchants/confirm_branch/' + id , null, function (data) {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                }else{
                    alert(data.message);
                }
            });
        }

        function approveMerchant(id) {
            if (!confirm('This will approve the current branch. Proceed?')) {
                return false;
            }

            $.getJSON('/merchants/finalize_branch/' + id , null, function (data) {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                }else{
                    alert(data.message);
                }
            });
        }



        function formatSelect2(resource) {
            return $('<span style="color: black;">' + resource.text + '</span>')
        }

        function formatSelect2Result(resource) {
            return $('<span style="color: black;">' + resource.text + '</span>')
        }

        let selectElements = $('.js-example-basic-single');

        selectElements.select2({
            templateSelection: formatSelect2,
            templateResult: formatSelect2Result
        })

    </script>
    <script src=@cdn('/js/supplierLeads/mcc.js')></script>
    @if(!$isInternal)
    <script>
        $('.nav-tabs').find('li#summary').addClass('active');
        $('.nav-tabs a[href="#overview"]').tab('show');
    </script>
    @endif
    <script>
	$('#optAddress').on('change', function() {
		var tabID = $(this).find(":selected").val();
		$("#optAddress option").each(function()
		{
			if ($(this).val() == tabID) {
				$('#' + tabID).removeClass('hide');
			} else {
				$('#' + $(this).val()).addClass('hide');
			}
		});
	});
    </script>
    @endsection