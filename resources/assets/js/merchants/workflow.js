import swal from 'sweetalert2'

$(document).ready(function() {
    

    $('.assignees').tokenize2({searchFromStart: false});
    $('.dueDate').datetimepicker({ 'format': 'MM/DD/YYYY' });

    $('.inputfile' ).each( function(){
        var $input   = $( this ),
            $label   = $input.next( 'label' ),
            labelVal = $label.html();

        $input.on( 'change', function( e )
        {
            var fileName = '';

            if( this.files && this.files.length > 1 )
            {    // fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( 'count', this.files.length );
                for (var i = 0, len = this.files.length; i < len; i++) {
                  fileName = fileName+this.files[i].name+', ';
                }
                fileName = fileName.substring(0, fileName.length - 2);
            }
            else if( e.target.value )
                fileName = e.target.value.split( '\\' ).pop();

            if( fileName )
                $label.find( 'span' ).html( fileName );
            else
                $label.html( labelVal );
        });

        // Firefox bug fix
        $input
        .on( 'focus', function(){ $input.addClass( 'has-focus' ); })
        .on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
    });

    $(document).on('click', '.EditSubtask', function (e){
        e.preventDefault();
        var obj = $(this).attr('data-subid');
        $('.subtaskcontent-edit'+obj).show();
        $('.subtaskcontent'+obj).hide();
    });

    $(document).on('click', '.cancelChanges', function (e){
        e.preventDefault();
        var obj = $(this).attr('data-subid');
        $('.subtaskcontent-edit'+obj).hide();
        $('.subtaskcontent'+obj).show();
    });

    $(document).on('click', '.saveSubTask', function (e) {
        
        e.preventDefault();
        var obj = $(this).attr('data-subid');
        var assignees = $('#stassign-'+obj).val();

       if ($('#txtSubTaskName-'+obj).val() ==""){
            alert("Please input sub task name.");
            return false;
        }
          
        if ($('#txtDueDate-'+obj).val()==""){
            alert("Please input sub task due date.");
            return false;
        }  

        if ($('#stassign-' + obj).data('is_hidden') == '') {
            if (assignees==null){
                alert("Please input at least one assignee.");
                return false;
            } 
        }
        

        $('#txtTaskLineNo').val(obj);
        $('#txtTaskNo').val($('#txtSubTaskNo-'+obj).val());
        $('#txtTaskName').val($('#txtSubTaskName-'+obj).val());
        $('#txtTaskAssignee').val(assignees);
        $('#txtDueOn').val($('#txtDueDate-'+obj).val());
        // $('#txtTaskLink').val($('#txtSubTaskLink-'+obj).val());
        // $('#txtTaskLinkText').val($('#txtSubTaskLinkText-'+obj).val());
        var postdata = $("#frmWorkflow").serializeArray();
        postdata.push({
            name : 'department_id', 
            value : 
                $(this).parent()
                .parent()
                .parent()
                .find($('select[name="department_id"]'))
                .val()
        })

        $.postJSON("/merchants/update_subtask", postdata, function(data) {    
            if(data.success)
            {
                location.reload(true)
                $('#taskUsers-'+obj).html('&nbsp;&nbsp;'+data.user);
                $('#taskName-'+obj).html($('#txtTaskName').val());
                $('#taskDue-'+obj).html('Due Date:&nbsp;'+$('#txtDueOn').val());
                $('#taskDueDate').html(data.due);
                $('.subtaskcontent-edit'+obj).hide();
                $('.subtaskcontent'+obj).show();
            } 
        }); 

    });

    $('#addSubtask').on('click', function(e){
        var obj = $('#txtNewLineNo').val();
        var assignees = $('#stassign-'+obj).val();

        if ($('#txtSubTaskName-'+obj).val() ==""){
            alert("Please input Assignment.");
            return false;
        }
          
        if ($('#txtDueDate-'+obj).val()==""){
            alert("Please input due date.");
            return false;
        }  

        $('#txtTaskName').val($('#txtSubTaskName-'+obj).val());
        $('#txtTaskAssignee').val(assignees);
        $('#txtDueOn').val($('#txtDueDate-'+obj).val());

        var postdata = $("#frmWorkflow").serializeArray();
        postdata.push({
            name : 'department_id', 
            value : $('#newSubTaskDepartmentId').val()
        })

        $.postJSON("/merchants/add_subtask", postdata, function(data) {    
            if (data.success) {
                location.reload(true);
            } else {
                alert(data.msg);
            }
        }); 

    });

    $('#btnAddNewTask').on('click', function(e){
        e.preventDefault();
        $('.subtask-option2').hide();
        $('.addsubtask-option').show();
        var subTctr = $('.subtask').length + 1;
        var maxTaskNo =0;
        var currTaskNo=0;
        $('.subtask').each(function(k,v){
            currTaskNo = $(this).find('.subtasknum').attr('value');
           if(parseFloat(maxTaskNo) < parseFloat(currTaskNo))
           {
                maxTaskNo = $(this).find('.subtasknum').attr('value');
           }
        });
        maxTaskNo++;

        let departmentOptions = ''
        departments.forEach((department) => {
            let isHead = department.head_id == $('input[name="user_id"]').val()
            departmentOptions += 
                '<option value="' + department.id  + '" data-is_head="' + (isHead ? 1 : 0)  + '">' + 
                    department.description + 
                '</option>' 
        })

        $('#subtask-wrapper').append('<div class="subtask newsubtask" id="subtask'+subTctr+'">'+
            '<div class="row">'+
                '<div class="col-sm-1 ta-right">'+
                    '<h5 class="subtasknum" value="'+maxTaskNo+'">#'+maxTaskNo+'</h5>'+
                '</div>'+
                '<div class="col-sm-11 subtaskcontent-edit'+subTctr+'">' +
                    '<input type="hidden" id="txtSubTaskNo-'+subTctr+'" name="txtSubTaskNo-'+subTctr+'" value="'+maxTaskNo+'">' +
                    '<input type="hidden" id="txtNewLineNo" name="txtNewLineNo" value="'+subTctr+'">' +
                    '<div class="form-group">' +
                        '<div class="input-group">' +
                            '<div class="input-group-addon">Assignment:</div>' +
                            '<input type="text" class="form-control" id="txtSubTaskName-'+subTctr+'" name="txtSubTaskName-'+subTctr+'" value="">' +
                        '</div>'+
                    '</div>'+
                    '<div class="row">' +
                        '<div class="form-group-col-sm-4">' +
                            '<div class="form-group">' +
                                '<label>Department:</label>' +
                                '<select id="newSubTaskDepartmentId" name="department_id" class="form-control">' +
                                    '<option value="-1">--Select Department--</option>' +
                                    departmentOptions +
                                '</select>' +
                            '</div>' +
                        '</div>' +
                        '<div id="newSubTaskAssigneeIds" class="form-group col-sm-4">' +
                            '<div class="form-group">' +
                                '<label>Assignees:</label>' +
                                '<select class="form-control assignees" id="stassign-'+subTctr+'" multiple>' +
                                '</select>' +
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-sm-2 custom-input-15">' +
                            '<label>Specify Due On:</label>' +
                            '<input type="text" class="form-control custom-input-8 dueDate" value="" id="txtDueDate-'+subTctr+'" name="txtDueDate-'+subTctr+'">' +
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>');

        $('.dueDate').datetimepicker({ 'format': 'MM/DD/YYYY' });
        $('.assignees').tokenize2({searchFromStart: false});

        $.getJSON('/products/template/workflow/get_user_sub_products/'+$('#txtPID').val(), null, function(data) {  
            var arr_assignees=data['users'];
            var select = $('#stassign-'+subTctr);
            $('#stassign'+subTctr).empty(); 
            for(var i = 0; i <arr_assignees.length; i++)
            {
              select.append('<option value="'+arr_assignees[i]['id']+'">'+arr_assignees[i]['name']+'</option>');
            }
        });

        $('#newSubTaskDepartmentId').trigger('change')
    });

    $(document).on('change', '#newSubTaskDepartmentId', function() {
        let selectedOption = $(this).find("option:selected");
        let condition = 
            !(selectedOption.data('is_head')) && 
            $('input[name="hasAssignWorkflow"]').val() == '0'
    
        if (condition) {
            $('#newSubTaskAssigneeIds').hide()
        } else {
            $('#newSubTaskAssigneeIds').show()
        }
    })

    $(document).on('click', '#cancelSubtask', function (e){
        e.preventDefault();
        $('.newsubtask').remove();
        $('.addsubtask-option').hide();
        $('.subtask-option2').show();
    });

    $(document).on('click', '.subtaskstatus', function (e){
        var taskNo = $(this).attr('data-taskNo');
        $('#txtTaskNo').val(taskNo);

        swal({
            title: 'Mark as',
            input: 'select',
            inputOptions: {
                'T': 'Todo',
                'I': 'In Progress',
                'P': 'Pending',
                'C': 'Completed'
            },
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    resolve()
                })
            },
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#808080',
            confirmButtonText: 'Submit',
            cancelButtonText: 'Close'

        }).then((result) => {
            if (result.value) {
                $('#txtSubTaskStatus').val(result.value);

                var postdata = $("#frmWorkflow").serialize();        
                $.postJSON("/merchants/update_subtask_status", postdata, function(data) {    
                    if (!data.success) {
                        alert(data.msg);
                    } else {
                        location.reload(true);
                    }  
                });
            } else {
                $(this).prop('checked', false); 
            }
        })
    });

    $(document).on('click', '.DelSubtask', function (e){
        var taskNo = $(this).attr('data-taskNo');
        $('#txtTaskNo').val(taskNo);

        swal({
            title: 'Cancel?',
            text: "This will cancel Task #" + taskNo,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#808080',
            confirmButtonText: 'Proceed',
            cancelButtonText: 'Close'
        }).then((result) => {
            if (result.value) {
                $('#txtSubTaskStatus').val('V');

                var postdata = $("#frmWorkflow").serialize();        
                $.postJSON("/merchants/update_subtask_status", postdata, function(data) {    
                    if (!data.success) {
                        alert(data.msg);
                    } else{
                        location.reload(true);
                    }  
                });
            }
        })
    });


   $('#txtVisibility').change(function () {
        if($(this).val()=="private")
        {
            $('#controls-assignee').show();
        } else {
            $('#controls-assignee').hide();    
        }
        //
    });
   $('#txtVisibility').trigger('change');

   $(document).on('click', '.visibility-sub', function (e){
        if($(this).val()=="private")
        {
            $('#controls-assignee-sub-'+this.id).show();
        } else {
            $('#controls-assignee-sub-'+this.id).hide();    
        }
        
    });
   $('.visibility').trigger('change');

    $(document).on('click', '.markAllTaskAsComplete', function (e){
        if ( $(this).is(':checked') ) {

            swal({
                title: 'Proceed?',
                text: "This will mark all task as completed.",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#808080',
                confirmButtonText: 'Proceed',
                cancelButtonText: 'Close'
            }).then((result) => {
                if (result.value) {
                    $("#form-mark-all").submit()
                } else {
                    $(this).prop('checked', false);
                }
            })

        }
    });
   
    $('.remove-link').on('click', function() {
        let listItem = $(this).parent()
        let ticketHeaderId = $(this).data('ticket_header_id')

        swal({
            title: 'Are you sure?',
            text: "This will unlink Ticket #" + ticketHeaderId + ' to this task',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, unlink it!'
        }).then((result) => {
            /** @todo Show loading */

            let formData = new FormData();
            formData.append('ticket_header_id', ticketHeaderId);

            axios.post('/merchants/unlinkTaskToTicket', formData)
                .then(response => {
                    /** @todo Hide Loading */

                    listItem.remove()

                    if ($('#ticket-section').find('li').length == 0) {
                        $('#ticket-section').remove()
                    }
                })
                .catch(error => {
                    /** @todo Hide Loading */

                    swal({
                        type: 'error',
                        title: "Something's not Right",
                        text: 'There was an error processing your request. Please try again later',
                        animation: true,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        position: "center"
                    })
                })
        })
    })

});

jQuery.extend({
   postJSON: function( url, data, callback) {
      return jQuery.post(url, data, callback, "json");
   }
});
