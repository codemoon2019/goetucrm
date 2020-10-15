import swal from "sweetalert2";
$(document).ready(function() {

    $("#btnSubmitChangeCompany").click(function() {

        var formData = new FormData( $("#frmChangeCompany")[0] );
        swal({
            title: 'Changing Company...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
              swal.showLoading();
            }
        })

        $.ajax({
            type: "POST",
            url: '/admin/users/changeCompany',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                swal.close();
                if (data.success) {
                  swal("Success", data.message, "success");
                  location.reload();
                } else {
                  swal("Warning", data.message, "warning");
                }
            },
        });
        $('#modalChangeCompany').modal('hide'); 
    });


 });


