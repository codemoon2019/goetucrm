$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        }
    });

    loadProductList();
});

function deleteMainProduct(product_id){
    if (confirm('Delete this product?')) {
        var formData = {
            product_id: product_id
        };

        $.ajax({
            type:'GET',
            url:'/products/deleteProduct',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                        // window.location.href = window.location.href;
                    }
                    loadProductList();
                }else {
                    if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                    }
                }
            }
        });
    }else {
        return false;
    }
}



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
        url: "/products/uploadfile", // Url to which the request is send
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
                // var delay = 3000; //3 second
                // setTimeout(function () {
                //     var str = window.location.href;
                //     str = str.replace("#", '');
                //     window.location.href = str;
                // }, delay);
                loadProductList();
            }
        }
    });
    return false;
});

function upload() {
    $('#modalUploadCSV').modal('show');
    return false;
}

function loadProductList(){
    $('.datatables').dataTable().fnDestroy();
    let dt = $('.datatables').dataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: {
            url: '/products/getProducts',
            data: function(data) {
                data.companyId = $('select[name="sel-company"]').val();
            }
        },
        columns: [
            {data: 'code'},
            {data: 'name'},
            {data: 'description'},
            {data: 'productType'},
            {data: 'companyOwner'},
            {data: 'buy_rate'},
            {data: 'actions'},
        ]
    })

    $('select[name="sel-company"]').on('change', function() {
        dt.api().ajax.reload()
    })


}

window.deleteMainProduct = deleteMainProduct;
window.upload = upload;
window.loadProductList = loadProductList;
