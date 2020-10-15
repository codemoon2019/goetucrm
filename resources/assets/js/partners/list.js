$('.datatables').dataTable();

$('.tabs-rectangular li a').click(function(){
    var curActive = $(this).parents('.tabs-rectangular');

    // hide active view
    var curActiveId = curActive.find('li.active a').attr('id');
    $('#'+curActiveId+'Container').addClass('hide');

    // change active view
    var id = $(this).attr('id');
    $('#'+id+'Container').removeClass('hide');

    // change active tab
    curActive.find('li.active').removeClass('active');
    $(this).parent().addClass('active');

    hiddenCols = []
});

$('#frmUploadCSV').submit(function () {
    var filename = document.getElementById("fileUploadCSV").value;
    if (document.getElementById("fileUploadCSV").value == "") {
        alert('Please select a file');
        return false;
    }
    var ext = filename.split('.').pop();
    if (ext != "csv") {
        alert('Please select csv file format.');
        return false;
    }

    $('#modalUploadCSV').modal('hide');
    showLoadingModal('Processing...');
    $.ajax({
        url: "/partners/uploadfile", // Url to which the request is send
        type: "POST", // Type of request to be send, called as method
        data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        dataType: 'json',
        contentType: false, // The content type used when sending data to the server.
        cache: false, // To unable request pages to be cached
        processData: false, // To send DOMDocument or non processed data file it is set to false
        success: function success(data) // A function to be called if request succeeds
        {
            closeLoadingModal();
            if (!data.logs) {
                alert(data.message);
                var delay = 3000; //3 second
                setTimeout(function () {
                    var str = window.location.href;
                    str = str.replace("#", '');
                    window.location.href = str;
                }, delay);
            } else {
                var logs = "";
                for (var i = 0; i < data.logs.length; i++) {
                    logs = logs + data.logs[i] + " \n";
                }
                alert('Successfully processed file but with exceptions \n\n' + logs);
                var delay = 3000; //3 second
                setTimeout(function () {
                    var str = window.location.href;
                    str = str.replace("#", '');
                    window.location.href = str;
                }, delay);
            }
        }
    });
    return false;
});

function upload() {
    $('#modalUploadCSV').modal('show');
    return false;
}

window.upload = upload;