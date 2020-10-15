import react from "react";
import axios from "axios";
import swal from "sweetalert2";
import * as Config from "../../react/config";


$(() => {

    CKEDITOR.replace('comment');

    /**
     * Status change
     */
    $(document).on('change', '#status', function (e) {

        swal({
            title: 'Loading...',
            text: 'Changing status...',
            imageUrl: Config.APP_URL + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        });

        var statusCode = $(this).val();
        var ticketId = $('#ticketId').val();
        axios.post(Config.APP_URL + "/tickets/update-status", {
            ids: ticketId,
            status: statusCode
        }).then(() => {
            location.reload();
        });
    });

    /**
     * file upload get file name
     */
    $(document).on('change','#exampleFormControlFile1',function(e){
        console.log($(this).val());
        var filePath = $(this).val().split('\\');
        $('#file_uploaded').empty().html(`File: ${filePath[filePath.length - 1]} <input type='button' class='btn-danger btn-circle' id="removeAttachment" value="X"/>`);
    });

    /**
     * Remove file upload
     */
    $(document).on('click','#removeAttachment',function(e){
        e.preventDefault();
        $('#file_uploaded').empty();
        $('#exampleFormControlFile1').val('');
    });

});