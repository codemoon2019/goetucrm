@extends('layouts.app')

@section('style')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
<style>
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
@php
$access = session('all_user_access');
$admin_access = isset($access['admin']) ? $access['admin'] : "";
$is_admin = true;
if (strpos($admin_access, 'super admin access') === false){
    $is_admin = false;
}
@endphp
@include("partners.details.profile.partnertabs")
<!-- Tabs within a box -->
@include("partners.details.profile.profiletabs")
<div class="tab-content no-padding">
    <form id="frmPartnerInfo" name="frmPartnerInfo" role="form" action="{{ url("/partners/details/profile/companyInfoUpdate/$id") }}"
        enctype="multipart/form-data" method="POST">
        <input name="_method" value="PUT" type="hidden">
        <input type="hidden" name="state" id="state" value="{{$partner_info->state}}">
        <input type="hidden" name="mailing_state" id="mailing_state" value="{{$partner_info->business_state}}">
        <input type="hidden" name="billing_state" id="billing_state" value="{{$partner_info->billing_state}}">
        <input type="hidden" name="contact_mobile" id="contact_mobile" value="{{$partner_info->mobile_number}}">
        {{ csrf_field() }}
        <div class="tab-pane active">
            <div class="row">
                <div class="row-header">
                    <h3 class="title">{{ucfirst(strtolower($partner_info->partner_type_description))}} Information</h3>
                </div>
                <div class="col-md-3">
                    <div class="circle circle-update">
                        <img class="profile-pic" id="partnerImg" src="{{ $partner_info->image }}" width="100%">
                    </div>
                    <div class="p-image p-image-update">
                        <i class="fa fa-camera upload-button"></i>
                        <input class="file-upload" id="profileUpload" name="profileUpload" type="file" accept="image/*"/>
                    </div>
                </div>
                <div class="col-md-9 {{$partner_info->partner_type_id == 1 ? "mt-5" : ''}}">
                    <div class="row">
                        @if($canEditStatus)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status:</label>
                                @if($partner_info->status == 'T')
                                <select class="form-control select2" id="txtPartnerStatus" name="txtPartnerStatus" disabled>
                                    <option value="T">Terminated</option>
                                </select>
                                @else
                                <select class="form-control select2" id="txtPartnerStatus" name="txtPartnerStatus">
                                    <option value="A" @if($partner_info->status == 'A') selected @endif>Active</option>
                                    <option value="I" @if($partner_info->status == 'I') selected @endif>Inactive </option>
                                    @if($is_admin)
                                    <option value="T">Terminated</option>
                                    @endif
                                </select>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if($partner_info->partner_type_id !=7 and $is_original_user==0)
                        <div class="col-md-6">
                            <label>Parent:</label>
                            <select class="form-control select2" id="txtUplineId" name="txtUplineId">
                                @if(count($uplines)>0)
                                    @foreach($uplines as $upline)
                                        <option data-image="{{ $upline->image }}" value="{{$upline->parent_id}}"
                                        {{$partner_info->parent_id==$upline->parent_id ? "selected=selected" : "" }}>&nbsp;{{$upline->partner_id_reference}} - {{$upline->company_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        @endif
                    </div>
                    @if($partner_info->partner_type_id !=1)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtOwnership">Ownership:<span class="required">*</span></label>
                                <select name="txtOwnership" id="txtOwnership" class="form-control">
                                    @if(count($ownerships)>0)
                                    @foreach($ownerships as $ownership)
                                    <option value="{{$ownership->code}}"
                                        {{$partner_info->ownership==$ownership->code ? "selected" : "" }}>{{$ownership->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="txtCompanyName">DBA:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="txtCompanyName" id="txtCompanyName"
                                    value="{{$partner_info->company_name}}" placeholder="Enter DBA" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="txtDBA">ISO/Affiliate Name (Legal Name/Business Name):</label>
                                <input type="text" class="form-control" name="txtDBA" id="txtDBA" value="{{$partner_info->dba}}"
                                    placeholder="Enter Legal Name" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="txtBusinessDate">Date when business was opened:</label>
                                <input type="text" class="form-control integer-only" name="txtBusinessDate" id="txtBusinessDate"
                                    value="{{$partner_info->business_date}}" placeholder="mm/yyyy" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" style="display:none;">
                            <div class="form-group">
                                <label for="txtCreditCardReference">Credit Card Reference ID:</label>
                                <input type="text" class="form-control" name="txtCreditCardReference" id="txtCreditCardReference"
                                    value="{{$partner_info->credit_card_reference_id}}" placeholder="Enter Reference ID" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="txtWebsite">Website:</label>
                                <input type="text" class="form-control" name="txtWebsite" id="txtWebsite" value="{{$partner_info->website}}"
                                    placeholder="Enter Website" />
                                <span id="txtWebsite-error" style="color:red;"><small></small></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="form-group col-md-4" style="display:none;">
                            <label for="txtTaxID">Tax ID Number:</label>
                            <input type="text" class="form-control" name="txtTaxIDNumber" id="txtTaxIDNumber" value="{{$partner_info->tax_id_number}}"
                                placeholder="Enter Tax ID" />
                            <span id="txtTaxID-error" style="color:red;"><small></small></span>
                        </div>

                        <div class="form-group col-md-4" style="display: none">
                            <label for="txtPricingType">Pricing Type:</label>
                            <input type="text" class="form-control" name="txtPricingType" id="txtPricingType" value="{{$partner_info->pricing_type}}"
                                placeholder="Enter Pricing Type" />
                            <span id="txtPricingType-error" style="color:red;"><small></small></span>
                        </div>
                    </div>

                </div>
                
                            <!-- <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="ssn">EIN:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtSSN" id="txtSSN" value="{{$partner_info->ssn}}"
                                        placeholder="Enter SSN/EIN"/ onblur="validateData('partner_companies','ssn',this,{{$id}},'false','empty', 'SSN already been used by other partners');">
                                </div>
                            </div> -->
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">{{ucfirst(strtolower($partner_info->partner_type_description))}} Contact Information</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Business Phone 1:<span class="required">*</span></label>
                                    <div class="input-group">
                                        <label for="businessPhone" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtBusinessPhone1" id="txtBusinessPhone1"
                                            value="{{$partner_info->nd_phone1}}" placeholder="Enter Business Phone 1" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="extension1">Extension:</label>
                                    <input type="text" class="form-control" name="txtExtension1" id="txtExtension1"
                                        value="{{$partner_info->business_extension}}" placeholder="Ext" />
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="fax">Fax:</label>
                                    <input type="text" class="form-control number-only" name="txtFax" id="txtFax" value="{{$partner_info->fax}}"
                                        placeholder="Enter Fax" />
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="extension1">Extension:</label>
                                    <input type="text" class="form-control" name="txtExtension3" id="txtExtension3"
                                        value="{{$partner_info->business_extension_3}}" placeholder="Ext" />
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Business Phone 2:</label>
                                    <div class="input-group">
                                        <label for="businessPhone" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtBusinessPhone2" id="txtBusinessPhone2"
                                            value="{{$partner_info->nd_phone2}}" placeholder="Enter Business Phone 2" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="extension2">Extension:</label>
                                    <input type="text" class="form-control" name="txtExtension2" id="txtExtension2"
                                        value="{{$partner_info->business_extension_2}}" placeholder="Ext" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtEmail" title="This will be used for sending user credentials.">Email(must
                                        be valid):<span class="required">@if(!isset($partner_info->mobile_number)) *
                                            @endif</span></label>
                                    <div class="input-group">
                                        @if(isset($partner_info->email))
                                        <label class="input-group-addon"><a href="javascript:void(0)" onclick="verifyEmail({{$id}})">Verify</a></label>
                                        @endif
                                        <!-- <input type="text" class="form-control" name="txtEmail" id="txtEmail" value="{{$partner_info->email}}" placeholder="Enter Email" onblur="validateData('users','email_address',this,{{$id}},'true','reference_', 'Email address already been used by other users'); validateData('partner_companies','email',this,{{$id}},'false','partner_', 'Email address already been used by other partners');" onchange="validateEmail(this.id);" /> -->
                                        <input type="text" class="form-control" name="txtEmail" id="txtEmail" value="{{$partner_info->email}}"
                                            placeholder="Enter Email" onblur="validateData('users','email_address',this,{{$id}},'true','reference_', 'Email address already been used by other users'); validateData('partner_companies','email',this,{{$id}},'false','partner_', 'Email address already been used by other partners');" />
                                    </div>
                                    <span id="txtEmail-error" style="color:red;"><small></small></span>
                                    <span class="error" style="color:orange;"><small>Note: If left blank, Partner must
                                            have at least the Contact Person's Mobile Number.</small></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Business Address</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="businessAddress1">Business Address 1:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtBusinessAddress1" id="txtBusinessAddress1"
                                        value="{{$partner_info->address1}}" placeholder="Enter Address" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="businessAddress2">Business Address 2:</label>
                                    <input type="text" class="form-control" name="txtBusinessAddress2" id="txtBusinessAddress2"
                                        value="{{$partner_info->address2}}" placeholder="Enter Address" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txtCountry">Country:<span class="required">*</span></label>
                                    <select name="txtCountry" id="txtCountry" class="form-control s2-country">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{ $country->name }}" data-code="{{ $country->iso_code_2 }}" {{ $partner_info->country_name == $country->name ? "selected" : "" }}>{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="zip">Zip:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtBusinessZip" id="txtBusinessZip"
                                        value="{{$partner_info->zip}}" placeholder="Ext"/>
                                    @include('incs.zipHelpNote')
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txtState">State:<span class="required">*</span></label>
                                    <select name="txtState" id="txtState" class="form-control s2-state" disabled>
                                        <input type="hidden" name="txtStateHidden" id="txtStateHidden" value="{{ $partner_info->state }}">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="city">City:<span class="required">*</span></label>
                                    {{-- <input type="text" class="form-control" name="txtCity" id="txtCity" value="{{$partner_info->city}}"
                                        placeholder="Enter City" /> --}}
                                    <select name="txtCity" id="txtCity" class="form-control select2" disabled>
                                        <option value="{{ $partner_info->city }}" selected>{{ $partner_info->city }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title pull-left">Billing Address</h3>
                                <div class="pull-right">
                                    <input type="checkbox" name="chkSameAsBusinessBilling" id="chkSameAsBusinessBilling">
                                    Same as Business Address
                                </div>
                            </div>
                        </div>
                        <div id="divBillingAddress" class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billingAddress1">Billing Address 1:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBillingAddress1" id="txtBillingAddress1"
                                        value="{{$partner_info->billing_address}}" placeholder="Enter Address" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="billingAddress2">Billing Address 2:</label>
                                    <input type="text" class="form-control" name="txtBillingAddress2" id="txtBillingAddress2"
                                        value="{{$partner_info->billing_address2}}" placeholder="Enter Address" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txtBillingCountry">Country:<span class="required"></span></label>
                                    <select name="txtBillingCountry" id="txtBillingCountry" class="form-control s2-country">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{ $country->name }}" data-code="{{ $country->iso_code_2 }}" {{ $partner_info->billing_country == $country->name ? "selected" : "" }}>{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="billingZip">Zip:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBillingZip" id="txtBillingZip"
                                        value="{{$partner_info->billing_zip}}" placeholder="Ext"/>
                                    @include('incs.zipHelpNote')
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txtBillingState">State:<span class="required"></span></label>
                                    <select name="txtBillingState" id="txtBillingState" class="form-control s2-state" disabled>
                                        <input type="hidden" name="txtStateBillingHidden" id="txtStateBillingHidden" value="{{ $partner_info->billing_state }}">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="city">City:<span class="required">*</span></label>
                                    {{-- <input type="text" class="form-control" name="txtCity" id="txtCity" value="{{$partner_info->city}}"
                                        placeholder="Enter City" /> --}}
                                    <select name="txtBillingCity" id="txtBillingCity" class="form-control select2" disabled>
                                            <option value="{{ $partner_info->billing_city }}" selected>{{ $partner_info->billing_city }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title pull-left">Mailing Address</h3>
                                <div class="pull-right">
                                    <input type="checkbox" name="chkSameAsBusiness" id="chkSameAsBusiness"> Same as
                                    Business Address
                                </div>
                            </div>
                        </div>
                        <div id="divMailingAddress" class="row">
                            <div class="clearfix"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtMailingAddress1">Mailing Address 1:</label>
                                    <input type="text" class="form-control" name="txtMailingAddress1" id="txtMailingAddress1"
                                        value="{{$partner_info->business_address}}" placeholder="Enter Mailing Address 1" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtMailingAddress2">Mailing Address 2:</label>
                                    <input type="text" class="form-control" name="txtMailingAddress2" id="txtMailingAddress2"
                                        value="{{$partner_info->business_address2}}" placeholder="Enter Mailing Address 2" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txtMailingCountry">Country:</label>
                                    <select name="txtMailingCountry" id="txtMailingCountry" class="form-control s2-country">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{ $country->name }}" data-code="{{ $country->iso_code_2 }}" {{ $partner_info->business_country == $country->name ? "selected" : "" }}>{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txtMailingZip">Zip:</label>
                                    <input type="text" class="form-control" name="txtMailingZip" id="txtMailingZip"
                                        value="{{$partner_info->business_zip}}" placeholder="Ext"/>
                                    @include('incs.zipHelpNote')
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txtMailingState">State:</label>
                                    <select name="txtMailingState" id="txtMailingState" class="form-control s2-state" disabled>
                                        <input type="hidden" name="txtStateMailingHidden" id="txtStateMailingHidden" value="{{ $partner_info->business_state }}">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label for="city">City:<span class="required">*</span></label>
                                    {{-- <input type="text" class="form-control" name="txtCity" id="txtCity" value="{{$partner_info->city}}"
                                        placeholder="Enter City" /> --}}
                                    <select name="txtMailingCity" id="txtMailingCity" class="form-control select2" disabled>
                                            <option value="{{ $partner_info->business_city }}" selected>{{ $partner_info->business_city }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title pull-left">Bank Information</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Bank Name:<span class="required"></span></label>
                                <input type="text" class="form-control" name="txtBankName" id="txtBankName" placeholder="Bank Name" value="{{ $partner_info->bank_name }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Bank Address:<span class="required"></span></label>
                                <input type="text" class="form-control" name="txtBankAddress" id="txtBankAddress" placeholder="Bank Address" value="{{ $partner_info->bank_address }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Bank Routing:<span class="required"></span></label>
                                <input type="text" class="form-control" name="txtBankRouting" id="txtBankRouting" placeholder="Bank Routing" value="{{ $partner_info->bank_routing_no }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Bank DDA:<span class="required"></span></label>
                                <input type="text" class="form-control" name="txtBankDDA" id="txtBankDDA" placeholder="Bank DDA" value="{{ $partner_info->bank_dda }}">
                            </div>
                        </div>

                        <div class="row" style="display: none">
                            <div class="row-header">
                                <h3 class="title">MID</h3>
                            </div>
                        </div>
                        <div class="row" style="display: none">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Front End MID:<span class="required"></span></label>
                                <input type="text" class="form-control" name="txtFrontEndMID" id="txtFrontEndMID" placeholder="Front End MID " value="{{ $partner_info->front_end_mid }}">
                                </div>
                            </div>
                            
                            <div class="col-lg-6 col-md-6 col-sm-12 ">
                                <div class="form-group">
                                    <label>Back End MID:<span class="required"></span></label>
                                <input type="text" class="form-control" name="txtBackEndMID" id="txtBackEndMID" placeholder="Back End MID" value="{{ $partner_info->back_end_mid }}">
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 ">
                                <div class="form-group">
                                    <label>Reporting MID:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtReportingMID" id="txtReportingMID" placeholder="Reporting MID" value="{{ $partner_info->reporting_mid }}">
                                </div>
                            </div>
                        </div>

                        @else
                        <!-- New Agent Creation Form -->
                        <!-- <input type="hidden" id="txtTogBtnUnpaid" name="txtTogBtnUnpaid" value="{{ $partner_info->email_unpaid_invoice == 1 ? 'on' : 'off' }}">
                        <input type="hidden" id="txtTogBtnPaid" name="txtTogBtnPaid" value="{{ $partner_info->email_paid_invoice == 1 ? 'on' : 'off' }}">
                        <input type="hidden" id="txtTogBtnSMTP" name="txtTogBtnSMTP" value="{{ $partner_info->smtp_settings == 1 ? 'on' : 'off' }}"> -->
                        <input type="hidden" id="txtPartnerType" name="txtPartnerType" value="{{ $partner_info->partner_type_id }}">
                        {{-- <div class="col-lg-12 agent-form"> --}}
                            <div class="custom-contact-wrap-sm row agent-form">
                                <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Business Name: <span class="required">*</span></label>
                                        <!-- Should be company_name since Business Name is required -->
                                        <input type="text" class="form-control" id="txtBusinessName" name="txtBusinessName"
                                            value="{{ $partner_info->company_name }}" placeholder="Enter Business Name">
                                        <span id="txtBusinessName-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                                <!-- <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Social Security Number: </label>
                                        <input type="text" class="form-control" id="txtSocialSecurityNumber" value="{{ $partner_info->social_security_id }}"
                                            name="txtSocialSecurityNumber" placeholder="Enter Social Security Number">
                                    </div>
                                </div> -->
                                <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>Legal Business Name: </label>
                                        <!-- Should be business_name since Business Name is required -->
                                        <input type="text" class="form-control" id="txtLegalBusinessName" name="txtLegalBusinessName"
                                            value="{{ $partner_info->business_name }}" placeholder="Enter Legal Business Name">
                                    </div>
                                </div>

                                    <div class="form-group col-md-4">
                                        <label for="txtTaxID">Tax ID Number:</label>
                                        <input type="text" class="form-control" name="txtTaxIDNumber" id="txtTaxIDNumber" value="{{$partner_info->tax_id_number}}"
                                            placeholder="Enter Tax ID" />
                                        <span id="txtTaxID-error" style="color:red;"><small></small></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="txtPricingType">Pricing Type:</label>
                                        <input type="text" class="form-control" name="txtPricingType" id="txtPricingType" value="{{$partner_info->pricing_type}}"
                                            placeholder="Enter Pricing Type" />
                                        <span id="txtPricingType-error" style="color:red;"><small></small></span>
                                    </div>
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
                                        <input type="text" class="form-control" name="txtBankName" id="txtBankName"
                                            placeholder="Bank Name" value="{{ $partner_info->bank_name }}">
                                    </div>
                                </div>
                                
                                <div class="col-lg- col-md-6 col-sm-12 ">
                                    <div class="form-group">
                                        <label>Bank Routing:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankRouting" id="txtBankRouting"
                                            placeholder="Bank Routing" value="{{ $partner_info->bank_routing_no }}">
                                    </div>
                                </div>

                                <div class="col-lg- col-md-6 col-sm-12 ">
                                    <div class="form-group">
                                        <label>Confirm Bank Routing:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankRoutingConfirmation" 
                                            id="txtBankRoutingConfirmation" placeholder="Confirm Bank Routing" value="{{ $partner_info->bank_routing_no }}">
                                        <span id="txtBankRoutingConfirmation-error" style="color: red;"><small></small></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 ">
                                    <div class="form-group">
                                        <label>Bank DDA:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankDDA" id="txtBankDDA"
                                            placeholder="Bank DDA" value="{{ $partner_info->bank_dda }}">
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-12 ">
                                    <div class="form-group">
                                        <label>Confirm Bank DDA:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="txtBankDDAConfirmation" id="txtBankDDAConfirmation" 
                                            placeholder="Confirm Bank DDA" value="{{ $partner_info->bank_dda  }}">
                                        <span id="txtBankDDAConfirmation-error" style="color: red;"><small></small></span>
                                    </div>
                                </div>

                                <div class="col-md-12 sm-col">
                                    <div class="row-header">
                                        <h3 class="title">Business Address </h3>
                                    </div>
                                </div>

                                <div class="col-lg-8 col-md-12 sm-col">
                                    <div class="tab-content" style="padding:10px;border: 1px solid #ddd;border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
                                        <!-- start tab content for Address -->
                                        <div class="tab-pane active" id="Legal">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Address: <span class="required">*</span></label>
                                                        <input type="text" class="form-control" id="txtAddressAgent"
                                                            name="txtAddressAgent" placeholder="Enter Address" value="{{ $partner_info->address1 }}">
                                                        <span id="txtAddressAgent-error" style="color:red;"><small></small></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="country">Country:<span class="required">*</span></label>
                                                        <select class="form-control s2-country select2-hidden-accessible"
                                                            style="width: 100%;" id="txtCountryAgent" name="txtCountryAgent"
                                                            tabindex="-1" aria-hidden="true">
                                                            @foreach($countries as $c)
                                                                <option value="{{ $c->name }}" data-code="{{ $c->iso_code_2 }}" {{ $partner_info->country_name == $c->name ? "selected" : "" }}>{{ $c->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="zip">Zip:<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="txtZipAgent" name="txtZipAgent"
                                                        placeholder="Enter Zip" value="{{ $partner_info->zip }}">
                                                    <span id="txtZipAgent-error" style="color:red;"><small></small></span>
                                                    @include('incs.zipHelpNote')
                                                </div>
                                                
                                                <div class="col-md-6" id="state_us" style="display: block;">
                                                    <div class="form-group">
                                                        <label for="state">State:<span class="required">*</span></label>
                                                        <select class="form-control s2-state select2-hidden-accessible"
                                                            style="width: 100%;" id="txtStateAgent" name="txtStateAgent"
                                                            tabindex="-1" aria-hidden="true" disabled>
                                                            <input type="hidden" name="txtStateHidden" id="txtStateHidden" value="{{ $partner_info->business_state }}">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="city">City:<span class="required">*</span></label>
                                                        {{-- <input type="text" class="form-control" id="txtCityAgent" name="txtCityAgent"
                                                            placeholder="Enter City" value="{{ $partner_info->city }}"> --}}
                                                        <select name="txtCityAgent" id="txtCityAgent" class="form-control select2" disabled>
                                                            @if ($partner_info->country_name == 'United States')
                                                            @foreach ($usCities as $usc)
                                                                <option value="{{ $usc->city }}"
                                                                {{ $partner_info->city == $usc->city ? "selected" : "" }}>{{ $usc->city }}</option>
                                                            @endforeach
                                                            @elseif ($partner_info->country_name == 'Philippines')
                                                            @foreach ($phCities as $pc)
                                                                <option value="{{ $pc->city }}"
                                                                {{ $partner_info->city == $pc->city ? "selected" : "" }}>{{ $pc->city }}</option>
                                                            @endforeach
                                                            @elseif ($partner_info->country_name == 'China')
                                                            @foreach ($cnCities as $cc)
                                                                <option value="{{ $cc->city }}"
                                                                {{ $partner_info->city == $cc->city ? "selected" : "" }}>{{ $cc->city }}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                        <span id="txtCityAgent-error" style="color:red;"><small></small></span>
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- end legal tab -->
                                        </div>
                                    </div></br>
                                    <div class="tab-content" style="padding:10px;border: 1px solid #ddd;border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;display: none">
                                        <div class="tab-pane-active">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Billing Cycle: <span class="required"></span></label>
                                                        <select class="form-control select2 select2-hidden-accessible"
                                                            style="width: 100%;" id="txtBillingCycle" name="txtBillingCycle"
                                                            tabindex="-1" aria-hidden="true">
                                                            <option value="Monthly"
                                                                {{ $partner_info->billing_cycle=='Monthly' ? "selected" : "" }}>Monthly</option>
                                                            <option value="Annually"
                                                                {{ $partner_info->billing_cycle=='Annually' ? "selected" : "" }}>Annually</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Month: <span class="required"></span></label>
                                                        <select class="form-control select2 select2-hidden-accessible"
                                                            style="width: 100%;" id="txtBillingMonth" name="txtBillingMonth"
                                                            tabindex="-1" aria-hidden="true">
                                                            <option value="January"
                                                                {{ $partner_info->billing_month=='January' ? "selected" : "" }}>January</option>
                                                            <option value="February"
                                                                {{ $partner_info->billing_month=='February' ? "selected" : "" }}>February</option>
                                                            <option value="March"
                                                                {{ $partner_info->billing_month=='March' ? "selected" : "" }}>March</option>
                                                            <option value="April"
                                                                {{ $partner_info->billing_month=='April' ? "selected" : "" }}>April</option>
                                                            <option value="May"
                                                                {{ $partner_info->billing_month=='May' ? "selected" : "" }}>May</option>
                                                            <option value="June"
                                                                {{ $partner_info->billing_month=='June' ? "selected" : "" }}>June</option>
                                                            <option value="July"
                                                                {{ $partner_info->billing_month=='July' ? "selected" : "" }}>July</option>
                                                            <option value="August"
                                                                {{ $partner_info->billing_month=='August' ? "selected" : "" }}>August</option>
                                                            <option value="September"
                                                                {{ $partner_info->billing_month=='September' ? "selected" : "" }}>September</option>
                                                            <option value="October"
                                                                {{ $partner_info->billing_month=='October' ? "selected" : "" }}>October</option>
                                                            <option value="November"
                                                                {{ $partner_info->billing_month=='November' ? "selected" : "" }}>November</option>
                                                            <option value="December"
                                                                {{ $partner_info->billing_month=='December' ? "selected" : "" }}>December</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>On what day? <span class="required"></span></label>
                                                        <select class="form-control select2 select2-hidden-accessible"
                                                            style="width: 100%;" id="txtbillingDay" name="txtBillingDay"
                                                            tabindex="-1" aria-hidden="true">
                                                            <option value="1"
                                                                {{ $partner_info->billing_day=='1' ? "selected" : "" }}>1st</option>
                                                            <option value="2"
                                                                {{ $partner_info->billing_day=='2' ? "selected" : "" }}>2nd</option>
                                                            <option value="3"
                                                                {{ $partner_info->billing_day=='3' ? "selected" : "" }}>3rd</option>
                                                            <option value="4"
                                                                {{ $partner_info->billing_day=='4' ? "selected" : "" }}>4th</option>
                                                            <option value="5"
                                                                {{ $partner_info->billing_day=='5' ? "selected" : "" }}>5th</option>
                                                            <option value="6"
                                                                {{ $partner_info->billing_day=='6' ? "selected" : "" }}>6th</option>
                                                            <option value="7"
                                                                {{ $partner_info->billing_day=='7' ? "selected" : "" }}>7th</option>
                                                            <option value="8"
                                                                {{ $partner_info->billing_day=='8' ? "selected" : "" }}>8th</option>
                                                            <option value="9"
                                                                {{ $partner_info->billing_day=='9' ? "selected" : "" }}>9th</option>
                                                            <option value="10"
                                                                {{ $partner_info->billing_day=='10' ? "selected" : "" }}>10th</option>
                                                            <option value="11"
                                                                {{ $partner_info->billing_day=='11' ? "selected" : "" }}>11th</option>
                                                            <option value="12"
                                                                {{ $partner_info->billing_day=='12' ? "selected" : "" }}>12th</option>
                                                            <option value="13"
                                                                {{ $partner_info->billing_day=='13' ? "selected" : "" }}>13th</option>
                                                            <option value="14"
                                                                {{ $partner_info->billing_day=='14' ? "selected" : "" }}>14th</option>
                                                            <option value="15"
                                                                {{ $partner_info->billing_day=='15' ? "selected" : "" }}>15th</option>
                                                            <option value="16"
                                                                {{ $partner_info->billing_day=='16' ? "selected" : "" }}>16th</option>
                                                            <option value="17"
                                                                {{ $partner_info->billing_day=='17' ? "selected" : "" }}>17th</option>
                                                            <option value="18"
                                                                {{ $partner_info->billing_day=='18' ? "selected" : "" }}>18th</option>
                                                            <option value="19"
                                                                {{ $partner_info->billing_day=='19' ? "selected" : "" }}>19th</option>
                                                            <option value="20"
                                                                {{ $partner_info->billing_day=='20' ? "selected" : "" }}>20th</option>
                                                            <option value="21"
                                                                {{ $partner_info->billing_day=='21' ? "selected" : "" }}>21st</option>
                                                            <option value="22"
                                                                {{ $partner_info->billing_day=='22' ? "selected" : "" }}>22nd</option>
                                                            <option value="23"
                                                                {{ $partner_info->billing_day=='23' ? "selected" : "" }}>23th</option>
                                                            <option value="24"
                                                                {{ $partner_info->billing_day=='24' ? "selected" : "" }}>24th</option>
                                                            <option value="25"
                                                                {{ $partner_info->billing_day=='25' ? "selected" : "" }}>25th</option>
                                                            <option value="26"
                                                                {{ $partner_info->billing_day=='26' ? "selected" : "" }}>26th</option>
                                                            <option value="27"
                                                                {{ $partner_info->billing_day=='27' ? "selected" : "" }}>27th</option>
                                                            <option value="28"
                                                                {{ $partner_info->billing_day=='28' ? "selected" : "" }}>28th</option>
                                                            <option value="29"
                                                                {{ $partner_info->billing_day=='29' ? "selected" : "" }}>29th</option>
                                                            <option value="30"
                                                                {{ $partner_info->billing_day=='30' ? "selected" : "" }}>30th</option>
                                                            <option value="31"
                                                                {{ $partner_info->billing_day=='31' ? "selected" : "" }}>31st</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-12 sm-col">
                                    <div class="tab-content" style="padding:10px;border: 1px solid #ddd;border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;">
                                        <div class="form-group">
                                            <label>Phone Number: <span class="required"></span></label>
                                            <div class="input-group">
                                                <label for="BusinessPhone" class="input-group-addon">1</label>
                                                <input type="text" class="form-control number-only" id="txtPhoneNumber"
                                                    name="txtPhoneNumber" placeholder="Enter Phone Number" value="{{ $partner_info->nd_phone1 }}">
                                            </div>
                                            <span id="txtPhoneNumber-error" style="color:red;"><small></small></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Email Notifier:<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtEmailNotifier" id="txtEmailNotifier"
                                                placeholder="Enter Email Notifier" value="{{ $partner_info->email_notifier }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email
                                                <small>(must be valid)</small> :
                                                <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="txtEmailAgent" name="txtEmailAgent"
                                                placeholder="Enter Email" 
                                                value="{{ $partner_info->email }}"
                                                onblur="validateData('users','email_address',this,{{$id}},'true','reference_', 'Email address already been used by other users'); validateData('partner_companies','email',this,{{$id}},'false','partner_', 'Email address already been used by other partners');">
                                            <span id="txtEmailAgent-error" style="color:red;"><small></small></span>
                                        </div>
                                        <div class="form-group" style="display:none;">
                                            <label>Email Unpaid Invoice: </label>
                                            <label class="switch switch-unpaid">
                                                <input type="checkbox" id="togBtnUnpaid" {{ $partner_info->email_unpaid_invoice == 1 ? "checked" : "" }}>
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
                                                <input type="checkbox" id="togBtnPaid" {{ $partner_info->email_paid_invoice == 1 ? "checked" : "" }}>
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
                                                <input type="checkbox" id="togBtnSMTP" {{ $partner_info->smtp_settings == 1 ? "checked" : "" }}>
                                                <div class="slider round">
                                                    <!--ADDED HTML -->
                                                    <span class="on">Custom</span><span class="off">Default</span>
                                                    <!--END-->
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 sm-col">
                            <div class="row-header">
                                <h3 class="title">MID</h3>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Front End MID:<span class="required"></span></label>
                            <input type="text" class="form-control" name="txtFrontEndMID" id="txtFrontEndMID" placeholder="Front End MID " value="{{ $partner_info->front_end_mid }}">
                            </div>
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-12 ">
                            <div class="form-group">
                                <label>Back End MID:<span class="required"></span></label>
                            <input type="text" class="form-control" name="txtBackEndMID" id="txtBackEndMID" placeholder="Back End MID" value="{{ $partner_info->back_end_mid }}">
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 ">
                            <div class="form-group">
                                <label>Reporting MID:<span class="required"></span></label>
                                <input type="text" class="form-control" name="txtReportingMID" id="txtReportingMID" placeholder="Reporting MID" value="{{ $partner_info->reporting_mid }}">
                            </div>
                        </div>

                        <!-- End of New Merchant Creation Form -->
                        @endif



                        <div class="form-group pull-right col-lg-12">
                            @php
                            if(array_key_exists(strtolower($partner_info->partner_type_description),$access)){
                            if(strpos($access[strtolower($partner_info->partner_type_description)], 'edit') !== false){
                            @endphp
                            <button type="submit" class="pull-right btn btn-primary" id="btnCreatePartner" name="btnCreatePartner">
                                Save
                            </button>
                            @php } } @endphp
                        </div>
                        <div class="clearfix"></div>
                    </div>
    </form>
</div>
</section>
<!-- /.content -->
</div>
@endsection

@section("script")
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ config(" app.cdn ") . "/js/partners/partner.js" . "?v=" . config(" app.version ") }}"></script>
<script src="{{ config(' app.cdn ') . '/js/partners/newFieldValidation.js' . '?v=' . config(' app.version ') }}"></script>
<script src=@cdn('/js/supplierLeads/mcc.js')></script>
@endsection