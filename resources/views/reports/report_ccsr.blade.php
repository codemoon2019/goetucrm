@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Credit Card Sales Report
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard </a></li>
                <li>Reports</li>
                <li class="active">Credit Card Sales Report</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-sm-6 clear">
                    <div class="row">
                        <div class="col-md-4">
                            <label> Start Date: </label> 
                            <input name="startdate" value="04/01/2018" class="form-control" type="text"> 
                        </div>
                        <div class="col-md-4">
                            <label> End Date: </label> 
                            <input name="enddate"value="04/30/2018" class="form-control" type="text"> 
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>  
                            <input name="submit" value="Export to Excel" class="btn btn-success form-control" type="submit">
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="col-md-12">
                    
                </div>

            </div>
        </section>
    </div>
@endsection