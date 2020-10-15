@extends('layouts.app')

@section('content')
                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                @php 
                    $access = session('all_user_access'); 
                    $canEdit = false;
                    if(array_key_exists(strtolower($partner_info->partner_type_description),$access)){
                        if(strpos($access[strtolower($partner_info->partner_type_description)], 'edit') !== false){ 
                            $canEdit = true;
                        } 
                    } 
                @endphp


    <style type="text/css">
        
    .table {
        font-size: 12px;
    }

    .table td, .table th {
         padding: .25rem; 
         vertical-align: top; 
         border-top: none; 
    }
    .table thead td {
        border-bottom:  1px solid #e9ecef; 
        background-color: #3c8dbc;
        color:white;
    }
    .table-val-name{
        font-weight: bold;
    }

    </style>

          
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    <li class="active"><a href="#company-info" data-toggle="tab" aria-expanded="true">Product Rates</a></li>
                    <li class=""><a href="#product-list" data-toggle="tab" aria-expanded="false">Product List</a></li>
                    <li class=""><a href="#order-list" data-toggle="tab" aria-expanded="false">Order List</a></li>
                    <li class=""><a href="#inventory" data-toggle="tab" aria-expanded="false">Inventory</a></li>
                </ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="company-info">
                        <form id="frmProductFee" name="frmProductFee"  method="post" enctype="multipart/form-data" action="{{$formUrl}}">
                        {{ csrf_field() }}
                        <input type="hidden" id="txtDetail" name="txtDetail">
                        <input type="hidden" id="asTemplate" name="asTemplate">
                        <input type="hidden" id="txtTemplateName" name="txtTemplateName">
                        <input type="hidden" id="txtTemplateDescription" name="txtTemplateDescription">
                        @if($canEdit)
                        <span class="pull-left">
                            @if($is_original_user==0)
                            <a href="#" class="btn btn-flat btn-sm btn-github" data-toggle="modal" data-target="#selectProduct">Add Product</a>
                            
                                &nbsp;
                                <span class="pull-right">
                                    <button type="button" class="btn btn-flat btn-sm btn-success" id="btnUpdateProduct" >Save Product</button>
                                </span>
                            @endif
                        </span>
                        <span class="pull-right">
                            @if($is_admin || $userType == 'COMPANY')
                                @if($is_original_user==0)
                                <a href="#" class="btn btn-flat btn-sm btn-github" data-toggle="modal" data-target="#modalSaveAsProductTemplate">Save as Product Template</a>
                                @endif
                            @endif
                            @if($is_original_user==0)
                            <a href="#" class="btn btn-flat btn-sm btn-github" data-toggle="modal" data-target="#modalProductTemplateSelection">Load Product Template</a>
                            @endif
                        </span>
                        @endif
                        <div class="clearfix"></div>
                        <div class="content" style="padding-bottom:5px !important" id="template-body">
                            @if(isset($partner_products))
                                @foreach($products as $product)
                                    <div id="form-{{$product->id}}"> 
                                        <div class="accordion-head" id="head-{{$product->id}}">
                                            <h4>{{$product->name}}</h4>
                                            @if($is_original_user==0)
                                            <div class="pull-right" style="float:right;">
                                            <button type="button" class="btn btn-primary btn-sm fa fa-pencil" style="margin: 2px" onclick="editAllSubProduct({{$product->id}},'{{$product->name}}')"></button>
                                            <button type="button" href="#" class="btn btn-danger btn-sm fa fa-trash" style="margin: 2px" onclick="deleteAllSubProduct({{$product->id}},'{{$product->name}}')"></button>
                                            </div>
                                            @endif
                                        </div>          

                                        <div class="content">
                                            <div class="box-group" id="accordion-{{$product->id}}">
                                                <div class="panel box box-primary" id="cat-container-{{$product->id}}">
                                                    @foreach($product->categories as $category)
                                                        @php $include_cat = false @endphp
                                                        @foreach ($partner_products as $item)
                                                            @if($item->product->product_category_id == $category->id)
                                                                @php $include_cat = true @endphp
                                                            @endif
                                                        @endforeach
                                                        @if($include_cat)
                                                            <div class="box-header with-border main-prod-div-{{$product->id}}" id="category-div-{{$category->id}}">
                                                                <h4 class="box-title">{{$category->name}}</h4>
                                                                <div class="box-tools pull-right">
                                                                    <a href="#collapseOne-{{$category->id}}" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion-{{$category->id}}"><i class="fa fa-arrow-down"></i></a>
                                                                </div>
                                                            </div>
                                                            <div id="collapseOne-{{$category->id}}" class="panel-collapse collapse in show">
                                                                <div class="box-body">
                                                                    <table class="table table-condense table-striped" id="category-table-{{$category->id}}">
                                                                        <thead>
                                                                        <tr>
                                                                            <td style="width:20%">Product Name</td>
                                                                            <td style="width:10%">Cost ($)</td>
                                                                            <!-- <td style="width:10%">First Buy Rate</td> -->
                                                                            <!-- <td style="width:10%">2nd Buy Rate</td> -->
                                                                            @if($is_original_user==0)<td style="width:10%">Buy Rate ($)</td>@endif                               
                                                                            <td style="width:10%">Payment Frequency</td>
                                                                            <!-- <td style="width:10%">Split Type</td> -->
                                                                            <td style="width:10%">Mark Up Split Percentage</td>
                                                                            <td style="width:10%">SRP ($)</td>
                                                                            @if($is_original_user==0)
                                                                            <td style="width:10%">Actions</td>
                                                                            @endif
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody> 
                                                                        @foreach ($partner_products as $item)
                                                                            @if($item->product->product_category_id == $category->id)
                                                                                <tr class="subproductrecord cat-table-{{$category->id}}" id="table-prod-{{$item->product_id}}">
                                                                                    <td class="table-val-name">{{$item->product->name}}</td>
                                                                                    <td class="table-val-buy_rate" style="display: none;">@if($is_original_user==0){{number_format((float)$item->buy_rate, 2, '.', '')}}@else @if($item->split_type=="Second Buy Rate"){{number_format((float)$item->other_buy_rate, 2, '.', '')}}@else{{number_format((float)$item->downline_buy_rate, 2, '.', '')}}@endif @endif</td>
                                                                                    <td class="table-val-cost">{{number_format((float)$item->cost, 2, '.', '')}}</td>
                                                                                    @if($is_original_user==0)<td class="table-val-downline_buy_rate" @if($item->split_type == 'Second Buy Rate') style="display: none;" @endif >@if($is_original_user==0){{number_format((float)$item->downline_buy_rate, 2, '.', '')}}@else - @endif</td>@endif
                                                                                    <td class="table-val-second_buy_rate" @if($item->split_type == 'First Buy Rate') style="display: none;" @endif >@if($is_original_user==0){{number_format((float)$item->other_buy_rate, 2, '.', '')}}@else - @endif</td>
                                                                                    <td class="table-val-frequency">{{$item->payment_frequency}}</td>
                                                                                    <td class="table-val-split_type" style="display: none;">{{$item->split_type}}</td>
                                                                                    <td class="table-val-split_percentage_text">
                                                                                        @if($item->is_split_percentage == 1) 
                                                                                        Upline: {{number_format((float)$item->upline_percentage, 2, '.', '')}} % <br> Downline: {{number_format((float)$item->downline_percentage, 2, '.', '')}}  %
                                                                                        @else NO @endif 
                                                                                    </td>
                                                                                    <td class="table-val-srp" >{{$item->srp}}</td>
                                                                                    @if($is_original_user==0)
                                                                                    <td><button type="button" class="btn btn-primary btn-sm fa fa-pencil" style="margin: 2px"  onclick="editRow({{$item->product_id}})"></button><button  type="button" class="btn btn-danger btn-sm fa fa-trash" style="margin: 2px" onclick="deleteRow(this,'{{$category->id}}','{{$product->id}}','{{$item->product_id}}')"></button></td>
                                                                                    @endif
                                                                                    <td class="table-val-upline_percent" style="display: none;">{{number_format((float)$item->upline_percentage, 2, '.', '')}}</td>
                                                                                    <td class="table-val-downline_percent" style="display: none;">{{number_format((float)$item->downline_percentage, 2, '.', '')}}</td>
                                                                                    <td class="table-val-pricing_option" style="display: none;">{{$item->pricing_option}}</td>
                                                                                    <td class="table-val-price" style="display: none;">{{number_format((float)$item->price, 2, '.', '')}}</td>
                                                                                    @if($item->is_split_percentage == 1)
                                                                                    <td class="table-val-split_percentage" style="display: none;">YES</td>
                                                                                    @else
                                                                                    <td class="table-val-split_percentage" style="display: none;">NO</td>
                                                                                    @endif
                                                                                    <td class="table-val-payment_frequency" style="display: none;">{{$item->frequency->id}}</td>
                                                                                    <td class="table-val-commission_type" style="display: none;">{{$item->commission_type}}</td>
                                                                                    <td class="table-val-commission" style="display: none;">@if($item->commission_type == 'fixed') {{number_format((float)$item->commission_fixed, 2, '.', '')}}@else{{$item->commission_based}}@endif</td>
                                                                                    <td class="table-val-cost_multiplier" style="display: none;">{{$item->cost_multiplier}}</td>
                                                                                    <td class="table-val-mrp" style="display: none;">{{$item->mrp}}</td>
                                                                                    <td class="table-val-cm_value" style="display: none;">{{$item->cost_multiplier_value}}</td>
                                                                                    <td class="table-val-cm_type" style="display: none;">{{$item->cost_multiplier_type}}</td>
                                                                                    <td class="table-val-bonus" style="display: none;">{{$item->bonus}}</td>
                                                                                    <td class="table-val-bonus_type" style="display: none;">{{$item->bonus_type}}</td>
                                                                                    <td class="table-val-bonus_amount" style="display: none;">{{$item->bonus_amount}}</td>
                                                                                    
                                                                                    <td class="table-val-sub_product_id" style="display: none;">{{$item->product_id}}</td>
                                                                                </tr>
                                                                                @if(count($item->modules) > 0)
                                                                                <tr id="table-prod-mod-{{$item->product_id}}">
                                                                                    <td></td>
                                                                                    <td colspan="7">
                                                                                        @foreach($item->modules as $mod)
                                                                                            <table style="display: inline-table;width:390px" >
                                                                                                <tr>
                                                                                                    <td class="td-checkbox" width="10%"><input type="checkbox" id="use-module-{{$mod->product_module_id}}" @if($mod->status == 'A')checked @endif></td>
                                                                                                    <td class="table-name" width="60%">{{str_replace("&amp;", "&", $mod->name)}}<input id="module-name-{{$mod->product_module_id}}" value="{{str_replace("&amp;", "&", $mod->name)}}" type="hidden"><input id="module-type-{{$mod->product_module_id}}" value="{{$mod->type}}" type="hidden"></td>
                                                                                                    <td>@if($mod->type == 'checkbox')
                                                                                                            <select id="module-{{$mod->product_module_id}}"><option value="yes">Yes</option value="no" @if($mod->value =='no') selected @endif><option>No</option></select>
                                                                                                        @else
                                                                                                        <input style="width:50%" name="module-{{$mod->product_module_id}}" id="module-{{$mod->product_module_id}}" value="{{$mod->value}}" type="text">
                                                                                                        @if($mod->type == 'percentage')(%)@endif
                                                                                                        @endif
                                                                                                    </td>
                                                                                                </tr>                                                                       
                                                                                            </table>
                                                                                        @endforeach
                                                                                    </td>
                                                                                </tr>
                                                                                @endif

                                                                            @endif
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="content" style="min-height:50px">
                            @if($is_original_user==0)
                            <span class="pull-right">
                            <button type="button" class="btn btn-flat btn-sm btn-success" id="btnUpdateProduct" >Save Product</button>
                            </span>
                            @endif
                        </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="product-list">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Product List</h3>
                            </div>
                            <table class="table datatables table-striped">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>MID</th>
                                    <th>Company</th>
                                    <th>Agent</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="order-list">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Order List</h3>
                            </div>
                            <div class="col-sm-6 mb-plus-20">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label> Start Date: </label>
                                        <input name="startdate" id="startdate" value="{{$startDate}}" class="form-control" type="text">
                                    </div>
                                    <div class="col-md-4">
                                        <label> End Date: </label>
                                        <input name="enddate" id="enddate" value="{{$endDate}}" class="form-control" type="text">
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp;</label>
                                        <!-- <input name="submit" value="Generate" class="btn btn-danger form-control" onclick="generateOrderList({{$id}})" type="submit"> -->
                                        <a href="javascript:void(0);" class="btn btn-danger form-control" onclick="generateOrderList({{$id}});">Generate</a>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <div class="col-md-12">
                                <table id="tblSearchApplicationList" class="table datatables table-striped">
                                    <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>MID</th>
                                        <th>Company</th>
                                        <th>Date</th>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Application Status</th>
                                        <th>Product Status</th>
                                        <th>Agent</th>
                                        <th>View</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="tab-pane" id="inventory">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Inventory</h3>
                            </div>
                            <table class="table datatables table-striped">
                                <thead>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Sub Product</th>
                                    <th>Quantity</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="selectProduct" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped" id="tblProductList">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Description</th>
                                <th><input type="checkbox" id="allcb" name="allcb" class="all_check_box" /></th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($productList as $list)
                                <tr>
                                    <td>{{$list->name}}</td>
                                    <td>{{$list->description}}</td>
                                    <td><input type="checkbox" id="{{$list->id}}" name="{{$list->id}}" value="{{$list->name}}"></td>
                                </tr>       
                            @endforeach
                            
                        </tbody>
                    </table>
                    @foreach($productList as $list)
                        @foreach($list->categories as $sub)
                            <input type="hidden" class="mainprodcat-{{$list->id}}" id="cat-{{$sub->id}}" value="{{$sub->id}}" 
                                data-name="{{$sub->name}}">
                        @endforeach
                        @foreach($list->subproducts as $sub)
                            @if($sub->status == "A")
                            <input type="hidden" class="mainprod-{{$list->id}}" id="prod-{{$sub->id}}" value="{{$sub->id}}" 
                                data-name="{{$sub->name}}" data-cat="{{$sub->product_category_id}}" data-brate="{{$sub->amount}}">
                            @endif
                            @foreach($sub->modules as $mod)
                                @if($mod->status == "A")
                                    <input type="hidden" class="subprod-{{$mod->product_id}}" id="mod-{{$mod->id}}" value="{{$mod->id}}" 
                                data-name="{{$mod->name}}" data-val="{{$mod->value}}" data-type="{{$mod->type}}">
                                @endif
                            @endforeach                
                        @endforeach
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save" id="btnAddProduct">Add Product</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="commissionAndRates" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Setup Commissions & Rates
                        <a href="#" class="info-circle" title="Product Setup">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" id="txtSubProductId" value="">
                            <input type="hidden" id="txtProductId" value="">
                            <div class="form-group">
                                <label><strong>Product:</strong></label>
                                <input type="text" class="form-control" name="txtSubProductName" id="txtSubProductName" value="" readonly/>
                            </div>
                            <div class="form-group">
                                <label><strong>Cost:</strong></label>
                                    <div class="input-group">
                                        <label for="txtSubProductCost" class="input-group-addon">$</label>
                                        <input type="text" class="form-control" name="txtSubProductCost" id="txtSubProductCost" value="" readonly/>
                                    </div>
                            </div>
                            <div class="form-group">
                                <label><strong>Payment Frequency:</strong></label>
                                <select id="txtPaymentFrequency" name="txtPaymentFrequency" class="form-control">
                                @foreach($frequency as $list)
                                    <option value="{{$list->id}}">{{$list->name}}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="form-group" >
                                <div class="form-group">
                                    <label><strong>SRP:</strong></label>
                                    <div class="input-group">
                                        <label for="txtSRP" class="input-group-addon">$</label>
                                        <input type="text" class="form-control" name="txtSRP" id="txtSRP" value="0.00" onkeypress="validate_numeric_input(event);"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><strong>MRP:</strong></label>
                                    <div class="input-group">
                                        <label for="txtMRP" class="input-group-addon">$</label>
                                        <input type="text" class="form-control" name="txtMRP" id="txtMRP" value="0.00" onkeypress="validate_numeric_input(event);"/>
                                    </div>

                                </div>                                
                            </div>
                            <div class="form-group" style="display: none;">
                                <label><strong>Downline Pricing Options:</strong></label><br/>
                                <input type="radio" class="" name="pricing_option" id="pricing_optionSRP" value="SRP" checked/>
                                <label>SRP</label>
                                <input type="radio" class="" name="pricing_option" id="pricing_optionMRP" value="MRP"/>
                                <label>MRP</label>
                                <div class="form-group">
                                    <label><strong>Price:</strong></label>
                                    <input type="text" class="form-control" name="txtPrice" id="txtPrice" value="0.0000" onkeypress="validate_numeric_input(event);"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label><strong>Split Type:</strong></label>
                                <select id="txtMarkUpType" name="txtMarkUpType" class="form-control">
                                @foreach($markUp as $list)
                                    <option value="{{$list->id}}">{{$list->name}}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label><strong>Buy Rate:</strong></label>
                                <div class="input-group">
                                    <label for="txtBuyRate" class="input-group-addon">$</label>
                                    <input type="text" class="form-control" name="txtBuyRate" id="txtBuyRate" value="0.00" onkeypress="validate_numeric_input(event);"/>
                                </div>
                            </div>
                            <div class="form-group" id="div2ndBrate" style="display:none;">
                                <label><strong>Second Buy Rate:</strong></label>
                                <div class="input-group">
                                    <label for="txtSecondBuyRate" class="input-group-addon">$</label>
                                    <input type="text" class="form-control" name="txtSecondBuyRate" id="txtSecondBuyRate" value="0.00" onkeypress="validate_numeric_input(event);"/>
                                </div>

                            </div>

                            <div class="form-group">
                                <input type="checkbox" class="" name="" id="chkSplit" name="chkSplit"/>
                                <label for="productName"><strong>Mark Up Split Percentage</strong></label>
                                <div class="row" id="divPercentageSplit" name="divPercentageSplit" style="display:none;">
                                    <div class="col-md-6">
                                        <label><strong>Upline %</strong></label>
                                        <div class="input-group">
                                            <input type="text" id="txtUpPercentage" name="txtUpPercentage" class="form-control" value="0.00"  onkeypress="validate_numeric_input(event);">
                                            <label class="input-group-addon">%</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label><strong>Downline %</strong></label>
                                        <div class="input-group">
                                            <input type="text" id="txtDownPercentage" name="txtDownPercentage" class="form-control" value="0.00"  onkeypress="validate_numeric_input(event);">
                                            <label class="input-group-addon">%</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <div class="form-group" >
                                    <input type="checkbox" class="" name="" id="chkBonus" name="chkBonus"/>
                                    <label> <strong>Bonus</strong></label>
                                    <div class="row" id="bonusDiv" style="display:none;">
                                        <div class="form-group col-md-6">
                                            <label><strong>Bonus Type:</strong></label>
                                        <select id="BonusType" name="BonusType" class="form-control">
                                            <option value="percentage">Percentage</option>
                                            <option value="fixed">Fixed Value</option>
                                        </select>
                                        </div>

                                         <div class="form-group col-md-6" id="divPercentBonus" >
                                            <label><strong>Bonus (%):</strong></label>
                                            <div class="input-group">
                                                <input type="text" id="percentageBonus" name="percentageBonus" class="form-control" value="0.00"  onkeypress="validate_numeric_input(event);">
                                                <label class="input-group-addon">%</label>
                                            </div>
                                        </div>
                                         <div class="form-group col-md-6" id="divFixedBonus" style="display:none;">
                                            <label><strong>Fixed Bonus:</strong></label>                                   
                                            <div class="input-group">
                                                <label for="fixedBonus" class="input-group-addon">$</label>
                                                <input type="text" class="form-control" name="fixedBonus" id="fixedBonus" value="0.00" onkeypress="validate_numeric_input(event);" />
                                            </div>

                                        </div>
                                    </div>

                            </div>


                                <div class="form-group" style="display:none;">
                                    <label>Buy Rate Commission</label>
                                    <label for="productName"><input type="checkbox" class="" name="" id="chkCostMultiplier" name="chkCostMultiplier"/>
                                    <strong><span id="lblBuyRate">0.00</span></strong>&nbsp;&nbsp;&nbsp;X&nbsp;&nbsp;&nbsp;<input type="text" id="CMValue" name="CMValue" class="form-control" style="display:inline;width:25%" value="0.00"  onkeypress="validate_numeric_input(event);">

                                    <select id="CMType" name="CMType" class="form-control" style="display:inline;width:40%" >
                                        <option value="percentage">Percentage</option>
                                        <option value="fixed">Fixed Value</option>
                                    </select>

                                </label>

                            </div>
                            <div style="display: none;">
                            <div class="form-group">
                                <label for="commissionType"><strong>Commission Type:</strong></label>
                                <select id="commissionType" name="commissionType" class="form-control">
                                    <option value="fixed">Fixed Percentage</option>
                                    <option value="based">Percent based on Cases</option>
                                </select>
                            </div>
                            <div class="form-group" id="divFixedPercentage">
                                <label for="productDescription"><strong>Commission(%):</strong></label>
                                <input type="text" class="form-control" name="fixedCommission" id="fixedCommission" value="0.00" onkeypress="validate_numeric_input(event);" />
                            </div>
                            <div class="form-group cost-group" id="divPercentBased" style="display:none;">
                                <table class="table cost-table table-condensed table-striped" id="commission-case-table">
                                    <tr class="bg-primary">
                                        <th colspan="3">Cases</th>
                                        <th>Commission(%)</th>
                                        <th>Action</th>
                                    </tr>
                                </table>
                                <a href="#" onclick="add_commision_case()"><i class="fa fa-plus-circle"></i> Add Cases</a>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save" id="btnEditSubProduct">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalSaveAsProductTemplate" class="modal" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Save as Template</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
              <div class="modal-body">
                
                    <label>Template Name:</label>
                    <input type="text" id="newTemplateName" name="newTemplateName" class="form-control" value="">
               
                    <label>Description:</label>
                    <textarea class="form-control noresize" id="newTemplateDescription" name="newTemplateDescription" ></textarea>

            </div>
              <div class="modal-footer">
                  <button type="button" id="btnSaveAsTemplate" name="btnSaveAsTemplate" class="btn btn-primary btn-save">Save Template</button>
                <button type="button" id="btnCancelPLoad" name="btnCancelPLoad" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
    </div>

    <div id="modalProductTemplateSelection" class="modal" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">SELECT PRODUCT</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <table id="tblListProductTemplate" name="tblListProductTemplate"  class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Description</th>
                                                <th>Select</th>
                                            </tr>
                                        </thead>
                                          <tbody>
                                            <div class="radio " id="product-templates">
                                                @foreach($product_templates as $list)
                                                    <tr>
                                                        <td>{{$list->name}}</td>
                                                        <td>{{$list->description}}</td>
                                                        <td><input type="radio" name="prod-fee-template" value="{{$list->id}}"></td>
                                                    </tr>
                                                @endforeach
                                            </div>
                                            </tbody>
                                    </table>
                                </div>                            
                                
                            </div>
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
                  <button type="button" id="btnLoadProdTemplate" name="btnLoadProdTemplate" class="btn btn-primary btn-save">Load Template</button>
                <button type="button" id="btnCancelPLoad" name="btnCancelPLoad" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
    </div>

@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/partners/product.js" . "?v=" . config("app.version") }}"></script>
    <script>
        $(function () {
            var popoverTemplate = "";
            popoverTemplate += '<div class="popover" role="tooltip" style="max-width: 676px">';
            popoverTemplate += '    <div class="arrow" style="margin-left: 10px;"></div>';
            popoverTemplate += '    <p class="popover-header"></p>';
            popoverTemplate += '    <div class="popover-content p-3">';
            popoverTemplate += '        <div class="row">';
            popoverTemplate += '            <div class="col-md-6">';
            popoverTemplate += '                <p><strong>Product : </strong> is the item offered for sale </p>';
            popoverTemplate += '                <p><strong>Payment Frequency : </strong> is used to determine how often the payment will be made</p>';
            popoverTemplate += '                <p><strong>Cost : </strong> Dollar Value of the products or services for which the UPLINE needs to buy.</p>';

            popoverTemplate += '                <p><strong>Split Type</strong></p>';
            popoverTemplate += '                <p> - First Buy Rate: Upline initial selling price of the product. Uses Buy Rate Field </p>';
            popoverTemplate += '                <p> - Second Buy Rate:  Upline secondary selling price of the product. Uses Second Buy Rate Field </p>';

            popoverTemplate += '                <p><strong>Mark Up Split Percentage : </strong> The profit sharing of the upline and downline based on percentage.</p>';
            popoverTemplate += '                <p><strong>Buy Rate Commission : </strong> Part of commission scheme based on Buy Rate. Currently set as Buy Rate * .30 as default value.</p>';
            popoverTemplate += '            </div>';

            popoverTemplate += '            <div class="col-md-6">';
            popoverTemplate += '                <p><strong>How to compute Mark Up Split Percentage? </strong></p>';
            popoverTemplate += '                <p>(Selling price of downline - selling price of upline) * Split Percentage</p>';
            popoverTemplate += '                <p>For instance if Product A was sold by Upline to its Downline at $100 then Downline sells it to Merchant at $150 and they split it to 50%, the value should be like:</p>';
            popoverTemplate += '                <p>($150 - $100) * .5 <br/>= 50 * .5 <br/>= 25</p>';
            popoverTemplate += '                <p>both Upline and downline  should receive $25 dollars each.</p>';

            popoverTemplate += '                <p><strong>How to compute the commission? </strong></p>';
            popoverTemplate += '                <p>The formula for commission is Mark Up Split Percentage + (30% of Buy Rate if the Merchant who buys the product is a Credit Card Client, otherwise 15% of Buy Rate)</p>';
            popoverTemplate += '                <p>(25) + (100 * .3)<br/> =85 (Credit Card Client)</p>';
            popoverTemplate += '                <p>(25) + (100 * .15)<br/> =70 (Non-Credit Card Client)</p>';
            popoverTemplate += '            </div>';
            popoverTemplate += '        </div>';
            popoverTemplate += '    </div>';
            popoverTemplate += '</div>';

            $('.info-circle').popover({
                template: popoverTemplate,
                placement: 'bottom'
            });
        })
    </script>
@endsection
