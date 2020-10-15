import axios from "axios";
import swal from "sweetalert2";

let appUrl = document.querySelector("#ctx").getAttribute("content");

function loadTickets(ticketListTable, filter) {
    /** Change Select Input */
    if (filter != null)  {
        $('#select-ticket-filter').val(filter)
    }
 
    /** Reload Table */
    ticketListTable.ajax.reload()
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

$(document).ready(function() {
    let ticketListTable = $('.datatables').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/tickets/admin',
            data: function(data) {
                data.filter = $("#select-ticket-filter").val()
                data.priorityCode = $("#select-ticket-priority").val()
            }
        },
        columns: [
            {'data': 'id', 'orderable': false},
            {'data': 'idLink'},
            {'data': 'subject', 'width': '25%'},
            {'data': 'created_at'},
            {'data': 'status'},
            {'data': 'type'},
            {'data': 'created_by'},
        ],
        'columnDefs': [
            {
               'targets': 0,
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

    $('#select-ticket-filter').on('change', function() {
        loadTickets(ticketListTable)
    })

    $('#select-ticket-priority').on('change', function() {
        loadTickets(ticketListTable)
    })

    $('#form-ticket').on('change', '.ticket-ids', function() {
        $(this).is(':checked') ?
            $(this).parent().parent().addClass('selected') :
            $(this).parent().parent().removeClass('selected');
    })

    $('#btn-delete-tickets').on('click', function() {
        if (validateTickets(ticketListTable)) {
            let formData = new FormData()

            let rowsSelected = ticketListTable.column(0).checkboxes.selected();
            for (let i = 0; i < rowsSelected.length; i++) {
                formData.append('ticket_ids[]', rowsSelected[i]);
            }
    
            swal({
                title: 'Deleting Tickets...',
                text: 'Please wait while deleting.',
                imageUrl: appUrl + "/images/user_img/goetu-profile.png",
                imageAlt: 'GOETU Image',
                imageHeight: 140,
                animation: false,
                showConfirmButton: false,
                allowOutsideClick: false,
                position: "center"
            })
    
            axios.post('/tickets/deleteTickets', formData)
                .then(function(response) {
                    response.data.unprocessedTicketIds.forEach(function(ticket) {
                        $('.selected').each(function() {
                            if (ticket == $(this).find('input:checkbox:first').val()) {
                                $(this).removeClass('selected');
                            }
                        })
                    })
                    
                    ticketListTable.rows( '.selected' ).remove().draw();
                    ticketListTable.column(0).checkboxes.deselect();
    
                    swal.close()

                    let responseMessage = ''
                    if (response.data.deletedTicketIds.length != 0) {
                        responseMessage += 'Successfully deleted ticket/s: ' + 
                            response.data.deletedTicketIds.toString() + 
                            '<br />'
                    }

                    if (response.data.unprocessedTicketIds.length != 0) {
                        responseMessage += '<span style="color:red">Permission denied deleting ticket/s: ' + 
                            response.data.unprocessedTicketIds.toString() + '</span>'
                    }

                    swal({
                        title: 'Deleting Result',
                        html: responseMessage,
                        imageUrl: appUrl + "/images/user_img/goetu-profile.png",
                        imageAlt: 'GOETU Image',
                        imageHeight: 140,
                        animation: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        position: "center"
                    })
                })
                .catch(function(error) {
                    alert(error)
                    swal.close()
                })
        }
    })
})