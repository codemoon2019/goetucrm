{{-- `partner` is an user instance --}}
<div class="form-group form-group-partner" style="margin-top: 10px">
  <label>Partner</label>

  @if (isset($ticketHeaderRequester) && ($viewOnly || $replyOnly))
    <div class="ta-item-actor pl-3">
      <img class="ta-item-actor-image ticket-img-md" src="{{ $ticketHeaderRequester->image }}">
      <span class="ta-item-actor-details">
        <span class="ta-item-actor-name text-sm">{{  $ticketHeader->requester->full_name }}</span>
        <span class="ta-item-actor-dept">{{ $ticketHeader->requester->username }}</span>
      </span><!--/ta-item-actor-details-->
    </div>
  @else
    <select class="js-example-basic-single form-control" 
      name="partner" 
      data-placeholder="Select Partner" 
      data-allow-clear="true">
      
      <option></option>
      
      @foreach ($partnersGroups as $partners)
        @php 
          $companyId = $partners->first()->company_id;
          $companyName = $partners->first()->company_name; 
        @endphp
        
        <optgroup label="{{ $companyName }}" class="optgroup optgroup-{{ $companyId }}">
          @foreach ($partners as $partner)
            <option value="{{ $partner->id }}" 
              data-image="{{ $partner->image }}" 
              data-user_type="{{ $partner->username }}"
              {{ $partner->id == ($ticketHeaderRequester->id ?? null) ? 'selected' : '' }}>
              
              {{ " {$partner->first_name} {$partner->last_name}" }}
            </option>
          @endforeach
        </optgroup>
      @endforeach
    </select>
    
    <p id="form-error-partner" class="form-error hidden"></p>
  @endif
</div>