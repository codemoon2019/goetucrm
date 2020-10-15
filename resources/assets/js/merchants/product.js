$(document).ready(function () {
    $("input[type='text']").attr('maxLength','50');
    
    $('.datatables').dataTable({ "lengthMenu": [25, 50, 75, 100 ],});
    $('#workflow-list').DataTable( {
        "order": [[ 2, "asc" ],[ 3, "asc" ]]
    } );
    var arr_cat_id = [];
    $('.mainProd').change(function () {
        arr_cat_id = [];
        var product_id = $(this).val();
        var chk = this.checked;
        var singleOnly = $(this).attr("data-sel");
        element = document.getElementById('categoryList main-'+product_id);
        // element = document.getElementById('categoryList');
        // element.parentNode.removeChild(element);
        html = '<div id="categoryList main-' + product_id + '">';
        if(chk){
            $(".mainprodcat-" + product_id).each(function () {
                var cat_id = $(this).val();
                var cat_name = $(this).attr("data-name");
                var cat_desc = $(this).attr("data-desc");
                var cat_div = document.getElementById('cat-chk-'+cat_id);
                var required = $(this).attr("data-req");
                var canPFEdit = $('#txtPFEdit').val();
                if(!cat_div){
                    html = html + '<div class="form-group">';
                    if(required == 1){
                        html = html + '<input  type="checkbox" class="catList catProd'+ product_id +'" id="cat-chk-' + cat_id + '" data-id="' + cat_id + '" data-name="' + cat_name + '" data-main="' + product_id + '" data-req="1" style="display:none;" checked>&nbsp;&nbsp;&nbsp;';
                    }else{
                        if(singleOnly == 1){
                            html = html + '<input  type="radio" name="cat-chk-' + product_id + '" class="catListRadio" id="cat-chk-' + cat_id + '" data-id="' + cat_id + '" data-name="' + cat_name + '" data-main="' + product_id + '" data-req="0">&nbsp;&nbsp;&nbsp;';  
                        }else{
                            html = html + '<input  type="checkbox" class="catList" id="cat-chk-' + cat_id + '" data-id="' + cat_id + '" data-name="' + cat_name + '" data-main="' + product_id + '" data-req="0">&nbsp;&nbsp;&nbsp;';
                        }                       
                    }


                    html = html + '<b>' + cat_name + '</b> - <i>' + cat_desc + '</i>';
                    if(canPFEdit == 1){
                          html = html + '<table class="table  table-striped" id="order-details-' + cat_id + '" style="display:none;font-size:12px">' +
                            '<thead>' +
                            '<tr>' +
                            '<th width="30%">Product</th>' +
                            '<th width="10%">Image</th>' +
                            '<th width="10%">Category</th>' +
                            '<th width="10%">Payment Frequency</th>' +
                            '<th width="10%">Price</th>' +
                            '<th width="10%">Start Date</th>' +
                            '<th width="10%">End Date</th>' +
                            '<th width="10%">Quantity</th>' +
                            '<th width="10%">Amount</th>' +
                            '<th width="10%">Action</th>' +
                            '</tr>' +
                            '</thead>' +
                            '<tbody>' +
                            '</tbody>' +
                            '</table></div>';  
                        }else{
                          html = html + '<table class="table  table-striped" id="order-details-' + cat_id + '" style="display:none;font-size:12px">' +
                            '<thead>' +
                            '<tr>' +
                            '<th width="30%">Product</th>' +
                            '<th width="10%">Image</th>' +
                            '<th width="10%">Category</th>' +
                            '<th width="10%">Price</th>' +
                            '<th width="10%">Quantity</th>' +
                            '<th width="10%">Amount</th>' +
                            '<th width="10%">Action</th>' +
                            '</tr>' +
                            '</thead>' +
                            '<tbody>' +
                            '</tbody>' +
                            '</table></div>'; 
                        }
                    
                }
            });
            html = html + '</div>'; 
            $('#divCategories').append(html);
            $('.catProd'+product_id).trigger('change');
        } else {
            element.parentNode.removeChild(element);
        }

        // $("#order-details tbody tr").remove(); 
        // $(".main-prod-div").remove();
        // $(".subProductList").remove();
        // if(!catdiv){
        //     $('#divSubProducts').append('<div class="box-header with-border main-prod-div" id="category-div-'+product_id+'">'+
        //         '<h4 class="box-title">'+product_name+'</h4>'+
        //     '</div>'+
        //     '<div id="subProductList-'+product_id+'" class="subProductList">'+
        //         '<table class="table  table-striped" id="order-details-'+product_id+'">'+
        //             '<thead>'+
        //                 '<tr>'+
        //                     '<th width="30%">Product</th>'+
        //                     '<th width="20%">Category</th>'+
        //                     '<th width="20%">Payment Frequency</th>'+
        //                     '<th width="10%">Quantity</th>'+
        //                     '<th width="10%">Amount</th>'+
        //                     '<th width="10%">Action</th>'+
        //                 '</tr>'+
        //             '</thead>'+
        //             '<tbody>'+
        //             '</tbody>'+
        //         '</table>'+
        //     '</div>');
        // }
    }); 
    // $('#prodSelection').trigger('change');

$(document).on('change', '.catList', function (e) {
    var product_id = $(this).attr('data-main');
    var cat_id = $(this).attr('data-id');
    var cat_name = $(this).attr('data-name');
    var cat_req = $(this).attr('data-req');
    var chk = this.checked;
    var canPFEdit = $('#txtPFEdit').val();
    
    $.getJSON('/merchants/select_payment_frequencies', null, function (data) {
        $(".mainprod-" + product_id).each(function () {
            var sub_id = $(this).val();
            var sub_name = $(this).attr("data-name");
            var sub_cat_id = $(this).attr("data-cat");
            var sub_amt = $(this).attr("data-brate");
            var sub_fqy = $(this).attr("data-frequency");
            var main_prod_id = $(this).attr('class').split('-').pop();
            var table_prod = document.getElementById('table-prod-' + sub_id);
            var pic = $(this).attr("data-pic");
            
            if (product_id == main_prod_id) {
                if (cat_id == sub_cat_id) {
                    if (chk && !table_prod) {
                        var singleOnly = $('#cat-'+cat_id).attr("data-sel");

                        arr_cat_id.push(cat_id);
                        table = document.getElementById('order-details-' + cat_id);
                        $('#order-details-' + cat_id).show();
                        var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
                        var pid = row.insertCell(0);
                        var main_prod = row.insertCell(1);
                        var field_product_name = row.insertCell(2);
                        var picture = row.insertCell(3);
                        var field_cat = row.insertCell(4);
                        var field_frequency = row.insertCell(5);

                        var price = row.insertCell(6);
                        var start_date = row.insertCell(7);
                        var end_date = row.insertCell(8)

                        var field_qty = row.insertCell(9);
                        var field_amount = row.insertCell(10);
                        var action = row.insertCell(11);

                        row.className = "subproductrecord";
                        row.id = "table-prod-" + sub_id;
                        field_product_name.className = "table-val-name";
                        if(singleOnly == 1){
                            field_product_name.innerHTML = '<input  type="radio" name="cat-radio-chk-' + cat_id + '" id="sel-prod-'+ sub_id+'">&nbsp;&nbsp;'+ sub_name;
                        }else{
                            field_product_name.innerHTML = '<input  type="checkbox" id="sel-prod-'+ sub_id+'" style="display:none;" checked>&nbsp;&nbsp;'+ sub_name;
                        }
                        picture.innerHTML = '<img style="border:1px solid black;" src='+pic+' height="100px" width="100px" alt="" >';
                        field_cat.className = "table-val-cat-" + cat_id;
                        field_cat.innerHTML = cat_name;

                        field_frequency.className = "table-val-frequency";

                        field_frequency.innerHTML = (canPFEdit == 1) ? '<select id="subfreq-' + sub_id + '">' + data + '</select>' : '<select id="subfreq-' + sub_id + '" disabled>' + data + '</select>' ;

                        price.className = "table-val-price";
                        start_date.className = "table-start-cat-" + cat_id;
                        end_date.className = "table-end-cat-" + cat_id;

                        // price.innerHTML = parseFloat(sub_amt).toFixed(2);
                        price.innerHTML = parseFloat(sub_amt).toFixed(2) + '<input type="hidden" id="subamt-price-' + sub_id + '" value="' + parseFloat(sub_amt).toFixed(2) + '">';
                        var d = new Date();
                        var month = d.getMonth()+1;
                        var day = d.getDate();
                        start_date.innerHTML = '<input class="form_datetime" id="startdate-' + sub_id + '" value="'+d.getFullYear()+'-'+ (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day+'" style="width:80%;font-size:12px">';
                        end_date.innerHTML = '<input class="form_datetime"  id="enddate-' + sub_id + '" style="width:80%;font-size:12px">';                       

                        field_amount.className = "table-val-amount";
                        field_amount.innerHTML = '<input id="subamt-' + sub_id + '" value="' + parseFloat(sub_amt).toFixed(2) + '" onkeypress="validate_numeric_input(event);" onchange="updateFormat(' + sub_id + ');" style="text-align:right; width:70%">';

                        field_qty.className = "table-val-qty";
                        field_qty.innerHTML = '<input id="subqty-' + sub_id + '" value="1" onkeypress="validate_numeric_input(event);" onchange="computeTotal(' + sub_id + ');" style="text-align:right; width:50%">';

                        if(cat_req == 1 || singleOnly == 1){
                            action.innerHTML = '';
                        }else{
                            action.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this,' + cat_id + ')">Remove</button>';                           
                        }


                        pid.className = "table-val-pid";
                        pid.innerHTML = sub_id;
                        pid.style.display = "none";

                        if(canPFEdit == 0){
                            start_date.style.display = "none";
                            end_date.style.display = "none";
                            field_frequency.style.display = "none";
                        }

                        main_prod.className = "table-val-mid";
                        main_prod.innerHTML = '<input id="subpid-' + sub_id + '" value="' + product_id + '" type="hidden">';
                        main_prod.style.display = "none";

                        $("#subfreq-" + sub_id).val(sub_fqy);

                        $(".form_datetime").datepicker({autoclose: true,format: 'yyyy-mm-dd'});

                    } else {
                        if ($('#table-prod-' + sub_id).length != 0) {
                            element = document.getElementById('table-prod-' + sub_id);
                            element.parentNode.removeChild(element);
                            $('#order-details-' + cat_id).hide();
                        }
                    }
                }
            }
        });
    });
});


$(document).on('change', '.catListRadio', function (e) {

    $(".catListRadio").each(function(){
        var product_id = $(this).attr('data-main');
        var cat_id = $(this).attr('data-id');
        var cat_name = $(this).attr('data-name');
        var cat_req = $(this).attr('data-req');
        var chk = this.checked;
        var canPFEdit = $('#txtPFEdit').val();
        //var pic = $(this).attr("data-pic");
        
        $.getJSON('/merchants/select_payment_frequencies', null, function (data) {
            $(".mainprod-" + product_id).each(function () {
                var sub_id = $(this).val();
                var sub_name = $(this).attr("data-name");
                var sub_cat_id = $(this).attr("data-cat");
                var sub_amt = $(this).attr("data-brate");
                var sub_fqy = $(this).attr("data-frequency");
                var main_prod_id = $(this).attr('class').split('-').pop();
                var table_prod = document.getElementById('table-prod-' + sub_id);
                var pic = $(this).attr("data-pic");

                if (product_id == main_prod_id) {
                    if (cat_id == sub_cat_id) {
                        if (chk && !table_prod) {
                            var singleOnly = $('#cat-'+cat_id).attr("data-sel");
                            arr_cat_id.push(cat_id);
                            table = document.getElementById('order-details-' + cat_id);
                            $('#order-details-' + cat_id).show();
                            var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
                            var i=0;
                            var pid = row.insertCell(0);
                            var main_prod = row.insertCell(1);
                            var field_product_name = row.insertCell(2);
                            var picture = row.insertCell(3);
                            var field_cat = row.insertCell(4);
                            var field_frequency = row.insertCell(5);

                            var price = row.insertCell(6);
                            var start_date = row.insertCell(7);
                            var end_date = row.insertCell(8);

                            var field_qty = row.insertCell(9);
                            var field_amount = row.insertCell(10);
                            var action = row.insertCell(11);


                            row.className = "subproductrecord";
                            row.id = "table-prod-" + sub_id;
                            field_product_name.className = "table-val-name";
                            if(singleOnly == 1){
                                field_product_name.innerHTML = '<input  type="radio" name="cat-radio-chk-' + cat_id + '" id="sel-prod-'+ sub_id+'">&nbsp;&nbsp;'+ sub_name;
                            }else{
                                field_product_name.innerHTML = '<input  type="checkbox" id="sel-prod-'+ sub_id+'" style="display:none;" checked>&nbsp;&nbsp;'+ sub_name;
                            }
                            picture.innerHTML = '<img style="border:1px solid black;" src='+pic+' height="100px" width="100px" alt="" >';

                            field_cat.className = "table-val-cat-" + cat_id;
                            field_cat.innerHTML = cat_name;

                            field_frequency.className = "table-val-frequency";
                            field_frequency.innerHTML = (canPFEdit == 1) ? '<select id="subfreq-' + sub_id + '">' + data + '</select>' : '<select id="subfreq-' + sub_id + '" disabled>' + data + '</select>' ;

                            price.className = "table-val-price";
                            start_date.className = "table-start-cat-" + cat_id;
                            end_date.className = "table-end-cat-" + cat_id;

                            // price.innerHTML = parseFloat(sub_amt).toFixed(2);
                            price.innerHTML = parseFloat(sub_amt).toFixed(2) + '<input type="hidden" id="subamt-price-' + sub_id + '" value="' + parseFloat(sub_amt).toFixed(2) + '">';
                            var d = new Date();
                            var month = d.getMonth()+1;
                            var day = d.getDate();
                            start_date.innerHTML = '<input class="form_datetime" id="startdate-' + sub_id + '" value="'+d.getFullYear()+'-'+ (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day+'" style="width:80%;font-size:12px">';
                            end_date.innerHTML = '<input class="form_datetime"  id="enddate-' + sub_id + '" style="width:80%;font-size:12px">';                       

                            field_amount.className = "table-val-amount";
                            field_amount.innerHTML = '<input id="subamt-' + sub_id + '" value="' + parseFloat(sub_amt).toFixed(2) + '" onkeypress="validate_numeric_input(event);" onchange="updateFormat(' + sub_id + ');" style="text-align:right; width:70%">';

                            field_qty.className = "table-val-qty";
                            field_qty.innerHTML = '<input id="subqty-' + sub_id + '" value="1" onkeypress="validate_numeric_input(event);" onchange="computeTotal(' + sub_id + ');" style="text-align:right; width:50%">';

                            if(cat_req == 1 || singleOnly == 1){
                                action.innerHTML = '';
                            }else{
                                action.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this,' + cat_id + ')">Remove</button>';                           
                            }
  
                            pid.className = "table-val-pid";
                            pid.innerHTML = sub_id;
                            pid.style.display = "none";

                            if(canPFEdit == 0){
                                start_date.style.display = "none";
                                end_date.style.display = "none";
                                field_frequency.style.display = "none";
                            }          

                            main_prod.className = "table-val-mid";
                            main_prod.innerHTML = '<input id="subpid-' + sub_id + '" value="' + product_id + '" type="hidden">';
                            main_prod.style.display = "none";

                            $("#subfreq-" + sub_id).val(sub_fqy);

                            $(".form_datetime").datepicker({autoclose: true,format: 'yyyy-mm-dd'});

                        } else {
                            if ($('#table-prod-' + sub_id).length != 0) {
                                element = document.getElementById('table-prod-' + sub_id);
                                element.parentNode.removeChild(element);
                                $('#order-details-' + cat_id).hide();
                            }
                        }
                    }
                }
            });
        });
    });
});

$('#frmMerchantOrder').submit(function () {
    var details = [];
    var product_id;
    var frequency;
    var qty;
    var amount;
    var price;
    var main_pid;var startdate;var enddate;
    var hasError = false;
    var count = 0;

    $(".subproductrecord").find('td').each(function () {
        var cell = $(this);
        switch (cell.attr('class')) {
            case "table-val-pid":
                product_id = cell[0].innerHTML;
                break;
            case "table-val-price":
                price = $("#subamt-price-" + product_id).val();
                break;
            case "table-val-frequency":
                frequency = $("#subfreq-" + product_id).val();
                break;
            case "table-val-mid":
                main_pid = $("#subpid-" + product_id).val();
                break;
            case "table-val-amount":
                amount = $("#subamt-" + product_id).val();
                startdate = $("#startdate-" + product_id).val();
                enddate = $("#enddate-" + product_id).val();
                if($("#sel-prod-"+ product_id).is(":checked")){
                    if(startdate == ""){
                        hasError = true;
                        alert('Fill up all Start Date to proceed.');
                    }    
                    details.push({
                        product_id: product_id,
                        frequency: frequency,
                        amount: amount,
                        qty: qty,
                        main_pid: main_pid,
                        startdate: startdate,
                        enddate: enddate,
                        price: price
                    });   
                    count++;       
                }

                break;
            case "table-val-qty":
                qty = $("#subqty-" + product_id).val();
                break;
            default:
                break;
        }
        
    });

    if (hasError) {
        return false;
    }
    if (count == 0) {
        alert('Cannot Save. No Product to Order!');
        return false;
    }
    $('#txtOrderDetails').val(JSON.stringify(details));
    showLoadingModal("Creating Order... Please wait.....");
});


$('#frmMerchantOrderEdit').submit(function () {
    var details = [];
    var product_id;
    var frequency;
    var qty;
    var amount;
    var price;
    var hasError = false;
    var count = 0;

    $(".subproductrecordedit").find('td').each(function () {
        var cell = $(this);
        switch (cell.attr('class')) {
            case "table-val-edit-pid":
                product_id = cell[0].innerHTML;
                break;
            case "table-val-edit-frequency":
                frequency = $("#subfreq-edit-" + product_id).val();
                break;
            case "table-val-edit-price":
                price = $("#subamt-price-" + product_id).val();
                break;
            case "table-val-edit-amount":
                amount = $("#subamt-edit-" + product_id).val();
                startdate = $("#startdate-" + product_id).val();
                enddate = $("#enddate-" + product_id).val();
                if(startdate == ""){
                    hasError = true;
                    alert('Fill up all Start Date to proceed.');
                }
                details.push({
                    product_id: product_id,
                    frequency: frequency,
                    amount: amount,
                    qty: qty,
                    startdate: startdate,
                    enddate: enddate,
                    price: price
                });
                break;
            case "table-val-edit-qty":
                qty = $("#subqty-edit-" + product_id).val();
                break;
            default:
                break;
        }
        count++;
    });

    if (hasError) {
        return false;
    }
    if (count == 0) {
        alert('Cannot Save. No Product to Order!');
        return false;
    }
    $('#txtOrderDetailsEdit').val(JSON.stringify(details));
    showLoadingModal("Updating Order... Please wait.....");
});



});

function deleteRow(btn, cat_id) {
    var row = btn.parentNode.parentNode;
    row.parentNode.removeChild(row);
    if ($('.table-val-cat-' + cat_id).length == 0) {
        document.getElementById('cat-chk-' + cat_id).checked = false;
        $('#order-details-' + cat_id).hide();
    }
}

function validate_numeric_input(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    var regex = /[0-9\b]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}

function computeTotal(id) {
    $("#subqty-" + id).val(parseFloat($("#subqty-" + id).val()).toFixed(0));
    var amt = $("#subamt-price-" + id).val();
    var qty = $("#subqty-" + id).val();
    if (amt == "" || qty == "") {
        $("#subamt-" + id).val(0);
        $("#subqty-" + id).val(0);
    } else {
        $("#subamt-" + id).val(parseFloat(amt * qty).toFixed(2));
    }
}

function showOrder(id) {
    $.getJSON('/merchants/get_order_details/' + id, null, function (data) {
        $('#txtOrderId').val(data['id']);
        $('#order-header').html('Order #' + data['id'] + '  ' + data['productname']);
        $("#order-product-detail tbody tr").remove();
        var canPFEdit = $('#txtPFEdit').val();
        for (var i = 0; i < data['details'].length; i++) {
            table = document.getElementById('order-product-detail');
            sub_id = data['details'][i]['product_id'];
            sub_amt = data['details'][i]['amount'];
            sub_qty = data['details'][i]['quantity'];
            sub_name = data['details'][i]['productname'];
            sub_fqy = data['details'][i]['frequency'];
            orig_price = data['details'][i]['price'];
            startdate = data['details'][i]['start_date'];
            enddate = data['details'][i]['end_date'];
            pic = data['details'][i]['picture'];

            var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
            var pid = row.insertCell(0);
            var field_product_name = row.insertCell(1);
            var picture = row.insertCell(2);
            var field_frequency = row.insertCell(3);
            var price = row.insertCell(4);
            var start_date = row.insertCell(5);
            var end_date = row.insertCell(6)
            if(enddate == '2999-01-01'){
                enddate = "";
            }

            var field_qty = row.insertCell(7);
            var field_amount = row.insertCell(8);
            var action = row.insertCell(9);

            row.className = "subproductrecordedit";
            row.id = "table-prod-edit-" + sub_id;
            field_product_name.className = "table-val-edit-name";
            field_product_name.innerHTML = sub_name;
            if(pic == ''){
                picture.innerHTML = '';
            }else{
                picture.innerHTML = '<img style="border:1px solid black;" src='+pic+' height="100px" width="100px" alt="" >'; 
            }
            

            field_frequency.className = "table-val-edit-frequency";
            field_frequency.innerHTML = (canPFEdit == 1) ? '<select id="subfreq-edit-' + sub_id + '">' + data['details'][i]['select'] + '</select>': '<select id="subfreq-edit-' + sub_id + '" disabled>' + data['details'][i]['select'] + '</select>' ;


            field_amount.className = "table-val-edit-amount";
            field_amount.innerHTML = '<input id="subamt-edit-' + sub_id + '" value="' + parseFloat(sub_amt).toFixed(2) + '" onkeypress="validate_numeric_input(event);" onchange="updateFormatEdit(' + sub_id + ');" style="text-align:right; width:70%">';

            field_qty.className = "table-val-edit-qty";
            field_qty.innerHTML = '<input id="subqty-edit-' + sub_id + '" value="' + sub_qty + '" onkeypress="validate_numeric_input(event);" onchange="computeTotalEdit(' + sub_id + ');" style="text-align:right; width:50%">';

            price.className = "table-val-edit-price";

            start_date.className = "table-start";
            end_date.className = "table-end";

            price.innerHTML = parseFloat(orig_price).toFixed(2) + '<input type="hidden" id="subamt-price-' + sub_id + '" value="' + parseFloat(orig_price).toFixed(2) + '">';
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();
            start_date.innerHTML = '<input class="form_datetime" id="startdate-' + sub_id + '" value="'+startdate+'" style="width:90%;font-size:12px">';
            end_date.innerHTML = '<input class="form_datetime"  id="enddate-' + sub_id + '" value="'+enddate+'" style="width:90%;font-size:12px">';   

            action.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">Remove</button>';

            pid.className = "table-val-edit-pid";
            pid.innerHTML = sub_id;
            pid.style.display = "none";

            if(canPFEdit == 0){
                start_date.style.display = "none";
                end_date.style.display = "none";
                field_frequency.style.display = "none";
            }    


            $("#subfreq-edit-" + sub_id).val(sub_fqy);

            $(".form_datetime").datepicker({autoclose: true,format: 'yyyy-mm-dd'});
        }

        $('#modalViewOrder').modal('show');
    });
}

function computeTotalEdit(id) {
    $("#subqty-edit-" + id).val(parseFloat($("#subqty-edit-" + id).val()).toFixed(0));
    var amt = $("#subamt-price-" + id).val();
    var qty = $("#subqty-edit-" + id).val();
    if (amt == "" || qty == "") {
        $("#subamt-edit-" + id).val(0);
        $("#subqty-edit-" + id).val(0);
    } else {
        $("#subamt-edit-" + id).val(parseFloat(amt * qty).toFixed(2));
    }
}


function SendEmail(id, emailAddress) {
    showLoadingModal("Sending Email to " + emailAddress + "... Please wait.....", "Sending Email");
    $.getJSON('/merchants/sendEmailOrder/' + id, null, function (data) {
        if (data['message'] == 'success') {
            alert("Email sent.");
            setTimeout(function () {
                window.location.href = data['redirect'];
            }, 1000);
        } else {
            alert(data['message']);
            setTimeout(function () {
                window.location.href = data['redirect'];
            }, 1000);
        }
        closeLoadingModal();
    });
}

function SendWelcomeEmail(id, emailAddress) {
    // To disable:    
    $('.sendWelcomeEmail').css('pointer-events', 'none');
    showLoadingModal("Sending Welcome Email to " + emailAddress + "... Please wait.....", "Sending Welcome Email");
    $.getJSON('/merchants/sendWelcomeEmail/' + id, null, function (data) {
        // To re-enable:
        $('.sendWelcomeEmail').css('pointer-events', 'auto');
        alert(data['message']);
        closeLoadingModal();
    });
    closeLoadingModal();
    // To re-enable:
    $('.sendWelcomeEmail').css('pointer-events', 'auto');
}

function updateFormat(id) {
    
    var mrp = parseFloat($("#prod-"+id).attr("data-mrp"));
    var srp = parseFloat($("#subamt-price-" + id).val());
    var qty = parseFloat($("#subqty-" + id).val());
    var price = parseFloat($("#subamt-" + id).val());

    if(mrp > 0){
        if(mrp > (price/qty)){
            $("#subamt-" + id).val(parseFloat(mrp*qty).toFixed(2));
        }else{
            $("#subamt-" + id).val(parseFloat($("#subamt-" + id).val()).toFixed(2));        
        } 
    }else{
        if(srp > (price/qty)){
            $("#subamt-" + id).val(parseFloat(srp*qty).toFixed(2));
        }else{
            $("#subamt-" + id).val(parseFloat($("#subamt-" + id).val()).toFixed(2));        
        }      
    }


}

function updateFormatEdit(id) {

    var mrp = parseFloat($("#prod-"+id).attr("data-mrp"));
    var srp = parseFloat($("#subamt-price-" + id).val());
    var qty = parseFloat($("#subqty-edit-" + id).val());
    var price = parseFloat($("#subamt-edit-" + id).val());
  
    if(mrp > 0){
        if(mrp > (price/qty)){
            $("#subamt-edit-" + id).val(parseFloat(mrp*qty).toFixed(2));
        }else{
            $("#subamt-edit-" + id).val(parseFloat($("#subamt-edit-" + id).val()).toFixed(2));        
        } 
    }else{
        if(srp > (price/qty)){
            $("#subamt-edit-" + id).val(parseFloat(srp*qty).toFixed(2));
        }else{
            $("#subamt-edit-" + id).val(parseFloat($("#subamt-edit-" + id).val()).toFixed(2));        
        }      
    }

}

function processOrder(id) {
    if(!confirm('This will process the order and create an invoice for the selected order. Proceed?')){
        return false;
    }

    showLoadingModal("Processing... Please wait.....");
    $.getJSON('/merchants/'+id+'/process_order',null, function (data) {
        if (data['message'] == 'success') {
            alert("Invoice Created!");
            closeLoadingModal();
            window.location.reload();
        } else {
            alert(data['message']);
            closeLoadingModal();
        }
    });
}

function load_workflows(){ 
    $.getJSON('/merchants/workflow_data', null, function(data) {  
        var oTable = $('#workflow-list').dataTable( {"bRetrieve": true,"order": [[ 2, "asc" ], [ 3, "asc" ]]} );
        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }
        $('#workflow-list').DataTable().columns.adjust().responsive.recalc();
    });    
}

window.load_workflows = load_workflows;

window.deleteRow = deleteRow;
window.validate_numeric_input = validate_numeric_input;
window.computeTotal = computeTotal;
window.showOrder = showOrder;
window.computeTotalEdit = computeTotalEdit;
window.SendEmail = SendEmail;
window.SendWelcomeEmail = SendWelcomeEmail;
window.updateFormatEdit = updateFormatEdit;
window.updateFormat = updateFormat;
window.processOrder = processOrder;