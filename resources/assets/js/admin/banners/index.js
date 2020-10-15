import swal from "sweetalert2"

$(document).ready(function() {
  $('#table-banner').dataTable();
  $('#btn-delete').on('click', function(e) {
    e.preventDefault()
    let form = $('#form-banners-delete')
    if ($('input[type="checkbox"]:checked').length == 0) {
      swal({
        type: 'error',
        title: "No Banner Selected",
        text: 'Please select one or more banners',
        animation: true,
        showConfirmButton: true,
        allowOutsideClick: false,
        position: "center"
      })
    } else {
      swal({
        title: 'Delete banner/s?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Confirm'
      }).then((result) => {
        if (result.value) {
          form.submit()
        }
      })
    }
  })
})
