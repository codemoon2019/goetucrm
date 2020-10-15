@extends('layouts.app')


    @php
        $access = session('all_user_access');
        $reportaccess = isset($access['reports']) ? $access['reports'] : "";
        $admin_access = isset($access['admin']) ? $access['admin'] : "";

        $ach = (strpos($reportaccess, 'ach transaction report') === false) ? false : true;
        $msr = (strpos($reportaccess, 'monthly sales report') === false) ? false : true;
        $comm = (strpos($reportaccess, 'commission report') === false) ? false : true;
        $np = (strpos($reportaccess, 'new partners report') === false) ? false : true;
        $nb = (strpos($reportaccess, 'new business report') === false) ? false : true;
        $product = (strpos($reportaccess, 'product report') === false) ? false : true;

        $is_admin =  (strpos($admin_access, 'super admin access') === false) ? false : true;

    @endphp


@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Reports
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li class="active">Reports</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                @if($ach)
                <div class="col-sm-3">
                    <a href="{{ url("billing/ach_report") }}">
                    <div class="thumbnail">
                    <img src="{{ asset("images/reports/ach_report.png") . "?v=" . config("app.version") }}">
                    <div class="caption" align="center"><b>ACH Transaction Report</b></div>       
                    </div>
                    </a> 
                </div>
                @endif

                @if($msr)
                <div class="col-sm-3">
                    <a href="{{ url("billing/report_ms") }}">
                        <div class="thumbnail">
                            <img src="{{ asset("images/reports/sales_report.png") . "?v=" . config("app.version") }}">
                            <div class="caption" align="center"><b>Monthly Sales Report</b></div>
                        </div>
                    </a>
                </div>
                @endif
                
                @if($comm)
                <div class="col-sm-3">
                    <a href="{{ url("billing/report_commission") }}">
                        <div class="thumbnail">
                            <img src="{{ asset("images/reports/commission_report.png") . "?v=" . config("app.version") }}">
                            <div class="caption" align="center"><b>Commission Report</b></div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ url("billing/report_commission_detailed") }}">
                        <div class="thumbnail">
                            <img src="{{ asset("images/reports/detailed_commission_report.png") . "?v=" . config("app.version") }}">
                            <div class="caption" align="center"><b>Detailed Commission Report</b></div>
                        </div>
                    </a>
                </div>

                @endif

                @if($np)
                <div class="col-sm-3">
                    <a href="{{ url("billing/report_new_partner") }}">
                    <div class="thumbnail">
                    <img src="{{ asset("images/reports/ach_report.png") . "?v=" . config("app.version") }}">
                    <div class="caption" align="center"><b>New Partners Report</b></div>       
                    </div>
                    </a> 
                </div>
                @endif

                @if($nb)
                <div class="col-sm-3">
                    <a href="{{ url("billing/report_new_business") }}">
                    <div class="thumbnail">
                    <img src="{{ asset("images/reports/ach_report.png") . "?v=" . config("app.version") }}">
                    <div class="caption" align="center"><b>New Business Report</b></div>       
                    </div>
                    </a> 
                </div>
                @endif

                @if($product)
                <div class="col-sm-3">
                    <a href="{{ url("billing/report_product") }}">
                    <div class="thumbnail">
                    <img src="{{ asset("images/reports/ach_report.png") . "?v=" . config("app.version") }}">
                    <div class="caption" align="center"><b>Product Report</b></div>       
                    </div>
                    </a> 
                </div>
                @endif

                @hasAccess('reports', 'consolidated branches report')
                <div class="col-sm-3">
                   <a href="{{ url("billing/report_branches") }}">
                        <div class="thumbnail">
                            <img src="{{ asset("images/reports/ach_report.png") . "?v=" . config("app.version") }}">
                            <div class="caption" align="center"><b>Consolidated Branches Report</b></div>
                        </div>
                    </a>
                </div>
                @endhasAccess


                @if($is_admin)
                <div class="col-sm-3">
                    <a href="{{ url("billing/report_export_log") }}">
                        <div class="thumbnail">
                            <img src="{{ asset("images/reports/logs.png") . "?v=" . config("app.version") }}">
                            <div class="caption" align="center"><b>Export Logs</b></div>
                        </div>
                    </a>
                </div>
                @endif

                @hasAccess('reports', 'user activities report')
                    <div class="col-sm-3">
                        <a href="{{ route('reports.userActivities.index') }}">
                            <div class="thumbnail">
                                <img src="{{ asset("images/reports/user_activities.png") . "?v=" . config("app.version") }}">
                                <div class="caption" align="center"><b>User Activities Report</b></div>
                            </div>
                    </a>
                    </div>
                @endhasAccess

                @hasAccess('reports', 'billing status report')
                <div class="col-sm-3">
                   <a href="{{ url("billing/report_billing") }}">
                        <div class="thumbnail">
                            <img src="{{ asset("images/reports/ach_report.png") . "?v=" . config("app.version") }}">
                            <div class="caption" align="center"><b>Merchant Billing Status Report</b></div>
                        </div>
                    </a>
                </div>
                @endhasAccess


            </div>
        </section>
    </div>
@endsection