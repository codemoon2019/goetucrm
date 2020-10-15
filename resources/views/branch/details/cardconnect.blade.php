@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Merchant
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><a href="/merchants">Merchant</a></li>
                <li class="active">{{$merchant->partner_company->company_name}}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                <h3>{{$merchant->partner_company->company_name}}</h3>
                <a href="/merchants" class="btn btn-default pull-right" style="margin-top: -40px">Back to Merchants</a>
            </div>
            <div class="nav-tabs-custom">
                @include("merchants.details.merchanttabs")
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    <!-- <li class=""><a href="#merchant-info" id="minfo" data-toggle="tab" aria-expanded="true">Merchant Info</a></li> -->
                    <li class="active"><a href="#cardpointe" id="cpointe" data-toggle="tab" aria-expanded="true">CardPointe User</a></li>
                    <li class=""><a href="#cardconnect" id="cconnect" data-toggle="tab" aria-expanded="true">Profile</a></li>
                    <li class=""><a href="#create-order" id="corder" data-toggle="tab" aria-expanded="false">Create Order</a></li>
                    <li class=""><a href="#order-history" id="ohistory" data-toggle="tab" aria-expanded="false">Order History</a></li>
                    @if(isset($merchant->cardconnect_profile_id))
                    <li class=""><a href="#create-billing" id="cbill" data-toggle="tab" aria-expanded="false">Create Billing Plan</a></li>
                    <li class=""><a href="#billing-history" id="chistory" data-toggle="tab" aria-expanded="false">Billing Plan History</a></li>
                    @endif
                </ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="cardpointe">
                        <form id="frmCardPointe" name="frmCardPointe"  method="post" enctype="multipart/form-data" action="{{$formCardPointeUrl}}">
                        {{ csrf_field() }}
                        <div class="row mb-plus-20">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>User Status: </label>&nbsp;<b @if($merchantStatus == "In Progress") style="color:red" @else style="color:blue" @endif >{{$merchantStatus}}</b><br>
                                    <label>Signature Status: </label>&nbsp;

                                    @if($signatureStatus == "PENDING")
                                    <b><a target="_blank" href="{{$signUrl}}" >Click to Here Sign</a></b>
                                    @else
                                    <b @if($signatureStatus == "Not Sent") style="color:red" @else style="color:blue"  @endif >{{$signatureStatus}}</b>
                                    @endif
                                    
                                    @if($signatureStatus == "Not Sent")
                                    <br><a href="{{$signatureUrl}}" class="btn btn-primary" id="btnRequestSignature" name="btnRequestSignature">Request Signature</a>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>First Name: </label>
                                    <input type="text" class="form-control" name="cpFirstName" id="cpFirstName" value="{{$copilot->getFirstName()}}"><br>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Last Name: </label>
                                    <input type="text" class="form-control" name="cpLastName" id="cpLastName" value="{{$copilot->getLastName()}}"><br>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Email: </label>
                                    <input type="text" class="form-control" name="cpEmail" id="cpEmail" value="{{$copilot->getEmail()}}"><br>
                                </div>
                            </div> 
                            <div class="col-md-12 sm-col pull-right ta-right">
                                <button type="submit" class="pull-right btn btn-primary" id="btnUpdateUser" name="btnUpdateUser">Update Info</button>
                            </div>

                        </div>
                        </form>

                    </div>
                    <div class="tab-pane" id="cardconnect">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Profiles</h3>
                                <div class="mini-drp-input pull-right mt-minus-40">
                                    <select class="form-control">
                                        <option>Create New</option>
                                    </select>
                                    <button type="button" onclick="createProfile();" class="btn btn-primary">GO</button>
                                </div>
                            </div>
                            <table class="table datatables table-striped">
                                <thead>
                                <th>Account Number</th>
                                <th>Account Name</th>
                                <th>Type</th>
                                <th>Default</th>
                                <th>Action</th>
                                </thead>
                                <tbody>
                                @foreach($ccProfiles as $cc)
                                    <tr id="lineNo{{$cc['acctid']}}">
                                    <td>********{{substr($cc['token'], -4)}}</td>
                                    <td>{{$cc['name']}}</td>
                                    <td>{{$cc['accttype']}}</td>
                                    <td>{{($cc['defaultacct'] == "Y") ? "YES" : "NO"}}</td>
                                    <td><button type="button" onclick="editProfile({{$id}},{{$cc['acctid']}},'********{{substr($cc['token'], -4)}}');" class="btn btn-default btn-sm">Edit</button>
                                    @if($cc['defaultacct'] != "Y")
                                    <button type="button" onclick="deleteProfile({{$id}},{{$cc['acctid']}});" class="btn btn-danger btn-sm">Delete</button>
                                    @endif
                                    </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="create-order">
                        <form id="frmCardConnectOrder" name="frmCardConnectOrder"  method="post" enctype="multipart/form-data" action="{{$formOrderUrl}}">
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control" name="equipmentUnitPrice" id="equipmentUnitPrice" >
                        <input type="hidden" class="form-control" name="equipmentName" id="equipmentName" >
                        <div class="row mb-plus-20">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>STEP 1 </label><br><p>Select a Supplier:</p>
                                    <select class="form-control" id="supplierCode" name="supplierCode">
                                        <option value="CARDCONNECT">CardConnect</option>
                                        <option value="FMDP">First Data Marketplace</option>
                                        <option value="THIRDPARTY">Thirdparty</option>
                                        <option value="TMS">TMS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label><br><p>Select a Type:</p>
                                    <select class="form-control" id="typeCode" name="typeCode">
                                        <option value="TERMINAL">Terminal</option>
                                        <option value="GATEWAY">Gateway</option>
                                        <option value="SOFTWARE">Software</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>STEP 2</label><br><p>Select an Equipment to Order:</p>
                                    <select class="form-control" id="equipmentCode" name="equipmentCode">

                                    </select><br>
                                    <p>Quantity:</p><input type="number" class="form-control" name="equipmentQty" id="equipmentQty" style="width:30%" value="1" onkeypress="validate_numeric_input(event);">
                                </div>
                            </div>         

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description:</label><br><p id="equipmentDescription"></p>
                            </div>
                        </div> 

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>STEP 3 </label><br><p>Bill To:</p>
                                <select class="form-control" id="billTo" name="billTo">
                                    <option value="PARTNER">Partner Residuals</option>
                                    <option value="PARTNERCC">Partner Credit Card</option>
                                    <option value="INTERNAL">Internal</option>
                                    <option value="CUST">Customer</option>
                                </select>
                                <div id="billToAddOpt" style="display: none;">
                                <br><p>Profile ID:</p><input type="text" class="form-control" name="profileID" id="profileID" >
                                <br><p>Account Number:</p><input type="text" class="form-control" name="acctID" id="acctID" >
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp; </label><br><p>Billing Frequency:</p>
                                <select class="form-control" id="billFrequency" name="billFrequency">
                                    <option value="ONETIME">One Time</option>
                                    <option value="MONTHLY">Monthly</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>STEP 4 </label><br><p>Shipping Details:</p>
                                <p>Address:</p><input type="text" class="form-control" name="shippingAddress" id="shippingAddress" value="{{$merchant->partner_shipping->address}}"><br>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br><p>&nbsp;</p>
                                <p>City:</p><input type="text" class="form-control" name="city" id="city" value="{{$merchant->partner_shipping->city}}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br><p>&nbsp;</p>
                                <p>Zip:</p><input type="text" class="form-control" name="zip" id="zip" style="width:40%" value="{{$merchant->partner_shipping->zip}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <p>Ship To:</p><input type="text" class="form-control" name="shipTo" id="shipTo" value="{{$merchant->partner_contact()->first_name . ' ' . $merchant->partner_contact()->last_name}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>Email Address:</p><input type="text" class="form-control" name="emailTo" id="emailTo" value="{{$merchant->partner_company->email}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <p>Shipping Method:</p>
                                <select class="form-control" id="shippingMethod" name="shippingMethod">
                                    <option value="STANDARD">Standard</option>
                                    <option value="EXPEDITED">Expedited</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" id="shippingBillToDiv" style="display: none;">
                            <div class="form-group">
                                <p>Bill To:</p>
                                <select class="form-control" id="shippingBillTo" name="shippingBillTo">
                                    <option value="PARTNER">Partner Residuals</option>
                                    <option value="PARTNERCC">Partner Credit Card</option>
                                    <option value="INTERNAL">Internal</option>
                                    <option value="CUST">Customer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <p>Contact Number:</p><input type="text" class="form-control" name="contactNo" id="contactNo" value="{{ltrim($merchant->partner_contact()->mobile_number,'-')}}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Notes:</label><br><input type="text" class="form-control" name="equipmentNotes" id="equipmentNotes" >
                            </div>
                        </div>  

                        <div class="col-md-12 sm-col pull-right ta-right">
                            <button type="submit" class="pull-right btn btn-primary" id="btnCreateOrder" name="btnCreateOrder">Create Order</button>
                        </div>
                        </form>
                    </div>
                </div>

                <div class="tab-pane" id="order-history">
                    <div class="row mb-plus-20">
                        <table id="cc-order-list"  class="table responsive table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Equipment</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderList as $order)
                                <tr>
                                    <td>{{$order['orderId']}}</td>
                                    <td>{{$order['equipment']}}</td>
                                    <td>{{$order['quantity']}}</td>
                                    <td>{{$order['orderStatusCd']}}</td>
                                    <td><a href="javascript:void(0);" class="btn btn-primary" onclick="showOrder({{$order['orderId']}},'{{$order['equipment']}}')">Edit</a>
                                        @if($order['orderStatusCd'] == 'NEW')
                                        <a href="{{$formOrderCancelUrl}}/{{$order['orderId']}}" class="btn btn-danger">Cancel</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(isset($merchant->cardconnect_profile_id))
                <div class="tab-pane" id="create-billing">
                    <form id="frmCardConnectOrder" name="frmCardConnectOrder"  method="post" enctype="multipart/form-data" action="{{$formBillingUrl}}">
                    {{ csrf_field() }}
                    <div class="row mb-plus-20">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>STEP 1 </label><br><p>Billing Plan Name:</p>
                                 <input type="text" class="form-control" name="billName" id="billName">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>STEP 2 </label><br><p>Select a Profile to be billed:</p>
                                <select class="form-control" id="billProfile" name="billProfile">
                                @foreach($ccProfiles as $cc)
                                    <option value="{{$cc['acctid']}}" @if($cc['defaultacct'] == "Y") selected @endif>
                                    ********{{substr($cc['token'], -4)}} - {{$cc['name']}} - {{$cc['accttype']}}
                                    </option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp; </label><br><p>Set Amount:</p>
                                <input type="text" class="form-control" name="billAmount" id="billAmount" style="width:30%" value="1.00" onkeypress="validate_numeric_input(event);">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>STEP 3 </label><br><p>Set Time Span:</p>
                                <select class="form-control" id="billTimeSpan" name="billTimeSpan">
                                    <option value="1">Daily</option>
                                    <option value="2">Weekly</option>
                                    <option value="3">Monthly</option>
                                    <option value="4">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>&nbsp; </label><br><p>Set Frequency of Billing: (Bill the customer every x time span) </p>
                                 <input type="number" class="form-control" name="billEvery" id="billEvery" style="width:10%" value="1" onkeypress="validate_numeric_input(event);">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <p>Billing Start Date:</p>
                                <input type="text" class="form-control" id="billStartDate" name="billStartDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <p>Bill until:</p>
                                <select class="form-control" id="billUntil" name="billUntil">
                                    <option value="C">Cancelled</option>
                                    <option value="N">Number of Payments</option>
                                    <option value="D">Date</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" id="billNumPayDiv" style="display: none;">
                            <div class="form-group">
                                 <p>Number of Payments:</p>
                                 <input type="number" class="form-control" name="billUntilNumPayments" id="billUntilNumPayments" style="width:30%" value="1" onkeypress="validate_numeric_input(event);">
                            </div>
                        </div>
                        <div class="col-md-3" id="billDateDiv" style="display: none;">
                            <div class="form-group">
                                <p>Last Billing Date:</p>
                                <input type="text" class="form-control" id="billUntilDate" name="billUntilDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <p>Email Receipt:</p>
                                <select class="form-control" id="billReceipt" name="billReceipt">
                                    <option value="Y">YES</option>
                                    <option value="N">NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9 sm-col pull-right ta-right">
                            <br><br>
                            <button type="submit" class="pull-right btn btn-primary" id="btnCreateBillPlan" name="btnCreateBillPlan">Create Billing Plan</button>
                        </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" id="billing-history">
                    <div class="row mb-plus-20">

                    </div>
                </div>
                @endif

            </div>
        </section>
        <!-- /.content -->
    </div>

    <div class="modal fade" id="editPaymentGateway" role="dialog">
        <div class="modal-dialog" role="document" style="max-width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmCardConnectProfile" name="frmCardConnectProfile"  method="post" enctype="multipart/form-data" action="/merchants/cardpoint/save_profile/{{$id}}">
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control" id="ccID" name="ccID">
                        <div class="row">
                            <div class="col-sm-5 sm-col">
                                <div class="form-group">
                                    <label>Account Number: <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="txtAccount" name="txtAccount">
                                </div>
                            </div>
                            <div class="col-sm-5 sm-col">
                                <div class="form-group">
                                    <label>Account Name:<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="txtName" name="txtName">
                                </div>
                            </div>

                            <div class="col-sm-3 sm-col">
                                <div class="form-group">
                                    <label>Expiry (MMYY):<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="txtExpiry" name="txtExpiry">
                                </div>
                            </div>

                            <div class="col-sm-6 sm-col">
                                <div class="form-group">
                                    <label>Address:<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="txtAddress" name="txtAddress">
                                </div>
                            </div>

                            <div class="col-sm-3 sm-col">
                                <div class="form-group">
                                    <label>City:<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="txtCity" name="txtCity">
                                </div>
                            </div>

                            <div class="col-sm-2 sm-col">
                                <div class="form-group">
                                    <label for="state">State:<span class="required">*</span></label>
                                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" id="txtState" name="txtState" tabindex="-1" aria-hidden="true">
                                        @foreach($stateUS as $s)
                                            <option value="{{$s->abbr}}">{{$s->abbr}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3 sm-col">
                                <div class="form-group">
                                    <label>Postal Code:<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="txtPostal" name="txtPostal">
                                </div>
                            </div>

                            <div class="col-sm-3 sm-col">
                                <div class="form-group">
                                    <label>Default Account:<span class="required">*</span></label>
                                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" id="txtDefault" name="txtDefault" tabindex="-1" aria-hidden="true">
                                        <option value="Y">YES</option>
                                        <option value="N">NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-save" >Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>



    <div id="modalViewOrder" class="modal" role="dialog" style="overflow:auto;">
          <div class="modal-dialog" style="max-width:800px;">
            <form id="frmCCOrderEdit" name="frmCCOrderEdit"  method="post" enctype="multipart/form-data" action="{{$formOrderUpdateUrl}}">
            {{ csrf_field() }}
            <input type="hidden" id="txtOrderId" name="txtOrderId">
            <input type="hidden" class="form-control" name="editEquipmentUnitPrice" id="editEquipmentUnitPrice" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="order-header">Card Connect Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
              <div class="modal-body">
                <div class="row mb-plus-20">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Equipment</label><p id="editEquipmentName"></p>
                            <label id="editPriceLabel">Unit Price</label><p id="editPrice"></p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Quantity:</label><input type="number" class="form-control" name="editEquipmentQty" id="editEquipmentQty" style="width:30%" value="1" onkeypress="validate_numeric_input(event);">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Bill To:</label>
                            <select class="form-control" id="editBillTo" name="editBillTo">
                                <option value="PARTNER">Partner Residuals</option>
                                <option value="PARTNERCC">Partner Credit Card</option>
                                <option value="INTERNAL">Internal</option>
                                <option value="CUST">Customer</option>
                            </select>
                            <div id="editBillToAddOpt" style="display: none;">
                            <br><label>Profile ID:</label><input type="text" class="form-control" name="editProfileID" id="editProfileID" >
                            <br><label>Account Number:</label><input type="text" class="form-control" name="editAcctID" id="editAcctID" >
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Billing Frequency:</label>
                            <select class="form-control" id="editBillFrequency" name="editBillFrequency">
                                <option value="ONETIME">One Time</option>
                                <option value="MONTHLY">Monthly</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address:</label>
                            <input type="text" class="form-control" name="editShippingAddress" id="editShippingAddress" value=""><br>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>City:</label><input type="text" class="form-control" name="editCity" id="editCity" value="">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Zip:</label><input type="text" class="form-control" name="editZip" id="editZip" value="">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ship To:</label><input type="text" class="form-control" name="editShipTo" id="editShipTo" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email Address:</label><input type="text" class="form-control" name="editEmailTo" id="editEmailTo" value="">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Shipping Method:</label>
                            <select class="form-control" id="editShippingMethod" name="editShippingMethod">
                                <option value="STANDARD">Standard</option>
                                <option value="EXPEDITED">Expedited</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" id="editShippingBillToDiv" style="display: none;">
                        <div class="form-group">
                            <label>Bill To:</label>
                            <select class="form-control" id="editShippingBillTo" name="editShippingBillTo">
                                <option value="PARTNER">Partner Residuals</option>
                                <option value="PARTNERCC">Partner Credit Card</option>
                                <option value="INTERNAL">Internal</option>
                                <option value="CUST">Customer</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Contact Number:</label><input type="text" class="form-control" name="editContactNo" id="editContactNo" value="">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Notes:</label><br><input type="text" class="form-control" name="editEquipmentNotes" id="editEquipmentNotes" >
                        </div>
                    </div>  


                </div>                     
              <div class="modal-footer">
                  <button type="submit" id="btnUpdatedOrder" name="btnUpdatedOrder" class="btn btn-primary btn-save">Update Order</button>
                <button type="button" id="btnCancelPLoad" name="btnCancelPLoad" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
            </form>
        </div>
    </div>



@endsection
@section('script')
    <script src="{{ config(" app.cdn ") . "/js/merchants/product.js" . "?v=" . config(" app.version ") }}"></script>
    <script>
    arr = window.location.href.split('#');
    if(arr[1] != undefined)
    {
        $('#'+arr[1]).trigger('click');  
    }

    $(document).ready(function () {
        var loadOnce = false;
        $('#acctID').mask('999999999');
        $('#editAcctID').mask('999999999');
        $('#profileID').mask('99999999999999999999');
        $('#editProfileID').mask('99999999999999999999');
        
        $('#zip').mask("99999");
        $('#contactNo').mask("999-999-9999");

        $('#txtExpiry').mask("9999");

        $('#billUntilDate').datepicker({ 'format': 'MM/DD/YYYY' });
        $('#billStartDate').datepicker({ 'format': 'MM/DD/YYYY' });

        $('#supplierCode').change(function () {
            refreshEquipmentList();
        });
        $('#typeCode').change(function () {
            refreshEquipmentList();
        });
        $('#equipmentCode').change(function () {
            $('#equipmentDescription').html('Price: $'+$('option:selected', this).attr('data-price')+'<br>'+$('option:selected', this).attr('data-desc'));
            // $('#equipmentPrice').html($('option:selected', this).attr('data-price'));
        });

        $('#billUntil').change(function () {
            $('#billDateDiv').hide();
            $('#billNumPayDiv').hide();

            if($(this).val() == "N"){
                $('#billNumPayDiv').show();
                $('#billDateDiv').hide();
            }

            if($(this).val() == "D"){
                $('#billDateDiv').show();
                $('#billNumPayDiv').hide();
            }
        });

        $('#billTo').change(function () {
            if($(this).val() == "PARTNERCC"){
                $('#billToAddOpt').show();
            }else{
                $('#billToAddOpt').hide();
            }
        });

        $('#editBillTo').change(function () {
            if($(this).val() == "PARTNERCC"){
                $('#editBillToAddOpt').show();
            }else{
                $('#editBillToAddOpt').hide();
            }
        });


        $('#shippingMethod').change(function () {
            if($(this).val() == "EXPEDITED"){
                $('#shippingBillToDiv').show();
            }else{
                $('#shippingBillToDiv').hide();
            }
        });

        $('#editShippingMethod').change(function () {
            if($(this).val() == "EXPEDITED"){
                $('#editShippingBillToDiv').show();
            }else{
                $('#editShippingBillToDiv').hide();
            }
        });

        $('#corder').click(function () {
            if(!loadOnce){
                $('#supplierCode').trigger('change');
                loadOnce = true;
            }
        });

        $('#frmCardPointe').submit(function () {
            if (!validateField('cpFirstName', 'First Name is required')) {
                return false;
            }

            if (!validateField('cpLastName', 'Last Name is required')) {
                return false;
            }

            if (!validateField('cpEmail', 'Email is required')) {
                return false;
            }

        });
        
        $('#frmCardConnectOrder').submit(function () {
            if ( $('#equipmentCode').val() == -1) {
                document.getElementById('equipmentCode').style.borderColor = "red";
                alert('Equipment to order is required!');
                return false;
            }else{
                document.getElementById('equipmentCode').style.removeProperty('border');
            }

            $('#equipmentUnitPrice').val($('option:selected', '#equipmentCode').attr('data-price'));
            $('#equipmentName').val($('option:selected', '#equipmentCode').attr('data-name'));

            if (!validateField('equipmentQty', 'Quantity is required')) {
                return false;
            }

            if($('#billTo').val() == "PARTNERCC"){
                if (!validateField('profileID', 'Profile ID is required')) {
                    return false;
                }
                if (!validateField('acctID', 'Account Number is required')) {
                    return false;
                }                
            }

            if (!validateField('shippingAddress', 'Shipping Address is required')) {
                return false;
            }

            if (!validateField('city', 'City is required')) {
                return false;
            }

            if (!validateField('zip', 'Zip is required')) {
                return false;
            }

            if (!validateField('shipTo', 'Ship To is required')) {
                return false;
            }

            if (!validateField('emailTo', 'Email Address is required')) {
                return false;
            }

            if (!validateField('contactNo', 'Contact Number is required')) {
                return false;
            }

            showLoadingModal('Creating Order.... Please wait...');

        });

         $('#frmCardConnectProfile').submit(function () {
            if (!validateField('txtAccount', 'Account Number is required')) {
                return false;
            }
            if (!validateField('txtName', 'Account Name is required')) {
                return false;
            }
            if (!validateField('txtExpiry', 'Expiry Date is required')) {
                return false;
            }
            if (!validateField('txtAddress', 'Address is required')) {
                return false;
            }
            if (!validateField('txtCity', 'City is required')) {
                return false;
            }
            if (!validateField('txtPostal', 'Postal Code is required')) {
                return false;
            }
            showLoadingModal('Saving Profile.... Please wait...');
         });

        $('#frmCCOrderEdit').submit(function () {
            
            if (!validateField('editEquipmentQty', 'Quantity is required')) {
                return false;
            }

            if($('#editBillTo').val() == "PARTNERCC"){
                if (!validateField('editProfileID', 'Profile ID is required')) {
                    return false;
                }
                if (!validateField('editAcctID', 'Account Number is required')) {
                    return false;
                }                
            }

            if (!validateField('editShippingAddress', 'Shipping Address is required')) {
                return false;
            }

            if (!validateField('editCity', 'City is required')) {
                return false;
            }

            if (!validateField('editZip', 'Zip is required')) {
                return false;
            }

            if (!validateField('editShipTo', 'Ship To is required')) {
                return false;
            }

            if (!validateField('editEmailTo', 'Email Address is required')) {
                return false;
            }

            if (!validateField('editContactNo', 'Contact Number is required')) {
                return false;
            }

            showLoadingModal('Updating Order.... Please wait...');

        });

    });

    function createProfile() {
        $('#ccID').val(-1);
        $('#txtAccount').val('');
        $('#txtExpiry').val('');
        $('#txtName').val('');
        $('#txtAddress').val('');
        $('#txtCity').val('');
        $('#txtPostal').val('');
        document.getElementById('txtAccount').disabled = false;
        $('#editPaymentGateway').modal('show');
    }


    function editProfile(id,profileId,account) {
        $('#ccID').val(profileId);
        showLoadingModal('Loading Profile...');
        $.getJSON('/merchants/cardpoint/get_profile/'+id+'/'+profileId, null, function(data) {  
            if(data.success)
            {
                $('#txtAccount').val(account);
                document.getElementById('txtAccount').disabled = true;
                $('#txtExpiry').val(data.data.expiry);
                $('#txtName').val(data.data.name);
                $('#txtAddress').val(data.data.address);
                $('#txtCity').val(data.data.city);
                $('#txtPostal').val(data.data.postal);
                $('#txtDefault').val(data.data.defaultacct);
                $('#txtState').val(data.data.region);
                $('#editPaymentGateway').modal('show');                                
            }else{
                alert(data.message);
            }
            closeLoadingModal();
        }); 
    }

    function deleteProfile(id,profileId,account) {
        $('#ccID').val(profileId);
        if(confirm('This will delete the selected profile. Proceed?')){
            showLoadingModal('Deleting Profile...');
            $.getJSON('/merchants/cardpoint/delete_profile/'+id+'/'+profileId, null, function(data) {  
                if(data.success)
                {
                    $("#lineNo" + profileId).remove(); 
                }
                closeLoadingModal();
                alert(data.message);
            });             
        }

    }


    function refreshEquipmentList(){
        showLoadingModal('Loading Equipments...');
        $('#equipmentDescription').html('');
        $('#equipmentPrice').html('');
        $.getJSON('/merchants/copilot_list_equipments/'+$('#supplierCode').val()+'/'+$('#typeCode').val(), null, function(data) {  
            $('#equipmentCode').empty(); 
            if(data.success)
            {
                var newOption = $(data.data);
                $('#equipmentCode').append(newOption);
                $('#equipmentCode').trigger("change");                     
            }else{
                alert(data.message);
            }

            closeLoadingModal();
        });        
    }

    function validate_numeric_input(evt) {
        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
        var regex = /[0-9\b]|\./;
        if (!regex.test(key)) {
            theEvent.returnValue = false;
            if (theEvent.preventDefault) theEvent.preventDefault();
        }
    }   

    function validateField(element, msg) {
        if ($('#' + element).val().trim() == "") {
            document.getElementById(element).style.borderColor = "red";
            alert(msg);
            return false;
        } else {
            document.getElementById(element).style.removeProperty('border');
            return true;
        }
    }

    function showOrder(id,equipmentName){
        showLoadingModal("Loading Order Details... Please wait.....");
        $.getJSON('/merchants/copilot_get_order/'+id, null, function(data) {
            $('#txtOrderId').val(id);
            $('#editEquipmentName').html(equipmentName);
            if(data['unitPrice'] == null){
                $('#editPrice').html('$ '+data['monthlyPrice']);
                $('#editEquipmentUnitPrice').val(data['monthlyPrice']);
            }
            if(data['monthlyPrice'] == null){
                $('#editPrice').html('$ '+data['unitPrice']);
                $('#editEquipmentUnitPrice').val(data['unitPrice']);
            }
            $('#editEquipmentQty').val(data['quantity']);
            $('#editBillTo').val(data['billToCd']);
            $('#editBillFrequency').val(data['billingFrequencyCd']);
            $('#editProfileID').val(data['profileId']);
            $('#editAcctID').val(data['acctId']);
            $('#editShippingAddress').val(data['shippingDetails']['shippingAddress']['address1']);

            $('#editCity').val(data['shippingDetails']['shippingAddress']['city']);
            $('#editZip').val(data['shippingDetails']['shippingAddress']['zip']);
            $('#editShipTo').val(data['shippingDetails']['shipToAttn']);
            $('#editEmailTo').val(data['shippingDetails']['shipToAttnEmail']);

            $('#editShippingMethod').val(data['shippingDetails']['shippingMethodCd']);
            $('#editContactNo').val(data['shippingDetails']['merchantContactPhone']);
            $('#editEquipmentNotes').val(data['orderNotes']);
            
            if(data['shippingDetails']['shippingMethodCd'] == "EXPEDITED"){
                $('#editShippingBillToDiv').show();
                $('#editShippingBillTo').val(data['shippingDetails']['shippingBillToCd']);
            }else{
                $('#editShippingBillToDiv').hide();
            }         
    
            if($('#editBillTo').val() == "PARTNERCC"){    
                $('#editBillToAddOpt').show();
            }else{
                $('#editBillToAddOpt').hide();
            }
            
            closeLoadingModal();
            $('#modalViewOrder').modal('show');
        });
    }

    </script>
@endsection