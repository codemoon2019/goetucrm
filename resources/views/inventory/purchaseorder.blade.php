@extends('layouts.app') @section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
                Purchase Order
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
        <ol class="breadcrumb">
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Inventory</a></li>
            <li class="active">Puchase Order</li>
        </ol>
        <div class="dotted-hr"></div>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group">
                        <a href="{{ url("inventory/") }}" class="list-group-item active">
                            Purchase Order
                        </a>
                        <a href="{{ url("inventory/receivingpurchaseorder") }}" class="list-group-item ">
                            Receiving Order
                        </a>
                </div>       
            </div>
            <div class="col-md-10">
                <table class="table datatables table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>PO#</th>
                            <th>PO Date</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                        <tr>
                            <td>&nbsp;</td>
                            <td>123456</td>
                            <td>04-23-2018</td>
                            <td>GoETU Inc.</td>
                            <td>100.00</td>
                            <td>Approved</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm">View</button>
                                <button type="button" class="btn btn-warning btn-sm">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm">Delete</button>
                            </td>
    
                        </tr>
                </table>
            </div>
        </div>
        
    </section>
    <!-- /.content -->
    </div>
    @endsection @section("script")
    <script src="{{ config(" app.cdn ") . "/js/inventory/purchaseorder.js" . "?v=" . config(" app.version ") }}"></script>
    @endsection