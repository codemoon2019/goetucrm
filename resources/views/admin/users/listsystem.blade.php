@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                System Defined Users
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">System Defined Users</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">     
                        <h3>System Defined Users</h3>
                        <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                            @php
                                $access = session('all_user_access'); 
                                if (!isset($access['users'])){
                                    return redirect('/')->with('failed','You have no access to that page.')->send();
                                }
                            @endphp
                            @php
                                if(strpos($access['users'], 'add') !== false){ @endphp
                                <a href="{{ url("admin/users/create") }}" class="btn btn-success pull-right">Create User</a>
                            @php } @endphp
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- <input type="text" class="form-control search-sys-usr" placeholder="Search System Users...">
                                    <button class="btn btn-primary system-usr-srch-btn">Search</button> -->
                                </div>

                                <div class="col-md-6">
                                    <button class="btn btn-default pull-right adv-search-btn">Advance Search</button>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" style="margin-top:20px;">
                            <table id="system-users-table"  name="system-users-table"  class="table responsive table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Company</th>
                                    <th>System Defined Group</th>
                                    <th>Email Address</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        </div>
                        </div>
                        </div>
                        
            @include('incs.advanceSearch')
        </section>
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/admin/users.js" . "?v=" . config("app.version") }}"></script>
@endsection