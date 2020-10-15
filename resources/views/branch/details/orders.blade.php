@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Product Orders
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
                <li class=""><a href="/processFlow">Process Flow</a></li>
                <li class="active">Orders</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                <h3></h3>
                
            </div>

            <div class="clearfix"></div>

            <div class="col-md-6" hidden>
                <input type="text" class="form-control search-sys-usr" placeholder="Search Products...">
                <button class="btn btn-primary system-usr-srch-btn">Search</button>
            </div>

            <div class="col-md-12" style="margin-top:20px;">
                <table id="orders-list" name="orders-list" class="table responsive datatables table-condense p-0">
                    <thead>
                        <tr>
                            @php
                                if (isset($access['merchant'])){ 
                                    if(strpos($access['merchant'], 'work flow') !== false){ @endphp
                                    <th>Task</th>
                            @php }} @endphp
                            <th>Date</th>
                            @if (session('partner_type_id')!== App\Models\Partner::MERCHANT_ID)
                                <th style="width:200px">Partner</th>
                                <th style="width:200px">Merchant</th>
                            @endif
                            <th>Batch ID</th>
                            <th>Order ID</th>
                            <!-- <th>Billing ID</th> -->
                            @php
                                if (isset($access['merchant'])){ 
                                    if(strpos($access['merchant'], 'order billing id') !== false){ @endphp
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
                                if (isset($access['merchant'])){ 
                                    if(strpos($access['merchant'], 'request signature') !== false){ @endphp
                                    <th>Resend to Merchant</th>
                            @php }} @endphp
                            @php
                                if (isset($access['merchant'])){ 
                                    if(strpos($access['merchant'], 'welcome email') !== false){ @endphp
                                    <th>Resend Welcome Email</th>
                            @php }} @endphp
                        </tr>
                    </thead>
                </table>
                    
            </div>
        </section>
    </div>
@endsection

@section("script")
    <script src="{{ config("app.cdn") . "/js/merchants/orders.js" . "?v=" . config("app.version") }}"></script>
    <script>
       load_orders();
    </script>
@endsection