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
          
          <li>
            <a href="{{ route('supplierLeads.show.contacts', $supplierLead->id) }}">
              Contacts
            </a>
          </li>
          
          <li class="active">
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
            <form method="POST" action="{{ route('supplierLeads.update.products', $supplierLead->id) }}">
              @csrf
              @method('PUT')

              <div class="row">
                <div class="row-header">
                  <h3 class="title">My Products</h3>
                </div>
              </div>

              <div id="products">
                @foreach ($supplierLead->products as $i => $product)
                  <div class="row">
                    <div class="col-md-12 mb-2">
                      <h5 class="d-flex align-items-center">
                        <strong>
                          <span class="product_number mr-2">Product {{ $i + 1 }}</span>
                        </strong>
                        <span class="clickable badge badge-danger btn-delete-product">Delete</span>
                      </h5>
                    </div>


                    <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
                    <div class="form-group col-md-9">
                      <label for="name">Name <span class="required">*</span></label>
                      <input type="text" 
                        name="product_names[]" 
                        class="form-control" 
                        placeholder="Enter Name"
                        value="{{ $product->name }}">
                    </div>

                    <div class="form-group col-md-3">
                      <label>Price <span class="required">*</span></label>
                      <input type="text" 
                        name="product_prices[]" 
                        class="form-control number-only" 
                        placeholder="Enter Price"
                        value="{{ $product->price }}" onkeypress="validate_numeric_input(event);">
                    </div>

                    <div class="form-group col-md-12">
                      <label>Description <span class="required">*</span></label>
                      <textarea class="form-control" 
                        name="product_descriptions[]" 
                        rows="4">{{ $product->description }}</textarea>
                    </div>
                  </div>
                @endforeach
              </div>
              
              <div class="row">
                <div class="col-md-12 d-flex flex-row-reverse">
                  <button type="submit" class="btn btn-primary">Save</button>
                  <button id="btn-add-product" type="button" class="clickable btn btn-secondary mr-2">Add Product</button>
                </div>
              </div>
            </form>
          </div>
        @endif
        </div>
      </div>
    </section>
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
      <label>Price <span class="required">*</span></label>
      <input type="text" 
        name="product_prices[]" 
        class="form-control number-only" 
        placeholder="Enter Price" onkeypress="validate_numeric_input(event);">
    </div>

    <div class="form-group col-md-12">
      <label>Description <span class="required">*</span></label>
      <textarea class="form-control" 
        name="product_descriptions[]" 
        rows="4"></textarea>
    </div>
  </div>
@endsection

@section('script')
  <script src=@cdn('/js/supplierLeads/products.js')></script>
  <script>
    $(document).ready(function() {
      $('form').on('submit', function(e) {
        if (!validateProducts()) {
          e.preventDefault()
        }
      })
    })
  </script>


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
  
@endsection