@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Commission Report
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard </a></li>
                <li>Reports</li>
                <li class="active">Commission Report</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12 clear">
                    <div class="row">
                        <div class="col-md-2">
                            <label> Start Date: </label> 
                            <input name="startdate" value="04/01/2018" class="form-control" type="text"> 
                        </div>
                        <div class="col-md-2">
                            <label> End Date: </label> 
                            <input name="enddate" value="04/30/2018" class="form-control" type="text"> 
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>  
                            <input name="submit" value="Generate" class="btn btn-danger form-control" type="submit">
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="col-md-12">
                    <table class="datatables table-striped table-bordered table-condensed" style="width:100%;">
                        <thead>
                            <tr role="row">
                                <th tabindex="0" aria-controls="">Agent Name</th>
                                <th tabindex="0" aria-controls="">Net Sales Amount</th>
                                <th tabindex="0" aria-controls="">Commission Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="odd">
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