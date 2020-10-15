@extends('layouts.app')

@section('style')
    <link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .ticket-img-xs {
            box-shadow: 0 0 2.5px #000000;
            height: 20px;
            width: 20px;
            border: 2px solid #ffffff;
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Add Supplier Lead</h1>
      
      <ol class="breadcrumb">
        <li><a href="/">Dashboard </a></li>
        <li><a href="/leads">Leads </a></li>
        <li class="active">Add Supplier Lead</li>
      </ol>
      
      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      <div class="row">
        <div class="col-md-12">
          <ul class="progressbar nav">
            <li class="col-sm-4 bi-tab list-tab active">
              <a href="#business-info" id="bi-tab" data-toggle="tab" aria-expanded="true">
                Business Information
              </a>
            </li>
            <li class="col-sm-4 cp-tab list-tab">
              <a href="#contact-person" id="cp-tab" data-toggle="tab" aria-expanded="false">
                Contact Persons
              </a>
            </li>
            <li class="col-sm-4 ip-tab list-tab">
              <a href="#interested-product" id="ip-tab" data-toggle="tab" aria-expanded="false">
                Products
              </a>
            </li>
          </ul>
        </div>
      </div>

      <form role="form" name="frmAddLead" id="frmAddLead" method="POST" action="{{ route('supplierLeads.store') }}">
        @csrf
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs ui-sortable-handle hide">
            <li class="active">
              <a href="#business-info" id="bi-tab" data-toggle="tab" aria-expanded="true">
                Business Information
              </a>
            </li>

            <li>
              <a href="#contact-person" id="cp-tab" data-toggle="tab" aria-expanded="false">
                Contact Persons
              </a>
            </li>
            
            <li>
              <a href="#interested-product" id="ip-tab" data-toggle="tab" aria-expanded="false">
                Interested Product
              </a>
            </li>
          </ul>
          
          <div class="tab-content no-padding">
            <div class="tab-pane active" id="business-info">
              <div class="row">
                <div class="row-header">
                  <h3 class="title">Business Information</h3>
                </div>
              </div>

              <div class="row {{ $systemUser ? '' : 'hidden' }}">
                <div class="col-md-6 form-group">
                  <br>

                  <input type="checkbox" name="assignToMe" id="assignToMe" value="" {{ $systemUser ? 'checked' : '' }}/>
                  <label for="assignToMe">
                      Set Parent as 
                      @if (auth()->user()->is_original_partner != 1)
                        {{ auth()->user()->first_name . ' ' . auth()->user()->last_name}}
                        @if($userDepartment != App\Models\Partner::OWNER)
                          <span style="color:rgb(255, 165, 0)">({{ $userDepartment }})<span>
                        @endif
                      @else
                        {{ session('company_name') }}
                      @endif
                  </label>

                  <input type="hidden" name="selfAssign" id="selfAssign">
                </div>

                <div class="form-group col-md-3 assignToDiv">
                  <label for="assignTo">Parent</label>
                  <select name="assignTo" id="assignTo" class="form-control">
                    @foreach ($upline_partner_type as $item)
                      <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md-3 assignToDiv">
                  <label for="assignee">&nbsp;</label>
                  <select name="assignee" id="assignee" class="form-control select2" style="width:100%">
                    @if(isset($upline))
                      @foreach ($upline_partner_type as $item)
                        <option value="{{$item->id}}" @if( $partner_id == $item.parent_id) selected="selected" @endif>{{$item->upline_partner}} - {{$item.partner_id_reference}}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-6">
                  <label for="doing_business_as">Doing Business As <span class="required">*</span></label>
                  <input type="text" 
                    name="doing_business_as" 
                    class="form-control" 
                    placeholder="Enter DBA" 
                    aria-describedby="doing_business_as_help_id"
                    maxlength="80">
                </div>

                <div class="form-group col-md-6">
                  <label for="business_name">Legal Name (Business Name)</label>
                  <input type="text" 
                    name="business_name" 
                    class="form-control" 
                    placeholder="Enter legal name (business name)"
                    maxlength="80">
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-5 pr-0">
                  <label for="business_industry">Business Industry<span class="required"></span></label>
                  <select name="business_industry" id="business_industry" class="form-control select2">
                    @foreach ($businessTypeGroups as $groupName => $businessTypes)
                      <optgroup label="{{ $groupName }}">
                        @foreach ($businessTypes as $businessType)
                          <option value="{{ $businessType->mcc }}">
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
                    placeholder="Enter business address">
                </div>

                <div class="form-group col-md-6">
                  <label for="business_address_2">Business Address 2</label>
                  <input type="text" 
                    name="business_address_2"
                    class="form-control" 
                    placeholder="Enter business address">
                </div>

                <div class="form-group col-md-3">
                  <label for="country">Country <span class="required">*</span></label>
                  <select name="country" class="form-control s2-country">
                    @foreach ($countries as $country)
                      <option value="{{ $country->id }}"
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
                    aria-describedby="zip_help">

                  @include('incs.zipHelpNote')
                </div>

                <div class="form-group col-md-3">
                  <label for="state">State <span class="required">*</span></label>
                  <select name="state" id="state" class="form-control s2-state" disabled>
                    @foreach ($countries as $country)
                      @foreach ($country->states as $state)
                        <option value="{{ $state->id }}" data-abbr="{{ $state->abbr }}">
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
                    placeholder="Enter city"> --}}
                    <select name="city" id="city" class="form-control select2" disabled>
                      @foreach ($initialCities as $c)
                        <option value="{{ $c->city }}">
                          {{ $c->city }}
                        </option>
                      @endforeach
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
                      placeholder="Enter business phone">
                  </div>
                </div>

                <div class="form-group col-md-1">
                  <label for="extension">Extension</label>
                  <input type="text" 
                    name="extension"
                    class="form-control" 
                    placeholder="Ext">
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
                      placeholder="Enter fax">
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
                      placeholder="Enter business phone">
                  </div>
                </div>

                <div class="form-group col-md-1">
                  <label for="extension_2">Extension</label>
                  <input type="text" 
                    name="extension_2"
                    class="form-control" 
                    placeholder="Ext">
                </div>

                <div class="form-group col-md-6">
                  <label for="business_email">Email Address</label>
                  <input type="text" 
                    name="business_email"
                    class="form-control" 
                    placeholder="Enter business email"
                    aria-describedby="business_email_help">

                  <small id="business_email_help" class="text-warning">
                    Note: If left blank, Lead must have at least the Contact Person's Mobile Number.
                  </small>
                </div>
              </div>

              <div class="form-group pull-right">
                <a href="#cp-tab" class="btn btn-primary btn-next-1" data-toggle="tab">Next</a>
              </div>

              <div class="clearfix"></div>
            </div>

            <div class="tab-pane" id="contact-person">
              <div class="row">
                <div class="row-header">
                  <h3 class="title">Contacts</h3>
                </div>
              </div>

              <div id="contacts">
                <div class="row">
                  <div class="col-md-12 mb-2">
                    <h5 class="d-flex align-items-center">
                      <strong>
                        <span class="contact_number mr-2">Contact 1</span>
                      </strong>
                    </h5>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="name">First Name <span class="required">*</span></label>
                    <input type="text" 
                      name="contact_first_names[]" 
                      class="form-control alpha" 
                      placeholder="Enter first name">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="name">Middle Name</label>
                    <input type="text" 
                      name="contact_middle_names[]" 
                      class="form-control alpha" 
                      placeholder="Enter middle name"
                      maxlength="1">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="name">Last Name <span class="required">*</span></label>
                    <input type="text" 
                      name="contact_last_names[]" 
                      class="form-control alpha" 
                      placeholder="Enter last name">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="name">Position <span class="required">*</span></label>
                    <input type="text" 
                      name="contact_positions[]" 
                      class="form-control alpha" 
                      placeholder="Enter position">
                  </div>

                  <div class="form-group col-md-6">
                    <label for="name">Contact Phone 1</label>
                    <div class="input-group"> 
                      <div class="input-group-addon">
                        <label class="m-0">1</label>
                      </div>

                      <input type="text" 
                        name="contact_phones[]" 
                        class="form-control" 
                        placeholder="Enter contact phone">
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="name">Contact Phone 2</label>
                    <div class="input-group"> 
                      <div class="input-group-addon">
                        <label class="m-0">1</label>
                      </div>

                      <input type="text" 
                        name="contact_phones_2[]" 
                        class="form-control" 
                        placeholder="Enter contact phone">
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="name">Fax</label>
                    <div class="input-group"> 
                      <div class="input-group-addon">
                        <label class="m-0">1</label>
                      </div>

                      <input type="text" 
                        name="contact_faxs[]" 
                        class="form-control" 
                        placeholder="Enter fax">
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="name">Mobile</label>
                    <div class="input-group"> 
                      <div class="input-group-addon">
                        <label class="m-0">1</label>
                      </div>

                      <input type="text" 
                        name="contact_mobiles[]" 
                        class="form-control" 
                        placeholder="Enter mobile">
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group pull-right">
                <button id="btn-add-contact" type="button" class="clickable btn btn-secondary mr-2">Add Contact</button>
                <a href="#bi-tab" class="btn btn-primary btn-previous">Prev</a>
                <a href="#ip-tab" class="btn btn-primary btn-next-2">Next</a>
              </div>

              <div class="clearfix"></div>
            </div>

            <div class="tab-pane" id="interested-product">
              <div class="row">
                <div class="row-header">
                  <h3 class="title">My Products</h3>
                </div>
              </div>

              <div id="products">
                <div class="row">
                  <div class="col-md-12 mb-2">
                    <h5 class="d-flex align-items-center">
                      <strong>
                        <span class="product_number mr-2">Product 1</span>
                      </strong>
                      <span class="clickable badge badge-danger btn-delete-product">Delete</span>
                    </h5>
                  </div>

                  <div class="form-group col-md-9">
                    <label for="name">Name <span class="required">*</span></label>
                    <input type="text" 
                      name="product_names[]" 
                      class="form-control" 
                      placeholder="Enter Name">
                  </div>

                  <div class="form-group col-md-3">
                    <label>Price <span class="required">*</span></label>
                    <input type="text" 
                      name="product_prices[]" 
                      class="form-control number-only" 
                      placeholder="Enter Price" onkeypress="validate_numeric_input(event);" >
                  </div>

                  <div class="form-group col-md-12">
                    <label>Description <span class="required">*</span></label>
                    <textarea class="form-control" 
                      name="product_descriptions[]" 
                      rows="4"></textarea>
                  </div>
                </div>
              </div>

              <div class="d-flex justify-content-end mb-4">
                <button id="btn-add-product" type="button" class="clickable btn btn-secondary 1mr-2">Add Product</button>
              </div>

              <div class="d-flex justify-content-end align-items-center">
                <div class="form-check mr-4">
                  <label class="form-check-label">
                    <input class="form-check-input" 
                      type="checkbox" 
                      name="create_another" 
                      value="1" />
                    <span>Create Another</span>
                  </label>
                </div>

                <a href="#cp-tab" class="btn btn-primary btn-previous mr-2">Prev</a>
                <button type="submit" class="btn btn-primary btn-submit" id="saveLeadProspect">Submit</a>
              </div>

              <div class="clearfix"></div>
            </div>
          </div>
        </div>
      </form>
    </section>
  </div>

  <div id="add-contact-template" class="row hidden">
    <div class="col-md-12 mb-2">
      <h5 class="d-flex align-items-center">
        <strong>
          <span class="contact_number mr-2">Contact X</span>
        </strong>
        <span class="clickable badge badge-danger btn-delete-contact">Delete</span>
      </h5>
    </div>

    <div class="form-group col-md-6">
      <label for="name">First Name <span class="required">*</span></label>
      <input type="text" 
        name="contact_first_names[]" 
        class="form-control" 
        placeholder="Enter First Name">
    </div>

    <div class="form-group col-md-6">
      <label for="name">Middle Name <span class="required">*</span></label>
      <input type="text" 
        name="contact_middle_names[]" 
        class="form-control" 
        placeholder="Enter Middle Name">
    </div>

    <div class="form-group col-md-6">
      <label for="name">Last Name <span class="required">*</span></label>
      <input type="text" 
        name="contact_last_names[]" 
        class="form-control" 
        placeholder="Enter Last Name">
    </div>

    <div class="form-group col-md-6">
      <label for="name">Position <span class="required">*</span></label>
      <input type="text" 
        name="contact_positions[]" 
        class="form-control" 
        placeholder="Enter Position">
    </div>

    <div class="form-group col-md-6">
      <label for="name">Contact Phone 1</label>
      <div class="input-group"> 
        <div class="input-group-addon">
          <label class="m-0">1</label>
        </div>

        <input type="text" 
          name="contact_phones[]" 
          class="form-control" 
          placeholder="Enter contact phone">
      </div>
    </div>

    <div class="form-group col-md-6">
      <label for="name">Contact Phone 2</label>
      <div class="input-group"> 
        <div class="input-group-addon">
          <label class="m-0">1</label>
        </div>

        <input type="text" 
          name="contact_phones_2[]" 
          class="form-control" 
          placeholder="Enter contact phone">
      </div>
    </div>

    <div class="form-group col-md-6">
      <label for="name">Fax</label>
      <div class="input-group"> 
        <div class="input-group-addon">
          <label class="m-0">1</label>
        </div>

        <input type="text" 
          name="contact_faxs[]" 
          class="form-control" 
          placeholder="Enter fax">
      </div>
    </div>

    <div class="form-group col-md-6">
      <label for="name">Mobile</label>
      <div class="input-group"> 
        <div class="input-group-addon">
          <label class="m-0">1</label>
        </div>

        <input type="text" 
          name="contact_mobiles[]" 
          class="form-control" 
          placeholder="Enter mobile">
      </div>
    </div>
  </div>

  <div id="add-product-template" class="row hidden">
    <div class="col-md-12 mb-2">
      <h5 class="d-flex align-items-center">
        <strong>
          <span class="product_number mr-2">Product X</span>
        </strong>
        <span class="clickable badge badge-danger btn-delete-product">Delete</span>
      </h5>
    </div>

    <div class="form-group col-md-9">
      <label for="name">Name <span class="required">*</span></label>
      <input type="text" 
        name="product_names[]" 
        class="form-control" 
        placeholder="Enter Name">
    </div>

    <div class="form-group col-md-3">
      <label>Price <span class="required"></span></label>
      <input type="text" 
        name="product_prices[]" 
        class="form-control number-only" 
        placeholder="Enter Price" onkeypress="validate_numeric_input(event);"  >
    </div>

    <div class="form-group col-md-12">
      <label>Description <span class="required"></span></label>
      <textarea class="form-control" 
        name="product_descriptions[]" 
        rows="4"></textarea>
    </div>
  </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    @if (!$systemUser)
        <script>
            $(document).ready(function() {
                $("#assignee").prop("disabled", false)      
                $("#assignTo").prop("disabled", false) 
            })
        </script>
    @endif
  <script type="text/javascript">
    
    function validate_numeric_input(evt) {
        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
        var regex = /[0-9\b]|\./;
        if (!regex.test(key)) {
            theEvent.returnValue = false;
            if (theEvent.preventDefault) theEvent.preventDefault();
        }
    }

    $(document).on('change', '.number-only', function (e) {
      var x = $(this).val();
      x = parseFloat(x).toFixed(2);
      if(x > 1000000000){
        alert('Invalid Price Amount!');
        $(this).val('0.00');
      }else{
        $(this).val(parseFloat(x).toFixed(2));
      }
      

    }); 

  </script>
  <script src=@cdn('/js/supplierLeads/supplierLead.js')></script>
  <script src=@cdn('/js/supplierLeads/contacts.js')></script>
@endsection