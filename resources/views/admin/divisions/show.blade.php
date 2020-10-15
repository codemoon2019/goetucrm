@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Divisions
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/departments">Divisions</a></li>
                <li class="breadcrumb-item">View</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">

                            <div class="form-group">
                                <label>Division Name:</label>
                                <input type="text" id="name" name="name" class="form-control dept-acl-input"  placeholder="Division Name..." value="{{$division->name}}" disabled>
                            </div>

                            <div class="form-group">
                                <label>Description:</label>
                                <input type="text" id="description" name="description" class="form-control dept-acl-input"  placeholder="Description..." value="{{$division->description}}" disabled>
                            </div>                   
        
                    </div>
                </div>

        </section>
    </div>
@endsection
@section("script")
@endsection