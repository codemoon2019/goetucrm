@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Cross Selling Commission Reports
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard </a></li>
                <li>Reports</li>
                <li class="active">Cross Selling Commission Reports</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-sm-6 clear">
                    <div class="row">
                        <div class="col-md-4">
                            <label> Start Date: </label> 
                            <input name="startdate" id="startdate" value="04/01/2018" class="form-control" type="text"> 
                        </div>
                        <div class="col-md-4">
                            <label> End Date: </label> 
                            <input name="startdate" id="startdate" value="04/30/2018" class="form-control" type="text"> 
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>  
                            <input name="submit" value="Generate" class="btn btn-danger form-control" type="submit">
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="col-md-12">
                    <table class="datatables table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                                <th aria-label="Invoice ID: activate to sort column descending">Invoice ID</th>
                                <th aria-label="MID: activate to sort column ascending">MID</th>
                                <th aria-label="Merchant Legal Business Name: activate to sort column ascending">Merchant Legal Business Name</th>
                                <th aria-label="Payment Method: activate to sort column ascending">Payment Method</th>
                                <th aria-label="Date: activate to sort column ascending">Date</th>
                                <th aria-label="Amount: activate to sort column ascending">Amount</th>
                                <th aria-label="Gateway Transaction ID: activate to sort column ascending">Gateway Transaction ID</th>
                                <th aria-label="Main Product: activate to sort column ascending">Main Product</th>
                                <th aria-label="Product Category: activate to sort column ascending">Product Category</th>
                                <th aria-label="Sub Product: activate to sort column ascending">Sub Product</th>
                                <th aria-label="Domain: activate to sort column ascending">Domain</th>
                                <th aria-label="Frequency: activate to sort column ascending">Frequency</th>
                                <th aria-label="Status: activate to sort column ascending">Status</th>
                                <th aria-label="Billing Date: activate to sort column ascending">Billing Date</th>
                                <th aria-label="Agent: activate to sort column ascending">Agent</th>
                                <th aria-label="Commission: activate to sort column ascending">Commission</th>
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
        </section>
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/reports/comm_reports.js" . "?v=" . config("app.version") }}"></script>
@endsection