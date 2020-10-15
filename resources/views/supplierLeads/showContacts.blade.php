@extends('layouts.app')

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
          <li>
            <a href="{{ route('supplierLeads.show', $supplierLead->id) }}">
              Profile
            </a>
          </li>
          
          <li class="active">
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
            <form id="form-update-contacts" method="POST" action="{{ route('supplierLeads.update.contacts', $supplierLead->id) }}">
              @csrf
              @method('PUT')

              <div class="row">
                <div class="row-header">
                  <h3 class="title">Contacts</h3>
                </div>
              </div>

              <div id="contacts">
                @foreach ($supplierLead->contacts as $i => $contact)
                  <div class="row">
                    <div class="col-md-12 mb-2">
                      <h5 class="d-flex align-items-center">
                        <strong>
                          <span class="contact_number mr-2">Contact {{ $i + 1 }}</span>
                        </strong>

                        @if ($i == 0)
                          <span class="clickable badge badge-danger btn-delete-contact">Delete</span>
                        @endif
                      </h5>
                    </div>

                    <input type="hidden" name="contact_ids[]" value="{{ $contact->id }}">
                    
                    <div class="form-group col-md-6">
                      <label for="name">First Name <span class="required">*</span></label>
                      <input type="text" 
                        name="contact_first_names[]" 
                        class="form-control alpha" 
                        placeholder="Enter first name"
                        value="{{ $contact->first_name }}"
                        maxlength="50">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="name">Middle Name</label>
                      <input type="text" 
                        name="contact_middle_names[]" 
                        class="form-control alpha" 
                        placeholder="Enter middle name"
                        value="{{ $contact->middle_name }}"
                        maxlength="1">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="name">Last Name <span class="required">*</span></label>
                      <input type="text" 
                        name="contact_last_names[]" 
                        class="form-control alpha" 
                        placeholder="Enter last name"
                        value="{{ $contact->last_name }}"
                        maxlength="50">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="name">Position <span class="required">*</span></label>
                      <input type="text" 
                        name="contact_positions[]" 
                        class="form-control alpha" 
                        placeholder="Enter position"
                        value="{{ $contact->position }}"
                        maxlength="50">
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
                          placeholder="Enter contact phone"
                          value="{{ $contact->phone }}">
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
                          placeholder="Enter contact phone"
                          value="{{ $contact->phone_2 }}">
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
                          placeholder="Enter fax"
                          value="{{ $contact->fax }}">
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
                          placeholder="Enter mobile"
                          value="{{ $contact->mobile }}">
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              
              <div class="row">
                <div class="col-md-12 d-flex flex-row-reverse">
                  <button type="submit" class="btn btn-primary">Save</button>
                  <button id="btn-add-contact" type="button" class="clickable btn btn-secondary mr-2">Add Contact</button>
                </div>
              </div>
            </form>
          </div>
        @endif
        </div>
      </div>
    </section>
  </div>

  <div id="add-contact-template" class="row hidden">
    <div class="col-md-12 mb-2">
      <h5 class="d-flex align-items-center">
        <strong>
          <span class="contact_number mr-2">Contact {{ $i + 1 }}</span>
        </strong>
        <span class="clickable badge badge-danger btn-delete-contact">Delete</span>
      </h5>
    </div>

    <div class="form-group col-md-6">
      <label for="name">First Name <span class="required">*</span></label>
      <input type="text" 
        name="contact_first_names[]" 
        class="form-control alpha" 
        placeholder="Enter First Name"
        maxlength="50">
    </div>

    <div class="form-group col-md-6">
      <label for="name">Middle Name</label>
      <input type="text" 
        name="contact_middle_names[]" 
        class="form-control alpha" 
        placeholder="Enter Middle Name"
        maxlength="50">
    </div>

    <div class="form-group col-md-6">
      <label for="name">Last Name <span class="required">*</span></label>
      <input type="text" 
        name="contact_last_names[]" 
        class="form-control alpha" 
        placeholder="Enter Last Name"
        maxlength="1">
    </div>

    <div class="form-group col-md-6">
      <label for="name">Position <span class="required">*</span></label>
      <input type="text" 
        name="contact_positions[]" 
        class="form-control alpha" 
        placeholder="Enter Position"
        maxlength="50">
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
          placeholder="Enter contact phone"
          value="{{ $contact->phone }}">
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
          placeholder="Enter contact phone"
          value="{{ $contact->phone_2 }}">
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
          placeholder="Enter fax"
          value="{{ $contact->fax }}">
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
@endsection

@section('script')
  <script src=@cdn('/js/supplierLeads/contacts.js')></script>
  <script>
    $(document).ready(function() {
      $('form').on('submit', function(e) {
        if (!validateContacts()) {
          e.preventDefault()
        }
      })
    })
  </script>
@endsection