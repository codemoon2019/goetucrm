@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Create Template
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li><a href="/products">Products</a></li>
                <li class="active">Templates</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle">
                    @if($has_pFee)
                    <li class="active"><a href="#commissions-rates" class="commissions-rates" data-toggle="tab" aria-expanded="true">Product Fee</a></li>
                    @endif

                    @if($has_wFlow)
                    <li class="" ><a href="#workflow" class="workflow" data-toggle="tab" aria-expanded="false">Workflow</a></li>
                    @endif

                    @if($has_wEmail)
                    <li class=""><a href="#welcome-email" class="welcome-email" data-toggle="tab" aria-expanded="false">Welcome Email</a></li>
                    @endif
                </ul>
                <div class="tab-content no-padding">

                    @if($has_pFee)
                    <div class="tab-pane active" id="commissions-rates">
                        <span class="pull-right"><a href="{{ url("products/template/productfee/create") }}" class="btn btn-success">Create Product Fee</a></span>
                        <table id="productfee-table"  name="productfee-table"  class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="20%">Name</th>
                                    <th width="25%">Description</th>
                                    <th width="10%">Type</th>
                                    <th width="20%">Company</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                    @endif

                    @if($has_wFlow)
                    <div class="tab-pane" id="workflow">
                        <span class="pull-right"><a href="{{ url("products/templates/workflow") }}" class="btn btn-success">Create Workflow Template</a></span>
                        <table id="workflow-table"  name="workflow-table"  class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="25%">Name</th>
                                    <th width="25%">Description</th>
                                    <th width="25%">Product</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                     @endif

                     @if($has_wEmail)
                    <div class="tab-pane" id="welcome-email">
                        <span class="pull-right"><a href="{{ url("products/template/wemail/create") }}" class="btn btn-success">Create Welcome Email</a></span>
                        <table id="wemail-table"  name="wemail-table"  class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="25%">Name</th>
                                    <th width="25%">Product</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                    @endif
                </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/products/templates.js" . "?v=" . config("app.version") }}"></script>
    <script>
        arr = window.location.href.split('#');
        if(arr[1] != undefined)
        {
            $('.'+arr[1]).trigger('click');  
        }

    </script>
@endsection