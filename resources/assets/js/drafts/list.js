import swal from "sweetalert2";

$(document).ready(function () {

    $('.datatable').dataTable();

    /* $("input.toggle-vis").on( 'click', function(){
        var table = $('#applicants-table').DataTable();
        var column = table.column( $(this).attr('data-column') );
        column.visible( ! column.visible() );
    }); */

});

function deleteDraftApplicant(id) {
    showLoadingAlert('Loading...');

    $.ajax({
        type: "POST",
        url: '/drafts/deleteDraftApplicant',
        data: 'partner_id=' + id,
        success: function(data) {
            closeLoading();
            if (data.success) {
                showSuccessMessage(data.message, '/drafts');
            } else {
                showWarningMessage(data.message);
            }
        },
    });
}

function showWarningMessage(msg) {
    swal("Warning", msg, "warning");
}

function showSuccessMessage(msg, url) {
    swal("Success", msg,"success").then((value) => {
        window.location.href = url;
    })
}

function showLoadingAlert(msg) {
    swal({
        title: msg,
        allowEscapeKey: false,
        allowOutsideClick: false,
        onOpen: () => {
          swal.showLoading();
        }
    })
}

function closeLoading() {
    swal.close();
}
window.deleteDraftApplicant = deleteDraftApplicant;