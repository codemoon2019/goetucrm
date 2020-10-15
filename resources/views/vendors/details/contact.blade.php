@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Partners : Company
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Vendors</a></li>
                <li class="active">Go2POS</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                <h3>Apple Inc.</h3>
                <a href="{{ URL::previous() }}" class="btn btn-default pull-right mt-minus-40">< Back to Vendors</a>
            </div>
            <div class="nav-tabs-custom">
                <ul class="tabs-rectangular">
                    <li><a href="{{ url('vendors/details/profile') }}">Profile</a></li>
                    <li class="active"><a href="{{ url('vendors/details/contacts') }}">Contact</a></li>
                    <li><a href="{{ url('vendors/details/products') }}">Products</a></li>
                </ul>
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs">
                </ul>
        </section>
        <!-- /.content -->
    </div>
@endsection