@extends('layouts.app')

@section('style')
  <link rel="stylesheet" type="text/css" href=@cdn('/css/workflow/kanban.css') />
  <link rel="stylesheet" type="text/css" href=@cdn('/css/workflow/recentActivities.css') />
@endsection

@section('content')
  <div class="content-wrapper">
    {{-- Main Header --}}
    <section class="content-header">
      <h1>Product Order Workflow</h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="/merchants">Merchants</a>
        </li>
        
        <li class="breadcrumb-item">
          <a href="/merchants/details/{{$merchant->id}}/products">
            Products
          </a>
        </li>

        <li class="breadcrumb-item">
            Product Order Workflow
        </li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      {{-- Header --}}
      <div class="row mb-2 px-2">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <div class="mr-4">
              <h2 class="mb-0"><strong>{{ $task->name }}</strong></h2>
              <small>Product Order {{ str_pad($productOrder->id, 6, '0', STR_PAD_LEFT) }}</small>
            </div>

            <div class="ml-2">
              <span class="h3 mb-0">
                <i class="fa fa-chevron-circle-down clickable btn-toggle-subheader"></i>
              </span>
            </div>
          </div>

          <div class="d-flex">
            <div class="mx-3">
              <h6 class="mb-0">{{ $task->completed_subtasks_count }} / {{ $task->subtasks->count() }}</h6>
              <small>Subtasks completed</small>
            </div>

            <div class="d-flex align-items-center mx-3">
              <img src="{{ \Illuminate\Support\Facades\Storage::url($product->display_picture) }}"
                width="35px"
                height="35px"
                class="mr-2">

              <div class="d-flex flex-column">
                <h6 class="mb-0">{{ $product->description }}</h6>
                <small>{{ $product->code }}</small>
              </div>
            </div>

            <div class="d-flex align-items-center ml-3">
              <img src="{{ $merchant->connectedUser->image }}"
                width="35px"
                height="35px"
                class="mr-2">

              <div class="d-flex flex-column">
                <h6 class="mb-0">{{ $merchant->partnerCompany->company_name }}</h6>
                <small>{{ $merchant->partner_id_reference }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>


      {{-- Sub Header --}}
      <div class="row mb-2 px-2 subheader hidden">
        <div class="col-md-12 mb-3">
          <hr class="m-0">
        </div>

        <div class="w-100"></div>
        
        <div class="col-md-6">
          <p class="mb-1">
            {{ $task->description }}
          </p>
        </div>
      </div>


      {{-- Recent Activities --}}
      <div class="row px-2">
        <div class="col-md-12 text-right">
          <a href="#" class="btn-show-recent-activities">
            <h4>Recent Activities</h4>
          </a>
        </div>
      </div>

      {{-- Kanban Board --}}
      <div class="row px-2">
        <div class="col-md-3">
          <div class="box box-info column column-to-do">
            <div class="box-header">
              <h3 class="box-title mx-1 mt-2">
                <strong>To Do</strong>
              </h3>
            </div>

            <div class="box-body" data-order="1" data-status_code="N">
              <div class="btn-add-subtask clickable px-3 py-2 mb-3" data-toggle="modal" data-target="#modal-add-subtask">
                <strong><i class="fa fa-plus mr-1"></i></strong> 
                <span>Add Subtask</span>
              </div>

              @foreach (($subtasks['N'] ?? []) as $subtask)
              <div id="subtask-{{ $subtask->id }}" class="subtask p-3 mb-3" data-order="1" draggable="true">
                <a href="/tickets/{{ $subtask->ticketHeader->id }}/edit">
                  <div class="subtask-header">
                      @php $priority = $subtask->ticketHeader->ticketPriority->description @endphp
                      <span class="badge priority-{{ strtolower($priority) }} px-2 py-1">
                        {{ $priority }} Priority
                      </span>
                      
                      @if (isset($subtask->ticketHeader->assignedTo))
                        <img src="{{ $subtask->ticketHeader->assignedTo->image }}" 
                          width="30px"
                          height="30px">
                      @endif
                    </div>

                    <div class="subtask-body py-4">
                      <p class="m-0">
                        <strong>{{ $subtask->task_no }}. {{ $subtask->ticketHeader->subject }}</strong>
                      </p>
                    </div>

                    <div class="subtask-footer">
                      <small>
                        <i class="fa fa-comment"></i> 
                        <span>{{ $subtask->ticketHeader->ticketDetails()->count() }}</span>
                      </small>
                      <small>Due {{ $subtask->ticketHeader->due_date->format('m/d/Y H:iA') }}</small>
                    </div>
                </a>
              </div>
              @endforeach

              @foreach (($subtasks[''] ?? []) as $subtask)
                <div class="subtask subtask-dark p-3 mb-2">
                  <div class="subtask-body">
                    <p class="mt-0 mb-1 text-right">Prereq: Subtask #{{ $subtask->prerequisite }}</p>
                    <p class="m-0">
                      <strong>{{ $subtask->task_no }}. {{ $subtask->name }}</strong>
                    </p>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="box box-info column column-in-progress">
            <div class="box-header">
              <h3 class="box-title mx-1 mt-2">
                <strong>In Progress</strong>
              </h3>
            </div>

            <div class="box-body" data-order="2" data-status_code="I">
              @foreach (($subtasks['I'] ?? []) as $subtask)
                <div id="subtask-{{ $subtask->id }}" class="subtask p-3 mb-3" draggable="true" data-order="2">
                  <a href="/tickets/{{ $subtask->ticketHeader->id }}/edit">
                    <div class="subtask-header">
                      @php $priority = $subtask->ticketHeader->ticketPriority->description @endphp
                      <span class="badge priority-{{ strtolower($priority) }} px-2 py-1">
                        {{ $priority }} Priority
                      </span>

                      @if (isset($subtask->ticketHeader->assignedTo))
                        <img src="{{ $subtask->ticketHeader->assignedTo->image }}" 
                          width="30px"
                          height="30px">
                      @endif
                    </div>

                    <div class="subtask-body py-4">
                      <p class="m-0">
                        <strong>{{ $subtask->task_no }}. {{ $subtask->ticketHeader->subject }}</strong>
                      </p>
                    </div>

                    <div class="subtask-footer">
                      <small>
                        <i class="fa fa-comment"></i> 
                        <span>{{ $subtask->ticketHeader->ticketDetails()->count() }}</span>
                      </small>
                      <small>Due {{ $subtask->ticketHeader->due_date->format('m/d/Y H:iA') }}</small>
                    </div>
                  </a>
                </div>
                @endforeach
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="box box-info column column-pending">
            <div class="box-header">
              <h3 class="box-title mx-1 mt-2">
                <strong>Pending</strong>
              </h3>
            </div>

            <div class="box-body" data-order="3" data-status_code="P">
              @foreach (($subtasks['P'] ?? []) as $subtask)
                <div id="subtask-{{ $subtask->id }}" class="subtask p-3 mb-3" data-order="3" draggable="true">
                  <a href="/tickets/{{ $subtask->ticketHeader->id }}/edit">
                    <div class="subtask-header">
                      @php $priority = $subtask->ticketHeader->ticketPriority->description @endphp
                      <span class="badge priority-{{ strtolower($priority) }} px-2 py-1">
                        {{ $priority }} Priority
                      </span>

                      @if (isset($subtask->ticketHeader->assignedTo))
                        <img src="{{ $subtask->ticketHeader->assignedTo->image }}" 
                          width="30px"
                          height="30px">
                      @endif
                    </div>

                    <div class="subtask-body py-4">
                      <p class="m-0">
                        <strong>{{ $subtask->task_no }}. {{ $subtask->ticketHeader->subject }}</strong>
                      </p>
                    </div>

                    <div class="subtask-footer">
                      <small>
                        <i class="fa fa-comment"></i> 
                        <span>{{ $subtask->ticketHeader->ticketDetails()->count() }}</span>
                      </small>
                      <small>Due {{ $subtask->ticketHeader->due_date->format('m/d/Y H:iA') }}</small>
                    </div>
                  </a>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="box box-info column column-closed">
            <div class="box-header">
              <h3 class="box-title mx-1 mt-2">
                <strong>Closed</strong>
              </h3>
            </div>

            <div class="box-body" data-order="4" data-status_code="S">
              @foreach (($subtasks['S'] ?? []) as $subtask)
                <div id="subtask-{{ $subtask->id }}" class="subtask p-3 mb-3" data-order="4" draggable="true">
                  <a href="/tickets/{{ $subtask->ticketHeader->id }}/edit">
                    <div class="subtask-header">
                      @php $priority = $subtask->ticketHeader->ticketPriority->description @endphp
                      <span class="badge priority-{{ strtolower($priority) }} px-2 py-1">
                        {{ $priority }} Priority
                      </span>

                      @if (isset($subtask->ticketHeader->assignedTo))
                        <img src="{{ $subtask->ticketHeader->assignedTo->image }}" 
                          width="30px"
                          height="30px">
                      @endif
                    </div>

                    <div class="subtask-body py-4">
                      <p class="m-0">
                        <strong>{{ $subtask->task_no }}. {{ $subtask->ticketHeader->subject }}</strong>
                      </p>
                    </div>

                    <div class="subtask-footer">
                      <small>
                        <i class="fa fa-comment"></i> 
                        <span>{{ $subtask->ticketHeader->ticketDetails()->count() }}</span>
                      </small>
                      <small>Due {{ $subtask->ticketHeader->due_date->format('m/d/Y H:iA') }}</small>
                    </div>
                  </a>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  @component('merchants.components.recentActivities', [
    'recentActivities' => $recentActivities
  ])
  @endcomponent

  <div class="modal fade" id="modal-add-subtask" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Add Subtask</h4>
        </div>

        <div class="modal-body">
          <form method="POST" action="{{ route('merchants.subtasks.add', $task->id) }}">
            @csrf

            <div class="row">
              <div class="col-md-4 form-group pr-0">
                <select name="priority" class="form-control form-control-sm">
                  <option selected disabled>Select Priority</option>
                  @foreach ($workflowDependencies->priorities as $priority)
                    <option value="{{ $priority->code }}">{{ $priority->description }}</option>    
                  @endforeach 
                </select>
              </div>
              
              <div class="col-md-8 form-group pl-0">
                <input class="form-control form-control-sm no-border-left" 
                  type="text" 
                  name="subtask_name"
                  placeholder="Subtask Name">
              </div>

              <div class="col-md-4 form-group mb-0">
                <label for="department" class="mb-0">Assignee</label>
                
                <div class="visible-panel">
                  <div class="sliding-panel">
                    <div class="left-panel">
                      <select class="form-control form-control-sm" name="department">
                        @foreach($workflowDependencies->departmentsNonGrouped as $department)
                          <option value="{{ $department->id }}">{{ $department->description }}</option>
                        @endforeach
                      </select>
                    </div>
        
                    <div class="right-panel">
                      <select class="form-control form-control-sm" name="assignee">
                        <option value="DEPARTMENT" selected></option>
                      </select>
                    </div>
                  </div>
                </div>
        
                <a href="#" class="btn btn-flat btn-default btn-xs pull-right back hide">
                  <i class="fa fa-chevron-left"></i> Back
                </a>
              </div>
              
              <div class="col-md-4 form-group mb-0">
                <label for="task_name" class="mb-0">Days to Complete</label>
                <input class="form-control form-control-sm integer-only" 
                  type="text" 
                  name="days_to_complete"
                  value="1">
              </div>
        
              <div class="col-md-4 form-check align-self-center d-flex justify-content-center mb-0">
                <input type="checkbox" class="form-check-input" name="has_prerequisite">
                <label class="form-check-label pl-2">
                  <small>has prereq</small>
                </label>
              </div>
        
              <div class="col-md-4 form-group has-prerequisite mt-3 mb-0 hidden">
                <label for="prereq_subtask_number" class="mb-0">Subtask Number</label>
                <select name="prereq_subtask_number" class="form-control form-control-sm">
                  @foreach ($task->subtasks as $i => $subtask)
                    <option value="{{ $i + 1 }}">{{ $i + 1 }}</option>
                  @endforeach
                </select>
              </div>
        
              <div class="col-md-8 form-group has-prerequisite mt-3 mb-0 hidden">
                <label for="start_this_subtask_on" class="mb-0">Start this subtask on</label>
                <select name="start_this_subtask_on" class="form-control form-control-sm">
                  <option value="Start">When Subtask Number 1 has started. (Corequisite)</i></option>
                  <option value="Completion">When Subtask Number 1 has been completed. (Prerequisite)</option>
                  <option value="Due date">When Subtask Number 1's due date has ended. (Strict Prerequisite)</option>
                </select>
              </div>

              <div class="col-md-12 mt-4">
                <button class="btn btn-sm btn-primary">Add</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script>
    $(document).ready(function() {

      $('.btn-toggle-subheader').on('click', function() {
        let el = $(this)
        if (el.hasClass('fa-chevron-circle-down')) {
          el.removeClass('fa-chevron-circle-down')
          el.addClass('fa-chevron-circle-up')
          $('.subheader').slideDown('fast')
        } else {
          el.removeClass('fa-chevron-circle-up')
          el.addClass('fa-chevron-circle-down')
          $('.subheader').slideUp('fast')
        }
      })

      $('.btn-show-recent-activities').on('click', function(e) {
        e.preventDefault()
        $('.recent-activities').animate({width: 500}, 300)
      })

      $('.btn-hide-recent-activities').on('click', function() {
        $('.recent-activities').animate({width: 0}, 300)
      })

      $('.subtask').on('dragstart', function(e){
        e.originalEvent.dataTransfer.setData("id", $(this).attr('id')); 
        e.originalEvent.dataTransfer.setData("order", $(this).data('order')); 
      });

      $('.box-body').on('dragover', function(e) {
        e.preventDefault()
      })

      $('.box-body').on('dragenter', function(e) {
        e.preventDefault()
      })

      $('.box-body').on('drop', function(e) {
        const subtaskId = e.originalEvent.dataTransfer.getData('id')
        const subtaskOrder = parseInt(e.originalEvent.dataTransfer.getData('order'))
        const boxBodyOrder = parseInt($(this).data('order'))

        if (boxBodyOrder >= subtaskOrder || subtaskOrder == 3) {
          const statusCode = $(this).data('status_code')

          $(this).append($(`#${subtaskId}`))
          $(`#${subtaskId}`).data('order', boxBodyOrder)

          let request = $.ajax({
            url: "/merchants/subtasks/" + subtaskId.split('-')[1],
            method: "POST",
            data: {
              status_code: statusCode
            }
          })

          request.done(function(data) {
            if (data.generated_tickets && data.generated_tickets > 0) {
              location.reload(true)
            }
          })
        }
      })

      $('.form-check-input').on('change', function() {
        if (this.checked) {
          $('.has-prerequisite').slideDown('fast');

          $('input[name="has_prereq"]', this).val('1')
          $('input[name="has_prereq"]', this).val('Start')
        } else {
          $('.has-prerequisite').slideUp('fast');
        }
      })

      $('select[name="prereq_subtask_number"]').on('change', function() {
        let selectedSubtaskNumber = $(this).val()
        let options = $('select[name="start_this_subtask_on"] option')

        options.each(function() {
          switch ($(this).val()) {
            case 'Start':
              $(this).text(`When Subtask Number ${selectedSubtaskNumber} has started. (Corequisite)`)
              break

            case 'Completion':
              $(this).text(`When Subtask Number ${selectedSubtaskNumber} has been completed. (Prerequisite)`)
              break

            case 'Due Date':
              $(this).text(`When Subtask Number ${selectedSubtaskNumber}'s due date has ended. (Strict Prerequisite)`)
              break
          }
        })

        updateOverview()
      })
    })
  </script>
@endsection