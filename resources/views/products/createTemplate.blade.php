@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                {{$headername}}
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Partners</a></li>
                <li><a href="#">Templates</a></li>
                <li class="active">{{$headername}}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Template Name:</label>
                    <input type="text" class="form-control" name="" id="" placeholder>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company:</label>
                            <select class="form-control" name="" id="">
                                @foreach($partnerList as $list)
                                    <option value="{{$list->id}}">{{$list->partner_company->company_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Type:</label>
                            <select class="form-control" name="" id="">
                                @foreach($productType as $list)
                                    <option value="{{$list->id}}">{{$list->description}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea class="form-control"></textarea>
                </div>
                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#selectProduct" >Add Product</a>
                <a href="#" class="btn btn-primary pull-right">Save Product Template</a>
            </div>
        </section>
        <section class="content container-fluid">
            <div class="accordion-head">
                <h4>GO3 Gift and Rewards</h4>
                <a href="#" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#commissionAndRates">Edit</a>
            </div>
            <div class="content">
                <div class="box-group" id="accordion">
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"> Card Carrier </h4>
                            <div class="box-tools pull-right">
                                <a href="#collapseOne" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion">
                                    <i class="fa fa-minus"></i>
                                </a>
                            </div>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in show">
                            <div class="box-body">
                                <table class="table datatables table-condense table-striped">
                                    <thead>
                                        <td>Product Name</td>
                                        <td>Cost</td>
                                        <td>First Buy Rate</td>
                                        <td>2nd Buy Rate</td>
                                        <td>Payment Frequency</td>
                                        <td>Split Type</td>
                                        <td>Split Percentage</td>
                                        <td>Actions</td>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>GO3 Gift and Rewards::Customer Designer Carrier</td>
                                        <td>100.0000</td>
                                        <td>100.0000</td>
                                        <td>0.0000</td>
                                        <td>One-Time</td>
                                        <td>Second Buy Rate</td>
                                        <td>NO</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>GO3 Gift and Rewards::Gen Card Designer 1000pcs.</td>
                                        <td>5.0000</td>
                                        <td>5.0000</td>
                                        <td>0.0000</td>
                                        <td>One-Time</td>
                                        <td>Second Buy Rate</td>
                                        <td>NO</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>GO3 Gift and Rewards::Card Carrier 500pcs.</td>
                                        <td>0.0000</td>
                                        <td>0.0000</td>
                                        <td>0.0000</td>
                                        <td>One-Time</td>
                                        <td>Second Buy Rate</td>
                                        <td>NO</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>GO3 Gift and Rewards::Card Carrier 100pcs.</td>
                                        <td>1.0000</td>
                                        <td>1.0000</td>
                                        <td>0.0000</td>
                                        <td>One-Time</td>
                                        <td>Second Buy Rate</td>
                                        <td>NO</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>GO3 Gift and Rewards::Card Carrier 50pcs.</td>
                                        <td>10.0000</td>
                                        <td>10.0000</td>
                                        <td>0.0000</td>
                                        <td>One-Time</td>
                                        <td>Second Buy Rate</td>
                                        <td>NO</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>GO3 Gift and Rewards::Card Carrier 25pcs.</td>
                                        <td>25.0000</td>
                                        <td>25.0000</td>
                                        <td>0.0000</td>
                                        <td>One-Time</td>
                                        <td>Second Buy Rate</td>
                                        <td>NO</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"> Customized Gift Card</h4>
                            <div class="box-tools pull-right">
                                <a href="#collapseTwo" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion">
                                    <i class="fa fa-minus"></i>
                                </a>
                            </div>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse">
                        </div>
                    </div>
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"> G Shipping and Handling </h4>
                            <div class="box-tools pull-right">
                                <a href="#collapseThree" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion">
                                    <i class="fa fa-minus"></i>
                                </a>
                            </div>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse">
                            <div class="box-body">
                            </div>
                        </div>
                    </div>
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"> Generic Gift Cards </h4>
                            <div class="box-tools pull-right">
                                <a href="#collapseFour" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion">
                                    <i class="fa fa-minus"></i>
                                </a>
                            </div>
                        </div>
                        <div id="collapseFour" class="panel-collapse collapse">
                            <div class="box-body">
                            </div>
                        </div>
                    </div>
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"> Go3 Gift Card Monthly Service (Package Plan) </h4>
                            <div class="box-tools pull-right">
                                <a href="#collapseFour" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion">
                                    <i class="fa fa-minus"></i>
                                </a>
                            </div>
                        </div>
                        <div id="collapseFour" class="panel-collapse collapse">
                            <div class="box-body">
                            </div>
                        </div>
                    </div>
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"> Rewards </h4>
                            <div class="box-tools pull-right">
                                <a href="#collapseFour" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion">
                                    <i class="fa fa-minus"></i>
                                </a>
                            </div>
                        </div>
                        <div id="collapseFour" class="panel-collapse collapse">
                            <div class="box-body">
                            </div>
                        </div>
                    </div>
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"> Terminals </h4>
                            <div class="box-tools pull-right">
                                <a href="#collapseFour" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion">
                                    <i class="fa fa-minus"></i>
                                </a>
                            </div>
                        </div>
                        <div id="collapseFour" class="panel-collapse collapse">
                            <div class="box-body">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
                        <table class="table table-bordered table-striped">
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
                                        <td><input type="checkbox" id="{{$list->id}}" name="{{$list->id}}" value="{{$list->name}}" checked></td>
                                    </tr>       
                                @endforeach
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-save">Add Product</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="commissionAndRates" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Setup Commissions & Rates</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Product:</strong></label>
                                    <input type="text" class="form-control" name="" id="" value="GO3 Gift and Rewards :: Custom Designer Carrier" readonly/>
                                </div>
                                <div class="form-group">
                                    <label><strong>Cost:</strong></label>
                                    <input type="text" class="form-control" name="" id="" value="100.0000" readonly/>
                                </div>
                                <div class="form-group">
                                    <label><strong>Payment Frequency:</strong></label>
                                    <select id="" name="" class="form-control">
                                        <option>Goetu</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><strong>Split Type:</strong></label>
                                    <select id="" name="" class="form-control">
                                        <option>Go3 Solutions Inc.</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><strong>Buy Rate:</strong></label>
                                    <input type="text" class="form-control" name="" id="" value="100.0000"/>
                                </div>
                                <div class="form-group">
                                    <label><strong>Second Buy Rate:</strong></label>
                                    <input type="text" class="form-control" name="" id="" value="0.0000"/>
                                </div>
                                <div class="form-group">
                                    <label><strong>Downline Pricing Options:</strong></label><br/>
                                    <input type="radio" class="" name="retailPrice" id="" value="srp"/>
                                    <label>SRP</label>
                                    <input type="radio" class="" name="retailPrice" id="" value="srp"/>
                                    <label>MRP</label>
                                </div>
                                <div class="form-group">
                                    <label><strong>Cost:</strong></label>
                                    <input type="text" class="form-control" name="" id="" value="0.0000"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="radio" class="" name="" id="" value="split" checked/>
                                    <label for="productName"><strong>Split Percentage</strong></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label><strong>Upline Percentage</strong></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="0.0000" placeholder="Upline Percentage">
                                                <label class="input-group-addon">%</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label><strong>Downline Percentage</strong></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="0.0000" placeholder="Downline Percentage">
                                                <label class="input-group-addon">%</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="radio" class="" name="" id="" value="split" checked/>
                                    <label for="productName"><strong>Commission</strong></label>
                                </div>
                                <div class="form-group">
                                    <label for="productType"><strong>Commission Type:</strong></label>
                                    <select id="productType" name="productType" class="form-control">
                                        <option>Goetu</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="productDescription"><strong>Commission(%):</strong></label>
                                    <input type="text" class="form-control" name="productDescription" id="productDescription" value="New Sample Product Description" placeholder="Enter Product Description"/>
                                </div>
                                <div class="form-group cost-group">
                                    <div class="form-group">
                                        <label for="productType"><strong>Commission Type:</strong></label>
                                        <select id="productType" name="productType" class="form-control">
                                            <option>Goetu</option>
                                        </select>
                                    </div>
                                    <table class="table cost-table table-condensed table-striped">
                                        <tr class="bg-primary">
                                            <th colspan="3">Cost</th>
                                            <th>Commission</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr class="text-center">
                                            <td><input type="text" class="form-control" value="0" width="20"></td>
                                            <td>to</td>
                                            <td><input type="text" class="form-control" value="0" width="20"></td>
                                            <td><input type="text" class="form-control" value="50.00" width="20"></td>
                                            <td><a href="#"><i class="fa fa-minus-circle fa-2x"></i></a></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td><input type="text" class="form-control" value="0" width="20"></td>
                                            <td>to</td>
                                            <td><input type="text" class="form-control" value="0" width="20"></td>
                                            <td><input type="text" class="form-control" value="50.00" width="20"></td>
                                            <td><a href="#"><i class="fa fa-minus-circle fa-2x"></i></a></td>
                                        </tr>
                                    </table>
                                    <a href="#"><i class="fa fa-plus-circle"></i> Add Cases</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-save">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content -->
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/products/create.js" . "?v=" . config("app.version") }}"></script>
@endsection