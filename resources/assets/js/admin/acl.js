$( document ).ready(function() {
	$('#module_category').change(function (){
        var resource_group = document.getElementById("module_category");
        var resource_group_selectedText = resource_group.options[resource_group.selectedIndex].text;
        var resource_group_selectedValue = resource_group.options[resource_group.selectedIndex].value;
        var module_access_id =  $('input[name="module_access_id"]').val();
		    var acl_url = '/admin/acl/get_resource_group_access/'+resource_group_selectedValue;	
        $.ajax({
          url: acl_url,
        }).done(function(items){
          let option ="";
          $.each(items, function(key, item){
             option += '<option value="' + item.id +  '">' + item.name  + '</option> ';
          });
          $('#module_access').empty(); //remove all child nodes
	      var newOption = option;
	      $('#module_access').append(newOption);
	      $('#module_access').trigger("chosen:updated");  
        if (module_access_id !=""){
           $('#module_access').val(module_access_id)
        }

        });
    });
    $('#module_category').trigger('change'); 

    loadACL();

    $('#departments-table').DataTable({
       "lengthMenu": [25, 50, 75, 100 ],
        serverSide: true,
        processing: true,
        ajax: '/admin/departments/department_data',
        columns: [
          {data: 'description'},
          {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });  
        
});


function loadACL(){
  $('#acl-table').dataTable().fnDestroy();
    $('#acl-table').DataTable({
       "lengthMenu": [25, 50, 75, 100 ],
          serverSide: true,
          processing: true,
          ajax: '/admin/acl/data',
          columns: [
              {data: 'name'},
              {data: 'description'},
              {data: 'resource'},
              {data: 'action', name: 'action', orderable: false, searchable: false}
          ]
      });
}

function deleteACL(id){
    if (confirm('Delete this ACL?')) {
        var formData = {
            id: id
        };

        $.ajax({
            type:'GET',
            url:'/admin/ACLDelete',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    alert(data.msg);
                    loadACL();
                }else {
                    alert(data.msg);
                }
            }
        });
    }else {
        return false;
    }
}

window.loadACL = loadACL;
window.deleteACL = deleteACL;