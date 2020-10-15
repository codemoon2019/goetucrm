@extends('layouts.app')

@section('style')
    @php 
        $involvedStatuses = [
            App\Models\MerchantStatus::BOARDING_ID,
            App\Models\MerchantStatus::FOR_APPROVAL_ID,
            App\Models\MerchantStatus::DECLINED_ID,
        ];
    @endphp

    @if (in_array($merchant->merchant_status_id, $involvedStatuses)) 
        @if (count($merchant->productOrders) > 0)
            <style>
                #product-list,
                #create-order,
                #order-history {
                    pointer-events: none !important;
                }
            </style>    
        @endif
    @endif
@endsection

@section('content')
    @php
        $access = session('all_user_access');
    @endphp
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Branch
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li><a href="/merchants/branch">Branch</a></li>
                <li class="active">{{$merchant->partner_company->company_name}}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                <h3>{{$merchant->partner_company->company_name}}</h3>
                <a href="/merchants/branch" class="btn btn-default pull-right" style="margin-top: -40px">Back to Branches</a>
            </div>
            <div class="nav-tabs-custom">
                @include("branch.details.branchtabs")
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    @if ($has_products)
                        <li class="{{$active_tab=="product-list" ? "active" : "" }}"><a href="#product-list" id="plist" data-toggle="tab" aria-expanded="true">Product List</a></li>
                        @if($canCreateOrder)
                            <li class="{{$active_tab=="create-order" ? "active" : "" }}"><a href="#create-order" id="corder" data-toggle="tab" aria-expanded="false">Product Order</a></li>
                        @endif
                    @endif
                    @if($canListOrder)
                    <li class="{{$active_tab=="order-history" ? "active" : "" }}"><a href="#order-history" id="history" data-toggle="tab" aria-expanded="false">Product History</a></li>
                    @endif
                </ul>
                <div class="tab-content no-padding">
                    @if($has_products)
                    <div class="tab-pane {{$active_tab=="product-list" ? "active" : "" }}" id="product-list">
                        @if(isset($products))
                            @foreach($products as $product)
                            <div class="panel box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="box-title"> {{$product->name}} </h4>
                                    <div class="box-tools pull-right">
                                        <a href="#collapseDoc{{$product->id}}" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion">
                                            <i class="fa fa-minus"></i>
                                        </a>
                                    </div>
                                </div>

                                <div id="collapseDoc{{$product->id}}" class="panel-collapse collapse">
                                    <div class="box-body">
                                        @foreach($product->categories as $cat)
                                        <label> {{$cat->name}} </label>
                                        <table class="table  table-condense table-striped">
                                            <thead>
                                                <td width="50%">Product Name</td>
                                                <!-- <td>Frequency</td> -->
                                                <td>SRP</td>
                                            </thead>
                                            <tbody>
                                                @foreach($product->subproducts as $sub)
                                                @if($sub->product_category_id == $cat->id)
                                                <tr>
                                                    <td>{{$sub->name}}</td>
                                                    <!-- <td>{{$sub->payment_frequency}}</td> -->
                                                    <td>{{$sub->srp}}</td>
                                                </tr>
                                                @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <br>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                            @endforeach


                            @foreach($products as $list)
                                @foreach($list->categories as $sub)
                                    <input type="hidden" class="mainprodcat-{{$list->id}}" id="cat-{{$sub->id}}" value="{{$sub->id}}" 
                                        data-name="{{$sub->name}}" data-desc="{{$sub->description}}" data-sel="{{$sub->single_selection}}" data-req="{{$sub->is_required}}">
                                @endforeach
                                @foreach($list->subproducts as $sub)
                                    <input type="hidden" class="mainprod-{{$list->id}}" id="prod-{{$sub->id}}" value="{{$sub->id}}" 
                                        data-name="{{$sub->code}} - {{$sub->name}}" data-cat="{{$sub->product_category_id}}" data-brate="{{$sub->srp}}" data-mrp="{{$sub->mrp}}" data-frequency="{{$sub->payment_frequency}}" data-pic="{{ url("storage/{$sub->display_picture}") }}">
                                @endforeach
                            @endforeach

                        @endif
                    </div>

                    <div class="tab-pane {{$active_tab=="create-order" ? "active" : "" }}" id="create-order">
                        <form id="frmMerchantOrder" name="frmMerchantOrder"  method="post" enctype="multipart/form-data" action="{{$formOrderUrl}}">
                            {{ csrf_field() }}
                        <input type="hidden" id="txtOrderDetails" name="txtOrderDetails">

                        <input type="hidden" id="txtPFEdit" name="txtPFEdit" @if($canEditPaymentFrequency) value="1" @else value="0" @endif>
                        <div class="row mb-plus-20">
                            @if(isset($products))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>STEP 1 </label><br><p>Select a Product:</p>
                                    <!-- <select class="form-control" id="prodSelection" name="prodSelection"> -->
                                        @foreach($products as $product)
                                            <input  type="checkbox" class="mainProd" id="mainProd-{{ $product->id }}" data-id="" data-name="{{ $product->name }}" value="{{ $product->id }}" data-sel="{{$product->single_selection}}">
                                            <label>{{  $product->name }}</label><br>
                                        <!-- <option value="{{$product->id}}" data-name="{{$product->name}}">{{$product->name}}</option> -->
                                        @endforeach
                                    <!-- </select> -->
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>STEP 2</label><br><p>Select a Category and Fill up Details:</p>
                                    <div id="divCategories">
                                        <div id="categoryList">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>STEP 3</label><br><p>Preferred Payment:</p>
                                    <div id="divPrefPayment">
                                        <select class="form-control select2 select2-hidden-accessible"
                                            style="width: 50%;" id="txtPreferredPayment" name="txtPreferredPayment"
                                            tabindex="-1" aria-hidden="true">
                                            @foreach($payment_types as $p)
                                            <option value="{{$p->name}}">{{$p->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

<!--                             <div class="col-md-12">
                                <div class="form-group">
                                    <label>STEP 3</label><br><p>Fill up Details:</p>
                                    <div id="divSubProducts">
                                    </div>
                                </div>
                            </div> -->
                            @php
                            if (isset($access['branch'])){ 
                                if(strpos($access['branch'], 'order billing id') !== false){ @endphp
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>OPTIONAL</label><br><p>Billing ID:</p>
                                        <input type="text" id="billing_id" name="billing_id" class="form-control col-md-4"/>
                                    </div>
                                </div>
                            @php }} @endphp
                            <div class="col-md-12 sm-col pull-right ta-right">
                                <button type="submit" class="pull-right btn btn-primary" id="btnCreateOrder" name="btnCreateOrder">Submit</button>
                            </div>
                            @endif
                        </div>
                        </form>
                    </div>
                    @endif

                    @if($canListOrder)
                    <div class="tab-pane {{$active_tab=="order-history" ? "active" : "" }}" id="order-history">
                        <table class="table datatables table-striped">
                            <thead>
                                <tr>
                                    @php
                                        if (isset($access['branch'])){ 
                                            if(strpos($access['branch'], 'work flow') !== false){ @endphp
                                            <th>Task</th>
                                    @php }} @endphp
                                    <th>Date</th>
                                    <th>Batch ID</th>
                                    <th>Order ID</th>
                                    <!-- <th>Billing ID</th> -->
                                    @php
                                        if (isset($access['branch'])){ 
                                            if(strpos($access['branch'], 'order billing id') !== false){ @endphp
                                            <th>Billing ID</th>
                                    @php }} @endphp
                                    <th style="width:400px">Product</th>
                                    <th style="width:200px">Application Status</th>
                                    <th style="width:100px">Product Status</th>
                                    <th>Edit</th>
                                    <th>Preview</th>
                                    @if($canSign)
                                    <th>Sign</th>
                                    @endif
                                    @if($canProcessOrder)
                                    <th>Process Order</th>
                                    @endif            
                                    @php
                                        if (isset($access['branch'])){ 
                                            if(strpos($access['branch'], 'request signature') !== false){ @endphp
                                            <th>Resend to Merchant</th>
                                    @php }} @endphp
                                    @php
                                        if (isset($access['branch'])){ 
                                            if(strpos($access['branch'], 'welcome email') !== false){ @endphp
                                            <th>Resend Welcome Email</th>
                                    @php }} @endphp


                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr style="font-size:14px">
                                    @php
                                        if (isset($access['branch'])){ 
                                            if(strpos($access['branch'], 'work flow') !== false){ @endphp
                                            <td><a href="\merchants\{{ $id }}\product-orders\{{ $order->id }}\workflow">
                                            <i class="fa fa-comment"></i></a></td>
                                    @php }} @endphp

                                    
                                    <td>{{$order->invoiceDate}}</td>
                                    <td>{{sprintf('%07d', $order->batch_id)}}</td>
                                    <td>{{$order->id}}</td>
                                    <!-- <td>{{$order->billing_id}}</td> -->
                                    @php
                                        if (isset($access['branch'])){ 
                                            if(strpos($access['branch'], 'order billing id') !== false){ @endphp
                                            <td>{{$order->billing_id}}</td>
                                    @php }} @endphp
                                    <td>{{$order->product->name}}</td>
                                    <td>{{$order->application_status}}</td>
                                    <td><label style="color: {{$order->task_status['color']}}"> {{$order->task_status['status']}} </label></td>
                                    <td>@if($order->status == 'Pending')
                                        <a href="javascript:void(0);" onclick="showOrder({{$order->id}})">
                                        <i class="fa fa-cog"></i>
                                        @endif
                                    </td>
                                    <td><a target="_blank" href="\merchants\{{$order->id}}\order_preview" >
                                    <i class="fa fa-file-pdf-o"></i></td>
                                    
                                    @if ($canSign)
                                        <td>
                                            @if ($order->status == 'Pending' || $order->status == 'PDF Sent')
                                                <a href="\merchants\{{$order->id}}\confirm_page">
                                                    <i class="fa fa-pencil"></i> 
                                                </a>
                                            @endif
                                        </td>
                                    @endif

                                    @if ($canProcessOrder)
                                        <td>
                                            @if ($order->status == 'Pending' || $order->status == 'PDF Sent')
                                                <a href="javascript:void(0);" onclick="processOrder({{$order->id}})">
                                                    <i class="fa fa-exchange"></i> 
                                                </a>
                                            @endif
                                        </td>
                                    @endif

                                    @php
                                        if (isset($access['branch'])){ 
                                            if(strpos($access['branch'], 'request signature') !== false){ @endphp
                                                <td>@if($order->status == 'Pending' || $order->status == 'PDF Sent')<a href="javascript:void(0);" onclick="SendEmail({{$order->id}}, '{{ $merchant->partner_company->email }}');">
                                                <i class="fa fa-send"></i> @endif</td>
                                    @php }} @endphp

                                    @php
                                        if (isset($access['branch'])){ 
                                            if(strpos($access['branch'], 'welcome email') !== false){ @endphp
                                                <td>@if(isset($order->welcomeEmail->id))<a href="javascript:void(0);" class="sendWelcomeEmail" onclick="SendWelcomeEmail({{$order->id}}, '{{ $merchant->partner_company->email }}');">
                                                <i class="fa fa-send"></i>@endif</td>
                                    @php }} @endphp

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>

    <div id="modalViewOrder" class="modal" role="dialog">
          <div class="modal-dialog" style="max-width:1000px">
            <form id="frmMerchantOrderEdit" name="frmMerchantOrderEdit"  method="post" enctype="multipart/form-data" action="{{$formOrderEditUrl}}">
            {{ csrf_field() }}
            <input type="hidden" id="txtOrderDetailsEdit" name="txtOrderDetailsEdit">
            <input type="hidden" id="txtOrderId" name="txtOrderId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="order-header">Product Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
              <div class="modal-body">
                <table class="table  table-striped" id="order-product-detail" style="font-size: 12px">
                    <thead>
                        <tr>
                            <th width="30%">Product</th>
                            <th width="30%">Image</th>
                            @if($canEditPaymentFrequency)
                            <th width="10%">Payment Frequency</th>
                            @endif
                            <th width="10%">Price</th>
                            @if($canEditPaymentFrequency)
                            <th width="10%">Start Date</th>
                            <th width="10%">End Date</th>
                            @endif
                            <th width="10%">Quantity</th>
                            <th width="10%">Amount</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
              </div>
              <div class="modal-footer">
                  <button type="submit" id="btnUpdatedOrder" name="btnUpdatedOrder" class="btn btn-primary btn-save">Save Order</button>
                <button type="button" id="btnCancelPLoad" name="btnCancelPLoad" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
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
    @if(!$has_products)
        alert("WARNING: No Product is available to this merchant's agent.");
        $('[href="#order-history"]').tab('show');
    @endif
    </script>
@endsection