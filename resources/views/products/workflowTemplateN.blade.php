@extends('layouts.app')

@section('style')
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .flex-grow-1 {
      flex-grow: 1
    }

    .no-border-left {
      border-left: 0px;
    }

    .overview {
      border-left: 1px solid #d2d6de;
    }

    .select2 {
      display: block;
      width: 100% !important;
    }

    .subtask {
      background: white;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.15)
    }

    .select2-container .select2-selection--multiple {
      border-radius: 0px;
      border-color: #d2d6de;
      min-height: 30px;
    }

    .select2-container .select2-selection__rendered {
      padding-left: 3px !important;
    }

    .select2-selection__choice {
      color: black !important;
      font-size: 0.8rem;
      margin-top: 3.75px !important;
    }

    .overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;

      display: none;
    }

    .overlay > div {
      margin: 0;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    {{-- Main Header --}}
    <section class="content-header">
      <h1>Product Workflow Template</h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.banners.create') }}">Products</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.banners.create') }}">Product Workflow Template</a></li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid animated">
      <div class="row px-4">
        {{-- Main Content --}}
        <div class="col-md-9 pr-4">
          
          {{-- Search Bar --}}
          <div class="row mb-4 pt-0 container-search-product hidden">
            <div class="col-md-12 form-group mb-4">
              <select name="search" class="form-control select2-custom">
                <option></option>
                @foreach ($productsGroup as $products)
                  <optgroup label="{{ $products->first()->partnerCompany->company_name ?? 'No Company'}}">
                    @foreach ($products as $product)
                      <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
            </div>
          </div>

          <div id="none-selected" class="my-4 py-4 text-center">
            <h4 class="mt-4 mb-2">No Product Selected</h4>
            <h6 class="mb-4">Please select a product to create/edit a workflow template</h6>
          </div>

          <div id="product-workflow-template">
            <form action="{{ route('products.templates.workflow.save') }}" method="POST">
              @csrf

              <input type="hidden" name="product_id">

              {{-- Product Details --}}
              <div class="row">
                <div class="col-md-12 d-flex justify-content-between">
                  <div class="d-flex align-items-center mb-2">
                    <img src="https://i0.wp.com/www.winhelponline.com/blog/wp-content/uploads/2017/12/user.png?fit=256%2C256&quality=100&ssl=1"
                      id="product-image"
                      width="45px"
                      height="45px"
                      class="mr-2">

                    <div class="d-flex flex-column">
                      <h4 class="mb-0"><strong id="product-name"></strong></h4>
                      <span id="product-code"></span>
                    </div>
                  </div>

                  <a href="#" class="toggle-search-product">
                    Select another product <i class="ml-1 fa fa-chevron-up toggle-search-product-chevron hidden"></i>
                  </a>
                </div>
              </div>

              {{-- Task Form --}}
              <div class="row">
                <div class="col-md-12 form-group">
                  <label for="task_name">Task Name</label>
                  <input class="form-control form-control-sm" 
                    type="text" 
                  type="text" 
                    type="text" 
                    name="task_name">
                </div>
                
                <div class="col-md-12 form-group">
                  <label for="task_description">Task Description</label>
                  <textarea class="form-control form-control-sm" 
                    name="task_description"
                    rows="4"></textarea>
                </div>
              </div>

              {{-- Subtasks Form --}}
              <div class="row">
                <div class="col-md-12">
                  <p class="text-xs"><strong>Subtasks</strong></p>
                </div>

                <div class="col-md-12 container-subtasks">
                </div>
              </div>

              <div class="row pb-0">
                <div class="col-md-12 d-flex justify-content-end">
                  <button class="btn btn-primary btn-sm btn-add-subtask clickable mr-2">
                    <i class="fa fa-plus mr-1"></i> 
                    <span>Add Subtask</span>
                  </button>
                  <button class="btn btn-success btn-sm btn-submit clickable" type="submit">Save</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        {{-- Overview --}}
        <div class="col-md-3 pl-4 overview">
          <h4 class="mb-1"><strong class="overview-subtasks-count">0 Subtask</strong></h4>
          <h4 class="mb-4">
            <strong class="overview-days-to-complete">0 Day to Complete</strong>
          </h4>

          <h5 class="mb-2"><strong>Departments Involved</strong></h5>
          <div class="d-flex flex-column mb-4 overview-departments-involved">
            <span>N/A</span>
          </div>

          <h5 class="mb-2"><strong>Users Involved</strong></h5>
          <div class="d-flex flex-column overview-users-involved">
            <span>N/A</span>
          </div>
        </div>
      </div>
    </section>
  </div>

  <div id="subtask-template" class="subtask-template mt-2 mb-4 hidden">
    <div class="d-flex flex-column align-items-center px-2">
      <h1><strong>1</strong></h1>
      <i class="text-red clickable btn-delete-subtask fa fa-times px-1"
        data-toggle="tooltip" 
        data-placement="left" 
        title="Delete subtask"></i>
    </div>
    
    <div class="row flex-grow-1 px-2">
      <div class="col-md-4 form-group pr-0">
        <select name="priority[]" class="form-control form-control-sm">
          <option selected disabled>Select Priority</option>
        </select>
      </div>
      
      <div class="col-md-8 form-group pl-0">
        <input class="form-control form-control-sm no-border-left" 
          type="text" 
          name="subtask_name[]"
          placeholder="Subtask Name">
      </div>

      <div class="col-md-12 form-group">
        <label for="subproducts" class="mb-0">Subproducts</label>
        <select name="subproducts[]" class="form-control form-control-sm subproducts-input-el" multiple>
        </select>
      </div>

      <div class="col-md-4 form-group mb-0">
        <label for="department" class="mb-0">Assignee</label>
        
        <div class="visible-panel">
          <div class="sliding-panel">
            <div class="left-panel">
              <select class="form-control form-control-sm" name="department[]"></select>
            </div>

            <div class="right-panel">
              <select class="form-control form-control-sm" name="assignee[]">
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
          name="days_to_complete[]"
          value="1">
      </div>

      <div class="col-md-4 form-check align-self-center d-flex justify-content-center mb-0">
        <input type="checkbox" class="form-check-input" name="has_prerequisite[]">
        <label class="form-check-label pl-2">
          <small>has prerequisite / corequisite</small>
        </label>
      </div>

      <div class="col-md-4 form-group has-prerequisite mt-3 mb-0 hidden">
        <label for="prereq_subtask_number[]" class="mb-0">Subtask Number</label>
        <select name="prereq_subtask_number[]" class="form-control form-control-sm"></select>
      </div>

      <div class="col-md-8 form-group has-prerequisite mt-3 mb-0 hidden">
        <label for="start_this_subtask_on[]" class="mb-0">Start this subtask on</label>
        <select name="start_this_subtask_on[]" class="form-control form-control-sm">
          <option value="Start">When Subtask Number 1 has started. (Corequisite)</i></option>
          <option value="Completion">When Subtask Number 1 has been completed. (Prerequisite)</option>
          <option value="Due date">When Subtask Number 1's due date has ended. (Strict Prerequisite)</option>
        </select>
      </div>
    </div>
  </div>

  <div class="overlay" style="margin-left: 100px">
    <div id="text">
      <img width="75px"   
        height="75px"
        src="https://ubisafe.org/images/transparent-gif-loading-5.gif"/>
    </div>
  </div>
@endsection

@section('script')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <script src=@cdn('/js/products/templates/workflow/overview.js')></script>
  <script src=@cdn('/js/products/templates/workflow/subtask.js')></script>
  <script src=@cdn('/js/products/templates/workflow/validator.js')></script>
  <script>
    let productId = @json($productId);
    let departmentsGlobal = null

    function getProductWorkflowTemplate(productId) {
      let request = $.ajax({
        url: `/products/templates/workflow/${productId}`,
        method: "GET",
      })

      $('.container-fluid').addClass('bounceOutLeft')
      $('.container-fluid').addClass('bounceInRight')
    
      request.done(function(data) {
        $('.container-subtasks').empty()

        departmentsGlobal = data.product.user_types

        let departments = data.workflow_dependencies.departments
        let priorities = data.workflow_dependencies.priorities
        let subproducts = data.product.sub_products

        $('#product-image').attr({src: data.product.display_picture})
        $('#product-name').text(data.product.name)
        $('#product-code').text(data.product.code)

        if (data.product.task_template == null) {
          $('input[name="task_name"]').val('')
          $('textarea[name="task_description"]').val('')

          updateSubtaskTemplateEl(priorities, subproducts, departments)
          addSubtaskEl()
        } else {
          let task = data.product.task_template
          let subtasks = data.product.task_template.sub_task_templates

          $('input[name="task_name"]').val(task.name)
          $('textarea[name="task_description"]').val(task.description)

          updateSubtaskTemplateEl(priorities, subproducts, departments)
          subtasks.forEach(subtask => {
            addSubtaskEl(subtask)          
          })
        }

        updateSubtasksNumber()
        updateOverview()

        if (!$('.toggle-search-product-chevron').first().hasClass('hidden'))
          $('.toggle-search-product').click()

        $('#none-selected').addClass('hidden')
        $('#product-workflow-template').removeClass('hidden')
        $('.overlay').hide()
        $('.container-fluid').removeClass('bounceOutLeft')
      })

      request.fail(function(xhr) {
        console.log(xhr.responseText)
      })
    }
    
    $(document).ready(function() {

      $('.select2').select2();
      $('.select2-custom').select2({
        allowClear: true,
        placeholder: '<i class="fa fa-search mr-2"></i> Select a Product',
        escapeMarkup: function(markup) {
          return markup
        }
      })

      $(document).on('keyup', 'input[name="days_to_complete[]"]', function() {
        updateOverview()
      })

      $(document).on('change', 'select[name="start_this_subtask_on[]"]', function() {
        updateOverview()
      })

      $(document).on('change', '.form-check-input', function() {
        let el = $(this)
        let prerequisiteFormInputs = el.parent().siblings('.has-prerequisite')

        if (this.checked) {
          prerequisiteFormInputs.slideDown('fast');

          $('input[name="has_prereq[]"]', this).val('1')
          $('input[name="has_prereq[]"]', this).val('Start')
        } else {
          prerequisiteFormInputs.slideUp('fast');
        }

        updateOverview()
      })

      $(document).on('change', 'select[name="prereq_subtask_number[]"]', function() {
        let selectedSubtaskNumber = $(this).val()
        let options = $(this).parent()
          .parent()
          .find('select[name="start_this_subtask_on[]"] option')

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

      $(document).on('click', '.btn-delete-subtask', function() {
        deleteSubtaskEl($(this).parents('.subtask'))
      })

      $(document).on('change', 'select[name="department[]"]', function() {
        let parent = $(this).parent().parent().parent().parent()

        parent.find('.sliding-panel').addClass('sliding-panel-right');
        parent.find('.back').removeClass('hide');

        let assigneeInputEl = parent.find('select[name="assignee[]"]')
        let department = departmentsGlobal.find((department) => department.id == $(this).val());

        assigneeInputEl.find('option').remove()

        if ($(this).val() !== null) {
          department.users.forEach(user => {
            assigneeInputEl.append(
              `<option value="${user.id}">` + 
                `${user.first_name} ${user.last_name}` +
              `</option>`)
          })
        

          if ($(this).data('assignee') != null) {
            assigneeInputEl.val($(this).data('assignee'))

            if (department.head_id != -1) {
              assigneeInputEl.append(`<option value="DEPARTMENT">${department.description}</option>`)
            }
          } else {
            if (department.head_id != -1) {
              assigneeInputEl.append(`<option value="DEPARTMENT" selected>${department.description}</option>`)
            }
          }
        }

        updateOverview()
      });

      $(document).on('change', 'select[name="assignee[]"]', function() {
        updateOverview()
      });
  
      $(document).on('click', '.back', function(e) {
        e.preventDefault()

        $(this).addClass('hide')

        let parent = $(this).parent()

        parent.find('.sliding-panel').removeClass('sliding-panel-right')
        parent.find('select[name="assignee[]"]').val('DEPARTMENT')
      })

      $('.toggle-search-product').on('click', function(e) {
        e.preventDefault()

        if ($('.container-search-product').is(':hidden')) {
          $('.container-search-product').slideDown('fast')
          $(this).find('i').removeClass('hidden')
        } else {
          $('.container-search-product').slideUp('fast')
          $(this).find('i').addClass('hidden')
        }
      })

      $('.btn-add-subtask').on('click', function(e) {
        e.preventDefault()
        
        addSubtaskEl()
        updateOverview()
      })

      $('select[name="search"]').on('change', function() {
        if ($(this).val() != '') {
          if ($('#product-workflow-template').hasClass('hidden')) {
            $('input[name="product_id"]').val($(this).val())
            getProductWorkflowTemplate($(this).val())

            return true
          }

          if (confirm('Are you sure you want to change product? You may lose unsaved changes!')) {
            $('input[name="product_id"]').val($(this).val())
            getProductWorkflowTemplate($(this).val())
          } else {
            $('select[name="search"]').val('').trigger('change')
          }
        }
      })

      $('.container-fluid').on('animationend', function() {
        if ($(this).hasClass('bounceOutLeft')) {
          $('.overlay').show()
        }
      })

      $('.btn-submit').on('click', function(e) {
        if (!validateWorkflowTemplateForm())
          e.preventDefault()
      })
 
      if (productId === null) {
        $('.toggle-search-product').click()
        $('#product-workflow-template').addClass('hidden')
      } else {
        $('input[name="product_id"]').val(productId)
        getProductWorkflowTemplate(productId)
      }
    })
  </script>
@endsection