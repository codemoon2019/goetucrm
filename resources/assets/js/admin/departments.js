$( document ).ready(function() {
    $('.acl-cb').change(function (){
      var access='';
      $('.acl-cb:checkbox:checked').each(function () {
           access = access +  $(this).val() + ",";
      });
      $('input[name="access"]').val(access);
    });
    $('.acl-cb').trigger('change'); 
    
    $('.product-cb').change(function (){
      var products='';
      $('.product-cb:checkbox:checked').each(function () {
           products = products +  $(this).val() + ",";
      });
      $('input[name="products"]').val(products);
    });
    $('.product-cb').trigger('change'); 

    loadDepartment();

    $('#system-group-table').DataTable({
      "lengthMenu": [25, 50, 75, 100 ],
        serverSide: true,
        processing: true,
        ajax: '/admin/departments/system_group_data',
        columns: [
          {data: 'description'},
          {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });  

    $("#cbCheckAllACL").click(function(){
      $('.acl-cb').not(this).prop('checked', this.checked);
      var access='';
      $('.acl-cb:checkbox:checked').each(function () {
           access = access +  $(this).val() + ",";
      });
      $('input[name="access"]').val(access);
    });

    $("#cbCheckAllProducts").click(function(){
      $('.product-cb').not(this).prop('checked', this.checked);
      var products='';
      $('.product-cb:checkbox:checked').each(function () {
           products = products +  $(this).val() + ",";
      });
      $('input[name="products"]').val(products);
    });

    $('.department-cb').change(function (){
	    var department='';
        if($(this).prop('checked')){
            $('.sys-dept-cb').prop('checked',false);
        }
	    $('.department-cb:checkbox:checked').each(function () {
	         department = department +  $(this).val() + ",";
	    });
	    $('input[name="departments"]').val(department);
	});
  	
  	$('.department-cb').trigger('change'); 

  	$('.adv-department-cb').change(function (){
	    var department='';
	    $('.adv-department-cb:checkbox:checked').each(function () {
	         department = department +  $(this).val() + ",";
	    });
	    $('input[name="advance_department_id"]').val(department);
	});

    $('#company-op').change(function (){
        id = $(this).val();
        $('.adv-department-cb').prop('checked',false);
        $('input[name="advance_company_id"]').val(id);
        $('.department-li').hide();
        $('.department-li-'+id).show();
    });

    $('#company-op').trigger('change'); 


    $('#loadTemplate').click(function (){
        $('#selectTemplate').modal('show');
    });

    $('#tblTemplateList').dataTable({"lengthMenu": [25, 50, 75, 100 ],});
        
});

  function showPermission(id){
      $('#sub-'+id).show();
      $('#main-'+id).hide();
  }

  function hidePermission(id){
      $('#sub-'+id).hide();
      $('#main-'+id).show();            
  }

  function collapsePermission(){
      if($('#showAll').prop('checked')){
          $('.sub-tr').show();
          $('.main-tr').hide();  
      }else{
          $('.sub-tr').hide();
          $('.main-tr').show();  
      }
    
  }

  function loadPointPersonData(){
       $('#pointPerson').prop('disabled', true);
      $.getJSON('/admin/departments/department_lead_data/'+$('#depHead').val(), null, function(data) {  
          $('#pointPerson').empty(); 
          if(data.success)
          {
              var newOption = $(data.data);
              $('#pointPerson').append(newOption);                
          }else{
              alert(data.message);
          }
           $('#pointPerson').prop('disabled', false);
      });  
  }


  function loadDepartmentData(){
       $('#depHead').prop('disabled', true);
       $('#division').prop('disabled', true);
      $.getJSON('/admin/departments/company_department_data/'+$('#company').val(), null, function(data) {  
          $('#depHead').empty();
          $('#division').empty();  
          if(data.success)
          {
              var newOption = $(data.data);
              $('#depHead').append(newOption);   
              var newOption = $(data.data2);
              $('#division').append(newOption);

              $('#tblTemplateList').dataTable().fnDestroy();
              var oTable = $('#tblTemplateList').dataTable({
                  "lengthMenu": [25, 50, 75, 100 ],
                  "bRetrieve": true
              });

              oTable.fnClearTable();
              if (data.data3.length >0){
                  oTable.fnAddData(data.data3);    
              }
              $('#tblTemplateList').DataTable().columns.adjust().responsive.recalc();

          }else{
              alert(data.message);
          }

           $('#depHead').prop('disabled', false);
           $('#division').prop('disabled', false);
          // loadPointPersonData();
      });  
  }

function advanceSearchDepartments(){
	if ($('#advance_department_id').val()==""){
	 	$('#advance_department_id').val(-1);
    }

	$("#departments-table").dataTable().fnDestroy();
    $('#departments-table').DataTable({
      "lengthMenu": [25, 50, 75, 100 ],
        serverSide: true,
        processing: true,
        ajax: {
            url: '/admin/departments/department_data',
            data: function(data) {
                data.companyId = $("input[name='advance_company_id']").val()
                data.departmentId = $("input[name='advance_department_id']").val()
            }
        },
        columns: [
            {data: 'color'},
            {data: 'division'},
            {data: 'description'},
            {data: 'head'},
            {data: 'company'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    }); 
    $('.adv-close').click();
}

function loadACLTemplate(ids){
    $('.acl-cb').not(this).prop('checked', false);
    var access = ids.split(',');
    var i;
    for (i = 0; i < access.length; i++) { 
      $('#acl-'+access[i]).not(this).prop('checked', true);
    }
    
    acl='';
    $('.acl-cb:checkbox:checked').each(function () {
       acl = acl +  $(this).val() + ",";
    });
    $('input[name="access"]').val(acl);
    $('#selectTemplate').modal('hide');
}  


function loadDepartment(){
  $('#departments-table').dataTable().fnDestroy();
  $('#departments-table').DataTable({
    "lengthMenu": [25, 50, 75, 100 ],
      serverSide: true,
      processing: true,
      ajax: '/admin/departments/department_data',
      columns: [
        {data: 'color'},
        {data: 'division'},
        {data: 'description'},
        {data: 'head'},
        {data: 'company'},
        {data: 'action', name: 'action', orderable: false, searchable: false}
      ]
  }); 
}

function deleteDepartment(id){
    if (confirm('Delete this department?')) {
        var formData = {
            id: id
        };

        $.ajax({
            type:'GET',
            url:'/admin/departmentDelete',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    alert(data.msg);
                    loadDepartment();
                }else {
                    alert(data.msg);
                }
            }
        });
    }else {
        return false;
    }
}

window.showPermission = showPermission;
window.hidePermission = hidePermission;
window.collapsePermission = collapsePermission;
window.loadDepartmentData = loadDepartmentData;
window.loadPointPersonData = loadPointPersonData;
window.advanceSearchDepartments = advanceSearchDepartments;
window.loadACLTemplate = loadACLTemplate;
window.loadDepartment = loadDepartment;
window.deleteDepartment = deleteDepartment;