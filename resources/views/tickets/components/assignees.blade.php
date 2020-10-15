
<div class="form-group">
  <label>Assignee</label>
  <div class="visible-panel">
    <div class="sliding-panel">
      <div class="left-panel ">
        <select class="js-example-basic-single form-control group-list" 
          name="department" 
          data-placeholder="Select a Department" 
          data-allow-clear="true"
          {{ ($viewOnly ?? false) || ($replyOnly ?? false) ? 'disabled' : '' }}>
          
          <option></option>
          
          @foreach ($departmentsGroups as $departments)
            @php $company = $departments->first()->partnerCompany @endphp

            <optgroup label="{{ $company->company_name }}">
              @foreach ($departments as $department)
                <option value="{{ $department->id }}" 
                  data-company_id="{{ $company->id ?? -1 }}"
                  {{ $department->id == ($ticketHeaderDepartmentId ?? null) ? 'selected' : ''}}>

                  {{ $department->description }}
                </option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
      </div>

      <div class="right-panel">
        <select class="form-control select2" name="assignee" {{ ($viewOnly ?? false) || ($replyOnly ?? false) ? 'disabled' : '' }}>
        </select>
      </div>
    </div>
  </div>

  <p id="form-error-department" class="form-error hidden"></p>
  <p id="form-error-assignee" class="form-error hidden"></p>

  @if (($allPrivileges ?? true))
    <a href="#" class="btn btn-flat btn-default btn-xs pull-right back hide">
      <i class="fa fa-chevron-left"></i> Back
    </a>
  @endif
</div>