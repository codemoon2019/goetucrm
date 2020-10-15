@extends('layouts.app')

@section('style')
  <link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .form-check-label {
      margin-right: 20px;
    }

    .form-check-label {
      font-weight: normal !important;
    }
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>User Activity Reports</h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/billing/report') }}">Reports</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">User Activity</a></li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid pb-0">
      <div class="offset-md-2 col-md-8 my-2">
        <form id="form-user-activites-generate" action="{{ route('reports.userActivities.show') }}" method="POST" autocomplete="off">
          @csrf
          
          <input type="hidden" name="company_id" />
          <div class="col-sm-12">
            <div class="form-group">
              <label>User/s</label>
              <div class="visible-panel">
                <div class="sliding-panel">
                  <div class="left-panel ">
                    <select  class="select2 form-control group-list" 
                      name="user_type" 
                      data-placeholder="Select user type" 
                      data-allow-clear="true">
                      <option></option>
                      @foreach ($departmentGroups as $departmentGroup)
                        @if ($company = $departmentGroup->first()->partnerCompany)
                          <optgroup label="{{ $company->company_name }}">
                        @else
                          <optgroup label="No Company">
                        @endif
                            @foreach ($userTypes as $userType)
                              <option value="{{ $userType->id }}" data-company_id="{{ $company->partner_id ?? -1 }}">
                                {{ $userType->description }}
                              </option>
                            @endforeach

                            @foreach ($departmentGroup->sortBy('description') as $department)
                              <option value="{{ $department->id }}" data-company_id="{{ $company->partner_id ?? -1 }}">
                                {{ $department->description }}
                              </option>
                            @endforeach
                          </optgroup>
                      @endforeach
                    </select>
                  </div>
  
                  <div class="right-panel">
                    <select class="form-control select2" name="user"></select>
                  </div>
                </div>
              </div>
  
              <a href="#" class="btn btn-flat btn-default btn-xs pull-right back hide">
                <i class="fa fa-chevron-left"></i> Back
              </a>
            </div>
          </div>

          <div class="col-sm-12 pb-1">
            <label>Date</label> <br/>

            <div class="form-check form-check-inline">
              <label class="form-check-label">
                <input type="radio" name="date_type" class="form-check-input" value="day">Day
              </label>

              <label class="form-check-label">
                <input type="radio" name="date_type" class="form-check-input" value="week">Week
              </label>

              <label class="form-check-label">
                <input type="radio" name="date_type" class="form-check-input" value="month">Month
              </label>

              <label class="form-check-label">
                <input type="radio" name="date_type" class="form-check-input" value="year">Year
              </label>

              <label class="form-check-label">
                <input type="radio" name="date_type" class="form-check-input" value="custom">Custom
              </label>
            </div>

            <div id="datepicker-container" class="form-group">
              <input name="date" type='text' id="datepicker" class="form-control" placeholder="Click to select date" />
            </div>

            <div id="datepicker-custom" class="input-group input-daterange hidden mb-3">
              <input class="form-control datepicker" 
                type="text" 
                name="custom_start_date" 
                placeholder="Select start date"/>
              <div class="input-group-addon">to</div>
              <input class="form-control datepicker" 
                type="text" 
                name="custom_end_date" 
                placeholder="Select end date" />
            </div>
          </div>

          <div class="col-sm-12 pb-1">
            <label>Display Records By</label> <br/>

            <div class="form-check form-check-inline">
              <label class="form-check-label">
                <input type="radio" name="display_by" class="form-check-input" value="DAILY">Daily
              </label>

              <label class="form-check-label hidden">
                <input type="radio" name="display_by" class="form-check-input" value="WEEKLY">Weekly
              </label>

              <label class="form-check-label hidden">
                <input type="radio" name="display_by" class="form-check-input" value="MONTHLY">Monthly
              </label>

              <label class="form-check-label hidden">
                <input type="radio" name="display_by" class="form-check-input" value="YEARLY">Yearly
              </label>

            </div>
          </div>

          <div class="col-sm-12 mt-4 mb">
            <button class="btn btn-primary mr-2" name="web">Generate Report</button>

            @hasAccess('reports', 'u.a. report (excel)')
              <button class="btn btn-success" name="excel">Generate Report in Excel</button>
            @endhasAccess
          </div>
        </form>
      </div>
    </section>
  </div>
@endsection

@section('script')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <script>
    let departments = @json($departments);
    let departmentsUsers = @json($departmentsUsers);
    let userTypesUsers = @json($userTypesUsers);
  </script>
  <script src=@cdn('/js/reports/partnerActivities/index.js')></script>
@endsection