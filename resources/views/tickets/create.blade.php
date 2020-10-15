@extends('layouts.app')

@section('style')
    <link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" />
    <style>
        *[readonly],
        *[disabled] {
            cursor: not-allowed;
        }
        
        .select2-container {
            display: block;
            width: 100% !important;
        }

        .form-error {
            color: red;
            font-size: 0.8em;
            padding-top: 3px;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        {{------- Header -------}}
        <section class="content-header">
            <h1>
                <i class="fa fa-ticket mr-1"></i>
                <span>Create Ticket</span>
            </h1>

            <div class="dotted-hr"></div>
        </section>

        {{------- Content -------}}
        <section class="content container-fluid">
            <form id="create-ticket-form" autocomplete="off" enctype="multipart/form-data">
                @csrf

                <input id="company-id-input" type="hidden" name="company_id">

                <div class="row px-4">
                    <div class="col-lg-8 offset-lg-2">

                        {{------- Product Selection -------}}
                        <div class="form-group">
                            <label>Product</label>
                            
                            <select id="product-select"
                                class="form-control select2"
                                name="product"
                                aria-describedby="product_help_block"
                                data-placeholder="Select Product" 
                                data-allow-clear="true">

                                <option></option>

                                @foreach ($productsGroups as $products)
                                    <optgroup label="{{ $products->first()->company_name }}">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach 
                                    </optgroup>
                                @endforeach
                            </select>

                            <small id="product_help_block" class="form-text text-muted">
                                Please select the corresponding product this ticket is concerned with.
                            </small>
                            
                            <p id="form-error-product" class="form-error hidden"></p>
                        </div>

                        {{------- Requester Selection -------}}
                        <div class="form-group mt-4 mb-1 hidden">
                            <span class="d-flex align-items-center">
                                <input id="requester-self-radio-btn" 
                                    class="form-radio mr-2"
                                    type="radio" 
                                    name="requester"
                                    value="S"
                                    {{ $userClassification->isMerchant || $userClassification->isPartner ? 
                                        'data-upline-user-id=' . \App\Models\User::find(Auth::id())->partner->upline->connectedUser->id : 
                                        '' }}>
    
                                <span>I'm creating this ticket for myself</span>
                            </span>
                        </div>
                    
                        <div class="form-group mb-1 hidden">
                            <span class="d-flex align-items-center">
                                <input id="requester-merchant-radio-btn" 
                                    class="form-radio mr-2"
                                    type="radio" 
                                    name="requester" 
                                    value="M">
    
                                <span>I'm creating this ticket for a merchant</span>
                            </span>
                        </div>

                        <div class="form-group hidden">
                            <span class="d-flex align-items-center">
                                <input id="requester-partner-radio-btn"
                                    class="form-radio mr-2"
                                    type="radio" 
                                    name="requester" 
                                    value="P">
    
                                <span>I'm creating this ticket for a partner</span>
                            </span>
                        </div>

                        <div class="form-group hidden">
                            <label>Merchant</label>
                            
                            <select id="merchant-select"
                                class="form-control select2"
                                name="merchant"
                                data-placeholder="Select Merchant" 
                                data-allow-clear="true">
                            </select>

                            <p id="form-error-merchant" class="form-error hidden"></p>
                        </div>

                        <div class="form-group hidden">
                            <label>Partner</label>
                            
                            <select id="partner-select"
                                class="form-control select2"
                                name="partner"
                                data-placeholder="Select Partner" 
                                data-allow-clear="true">
                            </select>

                            <p id="form-error-partner" class="form-error hidden"></p>
                        </div>

                        {{------- Issue Type Selection -------}}
                        <div class="form-group hidden">
                            <label>Issue Type</label>
                                
                            <select id="issue-type-select"
                                class="form-control select2"
                                name="issue_type"
                                data-placeholder="Select Issue Type" 
                                data-allow-clear="true">
                            </select>
        
                            <p id="form-error-issue_type" class="form-error hidden"></p>
                        </div>

                        {{------- Reason Selection -------}}
                        <div class="form-group hidden">
                            <label>Reason</label>
                                
                            <select id="reason-select"
                                class="form-control select2"
                                name="reason"
                                data-placeholder="Select Reason" 
                                data-allow-clear="true">
                            </select>
        
                            <p id="form-error-reason" class="form-error hidden"></p>
                        </div>

                        <div class="mt-4 text-right hidden">
                            <a href="#advance-options">Advance Options <i class="fa fa-caret-down"></i></a>
                        </div>

                        <div id="advance-options-container" class="hidden">
                            {{------- Assignee Selection -------}}
                            <div class="form-group">
                                <label>Assignee</label>

                                <div class="visible-panel">
                                    <div class="sliding-panel">
                                        <div class="left-panel">
                                            <select id="assignee-department-select" 
                                                class="form-control select2" 
                                                name="assignee_department" 
                                                data-placeholder="Select a Department" 
                                                data-allow-clear="true">
                                            </select>
                                        </div>
                                
                                        <div class="right-panel">
                                            <select id="assignee-user-select"
                                                class="form-control select2"
                                                name="assignee_user"
                                                data-placeholder="Select Assignee" 
                                                data-allow-clear="true">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                              
                                <p id="form-error-assignee_department" class="form-error hidden"></p>
                              
                                <a href="#" id="back-btn" class="btn btn-xs btn-default pull-right hidden">
                                    <i class="fa fa-chevron-left"></i> Back
                                </a>
                            </div>

                            {{------- CC Selection -------}}
                            <div class="form-group">
                                <label>CC's</label>
                                    
                                <select id="ccs-select"
                                    class="form-control select2"
                                    name="ccs[]"
                                    data-placeholder="Select users" 
                                    data-allow-clear="true"
                                    multiple>
                                </select>
            
                                <p id="form-error-ccs" class="form-error hidden"></p>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label>Priority</label>
                                
                                    <select id="priority-select"
                                        class="form-control select2"
                                        name="priority"
                                        data-placeholder="Select Reason" 
                                        data-allow-clear="true">
                                        
                                        <option></option>

                                        @foreach ($ticketPriorities as $priority)
                                            <option value="{{ $priority->code }}">
                                                {{ $priority->description }}
                                            </option>
                                        @endforeach
                                    </select>
                
                                    <p id="form-error-priority" class="form-error hidden"></p>
                                </div>

                                <div class="form-group col-lg-4">
                                    <label>Due Date</label>
                                    
                                    <input id="due-date-input"
                                        name="due_date" 
                                        type='text' 
                                        class="form-control" 
                                        placeholder="Click to select date" />
                    
                                    <p id="form-error-due_date" class="form-error hidden"></p>
                                </div>
                            
                                <div class="form-group col-lg-4">
                                    <label>&nbsp;</label>
                                    
                                    <input id="due-time-input"
                                        name="due_time" 
                                        type="text" 
                                        class="form-control" 
                                        placeholder="Click to select time" />
                    
                                    <p id="form-error-due_time" class="form-error hidden"></p>
                                </div>
                            </div>
                        </div>

                        {{------- Ticket Subject and Description -------}}
                        <div class="form-group mt-4">
                            <label>Subject<span class="required"></span></label>
                            <input id="subject-input" type="text" class="form-control" name="subject" readonly>
                            <p id="form-error-subject" class="form-error hidden"></p>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="description-textarea" class="form-control" name="description" rows="5" style="white-space: normal !important" readonly></textarea>
                            <p id="form-error-description" class="form-error hidden"></p>
                        </div>

                        <div class="form-group">
                            <input id="attachments-file-input" type="file" name="attachments[]" multiple disabled>
                        </div>

                        <div class="form-group mt-4">
                            <button id="submit-button" class="btn btn-primary pull-right" disabled>Create Ticket</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        // Datetime adjusted from 'America/New_York' to 'Asia/Manila' timezone
        let dates = {
            LOW: {
                DATE: '{{ substr(\Carbon\Carbon::now()->addHours(12)->addDays(3)->toDateTimeString(), 0, 10) }}',
                TIME: '{{ substr(\Carbon\Carbon::now()->addHours(12)->addDays(3)->toDateTimeString(), 11, 5) }}'
            },
            MEDIUM: {
                DATE: '{{ substr(\Carbon\Carbon::now()->addHours(12)->addDays(2)->toDateTimeString(), 0, 10) }}',
                TIME: '{{ substr(\Carbon\Carbon::now()->addHours(12)->addDays(2)->toDateTimeString(), 11, 5) }}'
            },
            HIGH: { 
                DATE: '{{ substr(\Carbon\Carbon::now()->addHours(12)->addDays(1)->toDateTimeString(), 0, 10) }}',
                TIME: '{{ substr(\Carbon\Carbon::now()->addHours(12)->addDays(1)->toDateTimeString(), 11, 5) }}'
            },
            URGENT: { // + 12 hrs after create_date of ticket
                DATE: '{{ substr(\Carbon\Carbon::now()->addDays(1)->toDateTimeString(), 0, 10) }}',
                TIME: '{{ substr(\Carbon\Carbon::now()->addDays(1)->toDateTimeString(), 11, 5) }}'
            }
        };

        class CreateTicketForm {
            constructor(userClassification) {
                this.userClassification       = userClassification
                this.form                     = $('#create-ticket-form')
                this.currentStep              = 1
                this.select2Elements          = $('.select2')
                
                this.companyIdInput           = $('#company-id-input')
                this.productSelect            = $('#product-select')
                this.issueTypeSelect          = $('#issue-type-select')
                this.reasonSelect             = $('#reason-select')
                this.subjectInput             = $('#subject-input')
                this.descriptionTextarea      = $('#description-textarea')
                this.attachmentsFileInput     = $('#attachments-file-input')
                this.submitButton             = $('#submit-button')
                this.advanceOptionsFormInputs = new AdvanceOptionsFormInputs()
                this.requesterFormInputs      = new RequesterFormInputs(this)

                this.merchants = []
                this.partners = []
                this.issueTypes = []
                this.reasons = []
                this.departmentsGroups = []
                this.usersGroups = []

                this.initialize()
            }

            down() {
                if (this.currentStep == 1) {
                    console.warn('Reached Step 1 - Cannot Go Down Further')
                    return
                }

                this.currentStep -= 1   
                switch (this.currentStep) {
                    case 1: this.step2().down()
                    case 2: this.step3().down()
                    case 3: this.step4().down()
                    case 4: this.step5().down()
                }                
            }

            emptySelect(select) {
                select.find('option').remove()
            }

            goto(step) {
                console.log(`Going to step ${step} from ${this.currentStep}`)

                const self = this
                if (step > this.currentStep) {
                    _.times(step - this.currentStep, function() { self.up() })
                } else {
                    _.times(this.currentStep - step, function() { self.down()})
                }
            }

            hideElement(element) {
                element.parent().slideUp('fast')
            }

            initialize() {
                const self = this

                this.select2Elements.select2()
                this.productSelect.on('change', function() {
                    if (this.value == '') {
                        self.goto(1)
                        this.form.reset()
                    } else {
                        axiosCustom.get(`/tickets/create/${this.value}/dependencies`)
                            .then(data => data.data)
                            .then(data => {
                                self.companyIdInput.val(data.companyId)
                                self.merchants = data.merchants
                                self.partners = data.partners
                                self.issueTypes = data.issueTypes
                                self.reasons = data.reasons
                                self.departmentsGroups = data.departmentsGroups
                                self.usersGroups = data.usersGroups

                                self.requesterFormInputs.populateMerchantSelect(self.merchants)
                                self.requesterFormInputs.populatePartnerSelect(self.partners)
                                self.advanceOptionsFormInputs
                                    .assigneeFormInputs
                                    .populateAssigneeDepartmentSelect(self.departmentsGroups)

                                self.advanceOptionsFormInputs.populateCcsSelect(self.usersGroups)

                                self.populateIssueTypeSelect()
                                self.goto(2)
                            })
                            .catch(err => console.error(err))
                    }
                })

                this.issueTypeSelect.on('change', function() {
                    if ($(this).data('stop-event'))
                        return

                    if (this.value == '') self.goto(3)
                    else {
                        self.populateReasonSelect(this.value)
                        self.goto(4) 
                    }
                })

                this.reasonSelect.on('change', function() {
                    if ($(this).data('stop-event'))
                        return

                    if (this.value == '') self.goto(4)
                    else {
                        const reason = self.reasons.find(reason => {
                            return reason.id == this.value
                        })

                        let selectedDepartment = undefined
                        for (let key in self.departmentsGroups) {
                            if (self.departmentsGroups.hasOwnProperty(key)) {
                                selectedDepartment = self.departmentsGroups[key].find(department => {
                                    return department.id == reason.department_id
                                })

                                if (selectedDepartment != undefined)
                                    break;
                            }
                        }

                        if (selectedDepartment == undefined) {
                            self.advanceOptionsFormInputs.show() 
                        } else { 
                            if (selectedDepartment.head_id == -1) {
                                self.advanceOptionsFormInputs.show() 
                            }
                            
                            self.advanceOptionsFormInputs.setDepartment(selectedDepartment)
                        }

                        self.advanceOptionsFormInputs.setPriority(reason.ticket_priority_code)
                        self.goto(5)  
                    } 
                })

                this.form.on('submit', function(e) {
                    e.preventDefault()

                    let formData = new FormData(this)
                    axiosCustom.post('/tickets/store-ajax', new FormData(this))
                        .then(() => {
                            $('.form-error').addClass('hidden')
                            window.location.replace("/tickets");
                        })
                        .catch(err => console.error(err))
                })
            }

            populateIssueTypeSelect() {
                this.emptySelect(this.issueTypeSelect)

                this.issueTypeSelect.data('stop-event', '1')
                this.issueTypeSelect.append('<option></option>')
                this.issueTypes.forEach(issueType => {
                    this.issueTypeSelect.append(
                        `<option value="${issueType.id}">` + 
                            `${issueType.description}` +
                        `</option>`)
                })

                this.issueTypeSelect.trigger('change')
                this.issueTypeSelect.removeData('stop-event')
            }

            populateReasonSelect(issueTypeId) {
                this.emptySelect(this.reasonSelect)

                console.log('Populating reason select!')
                this.reasonSelect.data('stop-event', '1')
                this.reasonSelect.append('<option></option>')
                this.reasons.forEach(reason => {
                    if (reason.ticket_type_id != issueTypeId)
                        return

                    this.reasonSelect.append(
                        `<option value="${reason.id}">` + 
                            `${reason.description}` +
                        `</option>`)
                })

                this.reasonSelect.trigger('change')
                this.reasonSelect.removeData('stop-event')
            }

            showElement(element) {
                element.parent().slideDown('fast')
            }

            step1() {
                const self = this 
                return {
                    up () {
                        return
                    },

                    down() {
                        return
                    }
                }
            }

            step2(){
                const self = this
                return {
                    up() {
                        if (self.userClassification.isMerchant) {
                            self.requesterFormInputs.selfRadioButton.click()
                            return
                        } else {
                            self.requesterFormInputs.show()
                        }
                    },

                    down() {
                        self.requesterFormInputs.hide()
                        self.down(2)
                    }
                }
            }

            step3() {
                const self = this
                return {
                    up() {
                        self.showElement(self.issueTypeSelect)
                    },

                    down() {
                        self.issueTypeSelect.data('stop-event', '1')
                        self.issueTypeSelect.val('').trigger('change')
                        self.issueTypeSelect.removeData('stop-event')
                        self.hideElement(self.issueTypeSelect)
                    },
                }
            }

            step4() {
                const self = this
                return {
                    up() {
                        self.showElement(self.reasonSelect)
                    },

                    down() {
                        self.reasonSelect.data('stop-event', '1')
                        self.reasonSelect.val('').trigger('change')
                        self.reasonSelect.removeData('stop-event')
                        self.hideElement(self.reasonSelect)
                    },
                }
            }

            step5() {
                const self = this
                return {
                    up() {
                        self.subjectInput.attr({readonly: false})
                        self.descriptionTextarea.attr({readonly: false})
                        self.submitButton.attr({disabled: false})
                        self.attachmentsFileInput.attr({disabled: false})

                        if (self.userClassification.isMerchant ||
                            self.userClassification.isPartner) {
                            return
                        } 

                        self.advanceOptionsFormInputs.showToggleButton()
                    },

                    down() {
                        self.advanceOptionsFormInputs.hide()
                        self.advanceOptionsFormInputs.hideToggleButton()
                        self.subjectInput.val('')
                        self.subjectInput.attr({readonly: true})
                        self.descriptionTextarea.val('')
                        self.descriptionTextarea.attr({readonly: true})
                        self.submitButton.attr({disabled: true})
                        self.attachmentsFileInput.attr({disabled: true})
                    },
                }
                
            }

            up() {
                if (this.currentStep == 5) {
                    console.warn('Reached Step 5 - Cannot Go Up Further')
                    return
                }

                this.currentStep += 1
                switch (this.currentStep) {
                    case 5: this.step5().up()
                    case 4: this.step4().up()
                    case 3: this.step3().up()
                    case 2: this.step2().up()
                }    
            }
        }

        class AdvanceOptionsFormInputs {
            constructor() {
                this.advanceOptionsContainer = $('#advance-options-container')
                this.advanceOptionsToggleBtn = $('a[href="#advance-options"]')

                this.assigneeFormInputs      = new AssigneeFormInputs()
                this.ccsSelect               = $('#ccs-select')
                this.prioritySelect          = $('#priority-select')
                this.dueDateFormInputs       = new DueDateFormInputs()

                this.initialize()
            }

            initialize() {
                const self = this

                this.advanceOptionsToggleBtn.on('click', function(e) {
                    e.preventDefault()

                    if (self.advanceOptionsContainer.is(':hidden')) {
                        self.show()
                    } else {
                        self.hide() 
                    }
                })

                this.prioritySelect.on('change', function() {
                    self.dueDateFormInputs.setDueDate(this.value)
                })
            }

            isHidden() {
                this.advanceOptionsContainer.is(':hidden')
            }

            hide() {
                this.advanceOptionsToggleBtn.find('i').addClass('fa-caret-down')
                this.advanceOptionsToggleBtn.find('i').removeClass('fa-caret-up')
                this.advanceOptionsContainer.slideUp()
            }

            hideToggleButton() {
                this.advanceOptionsToggleBtn.parent().slideUp('fast')
            }

            populateCcsSelect(usersGroups) {
                this.ccsSelect.empty()
                this.ccsSelect.append('<option></option>')
                for (let key in usersGroups) {
                    if (usersGroups.hasOwnProperty(key)) {
                        console.log(usersGroups[key][0])
                        const optgroup = $(
                            `<optgroup label="${usersGroups[key][0].partner_company.company_name}">` +
                            `</optgroup>`)

                        usersGroups[key].forEach(user => {
                            optgroup.append(
                                `<option value="${user.id}">` + 
                                    `${user.first_name} ${user.last_name}` +
                                `</option>`)
                        })

                        this.ccsSelect.append(optgroup)
                    }
                }

                this.ccsSelect.trigger('change')
            }

            setDepartment(department) {
                this.assigneeFormInputs.setDepartment(department)
            }

            setPriority(code) {
                this.prioritySelect.val(code).trigger('change')
            }

            show() {
                this.advanceOptionsToggleBtn.find('i').removeClass('fa-caret-down')
                this.advanceOptionsToggleBtn.find('i').addClass('fa-caret-up')
                this.advanceOptionsContainer.slideDown()
            }

            showToggleButton() {
                this.advanceOptionsToggleBtn.parent().slideDown('fast')
            }
        }

        class AssigneeFormInputs {
            constructor() {
                this.slidingPanel             = $('.sliding-panel')

                this.assigneeDepartmentSelect = $('#assignee-department-select')
                this.assigneeUserSelect       = $('#assignee-user-select')
                this.backButton               = $('#back-btn')

                this.departmentsGroups = []
                this.initialize()
            }

            initialize() {
                const self = this
                self.assigneeDepartmentSelect.on('change', function() {
                    if (this.value == '')
                        return

                    self.populateAssigneeUserSelect(this.value)
                    self.backButton.removeClass('hidden')   
                    self.slideRight()
                })

                self.assigneeUserSelect.on('change', function() {
                    if (this.value != '')
                        return
                })

                self.backButton.on('click', function(e) {
                    e.preventDefault()
                    self.slideLeft()
                })
            }

            populateAssigneeDepartmentSelect(departmentsGroups) {
                this.assigneeDepartmentSelect.find('optgroup').remove()
                this.assigneeDepartmentSelect.append('<option></option>')
                for (let key in departmentsGroups) {
                    if (departmentsGroups.hasOwnProperty(key)) {
                        const optgroup = $(
                            `<optgroup label="${departmentsGroups[key][0].partner_company.company_name}">` +
                            `</optgroup>`)

                        departmentsGroups[key].forEach(department => {
                            console.log('Adding Department')
                            optgroup.append(
                                `<option value="${department.id}">` + 
                                    `${department.description}` +
                                `</option>`)
                        })

                        this.assigneeDepartmentSelect.append(optgroup)
                    }
                }

                this.departmentsGroups = departmentsGroups
                this.assigneeDepartmentSelect.trigger('change')
            }

            populateAssigneeUserSelect(departmentId) {
                let selectedDepartment = undefined
                for (let key in this.departmentsGroups) {
                    if (this.departmentsGroups.hasOwnProperty(key)) {
                        selectedDepartment = this.departmentsGroups[key].find(department => {
                            return department.id == departmentId
                        })

                        if (selectedDepartment != undefined)
                            break
                    }
                }

                this.assigneeUserSelect.find('option').remove()
                this.assigneeUserSelect.append('<option></option>')
                selectedDepartment.users.forEach(user => {
                    this.assigneeUserSelect.append(
                        `<option value="${user.id}">` + 
                            `${user.first_name} ${user.last_name}` +
                        `</option>`)
                })
                
                if (selectedDepartment.head_id != -1) {
                    this.assigneeUserSelect.append(
                        `<option value="-1">` + 
                            `${selectedDepartment.description}` +
                        `</option>`)
                }

                const lastOptionValue = this.assigneeUserSelect
                    .find('option')
                    .last()
                    .val()

                this.assigneeUserSelect.val(lastOptionValue).trigger('change')
            }

            setDepartment(department) {
                this.assigneeDepartmentSelect.val(department.id).trigger('change')
                this.assigneeUserSelect.val('-1').trigger('change')
                this.slideRight()
            }

            slideLeft() {
                console.log('Sliding Left')
                this.backButton.addClass('hidden')
                this.slidingPanel.removeClass('sliding-panel-right');
            }

            slideRight() {
                console.log('Sliding Right')
                this.backButton.removeClass('hidden')
                this.slidingPanel.addClass('sliding-panel-right');
            }
        }

        class DueDateFormInputs {
            constructor() {
                this.dueDateInput = $('#due-date-input')
                this.dueTimeInput = $('#due-time-input')

                this.initialize()
            }

            initialize() {
                this.dueDateInput.datepicker({ 
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    startDate: '0d',
                })

                this.dueTimeInput.timepicker({ 
                    defaultTime: false,
                    showInputs: true,
                    showMeridian: false,
                    icons: {
                        up:   "fa fa-chevron-up",
                        down: "fa fa-chevron-down",
                    },
                })
            }

            setDueDate(priorityCode) {
                switch (priorityCode) {
                    case 'L':
                        this.dueDateInput.val(dates.LOW.DATE)
                        this.dueTimeInput.val(dates.LOW.TIME)
                        break

                    case 'M':
                        this.dueDateInput.val(dates.MEDIUM.DATE)
                        this.dueTimeInput.val(dates.MEDIUM.TIME)
                        break

                    case 'H':
                        this.dueDateInput.val(dates.HIGH.DATE)
                        this.dueTimeInput.val(dates.HIGH.TIME)
                        break

                    case 'U':
                        this.dueDateInput.val(dates.URGENT.DATE)
                        this.dueTimeInput.val(dates.URGENT.TIME)
                        break
                }
            }
        }

        class RequesterFormInputs {
            constructor(owner) {
                this.owner               = owner
                this.radioButtons        = $('input[name="requester"]')

                this.selfRadioButton     = $('#requester-self-radio-btn')
                this.merchantRadioButton = $('#requester-merchant-radio-btn')
                this.partnerRadioButton  = $('#requester-partner-radio-btn')

                this.merchantSelect = $('#merchant-select')
                this.partnerSelect  = $('#partner-select')

                this.initialize()
            }

            initialize() {
                const self = this

                this.radioButtons.on('change', function() {
                    switch (this.value) {
                        case 'S':
                            self.chooseSelfRadioButton()
                            self.owner.goto(3)
                            break

                        case 'M':
                            self.chooseMerchantRadioButton()
                            self.owner.goto(2)
                            break

                        case 'P':
                            self.choosePartnerRadioButton()
                            self.owner.goto(2)
                            break
                    }
                })

                this.merchantSelect.on('change', function() {
                    if ($(this).data('stop-event'))
                        return

                    if (this.value == '') 
                        self.owner.goto(2)
                    
                    const uplineUserId = $(this).find('option:selected').data('upline-user-id')
                    if (self.owner
                        .advanceOptionsFormInputs
                        .ccsSelect
                        .find(`option[value="${uplineUserId}"]`)
                        .length > 0) {

                        self.owner
                            .advanceOptionsFormInputs.ccsSelect
                            .val([uplineUserId])
                            .trigger('change')
                    }

                    self.owner.goto(3)
                })

                this.partnerSelect.on('change', function() {
                    if ($(this).data('stop-event'))
                        return

                    if (this.value == '') self.owner.goto(2)
                    else self.owner.goto(3)
                })
            }

            chooseSelfRadioButton() {
                if (this.owner.userClassification.isMerchant ||
                    this.owner.userClassification.isPartner) {
                    
                    const uplineUserId = this.selfRadioButton.data('upline-user-id')
                    console.log(uplineUserId)
                    if (this.owner
                        .advanceOptionsFormInputs
                        .ccsSelect
                        .find(`option[value="${uplineUserId}"]`)
                        .length > 0) {

                        this.owner
                            .advanceOptionsFormInputs.ccsSelect
                            .val([uplineUserId])
                            .trigger('change')
                    }
                }

                this.merchantSelect.parent().slideUp('fast');
                this.partnerSelect.parent().slideUp('fast');
            } 

            chooseMerchantRadioButton() {
                const self = this
                self.partnerSelect.parent().slideUp('fast', function() {
                    self.merchantSelect.parent().slideDown('fast');
                });
            }

            choosePartnerRadioButton() {
                const self = this
                self.merchantSelect.parent().slideUp('fast', function() {
                    self.partnerSelect.parent().slideDown('fast');
                });
            }

            hide() {
                const self = this
                self.partnerRadioButton.parent().parent().slideUp('fast')
                self.merchantRadioButton.parent().parent().slideUp('fast')
                self.selfRadioButton.parent().parent().slideUp('fast')
            }

            populateMerchantSelect(merchantsGroups) {
                this.merchantSelect.find('optgroup').remove()
                this.merchantSelect.data('stop-event', '1')
                this.merchantSelect.append('<option></option>')
                for (let key in merchantsGroups) {
                    if (merchantsGroups.hasOwnProperty(key)) {
                        const optgroup = $(
                            `<optgroup label="${merchantsGroups[key][0].company_name}">` +
                            `</optgroup>`)

                        merchantsGroups[key].forEach(merchant => {
                            optgroup.append(
                                `<option value="${merchant.id}" data-upline-user-id="${merchant.upline_user_id}">` + 
                                    `${merchant.self_company_name}` +
                                `</option>`)
                        })

                        this.merchantSelect.append(optgroup)
                    }
                }

                this.merchantSelect.trigger('change')
                this.merchantSelect.removeData('stop-event')
            }
 
            populatePartnerSelect(partnersGroups) {
                this.partnerSelect.find('optgroup').remove()
                this.partnerSelect.data('stop-event', '1')
                this.partnerSelect.append('<option></option>')
                for (let key in partnersGroups) {
                    if (partnersGroups.hasOwnProperty(key)) {
                        const optgroup = $(
                            `<optgroup label="${partnersGroups[key][0].company_name}">` +
                            `</optgroup>`)

                        partnersGroups[key].forEach(partner => {
                            optgroup.append(
                                `<option value="${partner.id}">` + 
                                    `${partner.first_name} ${partner.last_name}` +
                                `</option>`)
                        })

                        this.partnerSelect.append(optgroup)
                    }
                }

                this.partnerSelect.trigger('change')
                this.partnerSelect.removeData('stop-event')
            }

            show() {
                const self = this

                if (self.owner.userClassification.isAdmin ||
                    self.owner.userClassification.isCompany ||
                    self.owner.userClassification.isInternalDepartmentHead ||
                    self.owner.userClassification.isInternal) {
                    self.merchantRadioButton.parent().parent().slideDown(75, function() {
                        self.partnerRadioButton.parent().parent().slideDown(50)
                    })
                } else {
                    self.selfRadioButton.parent().parent().slideDown(150, function() {
                        self.merchantRadioButton.parent().parent().slideDown(75, function() {
                            self.partnerRadioButton.parent().parent().slideDown(50)
                        })
                    })
                }
            }
        }

        const createTicketForm = new CreateTicketForm(@json($userClassification))
    </script>
@endsection