@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Vendors
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">List of Vendors</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                <h5>Select a vendor to view their information ...</h5>
                <a href="{{ url("vendors/create") }}" class="btn btn-primary pull-right mt-minus-40">Create Vendor</a>
            </div>
            <div class="clearfix"></div>
            <div id="companyContainer" class="">
                <div class="col-md-12 mb-plus-20">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control search-sys-usr" placeholder="Search Company...">
                            <button class="btn btn-primary system-usr-srch-btn">Search</button>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-default pull-right adv-search-btn">Advance Search</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 no-padding">
                    <table class="table datatables table-condense">
                        <thead>
                        <tr>
                            <th>Vendor ID</th>
                            <th>Vendor name</th>
                            <th>Business Address</th>
                            <th>Business Phone</th>
                            <th>Contact Person</th>
                            <th>Contact Phone</th>
                            <th>Contact Email</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tr>
                            <td>V10001</td>
                            <td><a href="#">Go2POS</a></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <button class="btn btn-default btn-sm">View</button>
                                <button class="btn btn-primary btn-sm">Edit</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="isoContainer" class="hide">
                <div class="col-md-12 mb-plus-20">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control search-sys-usr" placeholder="Search ISO...">
                            <button class="btn btn-primary system-usr-srch-btn">Search</button>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-default pull-right">Advance Search</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 no-padding">
                    <table class="table datatables table-condense table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Parent</th>
                            <th>ISO Name</th>
                            <th>Contact Person</th>
                            <th>Mobile Phone</th>
                            <th>Email</th>
                            <th>State</th>
                        </tr>
                        </thead>
                        <tr>
                            <td>ISO</td>
                            <td>Apple Inc. - Steve</td>
                            <td>ISO Company Apple</td>
                            <td>Friend of Steve</td>
                            <td>111-123-1234</td>
                            <td>aasdf@gmail.com</td>
                            <td>CA</td>
                        </tr>
                        <tr>
                            <td>ISO</td>
                            <td>Apple Inc. - Steve</td>
                            <td>ISO Company Apple</td>
                            <td>Friend of Steve</td>
                            <td>111-123-1234</td>
                            <td>aasdf@gmail.com</td>
                            <td>CA</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="subISOContainer" class="hide">
                <div class="col-md-12 mb-plus-20">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control search-sys-usr" placeholder="Search Sub ISO...">
                            <button class="btn btn-primary system-usr-srch-btn">Search</button>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-default pull-right">Advance Search</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 no-padding">
                    <table class="table datatables table-condense table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Parent</th>
                            <th>SubISO Name</th>
                            <th>Contact Person</th>
                            <th>Mobile Phone</th>
                            <th>Email</th>
                            <th>State</th>
                        </tr>
                        </thead>
                        <tr>
                            <td>ISO</td>
                            <td>Apple Inc. - Steve</td>
                            <td>ISO Company Apple</td>
                            <td>Friend of Steve</td>
                            <td>111-123-1234</td>
                            <td>aasdf@gmail.com</td>
                            <td>CA</td>
                        </tr>
                        <tr>
                            <td>ISO</td>
                            <td>Apple Inc. - Steve</td>
                            <td>ISO Company Apple</td>
                            <td>Friend of Steve</td>
                            <td>111-123-1234</td>
                            <td>aasdf@gmail.com</td>
                            <td>CA</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="agentContainer" class="hide">
                <div class="col-md-12 mb-plus-20">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control search-sys-usr" placeholder="Search Agent...">
                            <button class="btn btn-primary system-usr-srch-btn">Search</button>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-default pull-right">Advance Search</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 no-padding">
                    <table class="table datatables table-condense table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Parent</th>
                            <th>Agent Name</th>
                            <th>Contact Person</th>
                            <th>Mobile Phone</th>
                            <th>Email</th>
                            <th>State</th>
                        </tr>
                        </thead>
                        <tr>
                            <td>ISO</td>
                            <td>Apple Inc. - Steve</td>
                            <td>ISO Company Apple</td>
                            <td>Friend of Steve</td>
                            <td>111-123-1234</td>
                            <td>aasdf@gmail.com</td>
                            <td>CA</td>
                        </tr>
                        <tr>
                            <td>ISO</td>
                            <td>Apple Inc. - Steve</td>
                            <td>ISO Company Apple</td>
                            <td>Friend of Steve</td>
                            <td>111-123-1234</td>
                            <td>aasdf@gmail.com</td>
                            <td>CA</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="subAgentContainer" class="hide">
                <div class="col-md-12 mb-plus-20">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control search-sys-usr" placeholder="Search Sub Agent...">
                            <button class="btn btn-primary system-usr-srch-btn">Search</button>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-default pull-right">Advance Search</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 no-padding">
                    <table class="table datatables table-condense table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Parent</th>
                            <th>Sub Agent Name</th>
                            <th>Contact Person</th>
                            <th>Mobile Phone</th>
                            <th>Email</th>
                            <th>State</th>
                        </tr>
                        </thead>
                        <tr>
                            <td>ISO</td>
                            <td>Apple Inc. - Steve</td>
                            <td>ISO Company Apple</td>
                            <td>Friend of Steve</td>
                            <td>111-123-1234</td>
                            <td>aasdf@gmail.com</td>
                            <td>CA</td>
                        </tr>
                        <tr>
                            <td>ISO</td>
                            <td>Apple Inc. - Steve</td>
                            <td>ISO Company Apple</td>
                            <td>Friend of Steve</td>
                            <td>111-123-1234</td>
                            <td>aasdf@gmail.com</td>
                            <td>CA</td>
                        </tr>
                    </table>
                </div>
            </div>
            @include('incs.advanceSearch')
        </section>
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/partners/list.js" . "?v=" . config("app.version") }}"></script>
@endsection