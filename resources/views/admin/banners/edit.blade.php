@extends('layouts.app')

@section('style')
  <link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .form-radio { transform: translateY(1px) }
    .text-muted { color: rgba(0, 0, 0, 0.4) }
    .select2 { display: block }
    .select2-container 
    .select2-selection--multiple { border-radius: 0px; min-height: 38px; }
    .select2-container 
    .select2-selection--single 
    .select2-selection__rendered { padding-left: 0px !important; }
    .datepicker { padding-left: 15px; min-height: 38px; }
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        <span>Edit Announcement</span>
        
        @switch ($banner->state)
          @case ('Showing')
            <span class="ml-2 badge badge-primary" style="transform: translateY(-3.5px)">
              {{ $banner->state }}
            </span>
            @break

          @case ('Upcoming')
            <span class="ml-2 badge badge-info" style="transform: translateY(-3.5px)">
              {{ $banner->state }}
            </span>
            @break

          @case ('Ended')
            <span class="ml-2 badge badge-warning" style="transform: translateY(-3.5px)">
              {{ $banner->state }}
            </span>
            @break
        @endswitch
      </h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Announcements</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.banners.edit', $banner->id) }}">Edit Announcement</a></li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <form id="form-banner-edit" action="{{ route('admin.banners.update', $banner->id) }}" method="POST">
            @csrf
            @method('PUT')

            <p class="text-muted">ANNOUNCEMENT INFORMATION</p>

            <div class="form-group">
              <label>Type</label>
              <select class="form-control" name="type">
                <option value="" disabled selected>Select Type</option>
                @foreach ($bannerTypes as $i => $type)
                  <option value="{{ $i }}" {{ $i == old('type', $banner->type) ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label>Title</label>
              <input class="form-control"
                type="text" 
                name="title" 
                placeholder="Enter title here..."
                value="{{ old('title', $banner->title) }}">
            </div>

            <div class="form-group">
              <label>Message</label>
              <textarea class="form-control"
                name="message"
                placeholder="Enter message here..."
                rows="5">{{ old('message', $banner->message) }}</textarea>
            </div>

            <div class="row">
              <div class="col-lg-3 pr-0">
                <div class="form-group">
                  <label>Starts at <small>(NY timezone)</small></label>
                  <input class="form-control datepicker"
                      type="text" 
                      name="starts_at_date" 
                      placeholder="Date"
                      value="{{ old('starts_at_date', $banner->starts_at->format('Y-m-d')) }}">
                </div>
              </div>

              <div class="col-lg-3 pl-0">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <input class="form-control timepicker"
                      type="text" 
                      name="starts_at_time" 
                      placeholder="Time"
                      value="{{ old('starts_at_time', $banner->starts_at->format('H:i')) }}">
                </div>
              </div>
              <input type="hidden" name="starts_at" />
  
              <div class="col-lg-3 pr-0">
                <div class="form-group">
                  <label>Ends at <small>(NY timezone)</small></label>
                  <input class="form-control datepicker"
                      type="text" 
                      name="ends_at_date" 
                      placeholder="Date"
                      value="{{ old('banners_end_at', $banner->ends_at->format('Y-m-d')) }}">
                </div>
              </div>

              <div class="col-lg-3 pl-0">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <input class="form-control timepicker"
                      type="text" 
                      name="ends_at_time" 
                      placeholder="Time"
                      value="{{ old('ends_at_time', $banner->ends_at->format('H:i')) }}">
                </div>
              </div>
            </div>
            <input type="hidden" name="ends_at" />

            <p class="text-muted mt-2">ANNOUNCEMENT VIEWERS</p>

            <div class="form-group mb-1">
              <span>
                <input class="form-radio"
                  type="radio" 
                  name="viewer_type" 
                  value="A" 
                  {{ old('viewer_type', $baseViewerType) == 'A' ? 'checked' : '' }} />&nbsp;&nbsp;
                <span>Can be viewed by everyone</span>
              </span>
            </div>

            <div class="form-group">
              <span>
                <input class="form-radio"
                  type="radio" 
                  name="viewer_type" 
                  value="S" 
                  {{ old('viewer_type', $baseViewerType) == 'S' ? 'checked' : '' }} />&nbsp;&nbsp;
                <span>Can be viewed by specific company/department/user</span>
              </span>
            </div>

            <div id="viewers" class="hidden">
              <div class="form-group">
                <label>Company</label>
                <select class="form-control js-example-basic-single" 
                  name="companies[]"
                  style="width: 100%" 
                  multiple>
                  @foreach ($companies as $company)
                    <option value="{{ $company->id }}" {{ in_array($company->id, old('companies', $banner->company_viewer_ids)) ? 'selected' : '' }}>
                      {{ $company->company_name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label>Department/s</label>
                <select class="form-control js-example-basic-single" 
                  name="departments[]"
                  style="width: 100%" 
                  multiple>
                  @foreach ($departmentGroups as $departmentGroup)
                    @if ($company = $departmentGroup->first()->partnerCompany)
                      <optgroup label="{{ $company->company_name }}">
                    @else
                      <optgroup label="No Company">
                    @endif
                        @foreach ($departmentGroup->sortBy('description') as $department)
                          <option class="option-company-{{ $department->company_id }}"
                            data-company_id="{{ $department->company_id }}"
                            value="{{ $department->id }}" {{ in_array($department->id, old('departments', $banner->department_viewer_ids)) ? 'selected' : '' }}>
                            {{ $department->description }}
                          </option>
                        @endforeach
                      </optgroup>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label>User/s</label>
                <select class="form-control js-example-basic-single" 
                  name="users[]"
                  style="width: 100%" 
                  multiple>
                  @foreach ($userGroups  as $users)
                    @if ($company = $users->first()->partnerCompany)
                      <optgroup label="{{ $company->company_name }}">
                    @else
                      <optgroup label="No Company">
                    @endif
                        @foreach ($users->sortBy('full_name') as $user)
                          <option class="option-company-{{ $user->company_id }} option-department-{{ $user->user_type_id }}"
                            data-company_id="{{ $user->company_id }}"
                            data-department_id="{{ $user->user_type_id }}"
                            value="{{ $user->id }}" {{ in_array($user->id, old('users', $banner->user_viewer_ids)) ? 'selected' : '' }}>
                            {{ $user->full_name }}
                          </option>
                        @endforeach
                      </optgroup>
                  @endforeach
                </select>
              </div>
            </div>

            <br />
            <hr />

            <div class="form-group d-flex align-items-center">
              <button type="submit" class="btn btn-primary">Save</button>
              <div class="form-check pl-3 mb-0">
                <label class="form-check-label">
                  <input class="form-check-input" 
                    type="checkbox" 
                    name="continue_updating" 
                    value="1" />
                  <span>Continue Updating</span>
                </label>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>
@endsection

@section('script')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
  <script>
    /**
     * Initialization
     */
    $(document).ready(function() {
      $('.js-example-basic-single').select2();

      $('.timepicker').timepicker({ 
        defaultTime: '00:00',
        showInputs: true,
        showMeridian: false,
        icons: {
            up: "fa fa-chevron-up",
            down: "fa fa-chevron-down",
        },
      })

      $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true,
      })

      /**
       * Logic
       */
      function hideShowViewers(value) {
        switch (value) {
          case 'A':
            $('#viewers').addClass('hidden')
            break
            
          case 'S':
            $('#viewers').removeClass('hidden')
            break
        }
      }

      $('input[name="viewer_type"]').on('change', function() {
        hideShowViewers($(this).val())
      })

      setTimeout(function() {
        hideShowViewers($('input[name="viewer_type"]:checked').val())
      }, 1000)

      $('#form-banner-edit').on('submit', function(e) {
        let startsAtDate = $('input[name="starts_at_date"]').val()
        let startsAtTime = $('input[name="starts_at_time"]').val()
        let endsAtDate = $('input[name="ends_at_date"]').val()
        let endsAtTime = $('input[name="ends_at_time"]').val()

        let condition1 = startsAtDate == '' || startsAtTime == ''
        let condition2 = endsAtDate == '' || endsAtTime == ''

        if (!(condition1 || condition2)) {
          let startHour = startsAtTime.substr(0, startsAtTime.indexOf(':'))
          if (parseInt(startHour) <= 9) {
            startsAtTime = '0' + startsAtTime
          }

          let endHour = endsAtTime.substr(0, startsAtTime.indexOf(':'))
          if (parseInt(endHour) <= 9) {
            endsAtTime = '0' + endsAtTime
          }

          $('input[name="starts_at"]').val(startsAtDate + ' ' + startsAtTime)
          $('input[name="ends_at"]').val(endsAtDate + ' ' + endsAtTime)
        }
      })

      $('select[name="companies[]"]').on('change', function() {
        const companyIds = $(this).val()

        $('select[name="departments[]"] option').removeAttr('disabled')
        $('select[name="users[]"] option').removeAttr('disabled')
        if (companyIds !== null ) {
          companyIds.forEach(companyId => {  
            $(`.option-company-${companyId}`).attr('disabled', true)

            if (departmentIds !== null) {
              departmentIds.forEach(departmentId => {
                $(`select[name='departments[]'] option[value='${departmentId}']`).each(function() {
                  if ($(this).data('company_id') == companyId) {
                    $(this).removeAttr('selected')
                  }
                })
              })
            }

            if (userIds !== null) {
              userIds.forEach(userId => {
                $(`select[name='users[]'] option[value='${userId}']`).each(function() {
                  if ($(this).data('company_id') == companyId) {
                    $(this).removeAttr('selected')
                  }
                })
              })
            }
          })
        }

        $('select[name="departments[]"]').select2()
        $('select[name="users[]"]').select2()
      })

      let departmentIds = null;
      $('select[name="departments[]"]').on('change', function() {
        departmentIds = $(this).val()

        $('select[name="users[]"] option').removeAttr('disabled')
        if (departmentIds !== null ) {
          departmentIds.forEach(departmentId => {  
            $(`.option-department-${departmentId}`).attr('disabled', true)

            if (userIds !== null) {
              userIds.forEach(userId => {
                $(`select[name='users[]'] option[value='${userId}']`).each(function() {
                  if ($(this).data('department_id') == departmentId) {
                    $(this).removeAttr('selected')
                  }
                })
              })
            }
          })
        }

        $('select[name="users[]"]').select2()
      })

      let userIds = null;
      $('select[name="users[]"]').on('change', function() {
        userIds = $(this).val()
      })

      $('select[name="users[]"]').trigger('change')
      $('select[name="departments[]"]').trigger('change')
      $('select[name="companies[]"]').trigger('change')
    })
  </script>
@endsection