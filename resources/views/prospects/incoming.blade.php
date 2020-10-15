@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Incoming Prospects
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
                <div class="alert alert-success hide">
                    <p id="msg-success"></p>
                </div>
                <div class="alert alert-danger hide">
                    <p id="msg-danger"></p>
                </div>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li>Prospects</li>
                <li class="active">Incoming Prospects</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                <h3>Prospects</h3>
                <a href="{{ url("prospects/create") }}" class="btn btn-primary pull-right" style="margin-top: -40px">Create Prospects</a>
            </div>
            <div class="clearfix"></div>

            <div class="col-md-12" style="margin-top:20px;">
                <table class="table datatables table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Assigned By</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Date/Time Requested</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    @if(isset($incoming_leads) && count($incoming_leads) > 0)
                        @foreach($incoming_leads as $incoming_leads)
                        <tr>
                            <td>{{ $incoming_leads->assigned_by }}</td>
                            <td>{{ $incoming_leads->assignee }}</td>
                            <td>{{ $incoming_leads->partner_type }}</td>
                            <td>{{ $incoming_leads->created_at }}</td>
                            <td>{{ $incoming_leads->request_status }}</td>
                            <td>
                                @if($incoming_leads->request_status == 'New')
                                <button type="button" class="btn btn-success btn-sm" onclick="updateIncomingLeadRequest({{ $incoming_leads->id }},'A')">Accept</button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="updateIncomingLeadRequest({{ $incoming_leads->id }},'D')">Decline</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/prospects/list.js" . "?v=" . config("app.version") }}"></script>
@endsection