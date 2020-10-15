$( document ).ready(function() {

  loadProductFeeList();
  loadWorkflowList();
  loadEmailList();

});

function loadProductFeeList(){
    $('#productfee-table').dataTable().fnDestroy();
    $('#productfee-table').DataTable({
          serverSide: true,
          processing: true,
          ajax: '/products/listTemplate/productfee_data',
          columns: [
              {data: 'name'},
              {data: 'description'},
              {data: 'type'},
              {data: 'company'},
              {data: 'action', name: 'action', orderable: false, searchable: false}
          ]
      });
}

function loadWorkflowList(){
    $('#workflow-table').dataTable().fnDestroy();
    $('#workflow-table').DataTable({
          serverSide: true,
          processing: true,
          ajax: '/products/listTemplate/workflow_data',
          columns: [
              {data: 'name'},
              {data: 'description'},
              {data: 'product'},
              {data: 'action', name: 'action', orderable: false, searchable: false}
          ]
      });
}

function loadEmailList(){
    $('#wemail-table').dataTable().fnDestroy();
    $('#wemail-table').DataTable({
          serverSide: true,
          processing: true,
          ajax: '/products/listTemplate/wemail_data',
          columns: [
              {data: 'name'},
              {data: 'product'},
              {data: 'action', name: 'action', orderable: false, searchable: false}
          ]
      });
}

function deleteProductFee(id){
    if (confirm('Delete this product fee?')) {
        var formData = {
            id: id
        };

        $.ajax({
            type:'GET',
            url:'/products/template/deleteProductFee',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                        // window.location.href = window.location.href;
                    }
                    loadProductFeeList();
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

function deleteWorkFlow(id){
    if (confirm('Delete this workflow?')) {
        var formData = {
            id: id
        };

        $.ajax({
            type:'GET',
            url:'/products/template/deleteWorkflow',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                        // window.location.href = window.location.href;
                    }
                    loadWorkflowList();
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

function deleteWemail(id){
    if (confirm('Delete this welcome email?')) {
        var formData = {
            id: id
        };

        $.ajax({
            type:'GET',
            url:'/products/template/deleteWemail',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                        // window.location.href = window.location.href;
                    }
                    loadEmailList();
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

window.loadProductFeeList = loadProductFeeList;
window.loadWorkflowList = loadWorkflowList;
window.loadEmailList = loadEmailList;
window.deleteProductFee = deleteProductFee;
window.deleteWorkFlow = deleteWorkFlow;
window.deleteWemail = deleteWemail;
