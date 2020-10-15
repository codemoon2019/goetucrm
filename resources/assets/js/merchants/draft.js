import swal from "sweetalert2";

$(document).ready(function () {

    // $('.datatable').dataTable();

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
                showSuccessMessage(data.message, '/merchants/draft_merchant');
            } else {
                showWarningMessage(data.message);
            }
        },
    });
}

function deleteDraftBranchApplicant(id) {
    showLoadingAlert('Loading...');

    $.ajax({
        type: "POST",
        url: '/drafts/deleteDraftApplicant',
        data: 'partner_id=' + id,
        success: function(data) {
            closeLoading();
            if (data.success) {
                showSuccessMessage(data.message, '/merchants/draft_branch');
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
window.deleteDraftBranchApplicant = deleteDraftBranchApplicant;