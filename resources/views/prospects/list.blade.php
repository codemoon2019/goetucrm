@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Prospects
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
                <div class="alert alert-success hide">
                    <p id="msg-success"></p>
                </div>
                <div class="alert alert-danger hide">
                    <p id="msg-danger"></p>
                </div>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li class="active">List of Prospects</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                <div class="row">
                    <div class="col-sm-8">
                        <h5>Select a prospect to view their information ...</h5>
                    </div>
                    <div class="col-sm-4">
                        <div class="col-md-12" style="margin-bottom:10px;display:inline-block;">
                            <a href="{{ url("prospects/create") }}" class="btn btn-primary pull-right">Create Prospects</a>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary pull-right" onclick="upload();">Upload Prospects</button>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary pull-right" style="margin-right:5px" onclick="showLPFileFormatDialog();">Get Upload File Format</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <ul class="tabs-rectangular">
                    <!-- <li class="active"><a href="#prospectsContainer" id="prospects" data-toggle="tab">Prospects</a></li> -->
                    <!-- <li><a href="#leadsContainer" id="leads" data-toggle="tab">Leads</a></li> -->
                </ul>
            </div>
            <div id="prospectsContainer" class="">
                <div class="col-md-12" style="margin-bottom: 20px;">
                    <div class="row">
                        <div class="col-lg-6 col-md-12" >
                            <!-- <input type="text" class="form-control search-sys-usr" placeholder="Search Prospects...">
                            <button class="btn btn-primary system-usr-srch-btn">Search</button> -->
                            <a href="#" class="dropdown-toggle btn btn-info" data-toggle="dropdown" aria-expanded="false">Show / Hide Columns <span class="caret"></span></a>
                            <ul class="dropdown-menu user-dept" role="menu">
                                {{-- <!-- <li class="hide">
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-0" class="toggle-vis" data-column="0" checked="checked">
                                    <label for="toggle-col-0" class="dept-name">Type</label>
                                </li> -->
                                <li class="hide">
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-0" class="toggle-vis" data-column="0" checked="checked">
                                    <label for="toggle-col-0" class="dept-name">Lead ID</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-1" class="toggle-vis" data-column="1" checked="checked">
                                    <label for="toggle-col-1" class="dept-name">Source</label>
                                </li>
                                @if($canViewUpline)
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-2" class="toggle-vis" data-column="2" checked="checked">
                                    <label for="toggle-col-2" class="dept-name">Assigned to</label>
                                </li>
                                @endif
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-3" class="toggle-vis" data-column="3" checked="checked">
                                    <label for="toggle-col-3" class="dept-name">Interested Products</label>
                                </li>
                                @if($canViewUpline)
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-4" class="toggle-vis" data-column="4" checked="checked">
                                    <label for="toggle-col-4" class="dept-name">Upline</label>
                                </li>
                                @endif
                                <li class="hide">
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-5" class="toggle-vis" data-column="5" checked="checked">
                                    <label for="toggle-col-5" class="dept-name">Company Name</label>
                                </li>
                                <li class="hide">
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-6" class="toggle-vis" data-column="6" checked="checked">
                                    <label for="toggle-col-6" class="dept-name">Contact Person</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-7" class="toggle-vis" data-column="7" checked="checked">
                                    <label for="toggle-col-7" class="dept-name">Business Phone</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-8" class="toggle-vis" data-column="8" checked="checked">
                                    <label for="toggle-col-8" class="dept-name">Mobile Phone</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-9" class="toggle-vis" data-column="9" checked="checked">
                                    <label for="toggle-col-9" class="dept-name">Status</label>
                                </li>
                                <!-- <li>
                                    <input type="checkbox" name="toggle-cols" id="toggle-col-11" class="toggle-vis" data-column="11" checked="checked">
                                    <label for="toggle-col-11" class="dept-name">Action</label>
                                </li> --> --}}
                            </ul>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <a href="#" class="btn btn-default pull-right adv-search-btn">Advance Search</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 no-padding">
                    <table class="table responsive table-condense table-striped table-bordered" id="prospects-table">
                        <thead>
                            <tr>
                                <!-- <th class="hide">Type</th> -->
                                <th>Prospect ID</th>
                                <th>Source</th>
                                @if($canViewUpline)
                                <th>Assigned to</th>
                                @endif
                                <th>Interested Products</th>
                                @if($canViewUpline)
                                <th>Upline</th>
                                @endif
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Business Phone</th>
                                <th>Mobile Phone</th>
                                <th>Status</th>
                                <!-- @if($canDelete)
                                <th class="hide">Action</th>
                                @endif -->
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($prospects))
                        @foreach($prospects as $prospects)
                        <tr>
                            <!-- <td class="hide">{{ $prospects->partner_type }}</td> -->
                            <td>
                                @if($prospects->partner_status == 'D')
                                    <button class="btn btn-danger btn-sm" onclick="deleteDraftApplicant({{ $prospects->partner_id }})" title="Delete Draft"><i class="fa fa-trash"></i></button>
                                @endif
                                <a href="{{ url("prospects/details/profile/".$prospects->partner_id) }}">{{ $prospects->merchant_id }}</a></td>
                            <td>{{ $prospects->lead_source }}</td>
                            @if($canViewUpline)
                            <td>
                            @if($prospects->partner1)
                                {{ $prospects->partner1 }}
                            @else
                                {{ $prospects->partner }}
                            @endif
                            </td>
                            @endif
                            @if(!empty($prospects->interested_products))
                                <td>{{ substr($prospects->interested_products,0,strlen($prospects->interested_products))}}</td>
                            @else
                                <td></td>
                            @endif
                            @if($canViewUpline)
                            @if(!empty($prospects->upline_partners))
                                <td>{!! substr($prospects->upline_partners,0,strlen($prospects->upline_partners))!!}</td>
                            @else
                                <td></td>
                            @endif
                            @endif
                            <td>
                                {{ $prospects->company_name }}
                            </td>
                            <td>{{ $prospects->contact_person }}</td>
                            @if($prospects->phone1 != '')
                                <td>
                                {{ $prospects->company_country_code }}{{ $prospects->phone1 }}<label style="display: none;">{{ str_replace('-', '', $prospects->company_country_code.$prospects->phone1) }}</label>
                                </td>
                            @else
                                <td></td>
                            @endif
                            @if($prospects->mobile_number != '')
                                <td>
                                {{ $prospects->company_country_code }}{{ $prospects->mobile_number }}<label style="display: none;">{{ str_replace('-', '', $prospects->company_country_code.$prospects->mobile_number) }}</label>
                                </td>
                            @else
                                <td></td>
                            @endif
                            <td>
                                @if($prospects->partner_status == 'D') 
                                    <a href="{{ url("drafts/draftLeadProspect/" . $prospects->partner_id . "/" . $prospects->partner_type_id . "/edit") }}">Incomplete Prospect Application</a>
                                @else
                                    {{ $prospects->lead_status }}
                                @endif
                            </td>
                            <!-- @if($canDelete)
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteProspect({{ $prospects->id }},'D')">Delete</button>
                            </td>
                            @endif -->
                        </tr>
                        @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @include('incs.advanceSearch')
            <input type="hidden" id="uplineView" value="{{$canViewUpline}}" >
        </section>

    </div>
        <div id="modalUploadCSV" class="modal" role="dialog">
            <form role="form" name="frmUploadCSV" id="frmUploadCSV" method="post" enctype="multipart/form-data" files="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">GoETU Billing</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <p style="font-style:italic">Note: Please make sure to fill up all the required 
                                                fields with proper format before uploading the CSV File for 
                                                Prospects (refer to <a href="{{asset('storage/filespecs.pdf')}}" target="_blank">Upload File Specifications and Guidelines</a> for the format and requirements).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Select CSV file: </label>
                                        <input type="file" id="fileUploadCSV" name="fileUploadCSV" accept=".csv"/>
                                    </div>

                                    <button class="btn btn-sm btn-danger clear-input" data-file_id="fileUploadCSV">Clear Input</butto>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnUploadCSV" name="btnUploadCSV" class="btn btn-primary">Upload</button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
            </form>
        </div>

        

        <div id="modalFileFormat" class="modal fade" role="dialog">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-title-message">Upload File Format</h4>
                    </div>
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <a href="{{ "/uploadfiles/leadfilespecs.pdf" . "?v=" . config("app.version") }}" target="_blank" style="text-align:center" class="fa fa-file-pdf-o fa-5x" title="Download File Specification"></a>
                        </div>
                        <div class="col-md-4"><label>Upload File Specifications and Guidelines</label> </div>
                        <div class="col-md-2">
                            <a href="{{ "/uploadfiles/leadfiletemplate.csv" . "?v=" . config("app.version") }}"  style="text-align:center" class="fa fa-file-excel-o fa-5x" title="Download Upload File Template"></a> 
                        </div>
                        <div class="col-md-4"><label>Upload File Template</label></div>
                    </div>
                    </div>
                    <div class="modal-footer">
                    
                    </div>
                </div>
                </div>
            </div>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/clearInput.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/jquery.maskedinput.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/prospects/list.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/merchants/toggleColumn.js" . "?v=" . config("app.version") }}"></script>
    <script>
        toggleCols('#prospects-table');
    </script>
@endsection