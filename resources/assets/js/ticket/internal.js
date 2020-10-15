import axios from "axios";
import swal from "sweetalert2";

let appUrl = document.querySelector("#ctx").getAttribute("content");

function loadTickets(ticketListTable, filter = null, moveTab = true) {
    if (filter != null) {
        $('#select-ticket-filter').val(filter)
    }

    ticketListTable.ajax.reload()

    if (moveTab) {
        $('.nav-tabs > li').removeClass('active')
        $('.tab-content > div').removeClass('active')
        $('.tab-ticket-list').addClass('active')
    }
}

function validateTickets(ticketListTable) {
    let rowsSelected = ticketListTable.column(0).checkboxes.selected();

    if (rowsSelected.length == 0) {
        swal({
            title: 'No Ticket Selected',
            text: 'Please select ticket/s',
            imageUrl: appUrl + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            position: "center"
        })

        return false
    }

    return true
}

function validateMerging(ticketListTable) {
    let rowsSelected = ticketListTable.column(0).checkboxes.selected();

    if (rowsSelected.length < 2) {
        showPrompt('Not enough selected ticket', 'Please select two or more ticket/s')
        return false
    }

    return true
}

function validateAssignees() {
    if (!$(".sliding-panel").hasClass('sliding-panel-right')) {
        showPrompt('No Assignee Selected', 'Please select assignee/s')
        return false
    }

    return true
}

function getDepartments(ticketListTable) {
    showLoading('Loading Departments...', 'Please wait while loading departments')

    let params = ''
    let rowsSelected = ticketListTable.column(0).checkboxes.selected();
    for (let i = 0; i < rowsSelected.length; i++) {
        params += rowsSelected[i] + ','
    }

    axios.get('/tickets/getDepartments', {
        params: {
            ticket_ids: params.substring(0, params.length - 1)
        }
    })
        .then(function (response) {
            closeLoading()

            $("#select-department").find('option').remove().end()
            $('#select-department').append('<option value ="-1">Please select department</option>')
            response.data.departments.forEach(function (department) {
                $('#select-department').append('<option value ="' + department.id + '">' + department.description + '</option>')
            })

            $("#viewModal").modal()
        })
        .catch(function (error) {
            showPrompt('Something went wrong',
                'There was an error processing you request, if you believe\
                 this is a bug, please contact our team')
            closeLoading()
        })
}

function showLoading(title, text) {
    swal({
        title: title,
        text: text,
        imageUrl: appUrl + "/images/user_img/goetu-profile.png",
        imageAlt: 'GOETU Image',
        imageHeight: 140,
        animation: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        position: "center"
    })
}

function closeLoading() {
    swal.close()
}

function showPrompt(title, html) {
    swal({
        title: title,
        html: html,
        imageUrl: appUrl + "/images/user_img/goetu-profile.png",
        imageAlt: 'GOETU Image',
        imageHeight: 140,
        animation: false,
        showConfirmButton: true,
        allowOutsideClick: false,
        position: "center"
    })
}

$(document).ready(function () {
    let ticketListTable = $('.datatables').DataTable({
        'processing': true,
        'serverSide': true,
        'pageLength': 25,
        'ajax': {
            'url': '/tickets/internal',
            'data': function (data) {
                data.filter = $("#select-ticket-filter").val()
                data.priorityCode = $("#select-ticket-priority").val()
                data.departmentId = $("#select-ticket-department").val()
                data.companyId = $("#select-ticket-company").val()
                data.requesterId = $('#select-ticket-requester').val()
            }
        },
        'columns': [
            {'data': 'id', 'orderable': false},
            {'data': 'idLink'},
            {'data': 'subject', 'width': '25%'},
            {'data': 'department'},
            {'data': 'priority'},
            {'data': 'created_at'},
            {'data': 'due_date'},
            {'data': 'status'},
            {'data': 'type'},
            {'data': 'assignee'},
            {'data': 'created_by'},
        ],
        'columnDefs': [
            {
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'checkboxes': {
                    'selectRow': true
                }
            }
        ],
        'select': {
            'style': 'multi'
        },
        'order': [[1, 'desc']]
    })

    $('.small-box').on('click', function () {
        $('.tab-dashboard-content').hide();

        $('.tab-ticket-list').show();
        $('.tab-ticket-list-content').addClass('active')

        let filterValue = $(this).data('filter')
        let statusValue = $(this).data('status')
        statusValue = statusValue == 'A' ? '' : statusValue

        if (filterValue == 'A') {
            $("#select-ticket-department").val('A')
            statusValue = 'A' + statusValue;
        } else if (filterValue == 'D') {
            statusValue = 'A' + statusValue;
            $("#select-ticket-department").val('A')
        } else {
            statusValue = 'M' + statusValue;
            $("#select-ticket-department").val('A')
        }

        loadTickets(ticketListTable, statusValue);
    })

    $('#select-ticket-filter').on('change', function () {
        let condition1 = $(this).val() == 'AD' || $(this).val() == 'AM'
        let condition2 = $(this).val() == 'MM' || $(this).val() == 'MD'

        if (condition1 || condition2) {
            $('.div-actions').css('visibility', 'hidden')
        } else {
            $('.div-actions').css('visibility', 'visible')
        }

        loadTickets(ticketListTable)
    })

    $('#select-ticket-department').on('change', function () {
        loadTickets(ticketListTable)
    })

    $('#select-ticket-requester').on('change', function () {
        loadTickets(ticketListTable)
    })

    $('#select-ticket-priority').on('change', function () {
        loadTickets(ticketListTable)
    })

    $('#select-ticket-company').on('change', function () {
        $('#select-ticket-department').val('A')
        $('#select-ticket-department option').removeClass('hidden')

        if ($(this).val() != 'A') {
            $('#select-ticket-department option:not(.option-company-' + $(this).val() + ')').addClass('hidden');
            $('#select-ticket-department .option-company--1').removeClass('hidden');
        }

        loadTickets(ticketListTable)
    })

    $('#form-ticket').on('change', '.ticket-ids', function () {
        $(this).is(':checked') ?
            $(this).parent().parent().addClass('selected') :
            $(this).parent().parent().removeClass('selected');
    })

    $('#btn-delete-tickets').on('click', function () {
        if (validateTickets(ticketListTable)) {
            let formData = new FormData()
            let rowsSelected = ticketListTable.column(0).checkboxes.selected();
            for (let i = 0; i < rowsSelected.length; i++) {
                formData.append('ticket_ids[]', rowsSelected[i]);
            }

            showLoading('Deleting Tickets...', 'Please wait while deleting')

            axios.post('/tickets/deleteTickets', formData)
                .then(function (response) {
                    response.data.unprocessedTicketIds.forEach(function (ticket) {
                        $('.selected').each(function () {
                            if (ticket == $(this).find('input:checkbox:first').val()) {
                                $(this).removeClass('selected');
                            }
                        })
                    })

                    ticketListTable.rows('.selected').remove().draw();
                    ticketListTable.column(0).checkboxes.deselect();

                    closeLoading()

                    let responseMessage = ''
                    if (response.data.deletedTicketIds.length != 0) {
                        responseMessage += 'Successfully deleted ticket/s # ' +
                            response.data.deletedTicketIds.toString() +
                            '<br />'
                    }

                    if (response.data.unprocessedTicketIds.length != 0) {
                        responseMessage += '<span style="color:red">Permission denied deleting ticket/s #' +
                            response.data.unprocessedTicketIds.toString() + '</span>'
                    }

                    showPrompt('Deleting Result', responseMessage)
                    ticketListTable.ajax.reload()
                    
                    if (response.data.deletedTicketIds.length != 0) {
                        location.reload();
                    }
                })
                .catch(function (error) {
                    showPrompt('Something went wrong',
                        'There was an error processing you request, if you believe\
                        this is a bug, please contact our team')
                    closeLoading()
                })
        }
    })

    $('#btn-assign-to-me-tickets').on('click', function () {
        if (validateTickets(ticketListTable)) {
            let formData = new FormData()
            let rowsSelected = ticketListTable.column(0).checkboxes.selected();
            for (let i = 0; i < rowsSelected.length; i++) {
                formData.append('ticket_ids[]', rowsSelected[i]);
            }

            showLoading('Assigning Tickets...', 'Please wait while assigning.')

            axios.post('/tickets/assignToMeTickets', formData)
                .then(function (response) {
                    closeLoading()
                    loadTickets(ticketListTable)

                    let responseMessage = ''
                    if (response.data.assignedTicketIds.length != 0) {
                        responseMessage += 'Successfully assigned ticket/s # ' +
                            response.data.assignedTicketIds.toString() +
                            ' to yourself' + ' <br />'
                    }

                    if (response.data.unprocessedTicketIds.length != 0) {
                        responseMessage += '<span style="color:red">Assignee exist on ticket/s # ' +
                            response.data.unprocessedTicketIds.toString() + '</span>'
                    }

                    showPrompt('Assigning Result', responseMessage)
                    ticketListTable.column(0).checkboxes.deselect();
                    ticketListTable.ajax.reload()
                })
                .catch(function (error) {
                    showPrompt('Something went wrong',
                        'There was an error processing you request, if you believe\
                        this is a bug, please contact our team')
                    closeLoading()
                })
        }
    })

    $('.btn-assign-tickets').on('click', function () {
        if (validateTickets(ticketListTable)) {
            $('.back').trigger('click')
            getDepartments(ticketListTable);
        }
    })

    $('#btn-assign-tickets-go').on('click', function (e) {
        if (validateAssignees()) {
            let assignee_id = $("#select-assignee").val();
            let department_id = $("#select-department").val();

            let formData = new FormData()
            let rowsSelected = ticketListTable.column(0).checkboxes.selected();
            for (let i = 0; i < rowsSelected.length; i++) {
                formData.append('ticket_ids[]', rowsSelected[i]);
            }

            formData.append('assignee_id', assignee_id)
            formData.append('department_id', department_id)

            showLoading('Assigning Tickets...', 'Please wait while assigning.')

            axios.post('/tickets/assignTickets', formData)
                .then(function (response) {
                    closeLoading();

                    response.data.unprocessedTicketIds.forEach(function (ticket) {
                        $('.selected').each(function () {
                            if (ticket == $(this).find('input:checkbox:first').val()) {
                                $(this).removeClass('selected');
                            }
                        })
                    })

                    if ($("#select-ticket").val() == 2 || $("#select-ticket").val() == 5) {
                        let ticketListTable = $("#ticket-list").DataTable()
                        ticketListTable.rows('.selected').remove().draw();
                    } else {
                        ticketListTable.column(0).checkboxes.deselect();
                    }

                    $("#viewModal").modal('hide');

                    let responseMessage = ''
                    if (response.data.assignedTicketIds.length != 0) {
                        responseMessage += 'Successfully assigned ticket/s # ' +
                            response.data.assignedTicketIds.toString() +
                            ' to ' + $('#select-assignee option:selected').text() +
                            ' <br />'
                    }

                    if (response.data.unprocessedTicketIds.length != 0) {
                        responseMessage += '<span style="color:red">Permission denied assigning ticket/s # ' +
                            response.data.unprocessedTicketIds.toString() + '</span>'
                    }

                    showPrompt('Assigning Result', responseMessage)
                    ticketListTable.ajax.reload()
                })
                .catch(function (error) {
                    showPrompt('Something went wrong',
                        'There was an error processing you request, if you believe\
                        this is a bug, please contact our team')
                    closeLoading()
                })
        }
    })

    $('#btn-merge-tickets').on('click', function () {
        if (validateMerging(ticketListTable)) {
            let formData = new FormData()
            let rowsSelected = ticketListTable.column(0).checkboxes.selected();
            for (let i = 0; i < rowsSelected.length; i++) {
                formData.append('ticket_ids[]', rowsSelected[i]);
            }

            showLoading('Merging Tickets', 'Please wait while merging')

            axios.post('/tickets/mergeTickets', formData)
                .then(function (response) {
                    closeLoading()

                    if (response.data.success) {
                        $('.selected').first().removeClass('selected')
                        $('input[type="checkbox"]').removeAttr('checked');

                        let ticketListTable = $("#ticket-list").DataTable()
                        ticketListTable.rows('.selected').remove().draw();

                        showPrompt('Tickets merged', 'Successfully merged Tickets')
                        ticketListTable.column(0).checkboxes.deselect();
                        ticketListTable.ajax.reload()
                        location.reload();
                    } else {
                        showPrompt('Merging Failed',
                            '<span style="color:red"> \
                                It seems that the tickets you chose does not have the same creator \
                            </span>'
                        )
                    }
                })
                .catch(function (error) {
                    showPrompt('Something went wrong',
                        'There was an error processing you request, if you believe\
                        this is a bug, please contact our team')
                    closeLoading()
                })
        }
    })

    $('.group-list').on('click', 'option', function () {
        $('.group-list').trigger('change')
    })

    $('.group-list').change(function (e) {
        if ($(this).val() != -1) {
            $('.sliding-panel').addClass('sliding-panel-right');
            $('.back').removeClass('hide');

            let department = $('option:selected', this).val();
            let department_name = $('option:selected', this).text();
            let url = '/tickets/getUsersByDepartment/' + department;

            $.ajax({
                url: url,
            }).done(function (items) {
                let option = "";
                $.each(items.users, function (key, item) {
                    option += '<option value="' + item.id + '">' + item.first_name + ' ' + item.last_name + '</option> ';
                });

                option += '<option value="-1">' + department_name + '</option> ';

                let newOption = option;
                $('#select-assignee').empty();
                $('#select-assignee').append(newOption);
                $('#select-assignee').trigger("chosen:updated");
            });
        }
    });

    $('.back').click(function (e) {
        $('.back').addClass('hide');
        $('.sliding-panel').removeClass('sliding-panel-right');
    });

    $('.table-super-admin').DataTable({
        paging: false,
        searching: false,
        ordering: false,
        bInfo: false,
        columnDefs: [
            {
                targets: [0],
                width: '15px',
            },
            {
                targets: [2, 3, 4, 5, 6],
                width: '10%',
                className: 'dt-center'
            },
        ]
    })

    $('.table-super-admin .td-toggle-departments').on('click', function () {
        let companyId = $(this).data('company')

        $('.tr-company-' + companyId).toggleClass('hidden')
        if ($('.tr-company-' + companyId).first().hasClass('hidden')) {
            $(this).html('&#9654;')
        } else {
            $(this).html('&#9660;')
        }
    })

    $('.table-super-admin .span-count').on('click', function () {
        let filterCode = $(this).data('filter')
        let companyCode = $(this).data('company')
        let departmentCode = $(this).data('department') === undefined ?
            'A' : $(this).data('department')

        $('#select-ticket-filter').val(filterCode)
        $('#select-ticket-company').val(companyCode)
        $("#select-ticket-department").val(departmentCode)

        loadTickets(ticketListTable, null)

        $('.tab-dashboard-content').hide();
        $('.tab-ticket-list').show();
        $('.tab-ticket-list-content').addClass('active')
    })

    $(function () {
            var i = 0;
            var parameters = {};
            var searchString = location.search.substr(1);
            var pairs = searchString.split("&");
            var parts;
            for (i = 0; i < pairs.length; i++) {
                parts = pairs[i].split("=");
                var name = parts[0];
                var data = decodeURI(parts[1]);
                parameters[name] = data;
            }
           if(parameters['companyCode'])
           {
               let filterCode = parameters['filterCode'] === '' ?
                   'A' : parameters['filterCode']
               let companyCode = parameters['companyCode']
               let departmentCode = $(this).data('department') === undefined ?
                   'A' : $(this).data('department')

               $('#select-ticket-filter').val(filterCode)
               $('#select-ticket-company').val(companyCode)
               $("#select-ticket-department").val(departmentCode)

               loadTickets(ticketListTable, null)

               $('.tab-dashboard-content').hide();
               $('.tab-ticket-list').show();
               $('.tab-ticket-list-content').addClass('active')
           }
        }
    )

    function formatSelect2(resource) {
        if (resource.element !== undefined && 
          resource.element.dataset !== undefined && 
          resource.element.dataset.image !== undefined) {
            return $(
            '<span style="margin-left: 3px;">' +
                '<img style="transform: translateY(-1px)" class="ticket-img-xs" src="' + resource.element.dataset.image + '">' +
                '<span style="color: black;">' + resource.text + '</span>' + 
            '</span>'
            )
        }
  
        return $('<span>' + resource.text + '</span>')
    }
  
    function formatSelect2Result(resource) {
        if (resource.element !== undefined && 
            resource.element.dataset !== undefined && 
            resource.element.dataset.image !== undefined) {
    
            if (resource.element.dataset.user_type !== undefined) {
                return $(
                    '<div style="display: flex; align-items: center;">' +
                    '<img class="ticket-img-md" src="' + resource.element.dataset.image + '">' +
                    '<span style="display: flex; flex-direction: column; margin-left: 10px;">' +
                        '<span style="font-size: 1.1rem;"><strong>' + resource.text + '</strong></span>' +
                        '<span style="font-size: 0.8rem; transform: translate(5px, -2px)">' + resource.element.dataset.user_type + '</span>' +
                    '</span><!--/ta-item-actor-details-->' +
                    '</div><!--/ta-item-actor--></div>'
                )
            }
    
            return $(
                '<span>' +
                    '<img class="ticket-img-xs" src="' + resource.element.dataset.image + '">' +
                    '<span>' + resource.text + '</span>' + 
                '</span>'
            )
        }
    
        return $('<span>' + resource.text + '</span>')
    }
  
    $('.js-example-basic-single').select2({
        templateSelection: formatSelect2,
        templateResult: formatSelect2Result
    })
})