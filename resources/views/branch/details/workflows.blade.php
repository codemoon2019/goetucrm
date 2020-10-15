@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Workflows
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
                <div class="alert alert-success hide">
                    <p id="msg-success"></p>
                </div>
                <div class="alert alert-danger hide">
                    <p id="msg-danger"></p>
                </div>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li class=""><a href="/processFlow">Process Flow</a></li>
                <li class="active">Workflows</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                <h3></h3>
                
            </div>

            <div class="clearfix"></div>

            <div class="col-md-6" hidden>
                <input type="text" class="form-control search-sys-usr" placeholder="Search Products...">
                <button class="btn btn-primary system-usr-srch-btn">Search</button>
            </div>

            <div class="col-md-12" style="margin-top:20px;">
                <table id="workflow-list" name="workflow-list" class="table responsive table-condense p-0">
                    <thead>
                        <tr>
                            <th>Workflow #</th>
                            <th>Workflow Date</th>
                            <th style="width:200px">Partner</th>
                            <th style="width:200px">Merchant</th>
                            <th style="width:400px">Product</th>
                            <th>Progress</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
                    
            </div>
        </section>
    </div>
@endsection

@section("script")
    <script src="{{ config("app.cdn") . "/js/merchants/product.js" . "?v=" . config("app.version") }}"></script>
    <script>
       load_workflows();
    </script>
@endsection