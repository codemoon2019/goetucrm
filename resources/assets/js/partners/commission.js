$(document).ready(function() {
    $('.datatables').dataTable();
    
    $(document).on('change','#commissionType', function(e){
        var selected = $(this).val();

        if(selected == 'none')
        {
            $("#divFixedPercentage").hide();
            $("#divPercentBased").hide();
        }

        if(selected == 'fixed')
        {
            $("#divFixedPercentage").show();
            $("#divPercentBased").hide();
        }

        if(selected == 'based')
        {
            $("#divFixedPercentage").hide();
            $("#divPercentBased").show();
        }

    });


    $('#btnSaveCommission').click(function(){
        $("#commissionBased").val('');

        var commission_type = $("#commissionType>option:selected").val()
        if(commission_type == 'based')
        {
            var commissionBased = [];
            $("#commission-case-table").find('td').each (function() {
                var cell = $(this);
                switch(cell.attr('class')) {
                    case "td-startCase":
                        startCase = $(this).find('.startCase').val();
                        break;
                    case "td-endCase":
                        endCase = $(this).find('.endCase').val();
                        break;
                    case "td-commission":
                        commission = $(this).find('.commissionCase').val();
                        break;
                    case "td-action":
                        commissionBased.push({startCase: startCase, endCase: endCase, commission: commission});
                        break;   
                    default:
                        break;
                }
            });

            commission = JSON.stringify(commissionBased);
            $("#commissionBased").val(commission);
        }

        var postdata = $("#frmCommission").serialize();
        $.postJSON("/partners/update_commission", postdata, function(data) { 
            
        });

        if(commission_type == 'none'){
            comtype = 'No Commission';
        }

        if(commission_type == 'fixed'){
            comtype = 'Fixed Percentage';
        }

        if(commission_type == 'based'){
            comtype = 'Percent based on Cases';
        }

        if($('#applyAll').val() == 'true'){
            $('.commission-'+$('#productId').val()).html(comtype);
        }else{
            $('#commission-type-'+$('#productId').val()).html(comtype);
        }
        $('#commissionAndRates').modal('hide');
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


function validate_numeric_input(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9\b]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}



function editRow(partner_id,id,name) {
    $('.modal-title').html(name);
    $('#applyAll').val('false');
    $('#commissionType').val('none');
    $('#commissionType').trigger('change');
    $('#fixedCommission').val('0.00');
    $("#commission-case-table").find("tr:gt(0)").remove();
    $('#partnerId').val(partner_id);
    $('#productId').val(id);
    $('#commissionModalLoading').show();
    $('#commissionModal').hide();
    
    $.getJSON('/partners/get_commission_detail/'+partner_id+'/'+id, null, function (data) {
        if(typeof data !== 'undefined'){
            $('#commissionType').val(data['type']);
            $('#commissionType').trigger('change');
            if(data['type'] == 'fixed'){
                $('#fixedCommission').val(data['commission_fixed']);
            }

            if(data['type'] == 'based'){
                commissions = JSON.parse(data['commission_based']);
                for (var k in commissions) {
                    table = document.getElementById('commission-case-table');
                    var row = table.insertRow(-1);
                    var startCase = row.insertCell(0);
                    startCase.className = "td-startCase";
                    var to = row.insertCell(1);
                    var endCase = row.insertCell(2);
                    endCase.className = "td-endCase";
                    var commission = row.insertCell(3);
                    commission.className = "td-commission";
                    var action = row.insertCell(4);
                    action.className = "td-action";
                    startCase.innerHTML = '<input type="text" class="form-control startCase" value="'+commissions[k].startCase+'" width="20" onkeypress="validate_numeric_input(event);">';
                    to.innerHTML = 'to';
                    endCase.innerHTML = '<input type="text" class="form-control endCase" value="'+commissions[k].endCase+'" width="20" onkeypress="validate_numeric_input(event);">';
                    commission.innerHTML = '<input type="text" class="form-control commissionCase" value="'+commissions[k].commission+'" width="20" onkeypress="validate_numeric_input(event);">';
                    action.innerHTML = '<a href="#" onclick="deleteCommissionRow(this);"><i class="fa fa-minus-circle fa-2x"></i></a>';

                }  
            }
        } 
        $('#commissionModal').show();
        $('#commissionModalLoading').hide();       
    });
    $('#commissionAndRates').modal('show');
}

function editAllSubProduct(partner_id,id,name) {
    $('.modal-title').html(name);
    $('#applyAll').val('true');
    $('#commissionType').val('none');
    $('#commissionType').trigger('change');
    $('#fixedCommission').val('0.00');
    $("#commission-case-table").find("tr:gt(0)").remove();
    $('#partnerId').val(partner_id);
    $('#productId').val(id);
    $('#commissionModal').show();
    $('#commissionModalLoading').hide();
    $('#commissionAndRates').modal('show');
}



function add_commision_case() {
    table = document.getElementById('commission-case-table');
    var row = table.insertRow(-1);
    var startCase = row.insertCell(0);
    startCase.className = "td-startCase";
    var to = row.insertCell(1);
    var endCase = row.insertCell(2);
    endCase.className = "td-endCase";
    var commission = row.insertCell(3);
    commission.className = "td-commission";
    var action = row.insertCell(4);
    action.className = "td-action";
    startCase.innerHTML = '<input type="text" class="form-control startCase" value="0" width="20" onkeypress="validate_numeric_input(event);">';
    to.innerHTML = 'to';
    endCase.innerHTML = '<input type="text" class="form-control endCase" value="0" width="20" onkeypress="validate_numeric_input(event);">';
    commission.innerHTML = '<input type="text" class="form-control commissionCase" value="0" width="20" onkeypress="validate_numeric_input(event);">';
    action.innerHTML = '<a href="#" onclick="deleteCommissionRow(this);"><i class="fa fa-minus-circle fa-2x"></i></a>';
}

function deleteCommissionRow(btn) {
  var row = btn.parentNode.parentNode;
  row.parentNode.removeChild(row);
}

jQuery.extend({
   postJSON: function( url, data, callback) {
      return jQuery.post(url, data, callback, "json");
   }
});


window.validateField = validateField;
window.validate_numeric_input = validate_numeric_input;
window.editRow = editRow;
window.editAllSubProduct = editAllSubProduct;
window.add_commision_case = add_commision_case;
window.deleteCommissionRow = deleteCommissionRow;