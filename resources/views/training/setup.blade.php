@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Training Setup
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li class="active">Training Setup</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ url("training/setupCreate") }}" class="btn btn-primary pull-right" >Create Training Module</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table datatables table-striped">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Product</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainings as $training)
                            <tr>
                                <td>{{$training->name}}</td>
                                <td>{{$training->productname}}</td>
                                <td><a href="/training/setupEdit/{{$training->id}}" class="btn btn-primary btn-sm">Edit</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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