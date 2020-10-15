@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Merchant
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li><a href="/merchants">Merchant</a></li>
                <li class="active">{{$merchant->partner_company->company_name}}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                <h3>{{$merchant->partner_company->company_name}}</h3>
                <a href="/merchants" class="btn btn-default pull-right" style="margin-top: -40px">Back to Merchants</a>
            </div>
            <div class="nav-tabs-custom">
                @include("merchants.details.merchanttabs")
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle secondary-tabs"></ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="iso">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">RMA / Servicing</h3>
                            </div>
                            <table class="table datatables table-striped">
                                <thead>
                                    <th></th>
                                    <th>Service #</th>
                                    <th>Return Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Edit</th>
                                </thead>
                                <tbody>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-plus-20">
                            <a href="#" class="btn btn-primary">Create Returns</a>
                            <a href="#" class="btn btn-primary">Void Returns</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
@section('script')
    <script>
        $('.datatables').dataTable();
    </script>
@endsection