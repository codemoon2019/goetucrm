@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Permissions
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Permissions</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">
                        
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div>
                        <h3>ACL</h3>
                         @php $access = session('all_user_access'); 
                            if (!isset($access['admin'])){
                                return redirect('/')->with('failed','You have no access to that page.')->send();
                            }
                        @endphp
                        @php 
                            if(strpos($access['admin'], 'add') !== false){ @endphp
                                <span class="pull-right"><a href="/admin/acl/create" class="btn btn-success">Create ACL</a></span>
                        @php } @endphp
                        </div>
                        <table id="acl-table"  name="acl-table"  class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="25%">Category</th>
                                    <th width="25%">Name</th>
                                    <th width="20%">Value</th>
                                    <th width="30%">Action</th>
                                </tr>
                            </thead>
                        </table>
              
                    </div>
                </div>

        </section>
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/admin/acl.js" . "?v=" . config("app.version") }}"></script>
@endsection
