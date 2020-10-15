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
            Create Branch
        </h1>
        <ol class="breadcrumb">
            <li><a href="/">Dashboard</a></li>
            <li><a href="/merchants/branch">Branch</a></li>
            <li class="active">Create Branch</li>
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
                                                <img class="profile-pic" id="partnerImg" src="/images/agent.png" width="100%">
                                            
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
                                            <div class="form-group col-lg-4 col-md-12">
                                                <label>Group Type:</label>
                                                <select class="form-control" style="width: 100%;" id="txtPartnerTypeId" name="txtPartnerTypeId"
                                                    tabindex="-1" aria-hidden="true">
                                                    <option value="9">BRANCH</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-12 assigntodiv" {{ $systemUser ? '' : 'style="display:none"' }}>
                                                <label>Owner:</label>
                                                <input type="checkbox" name="assigntome" id="assigntome" class="assigntome" style="display: none;"/>
                                                <select class="form-control select2" id="txtUplineId" name="txtUplineId" style="width:100%">
                                                    @if (count($upline) > 0)
                                                        @foreach($upline as $up)
                                                            <option data-image="{{ $up->image }}" value="{{ $up->parent_id }}">&nbsp;{{ $up->partner_id_reference }} - {{ $up->dba }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-3 pr-0">
                                                <label for="business_industry">Business Industry<span class="required"></span></label>
                                                <select name="business_industry" id="business_industry" class="form-control select2">
                                                @foreach ($businessTypeGroups as $groupName => $businessTypes)
                                                    <optgroup label="{{ $groupName }}">
                                                    @foreach ($businessTypes as $businessType)
                                                        <option value="{{ $businessType->mcc }}">
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

                                            <div class="form-group col-md-4">
                                                <label for="txtOwnership">Company Type / Ownership:<span class="required"></span></label>
                                                <select name="txtOwnership" id="txtOwnership" class="form-control">
                                                    @if(count($ownerships)>0)
                                                    @foreach($ownerships as $ownership)
                                                    <option value="{{$ownership->code}}">{{$ownership->name}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Legal Business Name: </label>
                                                <input type="text" class="form-control" id="txtLegalBusinessName" name="txtLegalBusinessName"
                                                    value="" placeholder="Enter Legal Business Name">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>DBA / Business Name: <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="txtBusinessName" name="txtBusinessName"
                                                    value="" placeholder="Enter Business Name">
                                                <span id="txtBusinessName-error" style="color:red"><small></small></span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-12" style="display: none;">
                                                <label>Tax ID Number: </label>
                                                <input type="text" class="form-control" id="txtTaxIdNumber" value="" name="txtTaxIdNumber"
                                                    placeholder="Enter Tax ID Number">
                                            </div>

                                            <div class="form-group col-lg-4 col-md-12">
                                                <label>Website / Url: </label>
                                                <input type="text" id="url" name="url" class="form-control" placeholder="Enter Website">
                                                <span id="url-error" style="color:red;"><small></small></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-3 col-md-12 mt-5">
                                        <input type="checkbox" class="creditcardclient" name="creditcardclient" id="creditcardclient">
                                        <label>Set as Credit Card Client</label>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-12">
                                        <label>Preferred Language: </label>
                                        <select class="js-example-basic-single form-control languages" name="languages[]" multiple>
                                            @foreach($language as $l)  
                                                <option value="{{$l->id}}" @if($l->id == 42) selected @endif>{{$l->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-12">
                                        <label for="txtPricingType">Pricing Type:</label>
                                        <input type="text" class="form-control" name="txtPricingType" id="txtPricingType" value=""
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
                                                        <input type="text" class="form-control" id="txtAddress" name="txtAddress" placeholder="Enter address"></small></span>
                                                        <span id="txtAddress-error" style="color:red"><small></small></span>
                                                    </div>

                                                    <div class="form-group col-md-12">
                                                        <label>Address 2: <span class="required"></span></label>
                                                        <input type="text" class="form-control" id="txtAddress2" name="txtAddress2" placeholder="Enter address 2"></small></span>
                                                        <span id="txtAddress2-error" style="color:red"><small></small></span>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="country">Country:<span class="required">*</span></label>
                                                        <select class="form-control s2-country"
                                                            style="width: 100%;" id="txtCountry" name="txtCountry" tabindex="-1"
                                                            aria-hidden="true">
                                                            @foreach($country as $c)
                                                                <option value="{{ $c->name }}" data-abbr="{{ $c->iso_code_2 }}"
                                                                    data-code="{{ $c->country_calling_code }}">
                                                                    {{ $c->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="zip">Zip:</label>
                                                        <input type="text" class="form-control" id="txtZip" name="txtZip" placeholder="Enter zip">
                                                        <span id="txtZip-error" style="color:red"><small></small></span>
                                                        @include('incs.zipHelpNote')
                                                    </div>

                                                    <div class="form-group col-md-3" id="state_us" style="display: block;">
                                                        <label for="state">State:</label>
                                                        <select class="form-control s2-state"
                                                            style="width: 100%;" id="txtState" name="txtState" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($stateUS as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{$s->id}}">{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3" id="state_ph" style="display:none;">
                                                        <label for="state">Province:</label>
                                                        <select class="form-control"
                                                            style="width: 100%;" id="txtStatePH" name="txtStatePH" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($statePH as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{$s->id}}">{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3" id="state_cn" style="display:none;">
                                                        <label for="state">Province:</label>
                                                        <select class="form-control"
                                                            style="width: 100%;" id="txtStateCN" name="txtStateCN" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($stateCN as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{$s->id}}">{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="city">City:</label>
                                                        {{-- <input type="text" class="form-control" id="txtCity" name="txtCity" placeholder="Enter city"> --}}
                                                        <select name="txtCity" id="txtCity" class="form-control select2" disabled>
                                                            @foreach ($initialCities as $c)
                                                                <option value="{{ $c->city }}">{{ $c->city }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span id="txtCity-error" style="color:red;"><small></small></span>
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="tab-pane" id="Billing">
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label>Address: </label>
                                                        <input type="text" class="form-control" id="txtBillingAddress" name="txtBillingAddress" placeholder="Enter address"></small></span>
                                                        <span id="txtBillingAddress-error" style="color:red"><small></small></span>
                                                    </div>

                                                    <div class="form-group col-md-12">
                                                        <label>Address 2: <span class="required"></span></label>
                                                        <input type="text" class="form-control" id="txtBillingAddress2" name="txtBillingAddress2" placeholder="Enter address 2"></small></span>
                                                        <span id="txtBillingAddress2-error" style="color:red"><small></small></span>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="country">Country:</label>
                                                        <select class="form-control s2-country"
                                                            style="width: 100%;" id="txtBillingCountry" name="txtBillingCountry" tabindex="-1"
                                                            aria-hidden="true">
                                                            @foreach($country as $c)
                                                                <option value="{{ $c->name }}" data-abbr="{{ $c->iso_code_2 }}"
                                                                    data-code="{{ $c->country_calling_code }}">
                                                                    {{ $c->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label for="zip">Zip:</label>
                                                        <input type="text" class="form-control" id="txtBillingZip" name="txtBillingZip"
                                                            placeholder="Enter zip">
                                                        <span id="txtBillingZip-error" style="color:red"><small></small></span>
                                                        @include('incs.zipHelpNote')
                                                    </div>
                                                    <div class="form-group col-md-3" id="bill_state_us" style="display: block;">
                                                        <label for="state">State:</label>
                                                        <select class="form-control s2-state"
                                                            style="width: 100%;" id="txtBillingState" name="txtBillingState" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($stateUS as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{$s->id}}">{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3" id="bill_state_ph" style="display:none;">
                                                        <label for="state">Province:</label>
                                                        <select class="form-control"
                                                            style="width: 100%;" id="txtBillingStatePH" name="txtBillingStatePH" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($statePH as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{$s->id}}">{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3" id="bill_state_cn" style="display:none;">
                                                        <label for="state">Province:</label>
                                                        <select class="form-control"
                                                            style="width: 100%;" id="txtBillingStateCN" name="txtBillingStateCN" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($stateCN as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{$s->id}}">{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="city">City:</label>
                                                        {{-- <input type="text" class="form-control" id="txtBillingCity" name="txtBillingCity" placeholder="Enter city"> --}}
                                                        <select name="txtBillingCity" id="txtBillingCity" class="form-control select2" disabled>
                                                            @foreach ($initialCities as $c)
                                                                <option value="{{ $c->city }}">{{ $c->city }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span id="txtBillingCity-error" style="color:red"><small></small></span>
                                                    </div>


                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane" id="Mailing">
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label>Address: </label>
                                                        <input type="text" class="form-control" id="txtMailingAddress" name="txtMailingAddress" placeholder="Enter address"></small></span>
                                                        <span id="txtMailingAddress-error" style="color:red"><small></small></span>
                                                    </div>

                                                    <div class="form-group col-md-12">
                                                        <label>Address 2: <span class="required"></span></label>
                                                        <input type="text" class="form-control" id="txtMailingAddress2" name="txtMailingAddress2" placeholder="Enter address 2"></small></span>
                                                        <span id="txtMailingAddress2-error" style="color:red"><small></small></span>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="country">Country:</label>
                                                        <select class="form-control s2-country"
                                                            style="width: 100%;" id="txtMailingCountry" name="txtMailingCountry" tabindex="-1"
                                                            aria-hidden="true">
                                                            @foreach($country as $c)
                                                                <option value="{{ $c->name }}" data-abbr="{{ $c->iso_code_2 }}"
                                                                    data-code="{{ $c->country_calling_code }}">
                                                                    {{ $c->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label for="zip">Zip:</label>
                                                        <input type="text" class="form-control" id="txtMailingZip" name="txtMailingZip" placeholder="Enter zip">
                                                        <span id="txtMailingZip-error" style="color:red"><small></small></span>
                                                        @include('incs.zipHelpNote')
                                                    </div>
                                                    <div class="form-group col-md-3" id="state_mail_us" style="display: block;">
                                                        <label for="state">State:</label>
                                                        <select class="form-control s2-state"
                                                            style="width: 100%;" id="txtMailingState" name="txtMailingState" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($stateUS as $s)
                                                            <option value="{{$s->abbr}}" data-code="{{$s->id}}">{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3" id="state_mail_ph" style="display:none;">
                                                        <label for="state">Province:</label>
                                                        <select class="form-control"
                                                            style="width: 100%;" id="txtMailingStatePH" name="txtMailingStatePH" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($statePH as $s)
                                                            <option value="{{$s->abbr}}">{{$s->abbr}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3" id="state_mail_cn" style="display:none;">
                                                        <label for="state">Province:</label>
                                                        <select class="form-control"
                                                            style="width: 100%;" id="txtMailingStateCN" name="txtMailingStateCN" tabindex="-1"
                                                            aria-hidden="true" disabled>
                                                            @foreach($stateCN as $s)
                                                            <option value="{{$s->abbr}}">{{$s->abbr}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="city">City:</label>
                                                        {{-- <input type="text" class="form-control" id="txtMailingCity" name="txtMailingCity" placeholder="Enter city"> --}}
                                                        <select name="txtMailingCity" id="txtMailingCity" class="form-control select2" disabled>
                                                            @foreach ($initialCities as $c)
                                                                <option value="{{ $c->city }}">{{ $c->city }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span id="txtMailingCity-error" style="color:red"><small></small></span>
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
                                <div class="row">
                                    <div class="form-group col-md-1">
                                        <label>Title: <span class="required"></span></label>
                                        <input type="text" class="form-control alpha" id="txtTitle" name="txtTitle"
                                            placeholder="Enter Title">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>First Name: <span class="required">*</span></label>
                                        <input type="text" class="form-control alpha" id="txtFirstName" name="txtFirstName"
                                            placeholder="Enter First Name">
                                        <span id="txtFirstName-error" style="color:red"><small></small></span>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label>M.I.:</label>
                                        <input type="text" class="form-control alpha" id="txtMiddleInitial" name="txtMiddleInitial"
                                            placeholder="MI">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Last Name: <span class="required">*</span></label>
                                        <input type="text" class="form-control alpha" id="txtLastName" name="txtLastName"
                                            placeholder="Enter Last Name">
                                        <span id="txtLastName-error" style="color:red"><small></small></span>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label>Social Security Number: <span class="required"></span></label>
                                        <input type="text" class="form-control integer-only" id="txtSSN" value=""
                                            name="txtSSN" placeholder="Enter SSN">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="phone2">Mobile Number <small>(must be valid)</small> : <span class="required" id="mobileNumber"></span></label>
                                        <div class="input-group">
                                            <label for="ContactPhone" class="input-group-addon">1</label>
                                            <input type="text" class="form-control number-only" id="txtContactMobileNumber"
                                                name="txtContactMobileNumber" placeholder="Enter Mobile #">
                                        </div>
                                        <span id="txtContactMobileNumber-error" class="help-block"><small></small></span>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>&nbsp;</label>
                                        <button class="form-control btn btn-primary" type="button" onclick="addContact();"><i class="fa fa-plus-square"></i>&nbsp;Add Contact</button>
                                    </div>

                                </div>
                                <div class="row">

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
                                        <input type="text" class="form-control" name="txtFrontEndMID" id="txtFrontEndMID" placeholder="Front End MID ">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Back End MID:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBackEndMID" id="txtBackEndMID" placeholder="Back End MID">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Reporting MID:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtReportingMID" id="txtReportingMID" placeholder="Reporting MID" value="">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>System: <span class="required"></span></label>
                                        <select name="txtSystem" id="txtSystem" class="form-control txtSystem" data-id="">
                                            @foreach ($systems as $s)
                                                <option value="{{ $s->id }}" data-format="{{ $s->mid_format }}">
                                                    {{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>MID: <span class="required"></span></label>
                                        <input type="text" class="form-control txtMID" id="txtMID" name="txtMID" data-id=""
                                            placeholder="Enter MID">
                                        <span id="txtMID-error" style="color:red"><small></small></span>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>&nbsp;</label>
                                        <button class="form-control btn btn-primary" type="button" onclick="addMID();"><i class="fa fa-plus-square"></i>&nbsp;Add MID</button>
                                        <input type="hidden" id="midCtr" name="midCtr" value="1">
                                    </div>
                                </div>
                                <div class="row">
                                    <div id="addBtnMID" class="col-lg-12 col-md-6 col-sm-12 sm-col">
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
                                        <input type="text" class="form-control" name="txtBankName" id="txtBankName" placeholder="Bank Name">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Bank Address:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankAddress" id="txtBankAddress" placeholder="Bank Address">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Bank Routing:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankRouting" id="txtBankRouting" placeholder="Bank Routing">
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label>Bank DDA:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankDDA" id="txtBankDDA" placeholder="Bank DDA">
                                    </div>
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
                                <div class="row">
                                    @if(count($documents)>0)
                                        @foreach($documents as $document)
                                            @if($document->id == 1 || $document->id == 2 || $document->id == 6)
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{$document->name}}:</label><br>
                                                    <input type="file" id="fileUpload{{$document->id}}" name="fileUpload{{$document->id}}" accept="application/pdf,image/x-png,image/jpeg">
                                                </div>
        
                                                <button class="btn btn-sm btn-danger clear-input" data-file_id="fileUpload{{ $document->id}}"><i class="fa fa-trash"></i>&nbsp;Clear Input</butto>
                                            </div>
                                            @endif
                                        @endforeach
                                    @endif
                                    <div class="col-md-2 align-self-end offset-md-1">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button class="form-control btn btn-primary" type="button" onclick="addFile();"><i class="fa fa-plus-square"></i>&nbsp;Add Attachment</button>
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
                    <button type="button" class="btn btn-success tabbtn btnSaveAsDraftBranch" id="btnSaveAsDraftBranch" name="btnSaveAsDraftBranch">
                        Save as Draft
                    </button>
                    &nbsp;
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
            <div class="col-lg-3 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                <label>System: <span class="required"></span></label>\
                <select name="txtSystem' + ctr + '" id="txtSystem' + ctr + '" class="txtSystem form-control" data-id="'+ctr+'">\
                    @foreach ($systems as $s)
                        <option value="{{ $s->id }}" data-format="{{ $s->mid_format }}">{{ $s->name }}</option>\
                    @endforeach
                </select>\
                </div>\
            </div>\
            <div class="col-lg-3 col-md-6 col-sm-12 sm-col">\
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