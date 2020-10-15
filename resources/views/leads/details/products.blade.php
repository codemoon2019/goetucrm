@extends('layouts.app')

@section('style')
<style type="text/css">
    .custom-fl-right{
        float: right;
    }

    .ta-right{
        text-align: right;
    }
    #post-comment select{
        display: inline-block;
        width: auto;
        height: 32px;
        padding: 4px 8px;
    }

    #post-comment .custom-fl-right, .comment-post-reply .custom-fl-right{
        margin-top: 5px;
    }

    .comment-view{
        margin-top: 0px !important;
    }

    .comment-view a{
        display: inline-block;
        vertical-align: top;
        padding: 1px 4px;
        background-color: #FFFFFF;
        margin-right: 5px;
        box-shadow: 0px 1px 4px #CDCDCD;
    }

    #comment-list .comment{
        margin: 10px 0;
        width: 100%;
        box-sizing: border-box;
        padding: 10px;
        background-color: #FFFFFF;
    }

    #comment-list .comment .comment-block{
        margin: 0 10px;
        padding: 10px 0;
        border-bottom: 1px solid #D7D7D7;
    }

    #comment-list .comment .comment-reply{
        margin-left: 30px;
        display: none;
    }

    #comment-list .comment .comment-block .comment-author{
        font-weight: 600;
    }

    #comment-list .comment .comment-block .comment-date{
        color:#3A3A3A;
    }

    #comment-list .comment .comment-block .comment-desc{
        padding-top: 6px;
    }

    #comment-list .comment .comment-options{
        padding: 10px;
    }

    #comment-list .comment .comment-options a{
        margin-right: 10px;
    }

    #comment-list .comment .comment-options a.showless, #comment-list .comment .comment-options a.cancelreply{
        display: none;
    }

    #comment-list .comment .comment-post-reply{
        display: none;
        margin: 0 10px;
        padding-top: 10px;
    }

    .discussion {
        box-shadow: 0px 0px 2px #E6E6E6;
    }
</style>
@endsection

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
                <li class="active">Lead</li>
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
                    {{ $calling_code }}{{ $partner_info[0]->phone1 }} <br/>
                    {{ $partner_info[0]->email }}
                </small>
                @endif
            </div>
            <div class="nav-tabs-custom">
                <ul class="tabs-rectangular">
                    @if($isInternal)
                    <li><a href="{{ url('leads/details/summary/'.$partner_id) }}">Summary</a></li>
                    @endif
                    <li><a href="{{ url('leads/details/profile/'.$partner_id) }}">Profile</a></li>
                    <li><a href="{{ url('leads/details/contact/'.$partner_id) }}">Contact</a></li>
                    <li><a href="{{ url('leads/details/interested/'.$partner_id) }}">Interested Products</a></li>
                    <!-- <li class="active"><a href="{{ url('leads/details/applications/'.$partner_id) }}">Applications</a></li> -->
                    <li><a href="{{ url('leads/details/appointment/'.$partner_id) }}">Appointment</a></li>
                </ul>
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    <li class="active"><a href="#info" data-toggle="tab" aria-expanded="true">Create Order</a></li>
                </ul>
                <div class="tab-content no-padding">
                    @if($isInternal)
                    <div class="tab-pane active" id="create-order">
                        <form id="frmMerchantOrder" name="frmMerchantOrder"  method="post" enctype="multipart/form-data" action="">
                            {{ csrf_field() }}
                        <input type="hidden" id="txtOrderDetails" name="txtOrderDetails">
                        <div class="row mb-plus-20">
                            @if(isset($products))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>STEP 1 </label><br><p>Select a Product:</p>
                                    <select class="form-control" id="prodSelection" name="prodSelection">
                                        @foreach($products as $product)
                                        <option value="{{$product->id}}">{{$product->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>STEP 2</label><br><p>Select a Category:</p>
                                    <div id="divCategories">
                                        <div id="categoryList">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>STEP 3</label><br><p>Fill up Details:</p>
                                    <div id="divSubProducts">
                                        <div id="subProductList">
                                            <table class="table  table-striped" id="order-details">
                                                <thead>
                                                    <tr>
                                                        <th width="30%">Product</th>
                                                        <th width="20%">Category</th>
                                                        <th width="20%">Payment Frequency</th>
                                                        <th width="10%">Quantity</th>
                                                        <th width="10%">Amount</th>
                                                        <th width="10%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 sm-col pull-right ta-right">
                                <button type="submit" class="pull-right btn btn-primary" id="btnProcessConvert" name="btnProcessConvert">Create Order</button>
                            </div>

                            @foreach($products as $list)
                                @foreach($list->categories as $sub)
                                    <input type="hidden" class="mainprodcat-{{$list->id}}" id="cat-{{$sub->id}}" value="{{$sub->id}}" 
                                        data-name="{{$sub->name}}">
                                @endforeach
                                @foreach($list->subproducts as $sub)
                                    <input type="hidden" class="mainprod-{{$list->id}}" id="prod-{{$sub->id}}" value="{{$sub->id}}" 
                                        data-name="{{$sub->name}}" data-cat="{{$sub->product_category_id}}" data-brate="{{$sub->amount}}" data-frequency="{{$sub->payment_frequency}}">
                                @endforeach
                            @endforeach
                            
                            @endif
                        </div>
                        </form>
                    </div>
                    @endif
                </div>
        </section>
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/leads/list.js" . "?v=" . config("app.version") }}"></script>
@endsection