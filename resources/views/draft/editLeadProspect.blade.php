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
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                @if($draft->partner_type_id == 6)
                Create Leads
                @elseif($draft->partner_type_id == 8)
                Create Prospects
                @endif
                <!-- <small>Dito tayo magpapasok                            <div class="row-header">
                                <h3 class="title">Personal Information</h3>
                            </div> ng different pages</small> -->
                <div class="alert alert-success hide">
                    <p id="msg-success"></p>
                </div>
                <div class="alert alert-danger hide">
                    <p id="msg-danger"></p>
                </div>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                @if($draft->partner_type_id == 6)
                <li><a href="/leads">Leads </a></li>
                <li class="active">Add New Leads </li>
                @elseif($draft->partner_type_id == 8)
                <li><a href="/prospects">Prospects </a></li>
                <li class="active">Add New Prospects </li>
                @endif
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <!--Progress Wizard-->
            <div class="row">
                <div class="col-md-12">
                    <ul class="progressbar nav">
                        <li class="col-sm-4 bi-tab list-tab active">
                            <a href="#business-info" id="bi-tab" data-toggle="tab" aria-expanded="true">
                                Business Information
                            </a>
                        </li>
                        <li class="col-sm-4 cp-tab list-tab">
                            <a href="#contact-person" id="cp-tab" data-toggle="tab" aria-expanded="false">
                                Contact Persons
                            </a>
                        </li>
                        <li class="col-sm-4 ip-tab list-tab">
                            <a href="#interested-product" id="ip-tab" data-toggle="tab" aria-expanded="false">
                                Interested Products
                            </a>
                        </li>
                    </ul>
                </div>
            </div><!--/Progress Wizard-->

            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle hide">
                    <li class="active"><a href="#business-info" id="bi-tab" data-toggle="tab" aria-expanded="true">Business Information</a></li>
                    <li class=""><a href="#contact-person" id="cp-tab" data-toggle="tab" aria-expanded="false">Contact Persons</a></li>
                    <li class=""><a href="#interested-product" id="ip-tab" data-toggle="tab" aria-expanded="false">Interested Product</a></li>
                </ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="business-info">
                        <form role="form" name="frmAddLead" id="frmAddLead">
                            <input type="hidden" id="txtDraftPartnerId" name="txtDraftPartnerId" value="{{ $draft->id }}">
                            <input type="hidden"  name="txtPartnerTypeId" id="txtPartnerTypeId" value="{{ $draft->partner_type_id }}">
                            <input type="hidden" id="txtDraftParent" name="txtDraftParent" value="{{ $draft->parent_id }}">
                            <input type="hidden" id="txtDraftParentType" name="txtDraftParentType" value="{{ $parentType }}">
                            <div class="row">
                                <div class="row-header">
                                    <h3 class="title">Company Information</h3>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-4 col-md-6 col-sm-12 hide">
                                    <div class="form-group">
                                        <label for="partnerType">Partner Type:</label>
                                        <select name="partnerType" id="partnerType" class="form-control">
                                            @foreach ($partner_type as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 {{ $systemUser ? '' : 'hidden' }}">
                                    <div class="form-group">
                                        <br/>
                                        <input type="checkbox" name="assignToMe" id="assignToMe" value="" {{ $draft->parent_id == auth()->user()->reference_id ? 'checked' : ''}}/>
                                        <label for="assignToMe">
                                            Set Parent as 
                                            @if (auth()->user()->is_original_partner != 1)
                                                {{ auth()->user()->first_name . ' ' . auth()->user()->last_name}}
                                                <span style="color:rgb(255, 165, 0)">({{ $userDepartment }})<span>
                                            @else
                                                {{ session('company_name') }}
                                            @endif
                                        </label>
                                    <input type="hidden" name="selfAssign" id="selfAssign">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 assignToDiv" {{ $systemUser ? '' : 'style="display:none"' }} >
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="assignTo">Parent:</label>
                                                <select name="assignTo" id="assignTo" class="form-control">
                                                    @foreach ($upline_partner_type as $item)
                                                        <option value="{{ $item->id }}" @if($parentType == $item->id) selected="selected" @endif>{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="assignee">&nbsp;</label>
                                                <select name="assignee" id="assignee" class="form-control select2" style="width:100%">
                                                    @if(isset($upline))
                                                        @foreach ($upline_partner_type as $item)
                                                            <option data-image="{{ $item->image }}" value="{{ $item->id }}" @if($draft->parent_id == $item.parent_id) selected="selected" @endif>{{ $item->upline_partner }} - {{ $item.partner_id_reference }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="currentProfessor">Current Processor:</label>
                                        <input type="text" class="form-control" name="currentProcessor" id="currentProccessor" value="{{ $draft->merchant_processor }}" placeholder="Enter Current Processor"/>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="legalName">DBA:<span class="required">*</span></label>
                                        <input type="text" class="form-control" name="legalName" id="legalName" value="{{ $draft->company_name }}" placeholder="Enter DBA"/>
                                        <span id="legalName-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="ownership">Ownership:<span class="required"></span></label>
                                        <select class="form-control" name="ownership" id="ownership">
                                            @foreach($ownerships as $item)
                                                <option value="{{ $item->code }}" {{$draft->ownership == $item->code ? "selected" : "" }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="ownership">Legal Name (Business Name):<span class="required"></span></label>
                                        <input type="text" class="form-control" name="dba" id="dba" value="{{ $draft->company_name }}" placeholder="Enter Legal Name"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                              <div class="form-group col-md-5 pr-0">
                                <label for="business_industry">Business Industry<span class="required"></span></label>
                                <select name="business_industry" id="business_industry" class="form-control select2">
                                  @foreach ($businessTypeGroups as $groupName => $businessTypes)
                                    <optgroup label="{{ $groupName }}">
                                      @foreach ($businessTypes as $businessType)
                                        <option value="{{ $businessType->mcc }}" {{ $draft->business_type_code == $businessType->mcc ? 'selected' : '' }}>
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
                                <div class="row-header">
                                    <h3 class="title">Business Address</h3>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="businessAddress1">Business Address 1:<span class="required">*</span></label>
                                        <input type="text" class="form-control" name="businessAddress1" id="businessAddress1" value="{{ $draft->business_address1 }}" placeholder="Enter Address"/>
                                        <span id="businessAddress1-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="businessAddress2">Business Address 2:</label>
                                        <input type="text" class="form-control" name="businessAddress2" id="businessAddress2" value="{{ $draft->business_address2 }}" placeholder="Enter Address"/>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="country">Country:<span class="required"></span></label>
                                        <select name="country" id="country" class="form-control s2-country">
                                            @foreach($countries as $item)
                                                <option value="{{ $item->name }}" data-code="{{ $item->iso_code_2 }}" {{ $draft->business_country == $item->name ? "selected" : "" }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="zip">Zip:<span class="required"></span></label>
                                        <input type="text" class="form-control" name="zip" id="zip" value="{{ $draft->business_zip }}" placeholder="Zip"/>
                                        <span id="zip-error" style="color:red;"><small></small></span>
                                        @include('incs.zipHelpNote')
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">

                                        <div id="state_us" >
                                            <label for="state">State:<span class="required"></span></label>
                                            <select class="form-control s2-state" style="width: 100%;" id="txtState" name="txtState" disabled>
                                                @foreach($states as $item)
                                                <option value="{{ $item->code }}" data-code="{{ $item->id }}" {{$draft->business_state == $item->code ? "selected" : "" }}>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="state_ph" style="display:none;">
                                            <label for="state">State:<span class="req"></span></label>
                                            <select class="form-control s2-state" style="width: 100%;" id="txtStatePH" name="txtStatePH" disabled>
                                                @if(isset($statePH))
                                                    @foreach($statePH as $item)
                                                        <option value="{{ $item->code }}" data-code="{{ $item->id }}" {{$draft->business_state == $item->code ? "selected" : "" }}>{{ $item->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div id="state_cn" style="display:none;">
                                            <label for="state">State:<span class="req"></span></label>
                                            <select class="form-control s2-state" style="width: 100%;" id="txtStateCN" name="txtStateCN" disabled>
                                                @if(isset($stateCN))
                                                    @foreach($stateCN as $item)
                                                        <option value="{{ $item->code }}" data-code="{{ $item->id }}" {{$draft->business_state == $item->code ? "selected" : "" }}>{{ $item->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="city">City:<span class="required"></span></label>
                                        {{-- <input type="text" class="form-control" name="city" id="city" value="{{ $draft->business_city }}" placeholder="Enter City"/> --}}
                                        <select name="city" id="city" class="form-control select2" disabled>
                                            @foreach ($initialCities as $c)
                                                <option value="{{ $c->city }}">{{ $c->city }}</option>
                                            @endforeach
                                        </select>
                                        <span id="city-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="row-header">
                                    <h3 class="title">Company Contact Information</h3>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-5 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <!-- <label for="businessPhone1">Business Phone 1:<span class="required">*</span></label>
                                        <input type="text" class="form-control" name="businessPhone1" id="businessPhone1" value="" placeholder="Enter Business Phone 1"/> -->
                                        <label>Business Phone 1:<span class="required"></span></label>
                                        <div class="input-group ">
                                            <div class="input-group-addon"><label for="businessPhone1">1</label></div>
                                            <input type="text" class="form-control number-only" id="businessPhone1" name="businessPhone1" value="{{ $draft->nd_phone1 }}" placeholder="Enter Business Phone 1"  onblur="validateData('partner_companies','phone1',this,'-1','false','empty', 'Business Phone 1 has already been used.','bi-tab');">
                                        </div>
                                        <span id="businessPhone1-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="extension1">Extension:</label>
                                        <input type="text" class="form-control" name="extension1" id="extension1" value="{{ $draft->extension }}" placeholder="Ext"/>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <!-- <label for="fax">Fax:</label>
                                        <input type="text" class="form-control" name="fax" id="fax" value="" placeholder="Enter Fax"/> -->
                                        <label>Fax:</label>
                                        <div class="input-group ">
                                            <div class="input-group-addon"><label for="businessPhone1">1</label></div>
                                            <input type="text" class="form-control number-only" id="fax" value="{{ $draft->partner_fax }}" name="fax" placeholder="Enter Fax">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <!-- <label for="businessPhone2">Business Phone 2:</label> -->
                                        <!-- <input type="text" class="form-control" name="businessPhone2" id="businessPhone2" value="" placeholder="Enter Business Phone 2"/> -->
                                        <label>Business Phone 2:</label>
                                        <div class="input-group ">
                                            <div class="input-group-addon"><label for="businessPhone2">1</label></div>
                                            <input type="text" class="form-control number-only" id="businessPhone2" name="businessPhone2" value="{{ $draft->nd_phone2 }}" placeholder="Enter Business Phone 2">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="extension2">Extension:</label>
                                        <input type="text" class="form-control" name="extension2" id="extension2" value="{{ $draft->extension_2 }}" placeholder="Ext"/>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="email">Email(must be valid):<span class="required"></span></label>
                                        <input type="text" class="form-control" @if($draft->partner_type_id == 6)name="txtEmailLead" id="txtEmailLead"@elseif($draft->partner_type_id == 8)name="txtEmailPros" id="txtEmailPros"@endif value="{{ $draft->partner_email }}" placeholder="Enter Email" onblur="validateData('partner_contacts','email',this,'-1','false','empty', 'Email address already been used by other contacts','bi-tab'); validateData('users','email_address',this,'-1','false','empty', 'Email address already been used by other users','bi-tab');"/>
                                        <span id="txtEmailPros-error" style="color:red;"><small></small></span>
                                        <span class="error" style="color:orange;"><small>Note: If left blank, Lead must have at least the Contact Person's Mobile Number.</small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pull-right">
                                @if($canSaveAsDraft)
                                    @include('incs.saveAsDraft')
                                @endif
                                <a href="#cp-tab" class="btn btn-primary btnNext" data-toggle="tab">Next</a>
                            </div>
                            <div class="clearfix"></div>
                    </div>
                    <div class="tab-pane" id="contact-person">
                        @foreach($draft->draftPartnerContacts as $key => $contact)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="fname" id="fname" value="{{ $contact->first_name }}" placeholder="Enter First Name">
                                    <span id="fname-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mname">Middle Initial:</label>
                                    <input type="text" class="form-control" name="mname" id="mname" value="{{ $contact->middle_name }}" placeholder="MI" maxlength="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="lname" id="lname" value="{{ $contact->last_name }}" placeholder="Enter Last Name">
                                    <span id="lname-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title">Title:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="title" id="title" value="{{ $contact->position }}" placeholder="Enter Title">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cphone1">Contact Phone 1:</label>
                                    <div class="input-group">
                                        <label class="input-group-addon" for="businessPhone1">1</label>
                                        <input type="text" class="form-control number-only" name="cphone1" id="cphone1" value="{{ $contact->nd_other_number }}" placeholder="Enter phone 1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cphone2">Contact Phone 2:</label>
                                    <div class="input-group">
                                        <label class="input-group-addon"  for="businessPhone2">2</label>
                                        <input type="text" class="form-control number-only" name="cphone2" id="cphone2" value="{{ $contact->nd_other_number_2 }}" placeholder="Enter Phone 2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fax">Fax:</label>
                                    <input type="text" class="form-control number-only" name="contactFax" id="contactFax" value="{{ $contact->contact_fax }}" placeholder="Enter Fax">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mobileNumber">Mobile Number:<span class="required" id="mobileNum"></span></label>
                                    <div class="input-group">
                                        <label class="input-group-addon"  for="businessPhone2">2</label>
                                        <input type="text" class="form-control number-only" name="mobileNumber" id="mobileNumber" value="{{ $contact->nd_mobile_number }}" placeholder="Enter Your Mobile Number" onblur="validateData('partner_contacts','mobile_number',this,'-1','false','empty', 'Mobile number already been used by other contacts','cp-tab'); validateData('users','mobile_number',this,'-1','false','empty', 'Mobile number already been used by other users','cp-tab');">
                                    </div>
                                    <span id="mobileNumber-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="form-group">
                            <label for="note">Enter Note:</label>
                            <textarea class="form-control" name="note" id="note" placeholder="Enter Note">@isset($draft->draftLeadComment->comment){{  $draft->draftLeadComment->comment }}@endisset</textarea>
                        </div>
                        <div class="form-group pull-right">
                            @if($canSaveAsDraft)
                                @include('incs.saveAsDraft')
                            @endif
                            <a href="#bi-tab" class="btn btn-primary btnPrevious">Prev</a>
                            <a href="#ip-tab" class="btn btn-primary btnNext">Next</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="tab-pane" id="interested-product">
                        <div class="row-header">
                            <h3 class="title">Check items that you're interested</h3>
                        </div>
                        <div id="interested-product-div">


                        </div>
                        <div class="form-group pull-right">
                            @if($canSaveAsDraft)
                                @include('incs.saveAsDraft')
                            @endif
                            <a href="#cp-tab" class="btn btn-primary btnPrevious">Prev</a>
                            <a href="#" class="btn btn-primary" id="saveLeadProspect">Submit</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    @if($draft->partner_type_id == 8)
    <script src="{{ config("app.cdn") . "/js/prospects/list.js" . "?v=" . config("app.version") }}"></script>
    @elseif($draft->partner_type_id == 6)
    <script src="{{ config("app.cdn") . "/js/leads/list.js" . "?v=" . config("app.version") }}"></script>
    @endif
    @if (!$systemUser)
        <script>
            $(document).ready(function() {
                $("#assignee").prop("disabled", false)      
                $("#assignTo").prop("disabled", false) 
            })
        </script>
    @endif
@endsection