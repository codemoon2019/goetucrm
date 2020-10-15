@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Draft Applicants
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li class="active">Draft Applicants List</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="clearfix"></div>
            <div class="col-md-12">
                <ul class="tabs-rectangular">
                </ul>
            </div>
            <div id="draftApplicantsContainer" class="">
                <div class="col-md-12" style="margin-bottom: 20px;">
                    <div class="row">
                        <div class="col-lg-6 col-md-12" >
                            <a href="#" class="dropdown-toggle btn btn-info" data-toggle="dropdown" aria-expanded="false">Show / Hide Columns <span class="caret"></span></a>
                            <ul class="dropdown-menu user-dept" role="menu">
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-0" class="toggle-vis" data-column="0" checked="checked">
                                    <label for="toggle-col-0" class="dept-name">Type</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-1" class="toggle-vis" data-column="1" checked="checked">
                                    <label for="toggle-col-1" class="dept-name">Company Name</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-2" class="toggle-vis" data-column="2" checked="checked">
                                    <label for="toggle-col-2" class="dept-name">Email Address</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-3" class="toggle-vis" data-column="3" checked="checked">
                                    <label for="toggle-col-3" class="dept-name">Mobile Number</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-4" class="toggle-vis" data-column="4" checked="checked">
                                    <label for="toggle-col-4" class="dept-name">Address</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-5" class="toggle-vis" data-column="5" checked="checked">
                                    <label for="toggle-col-5" class="dept-name">Action</label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <table class="table datatable responsive table-condense table-striped table-bordered" id="applicants-table">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Company Name</th>
                            <th>Email Address</th>
                            <th>Mobile Number</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        @if(isset($draftApplicants))
                        @foreach($draftApplicants as $d)
                        <tr>
                            <td>{{ $d->partnerType->name }}</td>
                            <td>{{ $d->company_name }}</td>
                            <td>{{ $d->partner_email }}</td>
                            <td>{{ $d->draftPartnerContacts[0]->mobile_number }}</td>
                            <td>{{ $d->full_address }}</td>
                            <td>
                                @if($d->partner_type_id == 3)
                                <a class="btn btn-primary btn-sm" role="button" href="{{ url("drafts/draftMerchant/" . $d->id . "/" . $d->partner_type_id . "/edit") }}" title="Retrieve"><i class="fa fa-get-pocket"></i></a>
                                @elseif($d->partner_type_id == 8 || $d->partner_type_id == 6)
                                <a class="btn btn-primary btn-sm" role="button" href="{{ url("drafts/draftLeadProspect/" . $d->id . "/" . $d->partner_type_id . "/edit") }}" title="Retrieve"><i class="fa fa-get-pocket"></i></a>
                                @else
                                <a class="btn btn-primary btn-sm" role="button" href="{{ url("drafts/draftPartners/" . $d->id . "/" . $d->partner_type_id . "/edit") }}" title="Retrieve"><i class="fa fa-get-pocket"></i></a>
                                @endif
                                <button class="btn btn-danger btn-sm" onclick="deleteDraftApplicant({{ $d->id }})" title="Delete"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </table>
                </div>  
            </div>
        </section>

    </div>
@endsection
@section('script')
<script src="{{ config("app.cdn") . "/js/drafts/list.js" . "?v=" . config("app.version") }}"></script>
@endsection