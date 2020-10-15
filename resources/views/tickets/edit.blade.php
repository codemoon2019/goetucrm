@extends('layouts.app')

@section('style')
  <link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href=@cdn('/css/tickets/edit.css') />
  <style>
    .hidden-select2 .select2 {
      display: none;
    }
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <div class="content-header">
      <div class="row">
        <div class="col-sm-12" style="display:flex;  flex-direction: column;">
          <div style="align-self: center;">
            <h2>
              <i class="fa fa-ticket"></i> &nbsp; 
              <strong>{{ $ticketHeader->subject }}</strong>&nbsp;&nbsp;&nbsp;
              <span class="badge badge-primary" style="font-size: 0.5em !important; transform: translateY(-7px)">
                {{ $ticketHeader->ticketStatus->description }}
              </span>
            </h2>
          </div>

          <div style="align-self: center;">
            <span class="datecreated">
              {{ $ticketHeader->created_at->format('l, F j Y') }}
            </span>
            <span>&nbsp;|&nbsp;</span>
            <span>For {{ $ticketHeader->requester->full_name }}</span>
                            
            @if ($ticketHeader->create_by != $ticketHeader->requester->username)
              <span>via {{$ticketHeader->createdBy->full_name}}</span>
            @endif
            
            <span>&nbsp;|&nbsp;Ticket #{{ $ticketHeader->id }}</span>
          </div>
        </div>
      </div>
    </div>

    @if ($ticketUserClassification->isInternal)
      @php
        $lastTicketActivity = $ticketActivities->where('support_responsed_at', '<>', null)
            ->sortByDesc('id')
            ->first();
      @endphp

      @isset ($lastTicketActivity)
        <div class="text-center">
          <span>
            <span>Assigned by</span>  
            <span>
              <strong>{{ $lastTicketActivity->createdBy->full_name }}</strong>
              <span>of</span>
              <strong>{{ $lastTicketActivity->createdBy->department_names }}</strong>
            </span>
          </span> 
        </div>
      @endisset
    @endif

    @isset ($ticketHeader->userType)
      <div class="text-center">
        <p>
          <span>
            <span>Assigned to</span>  
            
            @isset ($ticketHeader->assignedTo)
              <span>
                <strong>
                  {{ $ticketHeader->assignedTo->full_name }}
                </strong> 
                <span>of</span>
              </span>
            @endisset

            <span>
              <strong>
                {{ $ticketHeader->userType->description }}
              </strong>
              <span>Department</span>
            </span>
          </span> 
        </p>
      </div>
    @endisset

    <form id="form-edit-ticket" method="POST" autocomplete="off">
      @csrf

      <input type="hidden" name="workflow_ticket" value="{{ (int) $isWorkflowTicket }}">
      <input type="hidden" name="user_classification" value="{{ $ticketUserClassification->getClassification() }}">
      <input type="hidden" name="access_classification" value="{{ $ticketAccessClassification->getClassification() }}">

      <div class="content-header content-sub-header">
        <div class="row form-essentials">
          @if ($ticketUserClassification->isInternal)
            <div class="col-sm-12 col-md-6 col-lg-3">
              <div class="form-group">
                <label>Priority</label>
                
                <select class="js-example-basic-single form-control" 
                  name="ticket_priority_code"
                  {{ ($viewOnly ?? false) || ($replyOnly ?? false) ? 'disabled' : '' }}>

                  @foreach ($ticketDependencies->ticketPriorities as $ticketPriority)
                    <option value="{{ $ticketPriority->code }}" 
                      {{ $ticketPriority->code == $ticketHeader->priority ? 'selected' : '' }}>
                      
                      {{ $ticketPriority->description }}
                    </option>
                  @endforeach
                </select>

                <p id="form-error-ticket_priority_code" class="form-error hidden"></p>
              </div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-3">
              @component('tickets.components.assignees', [
                'departmentsGroups' => $departmentsGroups,
                'ticketHeaderDepartmentId' => $ticketHeader->department,
                'allPrivileges' => $allPrivileges,
                'replyOnly' => $replyOnly,
                'viewOnly' => $viewOnly,
              ])
              @endcomponent
            </div>
          @endif
            
          {!! $ticketUserClassification->isInternal ? '' : '<div class="col-sm-12 col-lg-8 offset-lg-2">' !!}
            <div class="col-sm-12 col-md-12 {{ $ticketUserClassification->isInternal ? 'col-lg-6' : 'px-0 col-lg-12' }} ">
              @component('tickets.components.ccs', [
                'usersGroups' => $ticketDependencies->usersGroups,
                'ticketHeaderCCs' => $ticketHeader->ccs,
                'allPrivileges' => $allPrivileges,
                'replyOnly' => $replyOnly,
                'viewOnly' => $viewOnly,
              ])
              @endcomponent
            </div>
          {!! $ticketUserClassification->isInternal ? '' : '</div>' !!}
        </div>
      </div>

      <div class="content-body">
        @if ($ticketUserClassification->isInternal && $isWorkflowTicket) 
          <div class="section-link-to-task text-center p-4">
            <h4 class="mt-2">
              @php $partnerId = $ticketHeader->subtask->task->productOrder->partner_id; @endphp
              @php $productOrderId = $ticketHeader->subtask->task->productOrder->id; @endphp

              <a target="_new" href="/merchants/{{ $partnerId }}/product-orders/{{ $productOrderId }}/workflow">
                This ticket is part of a Workflow <br /> 
                Product Order #{{ $productOrderId }}
              </a>
            </h4>
          </div>
        @endif

        <div class="col-sm-12 col-lg-8 offset-lg-2">
          <br>

          @component('tickets.components.products', [
            'productsGroups' => $ticketDependencies->productsGroups,
            'ticketHeaderProduct' => $ticketHeader->product,
            'allPrivileges' => $allPrivileges,
            'replyOnly' => $replyOnly,
            'viewOnly' => $viewOnly,
            'isWorkflowTicket' => $isWorkflowTicket
          ])
          @endcomponent

          @if ($ticketUserClassification->isInternal || $ticketUserClassification->isPartner)
            <div class="form-check form-check-inline {{ $viewOnly || $replyOnly || $isWorkflowTicket ? 'hidden' : '' }}">
              <label class="form-check-label">
                <input type="radio" name="reference" class="form-check-input" value="Merchant">Merchant
              </label>
            </div>

            <div class="form-check form-check-inline mb-2 {{ $viewOnly || $replyOnly || $isWorkflowTicket ? 'hidden' : '' }}">
              <label class="form-check-label">
                <input type="radio" name="reference" class="form-check-input" value="Partner">Partner
              </label>
            </div>

            @component('tickets.components.merchants', [
              'merchantsGroups' => $ticketRequesterAccessor->merchantsGroups,
              'ticketHeaderRequester' => $ticketHeader->requester,
              'replyOnly' => $replyOnly,
              'viewOnly' => $viewOnly,
              'isWorkflowTicket' => $isWorkflowTicket,
            ])
            @endcomponent

            @if (!$isWorkflowTicket)
              @component('tickets.components.partners', [
                'partnersGroups' => $ticketRequesterAccessor->partnersGroups,
                'ticketHeaderRequester' => $ticketHeader->requester,
                'replyOnly' => $replyOnly,
                'viewOnly' => $viewOnly,
                'ticketHeader'  => $ticketHeader,
                'isWorkflowTicket' => $isWorkflowTicket,
              ])
              @endcomponent
            @endif
          @endif

          <div class="row {{ $viewOnly || $replyOnly || $isWorkflowTicket ? 'flex-column' : '' }}">
            <div class="form-group col-sm-12 col-lg-6 {{ $viewOnly || $replyOnly || $isWorkflowTicket ? 'hidden-select2' : '' }}">
              <label>Issue Type</label>
              
              @if ($viewOnly || $replyOnly || $isWorkflowTicket)
                <p class="text-sm pl-3 mb-0">{{ $ticketHeader->ticketIssueType->description }}</p>
              @else
                <select class="js-example-basic-single form-control"
                  name="ticket_type_code" 
                  data-placeholder="Select Issue Type"
                  data-allow-clear="true">
                </select>
                
                <p id="form-error-ticket_type_code" class="form-error hidden"></p>
              @endif 
            </div>
    
            <div class="form-group col-lg-6">
              <label>Reason</label>

              @if ($viewOnly || $replyOnly || $isWorkflowTicket)
                <p class="text-sm pl-3 mb-0">{{ $ticketHeader->ticketReason->description }}</p>
              @else
                <select class="js-example-basic-single form-control"
                  name="ticket_reason_code"
                  data-placeholder="Select Reason"
                  data-allow-clear="true">
                </select>

                <p id="form-error-ticket_reason_code" class="form-error hidden"></p>
              @endif 
            </div>
          </div>

          @if ($ticketUserClassification->isInternal)
            <div class="row">
              <div class="form-group col-lg-6">
                <label>Due Date</label>
                
                @if ($viewOnly || $replyOnly)
                  <p class="text-sm pl-3 mb-0">
                    {{ $ticketHeader->due_date->format('M d, Y H:i A') }}
                  </p>
                @else
                  <input name="due_date" 
                    type='text' 
                    id="datepicker" 
                    class="form-control" 
                    placeholder="Click to select date" 
                    value="{{ substr($ticketHeader->due_date, 0, 10) }}" />

                  <p id="form-error-due_date" class="form-error hidden"></p>
                @endif
              </div>
          
              @if ($allPrivileges)
                <div class="form-group col-lg-6 {{ $viewOnly || $replyOnly ? 'hidden' : '' }}">
                  <label>&nbsp;</label>
                  
                  <input name="due_time" 
                    type="text" 
                    id="timepicker" 
                    class="form-control" 
                    placeholder="Click to select time"
                    value="{{ substr($ticketHeader->due_date, 11, 5) }}" />

                  <p id="form-error-due_time" class="form-error hidden"></p>
                </div>
              @endif
            </div>
          @endif

          @if ($replyOnly || $allPrivileges)
            <div class="row container-reply">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="reply-type reply-active reply-public">
                    <input type="radio" name="is_internal_note" value="0" selected>Public Reply
                  </label>
                  
                  @if ($ticketUserClassification->isInternal)
                    <label class="reply-type reply-internal">
                      <input type="radio" name="is_internal_note" value="1">Internal Note
                    </label>
                  @endif

                  <textarea class="form-control" name="message"></textarea>
                  <p id="form-error-message" class="form-error hidden"></p>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <input id="inputAttachments" type="file" name="attachments[]" multiple />
                </div>

                <button class="btn btn-sm btn-danger clear-input" 
                  data-file_id="inputAttachments">
                  
                  Clear Input
                </button>
              </div>
            </div>

            <br>

            <div class="col-12 px-0">
              <div class="pull-right">
                <a class="btn btn-danger btn-cancel" href="/tickets">Cancel</a>&nbsp;
                @if ($ticketUserClassification->isInternal)
                  <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <button type="button" class="btn btn-info btn-submit-reply" data-ticket_status_code="{{ $ticketHeader->status }}">
                      Submit
                    </button>

                    <div class="btn-group" role="group">    
                      <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                      <div class="dropdown-menu">
                        @foreach($ticketDependencies->ticketStatuses as $ticketStatus)
                          @if ($ticketHeader->status != $ticketStatus->code)
                            <a href="#" class="dropdown-item btn-submit-reply" data-ticket_status_code="{{ $ticketStatus->code }}">
                              Submit as {{ $ticketStatus->description }}
                            </a>
                          @endif
                        @endforeach
                      </div>
                    </div>
                  </div>
                @else
                  <button type="button" class="btn btn-info btn-submit-reply">
                    Submit
                  </button>
                @endif
              </div>
            </div>

            <div class="clearfix"></div>
          @endif

          <br>
          <br>
          <br>
        </div>

        <div class="row" style="font-size: 1.25rem; font-weight: bold">
          <div class="{{ $ticketUserClassification->isInternal ? 'col-md-9 pr-0' : 'col-md-12'}} text-center">Comments</div>

          @if ($ticketUserClassification->isInternal)
            <div class="col-md-3 text-center">Activity Log</div>
          @endif
        </div>

        <div class="row py-0" style="border-top: 3px solid black">
          <!-- 
                  Ticket Details
            ==========================
            - Shows ticket description
            - Shows replies/details 
            regarding this ticket
          -->
          <div class="{{ $ticketUserClassification->isInternal ? 'col-md-9 pr-0' : 'col-md-12'}}">
            <div class="msg-reply" style="background-color: lightblue; border-bottom: 1.5px solid black">
              <div class="msg-user-pic"><img src="{{ $ticketHeader->createdBy->image }}"></div>
              <div class="msg-content">
                <p class="msg-head">{{ $ticketHeader->createdBy->full_name ?? 'System' }}
                  <span class="time-ago">
                    {{ $ticketHeader->created_at->diffForHumans() }}
                  </span>
                </p>

                <p>{!! html_entity_decode($ticketHeader->description) !!}</p>

                <div class="clearfix"></div>

                @if (count($ticketHeader->attachments) != 0)
                <div class="row">
                  @foreach($ticketHeader->attachments as $attachment)
                  <div class="col-sm-2 text-center">
                    <i class="fa fa-file fa-1x text-center"></i>
                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url('attachment/ticket/'.$attachment->path) }}"
                      target="_blank">{{$attachment->name}}</a>
                  </div>
                  @endforeach
                </div>
                @endif
              </div>
            </div>

            <div id="reply-section" style="width:100%"></div>
          </div>

          @if ($ticketUserClassification->isInternal)
            <!-- 
                  Ticket Activity
              =======================
              - Shows changes made 
              by users to this ticket 
              - ta = ticket-activity
            -->
            <div class="col-md-3 ta-log">
              @foreach ($ticketActivities as $ticketActivity)
                <div class="ta-item">
                  <div class="ta-item-actor">
                    <img class="ta-item-actor-image ticket-img-md" src="{{ $ticketActivity->createdBy->image }}">
                    <span class="ta-item-actor-details">
                      <span class="ta-item-actor-name">{{ $ticketActivity->createdBy->full_name }}</span>
                      <span class="ta-item-actor-dept">{{ $ticketActivity->createdBy->department_names }}</span>
                    </span><!--/ta-item-actor-details-->
                  </div><!--/ta-item-actor-->
        
                  <div class="ta-item-datetime">
                    <a data-toggle="collapse" href="#ta-item-changes-{{ $ticketActivity->id }}">
                      <span>made changes {{ $ticketActivity->created_at->diffForHumans() }}&nbsp;</span>
                      <span class="fa fa-caret-down"></span>
                    </a>
                  </div><!--/ta-item-datetime-->
        
                  <div id="ta-item-changes-{{ $ticketActivity->id }}" class="ta-item-changes collapse">
                    @foreach (json_decode($ticketActivity->changes) as $change)
                      <div class="ta-item-changes-item">
                        <span class="ta-item-changes-item-field ticket-bold">{{ $change->readable_field }}</span>
                        <span class="ta-item-changes-item-value">
                          @if (strlen($change->previous_value) > 12)
                            <span>{{ substr($change->previous_value, 0, 12) }}...</span>&nbsp;
                          @else 
                            <span>{{ $change->previous_value }}</span>&nbsp;
                          @endif

                          <span class="fa fa-minus"></span>
                          <span class="fa fa-arrow-right" style="transform: translateY(-1px)"></span>&nbsp;

                          @if (strlen($change->new_value) > 12)
                            <span>{{ substr($change->new_value, 0, 12) }}...</span>&nbsp;
                          @else 
                            <span>{{ $change->new_value }}</span>&nbsp;
                          @endif
                        </span><!--/ta-item-changes-item-value-->
                      </div><!--/ta-item-changes-item-->
                    @endforeach
                  </div><!--ta-item-changes-->
                </div><!--/ta-item-->
              @endforeach
            </div><!--/ta-log-->
          @endif
        </div>
      </div>
    </form>
  </div>

  <input id="server-data-internal" type="hidden" value="{{ $ticketUserClassification->isInternal ? '1' : '0'}}" />
  <input id="server-data-th-id" type="hidden" value="{{ $ticketHeader->id }}" />
  <input id="server-data-path" type="hidden" value="{{ \Illuminate\Support\Facades\Storage::disk('public')->url('attachment/ticket/') }}" />

  <div class="overlay">
    <div id="text">
      <img width="75px"   
        height="75px"
        src="https://ubisafe.org/images/transparent-gif-loading-5.gif"/>
    </div>
  </div>
@endsection

@section('script')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

  <script>
    /** 
     * Variable Initialization
     */
    let isInternal = {{ $ticketUserClassification->isInternal ? 'true' : 'false' }}
    let isPartner = {{ isset($ticketUserClassification->isPartner) && $ticketUserClassification->isPartner ? 'true' : 'false' }}
    let ticketHeader = @json($ticketHeader);
    let ticketReasons = @json($ticketDependencies->ticketReasons);
    let ticketIssueTypes = @json($ticketDependencies->ticketIssueTypes);
    let ticketReasonId = @json($ticketHeader->reason);
    let ticketIssueTypeId = @json($ticketHeader->type);

    @if ($ticketUserClassification->isInternal)
      let departments = @json($departments);
      let isWorkflowTicket = @json($isWorkflowTicket);
    @endif

  </script>

  <script src=@cdn('/js/ticket/edit.js')></script>
  <script src=@cdn('/js/ticket/replySection.js')></script>
  <script src=@cdn('/js/clearInput.js')></script>
@endsection