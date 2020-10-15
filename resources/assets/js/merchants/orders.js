import swal from "sweetalert2";
let appUrl = document.querySelector("#ctx").getAttribute("content");

$(document).ready(function(){
    
});

function load_orders(){ 
    swal({
        title: 'Orders',
        text: 'Loading data. Please wait...',
        imageUrl: appUrl + "/images/user_img/goetu-profile.png",
        imageAlt: 'GOETU Image',
        imageHeight: 140,
        animation: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        position: "center"
    })
    $.getJSON('/merchants/details/orders_data', null, function(data) {  
        var oTable = $('#orders-list').dataTable( {"bRetrieve": true,"order": [[ 2, "asc" ], [ 3, "asc" ]]} );
        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }
        $('#orders-list').DataTable().columns.adjust().responsive.recalc();
    });
    swal.close();
    
}

window.load_orders = load_orders;