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
              <span>Draft Merchants</span>
            </a>
          </li>
        @endhasAccess

        @hasAccess('merchant', 'view merchant approval')
          <li>
            <a href="/merchants/approve_merchant">
              <span>For Approval</span>
            </a>
          </li>
        @endhasAccess


        @hasAccess('merchant', 'view merchant boarding')
          <li class="active">
            <a href="#">
              <span>Boarding</span>
            </a>
          </li>
        @endhasAccess

        @hasAccess('merchant', 'view')
          <li>
            <a href="/merchants">
              <span>Existing Merchant</span>
            </a>
          </li>
        @endhasAccess
      </ul>
    </div>

    <section class="content container-fluid">
      <div class="tab-content no-padding">
        <div id="merchant-boarding" class="tab-pane active">
          <section class="content-header">
            <h1>Merchant Boarding Process</h1>
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
                    <option value="mid">Merchant ID</option>
                    <option value="murl">Merchant URL</option>
                    <option value="phone">Merchant Phone Number</option>
                    <option value="dba">DBA</option>
                    <option value="cid">Credit Card Reference ID</option>
                    <option value="all" selected>Show All</option>
                  </select>
                </div>

                <div class="form-group col-sm-3" id="divSearchText">
                  <input type="text" class="form-control" id="txtSearchValue" name="txtSearchValue" placeholder="Search Merchants...">
                </div>

                <div class="form-group col-sm-3" id="divUplinePartner">
                  <select class="form-control" id="uplinePartner" name="uplinePartner">
                    <option value="company">Company</option>
                    <option value="iso">ISO</option>
                    <option value="subiso">Sub-ISO</option>
                    <option value="agent">Agent</option>
                    <option value="subagent">Sub-Agent</option>
                  </select>
                </div>

                <div class="form-group col-sm-3">
                  <button type="button" class="btn btn-primary" id="searchMerchants">Search Merchants</button>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="col-md-6 px-0">
                <a href="#" class="dropdown-toggle btn btn-info" data-toggle="dropdown" aria-expanded="false">Show / Hide Columns <span class="caret"></span></a>
                <ul class="dropdown-menu user-dept" role="menu">
                  <li class="hide">
                    <input type="checkbox" name="toggle-cols" id="toggle-col-1" class="toggle-vis" data-column="@if($canViewUpline) 0 @else 1 @endif" checked="checked">
                    <label for="toggle-col-2" class="dept-name">Merchant Name</label>
                  </li>
                  @if ($canViewUpline)
                    <li class="hide">
                      <input id="toggle-col-1" class="toggle-vis" type="checkbox" name="toggle-cols" data-column="@if($canViewUpline) 1 @else 2 @endif" checked="checked" />
                      <label for="toggle-col-1" class="dept-name">Partners</label>
                    </li>
                  @endif
                  <li class="hide">
                    <input type="checkbox" name="toggle-cols" id="toggle-col-1" class="toggle-vis" data-column="@if($canViewUpline) 2 @else 3 @endif" checked="checked">
                    <label for="toggle-col-2" class="dept-name">Merchant Name</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-3" class="toggle-vis" data-column="@if($canViewUpline) 3 @else 4 @endif" checked="checked">
                      <label for="toggle-col-3" class="dept-name">Status</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-4" class="toggle-vis" data-column="@if($canViewUpline) 4 @else 5 @endif" checked="checked">
                      <label for="toggle-col-4" class="dept-name">MID</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-5" class="toggle-vis" data-column="@if($canViewUpline) 5 @else 6 @endif" checked="checked">
                      <label for="toggle-col-5" class="dept-name">CID</label>
                  </li>
                  <li class="hide">
                      <input type="checkbox" name="toggle-cols" id="toggle-col-6" class="toggle-vis" data-column="@if($canViewUpline) 6 @else 7 @endif" checked="checked">
                      <label for="toggle-col-6" class="dept-name">Contact Person</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-7" class="toggle-vis" data-column="@if($canViewUpline) 7 @else 8 @endif" checked="checked">
                      <label for="toggle-col-7" class="dept-name">Mobile Number</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-8" class="toggle-vis" data-column="@if($canViewUpline) 8 @else 9 @endif" checked="checked">
                      <label for="toggle-col-8" class="dept-name">Email</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-9" class="toggle-vis" data-column="@if($canViewUpline) 9 @else 10 @endif" checked="checked">
                      <label for="toggle-col-9" class="dept-name">State</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-10" class="toggle-vis" data-column="@if($canViewUpline) 10 @else 11 @endif" checked="checked">
                      <label for="toggle-col-10" class="dept-name">URL</label>
                  </li>
                  <li>
                      <input type="checkbox" name="toggle-cols" id="toggle-col-10" class="toggle-vis" data-column="@if($canViewUpline) 11 @else 12 @endif" checked="checked">
                      <label for="toggle-col-10" class="dept-name">Action</label>
                  </li>
                </ul>
              </div>
            </div>

            <div class="col-md-12 px-0" style="margin-top:20px;">
              <table id="merchant-list" class="table responsive table-striped table-bordered">
                <thead>
                  <tr>
                    <th>Merchant ID</th>

                    @if($canViewUpline)
                    <th>Partners</th>
                    @endif

                    <th>Merchant Name</th>
                    <th>Status</th>
                    <th>MID</th>
                    <th>CID</th>
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
      </section>
    </div>
  </div>
@endsection

@section("script")
    <script src="{{ config("app.cdn") . "/js/merchants/board.js" . "?v=" . config("app.version") }}"></script>
    <script>
      load_merchants();

      function boardMerchant(id) {
        if (!confirm('This will board the current merchant. Proceed?')) {
          return false;
        }

        $.getJSON('/merchants/confirm_merchant/' + id , null, function (data) {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else{
            alert(data.message);
          }
        });
      }
    </script>
@endsection
