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
    .ticket-img-xs {
        box-shadow: 0 0 2.5px #000000;
        height: 20px;
        width: 20px;
        border: 2px solid #ffffff;
        border-radius: 50%;
    }
    .bg-redpink {
        background-color: #a74343!important;
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
    <section class="content">

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

            <ul class="timeline">
                
                    <!-- timeline time label -->
                    <li class="time-label">
                        <span class="bg-redpink text-white px-3 py-2">
                            <i class="fa fa-building"></i>&nbsp;
                            COMPANY INFORMATION
                        </span>
                    </li>
                    <!-- /.timeline-label -->
                
                    <!-- timeline item -->
                    <li id="section1">
                        <!-- timeline icon -->
                        <div class="timeline-item">
                            <div class="timeline-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="small-12 medium-2 large-2 columns">
                                            <div class="circle circle-create ml-5">
                                                <!-- User Profile Image -->
                                                <img class="profile-pic" id="partnerImg" src="{{ $draft->image }}" width="100%">
                                            
                                                <!-- Default Image -->
                                                <!-- <i class="fa fa-user fa-5x"></i> -->
                                            </div>
                                            <div class="p-image p-image-create">
                                                <i class="fa fa-camera upload-button"></i>
                                                <input class="file-upload" id="profileUpload" name="profileUpload" type="file" accept="image/*"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row" hidden>
                                            <div class="form-group col-md-6">
                                                <label>Group Type:</label>
                                                <select class="form-control" style="width: 100%;" id="txtPartnerTypeId" name="txtPartnerTypeId"
                                                    tabindex="-1" aria-hidden="true">
                                                    <option value="3">MERCHANT</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @if($is_internal)
                                            <div class="form-group col-md-4 assignToMe {{ $systemUser ? '' : 'hidden' }}">
                                                <input type="checkbox" name="assigntome" id="assigntome" class="assigntome" {{ $draft->parent_id == auth()->user()->reference_id ? 'checked' : ''}}/>
                                                <br>
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
                                            @else
                                                <input type="checkbox" class="assigntome" id="assigntome" hidden>
                                            @endif
                                            <div class="form-group col-md-4 assigntodiv" {{ $systemUser ? '' : 'style="display:none"' }}>
                                                <label>Parent:</label>
                                                <select class="form-control select2" id="txtUplineId" name="txtUplineId" style="width:100%">
                                                    @if (count($upline) > 0)
                                                        @foreach($upline as $up)
                                                            <option data-image="{{ $up->image }}"  value="{{$up->parent_id}}" @if($draft->parent_id == $up->parent_id) selected="selected" @endif>&nbsp;{{$up->partner_id_reference}} - {{$up->dba}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class=" form-group col-lg-3 pr-0">
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
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <input type="checkbox" class="creditcardclient" name="creditcardclient" id="creditcardclient" @if($draft->is_cc_client == 1) checked @endif>
                                                Set as Credit Card Client
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Legal Business Name: </label>
                                                <input type="text" class="form-control" id="txtLegalBusinessName" name="txtLegalBusinessName"
                                                    value="{{ $draft->txtLegalBusinessName }}" placeholder="Enter Legal Business Name">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>DBA / Business Name: <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="txtBusinessName" name="txtBusinessName"
                                                    value="{{ $draft->company_name }}" placeholder="Enter Business Name">
                                                <span id="txtBusinessName-error" style="color:red"><small></small></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="txtOwnership">Company Type / Ownership:<span class="required"></span></label>
                                                <select name="txtOwnership" id="txtOwnership" class="form-control">
                                                    @if(count($ownerships)>0)
                                                    @foreach($ownerships as $ownership)
                                                    <option value="{{$ownership->code}}" {{$draft->ownership == $ownership->code ? "selected" : "" }}>{{$ownership->name}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Tax ID Number: </label>
                                        <input type="text" class="form-control" id="txtTaxIdNumber" value="{{ $draft->tax_id_number }}" name="txtTaxIdNumber"
                                            placeholder="Enter Tax ID Number">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Credit Card Refence ID: </label>
                                        <input type="text" class="form-control" id="txtCreditCardReferenceId" value="" name="txtCreditCardReferenceId"
                                            placeholder="Enter Credit Card Refence ID">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="currentProfessor">Current Payment Processor:</label>
                                        <select name="currentProcessor" id="currentProccessor" class="form-control select2" style="width:100%">
                                            <option value="None">None</option>
                                            @foreach ($paymentProcessor as $item)
                                                <option value="{{$item->name}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="txtBusinessDate">Date when business was opened:</label>
                                        <input type="text" class="form-control" name="txtBusinessDate" id="txtBusinessDate" value="" placeholder="mm/yyyy" />
                                        <span id="txtBusinessDate-error" style="color:red;"><small></small></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Website / Url: </label>
                                        <input type="text" id="url" name="url" class="form-control" placeholder="Enter Website" value="{{ $draft->merchant_url }}">
                                        <span id="url-error" style="color:red;"><small></small></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Preferred Language: </label>
                                        <select class="js-example-basic-single form-control languages" name="languages[]" multiple>
                                            @foreach($language as $l)  
                                                <option value="{{$l->id}}" {{ $draft->draftPartnerLanguage->contains('draft_language_id',$l->id) ? 'selected' : '' }}>{{$l->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="txtPricingType">Pricing Type:</label>
                                        <input type="text" class="form-control" name="txtPricingType" id="txtPricingType"  value="{{ $draft->pricing_type }}"
                                            placeholder="Enter Pricing Type" />
                                        <span id="txtPricingType-error" style="color:red;"><small></small></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline time label -->
                    <li class="time-label">
                        <span class="bg-redpink text-white px-3 py-2">
                            <i class="fa fa-map-pin"></i>&nbsp;
                            ADDRESS INFORMATION
                        </span>
                    </li>
                    <!-- /.timeline-label -->
                
                    <!-- timeline item -->
                    <li id="section2">
                        <!-- timeline icon -->
                        <div class="timeline-item">
                            <div class="timeline-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="nav-tabs-custom mb-none">
                                            <ul class="nav nav-tabs">
                                                <li id="physical_tab" class="active"><a href="#DBA" data-toggle="tab" aria-expanded="false">Business Physical Address</a></li>
                                                <li id="bill_tab"><a href="#Billing" data-toggle="tab" aria-expanded="false">Billing Address</a></li>
                                                <li id="mail_tab"><a href="#Mailing" data-toggle="tab" aria-expanded="false">Mailing Address</a></li>
                                            </ul>
                                        </div>
                                        
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="DBA">
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <input type="checkbox" checked name="copy_to_billing" id="copy_to_billing" class="copy_to_billing"/>
                                                        <label for="copy_to_billing">Use Business Address as Billing Address</label>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <input type="checkbox" checked name="copy_to_mailing" id="copy_to_mailing" class="copy_to_mailing"/>
                                                        <label for="copy_to_mailing">Use Business Address as Mailing Address</label>
                                                    </div>

                                                    <div class="form-group col-md-12">
                                                        <label>Address: <span class="required">*</span></label>
                                                        <input type="text" class="form-control" id="txtAddress" name="txtAddress" 
                                                            value="{{ $draft->business_address1 }}" placeholder="Enter address"></small></span>
                                                        <span id="txtAddress-error" style="color:red"><small></small></span>
                                                    </div>

                                                    <div class="form-group col-md-12">
                                                        <label>Address 2: <span class="required"></span></label>
                                                        <input type="text" class="form-control" id="txtAddress2" name="txtAddress2" 
                                                            value="{{ $draft->business_address1 }}" placeholder="Enter address 2"></small></span>
                                                        <span id="txtAddress2-error" style="color:red"><small></small></span>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="country">Country:<span class="required">*</span></label>
                                                        <select class="form-control s2-country"
                                                            style="width: 100%;" id="txtCountry" name="txtCountry" tabindex="-1"
                                                            aria-hidden="true">
                                                            @foreach($countries as $c)
                                                                <option value="{{ $c->name }}" data-abbr="{{ $c->iso_code_2 }}"
                                                                    data-code="{{ $c->country_calling_code }}" 
                                                                    {{ $draft->business_country == $c->name ? "selected" : ""  }}>
                                                                    {{ $c->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="zip">Zip:<span class="required">*</span></label>
                                                        <input type="text" class="form-control" id="txtZip" name="txtZip" placeholder="Enter zip" value="{{ $draft->business_zip }}">
                                                        <span id="txtZip-error" style="color:red"><small></small></span>
                                                        @include('incs.zipHelpNote')
                                                    </div>

                                                    <div class="form-group col-md-6" id="state_us" style="display: block;">
                                                        <label for="state">State:<span class="required">*</span></label>
                                                        <select class="form-control s2-state"
                                                            style="width: 100%;" id="txtState" name="txtState" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($states as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{ $s->id }}" {{$draft->business_state == $s->abbr ? "selected" : "" }}>{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="city">City:<span class="required">*</span></label>
                                                        {{-- <input type="text" class="form-control" id="txtCity" name="txtCity" placeholder="Enter city"> --}}
                                                        <select name="txtCity" id="txtCity" class="form-control select2" disabled>
                                                            @foreach ($initialCities as $c)
                                                                <option value="{{ $c->city }}"
                                                                {{ $draft->business_city == $c->city ? "selected" : "" }}>{{ $c->city }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span id="txtCity-error" style="color:red;"><small></small></span>
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="tab-pane" id="Billing">
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label for="txtBillingAddress1">Address: </label>
                                                        <input type="text" class="form-control" id="txtBillingAddress1" name="txtBillingAddress1" placeholder="Enter Billing Address 1"
                                                            value="{{ $draft->billing_address }}"></small></span>
                                                        <span id="txtBillingAddress-error" style="color:red;"><small></small></span>
                                                    </div>
    
                                                    <div class="form-group col-md-12">
                                                        <label for="txtBillingAddress2">Address 2:</label>
                                                        <input type="text" class="form-control" name="txtBillingAddress2" id="txtBillingAddress2"
                                                            value="{{ $draft->billing_address2 }}" placeholder="Enter Billing Address 2" />
                                                    </div>
    
                                                    <div class="form-group col-md-6">
                                                        <label for="txtBillingCountry">Country:<span class="required"></span></label>
                                                        <select class="form-control s2-country"
                                                            style="width: 100%;" id="txtBillingCountry" name="txtBillingCountry" tabindex="-1"
                                                            aria-hidden="true">
                                                            @if(count($countries)>0)
                                                                @foreach($countries as $country)
                                                                    <option value="{{ $country->name }}" data-abbr="{{ $country->iso_code_2 }}"
                                                                        data-code="{{ $country->iso_code_2 }}" 
                                                                        {{$draft->billing_country == $country->name ? "selected" : "" }}>
                                                                        {{ $country->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="txtBillingZip">Zip:<span class="required"></span></label>
                                                        <input type="text" class="form-control" name="txtBillingZip" id="txtBillingZip"
                                                            value="{{ $draft->billing_zip }}" placeholder="Zip" onkeypress="return isNumberKey(event)"/>
                                                            <span id="txtBillingZip-error" style="color:red;"><small></small></span>
                                                        @include('incs.zipHelpNote')                                                            
                                                    </div>
                                                    <div class="form-group col-md-6" id="state_ship_us" style="display: block;">
                                                        <label for="txtBillingState">State:<span class="required"></span></label>
                                                        <select name="txtBillingState" id="txtBillingState" class="form-control s2-state" disabled>
                                                            @foreach($states as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{ $s->id }}" {{$draft->billing_state == $s->abbr ? "selected" : ""  }}>{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
    
                                                    <div class="form-group col-md-6">
                                                        <label for="txtBillingCity">City:<span class="required"></span></label>
                                                        <select name="txtBillingCity" id="txtBillingCity" class="form-control select2" disabled>
                                                            @foreach ($initialCities as $c)
                                                                <option value="{{ $c->city }}" {{ $draft->billing_city == $c->city ? "selected" : "" }}>{{ $c->city }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
    
    
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane" id="Mailing">
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label for="txtMailingAddress1">Address 1:<span class="required"></span></label>
                                                        <input type="text" class="form-control" name="txtMailingAddress1" id="txtMailingAddress1"
                                                            value="{{ $draft->mailing_address }}" placeholder="Enter Mailing Address 1" />
                                                    </div>
    
                                                    <div class="form-group col-md-12">
                                                        <label for="txtMailingAddress2">Address 2:</label>
                                                        <input type="text" class="form-control" name="txtMailingAddress2" id="txtMailingAddress2"
                                                            value="{{ $draft->mailing_address2 }}" placeholder="Enter Mailing Address 2" />
                                                    </div>
    
                                                    <div class="form-group col-md-6">
                                                        <label for="txtMailingCountry">Country:<span class="required"></span></label>
                                                        <select name="txtMailingCountry" id="txtMailingCountry" class="form-control s2-country">
                                                            @if(count($countries)>0)
                                                                @foreach($countries as $country)
                                                                    <option value="{{ $country->name }}" data-abbr="{{ $country->iso_code_2 }}"
                                                                        data-code="{{ $country->iso_code_2 }}" 
                                                                        {{$draft->mailing_country == $country->name ? "selected" : "" }}>
                                                                        {{ $country->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="txtMailingZip">Zip:<span class="required"></span></label>
                                                        <input type="text" class="form-control" name="txtMailingZip" id="txtMailingZip"
                                                            value="{{ $draft->mailing_zip }}" placeholder="Zip" onkeypress="return isNumberKey(event)"/>
                                                            <span id="txtMailingZip-error" style="color:red;"><small></small></span>
                                                        @include('incs.zipHelpNote')
                                                    </div>
                                                    <div class="form-group col-md-6" id="state_mail_us" style="display: block;">
                                                        <label for="txtMailingState">State:<span class="required"></span></label>
                                                        <select name="txtMailingState" id="txtMailingState" class="form-control s2-state" disabled>
                                                            @foreach($states as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{$s->id}}" {{$draft->mailing_state == $s->abbr ? "selected" : ""  }}>{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
    
                                                    <div class="form-group col-md-6">
                                                        <label for="txtMailingCity">City:<span class="required"></span></label>
                                                        <select name="txtMailingCity" id="txtMailingCity" class="form-control select2" disabled>
                                                            @foreach ($initialCities as $c)
                                                                <option value="{{ $c->city }}" {{ $draft->mailing_city == $c->city ? "selected" : "" }}>{{ $c->city }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
    
    
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 pt-5">
                                        <div class="tab-content" style="padding:10px;border:1px solid #ddd;">
                                            <div class="form-group">
                                                <label>Business Phone 1: <span class="required"></span></label>
                                                <div class="input-group">
                                                    <label for="BusinessPhone" class="input-group-addon">1</label>
                                                    <input type="text" class="form-control number-only" id="txtPhoneNumber"
                                                        name="txtPhoneNumber" placeholder="Enter Phone Number">
                                                    <button class="btn btn-primary" type="button" title="Add Alternate Business Phone" onclick="$('#phone2').toggleClass('hide')"><i class="fa fa-plus-square"></i></button>
                                                </div>
                                                <span id="txtPhoneNumber-error" style="color:red"><small></small></span>
                                            </div>
                                            <div class="form-group hide" id="phone2">
                                                <label>Business Phone 2: <span class="required"></span></label>
                                                <div class="input-group">
                                                    <label for="BusinessPhone" class="input-group-addon">1</label>
                                                    <input type="text" class="form-control number-only" id="txtPhoneNumber2"
                                                        name="txtPhoneNumber2" placeholder="Enter Phone Number 2">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Email Notifier:<span class="required"></span></label>
                                                <input type="text" class="form-control" name="txtEmailNotifier" id="txtEmailNotifier"
                                                    placeholder="Enter Email Notifier" value="">
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email
                                                    <small>(must be valid)</small> :
                                                    </label>
                                                <input type="text" class="form-control" id="txtEmail" name="txtEmail"
                                                    placeholder="Enter Email">
                                                <span id="txtEmail-error" class="help-block"><small></small></span>
                                            </div>
                                            <div class="form-group" style="display:none;">
                                                <label>Email Unpaid Invoice: </label>
                                                <label class="switch switch-unpaid">
                                                    <input type="checkbox" id="togBtnUnpaid">
                                                    <div class="slider round">
                                                        <span class="on">On</span><span class="off">Off</span>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="form-group" style="display:none;">
                                                <label>Email Paid Invoice: </label>
                                                <label class="switch switch-paid">
                                                    <input type="checkbox" id="togBtnPaid">
                                                    <div class="slider round">
                                                        <span class="on">On</span><span class="off">Off</span>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="form-group" style="display:none;">
                                                <label>SMTP Settings: </label>
                                                <label class="switch switch-smtp">
                                                    <input type="checkbox" id="togBtnSMTP">
                                                    <div class="slider round">
                                                        <span class="on">Custom</span><span class="off">Default</span>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label>Auto Emailer: </label>
                                                <label class="switch switch-auto">
                                                    <input type="checkbox" id="togBtnAutoEmailer">
                                                    <div class="slider round">
                                                        <span class="on">On</span><span class="off">Off</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline time label -->
                    <li class="time-label">
                        <span class="bg-redpink text-white px-3 py-2">
                            <i class="fa fa-industry"></i>&nbsp;
                            MID
                        </span>
                    </li>
                    <!-- /.timeline-label -->
                
                    <!-- timeline item -->
                    <li id="section3">
                        <!-- timeline icon -->
                        <div class="timeline-item">
                            <div class="timeline-body">

                                <div class="row">
                                    <div class="form-group col-sm-3">
                                        <label>Front End MID:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtFrontEndMID" id="txtFrontEndMID" placeholder="Front End MID " value="{{$draft->front_end_mid}}">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Back End MID:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBackEndMID" id="txtBackEndMID" placeholder="Back End MID" value="{{$draft->back_end_mid}}">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Reporting MID:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtReportingMID" id="txtReportingMID" placeholder="Reporting MID" value="{{$draft->reporting_mid}}">
                                    </div>
                                </div>


                                @php  $ctr = "";   @endphp
                                @if(count($mid) == 0)        
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>System: <span class="required"></span></label>
                                        <select name="txtSystem" id="txtSystem" class="form-control txtSystem" data-id="">
                                            @foreach ($systems as $s)
                                                <option value="{{ $s->id }}" data-format="{{ $s->mid_format }}">
                                                    {{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>MID: <span class="required"></span></label>
                                        <input type="text" class="form-control txtMID" id="txtMID" name="txtMID" data-id=""
                                            placeholder="Enter MID">
                                        <span id="txtMID-error" style="color:red"><small></small></span>
                                    </div>
                                    <div class="form-group col-md-2 offset-md-2">
                                        <label>&nbsp;</label>
                                        <button class="form-control btn btn-primary" type="button" onclick="addMID();"><i class="fa fa-plus-square"></i>&nbsp;Add MID</button>
                                        <input type="hidden" id="midCtr" name="midCtr" value="1">
                                    </div>
                                </div>
                                @php  $ctr = 1;   @endphp
                                @endif
                                <input type="hidden" id="midCtr" name="midCtr" value="{{$mid->count()}}">
                                <div class="row">
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
                                </div>
                            </div>
                        </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline time label -->
                    <li class="time-label">
                        <span class="bg-redpink text-white px-3 py-2">
                            <i class="fa fa-bank"></i>&nbsp;
                            BANK INFORMATION
                        </span>
                    </li>
                    <!-- /.timeline-label -->
                
                    <!-- timeline item -->
                    <li id="section4">
                        <!-- timeline icon -->
                        <div class="timeline-item">
                
                            <div class="timeline-body">
                                <div class="row">
                                    <div class="form-group col-sm-3">
                                        <label>Bank Name:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankName" id="txtBankName" placeholder="Bank Name" value="{{ $draft->bank_name }}">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Bank Address:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankAddress" id="txtBankAddress" placeholder="Bank Address">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Bank Routing:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankRouting" id="txtBankRouting" placeholder="Bank Routing" value="{{ $draft->bank_routing_no }}">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Bank DDA:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankDDA" id="txtBankDDA" placeholder="Bank DDA" value="{{ $draft->bank_dda }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline time label -->
                    <li class="time-label">
                        <span class="bg-redpink text-white px-3 py-2">
                            <i class="fa fa-user"></i>&nbsp;
                            CONTACT PERSON
                        </span>
                    </li>
                    <!-- /.timeline-label -->
                
                    <!-- timeline item -->
                    <li id="section5">
                        <!-- timeline icon -->
                        <div class="timeline-item" >
                
                            <div class="timeline-body">
                            @foreach($draft->draftPartnerContacts as $key => $contact)
                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <label>Title: <span class="required"></span></label>
                                        <input type="text" class="form-control" id="txtTitle" name="txtTitle"
                                            placeholder="Enter Title" value="{{ $contact->position }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>First Name: <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="txtFirstName" name="txtFirstName"
                                            placeholder="Enter First Name" value="{{ $contact->first_name }}">
                                        <span id="txtFirstName-error" style="color:red"><small></small></span>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>M.I.:</label>
                                        <input type="text" class="form-control" id="txtMiddleInitial" name="txtMiddleInitial"
                                            placeholder="MI" maxlength="1" value="{{ $contact->middle_name }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Last Name: <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="txtLastName" name="txtLastName"
                                            placeholder="Enter Last Name" value="{{ $contact->last_name }}">
                                        <span id="txtLastName-error" style="color:red"><small></small></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Social Security Number: <span class="required"></span></label>
                                        <input type="text" class="form-control" id="txtSSN" value="{{ $contact->ssn }}"
                                            name="txtSSN" placeholder="Enter Social Security Number">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="phone2">Mobile Number <small>(must be valid)</small> : <span class="required" id="mobileNumber"></span></label>
                                        <div class="input-group">
                                            <label for="ContactPhone" class="input-group-addon">1</label>
                                            <input type="text" class="form-control number-only" id="txtContactMobileNumber"
                                                value="{{ $contact->nd_mobile_number }}" name="txtContactMobileNumber" placeholder="Enter Your Mobile Number">
                                        </div>
                                        <span id="txtContactMobileNumber-error" class="help-block"><small></small></span>
                                    </div>
                                </div>
                            @endforeach
                                <div class="row">
                                    <div class="form-group col-md-2 offset-10">
                                        <label>&nbsp;</label>
                                        <button class="form-control btn btn-primary" type="button" onclick="addContact();"><i class="fa fa-plus-square"></i>&nbsp;Add Contact</button>
                                    </div>
                                </div>
                                <div id="addBtnContact">
                                </div>
                            </div>
                        </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline time label -->
                    <li class="time-label">
                        <span class="bg-redpink text-white px-3 py-2">
                            <i class="fa fa-file"></i>&nbsp;
                            ATTACHMENTS
                        </span>
                    </li>
                    <!-- /.timeline-label -->
                
                    <!-- timeline item -->
                    <li id="section6">
                        <!-- timeline icon -->
                        <div class="timeline-item">
                            <div class="timeline-body">
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
                                        <div class="col-md-2 align-self-end offset-md-1">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button class="form-control btn btn-primary" type="button" onclick="addFile();"><i class="fa fa-plus-square"></i>&nbsp;Add Attachment</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="addBtnFile">
                                    <span><small><i>Please click 'Add Attachment' button to add another file.</i></small></span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <!-- END timeline item -->
            </ul>

            <div class="form-group pull-right mb-5">
                @if($canSaveAsDraft)
                    @include('incs.saveAsDraft')
                @endif
                <button type="submit" class="pull-right btn btn-success ml-3">
                    Submit
                </button>
            </div>

        </form>

    </section>
</div>
@endsection

@section("script")
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ config(' app.cdn ') . '/js/merchants/create.js' . '?v=' . config(' app.version ') }}"></script>
<script src="{{ config(' app.cdn ') . '/js/merchants/newFieldValidation.js' . '?v=' . config(' app.version ') }}"></script>
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
    $('#txtSystem').trigger('change');

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
                <button class="close" type="button" onclick="closeMID(' + ctr + ');">&times;</button>\
                <div class="form-group">\
                    <label>MID: <span class="required"></span></label>\
                    <input type="text" class="form-control txtMID" id="txtMID' + ctr + '" name="txtMID' + ctr + '" placeholder="Enter MID" data-id="' + ctr + '">\
                    <span id="txtMID' + ctr + '-error" style="color:red"><small></small></span>\
                </div>\
            </div>\
            </div>';
        $("#addBtnMID").append(add_contact);
        $('#txtSystem' + ctr).trigger('change');
        ctr++;
        $("#midCtr").val(ctr);
    }

</script>
<script src=@cdn('/js/supplierLeads/mcc.js')></script>
<script src="{{ config(' app.cdn ') . '/js/clearInput.js' . '?v=' . config(' app.version ') }}"></script>
@endsection