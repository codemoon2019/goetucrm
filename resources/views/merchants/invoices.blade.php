@extends('layouts.app')

@section('content')
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
            </div>
            <div class="nav-tabs-custom">
            <ul class="tabs-rectangular">
                <li class="active"><a href="">Paid Invoices</a></li>
            </ul>
                <!-- Tabs within a box -->
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="history">
                        <table class="table datatables responsive table-condense table-striped table-bordered" id="leads-table">
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
                                    <td id="inv-date-{{$invoice->id}}">{{ date_format(new DateTime($invoice->invoice_date),"m/d/Y")}}</td>
                                    <td id="inv-due-{{$invoice->id}}">{{ date_format(new DateTime($invoice->due_date),"m/d/Y")}}</td>
                                    <td id="inv-total-{{$invoice->id}}" style="text-align: right">{{$invoice->total_due}} USD</td>
                                    <td id="inv-pm-{{$invoice->id}}" style="text-align: right">{{$invoice->payment->type->name}}</td>
                                    <td id="inv-status-{{$invoice->id}}" @if($invoice->status_code->description == "Unpaid" || $invoice->status_code->description == "Voided") style="color:red" @else style="color:green" @endif>{{$invoice->status_code->description}}</td>
                                    <td><a class="btn btn-default btn-sm" href="javascript:void(0);" onclick="showInvoice({{$invoice->id}})">View</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
        </section>
        <!-- /.content -->
    </div>


    <div id="modalViewInvoice" class="modal" role="dialog">
        <div class="modal-dialog" style="max-width:800px">
            <form id="frmInvoiceEdit" name="frmInvoiceEdit"  method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" id="txtInvoiceId" name="txtInvoiceId">
                <input type="hidden" id="txtInvoiceStatus" name="txtInvoiceStatus">

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

                            <div class="col-sm-4">
                                <table class="table table-striped table-bordered table-condensed" id="order-product-detail">
                                    <tr>
                                        <td>Total Due</td>
                                        <td id="inv-total">100.00 USD</td>
                                    </tr>
                                    <tr>
                                        <td>Payment Method</td>
                                        <td id="inv-pm">
                                            <select id="inv-payment" name="invpayment" class="form-control">
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
                        <input type="button" class="btn btn-primary btn-danger" id="btnVoid" value="Void" onclick="voidInvoice();">&nbsp;&nbsp;
                        <input type="button" class="btn btn-primary btn-primary" id="btnPayNow" value="Pay Now" onclick="payNow('P');">&nbsp;&nbsp;
                        <input type="button" class="btn btn-primary btn-danger" id="btnUnPay" value="Mark as Unpaid" onclick="payNow('U');">&nbsp;&nbsp;
                        <a target="_blank" href="javascript:void(0);" class="btn btn-primary" id="show-pdf"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;View PDF</a>
                    </div>
                </div>
                <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/merchants/merchant.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/merchants/invoice.js" . "?v=" . config("app.version") }}"></script>
@endsection