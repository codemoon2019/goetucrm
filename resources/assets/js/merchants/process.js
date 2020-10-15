import axios from "axios";
import swal from 'sweetalert2'

function boardMerchant(id) {
    if (!confirm('This will board the current merchant. Proceed?')) {
      return false;
    }

    $.getJSON('/merchants/confirm_merchant/' + id , null, function (data) {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else{
        alert(data.message);
      }
    });
}

function approveMerchant(id) {
    if (!confirm('This will approve the current merchant. Proceed?')) {
      return false;
    }

    $.getJSON('/merchants/finalize_merchant/' + id , null, function (data) {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert(data.message);
      }
    });
}

function declineMerchant(merchantId) {
    swal({
        title: 'Reason of Action',
        input: 'text',
        inputValidator: (value) => {
            return new Promise((resolve) => {
                resolve()
            })
        },
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#808080',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Decline',
        cancelButtonText: 'Close'

    }).then((result) => {
        if (result.value) {
            axios.post(`/merchants/${merchantId}/decline`, {
                reason_of_action: result.value
            })
                .then(response => {
                    alert(response.data.message);
                    location.reload(true)
                })
                .catch(error => {
                    console.log(error)
                })
        }
    })
}

window.boardMerchant = boardMerchant;
window.approveMerchant = approveMerchant;
window.declineMerchant = declineMerchant;