@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Lead
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
                <div class="alert alert-success hide">
                    <p id="msg-success"></p>
                </div>
                <div class="alert alert-danger hide">
                    <p id="msg-danger"></p>
                </div>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li><a href="/leads">Leads</a></li>
                <li class="active">Contact</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                @if(!isset($partner_info) || count($partner_info) <= 0)
                <h3>Lead Company</h3>
                <a href="{{ url("leads/") }}" class="btn btn-default pull-right mt-minus-40">< Back to Leads</a>
                <div class="crearfix"></div>
                <small class="small-details">
                    Lead ID <br/>
                    Business Address <br/>
                    Contact Phone <br/>
                    Email Address
                </small>
                @else
                <h3>{{ $partner_info[0]->company_name }}</h3>
                <a href="{{ url("leads/") }}" class="btn btn-default pull-right mt-minus-40">< Back to Leads</a>
                <div class="crearfix"></div>
                <small class="small-details">
                    {{ $partner_info[0]->partner_id_reference }} <br/>
                    {{ $partner_info[0]->address1 }}, {{ $partner_info[0]->city}} {{ $partner_info[0]->state }}, {{ $partner_info[0]->zip }}, {{ $partner_info[0]->country_name }} <br/>
                    {{  $calling_code }}{{ $partner_info[0]->phone1 }} <br/>
                    {{ $partner_info[0]->email }}
                </small>
                @endif
            </div>
            <div class="nav-tabs-custom">
                <ul class="tabs-rectangular">
                    <li><a href="{{ url('leads/details/summary/'.$partner_id) }}">Summary</a></li>
                    @if($isInternal)
                    <li><a href="{{ url('leads/details/profile/'.$partner_id) }}">Profile</a></li>
                    <li class="active"><a href="{{ url('leads/details/contact/'.$partner_id) }}">Contact</a></li>
                    <li><a href="{{ url('leads/details/interested/'.$partner_id) }}">Interested Products</a></li>
                    <!-- <li><a href="{{ url('leads/details/applications/'.$partner_id) }}">Applications</a></li> -->
                    <li><a href="{{ url('leads/details/appointment/'.$partner_id) }}">Appointment</a></li>
                    @endif
                </ul>
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                </ul>
                <div class="tab-content no-padding">
                    @if($isInternal)
                    <div class="tab-pane active" id="info">
                        <!-- <div class="tab-pane" id="contact-person"> -->
                    <form role="form" name="frmUpdateContact" id="frmUpdateContact">
                        <input type="hidden" name="countryName" id="countryName" value="{{$partner_info[0]->country_name}}">
                        <input type="hidden" id="txtLeadID" name="txtLeadID" value="{{$partner_id}}">
                        <input type="hidden" id="txtEmailLead" name="txtEmailLead" value="{{$partner_info[0]->email}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control alpha" name="fname" id="fname" value="{{$partner_info[0]->first_name}}" placeholder="Enter First Name">
                                    <span id="fname-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mname">Middle Initial</label>
                                    <input type="text" class="form-control alpha" name="mname" id="mname" value="{{$partner_info[0]->middle_name}}" placeholder="MI">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control alpha" name="lname" id="lname" value="{{$partner_info[0]->last_name}}" placeholder="Enter Last Name">
                                    <span id="lname-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title">Title:<span class="required"></span></label>
                                    <input type="text" class="form-control alpha" name="title" id="title" value="{{$partner_info[0]->position}}" placeholder="Enter Title">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cphone1">Contact Phone 1:</label>
                                    <div class="input-group">
                                        <label class="input-group-addon" for="businessPhone1">{{$partner_info[0]->company_country_code}}</label>
                                        <input type="text" class="form-control number-only" name="cphone1" id="cphone1" value="{{$partner_info[0]->nd_office_number}}" placeholder="Enter phone 1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cphone2">Contact Phone 2</label>
                                    <div class="input-group">
                                        <label class="input-group-addon"  for="businessPhone1">{{$partner_info[0]->company_country_code}}</label>
                                        <input type="text" class="form-control number-only" name="cphone2" id="cphone2" value="{{$partner_info[0]->nd_office_number_2}}" placeholder="Enter Phone 2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fax">Fax:</label>
                                    <div class="input-group">
                                        <label class="input-group-addon"  for="businessPhone1">{{$partner_info[0]->company_country_code}}</label>
                                        <input type="text" class="form-control number-only" name="contactFax" id="contactFax" value="{{$partner_info[0]->contact_fax}}" placeholder="Enter Fax">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">  
                                <div class="form-group">
                                    <label class="mobileNumber">Mobile Number:<span class="required">@if(!isset($partner_info[0]->email)) * @endif</span></label>
                                    <div class="input-group">
                                        <label class="input-group-addon"  for="businessPhone1">{{$partner_info[0]->company_country_code}}</label>
                                        <input type="text" class="form-control number-only" name="mobileNumber" id="mobileNumber" value="{{$partner_info[0]->nd_mobile_number}}" placeholder="Enter Your Mobile Number" onblur="validateData('partner_contacts','mobile_number',this,{{$partner_id}},'false','empty', 'Mobile number already been used by other contacts');">
                                    </div>
                                    <span id="mobileNumber-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label class="email2">2nd Email</label>
                                    <input type="text" class="form-control" name="email2" id="email2" value="{{$partner_info[0]->contact_email}}" placeholder="Enter Email">
                                </div>
                            </div> -->
                        </div>
                        <div class="form-group pull-right">
                            @if($canEdit == 1)
                            <a href="#" class="btn btn-primary" id="updateContact">Save</a>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <!-- </div> -->
                    {{--<div class="tab-pane" id="notes"></div>--}}
                @endif
                </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/leads/list.js" . "?v=" . config("app.version") }}"></script>
@endsection
