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
            <a href="/merchants/draft_branch">
              <span style="font-size:12px">Incomplete Branch Application</span>
            </a>
          </li>
        @endhasAccess

        @hasAccess('branch', 'view')
          <li class="active">
            <a href="#">
              <span>Branches</span>
            </a>
          </li>
        @endhasAccess
      </ul>
    </div>

    <section class="content container-fluid">
      <div class="tab-content no-padding">
        <div id="existing-merchant" class="tab-pane active">
          <section class="content-header">
            <h1>Branch</h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard</a></li>
                <li class="active">List of Branches</li>
            </ol>

            <div class="dotted-hr"></div>
          </section>

              
          <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
              <div class="row">
                <div class="col-sm-8">
                  <h3>Select a branch to view their information ...</h3>
                </div>

                @hasAccess('branch', 'add')
                  <div class="col-sm-4">
                    <a href="{{ url("merchants/branchCreate") }}" class="btn btn-primary pull-right">Create Branch</a>
                  </div>
                @endhasAccess
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
                    <option value="name">Branch Name</option>
                    <option value="mid">Branch ID</option>
                    <option value="murl">Branch URL</option>
                    <option value="phone">Branch Phone Number</option>
                    <option value="dba">DBA</option>
                    <option value="cid">Credit Card Reference ID</option>
                    <option value="upline">Merchant/Owner</option>
                    <option value="all" selected>Show All</option>
                  </select>
                </div>

                <div class="form-group col-sm-3" id="divSearchText">
                  <input id="txtSearchValue"
                    class="form-control"
                    type="text"
                    name="txtSearchValue" 
                    placeholder="Search Branches...">
                </div>

                <div class="form-group col-sm-6" id="divUplinePartner">
                  <select class="form-control" id="uplinePartner" name="uplinePartner">
                    @forelse ($upline as $up)
                      <option value="{{ $up->parent_id }}">
                        {{ $up->dba }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-sm-3">
                  <button type="button" class="btn btn-primary" id="searchBranches">Search Branch</button>
                </div>
              </div>
            </div>

            @include('incs.toggleColumns')
            {{-- <div class="col-md-12">
              <div class="col-md-6 px-0">
                <a href="#" class="dropdown-toggle btn btn-info" data-toggle="dropdown" aria-expanded="false">Show / Hide Columns <span class="caret"></span></a>
                <ul class="dropdown-menu user-dept" role="menu">
                  <li class="hide">
                    <input type="checkbox" name="toggle-cols" id="toggle-col-0" class="toggle-vis" data-column="0" checked="checked">
                    <label for="toggle-col-0" class="dept-name">Branch ID</label>
                  </li>
                  <li class="hide">
                    <input class="toggle-vis" type="checkbox"  id="toggle-col-1" name="toggle-cols" data-column="1" checked="checked" />
                    <label for="toggle-col-1" class="dept-name">Owner</label>
                  </li>

                  <li class="hide">
                    <input type="checkbox" name="toggle-cols" id="toggle-col-2" class="toggle-vis" data-column="2" checked="checked">
                    <label for="toggle-col-2" class="dept-name">Branch Name</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-3" class="toggle-vis" data-column="3" checked="checked">
                      <label for="toggle-col-3" class="dept-name">Status</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-4" class="toggle-vis" data-column="4" checked="checked">
                      <label for="toggle-col-4" class="dept-name">MID</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-5" class="toggle-vis" data-column="5" checked="checked">
                      <label for="toggle-col-5" class="dept-name">CID</label>
                  </li>
                  <li class="hide">
                      <input type="checkbox" name="toggle-cols" id="toggle-col-6" class="toggle-vis" data-column="6" checked="checked">
                      <label for="toggle-col-6" class="dept-name">Contact Person</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-7" class="toggle-vis" data-column="7" checked="checked">
                      <label for="toggle-col-7" class="dept-name">Mobile Number</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-8" class="toggle-vis" data-column="8" checked="checked">
                      <label for="toggle-col-8" class="dept-name">Email</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-9" class="toggle-vis" data-column="9" checked="checked">
                      <label for="toggle-col-9" class="dept-name">State</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-10" class="toggle-vis" data-column="10" checked="checked">
                      <label for="toggle-col-10" class="dept-name">URL</label>
                  </li>
                </ul>
              </div>
            </div> --}}

            <div class="col-md-12 px-0" style="margin-top:20px;">
              <table id="branch-list" class="table responsive table-striped table-bordered">
                <thead>
                  <tr>
                    <th>Branch ID</th>
                    <th>Owner</th>       
                    <th>Branch Name</th>
                    <th>Status</th>
                    <th>MID</th>
                    <th>CID</th>
                    <th>Contact Person</th>
                    <th>Mobile Number</th>
                    <th>Email</th>
                    <th>State</th>
                    <th>URL</th>
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

@endsection

@section("script")
  <script src="{{ config("app.cdn") . "/js/merchants/list.js" . "?v=" . config("app.version") }}"></script>
  <script>
    load_branches();
    </script>
  <script src="{{ config("app.cdn") . "/js/merchants/toggleColumn.js" . "?v=" . config("app.version") }}"></script>
@endsection
