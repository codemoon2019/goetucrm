@extends('layouts.app')

@section('style')
    <link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
            <div class="nav-tabs-custom">
                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                @include("partners.details.profile.profiletabs")
                <div class="tab-content no-padding">
                <form id="frmPartnerContact" name="frmPartnerContact" role="form" action="{{ url("/partners/details/profile/profileContactList/update/$id/$contact_id") }}"  enctype="multipart/form-data" method="POST">
                    <input type="hidden" id="state" name="state" value="{{$contact_info->state}}" />
                    <input type="hidden" name="partner_email" id="partner_email" value="{{$partner_info->email}}">
                    <input type="hidden" name="is_orig_con" id="is_orig_con" value="{{$contact_info->is_original_contact}}">
                    <input name="_method" value="PUT" type="hidden">
                    {{ csrf_field() }}
                    <div class="tab-pane active">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Contact</h3>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Title:<span class="required"></span></label>
                                    <input type="text" class="form-control" name="txtContactTitle1" id="txtContactTitle1" value="{{$contact_info->position}}" placeholder="Enter Title">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtContactFirstName1" id="txtContactFirstName1" value="{{$contact_info->first_name}}" placeholder="Enter First Name">
                                    <span id="txtContactFirstName1-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Middle Initial:</label>
                                    <input type="text" class="form-control" name="txtContactMiddleInitial1" id="txtContactMiddleInitial1" value="{{$contact_info->middle_name}}" placeholder="MI">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtContactLastName1" id="txtContactLastName1" value="{{$contact_info->last_name}}" placeholder="Enter Last Name">
                                    <span id="txtContactLastName1-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>

 
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>SSN:</label>
                                    <div class="input-group">
                                        <label for="txtSSNDisplay" class="input-group-addon"><small><a id="showSSN" href="javascript:void(0);" onclick="showSSN()">Update SSN</a></small></label>
                                        <input type="text" class="form-control integer-only" name="txtSSNDisplay" id="txtSSNDisplay" value="{{$contact_info->ssn}}" disabled>
                                        <input type="text" class="form-control integer-only" name="txtContactSSN1" id="txtContactSSN1" value="" placeholder="Enter SSN" onblur="validateData('partner_contacts','ssn',this,'{{$contact_info->partner_id}}','false','partner_', 'SSN already been used by other contacts');" style="display: none;">
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Percentage of Ownership:</label>
                                    <input type="text" class="form-control" name="txtOwnershipPercentage1" id="txtOwnershipPercentage1" value="{{$contact_info->ownership_percentage}}" placeholder="0" onkeypress="validate_numeric_input(event);" onblur="convertToFloat(this);">
                                    <span id="txtOwnershipPercentage1-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date of Birth:<span class="required"></span></label>
                                    <input type="text" class="form-control integer-only" name="txtContactDOB1" id="txtContactDOB1" value="{{$contact_info->dob}}" placeholder="MM/DD/YYYY">
                                    <span id="txtContactDOB1-error" style="color:red;"><small></small></span></br>
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
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactPhone1_1" id="txtContactPhone1_1" value="{{$contact_info->nd_other_number}}" placeholder="Enter Phone 1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone 2:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactPhone1_2" id="txtContactPhone1_2" value="{{$contact_info->nd_other_number_2}}" placeholder="Enter Phone 2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile <small>(must be valid)</small>:<span class="required">@if(!isset($partner_info->email)) * @endif</span>
                                    @if ($is_new==0 && isset($contact_info->mobile_number)) 
                                        @if ($contact_info->is_verified_mobile==0)
                                            <span class="label bg-gray">unverified</span>
                                        @endif
                                    @endif
                                    </label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        {{-- <input type="text" class="form-control number-only" name="mobile_number" id="mobile_number" value="{{$contact_info->mobile_number}}" placeholder="Enter Mobile 1" 
                                        onblur="validateData('partner_contacts','mobile_number',this,{{$contact_id}},'false','partner_', 'Mobile number already been used by other contacts'); 
                                        @if($contact_info->is_original_contact == 1)
                                        validateData('users','mobile_number',this,{{$contact_id}},'true','reference_', 'Mobile number already been used by other users'); 
                                        @endif"> --}}
                                        <input type="text" class="form-control number-only" name="mobile_number" id="mobile_number" value="{{$contact_info->nd_mobile_number}}" placeholder="Enter Mobile 1">
                                    </div>
                                    @if ($contact_info->is_original_contact==1)
                                        <span class="error" style="color:orange;"><small>Note: If left blank, Partner must have at least the Company&apos;s Email Address.</small></span>
                                    @endif
                                    <span id="mobile_number-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile 2:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactMobile1_2" id="txtContactMobile1_2" value="{{$contact_info->nd_mobile_number_2}}" placeholder="Enter Mobile 2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fax:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control number-only" name="txtContactFax1" id="txtContactFax1" value="{{$contact_info->fax}}" placeholder="Enter Fax">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <small></small>:<span class="required"></span></label>
                                    <!-- <input type="text" class="form-control" name="email_address" id="email_address" value="{{$contact_info->email}}" placeholder="Enter Email Address" onblur="validateData('partner_contacts','email',this,{{$contact_id}},'false','empty', 'Email address already been used by other contacts');" onchange="validateEmail(this.id);"> -->
                                    {{-- <input type="text" class="form-control" name="email_address" id="email_address" value="{{$contact_info->email}}" placeholder="Enter Email Address" 
                                    onblur="validateData('partner_contacts','email',this,{{$contact_id}},'false','partner_', 'Email address already been used by other contacts');
                                    @if($contact_info->is_original_contact == 1)
                                    validateData('users','email_address',this,{{$contact_id}},'true','reference_', 'Email address already been used by other users'); 
                                    @endif"> --}}
                                    <input type="text" class="form-control" name="email_address" id="email_address" value="{{$contact_info->email}}" placeholder="Enter Email Address">
                                    <span id="email_address-error" style="color:red;"><small></small></span></br>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Home Address</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Home Address 1:</label>
                                    <input type="text" class="form-control" name="txtContactHomeAddress1_1" id="txtContactHomeAddress1_1" value="{{$contact_info->address1}}" placeholder="Enter Home Address 1"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Home Address 2:</label>
                                    <input type="text" class="form-control" name="txtContactHomeAddress1_2" id="txtContactHomeAddress1_2" value="{{$contact_info->address2}}" placeholder="Enter Home Address 2"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="country">Country:</label>
                                    <select name="txtContactCountry1" id="txtContactCountry1" class="form-control s2-country">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{ $country->name }}" data-code="{{ $country->iso_code_2 }}" {{ $contact_info->country == $country->name ? "selected" : "" }}>{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Zip:</label>
                                    <input type="text" class="form-control" name="txtContactZip1" id="txtContactZip1" value="{{$contact_info->zip}}" placeholder="Zip"/>
                                    @include('incs.zipHelpNote')
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">State:</label>
                                    <select name="txtContactState1" id="txtContactState1" class="form-control s2-state" disabled>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="city">City:</label>
                                    {{-- <input type="text" class="form-control" name="txtContactCity1" id="txtContactCity1" value="{{$contact_info->city}}" placeholder="Enter City"/> --}}
                                    <select name="txtContactCity1" id="txtContactCity1" class="form-control select2" disabled>
                                        <option value="{{ $contact_info->city }}" selected>{{ $contact_info->city }}</option>
                                    </select>
                                </div>
                            </div>


                          
                    </div>

                    @php 
                        $access = session('all_user_access'); 
                        if(array_key_exists(strtolower($partner_info->partner_type_description),$access)){
                        if(strpos($access[strtolower($partner_info->partner_type_description)], 'edit') !== false){ @endphp
                    <div class="row">
                      <div class="form-group">
                            <button type="submit" class="btn btn-primary pull-right" id="btnCreateContact" name="btnCreateContact">
                                        Save
                            </button>
                        </div>
                    </div>
                    @php } } @endphp

                </form>
                </div>
        </section>
        <!-- /.content -->

    </div>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/partners/partnercontact.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config(' app.cdn ') . '/js/partners/newFieldValidation.js' . '?v=' . config(' app.version ') }}"></script>

@endsection
