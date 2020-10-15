@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                System Users
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">System Users</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">     
                        <h3>System Users</h3>
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
                        <!-- <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6"> -->
                                    <!-- <input type="text" class="form-control search-sys-usr" placeholder="Search System Users...">
                                    <button class="btn btn-primary system-usr-srch-btn">Search</button> -->
                                <!-- </div> -->

                                <!-- <div class="col-md-6">
                                    <button class="btn btn-default pull-right adv-search-btn">Advance Search</button>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-md-12 advanced-search">
                            <label>Advance Search</label>
                            <div class="row">
                                <div class="form-group col-sm-5">
                                    <select name="company-op" id="company-op" class="form-control">
                                        @if($is_partner==0)
                                        <option value="-1" data-code="-1">--SELECT A COMPANY--</option>
                                        @endif
                                        @if(count($companies)>0)
                                            @foreach($companies as $company)
                                                <option value="{{ $company->parent_id }}" data-code="{{ $company->parent_id }}" {{ $company->parent_id == auth()->user()->company_id ? 'selected' : '' }}>{{ $company->dba }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-sm-5" id="divUplinePartner">
                                    <button type="button" class="btn btn-outline-info dept-btn">SORT BY DEPARTMENT/S&nbsp;&nbsp;<i class="fa fa-caret-down"></i></button>
                                    <ul class="user-dept" style="display:none;">
                                    @foreach($departments as $department)
                                        <li class="department-li department-li-{{$department->company_id}}">
                                            <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" data-desc="{{$department->description}}" value="{{$department->id}}" class="adv-department-cb"/>
                                            <label for="adv-department" class="dept-name" title="{{$department->description}}">&nbsp;&nbsp;{{$department->description}}</label>
                                        </li>
                                    @endforeach
                                    </ul>
                                </div>
                                <div class="col-sm-2">
                                    <a href="#" class="btn btn-flat btn-primary" onclick="advanceSearchUsers();">Search Users</a>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="sort-by">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top:20px;">
                            <table id="users-table"  name="users-table"  class="table responsive table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Company</th>
                                    <th>Departments</th>
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