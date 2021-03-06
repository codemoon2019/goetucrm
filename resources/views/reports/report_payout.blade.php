@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Payout Report
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard </a></li>
                <li>Reports</li>
                <li class="active">Payout Report</li>
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
                                <th aria-label="">Company</th>
                                <th aria-label="">Agent Name</th>
                                <th aria-label="">Total Amount collected</th>
                                <th aria-label="">Agent Commission</th>
                                <th aria-label="">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan=2 >Total:</th>
                                <th>$0.00</th>
                                <th>$0.00</th>
                            </tr>
                        </tfoot>
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
