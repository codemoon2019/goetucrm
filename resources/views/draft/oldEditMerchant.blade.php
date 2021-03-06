@extends('layouts.app')

@section('style')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
<style>
    table, th, td {
        border: 1px solid black;
        margin-left: auto;
        margin-right: auto;
    }
    thead {
        text-align: center;
    }
    th, td {
        padding: 0.5em;
    }
    .form-category {
        background-color: #778899;
        color: #ffffff;
    }
    .no-border {
        border: none;
    }
    .ticket-img-xs {
        box-shadow: 0 0 2.5px #000000;
        height: 20px;
        width: 20px;
        border: 2px solid #ffffff;
        border-radius: 50%;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Create Merchants
        </h1>
        <ol class="breadcrumb">
            <li><a href="/">Dashboard</a></li>
            <li><a href="/merchants">Merchants</a></li>
            <li class="active">Create Merchants</li>
        </ol>
        <div class="dotted-hr"></div>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <!--Progress Wizard-->
        <div class="row">
            <div class="col-md-12">
                <ul class="progressbar nav">
                    <li class="col-sm-3 bi-tab list-tab active">
                        <a href="#business-info" id="bi-tab" data-toggle="tab" data-percent="25" aria-expanded="true">
                            Business Information
                        </a>
                    </li>

                    <li class="col-sm-3 cp-tab list-tab">
                        <a href="#contact-person" id="cp-tab" data-toggle="tab" data-percent="50" aria-expanded="false">
                            Contact Persons
                        </a>
                    </li>

                    <li class="col-sm-3 at-tab list-tab">
                        <a href="#attachments" id="at-tab" data-toggle="tab" data-percent="75" aria-expanded="false">
                            Attachments
                        </a>
                    </li>

                    <li class="col-sm-3 pr-tab list-tab">
                        <a href="#preview" id="pr-tab" data-toggle="tab" data-percent="100"  aria-expanded="false">
                            Preview
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!--/Progress Wizard-->

        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs ui-sortable-handle mainnav hide">
                <li class="active"><a href="#business-info" data-toggle="tab" aria-expanded="true">Business Information</a></li>
                <li class=""><a href="#contact-person" data-toggle="tab" aria-expanded="false">Contact Person</a></li>
                <li class=""><a href="#attachments" data-toggle="tab" aria-expanded="false">Attachments</a></li>
                <li class=""><a href="#preview" data-toggle="tab" aria-expanded="false">Preview</a></li>
                <!-- <li class=""><a href="#payment-gateway" data-toggle="tab" aria-expanded="false">Payment Gateway</a></li> -->
            </ul>
            <form id="frmMerchant" name="frmMerchant" method="post" enctype="multipart/form-data" action="{{$formUrl}}">
                {{ csrf_field() }}
                <input type="hidden" id="txtCopyDBA" name="txtCopyDBA">
                <input type="hidden" id="txtCopyBill" name="txtCopyBill">
                <input type="hidden" id="txtCopyShip" name="txtCopyShip">
                <input type="hidden" id="txtOtherHidden" name="txtOtherHidden">
                <input type="hidden" id="txtOtherHidden1" name="txtOtherHidden1">
                <!-- <input type="hidden" id="txtTogBtnUnpaid" name="txtTogBtnUnpaid" value="off">
                <input type="hidden" id="txtTogBtnPaid" name="txtTogBtnPaid" value="off">
                <input type="hidden" id="txtTogBtnSMTP" name="txtTogBtnSMTP" value="off"> -->
                <input type="hidden" id="txtTogBtnAutoEmailer" name="txtTogBtnAutoEmailer" value="off">
                <input type="hidden" id="txtCountrySelected" name="txtCountrySelected">
                <input type="hidden" id="txtDraftPartnerId" name="txtDraftPartnerId" value="{{ $draft->id }}">
                <input type="hidden" id="txtDraftFile" name="txtDraftFile" value="{{ $hasFiles }}">
                
                <!-- Tabs within a box -->
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="business-info">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row-header">
                                    <h3 class="title">Company Information </h3>
                                </div>
                            </div>

                            <!-- New Merchant Creation Form -->
                            <div class="col-lg-3 col-md-6 col-sm-12 sm-col" hidden>
                                <div class="form-group">
                                    <label>Group Type:</label>
                                    <select class="form-control" style="width: 100%;" id="txtPartnerTypeId" name="txtPartnerTypeId"
                                        tabindex="-1" aria-hidden="true">
                                        <option value="3">MERCHANT</option>
                                    </select>
                                </div>
                            </div>
                            @if($is_internal)
                            <div class="col-lg-3 col-md-6 col-sm-12 sm-col assignToMe {{ $systemUser ? '' : 'hidden' }}">
                                <div class="form-group">
                                    <br>
                                    <input type="checkbox" name="assigntome" id="assigntome" class="assigntome" {{ $draft->parent_id == auth()->user()->reference_id ? 'checked' : ''}}/>
                                    <label for="assigntome">Set Parent as
                                        @if (auth()->user()->is_original_partner != 1)
                                        {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}
                                        <span style="color:rgb(255, 165, 0)">({{ $userDepartment }})<span>
                                                @else
                                                {{ session('company_name') }}
                                                @endif
                                    </label>
                                    <input type="hidden" name="selfAssign" id="selfAssign">
                                </div>
                            </div>
                            @else
                                <input type="checkbox" class="assigntome" id="assigntome" hidden>
                            @endif
                            <div class="col-lg-6 col-md-12 assigntodiv" {{ $systemUser ? '' : 'style="display:none"' }}>
                                <label>Parent:</label>
        
                                        <select class="form-control select2" id="txtUplineId" name="txtUplineId" style="width:100%">
                                            @if (count($upline) > 0)
                                                @foreach($upline as $up)
                                                    <option data-image="{{ $up->image }}"  value="{{$up->parent_id}}" @if($draft->parent_id == $up->parent_id) selected="selected" @endif>&nbsp;{{$up->partner_id_reference}} - {{$up->dba}}</option>
                                                @endforeach
                                            @endif
                                        </select>
       
                            </div>
                            <div class="col-lg-12">
                                <div class="custom-contact-wrap-sm row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <input type="checkbox" class="creditcardclient" name="creditcardclient" id="creditcardclient" @if($draft->is_cc_client == 1) checked @endif>
                                            Set as Credit Card Client
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="custom-contact-wrap-sm row">
<!--                                     <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label>MID<span class="required">*</span></label>
                                            <input id="txtMID" 
                                                class="form-control"
                                                type="text" 
                                                name="txtMID" 
                                                placeholder="Enter MID"
                                                value="{{ $draft->merchant_id }}">
                                            <span id="txtMID-error" style="color:red"><small></small></span>
                                        </div>
                                    </div> -->
                                        
                                    <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label>DBA / Business Name: <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="txtBusinessName" name="txtBusinessName"
                                                value="{{ $draft->company_name }}" placeholder="Enter Business Name">
                                            <span id="txtBusinessName-error" style="color:red"><small></small></span>
                                        </div>
                                    </div>
                                    <!-- <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label>Social Security Number: </label>
                                            <input type="text" class="form-control" id="txtSocialSecurityNumber" value=""
                                                name="txtSocialSecurityNumber" placeholder="Enter Social Security Number">
                                        </div>
                                    </div> -->
                                    <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label>Legal Business Name: </label>
                                            <input type="text" class="form-control" id="txtLegalBusinessName" name="txtLegalBusinessName"
                                                value="" placeholder="Enter Legal Business Name" value="{{ $draft->txtLegalBusinessName }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label>Tax ID Number: </label>
                                            <input type="text" class="form-control" id="txtTaxIdNumber" value="" name="txtTaxIdNumber"
                                                placeholder="Enter Tax ID Number" valeu="{{ $draft->tax_id_number }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label for="txtOwnership">Ownership:<span class="required"></span></label>
                                            <select name="txtOwnership" id="txtOwnership" class="form-control">
                                                @if(count($ownerships)>0)
                                                @foreach($ownerships as $ownership)
                                                <option value="{{$ownership->code}}" {{$draft->ownership == $ownership->code ? "selected" : "" }}>{{$ownership->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-5 pr-0">
                                        <label for="business_industry">Business Industry<span class="required"></span></label>
                                        <select name="business_industry" id="business_industry" class="form-control select2">
                                        @foreach ($businessTypeGroups as $groupName => $businessTypes)
                                            <optgroup label="{{ $groupName }}">
                                            @foreach ($businessTypes as $businessType)
                                                <option value="{{ $businessType->mcc }}" {{ $draft->business_type_code == $businessType->mcc ? 'selected' : ''}}>
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
                                    
                                    <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label>Preferred Language: </label>
                                            <select class="js-example-basic-single form-control languages" name="languages[]" multiple>
                                                @foreach($language as $l)  
                                                    <option value="{{$l->id}}" {{ $draft->draftPartnerLanguage->contains('draft_language_id',$l->id) ? 'selected' : '' }}>{{$l->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label>Website / Url: </label>
                                             <input type="text" id="url" name="url" class="form-control" value="{{ $draft->merchant_url }}">
                                        </div>
                                    </div>


                                    <div class="col-md-12 sm-col">
                                        <div class="row-header">
                                            <h3 class="title">MID </h3>
                                        </div>
                                    </div>
                                    @php  $ctr = "";   @endphp
                                    @if(count($mid) == 0)
                                        <div class="col-lg-4 col-md-6 col-sm-12 sm-col">
                                            <div class="form-group">
                                                <label>System: <span class="required"></span></label>
                                                <select name="txtSystem" id="txtSystem" class="form-control txtSystem" data-id="">
                                                    @foreach ($systems as $s)
                                                        <option value="{{ $s->id }}" data-format="{{ $s->mid_format }}">
                                                            {{ $s->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-12 sm-col">
                                            <div class="form-group">
                                                <label>MID: <span class="required"></span></label>
                                                <input type="text" class="form-control txtMID" id="txtMID" name="txtMID" data-id=""
                                                    placeholder="Enter MID">
                                                <span id="txtMID-error" style="color:red"><small></small></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-sm-12 sm-col">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                 <button class="form-control btn btn-primary" type="button" onclick="addMID();">Additional MID</button>
                                            </div>
                                        </div>

                                        @php  $ctr = 1;   @endphp
                                    @endif

                                    <input type="hidden" id="midCtr" name="midCtr" value="{{$mid->count()}}">
                                    <div id="addBtnMID" class="col-lg-12 col-md-6 col-sm-12 sm-col">

                                        @foreach($mid as $m)

                                        <div class="addmid row" id="addmid-{{$ctr}}">
                                            <div class="col-lg-4 col-md-6 col-sm-12 sm-col">
                                                <div class="form-group">
                                                <label>System: <span class="required"></span></label>
                                                <select name="txtSystem{{$ctr}}" id="txtSystem{{$ctr}}" class="txtSystem form-control" data-id="{{$ctr}}">
                                                    @foreach ($systems as $s)
                                                        <option value="{{ $s->id }}" data-format="{{ $s->mid_format }}" @if($m->system_id == $s->id) selected @endif>{{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12 sm-col">
                                                <div class="form-group">
                                                    <label>MID: <span class="required"></span></label>
                                                    <input type="text" class="form-control txtMID" id="txtMID{{$ctr}}" name="txtMID{{$ctr}}" placeholder="Enter MID" data-id="{{$ctr}}" value = "{{$m->mid}}">
                                                    <span id="txtMID{{$ctr}}-error" style="color:red"><small></small></span>
                                                </div>
                                            </div>
                                            @if($ctr == "")
                                            <div class="col-lg-2 col-md-6 col-sm-12 sm-col">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                     <button class="form-control btn btn-primary" type="button" onclick="addMID();">Additional MID</button>
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-lg-2 col-md-6 col-sm-12 sm-col">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button class="form-control btn btn-danger" type="button" onclick="closeMID({{$ctr}});">Remove</button>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        @php
                                            if($ctr == ""){
                                                $ctr = 0;
                                            }

                                            $ctr++;
                                        @endphp

                                        @endforeach

                                    </div>

                                    <div class="col-md-12 sm-col">
                                        <div class="row-header">
                                            <h3 class="title">Bank Information </h3>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Bank Name:<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtBankName" id="txtBankName" value="{{ $draft->bank_name }}" placeholder="Bank Name">
                                        </div>
                                    </div>

                                    <div class="col-lg- col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Bank Routing:<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtBankRouting" id="txtBankRouting"
                                                placeholder="Bank Routing" value="{{ $draft->bank_routing_no }}">
                                        </div>
                                    </div>

                                    <div class="col-lg- col-md-6 col-sm-12 ">
                                        <div class="form-group">
                                            <label>Confirm Bank Routing:<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtBankRoutingConfirmation" id="txtBankRoutingConfirmation" placeholder="Confirm Bank Routing">
                                            <span id="txtBankRoutingConfirmation-error" style="color: red;"><small></small></span>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12 ">
                                        <div class="form-group">
                                            <label>Bank DDA:<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtBankDDA" id="txtBankDDA"
                                                placeholder="Bank DDA" value="{{ $draft->bank_dda }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12 ">
                                        <div class="form-group">
                                            <label>Confirm Bank DDA:<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtBankDDAConfirmation" id="txtBankDDAConfirmation" placeholder="Confirm Bank DDA">
                                            <span id="txtBankDDAConfirmation-error" style="color: red;"><small></small></span>
                                        </div>
                                    </div>

                                    <div class="col-md-12 sm-col">
                                        <div class="row-header">
                                            <h3 class="title">Business Physical Address </h3>
                                        </div>
                                    </div>

                                    
                                    <div class="col-lg-8">
                                        <div class="nav-tabs-custom mb-none">
                                            <ul class="nav nav-tabs">
                                                <li id="physical_tab" class="active"><a href="#DBA" data-toggle="tab" aria-expanded="false">Business Physical Address</a></li>
                                                <li id="ship_tab"><a href="#Shipping" data-toggle="tab" aria-expanded="false">Shipping Address</a></li>
                                                <li id="mail_tab"><a href="#Mailing" data-toggle="tab" aria-expanded="false">Mailing Address</a></li>
                                            </ul>
                                        </div>
                                        
                                        <div class="tab-content" style="padding:10px;border-left: 1px solid #ddd;border-bottom: 1px solid #ddd;border-right: 1px solid #ddd;border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
                                            <!-- New Form -->
                                            <div class="tab-pane active" id="DBA">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-0">
                                                            <input type="checkbox" name="copy_to_shipping" id="copy_to_shipping" class="copy_to_shipping"/>
                                                            <label for="copy_to_shipping">Copy to shipping address</label>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <input type="checkbox" name="copy_to_mailing" id="copy_to_mailing" class="copy_to_mailing"/>
                                                            <label for="copy_to_mailing">Copy to mailing address</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Address: <span class="required">*</span></label>
                                                            <input type="text" class="form-control" id="txtAddress" name="txtAddress" placeholder="Enter address" value="{{ $draft->business_address1 }}"></small></span>
                                                            <span id="txtAddress-error" style="color:red"><small></small></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="country">Country:<span class="required">*</span></label>
                                                            <select class="form-control"
                                                                style="width: 100%;" id="txtCountry" name="txtCountry" tabindex="-1"
                                                                aria-hidden="true">
                                                                @foreach($countries as $c)
                                                                    <option value="{{ $c->name }}" data-code="{{ $c->country_calling_code }}" {{ $draft->business_country == $c->name ? "selected" : ""  }}>{{ $c->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="zip">Zip:<span class="required">*</span></label>
                                                        <input type="text" class="form-control" id="txtZip" name="txtZip"
                                                            placeholder="Enter zip" value="{{ $draft->business_zip }}" onblur="isValidZip(this, 'txtCity', 'txtState')">
                                                        <span id="txtZip-error" style="color:red"><small></small></span>
                                                    </div>


                                                    <div class="col-md-6" id="state_us" style="display: block;">
                                                        <div class="form-group">
                                                            <label for="state">State:<span class="required">*</span></label>
                                                            <select class="form-control"
                                                                style="width: 100%;" id="txtState" name="txtState" tabindex="-1"
                                                                aria-hidden="true">
                                                                @foreach($states as $s)
                                                                <option value="{{$s->abbr}}" {{$draft->business_state == $s->abbr ? "selected" : "" }}>{{$s->abbr}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="city">City:<span class="required">*</span></label>
                                                            <input type="text" class="form-control" id="txtCity" name="txtCity" placeholder="Enter city" value="{{ $draft->business_city }}">
                                                            <span id="txtCity-error" style="color:red"><small></small></span>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="tab-pane" id="Shipping">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Address: </label>
                                                            <input type="text" class="form-control" id="txtShippingAddress" name="txtShippingAddress" placeholder="Enter address" value="{{ $draft->shipping_address }}"></small></span>
                                                            <span id="txtShippingAddress-error" style="color:red"><small></small></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="country">Country:</label>
                                                            <select class="form-control"
                                                                style="width: 100%;" id="txtShippingCountry" name="txtShippingCountry" tabindex="-1"
                                                                aria-hidden="true">
                                                                @foreach($countries as $c)
                                                                    <option value="{{ $c->name }}" data-code="{{ $c->country_calling_code }}" {{ $draft->shipping_country == $c->name ? "selected" : ""  }}>{{ $c->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="zip">Zip:</label>
                                                        <input type="text" class="form-control" id="txtShippingZip" name="txtShippingZip"
                                                            placeholder="Enter zip" value="{{ $draft->shipping_zip }}"onblur="isValidZip(this, 'txtShippingCity', 'txtShippingState')">
                                                        <span id="txtShippingZip-error" style="color:red"><small></small></span>
                                                    </div>
                                                    <div class="col-md-6" id="state_ship_us" style="display: block;">
                                                        <div class="form-group">
                                                            <label for="state">State:</label>
                                                            <select class="form-control"
                                                                style="width: 100%;" id="txtShippingState" name="txtShippingState" tabindex="-1"
                                                                aria-hidden="true">
                                                                @foreach($states as $s)
                                                                <option value="{{$s->abbr}}" {{$draft->shipping_state == $s->abbr ? "selected" : ""  }}>{{$s->abbr}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="city">City:</label>
                                                            <input type="text" class="form-control" id="txtShippingCity" name="txtShippingCity" placeholder="Enter city" value="{{ $draft->shipping_city }}">
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
                                                            <input type="text" class="form-control" id="txtMailingAddress" name="txtMailingAddress" placeholder="Enter address" value="{{ $draft->mailing_address }}"></small></span>
                                                            <span id="txtMailingAddress-error" style="color:red"><small></small></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="country">Country:</label>
                                                            <select class="form-control"
                                                                style="width: 100%;" id="txtMailingCountry" name="txtMailingCountry" tabindex="-1"
                                                                aria-hidden="true">
                                                                @foreach($countries as $c)
                                                                    <option value="{{ $c->name }}" data-code="{{ $c->country_calling_code }}" {{ $draft->mailing_country == $c->name ? "selected" : ""  }}>{{ $c->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="zip">Zip:</label>
                                                        <input type="text" class="form-control" id="txtMailingZip" name="txtMailingZip" placeholder="Enter zip" value="{{ $draft->mailing_zip }}" onblur="isValidZip(this, 'txtMailingCity', 'txtMailingState')">
                                                        <span id="txtMailingZip-error" style="color:red"><small></small></span>
                                                    </div>
                                                    <div class="col-md-6" id="state_mail_us" style="display: block;">
                                                        <div class="form-group">
                                                            <label for="state">State:</label>
                                                            <select class="form-control"
                                                                style="width: 100%;" id="txtMailingState" name="txtMailingState" tabindex="-1"
                                                                aria-hidden="true">
                                                                @foreach($states as $s)
                                                                <option value="{{$s->abbr}}" {{$draft->mailing_state == $s->abbr ? "selected" : ""  }}>{{$s->abbr}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="city">City:</label>
                                                            <input type="text" class="form-control" id="txtMailingCity" name="txtMailingCity" placeholder="Enter city" value="{{ $draft->mailing_city }}">
                                                            <span id="txtCity-error" style="color:red"><small></small></span>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <!-- End of New Form -->
                                        </div>
                                        
                                        <br />
                                    </div>

                                    <div class="col-lg-4 col-md-12 sm-col">
                                        <div class="tab-content" style="padding:10px;border: 1px solid #ddd;border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
                                            <div class="form-group">
                                                <label>Phone Number: <span class="required"></span></label>
                                                <div class="input-group">
                                                    <label for="BusinessPhone" class="input-group-addon">1</label>
                                                    <input type="text" class="form-control number-only" id="txtPhoneNumber"
                                                        name="txtPhoneNumber" placeholder="Enter Phone Number" value="{{ $draft->nd_phone1 }}">
                                                </div>
                                                <span id="txtPhoneNumber-error" style="color:red"><small></small></span>
                                            </div>
                                            <div class="form-group">
                                                <label>Email Notifier:<span class="required"></span></label>
                                                <input type="text" class="form-control" name="txtEmailNotifier" id="txtEmailNotifier"
                                                    placeholder="Enter Email Notifier" value="{{ $draft->email_notifier }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email
                                                    <small>(must be valid)</small> :
                                                    </label>
                                                <input type="text" class="form-control" id="txtEmail" name="txtEmail" value="{{ $draft->partner_email }}" 
                                                    placeholder="Enter Email" onchange="validateData('users','email_address',this,'-1','true','reference_', 'Email address already been used by other users'); validateData('partner_companies','email',this,'-1','false','partner_', 'Email address already been used by other partners');">
                                                <span id="txtEmail-error" style="color:red;"><small></small></span>
                                            </div>
                                            <div class="form-group" style="display:none;">
                                                <label>Email Unpaid Invoice: </label>
                                                <label class="switch switch-unpaid">
                                                    <input type="checkbox" id="togBtnUnpaid">
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
                                                    <input type="checkbox" id="togBtnPaid">
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
                                                    <input type="checkbox" id="togBtnSMTP">
                                                    <div class="slider round">
                                                        <!--ADDED HTML -->
                                                        <span class="on">Custom</span><span class="off">Default</span>
                                                        <!--END-->
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label>Auto Emailer: </label>
                                                <label class="switch switch-auto">
                                                    <input type="checkbox" id="togBtnAutoEmailer" {{$draft->auto_emailer == 1 ? "checked" : ""  }}>>
                                                    <div class="slider round">
                                                        <!--ADDED HTML -->
                                                        <span class="on">On</span><span class="off">Off</span>
                                                        <!--END-->
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of New Merchant Creation Form -->
                            <div class="col-md-12 sm-col pull-right ta-right">
                                @if($canSaveAsDraft)
                                    @include('incs.saveAsDraft')
                                @endif
                                <a class="btn btn-primary pull-right btnNext" href="#contact-person" data-toggle="tab"
                                    aria-expanded="false">Next</a>
                            </div>

                        </div>
                    </div>

                    <div class="tab-pane" id="contact-person">
                        @foreach($draft->draftPartnerContacts as $key => $contact)
                            <div class="box-header with-border">
                                <h3 class="box-title title" style="color:#3c8dbc;" id="countContact"><b>Contact {{ $key + 1 }}</b></h3>
                            </div><br>
                            <!-- New Merchant Creation Form -->
                        
                            <div class="custom-contact-wrap-sm row">
                                <div class="col-lg-2 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Title: <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="txtTitle" name="txtTitle"
                                            placeholder="Enter Title" value="{{ $contact->position }}">
                                        <span id="txtTitle-error" style="color:red"><small></small></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Contact First Name: <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="txtFirstName" name="txtFirstName"
                                            placeholder="Enter First Name" value="{{ $contact->first_name }}">
                                        <span id="txtFirstName-error" style="color:red"><small></small></span>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Contact M.I.:</label>
                                        <input type="text" class="form-control" id="txtMiddleInitial" name="txtMiddleInitial"
                                            placeholder="MI" maxlength="1" value="{{ $contact->middle_name }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Contact Last Name: <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="txtLastName" name="txtLastName"
                                            placeholder="Enter Last Name" value="{{ $contact->last_name }}">
                                        <span id="txtLastName-error" style="color:red"><small></small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="custom-contact-wrap-sm row">
                                <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label for="phone2">Mobile Number
                                            <small>(must be valid)</small> :
                                            <span class="required" id="mobileNumber"></span></label>
                                        <div class="input-group ">
                                            <label for="ContactPhone" class="input-group-addon">1</label>
                                            <input type="text" class="form-control number-only" id="txtContactMobileNumber" value="{{ $contact->nd_mobile_number }}" 
                                                name="txtContactMobileNumber" placeholder="Enter Your Mobile Number" onchange="validateData('partner_contacts','mobile_number',this,'-1','false','empty', 'Mobile number already been used by other partners'); validateData('users','mobile_number',this,'-1','true','empty', 'Mobile number already been used by other users');">
                                        </div>
                                        <span id="txtContactMobileNumber-error" style="color:red"><small></small></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Social Security Number: </label>
                                        <input type="text" class="form-control" id="txtSSN" value="{{ $contact->ssn }}"
                                            name="txtSSN" placeholder="Enter Social Security Number">
                                    </div>
                                </div>
                            </div>
                            <!-- End of New Merchant Creation Form -->
                        @endforeach
                        <div id="addBtnContact">
                        </div>
                        <div class="form-group pull-right">
                            <button class="btn btn-primary" type="button" onclick="addContact();">Add Contact</button>
                        </div>
                        <div class="clearfix">
                        </div>
                        <div class="row">
                            <div class="col-md-12 sm-col pull-right ta-right">
                                @if($canSaveAsDraft)
                                    @include('incs.saveAsDraft')
                                @endif
                                <a class="btn btn-primary pull-right btnNext" href="#attachments" data-toggle="tab"
                                    aria-expanded="false">Next</a>
                                <a href="#bi-tab" class="btnPrevious pull-right  btn btn-primary" data-toggle="tab"
                                    aria-expanded="true">Prev</a> 

                            </div>
                        </div>

                    </div>

                    <div class="tab-pane" id="attachments">
                        <p><small>(Only .pdf, .png and .jpeg image are accepted. File size should not exceed 2mb)</small></p>
                        <div class="content">
                            <div class="box-group" id="accordion">
                                <div class="panel box box-primary">
                                    <div class="box-header with-border">
                                        <h4 class="box-title"> Draft Attachments </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse in show">
                                        <table class="table datatables table-condense table-striped no-border" id="tblDraft">
                                            <thead>
                                            <th class="no-border"> Document Name </th>
                                            <th class="no-border"> Image </th>
                                            <th class="no-border"> Saved at </th>
                                            <th class="no-border"> Action </th>
                                            </thead>
                                            <tbody>
                                            @if(count($draft->draftPartnerAttachments) > 0)
                                                @foreach($draft->draftPartnerAttachments as $detail)
                                                    <tr id="countFile" data-docu-id="{{ $detail->document_id }}">
                                                        <td class="no-border">
                                                            {{ $detail->document_name }}
                                                        </td>
                                                        <td class="no-border">
                                                            <a target="_blank" href="/storage/attachments/{{ $detail->document_image }}"><i class="fa fa-file"></i></a>
                                                        </td>
                                                        <td>
                                                            {{ $detail->created_at }}
                                                        </td>
                                                        <td class="no-border">
                                                            <!-- <button class="btn btn-danger btn-sm icon-delete" type="button" onclick="deleteDraftAttachement({{ $detail->document_id }}, {{ $detail->id }})" title="Delete"><i class="fa fa-trash"></i></button> -->
                                                            <button class="btn btn-danger btn-sm icon-delete" type="button" title="Delete"><i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="row">
                                @if(count($documents) > 0)
                                    @forelse($documents as $document)
                                        <div class="col-lg-3 col-md-6 col-sm-12 sm-col @if($draft->draftPartnerAttachments->contains('document_id',$document->id)) hide @endif" id="file{{ $document->id }}">
                                            <div class="form-group">
                                                <label>{{ $document->name }}:</label>
                                                <input type="file" id="fileUpload{{ $document->id }}" name="fileUpload{{ $document->id }}"
                                                    accept="image/x-png,image/jpeg,application/pdf">
                                            </div>
                                            <button class="btn btn-sm btn-danger clear-input" data-file_id="fileUpload{{ $document->id}}">Clear Input</butto>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                            
                        <hr>

                        <div id="addBtnFile">
                            <span><small><i>Please click 'Add Attachment' button to add another file.</i></small></span>
                        </div>
                        <div class="form-group pull-right">
                            <button class="btn btn-primary" type="button" onclick="addFile();">Add Attachment</button>
                        </div>
                        <div class="clearfix">
                        </div>

                        <div class="row">
                            <div class="col-md-12 sm-col pull-right ta-right">
                                @if($canSaveAsDraft)
                                    @include('incs.saveAsDraft')
                                @endif
                                <a class="btn btn-primary pull-right btnNext" href="#preview" data-toggle="tab" aria-expanded="false" id="btnCreateMerchant" name="btnCreateMerchant">Next</a>
                                <a href="#bi-tab" class="btnPrevious pull-right  btn btn-primary" data-toggle="tab"
                                    aria-expanded="true">Prev</a> 
                            </div> 
                        </div>
                    </div> 

                    <div class="tab-pane" id="preview">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row-header">
                                    <h3 class="title">Merchant Registration Preview</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table style="width:75%;">
                                <thead>
                                    <tr>
                                        <th colspan="12">BUSINESS INFORMATION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th colspan="12" class="form-category">Company Information</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Parent:</th>
                                        <td colspan="11"><i id="parent_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Business Name:</th>
                                        <td colspan="11"><i id="txtBusinessName_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Legal Business Name:</th>
                                        <td colspan="11"><i id="txtLegalBusinessName_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Preferred Language:</th>
                                        <td colspan="11"><i id="languages_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Tax ID Number:</th>
                                        <td colspan="7"><i id="txtTaxIdNumber_preview" class="view"></i></td>
                                        <td colspan="4"><input type="checkbox" id="creditcardclient_preview" disabled="disabled">&nbsp;Set as Credit Card Client</td>
                                    </tr>
                                    <tr>
                                        <th colspan="12" class="form-category">Bank Information</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Bank Name:</th>
                                        <td colspan="11"><i id="txtBankName_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Bank Routing:</th>
                                        <td colspan="11"><i id="txtBankRouting_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Bank DDA:</th>
                                        <td colspan="11"><i id="txtBankDDA_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="12" class="form-category">Business Address</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Address:</th>
                                        <td colspan="11"><i id="txtAddress_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Country:</th>
                                        <td colspan="2"><i id="txtCountry_preview" class="view"></i></td>
                                        <th colspan="1">State:</th>
                                        <td colspan="2"><i id="txtState_preview" class="view"></i></td>
                                        <th colspan="1">City:</th>
                                        <td colspan="2"><i id="txtCity_preview" class="view"></i></td>
                                        <th colspan="1">Zip:</th>
                                        <td colspan="2"><i id="txtZip_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="12" class="form-category">Business Contact</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Phone Number:</th>
                                        <td colspan="11"><i id="txtPhoneNumber_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Email Notifier:</th>
                                        <td colspan="11"><i id="txtEmailNotifier_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Email:</th>
                                        <td colspan="5"><i id="txtEmail_preview" class="view"></i></td>
                                        <th colspan="1">Auto Emailer:</th>
                                        <td colspan="5">
                                        <input type="checkbox" id="txtTogBtnAutoEmailer_preview_on" disabled="disabled">&nbsp;On
                                        <input type="checkbox" id="txtTogBtnAutoEmailer_preview_off" disabled="disabled">&nbsp;Off</td>
                                    </tr>
                                    <tr>
                                        <th colspan="12" class="form-category">Billing Information</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Billing Cycle:</th>
                                        <td colspan="3"><i id="txtBillingCycle_preview" class="view"></i></td>
                                        <th colspan="1">Month:</th>
                                        <td colspan="3"><i id="txtBillingMonth_preview" class="view"></i></td>
                                        <th colspan="1">Day:</th>
                                        <td colspan="3"><i id="txtBillingDay_preview" class="view"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width:75%;">
                                <thead>
                                    <tr>
                                        <th colspan="12">CONTACT PERSONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Middle Initial</th>
                                        <th>Last Name</th>
                                        <th>Mobile Number</th>
                                        <th>Social Security Number</th>
                                    </tr>
                                    <tr id="first-contact-merchant">
                                        <td><i class="view">1</i></td>
                                        <td><i id="txtFirstName_preview" class="view"></i></td>
                                        <td><i id="txtMiddleInitial_preview" class="view"></i></td>
                                        <td><i id="txtLastName_preview" class="view"></i></td>
                                        <td><i id="txtContactMobileNumber_preview" class="view"></i></td>
                                        <td><i id="txtSSN_preview" class="view"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12 sm-col pull-right ta-right">
                                @if($canSaveAsDraft)
                                    @include('incs.saveAsDraft')
                                @endif
                                <input class="btn btn-primary pull-right" type="submit" value="Save" />
                                <a href="#bi-tab" class="btnPrevious pull-right  btn btn-primary" data-toggle="tab"
                                    aria-expanded="true">Prev</a> 
                            </div>
                        </div>
                    </div>
            
            </form>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection

@section("script")
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ config(' app.cdn ') . '/js/merchants/create.js' . '?v=' . config(' app.version ') }}"></script>
<script type="text/javascript">
    $('#chkSameBankInfo').click(function () {
        if (this.checked) {
            $('.withdrawalInfo').show();
            $('#txtWBankName').val($('#txtBankName').val());
            $('#txtWBankAccountNo').val($('#txtBankAccountNo').val());
            $('#txtWRoutingNo').val($('#txtRoutingNo').val());
            $('#txtWBankAccountType').val($('#txtBankAccountType').val());
        } else {
            $('.withdrawalInfo').hide();
            $('#txtWBankName').val('');
            $('#txtWBankAccountNo').val('');
            $('#txtWRoutingNo').val('');
        }
    });


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
    
    $(document).on('change','.txtSystem', function(e){
        var id = $(this).attr('data-id');
        var format = this.options[this.selectedIndex].getAttribute('data-format');
        $('#txtMID'+id).val('');
        $('#txtMID'+id).mask(format, {clearIfNotMatch: true})
    });

    $(document).on('change','.txtMID', function(e){
        var id = $(this).attr('data-id');
        var element = $('#txtSystem'+id).find('option:selected');
        var format = element.attr('data-format');
        $('#txtMID'+id).mask(format, {clearIfNotMatch: true})
    });

    $('.txtMID').each(function(){

        var id = $(this).attr('data-id');
        var element = $('#txtSystem'+id).find('option:selected');
        var format = element.attr('data-format');
        $('#txtMID'+id).mask(format, {clearIfNotMatch: true})

     });

    function closeMID(id) {
        $('#addmid-' + id).remove();
    }

    function addMID() {
        var count = $("#addBtnMID.addmid").length;
        var ctr = parseInt($("#midCtr").val());

        var add_contact = '<div class="addmid row" id="addmid-' + ctr + '">\
            <div class="col-lg-4 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                <label>System: <span class="required"></span></label>\
                <select name="txtSystem' + ctr + '" id="txtSystem' + ctr + '" class="txtSystem form-control" data-id="'+ctr+'">\
                    @foreach ($systems as $s)
                        <option value="{{ $s->id }}" data-format="{{ $s->mid_format }}">{{ $s->name }}</option>\
                    @endforeach
                </select>\
                </div>\
            </div>\
            <div class="col-lg-4 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>MID: <span class="required"></span></label>\
                    <input type="text" class="form-control txtMID" id="txtMID' + ctr + '" name="txtMID' + ctr + '" placeholder="Enter MID" data-id="' + ctr + '">\
                    <span id="txtMID' + ctr + '-error" style="color:red"><small></small></span>\
                </div>\
            </div>\
            <div class="col-lg-2 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>&nbsp;</label>\
                    <button class="form-control btn btn-danger" type="button" onclick="closeMID(' + ctr + ');">Remove</button>\
                </div>\
            </div>\
            </div>';
        $("#addBtnMID").append(add_contact);
        $('#txtSystem' + ctr).trigger('change');
        ctr++;
        $("#midCtr").val(ctr);


    }
</script>
<script src="{{ config(' app.cdn ') . '/js/clearInput.js' . '?v=' . config(' app.version ') }}"></script>
@endsection