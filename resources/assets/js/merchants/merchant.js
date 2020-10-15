$('.datatables').dataTable();

$(document).ready(function() {

    $("input[type='text']").attr('maxLength','50');
	
    $('#txtPaymentType').change(function (){
        var ach = document.getElementById("txtPaymentType");
        var ach_selectedText = ach.options[ach.selectedIndex].text;
        if(ach_selectedText=="ACH"){
            $('#divACH').show();
        } else {
            $('#divACH').hide();
        }
    });
    $('#txtPaymentType').trigger('change');

    $('#btnSavePaymentType').click(function () {
        var payment_type = document.getElementById("txtPaymentType");
        var payment_type_selectedText = payment_type.options[payment_type.selectedIndex].text;
        var payment_type_selectedValue = payment_type.options[payment_type.selectedIndex].value;
        if (payment_type_selectedValue==1){ //ACH  
            if ($('#txtBankName').val()==""){
                alert('Please input bank name');
                return false    
            }
            if ($('#txtRoutingNumber').val()==""){
                alert('Please input routing number');
                return false    
            }
            if ($('#txtBankAccountNumber').val()==""){
                alert('Please input bank account number');
                return false    
            }
        }
          
    }); 
});


function validateField(element,msg)
{
    if($('#'+element).val().trim() == ""){
        document.getElementById(element).style.borderColor = "red";
        alert(msg);
        return false;
    }else{
        document.getElementById(element).style.removeProperty('border');
        return true;
    }            
}  

function isValidDateEx(dateString)
{
    // First check for the pattern
    if(!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
        return false;

    // Parse the date parts to integers
    var parts = dateString.split("/");
    var day = parseInt(parts[1], 10);
    var month = parseInt(parts[0], 10);
    var year = parseInt(parts[2], 10);

    // Check the ranges of month and year
    if(year < 1000 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
};



function createPayment()
{
    $('#paymentMethodId').val(-1);
    $('#txtPaymentType').val(1);
    $('#txtBankName').val('');
    $('#txtRoutingNumber').val('');
    $('#txtBankAccountNumber').val('');
    $('#chkSetAsDefault').prop('checked', false); 
    $('#btnSavePaymentType').val('Create Payment');
    $('#txtPaymentType').trigger('change');
    $('#goetu-billing').modal('show'); 
}

function editPayment(id)
{
    $.getJSON('/merchants/details/'+id+'/payment_method', null, function(data) {  
        $('#paymentMethodId').val(data.id);
        $('#txtBankName').val(data.bank_name);
        $('#txtPaymentType').val(data.payment_type_id);
        $('#txtRoutingNumber').val(data.routing_number);
        $('#txtBankAccountNumber').val(data.bank_account_number);
        if(data.is_default_payment===1)
        {
            $('#chkSetAsDefault').prop('checked', true); 
        } else {
            $('#chkSetAsDefault').prop('checked', false);     
        }
        $('#btnSavePaymentType').val('Update Payment');
        $('#txtPaymentType').trigger('change');
        $('#goetu-billing').modal('show'); 
    });
   
}

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

window.createPayment = createPayment;
window.editPayment = editPayment;
window.isNumberKey = isNumberKey;