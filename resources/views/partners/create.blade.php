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
            Create Partner
            <!-- <small>Dito tayo magpapasok ng different pages</small> -->
        </h1>
        <ol class="breadcrumb">
            <li><a href="/">Dashboard</a></li>
            <li><a href="/partners">Partners</a></li>
            <li class="active">Create Partner</li>
        </ol>
        <div class="dotted-hr"></div>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">
        <form id="frmPartner" name="frmPartner" method="post" enctype="multipart/form-data" action="{{ url("/partners") }}">
            {{ csrf_field() }}
            <input type="hidden" id="txtOtherHidden" name="txtOtherHidden">
            <input type="hidden" id="txtOtherHidden1" name="txtOtherHidden1">
            <input type="hidden" id="groupType" name="groupType">
            <!-- <input type="hidden" id="txtTogBtnUnpaid" name="txtTogBtnUnpaid" value="off">
            <input type="hidden" id="txtTogBtnPaid" name="txtTogBtnPaid" value="off">
            <input type="hidden" id="txtTogBtnSMTP" name="txtTogBtnSMTP" value="off"> -->
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
                                    <div>
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
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label for="partnerType">Partner Type:</label>
                                            <select name="txtPartnerTypeId" id="txtPartnerTypeId" class="form-control select2">
                                                @if(count($partner_types)>0)
                                                @foreach($partner_types as $partner_type)
                                                <option value="{{$partner_type->id}}">{{$partner_type->display_name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        @if($is_internal)
                                        <div class="form-group col-md-3 assignToMe {{ $systemUser ? '' : 'hidden' }}">
                                            <input type="checkbox" name="assigntome" id="assigntome" class="assigntome" value=""
                                                {{ $systemUser ? 'checked' : '' }} />
                                            <strong>
                                                Set Parent as:
                                            </strong>
                                            <label for="assigntome">
                                                @if (auth()->user()->is_original_partner != 1)
                                                {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}
                                                <span style="color:rgb(255, 165, 0)">({{ $userDepartment }})<span>
                                                        @else
                                                        {{ session('company_name') }} (COMPANY)
                                                        @endif
                                            </label>
                                            <input type="hidden" name="selfAssign" id="selfAssign">
                                        </div>
                                        @else
                                            <input type="checkbox" class="assigntome" id="assigntome" hidden>
                                        @endif
                                        <div id="divUpline" class="form-group col-md-6 assigntodiv" style="display:none">
                                            <label>Parent:</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <select class="form-control" id="txtUplinePartnerType" name="txtUplinePartnerType">
                                                        @if(count($partner_types)>0)
                                                        @foreach($partner_types as $partner_type)
                                                        <option value="{{$partner_type->id}}">{{$partner_type->name}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select class="form-control select2" id="txtUplineId" name="txtUplineId" style="width:100%"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="txtDBA">Legal Name:<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtDBA" id="txtDBA" value=""
                                                placeholder="Enter Legal Name" maxlength="50"/>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="txtCompanyName">DBA:<span class="required">*</span></label>
                                            <input type="text" class="form-control" name="txtCompanyName" id="txtCompanyName"
                                                value="" placeholder="Enter DBA" maxlength="50"/>
                                            <span id="txtCompanyName-error" style="color:red;"><small></small></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="txtOwnership">Ownership:<span class="required"></span></label>
                                            <select name="txtOwnership" id="txtOwnership" class="form-control select2">
                                                @if(count($ownerships)>0)
                                                @foreach($ownerships as $ownership)
                                                <option value="{{$ownership->code}}">{{$ownership->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4" style="display:none;">
                                            <label for="txtCreditCardReference">Credit Card Reference ID:</label>
                                            <input type="text" class="form-control" name="txtCreditCardReference" id="txtCreditCardReference"
                                                value="" placeholder="Enter Reference ID" maxlength="50"/>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="txtBusinessDate">Date when business was opened:</label>
                                            <input type="text" class="form-control integer-only" name="txtBusinessDate" id="txtBusinessDate" value="" placeholder="mm/yyyy"/>
                                            <span id="txtBusinessDate-error" style="color:red;"><small></small></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="txtWebsite">Website:</label>
                                            <input type="text" class="form-control" name="txtWebsite" id="txtWebsite" value=""
                                                placeholder="Enter Website" maxlength="50"/>
                                            <span id="txtWebsite-error" style="color:red;"><small></small></span>
                                        </div>

                                        <div class="form-group col-md-4" style="display:none;">
                                            <label for="txtTaxID">Tax ID:</label>
                                            <input type="text" class="form-control" name="txtTaxID" id="txtTaxID" value=""
                                                placeholder="Enter Tax ID" maxlength="50"/>
                                            <span id="txtTaxID-error" style="color:red;"><small></small></span>
                                        </div>

                                        <div class="form-group col-md-4" style="display: none">
                                            <label for="txtPricingType">Pricing Type:</label>
                                            <input type="text" class="form-control" name="txtPricingType" id="txtPricingType" value=""
                                                placeholder="Enter Pricing Type" />
                                            <span id="txtPricingType-error" style="color:red;"><small></small></span>
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
                                                    <label for="businessAddress1">Address: <span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="txtBusinessAddress1" name="txtBusinessAddress1" placeholder="Enter address"></small></span>
                                                    <span id="txtBusinessAddress1-error" style="color:red;"><small></small></span>
                                                </div>

                                                <div class="form-group col-md-12">
                                                    <label for="businessAddress2">Address 2: <span class="required"></span></label>
                                                    <input type="text" class="form-control" id="txtBusinessAddress2" name="txtBusinessAddress2" placeholder="Enter address 2"></small></span>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="txtCountry">Country:<span class="required">*</span></label>
                                                    <select class="form-control s2-country"
                                                        style="width: 100%;" id="txtCountry" name="txtCountry" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($countries as $c)
                                                            <option value="{{ $c->name }}" data-code="{{ $c->iso_code_2 }}">{{ $c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="zip">Zip:<span class="required">*</label>
                                                    <input type="text" class="form-control" id="txtBusinessZip" name="txtBusinessZip"
                                                        placeholder="Enter zip" onkeypress="return isNumberKey(event)">
                                                    <span id="txtBusinessZip-error" style="color:red;"><small></small></span>
                                                    @include('incs.zipHelpNote')
                                                </div>

                                                <div class="form-group col-md-3" id="state_us" style="display: block;">
                                                    <label for="txtState">State:</label>
                                                    <select name="txtState" id="txtState" class="form-control s2-state" disabled>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="city">City:</label>
                                                    {{-- <input type="text" class="form-control" name="txtCity" id="txtCity" value=""
                                                        placeholder="Enter City" /> --}}
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
                                                    <label for="txtBillingAddress1">Address: </label>
                                                    <input type="text" class="form-control" id="txtBillingAddress1" name="txtBillingAddress1" placeholder="Enter Billing Address 1"></small></span>
                                                    <span id="txtBillingAddress-error" style="color:red;"><small></small></span>
                                                </div>

                                                <div class="form-group col-md-12">
                                                    <label for="txtBillingAddress2">Address 2:</label>
                                                    <input type="text" class="form-control" name="txtBillingAddress2" id="txtBillingAddress2"
                                                        value="" placeholder="Enter Billing Address 2" />
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="txtBillingCountry">Country:<span class="required"></span></label>
                                                    <select class="form-control s2-country"
                                                        style="width: 100%;" id="txtBillingCountry" name="txtBillingCountry" tabindex="-1"
                                                        aria-hidden="true">
                                                        @foreach($countries as $c)
                                                            <option value="{{ $c->name }}" data-code="{{ $c->iso_code_2 }}">{{ $c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="txtBillingZip">Zip:<span class="required">*</span></label>
                                                    <input type="text" class="form-control" name="txtBillingZip" id="txtBillingZip"
                                                        value="" placeholder="Zip" onkeypress="return isNumberKey(event)"/>
                                                        <span id="txtBillingZip-error" style="color:red;"><small></small></span>
                                                    @include('incs.zipHelpNote')
                                                </div>
                                                <div class="form-group col-md-3" id="state_ship_us" style="display: block;">
                                                    <label for="txtBillingState">State:<span class="required"></span></label>
                                                    <select name="txtBillingState" id="txtBillingState" class="form-control s2-state" disabled>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="txtBillingCity">City:<span class="required"></span></label>
                                                    <select name="txtBillingCity" id="txtBillingCity" class="form-control select2" disabled>
                                                        @foreach ($initialCities as $c)
                                                            <option value="{{ $c->city }}">{{ $c->city }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                            </div>
                                        </div>
                                        
                                        <div class="tab-pane" id="Mailing">
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="txtMailingAddress1">Address 1:<span class="required"></span></label>
                                                    <input type="text" class="form-control" name="txtMailingAddress1" id="txtMailingAddress1"
                                                        value="" placeholder="Enter Mailing Address 1" />
                                                </div>

                                                <div class="form-group col-md-12">
                                                    <label for="txtMailingAddress2">Address 2:</label>
                                                    <input type="text" class="form-control" name="txtMailingAddress2" id="txtMailingAddress2"
                                                        value="" placeholder="Enter Mailing Address 2" />
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="txtMailingCountry">Country:<span class="required"></span></label>
                                                    <select name="txtMailingCountry" id="txtMailingCountry" class="form-control s2-country">
                                                        @if(count($countries)>0)
                                                            @foreach($countries as $c)
                                                                <option value="{{$c->name}}" data-code="{{$c->iso_code_2}}">{{$c->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="txtMailingZip">Zip:<span class="required">*</span></label>
                                                    <input type="text" class="form-control" name="txtMailingZip" id="txtMailingZip"
                                                        value="" placeholder="Zip" onkeypress="return isNumberKey(event)"/>
                                                        <span id="txtMailingZip-error" style="color:red;"><small></small></span>
                                                    @include('incs.zipHelpNote')
                                                </div>
                                                <div class="form-group col-md-3" id="state_mail_us" style="display: block;">
                                                    <label for="txtMailingState">State:<span class="required"></span></label>
                                                    <select name="txtMailingState" id="txtMailingState" class="form-control s2-state" disabled>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="txtMailingCity">City:<span class="required"></span></label>
                                                    <select name="txtMailingCity" id="txtMailingCity" class="form-control select2" disabled>
                                                        @foreach ($initialCities as $c)
                                                            <option value="{{ $c->city }}">{{ $c->city }}</option>
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
                                            <label>Business Phone 1: <span class="required">*</span></label>
                                            <div class="input-group">
                                                <label for="businessPhone" class="input-group-addon">1</label>
                                                <input type="text" class="form-control w-50 number-only" id="txtBusinessPhone1"
                                                    name="txtBusinessPhone1" placeholder="Enter Business Phone 1">
                                                <input type="text" class="form-control" name="txtExtension1" id="txtExtension1"
                                                    value="" placeholder="Ext" />
                                                <button class="btn btn-primary" type="button" title="Add Alternate Business Phone" onclick="$('#phone2').toggleClass('hide')"><i class="fa fa-plus-square"></i></button>
                                            </div>
                                            <span id="txtBusinessPhone1-error" style="color:red;"><small></small></span>
                                        </div>
                                        <div class="form-group hide" id="phone2">
                                            <label>Business Phone 2: <span class="required"></span></label>
                                            <div class="input-group">
                                                <label for="businessPhone" class="input-group-addon">1</label>
                                                <input type="text" class="form-control w-50 number-only" id="txtBusinessPhone2"
                                                    name="txtBusinessPhone2" placeholder="Enter Business Phone 2">
                                                <input type="text" class="form-control" name="txtExtension2" id="txtExtension2"
                                                    value="" placeholder="Ext" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="fax">Fax:</label>
                                            <div class="input-group">
                                                <label for="businessPhone" class="input-group-addon">1</label>
                                                <input type="text" class="form-control w-50 number-only" name="txtFax" id="txtFax" value=""
                                                    placeholder="Enter Fax" />
                                                <input type="text" class="form-control" name="txtExtension3" id="txtExtension3"
                                                    value="" placeholder="Ext" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="txtEmail" title="This will be used for sending user credentials upon registration.">Email(must
                                                be valid):<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtEmail" id="txtEmail" value=""
                                                placeholder="Enter Email"
                                                title="This will be used for sending user credentials upon registration." />
                                            <span id="txtEmail-error" class="help-block"><small>Note: If left blank, Partner must
                                                have at least the Contact Person's Mobile Number.</small></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <!-- END timeline item -->

                <!-- timeline time label -->
                <li class="time-label" style="display: none">
                    <span class="bg-redpink text-white px-3 py-2">
                        <i class="fa fa-industry"></i>&nbsp;
                        MID
                    </span>
                </li>
                <!-- /.timeline-label -->
            
                <!-- timeline item -->
                <li id="section3" style="display: none">
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
                        </div>
                    </div>
                </li>


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
                                <div class="form-group col-md-2">
                                    <label>Title: <span class="required"></span></label>
                                    <input type="text" class="form-control alpha" id="txtContactTitle1" name="txtContactTitle1"
                                        placeholder="Enter Title">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>First Name: <span class="required">*</span></label>
                                    <input type="text" class="form-control alpha" id="txtContactFirstName1" name="txtContactFirstName1"
                                        placeholder="Enter First Name">
                                    <span id="txtContactFirstName1-error" style="color:red;"><small></small></span>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>M.I.:</label>
                                    <input type="text" class="form-control alpha" id="txtContactMiddleInitial1" name="txtContactMiddleInitial1"
                                        placeholder="MI" maxlength="1">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Last Name: <span class="required">*</span></label>
                                    <input type="text" class="form-control alpha" id="txtContactLastName1" name="txtContactLastName1"
                                        placeholder="Enter Last Name">
                                    <span id="txtContactLastName1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label>Social Security Number: <span class="required"></span></label>
                                    <input type="text" class="form-control integer-only" id="txtContactSSN1" value=""
                                        name="txtContactSSN1" placeholder="Enter Social Security Number"
                                        onblur="validateData('partner_contacts','ssn',this,'-1','false','empty', 'SSN already been used by other contacts');">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Percentage of Ownership:</label>
                                    <div class="input-group">
                                    <input type="text" class="form-control ownerPercentage" name="txtOwnershipPercentage1" id="txtOwnershipPercentage1"
                                        value="100" placeholder="0" onkeypress="return isNumberKey(event)" onchange="validateOwnershipPerc(this);">
                                    <label for="txtOwnershipPercentage1" class="input-group-addon">%</label>
                                    </div>
                                    <span id="txtOwnershipPercentage1-error" style="color:red;"><small></small></span>
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Date of Birth:</label>
                                    <input type="text" class="form-control integer-only" name="txtContactDOB1" id="txtContactDOB1"
                                        value="" placeholder="MM/DD/YYYY">
                                    <span id="txtContactDOB1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">

                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="">Home Address: <span class="required"></span></label>
                                            <input type="text" class="form-control" id="txtContactHomeAddress1_1" name="txtContactHomeAddress1_1" placeholder="Enter address"></small></span>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="">Home Address 2: <span class="required"></span></label>
                                            <input type="text" class="form-control" id="txtContactHomeAddress1_2" name="txtContactHomeAddress1_2" placeholder="Enter address 2"></small></span>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="txtCountry">Country:<span class="required"></span></label>
                                            <select class="form-control s2-country"
                                                style="width: 100%;" id="txtContactCountry1" name="txtContactCountry1" tabindex="-1"
                                                aria-hidden="true">
                                                @foreach($countries as $c)
                                                    <option value="{{ $c->name }}" data-code="{{ $c->iso_code_2 }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="zip">Zip:<span class="required">*</span></label>
                                            <input type="text" class="form-control" id="txtContactZip1" name="txtContactZip1"
                                                placeholder="Enter zip" onkeypress="return isNumberKey(event)">
                                            <span id="txtContactZip1-error" style="color:red;"><small></small></span>
                                            @include('incs.zipHelpNote')
                                        </div>

                                        <div class="form-group col-md-3" id="state_us">
                                            <label>State:<span class="required"></span></label>
                                            <select name="txtContactState1" id="txtContactState1" class="form-control s2-state" disabled>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="city">City:<span class="required"></span></label>
                                            <select name="txtContactCity1" id="txtContactCity1" class="form-control select2" disabled>
                                                @foreach ($initialCities as $c)
                                                    <option value="{{ $c->city }}">{{ $c->city }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
    
                                <div class="col-md-4">
                                    <div class="tab-content" style="padding:10px;border:1px solid #ddd;">
                                        <div class="form-group">
                                            <label>Phone 1: <span class="required"></span></label>
                                            <div class="input-group">
                                                <label for="contactPhone1" class="input-group-addon">1</label>
                                                <input type="text" class="form-control w-50 number-only" id="txtContactPhone1_1"
                                                    name="txtContactPhone1_1" placeholder="Enter Phone 1">
                                                <button class="btn btn-primary" type="button" title="Add Alternate Phone 2" onclick="$('#cphone1').toggleClass('hide')"><i class="fa fa-plus-square"></i></button>
                                            </div>
                                            <span id="txtPhoneNumber-error" style="color:red;"><small></small></span>
                                        </div>
                                        <div class="form-group hide" id="cphone1">
                                            <label>Phone 2: <span class="required"></span></label>
                                            <div class="input-group">
                                                <label for="contactPhone1" class="input-group-addon">1</label>
                                                <input type="text" class="form-control w-50 number-only" id="txtContactPhone1_2"
                                                    name="txtContactPhone1_2" placeholder="Enter Phone 2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Mobile number 1: <span class="required"></span></label>
                                            <div class="input-group">
                                                <label for="contactPhone1" class="input-group-addon">1</label>
                                                <input type="text" class="form-control w-50 number-only" id="txtContactMobile1_1"
                                                    name="txtContactMobile1_1" placeholder="Enter Mobile 1">
                                                <button class="btn btn-primary" type="button" title="Add Alternate Mobile 2" onclick="$('#cphone2').toggleClass('hide')"><i class="fa fa-plus-square"></i></button>
                                            </div>
                                            <span id="txtContactMobile1_1-error" class="help-block"><small></small></span>
                                        </div>
                                        <div class="form-group hide" id="cphone2">
                                            <label>Mobile Number 2: <span class="required"></span></label>
                                            <div class="input-group">
                                                <label for="contactPhone1" class="input-group-addon">1</label>
                                                <input type="text" class="form-control w-50 number-only" id="txtContactMobile1_2"
                                                    name="txtContactMobile1_2" placeholder="Enter Mobile 2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Email <small></small>:<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtContactEmail1" id="txtContactEmail1"
                                                value="" placeholder="Enter Email Address">
                                            <span id="txtContactEmail1-error" style="color:red;"><small></small></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 offset-md-10">
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
                                    <input type="text" class="form-control" name="txtBankRouting" id="txtBankRouting" placeholder="Bank Routing" value="">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Bank DDA:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBankDDA" id="txtBankDDA" placeholder="Bank DDA" value="">
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
                                        @if($document->sequence != 0)
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
                                <div class="col-md-2 align-self-end offset-md-10">
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
                    @include('incs.saveAsDraft')
                @endif
                <button type="submit" class="pull-right btn btn-success ml-3">
                    Submit
                </button>
            </div>

        </form>
    </section>
    <input type="hidden" name="system-user" value="{{ $systemUser }}" />
</div>
@endsection

@section("script")
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ config(' app.cdn ') . '/js/partners/partner.js' . '?v=' . config(' app.version ') }}"></script>
<script src="{{ config(' app.cdn ') . '/js/partners/newFieldValidation.js' . '?v=' . config(' app.version ') }}"></script>
@if (!$systemUser)
<script>
    $('#assigntome').prop('checked', false);
</script>
@endif
<script src=@cdn('/js/supplierLeads/mcc.js')></script>
<script src="{{ config(' app.cdn ') . '/js/clearInput.js' . '?v=' . config(' app.version ') }}"></script>

@endsection