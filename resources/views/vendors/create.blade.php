@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Create Vendor
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Vendors</a></li>
                <li class="active">Create Vendor</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle">
                    <li class="active"><a href="#business-info" data-toggle="tab" aria-expanded="true">Business Information</a></li>
                    <li class=""><a href="#contact-person" data-toggle="tab" aria-expanded="false">Contact Persons</a></li>
                    <li class=""><a href="#attachments" data-toggle="tab" aria-expanded="false">Attachments</a></li>
                </ul>
                <div class="tab-content no-padding">

                    <div class="tab-pane active" id="business-info">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Personal Information</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="partnerType">Partner Type:</label>
                                    <select name="partnerType" id="partnerType" class="form-control">
                                        <option value="1">Company</option>
                                        <option value="2">ISO</option>
                                        <option value="3">Sub-ISO</option>
                                        <option value="4">Agent</option>
                                        <option value="5">Sub-agent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <br/>
                                    <input type="checkbox" name="assignToMe" id="assignToMe" value="" checked/>
                                    <label for="assignToMe">Assign to me</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="ownership">Ownership:</label>
                                    <select name="ownership" id="ownership" class="form-control">
                                        <option value="1">Limited Liability</option>
                                        <option value="2">Partnership</option>
                                        <option value="3">Private Corp.</option>
                                        <option value="4">Sole Proprietorship</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="legalName">ISO/Affiliate Name (Legal Name):<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="legalName" id="legalName" value="" placeholder="Enter Legal Name"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="ein">EIN:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="ein" id="ein" value="" placeholder="Enter SSN/EIN"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="dba">DBA:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="dba" id="dba" value="" placeholder="Enter DBA"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="dateOpened">Date when business was opened:</label>
                                    <input type="text" class="form-control" name="dateOpened" id="dateOpened" value="" placeholder="mm/yyyy"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="referenceId">Credit Card Reference ID:</label>
                                    <input type="text" class="form-control" name="referenceId" id="referenceId" value="" placeholder="Enter Reference ID"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="website">Website:</label>
                                    <input type="text" class="form-control" name="website" id="website" value="" placeholder="Enter Website"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Personal Contact Information</h3>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="businessPhone1">Business Phone 1:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="businessPhone1" id="businessPhone1" value="" placeholder="Enter Business Phone 1"/>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="extension1">Extension:</label>
                                    <input type="text" class="form-control" name="extension1" id="extension1" value="" placeholder="Ext"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fax">Fax:</label>
                                    <input type="text" class="form-control" name="fax" id="fax" value="" placeholder="Enter Fax"/>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="businessPhone2">Business Phone 2:</label>
                                    <input type="text" class="form-control" name="businessPhone2" id="businessPhone2" value="" placeholder="Enter Business Phone 2"/>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="extension2">Extension:</label>
                                    <input type="text" class="form-control" name="extension2" id="extension2" value="" placeholder="Ext"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email(must be valid):<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="email" id="email" value="" placeholder="Enter Email"/>
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
                                    <input type="text" class="form-control" name="businessAddress1" id="businessAddress1" value="" placeholder="Enter Address"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="businessAddress2">Business Address 2:</label>
                                    <input type="text" class="form-control" name="businessAddress2" id="businessAddress2" value="" placeholder="Enter Address"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="city" id="city" value="" placeholder="Enter City"/>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="state">State:<span class="required">*</span></label>
                                    <select name="state" id="state" class="form-control">
                                        <option value="1">AK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="zip">Zip:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="zip" id="zip" value="" placeholder="Ext"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">Country:<span class="required">*</span></label>
                                    <select name="country" id="country" class="form-control">
                                        <option value="1">United States</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title pull-left">Mailing Address</h3>
                                <div class="pull-right">
                                    <input type="checkbox" name="businessAddress" id="businessAddress" checked> Same as Mailing Address
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mailingAddress1">Mailing Address 1:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="mailingAddress1" id="mailingAddress1" value="" placeholder="Enter Mailing Address 1"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mailingAddress2">Mailing Address 2:</label>
                                    <input type="text" class="form-control" name="mailingAddress2" id="mailingAddress2" value="" placeholder="Enter Mailing Address 2"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mailingCity">City:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="mailingCity" id="mailingCity" value="" placeholder="Enter City"/>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="mailingState">State:<span class="required">*</span></label>
                                    <select name="mailingState" id="mailingState" class="form-control">
                                        <option value="1">AK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="mailingZip">Zip:<span class="required">*</span></label>
                                    <input type="text" class="form-control" name="mailingZip" id="mailingZip" value="" placeholder="Ext"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mailingCountry">Country:<span class="required">*</span></label>
                                    <select name="mailingCountry" id="mailingCountry" class="form-control">
                                        <option value="1">United States</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="contact-person" style="position: relative;">
                        2nd
                    </div>

                    <div class="tab-pane" id="attachments" style="position: relative;">
                        3rd
                    </div>

                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection