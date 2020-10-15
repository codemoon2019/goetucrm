@php $isWorkflowTicket = $isWorkflowTicket ?? false; @endphp
@php $viewOnly = $viewOnly ?? false; @endphp
@php $replyOnly = $replyOnly ?? false; @endphp
@php $allPrivileges = $allPrivileges ?? false; @endphp

<div class="form-group">
  <label>Product</label>

  <div class="ta-item-actor pl-3">
    <img class="ta-item-actor-image ticket-img-md" src="{{ $ticketHeaderProduct->display_picture_url }}">
    <span class="ta-item-actor-details">
      <span class="ta-item-actor-name text-sm">{{ $ticketHeaderProduct->name }}</span>
      <span class="ta-item-actor-dept">{{ $ticketHeaderProduct->code }}</span>
    </span><!--/ta-item-actor-details-->
  </div>

  <div class="hidden">
    <select class="js-example-basic-single form-control" 
      name="product" 
      data-placeholder="Select Product" 
      data-allow-clear="true">
      
      <option></option>
      
      @foreach ($productsGroups as $products)
        @php $company = $products->first()->partnerCompany; @endphp

        <optgroup label="{{ $company->company_name }}" class="optgroup optgroup-{{ $company->id }}">
          @foreach ($products as $product)
            <option value="{{ $product->id }}" 
              data-image="{{ url("storage/{$product->display_picture}") }}" 
              data-user_type="{{ $product->code }}" 
              data-company_id={{ $company->partner_id }}
              {{ $product->id == ($ticketHeaderProduct->id ?? null) ? 'selected' : '' }}>
              
              {{ " {$product->name}" }}
            </option>
          @endforeach
        </optgroup>
      @endforeach
    </select>
  </div>

  <p id="form-error-product" class="form-error hidden"></p>
</div>