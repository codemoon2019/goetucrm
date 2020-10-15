@extends('layouts.app')

@section('content')
    <link href="https://fonts.googleapis.com/css?family=Oswald:400,500,600,700" rel="stylesheet">
    <style>
        .title-header
        {
            font-family: 'Oswald', sans-serif;
        }
        
        .hidden {
            display: none;
        }

        .td-toggle-merchants {
            cursor: pointer;
        }

        .card {
          background: #fff;
          border-radius: 2px;
          display: inline-block;
          height: 100px;
          margin: 1rem;
          position: relative;
          width: 200px;
        }

        .recent-amount
        {
            font-family: 'Oswald', sans-serif;
            text-align: center;
            padding-top: 20px;
        }

        .recent-date
        {
            font-family: 'Oswald', sans-serif;
            text-align: center;
        }

        .table
        {
            font-family: 'Oswald', sans-serif;
            font-size: 12px;
        }

        .form-control
        {
            font-family: 'Oswald', sans-serif;
        }
    </style>
    <div class="content-wrapper">
        <section class="content-header">
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><i class="fa fa-dashboard"></i>&nbsp;&nbsp;{{ __("common.dashboard") }}</li>
                </ol>
            </nav>
        </section>

        <div class="content">
            <div class="col-md-12">

                <div class="row">                    

                </div>

                <div class="row"> 
                    @if($dash_access['task_completion_rate'] == true && (strpos($user->dashboard_items, 'task_completion_rate') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.task_completion_rate")
                    @endif

                    @if($dash_access['task_list'] == true && (strpos($user->dashboard_items, 'task_list') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.task_list")
                    @endif

                    @if($dash_access['yearly_revenue'] == true && (strpos($user->dashboard_items, 'yearly_revenue') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.yearly_revenue")
                    @endif

                    @if($dash_access['sales_per_agent'] == true && (strpos($user->dashboard_items, 'sales_per_agent') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.sales_per_agent")
                    @endif

                    @if($dash_access['leads_this_month'] == true && (strpos($user->dashboard_items, 'leads_this_month') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.leads_this_month")
                    @endif

                    @if($dash_access['top_5_products'] == true && (strpos($user->dashboard_items, 'top_5_products') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.top_5_products")
                    @endif

                    @if($dash_access['merchant_by_agents'] == true && (strpos($user->dashboard_items, 'merchant_by_agents') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.merchant_by_agents")
                    @endif

                    @if($dash_access['owner_dashboard'] == true && (strpos($user->dashboard_items, 'owner_dashboard') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.owner_dashboard")
                    @endif

                    @if($dash_access['transaction_activity'] == true && (strpos($user->dashboard_items, 'transaction_activity') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.transaction_activity")
                    @endif

                    @if($dash_access['recent_sales'] == true && (strpos($user->dashboard_items, 'recent_sales') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.recent_sales")
                    @endif

                    @if($dash_access['active_vs_closed_merchants'] == true && (strpos($user->dashboard_items, 'active_vs_closed_merchants') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.active_vs_closed_merchants")
                    @endif

                    @if($dash_access['merchants_enrollment'] == true && (strpos($user->dashboard_items, 'merchants_enrollment') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.merchants_enrollment")
                    @endif

                    @if($dash_access['sales_trends'] == true && (strpos($user->dashboard_items, 'sales_trends') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.sales_trends")
                    @endif

                    @if($dash_access['sales_matrix'] == true && (strpos($user->dashboard_items, 'sales_matrix') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.sales_matrix")
                    @endif

                    @if($dash_access['sales_profit'] == true && (strpos($user->dashboard_items, 'sales_profit') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.sales_profit")
                    @endif

                    @if($dash_access['incoming_leads_today'] == true && (strpos($user->dashboard_items, 'incoming_leads_today') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.incoming_leads_today")
                        <script src="{{ asset('js/admin/incoming_leads_today.js') }}"></script>
                    @endif

                    @if($dash_access['total_leads'] == true && (strpos($user->dashboard_items, 'total_leads') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.total_leads")
                        <script src="{{ asset('js/admin/total_leads.js') }}"></script>
                    @endif

                    @if($dash_access['leads_payment_processor'] == true && (strpos($user->dashboard_items, 'leads_payment_processor') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.leads_payment_processor")
                        <script src="{{ asset('js/admin/leads_payment_processor.js') }}"></script>
                    @endif

                    @if($dash_access['converted_leads'] == true && (strpos($user->dashboard_items, 'converted_leads') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.converted_leads")
                        <script src="{{ asset('js/admin/converted_leads.js') }}"></script>
                    @endif

                    @if($dash_access['converted_prospects'] == true && (strpos($user->dashboard_items, 'converted_prospects') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.converted_prospects")
                        <script src="{{ asset('js/admin/converted_prospects.js') }}"></script>
                    @endif

                    @if($dash_access['appointments_per_day'] == true && (strpos($user->dashboard_items, 'appointments_per_day') !== false || !isset($user->dashboard_items)))
                        @include("dashboard.appointments_per_day")
                        <script src="{{ asset('js/admin/appointments_per_day.js') }}"></script>
                    @endif
                </div>

            </div>
        </div>
    </div>








@endsection
@section('script')

@if($dash_access['task_completion_rate'] == true && (strpos($user->dashboard_items, 'task_completion_rate') !== false || !isset($user->dashboard_items)))
    @include("dashboard.task_completion_rate_script")
@endif

@if($dash_access['yearly_revenue'] == true && (strpos($user->dashboard_items, 'yearly_revenue') !== false || !isset($user->dashboard_items)))
    @include("dashboard.yearly_revenue_script")
@endif

@if($dash_access['sales_per_agent'] == true && (strpos($user->dashboard_items, 'sales_per_agent') !== false || !isset($user->dashboard_items)))
    @include("dashboard.sales_per_agent_script")
@endif

@if($dash_access['merchant_by_agents'] == true && (strpos($user->dashboard_items, 'merchant_by_agents') !== false || !isset($user->dashboard_items)))
    @include("dashboard.merchant_by_agents_script")
@endif

@if($dash_access['owner_dashboard'] == true && (strpos($user->dashboard_items, 'owner_dashboard') !== false || !isset($user->dashboard_items)))
    @include("dashboard.owner_dashboard_script")
@endif

@if($dash_access['transaction_activity'] == true && (strpos($user->dashboard_items, 'transaction_activity') !== false || !isset($user->dashboard_items)))
    @include("dashboard.transaction_activity_script")
@endif

@if($dash_access['active_vs_closed_merchants'] == true && (strpos($user->dashboard_items, 'active_vs_closed_merchants') !== false || !isset($user->dashboard_items)))
<script src="{{ asset('js/admin/merchant_active_vs_cancelled.js') }}"></script>
@endif
@if($dash_access['merchants_enrollment'] == true && (strpos($user->dashboard_items, 'merchants_enrollment') !== false || !isset($user->dashboard_items)))
<script src="{{ asset('js/admin/merchants_enrollment.js') }}"></script>
@endif
@if($dash_access['sales_trends'] == true && (strpos($user->dashboard_items, 'sales_trends') !== false || !isset($user->dashboard_items)))
<script src="{{ asset('js/admin/sales_trends.js') }}"></script>
@endif

@if($dash_access['sales_profit'] == true && (strpos($user->dashboard_items, 'sales_profit') !== false || !isset($user->dashboard_items)))
<script src="{{ asset('js/admin/sales_profit.js') }}"></script>
@endif

@if($dash_access['sales_matrix'] == true && (strpos($user->dashboard_items, 'sales_matrix') !== false || !isset($user->dashboard_items)))
<script>
    load_invoices_matrix();
    function load_invoices_matrix() {
        $.getJSON('/company/invoice_volume_data', null, function (data) {
            var oTable = $('#invoice-matrix').dataTable({ "bRetrieve": true , "order": []});
            oTable.fnClearTable();
            if (data.length > 0) {
                oTable.fnAddData(data);
            }
            $('#invoice-matrix').DataTable().columns.adjust().responsive.recalc();
        });
    }
</script>
@endif




<script>
    $('#taskList').dataTable({ "bRetrieve": true });
</script>
@endsection