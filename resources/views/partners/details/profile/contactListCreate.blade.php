@extends('layouts.app')

@section('content')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Main content -->

            <div class="nav-tabs-custom">
                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                @include("partners.details.profile.profiletabs")
                <div class="tab-content no-padding">
                <form id="frmPartnerContact" name="frmPartnerContact" role="form" action="{{ url("/partners/details/profile/profileContactList/store/$id") }}"  enctype="multipart/form-data" method="POST">
                    <input type="hidden" id="state" name="state" value="" />
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
                                    <input type="text" class="form-control" name="txtContactTitle1" id="txtContactTitle1" value="" placeholder="Enter Title">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtContactFirstName1" id="txtContactFirstName1" value="" placeholder="Enter First Name">
                                    <span id="txtContactFirstName1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Middle Initial:</label>
                                    <input type="text" class="form-control" name="txtContactMiddleInitial1" id="txtContactMiddleInitial1" value="" placeholder="MI" maxlength="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="txtContactLastName1" id="txtContactLastName1" value="" placeholder="Enter Last Name">
                                    <span id="txtContactLastName1-error" style="color:red;"><small></small></span>
                                </div>
                            </div>
   
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>SSN:<span class="required"></span></label>
                                    <input type="text" class="form-control integer-only" name="txtContactSSN1" id="txtContactSSN1" value="" placeholder="Enter SSN" onblur="validateData('partner_contacts','ssn',this,'-1','false','empty', 'SSN already been used by other contacts');">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Percentage of Ownership:</label>
                                    <input type="text" class="form-control" name="txtOwnershipPercentage1" id="txtOwnershipPercentage1" value="" placeholder="0" onkeypress="validate_numeric_input(event);" onblur="convertToFloat(this);">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date of Birth:<span class="required">*</span></label>
                                    <input type="text" class="form-control integer-only" name="txtContactDOB1" id="txtContactDOB1" value="" placeholder="MM/DD/YYYY">
                                    <span id="txtContactDOB1-error" style="color:red;"><small></small></span>
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
                                        <input type="text" class="form-control" name="txtContactPhone1_1" id="txtContactPhone1_1" value="" placeholder="Enter Phone 1">
                                        <span id="txtContactPhone1_1-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone 2:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control" name="txtContactPhone1_2" id="txtContactPhone1_2" value="" placeholder="Enter Phone 2">
                                        <span id="txtContactPhone1_2-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile <small>(must be valid)</small>:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        {{-- <input type="text" class="form-control" name="txtContactMobile1_1" id="txtContactMobile1_1" value="" placeholder="Enter Mobile 1" onblur="validateData('partner_contacts','mobile_number',this,-1,'false','empty', 'Mobile number already been used by other contacts');"> --}}
                                        <input type="text" class="form-control" name="txtContactMobile1_1" id="txtContactMobile1_1" value="" placeholder="Enter Mobile 1">
                                        <span id="txtContactMobile1_1-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile 2:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control" name="txtContactMobile1_2" id="txtContactMobile1_2" value="" placeholder="Enter Mobile 2">
                                        <span id="txtContactMobile1_2-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fax:</label>
                                    <div class="input-group">
                                        <label for="contactPhone1" class="input-group-addon">1</label>
                                        <input type="text" class="form-control" name="txtContactFax1" id="txtContactFax1" value="" placeholder="Enter Fax">
                                        <span id="txtContactFax1-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <small></small>:</label>
                                    {{-- <input type="text" class="form-control" name="txtContactEmail1" id="txtContactEmail1" value="" placeholder="Enter Email Address" onblur="validateData('partner_contacts','email',this,-1,'false','empty', 'Email address already been used by other contacts');"> --}}
                                    <input type="text" class="form-control" name="txtContactEmail1" id="txtContactEmail1" value="" placeholder="Enter Email Address">
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
                                    <input type="text" class="form-control" name="txtContactHomeAddress1_1" id="txtContactHomeAddress1_1" value="" placeholder="Enter Home Address 1"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Home Address 2:</label>
                                    <input type="text" class="form-control" name="txtContactHomeAddress1_2" id="txtContactHomeAddress1_2" value="" placeholder="Enter Home Address 2"/>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="country">Country:</label>
                                    <select name="txtContactCountry1" id="txtContactCountry1" class="form-control">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{ $country->name }}" data-code="{{ $country->iso_code_2 }}">{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="">Zip:</label>
                                    <input type="text" class="form-control" name="txtContactZip1" id="txtContactZip1" value="" placeholder="Zip" onblur="isValidZip(this, 'txtContactCity1', 'txtContactState1')"/>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">State:</label>
                                    <select name="txtContactState1" id="txtContactState1" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="city">City:</label>
                                    <input type="text" class="form-control" name="txtContactCity1" id="txtContactCity1" value="" placeholder="Enter City"/>
                                </div>
                            </div>


                          
                    </div>
                    <div class="row">
                      <div class="form-group pull-right col-lg-12">
                            <button type="submit" class="btn btn-primary pull-right" id="btnCreateContact" name="btnCreateContact">
                                        Save
                            </button>
                        </div>
                    </div>
                </form>
                </div>
        </section>
        <!-- /.content -->

@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/partners/partnercontact.js" . "?v=" . config("app.version") }}"></script>
@endsection

