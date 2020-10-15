@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Training Access Control : 159278Go
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard </a></li>
                <li>Training Access Control</li>
                <li class="active">159278Go</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-condensed text-center">
                        <thead>
                        <tr>
                            <th>Partner Type</th>
                            <th>Training/Module</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="text" class="form-control" value=""></td>
                            <td><input type="text" class="form-control" value=""></td>
                            <td><a href="#" class="required"><i class="fa fa-minus-circle fa-2x"></i></a></td>
                        </tr>
                        </tbody>
                    </table>
                    <a href="#" class="pull-right"><i class="fa fa-plus-circle"></i>&nbsp; Create Access</a>
                    <a href="#" class="btn btn-primary">Save Training</a>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script>
        $('.datatables').dataTable();
    </script>
@endsection