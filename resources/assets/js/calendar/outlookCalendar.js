import swal from "sweetalert2";

$(document).ready(function () {

    $('#btnSyncOutlookCal').click(function () {
        showLoadingAlert('Loading...');
        
        $.ajax({
            url: "/calendar/getOClient",
            type: "GET",
            success: function(data) {
                closeLoading();
                if (data.success) {
                    // showSuccessMessage(data.message, data.url);
                    window.location.href = data.url;
                } else {
                    showWarningMessage(data.message);
                }
            },
        });
    });
});

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