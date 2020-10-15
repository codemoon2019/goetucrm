@extends('layouts.app')

@section('style')
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href=@cdn('/css/workflow/kanban.css') />
  <style>
    #container-product-with-config {
      border-left: 1px solid #d2d6de;
    }

    .product-with-config {
      display: flex;
      align-items: center;
    }

    #product-image,
    .product-with-config img {
      border: 1px solid black;
      border-radius: 50%;
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
    {{------- Header -------}}
    <section class="content-header">
      <h1>
        <i class="fa fa-ticket mr-1"></i>
        <span>Ticket Configuration</span>
      </h1>

      <div class="dotted-hr"></div>
    </section>

    {{------- Content -------}}
    <section class="content container-fluid animated">
      <div class="row px-4">
        <div class="col-md-9 pr-4">
          {{------- Search Bar -------}}
          <div id="container-search-product" class="row mb-4 pt-0">
            <div class="col-md-12 form-group mb-4">
              <select id="input-search-product" class="form-control">
                <option></option>

                @foreach ($products as $product)
                  <option value="{{ $product->id }}">
                    {{ $product->name }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          {{------- Content (No Product Selected) -------}}
          <div id="content-no-product-selected" class="my-4 py-4 text-center">
            <h4 class="mt-4 mb-2">No Product Selected</h4>
            <h6 class="mb-4">
              Please select a product to create/edit it's ticket configuration
            </h6>
          </div>

          {{------- Content (Product Selected) -------}}
          <div id="content-product-selected" class="hidden">

            {{------- Selected Product Details-------}}
            <div class="row">
              <div class="col-md-12 d-flex justify-content-between">
                <div class="d-flex align-items-center mb-2">
                  <img src=""
                    width="45px"
                    height="45px"
                    id="product-image"
                    class="mr-2">

                  <div class="d-flex flex-column">
                    <h4 class="mb-0"><strong id="product-name"></strong></h4>
                    <span id="product-code"></span>
                  </div>
                </div>

                <a href="#" id="toggle-search-product">
                  <span>Select another product</span>
                  <i id="toggle-search-product-chevron" class="ml-1 fa fa-chevron-up hidden"></i>
                </a>
              </div>
            </div>

            {{------- Ticket Config -------}}
            <div id="container-ticket-config" class="mt-4">
              <div id="container-header" class="d-flex justify-content-between">
                <h5 class="text-muted">ISSUE TYPES</h5>
                <div class="btn-group">
                  <button type="button" 
                    class="btn btn-sm btn-pfrimary dropdown-toggle" 
                    data-toggle="dropdown" 
                    aria-haspopup="true" 
                    aria-expanded="false">
                    Mode  
                  </button>
                  <div class="dropdown-menu">
                    <a id="btn-mode-read-only" class="dropdown-item" href="#">Read Only Mode</a>
                    <a id="btn-mode-edit" class="dropdown-item" href="#">Edit Mode</a>
                  </div>
                </div>
              </div>

              <form id="form-add-ticket-issue-type" class="mt-4">
                <div class="form-row">
                  <div class="col-10">
                    <input type="text" 
                      name="description"
                      class="form-control maxlength-20" 
                      placeholder="Type issue type here...">
                  </div>

                  <div class="col-2 pl-2">
                    <button type="submit" class="btn btn-primary">
                      <i class="fa fa-plus mr-1"></i>
                      <span>Add</span>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{------- Products with Config List -------}}
        <div id="container-product-with-config" class="col-md-3 pl-4">
          <div class="row pt-0">
            <div class="col-md-12">
              <p class="mb-2 text-large">
                <strong>Products w/ custom Config</strong>
              </p>

              @foreach ($productsWithConfig as $product)
                <div class="product-with-config mb-2" 
                  data-id="{{ $product->id }}">

                  <img src="{{ $product->display_picture_url }}"
                    width="45px"
                    height="45px"
                    class="mr-2">
    
                  <div class="d-flex flex-column">
                    <h4 class="mb-0">
                      <strong id="product-name">
                        {{ $product->name }}
                      </strong>
                    </h4>

                    <span id="product-code">Product {{ $product->code }}</span>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <div id="template-ticket-issue-type" class="hidden mb-3">
    <div class="d-flex align-items-center">
      <span class="description text-large mr-3"></span>
      <i class="fa fa-times text-danger clickable btn-delete btn-delete-ticket-issue-type"></i>
    </div>
    
    <div class="ticket-reasons ml-4 my-2">
      <h6 class="text-muted">REASONS</h6>
      <form class="form-add-ticket-reason mt-2">
        <input type="hidden" name="ticket_issue_type">

        <div class="form-row">
          <div class="col-4">
            <input type="text" 
              name="description"
              class="form-control form-control-sm maxlength-20" 
              placeholder="Type reason here...">
          </div>

          <div class="col-3">
            <select name="department" class="form-control form-control-sm">
            </select>
          </div>

          <div class="col-3">
            <select name="ticket_priority" class="form-control form-control-sm">
              <option value="" selected disabled>Select Ticket Priority</option>

              @foreach ($tcDependencies->ticketPriorities as $ticketPriority)
                <option value="{{ $ticketPriority->code }}">
                  {{ $ticketPriority->description }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-2">
            <button type="submit" class="btn btn-sm btn-primary">
              <i class="fa fa-plus mr-1"></i>
              <span>Add</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div id="template-ticket-reason" class="hidden align-items-center">
    <span class="description text-large mr-3"></span>
    <span class="badge badge-dark mr-3"></span>
    <span class="badge badge-tp mr-3"></span>
    <i class="fa fa-times text-danger clickable btn-delete btn-delete-ticket-reason"
      data-toggle="tooltip" 
      data-placement="left" 
      title="Delete Ticket Reason">
    </i>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script>
    let companyId = @json($companyId);
    let productId = -1

    function changeProduct(pid) {
      productId = pid

      return new Promise((resolve, reject) => {
        axios.get(`/admin/company_settings/ticket-config/products/${productId}`)
          .then(data => {
            removeConfigurationEl()

            data = data.data
            document.getElementById('product-image').src = data.display_picture
            document.getElementById('product-name').innerHTML = data.name
            document.getElementById('product-code').innerHTML = data.code

            $('#template-ticket-issue-type select[name="department"]').empty()
            $('#template-ticket-issue-type select[name="department"]').append(
                `<option value="" selected disabled>` + 
                  `Select Department` +
                `</option>`)

            for (let key in data.departmentsGroups) {
              if (data.departmentsGroups.hasOwnProperty(key)) {
                const optgroup = $(
                  `<optgroup label="${data.departmentsGroups[key][0].partner_company.company_name}">` +
                  `</optgroup>`)

                data.departmentsGroups[key].forEach(department => {
                  if (department.head_id !== -1) {
                    optgroup.append(
                      `<option value="${department.id}">` + 
                          `${department.description}` +
                      `</option>`)
                  }
                })

                if (optgroup.find('option').length > 0)
                  $('#template-ticket-issue-type select[name="department"]').append(optgroup)
              }
            }

            data.ticket_issue_types.forEach(ticketIssueType => {
              ticketIssueTypeEl = addTicketIssueTypeEl(ticketIssueType, formAddTitEl);

              let className = 'form-add-ticket-reason'
              let formEl = ticketIssueTypeEl.getElementsByClassName(className)[0]

              ticketIssueType.ticket_reasons.forEach(ticketReason => {
                addTicketReasonEl(ticketReason, formEl)
              })
            })

            resolve()
          })
          .catch(error => reject())
      })
    }

    function addTicketIssueTypeEl(ticketIssueType, insertBeforeEl) {
      let templateId = 'template-ticket-issue-type'
      let templateTicketIssueTypeEl = (document).getElementById(templateId)

      let ticketIssueTypeEl = templateTicketIssueTypeEl.cloneNode(true)
      ticketIssueTypeEl.id = `ticket-issue-type-${ticketIssueType.id}`
      ticketIssueTypeEl.classList.remove('hidden')
      ticketIssueTypeEl.classList.add('ticket-issue-type');

      (document)
        .getElementById('container-ticket-config')
        .insertBefore(ticketIssueTypeEl, insertBeforeEl);

      (ticketIssueTypeEl)
        .getElementsByClassName('description')[0]
        .innerHTML = ticketIssueType.description;

      (ticketIssueTypeEl)
        .getElementsByClassName('form-add-ticket-reason')[0]
        .addEventListener('submit', submitTicketReasonHandler);

      (ticketIssueTypeEl)
        .getElementsByClassName('form-add-ticket-reason')[0]
        .elements['ticket_issue_type']
        .value = ticketIssueType.id

      let className = 'btn-delete-ticket-issue-type'
      let btnDelete = ticketIssueTypeEl.getElementsByClassName(className)[0]

      if (ticketIssueType.product_id == null) {
        btnDelete.parentNode.removeChild(btnDelete)
      } else {
        btnDelete.dataset.id = ticketIssueType.id
        btnDelete.addEventListener('click', deleteTicketIssueTypeHandler)
      }

      return ticketIssueTypeEl
    }

    function addTicketReasonEl(ticketReason, insertBeforeEl) {
      let templateId = 'template-ticket-reason'
      let templateTicketReasonEl = (document).getElementById(templateId)

      let ticketReasonEl = templateTicketReasonEl.cloneNode(true)
      ticketReasonEl.id = ticketReason.id
      ticketReasonEl.classList.remove('hidden')
      ticketReasonEl.classList.add('ticket-reason');
      ticketReasonEl.classList.add('d-flex');

      let ticketIssueTypeId = insertBeforeEl.elements['ticket_issue_type'].value;

      (document)
        .getElementById(`ticket-issue-type-${ticketIssueTypeId}`)
        .getElementsByClassName('ticket-reasons')[0] 
        .insertBefore(ticketReasonEl, insertBeforeEl);

      (ticketReasonEl)
        .getElementsByClassName('description')[0]
        .innerHTML = ticketReason.description

      let badgeText = 'Default'
      let badgeClassName = 'default'
      switch (ticketReason.ticket_priority_code) {
        case 'L':
          badgeText = 'Low Priority'
          badgeClassName = 'priority-low'
          break

        case 'M':
          badgeText = 'Medium Priority'
          badgeClassName = 'priority-medium'
          break

        case 'H':
          badgeText = 'High Priority'
          badgeClassName = 'priority-high'
          break

        case 'U':
          badgeText = 'Urgent Priority'
          badgeClassName = 'priority-urgent'
          break
      };

      let badgePriorityEl = ticketReasonEl.getElementsByClassName('badge-tp')[0]
      badgePriorityEl.classList.add(badgeClassName);
      badgePriorityEl.innerHTML = badgeText

      let badgeDepartmentEl = ticketReasonEl.getElementsByClassName('badge-dark')[0]
      badgeDepartmentEl.innerHTML = ticketReason.department.description

      let className = 'btn-delete-ticket-reason'
      let btnDelete = ticketReasonEl.getElementsByClassName(className)[0]

      if (ticketReason.product_id == null) {
        btnDelete.parentNode.removeChild(btnDelete)
      } else {
        btnDelete.dataset.id = ticketReason.id
        btnDelete.addEventListener('click', deleteTicketReasonHandler)
      }
    }

    function deleteTicketReasonEl(ticketReasonEl) {
      ticketReasonEl.parentNode.removeChild(ticketReasonEl)
    }

    function deleteTicketIssueTypeEl(ticketReasonEl) {
      ticketReasonEl.parentNode.removeChild(ticketReasonEl)
    }
   
    function deleteTicketIssueTypeHandler(e) {
      e.preventDefault()

      if (confirm("Are you sure you want to delete this Ticket Issue Type?")) {
        let apiUrl = `/admin/company_settings/ticket-config/products/${productId}` + 
                   `/ticket-issue-types/${this.dataset.id}`

        axios.post(apiUrl, { _method: 'DELETE' })
          .then(data => { deleteTicketIssueTypeEl(this.parentNode.parentNode) })
          .catch(error => { console.log(error) })
      }
    }

    function deleteTicketReasonHandler(e) {
      e.preventDefault()

      if (confirm("Are you sure you want to delete this Ticket Reason?")) {
        let apiUrl = `/admin/company_settings/ticket-config/products/${productId}` + 
                   `/ticket-reasons/${this.dataset.id}`

        axios.post(apiUrl, { _method: 'DELETE' })
          .then(data => { deleteTicketReasonEl(this.parentNode) })
          .catch(error => { console.log(error) })
      }
    }
  
    function removeConfigurationEl() {
      let ticketIssueTypesEl = document.getElementsByClassName('ticket-issue-type')
      let j = ticketIssueTypesEl.length
      for (let i = 0; i < j; i++) {
        ticketIssueTypesEl[0].parentNode.removeChild(ticketIssueTypesEl[0])
      }
    }

    function submitTicketReasonHandler(e) {
      e.preventDefault()
      
      if (validateForm(this)) {
        let formEl = this
        let apiUrl = `/admin/company_settings/ticket-config/companies/${companyId}` + 
                    `/products/${productId}/ticket-reasons`

        axios.post(apiUrl, new FormData(formEl))
          .then(data => {
            addTicketReasonEl(data.data, formEl)
            formEl.reset()
          })
          .catch(error => { console.log(error) })
      }
    }

    function validateForm(formEl) {
      let hasError = false
      for (let i = 0; i < formEl.elements.length; i++) {
        if (formEl.elements[i].classList.contains('btn'))
          continue

        if (formEl.elements[i].value.trim() == '') {
          hasError = true
          formEl.elements[i].style.borderColor = 'red'

          setTimeout(function() {
            formEl.elements[i].style.borderColor = '#d2d6de'
          }, 4000);
        } else {
          formEl.elements[i].style.borderColor = '#d2d6de'
        }
      }

      return !hasError
    }

    /******* Store Ticket Issue Type *******/
    let formAddTitEl = document.getElementById('form-add-ticket-issue-type')
    formAddTitEl.addEventListener('submit', function(e) {
      e.preventDefault()

      if (validateForm(this)) {
        let formEl = this
        let apiUrl = `/admin/company_settings/ticket-config/companies/${companyId}` +
                    `/products/${productId}/ticket-issue-types`

        axios.post(apiUrl, new FormData(formEl))
          .then(data => {
            addTicketIssueTypeEl(data.data, formEl)
            formEl.reset()
          })
          .catch(error => { console.log(error) })
      }
    })

    /******* Store Ticket Reason *******/
    let formAddTrEl = document.getElementsByClassName('form-add-ticket-reason')
    for (let i = 0; i < formAddTrEl.length; i++) {
      formAddTrEl[i].addEventListener('submit', submitTicketReasonHandler)
    }

    /******* Toggle Search Product *******/
    let toggleSearchProductEl = document.getElementById('toggle-search-product')
    toggleSearchProductEl.addEventListener('click', function(e) {
      e.preventDefault()

      let containerSearchProductEl = $('#container-search-product')
      if (containerSearchProductEl.is(':hidden')) {
        containerSearchProductEl.slideDown('fast');
        
        (document)
          .getElementById('toggle-search-product-chevron')
          .classList
          .remove('hidden')
      } else {
        containerSearchProductEl.slideUp('fast');

        (document)
          .getElementById('toggle-search-product-chevron')
          .classList
          .add('hidden')
      }
    })

    /******* Mode change *******/
    let btnModeReadOnlyEl = document.getElementById('btn-mode-read-only')
    btnModeReadOnlyEl.addEventListener('click', function(e) {
      e.preventDefault()

      let formsEl = document.getElementsByTagName('form')
      for (let i = 0; i < formsEl.length; i ++) {
        formsEl[i].classList.add('hidden')
      }

      let btnDeletesEl = document.getElementsByClassName('btn-delete')
      for (let i = 0; i < formsEl.length; i ++) {
        btnDeletesEl[i].classList.add('hidden')
      }
    })

    let btnModeEdit = document.getElementById('btn-mode-edit')
    btnModeEdit.addEventListener('click', function(e) {
      e.preventDefault()

      let formsEl = document.getElementsByTagName('form')
      for (let i = 0; i < formsEl.length; i ++) {
        formsEl[i].classList.remove('hidden')
      }

      let btnDeletesEl = document.getElementsByClassName('btn-delete')
      for (let i = 0; i < formsEl.length; i ++) {
        btnDeletesEl[i].classList.remove('hidden')
      }
    })

    /******* Change Product *******/
    let mainContainer = document.getElementsByClassName('container-fluid')[0];
    mainContainer.addEventListener('animationend', function() {
      if (this.classList.contains('bounceOutLeft')) {
        document.getElementsByClassName('overlay')[0].style.display = 'block';
      }
    })

    $('#input-search-product').select2({
      allowClear: true,
      placeholder: '<i class="fa fa-search mr-2"></i> Select a Product',
      escapeMarkup: function(markup) {
        return markup
      }
    })

    $('#input-search-product').on('change', function() {
      const msg = `Are you sure you want to change product? ` + 
                  `You may lose unsaved changes!`

      const contentProductSelectedIsHidden =  document
        .getElementById('content-product-selected')
        .classList
        .contains('hidden')

      if (this.value != '' && (contentProductSelectedIsHidden || confirm(msg))) {
        document.getElementsByClassName('container-fluid')[0].classList.add('bounceOutLeft')
        document.getElementsByClassName('container-fluid')[0].classList.add('bounceInRight')

        changeProduct(this.value)
          .then(() => {
            toggleSearchProductEl.click()

            document.getElementById('content-no-product-selected').classList.add('hidden')
            document.getElementById('content-product-selected').classList.remove('hidden')

            document.getElementsByClassName('overlay')[0].style.display = 'none';
            document.getElementsByClassName('container-fluid')[0]
              .classList
              .remove('bounceOutLeft')
          })
          .catch(error => console.log(error))
      } else {
        this.value = ''
      }
    })

    /******* Input max length of 20 *******/
    document.addEventListener('keypress', function(e) {
      if (e.target.classList.contains('maxlength-20')) {
        if (e.which < 0x20)
          return

        if (e.target.value.length == 20) {
          e.preventDefault();
        } else if (e.target.value.length > 20) {
          e.target.value = e.target.value.substring(0, 20);
        }
      }
    })
  </script>
@endsection
