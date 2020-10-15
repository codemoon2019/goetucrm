@extends('layouts.app')

@section('content')
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
                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                    <li class="active"><a href="#commission-setup" data-toggle="tab" aria-expanded="false">Commission Setup</a></li>
                </ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="commission-setup">
                        <div class="content" style="padding-bottom:5px !important" id="commission-body">
                        @php $prev_prod = ""; @endphp
                        @foreach($products as $product)
                            @php $curr_prod = $product->main_id @endphp
                            @if( $curr_prod  != $prev_prod)
                            <div id="form-{{$product->main_id}}"> 
                                <div class="accordion-head" id="head-{{$product->main_id}}">
                                    <h4>{{$product->main_product}}</h4>
                                    <div class="pull-right" style="float:right;">
                                    <button type="button" class="btn btn-primary btn-sm fa fa-pencil" style="margin: 2px" onclick="editAllSubProduct({{$id}},{{$product->main_id}},'{{$product->main_product}}')"></button>
                                    </div>
                                </div>
                                <div class="content">
                                    <div class="box-group" id="accordion-{{$product->main_id}}">
                                        <div class="panel box box-primary" id="cat-container-{{$product->main_id}}">
                                        @php $prev_cat = ""; @endphp
                                        @foreach($products as $cat)
                                            @if($cat->main_id == $product->main_id)
                                                @php $curr_cat = $cat->cat_id @endphp
                                                @if($curr_cat != $prev_cat)

                                                    
                                                    <div class="box-header with-border main-prod-div-{{$cat->main_id}}" id="category-div-{{$cat->cat_id}}">
                                                        <h4 class="box-title">{{$cat->category}}</h4>
                                                        <div class="box-tools pull-right">
                                                            <a href="#collapseOne-{{$cat->cat_id}}" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion-{{$cat->cat_id}}"><i class="fa fa-minus"></i></a>
                                                        </div>
                                                    </div>

                                                    <div id="collapseOne-{{$cat->cat_id}}" class="panel-collapse collapse in show">
                                                        <div class="box-body">
                                                            <table class="table table-condense table-striped" id="category-table-{{$cat->cat_id}}">
                                                                <thead>
                                                                <tr>
                                                                    <td style="width:50%">Product Name</td>
                                                                    <td style="width:40%">Commission Type</td>
                                                                    <td style="width:10%">Actions</td>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                 @foreach($products as $sub)
                                                                    @if($sub->main_id == $product->main_id && $sub->cat_id == $cat->cat_id)
                                                                    <tr>
                                                                        <td >{{$sub->sub_product}}</td>
                                                                        <td class="commission-{{$sub->main_id}}" id="commission-type-{{$sub->sub_id}}">@if($sub->type == "" || $sub->type == 'none') No Commission @endif
                                                                            @if($sub->type == 'fixed') Fixed Percentage @endif
                                                                            @if($sub->type == 'based') Percent based on Cases @endif</td>
                                                                        <td ><button type="button" class="btn btn-primary btn-sm fa fa-pencil" style="margin: 2px"  onclick="editRow({{$id}},{{$sub->sub_id}},'{{$sub->sub_product}}')"></button></td>
                                                                    </tr>
                                                                    @endif
                                                                 @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
      
                                            @endif
                                            @php $prev_cat = $cat->cat_id @endphp
                                        @endif
                                        @endforeach
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @php $prev_prod = $product->main_id @endphp
                        @endforeach
                        </div>
                    </div>
                </div>
        </section>
    </div>





    <div class="modal fade" id="commissionAndRates" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="frmCommission" name="frmCommission"  method="post" enctype="multipart/form-data">
                <input type="hidden" id="partnerId" name="partnerId">
                <input type="hidden" id="productId" name="productId">
                <input type="hidden" id="commissionBased" name="commissionBased">
                <input type="hidden" id="applyAll" name="applyAll">
                <div class="modal-header">
                    <h5 class="modal-title">Setup Commissions & Rates</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="commissionModalLoading">
                    <label> Loading Data.....</label>
                </div>
                <div class="modal-body" id="commissionModal" style="display:none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="commissionType"><strong>Commission Type:</strong></label>
                                <select id="commissionType" name="commissionType" class="form-control">
                                    <option value="none">No Commission</option>
                                    <option value="fixed">Fixed Percentage</option>
                                    <option value="based">Percent based on Cases</option>
                                </select>
                            </div>
                            <div class="form-group" id="divFixedPercentage" style="display:none;">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save" id="btnSaveCommission">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/partners/commission.js" . "?v=" . config("app.version") }}"></script>
@endsection
