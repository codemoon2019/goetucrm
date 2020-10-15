@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Merchant
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
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

        <div class="content">
            <div class="col-md-12" style="display: none;">
                <div class="row">
                    <div class="col-lg-12 col-xs-6">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><b>Merchant Purchases</b></h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table datatable responsive table-condense table-striped table-bordered" id="merchant-purchase">
                                            <thead>
                                            <tr>
                                                <th colspan="3" style="text-align: center;background: #dd4b39">Unpaid</th>
                                                <th colspan="3" style="text-align: center;background: #00a65a">Paid</th>
                                            </tr>

                                            <tr>
                                                <th style="text-align: center;background: #dd4b39">This Month</th>
                                                <th style="text-align: center;background: #dd4b39">This Year</th>
                                                <th style="text-align: center;background: #dd4b39">Overall</th>
                                                <th style="text-align: center;background: #00a65a">This Month</th>
                                                <th style="text-align: center;background: #00a65a">This Year</th>
                                                <th style="text-align: center;background: #00a65a">Overall</th>
                                            </tr>
                                            </thead>
                                            <tbody style="text-align: center">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div id="merchant-tree" class="chart"> </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    // $('#merchant-purchase').DataTable({
    //     serverSide: true,
    //     processing: true,
    //     searching: false,
    //     lengthChange: false,
    //     ajax: '/merchants/details/dashboard/merchant-purchase/{{$id}}',
    //     columns: [
    //         {data: 'monthTotalU'},
    //         {data: 'yearTotalU'},
    //         {data: 'overallTotalU'},
    //         {data: 'monthTotal'},
    //         {data: 'yearTotal'},
    //         {data: 'overallTotal'}
    //     ]
    // });
</script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.css">
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.7/raphael.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.min.js.map"></script> -->


        <style type="text/css">
            
        .nodeExample1 {
            padding: 2px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            background-color: #ffffff;
            border: 1px solid #000;
            width: 200px;
            font-family: Tahoma;
            font-size: 12px;
        }
        .nodeExample1 img {
            margin-right:  10px;
            width: 30%; height: 30%;
        }

        .node-desc {
            margin: 0 10px;
            font-size: .6rem;
        }
        .node-contact {
             margin: 0 10px;
            font-size: .6rem;
        }
        .Treant > .node { padding: 3px; border: 2px solid #484848; border-radius: 3px; }
        .Treant > .node img { width: 30%; height: 30%; }

        .Treant .collapse-switch { width: 100%; height: 70%; border: none;}
        .Treant .node.collapsed { background-color: #DEF82D; }
        .Treant .node.collapsed .collapse-switch { background: none; width: 100%; height: 70%;}
        .node-tip {
            display: none;
            position: absolute;
            border: 0px solid #333;
            background-color: rgba(0,0,0,0.7);
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            -webkit-box-shadow: #000 1px 5px 20px;
            -moz-box-shadow: #000 1px 5px 20px;
            box-shadow: #000 1px 5px 20px;
            padding: 10px;
            color: #fff;
            font-size: 16px;
            width: 250px;
        }
        </style>
    <script>
        $(document).ready(function () {
            $( ".node-desc" ).addClass( "btn btn-primary btn-sm fa fa-pencil" );
            $('.node').click(function () {

                $('.node').css("border","1px  solid #484848");
                // $('.node').css("background-color","");
                $(this).css("border","3px  solid #484848");
                // $(this).css("background-color","#17a2b8");
                 
            });
        });
        {!!  $js !!}

    </script>

@endsection