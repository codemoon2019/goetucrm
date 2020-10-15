<div class="form-group">
  <label>CCs</label>
  <select class="js-example-basic-single form-control" name="cc_ids[]" multiple {{ ($viewOnly ?? false) || ($replyOnly ?? false) ? 'disabled' : '' }}>
    @foreach ($usersGroups as $users)
      @php $company = $users->first()->partnerCompany; @endphp

      <optgroup label="{{ $company->company_name }}" class="optgroup optgroup-{{ $company->id }}">
        @foreach ($users as $user)
          <option value="{{ $user->id }}" 
            data-image="{{ $user->image }}" 
            data-user_type="{{ $user->department_names }}" 
            {{ ($ticketHeaderCCs ?? collect([]))->contains($user->id) ? 'selected' : '' }}>

            {{ " {$user->full_name} " }}
          </option>
        @endforeach
      </optgroup>
    @endforeach
  </select>
</div>