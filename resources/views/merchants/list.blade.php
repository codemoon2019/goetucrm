@extends('layouts.app')

@section('style')
  <style>
    .tab-pane {
      padding: 0px;
    }
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <div class="chrome-tabs">
      <ul class="nav nav-tabs ui-sortable-handle">
        @hasAccess('draft applicants', 'draft applicants list')
          <li>
            <a href="/merchants/draft_merchant">
              <span style="font-size:small">Incomplete Merchant Application</span>
            </a>
          </li>
        @endhasAccess

        @hasAccess('merchant', 'view merchant approval')
          <li class="hide">
            <a href="/merchants/approve_merchant">
              <span>For Approval</span>
            </a>
          </li>
        @endhasAccess

        @hasAccess('merchant', 'view merchant boarding')
          <li class="hide">
            <a href="/merchants/board_merchant">
              <span>Boarding</span>
            </a>
          </li>
        @endhasAccess

        @hasAccess('merchant', 'view')
          <li class="active">
            <a href="#">
              <span>Existing Merchant</span>
            </a>
          </li>
        @endhasAccess
      </ul>
    </div>

    <section class="content container-fluid">
      <div class="tab-content no-padding">
        <div id="existing-merchant" class="tab-pane active">
          <section class="content-header">
            <h1>Merchants</h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li class="active">List of Merchants</li>
            </ol>

            <div class="dotted-hr"></div>
          </section>

              
          <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
              <div class="row">
                <div class="col-sm-8">
                  <h3>Select a merchant to view their information ...</h3>
                </div>

                @if (App\Models\Access::hasPageAccess('merchant', 'add', true))
                  <div class="col-sm-4">
                    <a href="{{ url("merchants/create") }}" class="btn btn-primary pull-right">Create Merchant</a>
                  </div>
                  <div class="col-sm-12">
                  <!-- <a href="{{ url("leads/create") }}" class="btn btn-primary pull-right">Upload Leads / Prospects</a> -->
                      <button class="btn btn-primary pull-right" onclick="upload();">Upload Merchant</button>
                      <button class="btn btn-primary pull-right" style="margin-right:5px" onclick="showLPFileFormatDialog();">Get Upload File Format</button>
                  </div>

                @endif
              </div>
            </div>

            <div class="clearfix"></div>
            
            <div class="col-md-12">
              <div class="row">
                <div class="offset-md-6 col-md-6">
                  <a href="#" class="btn btn-default pull-right adv-search-btn">Advance Search</a>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="row">
                <div class="form-group col-sm-3">
                  <select class="form-control" id="filterType" name="filterType">
                    <option value="name">Merchant Name</option>
                    <option value="mid">Customer ID</option>
                    <option value="murl">Merchant URL</option>
                    <option value="phone">Merchant Phone Number</option>
                    <option value="dba">DBA</option>
                    <option value="cid">Credit Card Reference ID</option>
                    <option value="upline">Partner</option>
                    <option value="all" selected>Show All</option>
                  </select>
                </div>

                <div class="form-group col-sm-3" id="divSearchText">
                  <input id="txtSearchValue"
                    class="form-control"
                    type="text"
                    name="txtSearchValue" 
                    placeholder="Search Merchants...">
                </div>

                <div class="form-group col-sm-6" id="divUplinePartner">
                  <select class="form-control" id="uplinePartner" name="uplinePartner">
                    @forelse ($upline as $up)
                      <option value="{{ $up->parent_id }}">
                        {{ $up->dba }} - {{ $up->upline_partner }} - {{ $up->partner_id_reference }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-sm-3">
                  <button type="button" class="btn btn-primary" id="searchMerchants">Search Merchants</button>
                </div>
              </div>
            </div>

            @include('incs.toggleColumns')

            <div class="col-md-12 px-0" style="margin-top:20px;">
              <table id="merchant-list" class="table responsive table-striped table-bordered">
                <thead>
                  <tr>
                    <th>Customer ID</th>
                    
                    @if ($canViewUpline)
                      <th>Partners</th>
                    @endif
                              
                    <th>Merchant Name</th>
                    <th>Status</th>
                    <th>MID</th>
                    <th>PID</th>
                    <th>Contact Person</th>
                    <th>Mobile Number</th>
                    <th>Email</th>
                    <th>State</th>
                    <th>URL</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>

            @include('incs.advanceSearch')
          </section>
        </div>
      </div>        
    </section>
  </div>


        <div id="modalUploadCSV" class="modal" role="dialog">
            <form role="form" name="frmUploadCSV" id="frmUploadCSV" method="post" enctype="multipart/form-data" files="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">GoETU Merchant Upload</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Select CSV file:</label>
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
                            <a href="{{ "/uploadfiles/merchantfilespecs.pdf" . "?v=" . config("app.version") }}" target="_blank" style="text-align:center" class="fa fa-file-pdf-o fa-5x" title="Download File Specification"></a>
                        </div>
                        <div class="col-md-4"><label>Upload File Specifications and Guidelines</label> </div>
                        <div class="col-md-2">
                            <a href="{{ "/uploadfiles/merchantfiletemplate.csv" . "?v=" . config("app.version") }}"  style="text-align:center" class="fa fa-file-excel-o fa-5x" title="Download Upload File Template"></a> 
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

@section("script")
<script src="{{ config("app.cdn") . "/js/merchants/list.js" . "?v=" . config("app.version") }}"></script>
<script src="{{ config("app.cdn") . "/js/merchants/process.js" . "?v=" . config("app.version") }}"></script>
<script>
  load_merchants();
  </script>
<script src="{{ config("app.cdn") . "/js/merchants/toggleColumn.js" . "?v=" . config("app.version") }}"></script>
@endsection
