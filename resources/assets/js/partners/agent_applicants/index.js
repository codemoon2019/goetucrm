import axios from "axios";
import swal from "sweetalert2";

let appUrl = document.querySelector("#ctx").getAttribute("content");
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

function toggleButtons(value) {
    if (value == '0') {
        $('.btn-approve').show()
        $('.btn-disapprove').show()
        $('.btn-restore').hide()
    } else if (value == '1') {
        $('.btn-approve').hide()
        $('.btn-disapprove').hide()
        $('.btn-restore').hide()
    } else if (value == '2') {
        $('.btn-approve').hide()
        $('.btn-disapprove').hide()
        $('.btn-restore').show()
    }
}

function processRequest(title, message, url, agentApplicantDataTable) {
    if (!validate()) {
        return false
    }

    let form = $("#form-agent-applicants").get(0)
    let formData = new FormData(form);

    swal({
        title: title,
        text: message,
        imageUrl: appUrl + "/images/user_img/goetu-profile.png",
        imageAlt: 'GOETU Image',
        imageHeight: 140,
        animation: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        position: "center"
    })

    axios.post(url, formData)
        .then(function(response) {
            if (response.data.success) {
                agentApplicantDataTable.ajax.reload()
                swal.close()

                swal({
                    title: response.data.message,
                    imageUrl: appUrl + "/images/user_img/goetu-profile.png",
                    imageAlt: 'GOETU Image',
                    imageHeight: 140,
                    animation: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    position: "center"
                })
            } else {
                alert(response.data.message)
            }
        })
        .catch(function(error) {
            swal.close()
            alert(error)
        })
}

function validate() {
    if ($('input[name="agent_applicant_ids[]"]:checked').length == 0) {
        swal({
            title: 'No Agent Applicant Selected',
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
    let agentApplicantDataTable = $('table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/partners/agent-applicants/get',
            data: function(data) {
                data.filter = $("#select-filter-agent-applicants").val()
            }
        },
        columns: [
            {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
            {data: 'first_name'},
            {data: 'last_name'},
            {data: 'company'},
            {data: 'email_address'},
            {data: 'mobile_number'},
            {data: 'business_address'},
            {data: 'source'},
            {data: 'status'}
        ],
    })

    toggleButtons( $('#select-filter-agent-applicants').val() )

    $('.btn-approve').on('click', function() {
        processRequest(
            'Approving Agent Applicants...', 
            'Please wait while approving.',
            '/partners/agent-applicants/approve',
            agentApplicantDataTable
        )
    })

    $('.btn-disapprove').on('click', function() {
        processRequest(
            'Disapproving Agent Applicants...', 
            'Please wait while disapproving.',
            '/partners/agent-applicants/disapprove',
            agentApplicantDataTable
        )
    })

    $('.btn-restore').on('click', function() {
        processRequest(
            'Restoring Agent Applicants...', 
            'Please wait while restoring.',
            '/partners/agent-applicants/restore',
            agentApplicantDataTable
        )
    })

    $('#select-filter-agent-applicants').on('change', function() {
        toggleButtons( $(this).val() )
        agentApplicantDataTable.ajax.reload()
    })
})