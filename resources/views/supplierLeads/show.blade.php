@extends('layouts.app')

@section('style')
  <link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1 class="d-flex align-items-center">
        <span>{{ $supplierLead->doing_business_as }}</span>
        <span class="badge badge-primary ml-4">Supplier Lead</span>
        <span class="badge badge-success ml-1">Active</span> <!-- @todo To be changed -->
      </h1>
      
      <ol class="breadcrumb">
        <li><a href="/">Dashboard</a></li>
        <li><a href="{{ route('supplierLeads.index') }}">List of Supplier Leads</a></li>
        <li>{{ $supplierLead->doing_business_as }}</li>
      </ol>
      
      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      <div class="nav-tabs-custom" style="box-shadow: none;">
        <ul class="tabs-rectangular">
          <li>
            <a href="{{ route('supplierLeads.show.overview', $supplierLead->id) }}">
            Summary
            </a>
          </li>
          @if($isInternal)
          <li class="active">
            <a href="{{ route('supplierLeads.show', $supplierLead->id) }}">
              Profile
            </a>
          </li>
          
          <li>
            <a href="{{ route('supplierLeads.show.contacts', $supplierLead->id) }}">
              Contacts
            </a>
          </li>
          
          <li>
            <a href="{{ route('supplierLeads.show.products', $supplierLead->id) }}">
              Products
            </a>
          </li>
          @endif
        </ul>

        <ul class="nav nav-tabs ui-sortable-handle secondary-tabs"></ul>

        <div class="tab-content p-0">
        @if($isInternal)
          <div class="tab-pane active">
            <form method="POST" action="{{ route('supplierLeads.update', $supplierLead->id) }}">
              @csrf
              @method('PUT')

              <div class="row">
                <div class="row-header">
                  <h3 class="title">Business Information</h3>
                </div>

                <div class="form-group col-md-6">
                  <label for="doing_business_as">Doing Business As <span class="required">*</span></label>
                  <input type="text" 
                    name="doing_business_as" 
                    class="form-control" 
                    placeholder="Enter DBA" 
                    aria-describedby="doing_business_as_help_id"
                    value="{{ $supplierLead->doing_business_as }}">
                </div>

                <div class="form-group col-md-6">
                  <label for="business_name">Legal Name (Business Name)</label>
                  <input type="text" 
                    name="business_name" 
                    class="form-control" 
                    placeholder="Enter legal name (business name)"
                    value="{{ $supplierLead->business_name }}">
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-5 pr-0">
                  <label for="business_industry">Business Industry<span class="required"></span></label>
                  <select name="business_industry" id="business_industry" class="form-control select2">
                    @foreach ($businessTypeGroups as $groupName => $businessTypes)
                      <optgroup label="{{ $groupName }}">
                        @foreach ($businessTypes as $businessType)
                          <option value="{{ $businessType->mcc }}" {{ $supplierLead->business_type_code == $businessType->mcc ? 'selected' : '' }}>
                            {{ $businessType->description }}
                          </option>
                        @endforeach
                      </optgroup>
                    @endforeach
                  </select>

                  <small class="text-danger business_industry-error hidden">Error for select2</small>
                </div>

                <div class="form-group col-md-1 pl-0 text-center">
                  <label for="business_industry">MCC<span class="required"></span></label>
                  <input type="text" name="mcc" class="form-control" style="border-left: 0px; text-align: center">
                </div>
              </div>

              <div class="row">
                <div class="row-header">
                  <h3 class="title">Business Address</h3>
                </div>

                <div class="form-group col-md-6">
                  <label for="business_address">Business Address <span class="required">*</span></label>
                  <input type="text" 
                    name="business_address"
                    class="form-control" 
                    placeholder="Enter business address"
                    value="{{ $supplierLead->business_address }}">
                </div>

                <div class="form-group col-md-6">
                  <label for="business_address_2">Business Address 2</label>
                  <input type="text" 
                    name="business_address_2"
                    class="form-control" 
                    placeholder="Enter business address"
                    value="{{ $supplierLead->business_address_2 }}">
                </div>

                <div class="form-group col-md-3">
                  <label for="country">Country <span class="required">*</span></label>
                  <select name="country" class="form-control s2-country">
                    @foreach ($countries as $country)
                      <option value="{{ $country->id }}"
                        {{ $supplierLead->country_id == $country->id ? "selected" : ""}}
                        data-abbr="{{  $country->iso_code_2 }}">
                        {{ $country->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md-3">
                  <label for="zip">Zip <span class="required">*</span></label>
                  <input type="text" 
                    name="zip"
                    class="form-control" 
                    placeholder="Enter zip"
                    aria-describedby="zip_help"
                    value="{{ $supplierLead->zip }}">

                  @include('incs.zipHelpNote')
                </div>

                <div class="form-group col-md-3">
                  <label for="state">State <span class="required">*</span></label>
                  <select name="state" id="state" class="form-control s2-state" disabled>
                    @foreach ($countries as $country)
                      @foreach ($country->states as $state)
                        <option value="{{ $state->id }}" data-abbr="{{ $state->abbr }}" {{ $supplierLead->state_id == $state->id ? "selected" : ""}}>
                          {{ $state->name }}
                        </option>
                      @endforeach
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md-3">
                  <label for="city">City <span class="required">*</span></label>
                  {{-- <input type="text" 
                    name="city" id="city"
                    class="form-control" 
                    placeholder="Enter city"
                    value="{{ $supplierLead->city }}"> --}}
                    <select name="city" id="city" class="form-control select2" disabled>
                      <option value="{{ $supplierLead->city }}" selected>
                        {{ $supplierLead->city }}
                      </option>
                    </select>
                </div>

              </div>

              <div class="row">
                <div class="row-header">
                  <h3 class="title">Business Contact Information</h3>
                </div>

                <div class="form-group col-md-5">
                  <label for="business_phone">Business Phone <span class="required">*</span></label>
                  <div class="input-group"> 
                    <div class="input-group-addon">
                      <label for="business_phone" class="m-0">1</label>
                    </div>

                    <input type="text" 
                      name="business_phone"
                      class="form-control" 
                      placeholder="Enter business phone"
                      value="{{ $supplierLead->business_phone }}">
                  </div>
                </div>

                <div class="form-group col-md-1">
                  <label for="extension">Extension</label>
                  <input type="text" 
                    name="extension"
                    class="form-control" 
                    placeholder="Ext"
                    value="{{ $supplierLead->business_phone }}">
                </div>

                <div class="form-group col-md-6">
                  <label for="business_address">Fax</label>
                  <div class="input-group"> 
                    <div class="input-group-addon">
                      <label for="fax" class="m-0">1</label>
                    </div>

                    <input type="text" 
                      name="fax"
                      class="form-control" 
                      placeholder="Enter fax"
                      value="{{ $supplierLead->fax }}">
                  </div>
                </div>

                <div class="form-group col-md-5">
                  <label for="business_phone_2">Business Phone 2</label>
                  <div class="input-group"> 
                    <div class="input-group-addon">
                      <label for="business_phone" class="m-0">1</label>
                    </div>

                    <input type="text" 
                      name="business_phone_2"
                      class="form-control" 
                      placeholder="Enter business phone"
                      value="{{ $supplierLead->business_phone_2 }}">
                  </div>
                </div>

                <div class="form-group col-md-1">
                  <label for="extension_2">Extension</label>
                  <input type="text" 
                    name="extension_2"
                    class="form-control" 
                    placeholder="Ext"
                    value="{{ $supplierLead->extension_2 }}">
                </div>

                <div class="form-group col-md-6">
                  <label for="business_email">Email Address</label>
                  <input type="text" 
                    name="business_email"
                    class="form-control" 
                    placeholder="Enter business email"
                    value="{{ $supplierLead->business_email }}">
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 d-flex flex-row-reverse">
                  <button type="submit" class="btn btn-primary">Save</button>
                </div>
              </div>
            </form>
          </div>
        @endif  
        </div>
      </div>
    </section>
  </div>
@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <script src=@cdn('/js/supplierLeads/mcc.js')></script>
  <script>
    $(document).ready(function() {
      $('.select2').select2()

      $('input[name="business_phone"]').mask('999-999-9999', {clearIfNotMatch: true})
      $('input[name="business_phone_2"]').mask('999-999-9999', {clearIfNotMatch: true})
      $('input[name="extension"]').mask('999', {clearIfNotMatch: true})
      $('input[name="extension_2"]').mask('999', {clearIfNotMatch: true})
      $('input[name="fax"]').mask('999-999-9999', {clearIfNotMatch: true})
      $('input[name="zip"]').mask('99999', {clearIfNotMatch: true})
    })

    function isValidZip(el, city_id, state_id) {
      $('#' + city_id).prop('disabled', true);
      $('#' + state_id).prop('disabled', true);
      if (el.value.length == 5) {
          $.ajax({
              url: "/merchants/getCityState/" + el.value,
              type: "GET",
          }).done(function(data) {
              $('#' + city_id).val(data.city);
              $('#' + state_id).val(data.abbr).trigger('change');
              $('#' + el.id + '-error small').text('');
              document.getElementById(el.id).style.removeProperty('border');
              $('#' + city_id).prop('disabled', false);
              $('#' + state_id).prop('disabled', false);

          }).fail(function(data) {
            document.getElementById(el.id).style.borderColor = "red";
            $('#' + el.id + '-error small').text('Error, not a US zip code.'); 
            $('#' + el.id).val('');
            $('#' + city_id).prop('disabled', false);
            $('#' + state_id).prop('disabled', false);
          });
      }
    }

    </script>
    <script src=@cdn('/js/supplierLeads/supplierLead.js')></script>
@endsection