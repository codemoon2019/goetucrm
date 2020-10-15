import swal from "sweetalert2";

$(document).ready(function () {
    // $('.dataPicker').datetimepicker({
    //     format: 'MM/DD/YYYY'
    // });
    $('.datatables').dataTable();
    $('#invoice-list').DataTable( {
        "order": [[ 0, "desc" ],[ 1, "desc" ]]
    } );
    $('#txtInvoiceDate').mask("99/99/9999");
    $('#txtInvoiceDueDate').mask("99/99/9999");
    $('#frmInvoiceCreate').submit(function () {
        var details = [];
        var hasItem = false;
        var itemDesc = "";
        var itemAmount = "";
        var stop = false;
        var productId = 0;

        if ($("#txtInvoiceDate").val() == "") {
            alert("Please set the Invoice Date");
            return false;
        }

        if ($("#txtInvoiceDueDate").val() == "") {
            alert("Please set the Due Date");
            return false;
        }

        if (Date.parse($('#txtInvoiceDate').val()) > Date.parse($('#txtInvoiceDueDate').val())) {
            alert("Invoice date should not be greater than due date.");
            return false;
        }

        $('#invoice_items > tbody  > tr > td > input').each(function () {
            if ($(this).attr('name') == "itemDescription") {
                if ($(this).val() == "") {
                    alert("Please fill up all the Item Description");
                    stop = true;
                    return false;
                }
                itemDesc = $(this).val();
            }

            if ($(this).attr('name') == "itemAmount") {
                itemAmount = $(this).val();
                if (!isANumber(itemAmount) || itemAmount < 0) {
                    alert("Please fill up all the Item Amount");
                    $(this).val("");
                    stop = true;
                    return false;
                } else {
                    $(this).val(parseFloat(itemAmount).toFixed(2));
                }
            }

            if ($(this).attr('name') == "itemProductId") {
                productId = $(this).val();
            }

            if (itemDesc != "" && itemAmount != "" && productId != 0) {
                hasItem = true;
                details.push({
                    description: itemDesc,
                    amount: itemAmount,
                    product_id: productId
                });
                itemDesc = "";
                itemAmount = "";
                productId = 0;
            }

        });

        if (stop) {
            return false;
        }

        if (!hasItem) {
            alert("Please input at least one item");
            return false;
        }

        $('#txtInvoiceDetailList').val(JSON.stringify(details));
        showLoadingModal("Creating Invoice.... Please wait....");
    });

    $(document).on('click', '#addInvoice', function (e) {
        e.preventDefault();
        var counter = $('#txtInvoiceDetailCount').val();
        counter++;
        var newTextBoxDiv = $(document.createElement('tr'))
            .attr("id", 'lineNo' + counter);

        newTextBoxDiv.after().html('<td class="linecount"><input class="form-control" style="height: 36px;" name="itemDescription" value=""></td>' +
            '<td><input type="text" class="form-control" placeholder="0.00" name="itemAmount" onkeypress="validate_numeric_input(event);" onchange = "InvoiceUpdateTotal(' + counter + ')" id="itemAmount' + counter + '" value=""></td>' +
            '<td class="data_center"><a href="javascript:void(0)" class="deleteInvoiceitem" onclick = "InvoiceDeleteLine(' + counter + ')" id="deleteLine' + counter + '"> <i class="fa fa-minus-circle delete" aria-hidden="true" style="margin-top:10px;"></i></a></td>' +
            '<td hidden><input type="hidden" name="itemOrderId" value="-1"></td> <td hidden><input type="hidden" name="itemProductId" value="-1"></td> <td hidden><input type="hidden" name="itemFrequencyId" value="-1"></td>');

        newTextBoxDiv.appendTo("#invoice_items");

        $('#txtInvoiceDetailCount').val(counter);
    });

});

function InvoiceUpdateTotal($id) {
    var sum = 0;
    var itemAmount = $("#itemAmount" + $id).val();
    if (!isANumber(itemAmount) || itemAmount < 0) {
        $("#itemAmount" + $id).val("0.00");
    } else {
        $("#itemAmount" + $id).val(parseFloat(itemAmount).toFixed(2));
    }

    $("input[name=itemAmount]").each(function () {
       var  value = parseFloat(this.value);
        if (this.value == "") {
            value = 0;
        }
        sum += value;
    });
    $('#invoiceTotalDue').text('$ ' + sum.toFixed(2) + ' USD');
    $('#invoiceTotal').text('$ ' + sum.toFixed(2));
    $('#txtTotalDue').val(sum.toFixed(2));
}

function InvoiceDeleteLine($id) {
    $("#lineNo" + $id).remove();
}

function isANumber(sText) {
    var ValidChars = "0123456789.";
    var IsNumber = true;
    var Char;
    var i;
    for (i = 0; i < sText.length && IsNumber == true; i++) {
        Char = sText.charAt(i);
        if (ValidChars.indexOf(Char) == -1) {
            IsNumber = false;
        }
    }
    if ($.trim(sText) == '') {
        IsNumber = false;
    }
    return IsNumber;
}

function showInvoice(id, partner_id=0) {
    if(partner_id > 0)
    {
        $('#txtPartnerId').val(partner_id);
    }
    $.getJSON('/merchants/get_invoice_details/' + id, null, function (data) {
        $('#invoice-header').html('Invoice # ' + id);
        $('#inv-date').html($('#inv-date-' + id).html());
        $('#inv-due').html($('#inv-due-' + id).html());
        $('#inv-total').html($('#inv-total-' + id).html());
        $('#inv-status').html($('#inv-status-' + id).html());
        $('#inv-payment').val(data['payment_id']);
        $('#inv-client').html(data['merchant']);
        $('#inv-amt-paid').html(data['amount_paid'] + ' USD');
        
        $('#txtPending').val(data['pending_payment']);
        $('#txtInvoiceId').val(id);
        $("#invoice-product-detail tbody tr").remove();
        if($('#inv-status-' + id).html() == "Paid"){
            $('#payment-div').hide();
        }else{
            $('#payment-div').show();
            $('#inv-amt').val(data['pending_payment']);
        }

        for (var i = 0; i < data['details'].length; i++) {
            var table = document.getElementById('invoice-product-detail');
            var sub_id = data['details'][i]['product_id'];
            var sub_cat = data['details'][i]['category'];
            var sub_name = data['details'][i]['productname'];
            var sub_amt = data['details'][i]['amount'];

            var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
            var chk = row.insertCell(0);
            var field_category = row.insertCell(1);
            var field_product_name = row.insertCell(2);
            var field_amount = row.insertCell(3);
            var pid = row.insertCell(4);

            pid.className = "table-val-edit-pid";
            pid.innerHTML = sub_id;
            pid.style.display = "none";

            row.className = "invoicedetailedit";
            row.id = "table-prod-edit-" + sub_id;
            field_product_name.className = "table-val-edit-name";
            field_product_name.innerHTML = sub_name;

            field_category.className = "table-val-edit-cat";
            field_category.innerHTML = sub_cat;

            field_amount.className = "table-val-edit-amount";
            field_amount.innerHTML = parseFloat(sub_amt).toFixed(2);

        }
        $('#show-pdf').attr('href', '/merchants/invoice/view/' + id);

        switch (data['status']) {
            case 'C':
                $('#btnVoid').hide();
                $('#btnPayNow').hide();
                $('#btnUnPay').hide();
                $('#inv-payment').attr('disabled', true);
                break;
            case 'P':
                $('#btnVoid').hide();
                $('#btnPayNow').hide();
                $('#btnUnPay').show();
                $('#inv-payment').attr('disabled', true);
                break;
            case 'S':
                $('#btnVoid').hide();
                $('#btnPayNow').hide();
                $('#btnUnPay').hide();
                $('#inv-payment').attr('disabled', true);
                break;
            default:
                $('#btnVoid').show();
                $('#btnPayNow').show();
                $('#btnUnPay').hide();
                $('#inv-payment').attr('disabled', false);
                break;
        }


        $('#invoice-header').html('Invoice # ' + id);
        $('#modalViewInvoice').modal('show');
    });
}

function voidInvoice(id) {
    showLoadingModal('Voiding Invoice..... Please Wait...');
    var postdata = $("#frmInvoiceEdit").serialize();
    $.postJSON("/merchants/void_invoice", postdata, function (data) {
        if (data.success) {
            location.reload();
        }
        closeLoadingModal();
    });

}

function payNow(status) {
    showLoadingModal('Updating Invoice Payment..... Please Wait...');
    $('#txtInvoiceStatus').val(status);

    var postdata = $("#frmInvoiceEdit").serialize();
    var email = $('#txtMerchantEmail').val();
    var id = $('#txtInvoiceId').val();
    var appUrl = document.querySelector("#ctx").getAttribute("content");
    var amount = $('#inv-amt').val();
    var pending = $('#txtPending').val();
    if(status == 'P'){
         if (!isANumber(amount) || amount < 0) {
            alert('Invalid payment amount');
            closeLoadingModal();
            return false;
        } 

        if (isNaN(amount)) {
            alert('Invalid payment amount');
            closeLoadingModal();
            return false;
        }

        if(parseFloat(amount) > parseFloat(pending)){
            alert('Payment Amount is greater than amount required');
            closeLoadingModal();
            return false;        
        }       
    }


    $.postJSON("/merchants/pay_invoice", postdata, function (data) {
        if (data.success) {
            if (email == "" && status == "P") {
                swal({
                    type: 'warning',
                    title: 'Warning',
                    text: 'Merchant cannot receive an invoice email because \
                        there was no email provided on the account.',
                    animation: true,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    position: "center",
                    footer: '<a href="' + appUrl + '/pdf/invoice_preview_' + id + '.pdf" \
                        download><i class="fa fa-download"></i>&nbsp;Download instead?</a>',
                }).then((result) => {
                    if (result.value) {
                        location.reload();
                    }
                })
            } else {
                location.reload();
            }
        }
        closeLoadingModal();
    });

}

function isValidDateEx(dateString) {
    // First check for the pattern
    if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
        return false;

    // Parse the date parts to integers
    var parts = dateString.split("/");
    var day = parseInt(parts[1], 10);
    var month = parseInt(parts[0], 10);
    var year = parseInt(parts[2], 10);

    // Check the ranges of month and year
    if (year < 1000 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    // Adjust for leap years
    if (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
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


function showRecurring(id) {

    $("#rec-detail").find("tr:gt(0)").remove();
    $('#txtFrequencyId').val(id);
    $('#recurring-header').html('Recurring Invoice');
    $('#rec-product').html($('#rec-product-' + id).html());
    $('#rec-sub-product').html($('#rec-sub-product-' + id).html());
    $('#rec-frequency').html($('#rec-frequency-' + id).html());
    $('#rec-amount').html($('#rec-amount-' + id).html() + ' USD');
        console.log($('#rec-amount-' + id).html());
    $('#recStatusSelect').html($('#rec-status-' + id).html());
    $('#txtFrequencyStatus').val($('#rec-status-' + id).html());
    if ($('#rec-status-' + id).html() == 'Active') {
        $('#btnResume').hide();
        $('#btnStop').show();
    } else {
        $('#btnResume').show();
        $('#btnStop').hide();
    }

    $.getJSON('/merchants/get_recurring_details/' + id, null, function (data) {

        $('#recStart').val(data['start_date']);
        $('#recEnd').val(data['end_date']);

        for (var i = 0; i < data['invoices'].length; i++) {
            var table = document.getElementById('rec-detail');
            var id = data['invoices'][i]['id'];
            var invdate = data['invoices'][i]['invoice_date'];
            var status = data['invoices'][i]['status'];

            var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
            var f1 = row.insertCell(0);
            var f2 = row.insertCell(1);
            var f3 = row.insertCell(2);
            var f4 = row.insertCell(3);

            f1.innerHTML = id;
            f2.innerHTML = invdate;
            if (status == 'Paid') {
                f3.innerHTML = '<span style="color:green">' + status + '</span>';
            } else {
                f3.innerHTML = '<span style="color:red">' + status + '</span>';
            }

            f4.innerHTML = '<a class="btn btn-default btn-sm" href="javascript:void(0);" onclick="showInvoice(' + id + ')">View</a>';

        }

        $('#modalViewRecurring').modal('show');
    });
}


function updateRecurring() {
    showLoadingModal('Updating Recurring Invoice..... Please Wait...');
    $('#frmRecurringEdit').submit();
}

function resumeRecurring(status) {
    showLoadingModal('Updating Recurring Invoice..... Please Wait...');
    $('#txtFrequencyStatus').val('Active');
    $('#frmRecurringEdit').submit();
}

function stopRecurring(status) {
    showLoadingModal('Updating Recurring Invoice..... Please Wait...');
    $('#txtFrequencyStatus').val('Inactive');
    $('#frmRecurringEdit').submit();
}

function updateRecurringBranch() {
    showLoadingModal('Updating Recurring Invoice..... Please Wait...');
    $('#frmRecurringBranchEdit').submit();
}

function resumeRecurringBranch(status) {
    showLoadingModal('Updating Recurring Invoice..... Please Wait...');
    $('#txtFrequencyStatus').val('Active');
    $('#frmRecurringBranchEdit').submit();
}

function stopRecurringBranch(status) {
    showLoadingModal('Updating Recurring Invoice..... Please Wait...');
    $('#txtFrequencyStatus').val('Inactive');
    $('#frmRecurringBranchEdit').submit();
}


function load_invoices(){ 
    swal({
        title: 'Invoice',
        text: 'Loading data. Please wait...',
        imageUrl: appUrl + "/images/user_img/goetu-profile.png",
        imageAlt: 'GOETU Image',
        imageHeight: 140,
        animation: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        position: "center"
    })
    $.getJSON('/merchants/details/invoices_data', null, function(data) {  
        var oTable = $('#invoice-list').dataTable( {"bRetrieve": true,"order": [[ 2, "asc" ], [ 3, "asc" ]]} );
        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }
        $('#invoice-list').DataTable().columns.adjust().responsive.recalc();
    });    
    swal.close();
}

window.load_invoices = load_invoices;


window.InvoiceUpdateTotal = InvoiceUpdateTotal;
window.InvoiceDeleteLine = InvoiceDeleteLine;
window.isANumber = isANumber;
window.showInvoice = showInvoice;
window.voidInvoice = voidInvoice;
window.payNow = payNow;
window.validate_numeric_input = validate_numeric_input;
window.showRecurring = showRecurring;
window.updateRecurring = updateRecurring;
window.resumeRecurring = resumeRecurring;
window.stopRecurring = stopRecurring;

window.updateRecurringBranch = updateRecurringBranch;
window.resumeRecurringBranch = resumeRecurringBranch;
window.stopRecurringBranch = stopRecurringBranch;

jQuery.extend({
    postJSON: function postJSON(url, data, callback) {
        return jQuery.post(url, data, callback, "json");
    }
});