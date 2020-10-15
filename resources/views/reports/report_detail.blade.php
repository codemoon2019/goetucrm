@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Detail Report
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard </a></li>
                <li>Reports</li>
                <li class="active">Detail Report</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid"> <!-- start of main container-->
            <div class="row"> <!-- start of header's first row -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-2 col-md-3 col-sm-4">
                            <label> Start Date: </label> 
                            <input name="startdate" value="04/01/2018" class="form-control" type="text"> 
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4">
                            <label> End Date: </label> 
                            <input name="enddate" value="04/30/2018" class="form-control" type="text"> 
                        </div>
                        <div class="col-lg-4">
                            <!-- for spacing purposes -->
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4">
                            <label>&nbsp;</label>  
                            <input name="submit" value="Generate" class="btn btn-flat btn-danger form-control" type="submit">
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4">
                            <label>&nbsp;</label>  
                            <input name="submit" value="Export" class="btn btn-flat btn-success form-control" type="submit">
                        </div>
                    </div>
                </div>
            </div> <!-- end of header's first row -->

            
            
            <div class="row"> <!-- start of table row -->
                <div class="col-md-12" > 
                    <table class="datatables table-striped table-bordered" style="width: 100%;" >
                        <thead>
                            <tr>
                                <th aria-label="">Merchant ID</th>
                                <th aria-label="">Merchant</th>
                                <th aria-label="">Agent</th>
                                <th aria-label="">Sales</th>
                                <th aria-label="">Count</th>
                                <th aria-label="">Sell Rate</th>
                                <th aria-label="">Transaction Fee Monthly</th>
                                <th aria-label="">Transaction Cost</th>
                                <th aria-label="">Website (One time)</th>
                                <th aria-label="">Hosting Fee (Annualy)</th>
                                <th aria-label="">Design (One time)</th>
                                <th aria-label="">Gift (Monthly)</th>
                                <th aria-label="">OLO (Monthly)</th>
                                <th aria-label="">Setup Fee</th>
                                <th aria-label="">Shipping Fee</th>
                                <th aria-label="">Card Fee (One time)</th>
                                <th aria-label="">Loyalty (Monthly)</th>
                                <th aria-label="">Bundle (Monthly)</th>
                                <th aria-label="">Mark up</th>
                                <th aria-label="">Agent Commission</th>
                                <th aria-label="">Total Commission</th>
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
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> <!-- end of table row -->
        </section> <!-- end of main container -->
    </div>
@endsection
@section('script')
    <script>
        $('.datatables').dataTable(
            {
                "scrollX": true
            });
    </script>
@endsection
