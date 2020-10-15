listSubtask();

$(document).ready(function() {
    $('#txtProduct').on('change',function (e) {
        $( ".subproducts" ).each(function() {
            $(this).tokenize2().trigger('tokenize:clear');
        });
        $( ".assignees" ).each(function() {
            $(this).tokenize2().trigger('tokenize:clear');
        });
        refreshAutoComplete(0)
    }); 
    sortSubtask();
})

$(document).on('change','.startsubtask', function(e){
    var id = $(this).attr('id');
    id = id.split("-");
    id = id[1];
    $('#prerequisite'+id).val(this.options[this.selectedIndex].text);
}); 


$('#btnAddSubtask').on('click', function(e){
    e.preventDefault();
    var subTctr = $('.subtask').length + 1;
    $('#subtask-wrapper').append('<div class="subtask" id="subtask'+subTctr+'">'+
                '<div class="row bordered-row subtaskborder" id="subtaskborder'+subTctr+'">'+
                    '<div class="col-md-1 text-right left-action">' +
                        '<h5 class="subtasknum">#'+subTctr+'</h5>'+
                        '<a href="#" class="btnSortSubtask text-blue" title="Sort Subtask"><i class="fa fa-sort"></i> Sort</a><br>'+
                        '<a href="#" class="btnDelSubtask text-red" data-subid="subtask'+subTctr+'" title="Delete Subtask"><i class="fa fa-minus-circle"></i> Delete</a>'+
                    '</div>' +
                    '<div class="col-md-11">' +
                        '<div class="form-group">' +
                            '<div class="input-group">' +
                                '<label class="input-group-addon">Assignment:</label>' +
                                '<input type="text" class="form-control subTaskName" id="txtSubTaskName-'+subTctr+'" name="txtSubTaskName-'+subTctr+'" value="">' +
                            '</div>' +
                        '</div>' +
                        '<div class="row">' +
                            '<div class="col-md-6 right-assignment">' +
                                '<div class="form-group">' +
                                    '<label>Department:</label>' +
                                    '<select name="department_id" class="form-control departments" id="departments-task-' + subTctr + '" required>' +
                                    '</select>' +
                                '</div>' +
                            '</div>' +

                            '<div class="col-md-6">' +
                                '<div class="form-group">' +
                                    '<label>Sub-Products:</label>' +
                                    '<select class="form-control subproducts" id="subproductTask'+subTctr+'" multiple>' +
                                    '</select>' +
                                '</div>' +
                            '</div>' +
                            '<div class="col-md-4">' +
                                '<label>Days To Complete:</label>' +
                                '<input type="hidden" class="prerequisite" id="prerequisite'+subTctr+'" name="prerequisite'+subTctr+'" value="'+subTctr+'">' +
                                '<input type="text" class="form-control daysToCompleteDetail" id="txtDaysToCompleteDetail-'+subTctr+'" name="txtDaysToCompleteDetail-'+subTctr+'" onkeypress="validate_numeric_input(event);">' +
                            '</div>' +
                            '<div class="col-md-4 subtasklink">' +
                                '<label>Starts after assignment:</label>' +
                                '<select class="form-control startsubtask" id="txtSubTaskLink-'+subTctr+'"  name="txtSubTaskLink-'+subTctr+'" data-lineno="'+subTctr+'"></select>' +
                            '</div>' +
                            '<div class="col-md-4">' +
                                '<label>Upon Completion or Start:</label>' +
                                '<select class="form-control subTaskLinkText" id="txtSubTaskLinkText-'+subTctr+'"  name="txtSubTaskLinkText-'+subTctr+'">' +
                                    '<option selected></option>'+
                                    '<option>Completion</option>'+
                                    '<option>Due Date</option>'+
                                    '<option>Start</option>'+
                                '</select>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>');

    refreshAutoComplete(subTctr);
    sortSubtask();
    listSubtask();
});
    
function sortSubtask(){
    $('#subtask-wrapper').sortable({
        connectWith: '#subtask-wrapper',
        handle: '.btnSortSubtask',
    });

    $('#subtask-wrapper').on('sortstop', function() {
        $('.subtask').each(function(k,v){
            $(this).attr('id','subtask'+(k + 1));
            $(this).find('.btnDelSubtask').attr('data-subid','subtask'+(k + 1));
            $(this).find('.subtaskborder').attr('id','subtaskborder' + (k + 1));
            $(this).find('.subTaskName').attr('id','txtSubTaskName-' + (k + 1));
            $(this).find('.subTaskName').attr('name','txtSubTaskName-' + (k + 1));
            $(this).find('.departments').attr('id','departments-task-' + (k + 1));
            $(this).find('.subproducts').attr('id','subproductTask' + (k + 1));
            $(this).find('.daysToCompleteDetail').attr('id','txtDaysToCompleteDetail-' + (k + 1));
            $(this).find('.daysToCompleteDetail').attr('name','txtDaysToCompleteDetail-' + (k + 1));
            $(this).find('.subTaskLinkText').attr('id','txtSubTaskLinkText-'+(k + 1));
            $(this).find('.subTaskLinkText').attr('name','txtSubTaskLinkText-'+(k + 1));

            $(this).find('.subtasknum').text('#' + (k + 1));
            $(this).find('.modal-btn').attr('data-id', (k + 1));
            $(this).find('.subTaskassignees').attr('id','stassign-' + (k + 1));
            $(this).find('.subTasksubproducts').attr('id','stassignprod-' + (k + 1));
            $(this).find('.startsubtask').attr('id','txtSubTaskLink-'+(k + 1));
            $(this).find('.startsubtask').attr('name','txtSubTaskLink-'+(k + 1));
            $(this).find('.startsubtask').attr('data-lineno',(k + 1));
        });
        listSubtask();
    });
}

function refreshAutoComplete(lineNo)
{
    if (lineNo == 0) {

        $('.subproducts').tokenize2();

        var product = document.getElementById("txtProduct");
        var product_selectedValue = product.options[product.selectedIndex].value;
        
        var apiURL = '/products/template/workflow/get_user_sub_products/' + product_selectedValue
        $.getJSON(apiURL, null, function(data) {  
            var arr_sub_products = data['sub_products'];
            
            var select = $('.subproducts');
            select.empty(); 
            for (var i = 0; i <arr_sub_products.length; i++) {
                select.append('<option value="' + arr_sub_products[i]['id'] + '">' + arr_sub_products[i]['name'] + '</option>');
            }

            var departmentElements = $('.departments');
            departmentElements.empty()
            departmentElements.append(
                '<option value="-1">' +
                    '--Select Department--' + 
                '</option>'
            )

            let companyId = $('#txtProduct').find(":selected").data('company_id')
            departments.forEach((department) => {
                if (department.company_id == companyId) {
                    departmentElements.append(
                        '<option value="' + department.id + '">' +
                            department.description + 
                        '</option>'
                    )
                }
            })
        }); 
    }
    else
    {
        $('#assigneeTask'+lineNo).tokenize2();
        $('#subproductTask'+lineNo).tokenize2();
        var product = document.getElementById("txtProduct");
        var product_selectedText = product.options[product.selectedIndex].text;
        var product_selectedValue = product.options[product.selectedIndex].value;
        var append_sub_product="";
        $.getJSON('/products/template/workflow/get_user_sub_products/'+product_selectedValue, null, function(data) {  
            var arr_sub_products=data['sub_products'];
            
            var select = $('#subproductTask'+lineNo);
            select.empty(); 
            for(var i = 0; i <arr_sub_products.length; i++) {
              select.append('<option value="'+arr_sub_products[i]['id']+'">'+arr_sub_products[i]['name']+'</option>');
            }

            let departmentElement = $("#departments-task-" + lineNo);
            departmentElement.empty();
            departmentElement.append(
                '<option value="-1">' +
                    '--Select Department--' + 
                '</option>'
            )

            let companyId = $('#txtProduct').find(":selected").data('company_id')
            departments.forEach((department) => {
                if (department.company_id == companyId) {
                    departmentElement.append(
                        '<option value="' + department.id + '">' +
                            department.description + 
                        '</option>'
                    )
                }
            })
        });                 
    }
    
}



function listSubtask(){
    var subTctr = $('.subtask').length;
    var prereq = "";
    if (subTctr > 0){
        setTimeout(function(){
            $('.subtasklink .startsubtask').html('');
            $('.subtasklink .startsubtask').each(function(k,v){
                $(this).append('<option selected></option>');
                for (var i=1; i<=subTctr; i++){
                    if(i != (k + 1)){
                        prereq = $('#prerequisite'+(k+1)).val();
                        if (prereq == i){
                            $(this).append('<option selected value="'+i+'">'+i+'</option>');    
                        } else {
                            $(this).append('<option value="'+i+'">'+i+'</option>');    
                        }
                    }
                }
            });
        },200);
    } else {
        return false;
    }
}

$(document).on('click','.btnDelSubtask', function(e){
    e.preventDefault();
    
    var obj = $(this).attr('data-subid');
    $('#' + obj).remove();
    
    $('.subtask').each(function(k,v){
        $(this).attr('id','subtask'+(k + 1));
        $(this).find('.btnDelSubtask').attr('data-subid','subtask'+(k + 1));
        $(this).find('.subtasknum').text('#' + (k + 1));
        $(this).find('.modal-btn').attr('data-id', (k + 1));
        $(this).find('.assignees').attr('id','assigneeTask' + (k + 1));
        $(this).find('.subproducts').attr('id','subproductTask' + (k + 1));

        $(this).find('.subtaskborder').attr('id','subtaskborder' + (k + 1));
        $(this).find('.startsubtask').attr('id','txtSubTaskLink-' + (k + 1));
        $(this).find('.subTaskLinkText').attr('id','txtSubTaskLinkText-' + (k + 1));
        $(this).find('.subTaskName').attr('id','txtSubTaskName-' + (k + 1));
        $(this).find('.daysToCompleteDetail').attr('id','txtDaysToCompleteDetail-' + (k + 1));
        $(this).find('.prerequisite').attr('id','prerequisite' + (k + 1));
        
    });
    listSubtask();
});


$('#frmWorkflowTemplate').submit(function(){
    var template = [];
    var daysToCompleteCounter = 0;
    var has_detail = false;
    var error = false;

    document.getElementById('workFlowTemplateName').style.removeProperty('border');
    document.getElementById('workFlowTemplateMainTask').style.removeProperty('border');
    document.getElementById('workFlowTemplateDescription').style.removeProperty('border');

    if ($('#workFlowTemplateName').val()==""){
        document.getElementById('workFlowTemplateName').style.borderColor = "red";
        alert("Please input Template name.");
        return false;
    }
      
    if ($('#workFlowTemplateMainTask').val()==""){
        document.getElementById('workFlowTemplateMainTask').style.borderColor = "red";
        alert("Please input main task.");
        return false;
    }  

    if ($('#workFlowTemplateDescription').val()==""){
        document.getElementById('workFlowTemplateDescription').style.borderColor = "red";
        alert("Please input description.");
        return false;
    }  
   
    $("#subtask-wrapper div").each(function(k,v){
        if (typeof $('#txtSubTaskLink-'+(k+1)).val() != 'undefined') { 

            subTaskName = $('#txtSubTaskName-'+(k+1)).val();
            department_id = $('#departments-task-'+(k+1)).val();
            subproducts = $('#subproductTask'+(k+1)).val();
            startSubTask = $('#txtSubTaskLink-'+(k+1)).val();
            startSubTaskText = $('#txtSubTaskLinkText-'+(k+1)).val();
            daysToComplete = $('#txtDaysToCompleteDetail-'+(k+1)).val();
            lineNo = $('#txtSubTaskLink-'+(k+1)).attr('data-lineno');
            document.getElementById('subtaskborder'+(k+1)).style.removeProperty('border');
            if (subTaskName==""){
                alert("Please Input Assignment Name.");
                document.getElementById('subtaskborder'+(k+1)).style.borderColor = "red";
                error=true;
                return false;
            } 

            if (subproducts=="" || subproducts == null){
                alert("Please Input Valid Sub Products.");
                document.getElementById('subtaskborder'+(k+1)).style.borderColor = "red";
                error=true;
                return false;
            }

            if (daysToComplete==""){
                alert("Please Input Days to Complete.");
                document.getElementById('subtaskborder'+(k+1)).style.borderColor = "red";
                error=true;
                return false;
            }

            daysToCompleteCounter +=  parseInt(daysToComplete);
                has_detail = true;
            template.push({subTask: subTaskName, department_id: department_id, product_tag: subproducts , dtc: daysToComplete, sst: startSubTask, sstt: startSubTaskText, lineNo: lineNo});  
            
            subTaskName = "";
            daysToComplete = "";
            assignee = "";
            product_tag = "";
        }
    });
    if(error)
    {
        return false;
    }

    if (!has_detail){
        alert("Please add atleast one sub-task and make sure to fill-up the details needed before saving.");
        return false;            
    }

    $('#txtDetailList').val(JSON.stringify(template)); 
    $('#txtDaysToCompleteH').val(daysToCompleteCounter); 

});

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

window.refreshAutoComplete = refreshAutoComplete;
window.listSubtask = listSubtask;
window.validate_numeric_input = validate_numeric_input;