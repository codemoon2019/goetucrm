@extends('layouts.app')

@section('content')

<style type="text/css">
    .table {
        font-size: 12px;
    }

    .table td,
    .table th {
        padding: .25rem;
        vertical-align: top;
        border-top: none;
    }

    .table thead td {
        border-bottom: 1px solid #e9ecef;
        background-color: #3c8dbc;
        color: white;
    }

    .table-val-name {
        font-weight: bold;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Merchant
            <!-- <small>Dito tayo magpapasok ng different pages</small> -->
        </h1>
        <ol class="breadcrumb">
            <li><a href="/">Dashboard</a></li>
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
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                @if($canCreate)
                <li class=""><a href="#create-invoice" data-toggle="tab" aria-expanded="true" id="createInv">Create
                        Invoice</a></li>
                @endif
                <li class="active"><a href="#history" data-toggle="tab" aria-expanded="false" id="historyInv">History</a></li>
                <li class=""><a href="#charges" data-toggle="tab" aria-expanded="false" id="recInv">Recurring Invoice</a></li>
                <li class=""><a href="#payment-method" id="pm" data-toggle="tab" aria-expanded="false" id="payMetInv">Payment
                        Method</a></li>
            </ul>
            <div class="tab-content no-padding">
                <div class="tab-pane" id="create-invoice">
                    <form id="frmInvoiceCreate" name="frmInvoiceCreate" method="post" enctype="multipart/form-data"
                        action="/merchants/create_invoice/{{$id}}">
                        {{ csrf_field() }}
                        <input type="hidden" id="txtInvoiceDetailCount" name="txtInvoiceDetailCount" value="0">
                        <input type="hidden" id="txtTotalDue" name="txtTotalDue">
                        <input type="hidden" id="txtInvoiceDetailList" name="txtInvoiceDetailList">
                        <input type="hidden" id="txtMerchantEmail" name="txtMerchantEmail" value="{{ $email }}">
                        <div class="row mb-plus-20">
                            <div class="row-header">
                                <h3 class="title">Information</h3>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <tr>
                                        <td class="text-right">Client Name</td>
                                        <td class="light-blue"><label>{{$merchant->partner_contact()->first_name}}
                                                {{$merchant->partner_contact()->middle_name}}
                                                {{$merchant->partner_contact()->last_name}}</label></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Invoice Date</td>
                                        <td class="light-blue"><input type="text" class="form-control dataPicker" name="txtInvoiceDate"
                                                id="txtInvoiceDate" placeholder="mm/dd/yyyy" /></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Due Date</td>
                                        <td class="light-blue"><input type="text" class="form-control dataPicker" name="txtInvoiceDueDate"
                                                id="txtInvoiceDueDate" placeholder="mm/dd/yyyy" /></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <tr>
                                        <td class="text-right">Total Due</td>
                                        <td class="light-blue">
                                            <span id="invoiceTotal">$ 0.00</span>
                                            <label>USD</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Payment Method</td>
                                        <td class="light-blue">
                                            <select id="newInvoicePaymentType" name="newInvoicePaymentType">
                                                @foreach($payment_types as $payment_type)
                                                <option value="{{$payment_type->id}}">{{$payment_type->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mb-plus-20">
                            <div class="row-header">
                                <h3 class="title">Invoice Items</h3>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered table-condensed text-center" id="invoice_items">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th width="10%">Amount</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <td colspan="3">
                                                <a href="javascript:void(0);" class="pull-right" id="addInvoice"><i
                                                        class="fa fa-plus-circle"></i>&nbsp; Add Invoice Item</a>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <table class="table table-bordered table-condensed text-center">
                                    <thead>
                                        <tr>
                                            <th class="text-right" colspan="2">Total Due:</th>
                                            <th width="20%" class="text-left" id="invoiceTotalDue" name="invoiceTotalDue">$
                                                0.00 USD</th>
                                        </tr>
                                    </thead>


                                </table>
                            </div>
                        </div>
                        <div class="row mb-plus-20">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Notes:</label>
                                    <textarea class="form-control" name="txtNotes"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-primary">Create Invoice</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane active" id="history">
                    <table class="table datatables table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Invoice#</th>
                                <th>Product</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th>Total</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                            <tr>
                                <td></td>
                                <td>{{$invoice->id}}</td>
                                <td>{{$invoice->reference}}</td>
                                <td id="inv-date-{{$invoice->id}}">{{ date_format(new
                                    DateTime($invoice->invoice_date),"m/d/Y")}}</td>
                                <td id="inv-due-{{$invoice->id}}">{{ date_format(new
                                    DateTime($invoice->due_date),"m/d/Y")}}</td>
                                <td id="inv-total-{{$invoice->id}}" style="text-align: right">{{$invoice->total_due}}
                                    USD</td>
                                <td id="inv-pm-{{$invoice->id}}" style="text-align: left">{{$invoice->payment->type->name}}</td>
                                <td id="inv-status-{{$invoice->id}}" @if($invoice->status_code->description == "Unpaid"
                                    || $invoice->status_code->description == "Voided") style="color:red" @else
                                    style="color:green" @endif>{{$invoice->status_code->description}}</td>
                                <td><a class="btn btn-default btn-sm" href="javascript:void(0);" onclick="showInvoice({{$invoice->id}})">View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="charges">
                    <table class="table datatables table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Sub Product</th>
                                <th>Frequency</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Billing Date</th>
                                <th>Last Bill Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recurring as $r)
                            <tr>
                                <td id="rec-product-{{$r->id}}">{{$r->sub_product->mainproduct->name}}</td>
                                <td id="rec-sub-product-{{$r->id}}">{{$r->sub_product->name}}</td>
                                <td id="rec-frequency-{{$r->id}}">{{$r->frequency}}</td>
                                <td id="rec-start-{{$r->id}}">{{date('m/d/Y', strtotime($r->start_date))}}</td>
                                <td id="rec-end-{{$r->id}}">{{date('m/d/Y', strtotime($r->end_date))}}</td>
                                <td id="rec-billdate-{{$r->id}}">{{date('m/d/Y', strtotime($r->bill_date))}}</d>
                                <td id="rec-lastbill-{{$r->id}}">{{date('m/d/Y', strtotime($r->last_bill_date))}}</td>
                                <td id="rec-amount-{{$r->id}}">{{$r->amount}}</td>
                                <td id="rec-status-{{$r->id}}" @if($r->status == "Active") style="color:green" @else
                                    style="color:red" @endif>{{$r->status}}</td>
                                <td><a class="btn btn-default btn-sm" href="javascript:void(0);" onclick="showRecurring({{$r->id}})">View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="payment-method">
                    <div class="row">
                        <div class="col-md-12">   
                            <div class="pull-right">
                                <button type="button" class="btn btn-primary" onclick="createPayment();">Create New Payment</button>
                            </div>
                        </div>
                    </div>
                        
                    @if(count($payment_methods) > 0)
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                                <div id="pn-list" class="list-group">
                                    @foreach($payment_methods as $row)
                                    <a href="{{ url('#'.$row['name']) }}" class="list-group-item" data-toggle="tab"
                                        aria-expanded="true"> {{$row['name']}} </a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-10">
                                <div class="tab-content no-padding">
                                    @foreach($payment_methods as $payment_method)
                                    <div class="tab-pane" id="{{ $payment_method['name'] }}">
                                        <div class="row mb-plus-20">
                                            <div class="row-header">
                                                <h3 class="title">{{$payment_method['name']}}</h3>
                                            </div>
                                            <table class="table datatables table-striped">
                                                <thead>
                                                    <tr>
                                                        {{-- <th>Type</th> --}}
                                                        @foreach($payment_method['header'] as $header)
                                                        <th>{{$header}}</th>
                                                        @endforeach
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($payment_method['details'] as $details)
                                                    <tr>
                                                        {{-- <td>{{$payment_method['name']}}</td> --}}
                                                        @foreach($payment_method['body'] as $body)
                                                        <td>{{$details->$body}}</td>
                                                        @endforeach
                                                        <td>
                                                            <input type="button" onclick="editPayment({{$details->id}})"
                                                                class="btn btn-success" value="Edit" />
                                                            <a href="/merchants/details/payment_method/{{$details->id}}/{{$id}}/cancel"
                                                                class="btn btn-danger" onclick="return confirm('Delete this payment?')">Delete
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
    </section>
    <div class="modal fade" id="goetu-billing" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">GoETU Billing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmPaymentMethod" name="frmPaymentMethod" method="post" enctype="multipart/form-data"
                        action="/merchants/updatepaymentmethod/{{$id}}">
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control" id="paymentMethodId" name="paymentMethodId" value="-1">
                        <div class="form-group">
                            <label>Select Payment Type</label>
                            <select class="form-control" id="txtPaymentType" name="txtPaymentType" />
                            @if(count($payment_types)>0)
                            @foreach($payment_types as $payment_type)
                            <option value="{{$payment_type->id}}">{{$payment_type->name}}</option>
                            @endforeach
                            @endif
                            </select>
                        </div>
                        <div id="divACH">
                            <div class="form-group">
                                <label>Bank Name:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="txtBankName" id="txtBankName" value="{{old('txtBankName')}}" />
                            </div>
                            <div class="form-group">
                                <label>Routing Number:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="txtRoutingNumber" id="txtRoutingNumber"
                                    value="{{old('txtRoutingNumber')}}" onkeypress="return isNumberKey(event)" />
                            </div>
                            <div class="form-group">
                                <label>Bank Account Number:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="txtBankAccountNumber" id="txtBankAccountNumber"
                                    value="{{old('txtBankAccountNumber')}}" onkeypress="return isNumberKey(event)" />
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="chkSetAsDefault" name="chkSetAsDefault" />
                            <label>Set as Default Payment</label>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary btn-save" id="btnSavePaymentType" name="btnSavePaymentType"
                        value="Create Payment">
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /.content -->
</div>



<div id="modalViewRecurring" class="modal" role="dialog">
    <div class="modal-dialog" style="max-width:800px">
        <form id="frmRecurringEdit" name="frmRecurringEdit" method="post" enctype="multipart/form-data" action="/merchants/update_recurring_invoice">
            {{ csrf_field() }}
            <input type="hidden" id="txtFrequencyId" name="txtFrequencyId">
            <input type="hidden" id="txtFrequencyStatus" name="txtFrequencyStatus">
            <input type="hidden" id="txtFrequencyPID" name="txtFrequencyPID" value="{{ $id }}">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recurring-header">Recurring Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-striped table-bordered table-condensed" id="order-product-detail">
                                <tr>
                                    <td>Product</td>
                                    <td id="rec-product"></td>
                                </tr>
                                <tr>
                                    <td>Sub Product</td>
                                    <td id="rec-sub-product"></td>
                                </tr>
                                <tr>
                                    <td>Frequency</td>
                                    <td id="rec-frequency"></td>
                                </tr>
                                <tr>
                                    <td>Total Due</td>
                                    <td id="rec-amount"></td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-sm-6">
                            <table class="table table-striped table-bordered table-condensed" id="order-product-detail">
                                <tr>
                                    <td>Status</td>
                                    <td id="recStatusSelect" name="recStatusSelect"></td>
                                </tr>
                                <tr>
                                    <td>Start Date</td>
                                    <td><input type="date" id="recStart" name="recStart" disabled></td>
                                </tr>
                                <tr>
                                    <td>End Date</td>
                                    <td><input type="date" id="recEnd" name="recEnd"></td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-body">
                    <table class="table  table-striped table-bordered table-condensed" id="rec-detail">
                        <thead>
                            <tr>
                                <th>Invoice#</th>
                                <th>Invoice Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    @if($canCharge)
                    <input type="button" class="btn btn-primary" value="Update" onclick="updateRecurring();">
                    <input type="button" class="btn btn-success" id="btnResume" value="Resume Billing" onclick="resumeRecurring();">
                    <input type="button" class="btn btn-danger" id="btnStop" value="Stop Billing" onclick="stopRecurring();">
                    @endif
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </form>
    </div>
    <!-- /.modal-dialog -->
</div>


<div id="modalViewInvoice" class="modal" role="dialog">
    <div class="modal-dialog" style="max-width:800px">
        <form id="frmInvoiceEdit" name="frmInvoiceEdit" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" id="txtInvoiceId" name="txtInvoiceId">
            <input type="hidden" id="txtInvoiceStatus" name="txtInvoiceStatus">
            <input type="hidden" id="txtPartnerId" name="txtPartnerId" value="{{ $id }}">
            <input type="hidden" id="txtPending" name="txtPending">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoice-header">Invoice #</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <table class="table table-striped table-bordered table-condensed" id="order-product-detail">
                                <tr>
                                    <td>Client Name</td>
                                    <td id="inv-client">Client Name</td>
                                </tr>
                                <tr>
                                    <td>Invoice Date</td>
                                    <td id="inv-date">1/1/2011</td>
                                </tr>
                                <tr>
                                    <td>Due Date</td>
                                    <td id="inv-due">1/1/2011</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-2">

                        </div>

                        <div class="col-sm-5">
                            <table class="table table-striped table-bordered table-condensed" id="order-product-detail">
                                <tr>
                                    <td>Total Due</td>
                                    <td id="inv-total">100.00 USD</td>
                                </tr>
                                <tr>
                                    <td>Payment Method</td>
                                    <td id="inv-pm">
                                        <select id="inv-payment" name="invpayment" >
                                            @if(count($payment_types)>0)
                                            @foreach($payment_types as $payment_type)
                                            <option value="{{$payment_type->id}}">{{$payment_type->name}}</option>
                                            @endforeach
                                            @endif
                                        </select>

                                    </td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td id="inv-status"></td>
                                </tr>
                                <tr id="payment-div">
                                    <td>Payment</td>
                                    <td><input id="inv-amt" name="paymentAmount" onkeypress="validate_numeric_input(event);" style="width:50%"> </td>
                                </tr>
                                <tr>
                                    <td>Amount Paid</td>
                                    <td id="inv-amt-paid"></td>
                                </tr>

                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-body">
                    <table class="table  table-striped table-bordered table-condensed" id="invoice-product-detail">
                        <thead>
                            <tr>
                                <th width="5%">Select</th>
                                <th width="20%">Category</th>
                                <th width="30%">Product</th>
                                <th width="10%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    @if($canVoid)
                    <input type="button" class="btn btn-primary btn-danger" id="btnVoid" value="Void" onclick="voidInvoice();">&nbsp;&nbsp;
                    @endif
                    @if($canPay)
                    <input type="button" class="btn btn-primary btn-primary" id="btnPayNow" value="Pay Now" onclick="payNow('P');">&nbsp;&nbsp;
                    <input type="button" class="btn btn-primary btn-danger" id="btnUnPay" value="Mark as Unpaid"
                        onclick="payNow('U');">&nbsp;&nbsp;
                     @endif
                    <a target="_blank" href="javascript:void(0);" class="btn btn-primary" id="show-pdf"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;View
                        PDF</a>
                </div>
            </div>
            <!-- /.modal-content -->
        </form>
    </div>
    <!-- /.modal-dialog -->
</div>

@endsection
@section('script')
<script src="{{ config("app.cdn") . "/js/merchants/merchant.js" . "?v=" . config("app.version") }}"></script>
<script src="{{ config("app.cdn") . "/js/merchants/invoice.js" . "?v=" . config("app.version") }}"></script>
<script>
    $(window).on('load', function () {
        $('div.list-group a').first().addClass('active');
    });
    $(window).on('load', function () {
        var id = $('#pm-list a').first().attr('id');
        $('#' + id).addClass('active');

        $('#payment-method .tab-pane').first().addClass('active');
    });
    arr = window.location.href.split('#');
    if (arr[1] != undefined) {
        $('#' + arr[1]).trigger('click');
    }
</script>
@endsection