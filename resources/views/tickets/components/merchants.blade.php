@php $isWorkflowTicket = $isWorkflowTicket ?? false; @endphp
@php $viewOnly = $viewOnly ?? false; @endphp
@php $replyOnly = $replyOnly ?? false; @endphp
@php $allPrivileges = $allPrivileges ?? false; @endphp

{{-- `merchant` is an user instance --}}
<div class="form-group form-group-merchant" style="margin-top: 10px">
  <label>Merchant</label>

  @if (isset($ticketHeaderRequester) && ($viewOnly || $replyOnly || $isWorkflowTicket))
    <div class="ta-item-actor pl-3">
      <img class="ta-item-actor-image ticket-img-md" src="{{ $ticketHeaderRequester->image }}">
      <span class="ta-item-actor-details">
        <span class="ta-item-actor-name text-sm">{{ $ticketHeaderRequester->partner->partnerCompany->company_name }}</span>
        <span class="ta-item-actor-dept">{{ $ticketHeaderRequester->username }}</span>
      </span><!--/ta-item-actor-details-->
    </div>
  @else
    <select class="js-example-basic-single form-control" 
      name="merchant" 
      data-placeholder="Select Merchant" 
      data-allow-clear="true">
    
      <option></option>
    
      @foreach ($merchantsGroups as $merchants)
        @php 
          $companyId = $merchants->first()->company_id;
          $companyName = $merchants->first()->company_name; 
        @endphp
      

        <optgroup label="{{ $companyName }}" class="optgroup optgroup-{{ $companyId }}">
          @foreach ($merchants as $merchant)
            <option value="{{ $merchant->id }}" 
              data-image="{{ $merchant->image }}" 
              data-user_type='{{ $merchant->username }}'
              {{ $merchant->id == ($ticketHeaderRequester->id ?? null) ? 'selected' : '' }}>
              
              {{ " {$merchant->self_company_name}" }}
            </option>
          @endforeach
        </optgroup>
      @endforeach
    </select>

    <p id="form-error-merchant" class="form-error hidden"></p>
  @endif
</div>