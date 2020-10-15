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
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle hide">
                    <li class="active"><a href="#business-info" id="bi-tab" data-toggle="tab" aria-expanded="true">Business
                            Information</a></li>
                    <li><a href="#contact-person" id="cp-tab" data-toggle="tab" aria-expanded="false">Contact Persons</a></li>
                    <li><a href="#attachments" id="at-tab" data-toggle="tab" aria-expanded="false">Attachments</a></li>
                    <li><a href="#preview" id="pr-tab" data-toggle="tab" aria-expanded="false">Preview</a></li>
                </ul>

                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="business-info">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Company Information</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="partnerType">Partner Type:</label>
                                    <select name="txtPartnerTypeId" id="txtPartnerTypeId" class="form-control">
                                        @if(count($partner_types)>0)
                                        @foreach($partner_types as $partner_type)
                                        <option value="{{$partner_type->id}}">{{$partner_type->display_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            @if($is_internal)
                            <div class="col-lg-2 col-md-6 col-sm-12 sm-col assignToMe {{ $systemUser ? '' : 'hidden' }}">
                                <div class="form-group">
                                    <br>
                                    <input type="checkbox" name="assigntome" id="assigntome" class="assigntome" value=""
                                        {{ $systemUser ? 'checked' : '' }} />
                                    <label for="assigntome">Set Parent as
                                        @if (auth()->user()->is_original_partner != 1)
                                        {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}
                                        <span style="color:rgb(255, 165, 0)">({{ $userDepartment }})<span>
                                                @else
                                                {{ session('company_name') }} (COMPANY)
                                                @endif
                                    </label>
                                    <input type="hidden" name="selfAssign" id="selfAssign">
                                </div>
                            </div>
                            @else
                                <input type="checkbox" class="assigntome" id="assigntome" hidden>
                            @endif
                            

                            <div id="divUpline" class="col-md-6 assigntodiv" style="display:none">
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
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <select class="form-control select2" id="txtUplineId" name="txtUplineId" style="width:100%"></select>
                                    </div>
                                </div>
                            </div>

                            <!-- New Agent Creation Form -->
                            <div class="col-lg-12 agent-form">
                                <div class="custom-contact-wrap-sm row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label>Business Name: <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="txtBusinessName" name="txtBusinessName"
                                                value="" placeholder="Enter Business Name">
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
                                                value="" placeholder="Enter Legal Business Name">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                        <div class="form-group">
                                            <label>Tax ID Number: </label>
                                            <input type="text" class="form-control" id="txtTaxIdNumber" value="" name="txtTaxIdNumber"
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
                                            <input type="text" class="form-control" name="txtBankName" id="txtBankName" placeholder="Bank Name">
                                        </div>
                                    </div>

                                    <div class="col-lg- col-md-6 col-sm-12 ">
                                        <div class="form-group">
                                            <label>Bank Routing:<span class="required"></span></label>
                                            <input type="text" class="form-control" name="txtBankRouting" id="txtBankRouting"
                                                placeholder="Bank Routing" value="">
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
                                                placeholder="Bank DDA" value="">
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
                                            <h3 class="title"> Company Contact Information </h3>
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
                                                                name="txtAddressAgent" placeholder="Enter Address">
                                                            <span id="txtAddressAgent-error" style="color:red"><small></small></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="country">Country:<span class="required">*</span></label>
                                                            <select class="form-control select2 select2-hidden-accessible"
                                                                style="width: 100%;" id="txtCountryAgent" name="txtCountryAgent"
                                                                tabindex="-1" aria-hidden="true">
                                                                @foreach($countries as $c)
                                                                    <option value="{{$c->name}}" data-code="{{$c->iso_code_2}}">{{$c->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="zip">Zip:<span class="required">*</span></label>
                                                        <input type="text" class="form-control" id="txtZipAgent" name="txtZipAgent"
                                                            placeholder="Enter Zip" onblur="isValidZip(this, 'txtCityAgent', 'txtStateAgent')">
                                                        <span id="txtZipAgent-error" style="color:red"><small></small></span>
                                                    </div>

                                                    <div class="col-md-6" id="state_us" style="display: block;">
                                                        <div class="form-group">
                                                            <label for="state">State:<span class="required">*</span></label>
                                                            <select class="form-control select2 select2-hidden-accessible"
                                                                style="width: 100%;" id="txtStateAgent" name="txtStateAgent"
                                                                tabindex="-1" aria-hidden="true">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="city">City:<span class="required">*</span></label>
                                                            <input type="text" class="form-control" id="txtCityAgent" name="txtCityAgent"
                                                                placeholder="Enter City">
                                                            <span id="txtCityAgent-error" style="color:red"><small></small></span>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- end legal tab -->
                                            </div>
                                        </div><br>
                                        <div class="tab-content" style="padding:10px;border: 1px solid #ddd;border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;display:none;">
                                            <div class="tab-pane-active">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Billing Cycle: <span class="required"></span></label>
                                                            <select class="form-control select2 select2-hidden-accessible"
                                                                style="width: 100%;" id="txtBillingCycle" name="txtBillingCycle"
                                                                tabindex="-1" aria-hidden="true">
                                                                <option value="Monthly">Monthly</option>
                                                                <option value="Annually">Annually</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Month: <span class="required"></span></label>
                                                            <select class="form-control select2 select2-hidden-accessible"
                                                                style="width: 100%;" id="txtBillingMonth" name="txtBillingMonth"
                                                                tabindex="-1" aria-hidden="true">
                                                                <option value="January">January</option>
                                                                <option value="February">February</option>
                                                                <option value="March">March</option>
                                                                <option value="April">April</option>
                                                                <option value="May">May</option>
                                                                <option value="June">June</option>
                                                                <option value="July">July</option>
                                                                <option value="August">August</option>
                                                                <option value="September">September</option>
                                                                <option value="October">October</option>
                                                                <option value="November">November</option>
                                                                <option value="December">December</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>On what day? <span class="required"></span></label>
                                                            <select class="form-control select2 select2-hidden-accessible"
                                                                style="width: 100%;" id="txtbillingDay" name="txtBillingDay" tabindex="-1"
                                                                aria-hidden="true">
                                                                <option value="1">1st</option>
                                                                <option value="2">2nd</option>
                                                                <option value="3">3rd</option>
                                                                <option value="4">4th</option>
                                                                <option value="5">5th</option>
                                                                <option value="6">6th</option>
                                                                <option value="7">7th</option>
                                                                <option value="8">8th</option>
                                                                <option value="9">9th</option>
                                                                <option value="10">10th</option>
                                                                <option value="11">11th</option>
                                                                <option value="12">12th</option>
                                                                <option value="13">13th</option>
                                                                <option value="14">14th</option>
                                                                <option value="15">15th</option>
                                                                <option value="16">16th</option>
                                                                <option value="17">17th</option>
                                                                <option value="18">18th</option>
                                                                <option value="19">19th</option>
                                                                <option value="20">20th</option>
                                                                <option value="21">21st</option>
                                                                <option value="22">22nd</option>
                                                                <option value="23">23th</option>
                                                                <option value="24">24th</option>
                                                                <option value="25">25th</option>
                                                                <option value="26">26th</option>
                                                                <option value="27">27th</option>
                                                                <option value="28">28th</option>
                                                                <option value="29">29th</option>
                                                                <option value="30">30th</option>
                                                                <option value="31">31st</option>
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
                                                    <div class="input-group-addon">
                                                        <label for="BusinessPhone">1</label>
                                                    </div>
                                                    <input type="text" class="form-control number-only" id="txtPhoneNumber"
                                                        name="txtPhoneNumber" placeholder="Enter Phone Number">
                                                </div>
                                                <span id="txtPhoneNumber-error" style="color:red"><small></small></span>
                                            </div>
                                            <div class="form-group">
                                                <label>Email Notifier:<span class="required"></span></label>
                                                <input type="text" class="form-control" name="txtEmailNotifier" id="txtEmailNotifier"
                                                    placeholder="Enter Email Notifier" value="">
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email
                                                    <small>(must be valid)</small> :
                                                    <span class="required"></span></label>
                                                <input type="text" class="form-control" id="txtEmailAgent" name="txtEmailAgent"
                                                    placeholder="Enter Email" onblur="validateData('users','email_address',this,'-1','true','empty', 'Email address already been used by other users'); validateData('partner_companies','email',this,'-1','false','empty', 'Email address already been used by other partners');">
                                                <span id="txtEmailAgent-error" style="color:red;"><small></small></span>
                                                <span class="error" style="color:orange;"><small>Note: If left blank, Partner must have at least the Contact Person's Mobile Number.</small></span>
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of New Agent Creation Form -->

                            <div class="col-lg-6 col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="txtOwnership">Ownership:<span class="required"></span></label>
                                    <select name="txtOwnership" id="txtOwnership" class="form-control">
                                        @if(count($ownerships)>0)
                                        @foreach($ownerships as $ownership)
                                        <option value="{{$ownership->code}}">{{$ownership->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="txtCompanyName">DBA:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtCompanyName" id="txtCompanyName"
                                        value="" placeholder="Enter DBA" />
                                    <span id="txtCompanyName-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>
                            <!-- <div class="col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="ssn">EIN:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtSSN" id="txtSSN" value=""
                                        placeholder="Enter SSN/EIN" onblur="validateData('partner_companies','ssn',this,'-1','false','empty', 'SSN already been used by other partners', 'bi-tab');">
                                </div>
                            </div> -->
                            <div class="col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="txtDBA">ISO/Affiliate Name (Legal Name/Business Name):<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtDBA" id="txtDBA" value=""
                                        placeholder="Enter Legal Name" />
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="txtBusinessDate">Date when business was opened:</label>
                                    <input type="text" class="form-control" name="txtBusinessDate" id="txtBusinessDate"
                                        value="" placeholder="mm/yyyy" />
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="txtCreditCardReference">Credit Card Reference ID:</label>
                                    <input type="text" class="form-control" name="txtCreditCardReference" id="txtCreditCardReference"
                                        value="" placeholder="Enter Reference ID" />
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="txtWebsite">Website:</label>
                                    <input type="text" class="form-control" name="txtWebsite" id="txtWebsite" value=""
                                        placeholder="Enter Website" />
                                    <span id="txtWebsite-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header non-agent-form">
                                <h3 class="title">Address</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-6 col-md-12 non-agent-form">
                                <div class="form-group">
                                    <label for="businessAddress1">Business Address 1:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtBusinessAddress1" id="txtBusinessAddress1"
                                        value="" placeholder="Enter Address" />
                                    <span id="txtBusinessAddress1-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 non-agent-form">
                                <div class="form-group">
                                    <label for="businessAddress2">Business Address 2:</label>
                                    <input type="text" class="form-control" name="txtBusinessAddress2" id="txtBusinessAddress2"
                                        value="" placeholder="Enter Address" />
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="txtCountry">Country:<span class="required">*</span></label>
                                    <select name="txtCountry" id="txtCountry" class="form-control">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}" data-code="{{$country->iso_code_2}}">{{$country->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="zip">Zip:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtBusinessZip" id="txtBusinessZip"
                                        value="" placeholder="Zip" onkeypress="return isNumberKey(event)" onblur="isValidZip(this, 'txtCity', 'txtState')"/>
                                    <span id="txtBusinessZip-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="txtState">State:<span class="required">*</span></label>
                                    <select name="txtState" id="txtState" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 non-agent-form">
                                <div class="form-group">
                                    <label for="city">City:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtCity" id="txtCity" value=""
                                        placeholder="Enter City" />
                                    <span id="txtCity-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>

                        </div>

                        <div class="row non-agent-form">
                            <div class="row-header">
                                <h3 class="title pull-left">Billing Address</h3>
                                <div class="pull-right">
                                    <input type="checkbox" name="chkSameAsBusinessBilling" id="chkSameAsBusinessBilling"> Same as
                                    Business Address
                                </div>
                            </div>
                        </div>
                        <div id="divBillingAddress" class="row non-agent-form">
                            <div class="clearfix"></div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="txtBillingAddress1">Billing Address 1:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBillingAddress1" id="txtBillingAddress1"
                                        value="" placeholder="Enter Billing Address 1" />
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="txtBillingAddress2">Billing Address 2:</label>
                                    <input type="text" class="form-control" name="txtBillingAddress2" id="txtBillingAddress2"
                                        value="" placeholder="Enter Billing Address 2" />
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="txtBillingCountry">Country:<span class="required"></span></label>
                                    <select name="txtBillingCountry" id="txtBillingCountry" class="form-control">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}" data-code="{{$country->iso_code_2}}">{{$country->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="txtBillingZip">Zip:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBillingZip" id="txtBillingZip"
                                        value="" placeholder="Zip" onkeypress="return isNumberKey(event)" onblur="isValidZip(this, 'txtBillingCity', 'txtBillingState')"/>
                                        <span id="txtBillingZip-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="txtBillingState">State:<span class="required"></span></label>
                                    <select name="txtBillingState" id="txtBillingState" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="txtBillingCity">City:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtBillingCity" id="txtBillingCity"
                                        value="" placeholder="Enter City" />
                                </div>
                            </div>

                        </div>

                        <div class="row non-agent-form">
                            <div class="row-header">
                                <h3 class="title pull-left">Mailing Address</h3>
                                <div class="pull-right">
                                    <input type="checkbox" name="chkSameAsBusiness" id="chkSameAsBusiness"> Same as
                                    Business Address
                                </div>
                            </div>
                        </div>
                        <div id="divMailingAddress" class="row non-agent-form">
                            <div class="clearfix"></div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="txtMailingAddress1">Mailing Address 1:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtMailingAddress1" id="txtMailingAddress1"
                                        value="" placeholder="Enter Mailing Address 1" />
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="txtMailingAddress2">Mailing Address 2:</label>
                                    <input type="text" class="form-control" name="txtMailingAddress2" id="txtMailingAddress2"
                                        value="" placeholder="Enter Mailing Address 2" />
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="txtMailingCountry">Country:<span class="required"></span></label>
                                    <select name="txtMailingCountry" id="txtMailingCountry" class="form-control">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}" data-code="{{$country->iso_code_2}}">{{$country->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="txtMailingZip">Zip:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtMailingZip" id="txtMailingZip"
                                        value="" placeholder="Zip" onkeypress="return isNumberKey(event)" onblur="isValidZip(this, 'txtMailingCity', 'txtMailingState')"/>
                                        <span id="txtMailingZip-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="txtMailingState">State:<span class="required"></span></label>
                                    <select name="txtMailingState" id="txtMailingState" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="txtMailingCity">City:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtMailingCity" id="txtMailingCity"
                                        value="" placeholder="Enter City" />
                                </div>
                            </div>

                        </div>

                        <div class="row non-agent-form">
                            <div class="row-header">
                                <h3 class="title">Company Contact Information</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-5 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Business Phone 1:<span class="required">*</span></label>
                                    <div class="input-group">
                                        <label for="businessPhone" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtBusinessPhone1" id="txtBusinessPhone1"
                                            value="" placeholder="Enter Business Phone 1" />
                                    </div>
                                    <span id="txtBusinessPhone1-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="extension1">Extension:</label>
                                    <input type="text" class="form-control" name="txtExtension1" id="txtExtension1"
                                        value="" placeholder="Ext" />
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="fax">Fax:</label>
                                    <input type="text" class="form-control number-only" name="txtFax" id="txtFax" value=""
                                        placeholder="Enter Fax" />
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Business Phone 2:</label>
                                    <div class="input-group">
                                        <label for="businessPhone" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtBusinessPhone2" id="txtBusinessPhone2"
                                            value="" placeholder="Enter Business Phone 2" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="extension2">Extension:</label>
                                    <input type="text" class="form-control" name="txtExtension2" id="txtExtension2"
                                        value="" placeholder="Ext" />
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="txtEmail" title="This will be used for sending user credentials upon registration.">Email(must
                                        be valid):<span class="required"></span></label>
                                    <!-- <input type="text" class="form-control" name="txtEmail" id="txtEmail" value="" placeholder="Enter Email" onblur="validateData('users','email_address',this,'-1','true','reference_', 'Email address already been used by other users'); validateData('partner_companies','email',this,'-1','false','partner_', 'Email address already been used by other partners');" onchange="validateEmail(this.id);" title="This will be used for sending user credentials upon registration."/> -->
                                    <input type="text" class="form-control" name="txtEmail" id="txtEmail" value=""
                                        placeholder="Enter Email" onblur="validateData('users','email_address',this,'-1','true','empty', 'Email address already been used by other users', 'bi-tab'); validateData('partner_companies','email',this,'-1','false','empty', 'Email address already been used by other partners', 'bi-tab');"
                                        title="This will be used for sending user credentials upon registration." />
                                    <span id="txtEmail-error" style="color:red;"><small></small></span>
                                    <span class="error" style="color:orange;"><small>Note: If left blank, Partner must
                                            have at least the Contact Person's Mobile Number.</small></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group pull-right">
                            @if($canSaveAsDraft)
                                @include('incs.saveAsDraft')
                            @endif
                            <a href="#" class="btn btn-primary btnNext">Next</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane" id="contact-person">
                        <div class="row non-agent-form">
                            <div class="row-header">
                                <h3 class="title"><strong>Contact 1</strong></h3>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>First Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtContactFirstName1" id="txtContactFirstName1"
                                        value="" placeholder="Enter First Name">
                                    <span id="txtContactFirstName1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Middle Initial:</label>
                                    <input type="text" class="form-control" name="txtContactMiddleInitial1" id="txtContactMiddleInitial1"
                                        value="" placeholder="MI" maxlength="1">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Last Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtContactLastName1" id="txtContactLastName1"
                                        value="" placeholder="Enter Last Name">
                                    <span id="txtContactLastName1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Title:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactTitle1" id="txtContactTitle1"
                                        value="" placeholder="Enter Title">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>SSN:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactSSN1" id="txtContactSSN1"
                                        value="" placeholder="Enter SSN" onblur="validateData('partner_contacts','ssn',this,'-1','false','empty', 'SSN already been used by other contacts');">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Percentage of Ownership:</label>
                                    <input type="text" class="form-control" name="txtOwnershipPercentage1" id="txtOwnershipPercentage1"
                                        value="0" placeholder="0" onkeypress="return isNumberKey(event)">
                                    <span id="txtOwnershipPercentage1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Date of Birth:</label>
                                    <input type="text" class="form-control" name="txtContactDOB1" id="txtContactDOB1"
                                        value="" placeholder="MM/DD/YYYY">
                                    <span id="txtContactDOB1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                        </div>
                        <div class="row non-agent-form">
                            <div class="row-header">
                                <h3 class="title">Home Address</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="">Home Address 1:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactHomeAddress1_1" id="txtContactHomeAddress1_1"
                                        value="" placeholder="Enter Home Address 1" />
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="">Home Address 2:</label>
                                    <input type="text" class="form-control" name="txtContactHomeAddress1_2" id="txtContactHomeAddress1_2"
                                        value="" placeholder="Enter Home Address 2" />
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="country">Country:<span class="required"></span></label>
                                    <select name="txtContactCountry1" id="txtContactCountry1" class="form-control">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}" data-code="{{$country->iso_code_2}}">{{$country->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                           <div class="col-lg-1 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Zip:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactZip1" id="txtContactZip1"
                                        value="" placeholder="Zip" onkeypress="return isNumberKey(event)" onblur="isValidZip(this, 'txtContactCity1', 'txtContactState1')"/>
                                        <span id="txtContactZip1-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">State:<span class="required"></span></label>
                                    <select name="txtContactState1" id="txtContactState1" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="city">City:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactCity1" id="txtContactCity1"
                                        value="" placeholder="Enter City" />
                                </div>
                            </div>
 
                        </div>
                        <div class="row non-agent-form">
                            <div class="row-header">
                                <h3 class="title">Personal Contact Information</h3>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>Phone 1:<span class="required"></span></label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactPhone1_1" id="txtContactPhone1_1"
                                            value="" placeholder="Enter Phone 1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone 2:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactPhone1_2" id="txtContactPhone1_2"
                                            value="" placeholder="Enter Phone 2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label title="This will be used for verification.">Mobile <small>(must be valid)</small>:<span
                                            class="required" id="mobileNumber"></span></label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactMobile1_1" id="txtContactMobile1_1"
                                            value="" placeholder="Enter Mobile 1" onblur="validateData('partner_contacts','mobile_number',this,'-1','false','empty', 'Mobile number already been used by other partners', 'bi-tab'); validateData('users','mobile_number',this,'-1','false','empty', 'Mobile number already been used by other users', 'cp-tab');"
                                            title="This will be used for verification.">
                                    </div>
                                    <span id="txtContactMobile1_1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile 2:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactMobile1_2" id="txtContactMobile1_2"
                                            value="" placeholder="Enter Mobile 2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fax:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactFax1" id="txtContactFax1"
                                            value="" placeholder="Enter Fax">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <small></small>:<span class="required"></span></label>
                                    <!-- <input type="text" class="form-control" name="txtContactEmail1" id="txtContactEmail1" value="" placeholder="Enter Email Address" onblur="validateData('partner_contacts','email',this,'-1','false','empty', 'Email address already been used by other contacts');" onchange="validateEmail(this.id);" > -->
                                    <input type="text" class="form-control" name="txtContactEmail1" id="txtContactEmail1"
                                        value="" placeholder="Enter Email Address" onblur="validateData('partner_contacts','email',this,'-1','false','empty', 'Email address already been used by other contacts', 'cp-tab');">
                                    <span id="txtContactEmail1-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="row">
                            <div class="row-header">
                                <h3 class="title"><strong>Contact 2</strong></h3>
                            </div>
                            
                            <div class="col-lg-2 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>First Name:</label>
                                    <input type="text" class="form-control" name="txtContactFirstName2" id="txtContactFirstName2" value="" placeholder="First Name">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Middle Initial:</label>
                                    <input type="text" class="form-control" name="txtContactMiddleInitial2" id="txtContactMiddleInitial2" value="" placeholder="MI" maxlength="1">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Last Name:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactLastName2" id="txtContactLastName2" value="" placeholder="Enter Last Name">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Title:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactTitle2" id="txtContactTitle2" value="" placeholder="Enter Title">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>SSN:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactSSN2" id="txtContactSSN2" value="" placeholder="Enter SSN">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Percentage of Ownership:</label>
                                    <input type="text" class="form-control" name="txtOwnershipPercentage2" id="txtOwnershipPercentage2" value="0" placeholder="0" onkeypress="return isNumberKey(event)">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Date of Birth:</label>
                                    <input type="text" class="form-control" name="txtContactDOB2" id="txtContactDOB2" value="" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Home Address</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="">Home Address 1:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactHomeAddress2_1" id="txtContactHomeAddress2_1" value="" placeholder="Enter Home Address 1"/>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="">Home Address 2:</label>
                                    <input type="text" class="form-control" name="txtContactHomeAddress2_2" id="txtContactHomeAddress2_2" value="" placeholder="Enter Home Address 2"/>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="city">City:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactCity2" id="txtContactCity2" value="" placeholder="Enter City"/>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">State:<span class="required"></span></label>
                                    <select name="txtContactState2" id="txtContactState2" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Zip:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactZip2" id="txtContactZip2" value="" placeholder="Zip" onkeypress="return isNumberKey(event)"/>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="country">Country:<span class="required"></span></label>
                                    <select name="txtContactCountry" id="txtContactCountry" class="form-control">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}" data-code="{{$country->iso_code_2}}">{{$country->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Personal Contact Information</h3>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone 1:<span class="required"></span></label>
                                    <div class="input-group">
                                        <label for="contactPhone2" class="input-group-addon">1</label>
                                        <input type="text" class="form-control" name="txtContactPhone2_1" id="txtContactPhone2_1" value="" placeholder="Enter Phone 1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone 2:<span class="required"></span></label>
                                    <div class="input-group">
                                        <label for="contactPhone2" class="input-group-addon">1</label>
                                        <input type="text" class="form-control" name="txtContactPhone2_2" id="txtContactPhone2_2" value="" placeholder="Enter Phone 2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile <small>(must be valid)</small>:<span class="required"></span></label>
                                    <div class="input-group">
                                        <label for="contactPhone2" class="input-group-addon">1</label>
                                        <input type="text" class="form-control" name="txtContactMobile2_1" id="txtContactMobile2_1" value="" placeholder="Enter Mobile 1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile 2:</label>
                                    <div class="input-group">
                                        <label for="contactPhone2" class="input-group-addon">1</label>
                                        <input type="text" class="form-control" name="txtContactMobile2_2" id="txtContactMobile2_2" value="" placeholder="Enter Mobile 2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fax:<span class="required"></span></label>
                                    <div class="input-group">
                                        <label for="contactPhone2" class="input-group-addon">1</label>
                                        <input type="text" class="form-control" name="txtContactFax2" id="txtContactFax2" value="" placeholder="Enter Fax">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <small></small>:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactEmail2" id="txtContactEmail2" value="" placeholder="Enter Email" onblur="validateData('partner_contacts','email',this,'-1','false','empty', 'Email address already been used by other contacts');">
                                </div>
                            </div>
                        </div> -->

                        <!-- New Agent Creation Form -->
                        <div class="row agent-form">
                            <div class="row-header">
                                <h3 class="title"><strong>Contact 1</strong></h3>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                <div class="form-group">
                                    <label>Contact First Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtContactFirstNameAgent" id="txtContactFirstNameAgent"
                                        value="" placeholder="Enter First Name">
                                    <span id="txtContactFirstNameAgent-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-12 sm-col">
                                <div class="form-group">
                                    <label>Contact Middle Initial:</label>
                                    <input type="text" class="form-control" name="txtContactMiddleInitialAgent" id="txtContactMiddleInitialAgent"
                                        value="" placeholder="MI" maxlength="1">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 sm-col">
                                <div class="form-group">
                                    <label>Contact Last Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtContactLastNameAgent" id="txtContactLastNameAgent"
                                        value="" placeholder="Enter Last Name">
                                    <span id="txtContactLastNameAgent-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                        </div>
                        <div class="row agent-form">
                            <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                <div class="form-group">
                                    <label title="This will be used for verification.">Mobile Number <small>(must be valid)</small>:<span
                                            class="required" id="mobileNumber"></span></label>
                                    <div class="input-group">
                                        <label for="ContactPhone" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactMobileNumberAgent" id="txtContactMobileNumberAgent"
                                            value="" placeholder="Enter Mobile 1" onblur="validateData('partner_contacts','mobile_number',this,'-1','false','empty', 'Mobile number already been used by other partners', 'bi-tab'); validateData('users','mobile_number',this,'-1','false','empty', 'Mobile number already been used by other users', 'cp-tab');"
                                            title="This will be used for verification.">
                                    </div>
                                    <span id="txtContactMobileNumberAgent-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 sm-col">
                                <div class="form-group">
                                    <label>Social Security Number: </label>
                                    <input type="text" class="form-control" id="txtSSNAgent" value=""
                                        name="txtSSNAgent" placeholder="Enter Social Security Number">
                                </div>
                            </div>
                        </div>
                        <div id="addBtnContact">
                        </div>
                        <div class="form-group pull-right">
                            <button class="btn btn-primary" type="button" onclick="addContact();">Add Contact</button>
                        </div>
                        <div class="clearfix">
                        </div>
                        <!-- End of New Agent Creation Form -->

                        <div class="form-group pull-right">
                            @if($canSaveAsDraft)
                                @include('incs.saveAsDraft')
                            @endif
                            <a href="#" class="btn btn-primary btnPrevious">Prev</a>
                            <a href="#" class="btn btn-primary btnNext">Next</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane" id="attachments">
                        <p><small>(Only .pdf, .png and .jpeg image are accepted. File size should not exceed 2mb)</small></p>
                        <div class="row">
                            @if(count($documents)>0)
                            @forelse($documents as $document)
                            <div class="col-lg-3 col-md-6 col-sm-12 sm-col">
                                <div class="form-group">
                                    <label>{{$document->name}}:</label>
                                    <input type="file" id="fileUpload{{$document->id}}" name="fileUpload{{$document->id}}"
                                        accept="image/x-png,image/jpeg,application/pdf">
                                </div>
                                
                                <button class="btn btn-sm btn-danger clear-input" data-file_id="fileUpload{{ $document->id}}">Clear Input</butto>
                            </div>
                            @endforeach
                            <!-- <div class="col-lg-3 col-md-6 col-sm-12 sm-col">
                                    <div class="form-group">
                                        <label>OTHERS:</label>
                                        <input type="file" id="fileUploadOthers" name="fileUploadOthers" accept="image/x-png,image/jpeg">
                                    </div>
                                     <div class="form-group">
                                        <label>Enter Description:</label>
                                        <input type="text" id="OthersDescription" name="OthersDescription" accept="image/x-png,image/jpeg">
                                    </div>
                                </div> -->
                            @endif
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

                        <div class="form-group pull-right">
                            <br />
                            @if($canSaveAsDraft)
                                @include('incs.saveAsDraft')
                            @endif
                            <a href="#" class="btn btn-primary btnPrevious">Prev</a>
                            <a href="#" class="btn btn-primary btnNext" id="btnCreatePartner" name="btnCreatePartner">Next</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="tab-pane" id="preview">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row-header">
                                    <h3 class="title">Partner Registration Preview</h3>
                                </div>
                            </div>
                        </div>
                        <!-- Agent Preview -->
                        <div class="row agent-form">
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
                                        <th colspan="1">Partner Type:</th>
                                        <td colspan="11"><i id="groupType_preview_agent" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Parent:</th>
                                        <td colspan="11"><i id="parent_preview_agent" class="view"></i></td>
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
                                        <th colspan="1">Tax ID Number:</th>
                                        <td colspan="11"><i id="txtTaxIdNumber_preview" class="view"></i></td>
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
                                        <td colspan="11"><i id="txtAddressAgent_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Country:</th>
                                        <td colspan="2"><i id="txtCountryAgent_preview" class="view"></i></td>
                                        <th colspan="1">State:</th>
                                        <td colspan="2"><i id="txtStateAgent_preview" class="view"></i></td>
                                        <th colspan="1">City:</th>
                                        <td colspan="2"><i id="txtCityAgent_preview" class="view"></i></td>
                                        <th colspan="1">Zip:</th>
                                        <td colspan="2"><i id="txtZipAgent_preview" class="view"></i></td>
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
                                        <td colspan="5"><i id="txtEmailNotifier_preview" class="view"></i></td>
                                        <th colspan="1">Email:</th>
                                        <td colspan="5"><i id="txtEmailAgent_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="12" class="form-category">Billing Information</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Billing Cycle:</th>
                                        <td colspan="3"><i id="txtBillingCycle_preview" class="view"></i>
                                        <th colspan="1">Month:</th>
                                        <td colspan="3"><i id="txtBillingMonth_preview" class="view"></i>
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
                                    <tr id="first-contact-person-agent">
                                        <td><i class="view">1</i></td>
                                        <td><i id="txtContactFirstNameAgent_preview" class="view"></i></td>
                                        <td><i id="txtContactMiddleInitialAgent_preview" class="view"></i></td>
                                        <td><i id="txtContactLastNameAgent_preview" class="view"></i></td>
                                        <td><i id="txtContactMobileNumberAgent_preview" class="view"></i></td>
                                        <td><i id="txtSSNAgent_preview" class="view"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- End Agent Preview -->
                        <!-- Non-Agent Preview -->
                        <div class="row non-agent-form">
                            <table style="width:75%;">
                                <thead>
                                    <tr>
                                        <th colspan="12">BUSINESS INFORMATION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th colspan="12"  class="form-category">Company Information</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Partner Type:</th>
                                        <td colspan="5"><i id="groupType_preview_non_agent" class="view"></i></td>
                                        <th colspan="1">Parent:</th>
                                        <td colspan="5"><i id="parent_preview_non_agent" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Ownership:</th>
                                        <td colspan="5"><i id="txtOwnership_preview" class="view"></i></td>
                                        <th colspan="1">DBA:</th>
                                        <td colspan="5"><i id="txtCompanyName_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">ISO/Affiliate Name <br> (Legal Name/Business Name):</th>
                                        <td colspan="11"><i id="txtDBA_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Date when business was opened:</th>
                                        <td colspan="11"><i id="txtBusinessDate_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Credit Card Reference ID:</th>
                                        <td colspan="11"><i id="txtCreditCardReference_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Website:</th>
                                        <td colspan="11"><i id="txtWebsite_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="12"  class="form-category">Business Address</th>
                                    </tr>

                                    <tr>
                                        <th colspan="1">Business Address 1:</th>
                                        <td colspan="11"><i id="txtBusinessAddress1_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Business Address 2:</th>
                                        <td colspan="11"><i id="txtBusinessAddress2_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Country:</th>
                                        <td colspan="5"><i id="txtCountry_preview" class="view"></i></td>
                                        <th colspan="1">State:</th>
                                        <td colspan="5"><i id="txtState_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">City:</th>
                                        <td colspan="5"><i id="txtCity_preview" class="view"></i>
                                        <th colspan="1">Zip:</th>
                                        <td colspan="5"><i id="txtBusinessZip_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="12"  class="form-category">Billing Address
                                        <span class="pull-right"><input type="checkbox" id="chkSameAsBusinessBilling_preview" disabled="disabled">&nbsp;<small>Same as Business Address</small></span></th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Biliing Address 1:</th>
                                        <td colspan="11"><i id="txtBillingAddress1_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Billing Address 2:</th>
                                        <td colspan="12"><i id="txtBillingAddress2_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Country:</th>
                                        <td colspan="5"><i id="txtBillingCountry_preview" class="view"></i>
                                        <th colspan="1">State:</th>
                                        <td colspan="5"><i id="txtBillingState_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">City:</th>
                                        <td colspan="5"><i id="txtBillingCity_preview" class="view"></i>
                                        <th colspan="1">Zip:</th>
                                        <td colspan="5"><i id="txtBillingZip_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="12"  class="form-category">Mailing Address
                                        <span class="pull-right"><input type="checkbox" id="chkSameAsBusiness_preview" disabled="disabled">&nbsp;<small>Same as Business Address</small></span></th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Mailing Address 1:</th>
                                        <td colspan="11"><i id="txtMailingAddress1_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Mailing Address 2:</th>
                                        <td colspan="11"><i id="txtMailingAddress2_preview" class="view"></i></td>
                                    </tr>
                                    <tr>

                                        <th colspan="1">Country:</th>
                                        <td colspan="5"><i id="txtMailingCountry_preview" class="view"></i>
                                        <th colspan="1">State:</th>
                                        <td colspan="5"><i id="txtMailingState_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">City:</th>
                                        <td colspan="5"><i id="txtMailingCity_preview" class="view"></i>
                                        <th colspan="1">Zip:</th>
                                        <td colspan="5"><i id="txtMailingZip_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="12"  class="form-category">Business Contact</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Business Phone 1:</th>
                                        <td colspan="5"><i id="txtBusinessPhone1_preview" class="view"></i>
                                        <th colspan="1">Extension:</th>
                                        <td colspan="5"><i id="txtExtension1_preview" class="view"></i></td>
                                    </tr> 
                                    <tr>
                                        <th colspan="1">Business Phone 2:</th>
                                        <td colspan="5"><i id="txtBusinessPhone2_preview" class="view"></i>
                                        <th colspan="1">Extension:</th>
                                        <td colspan="5"><i id="txtExtension2_preview" class="view"></i></td>
                                    </tr> 
                                    <tr>
                                        <th colspan="1">Fax:</th>
                                        <td colspan="5"><i id="txtFax_preview" class="view"></i></td>
                                        <th colspan="1">Email:</th>
                                        <td colspan="5"><i id="txtEmail_preview" class="view"></i></td>
                                    </tr> 
                                </tbody>
                            </table>
                            <table style="width:75%;">
                                <thead style="">
                                    <tr>
                                        <th colspan="12">CONTACT PERSONS</th>
                                    </tr>
                                </thead>
                                <tbody id="non-agent-contact-table">
                                    <tr>
                                        <th colspan="12" class="form-category">Contact 1</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">First Name:</th>
                                        <td colspan="3"><i id="txtContactFirstName1_preview" class="view"></i>
                                        <th colspan="1">Middle Initial:</th>
                                        <td colspan="3"><i id="txtContactMiddleInitial1_preview" class="view"></i>
                                        <th colspan="1">Last Name:</th>
                                        <td colspan="3"><i id="txtContactLastName1_preview" class="view"></i></td>
                                    </tr> 
                                    <tr>
                                        <th colspan="1">Title:</th>
                                        <td colspan="5"><i id="txtContactTitle1_preview" class="view"></i>
                                        <th colspan="1">SSN:</th>
                                        <td colspan="5"><i id="txtContactSSN1_preview" class="view"></i>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Percentage of Ownership:</th>
                                        <td colspan="5"><i id="txtOwnershipPercentage1_preview" class="view"></i>
                                        <th colspan="1">Date of Birth:</th>
                                        <td colspan="5"><i id="txtContactDOB1_preview" class="view"></i></td>
                                    </tr> 
                                    <tr>
                                        <th colspan="12" class="form-category">Home Address</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Home Address 1:</th>
                                        <td colspan="11"><i id="txtContactHomeAddress1_1_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Home Address 2:</th>
                                        <td colspan="11"><i id="txtContactHomeAddress1_2_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Country:</th>
                                        <td colspan="5"><i id="txtContactCountry1_preview" class="view"></i>
                                        <th colspan="1">State:</th>
                                        <td colspan="5"><i id="txtContactState1_preview" class="view"></i>
                                    </tr>
                                    <tr>
                                        <th colspan="1">City:</th>
                                        <td colspan="5"><i id="txtContactCity1_preview" class="view"></i>
                                        <th colspan="1">Zip:</th>
                                        <td colspan="5"><i id="txtContactZip1_preview" class="view"></i></td>
                                    </tr>
                                    <tr>
                                        <th colspan="12" class="form-category">Personal Contact Information</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Phone 1:</th>
                                        <td colspan="5"><i id="txtContactPhone1_1_preview" class="view"></i>
                                        <th colspan="1">Phone 2:</th>
                                        <td colspan="5"><i id="txtContactPhone1_2_preview" class="view"></i></td>
                                    </tr> 
                                    <tr>
                                        <th colspan="1">Mobile:</th>
                                        <td colspan="5"><i id="txtContactMobile1_1_preview" class="view"></i>
                                        <th colspan="1">Mobile 2:</th>
                                        <td colspan="5"><i id="txtContactMobile1_2_preview" class="view"></i></td>
                                    </tr> 
                                    <tr>
                                        <th colspan="1">Fax:</th>
                                        <td colspan="5"><i id="txtContactFax1_preview" class="view"></i>
                                        <th colspan="1">Email:</th>
                                        <td colspan="5"><i id="txtContactEmail1_preview" class="view"></i></td>
                                    </tr> 
                                </tbody>
                            </table>
                        </div>
                        <!-- End Non-Agent Preview -->
                        <div class="form-group pull-right">
                            <br />
                            @if($canSaveAsDraft)
                                @include('incs.saveAsDraft')
                            @endif
                            <a href="#" class="btn btn-primary btnPrevious">Prev</a>
                            <button type="submit" class="pull-right btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <!-- /.content -->

    <input type="hidden" name="system-user" value="{{ $systemUser }}" />
</div>
@endsection

@section("script")
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ config(' app.cdn ') . '/js/partners/partner.js' . '?v=' . config(' app.version ') }}"></script>
@if (!$systemUser)
<script>
    $('#assigntome').prop('checked', false);
</script>
@endif

<script src="{{ config(' app.cdn ') . '/js/clearInput.js' . '?v=' . config(' app.version ') }}"></script>
@endsection