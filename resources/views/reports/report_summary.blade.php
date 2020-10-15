@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Report Summary
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard </a></li>
                <li>Reports</li>
                <li class="active">Report Summary</li>
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
                <div class="col-md-12">
                    <table class="datatables table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                                <th aria-label="Merchant ID: activate to sort column descending">Merchant ID</th>
                                <th aria-label="">Merchant</th>
                                <th aria-label="">Agent ID</th>
                                <th aria-label="">Agent</th>
                                <th aria-label="">Sell Rate</th>
                                <th aria-label="">Commission</th>
                                <th aria-label="">Total Commision</th>
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
       $('.datatables').dataTable();
    </script>
@endsection
