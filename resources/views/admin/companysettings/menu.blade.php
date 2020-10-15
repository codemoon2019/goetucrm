@extends('layouts.app')


@section('style')
    <style>
        .inner:hover {
            color: black;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">

        <div class="content">
        
            <div class="row">
                <!--  -->
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <a href="#" onclick="editACH({{$id}});">
                    <div class="small-box bg-white">
                        <div class="inner">
                            <h3>ACH</h3>

                            <p>Configuration</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-cogs"></i>
                        </div>
                    </div>
                    </a>
                </div>
                <!--  -->
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <a href="{{ url("admin/company_settings/$id/training_access") }}">
                    <div class="small-box bg-white">
                        <div class="inner">
                            <h3>Training</h3>

                            <p>Setup</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                    </div>
                    </a>
                </div>

                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <a href="{{ url("admin/company_settings/ticket-config/companies/{$id}") }}">
                    <div class="small-box bg-white">
                        <div class="inner">
                            <h3>Ticket</h3>

                            <p>Configuration</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-ticket"></i>
                        </div>
                    </div>
                    </a>
                </div>
                   
            </div>
            @include('admin.companysettings.modalsettings')
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ config(" app.cdn ") . "/js/admin/companysettings.js" . "?v=" . config(" app.version ") }}"></script>
@endsection