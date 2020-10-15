$('.datatables').dataTable();

$('.tabs-rectangular li a').click(function(){
    var curActive = $(this).parents('.tabs-rectangular');

    // hide active view
    var curActiveId = curActive.find('li.active a').attr('id');
    $('#'+curActiveId+'Container').addClass('hide');

    // change active view
    var id = $(this).attr('id');
    $('#'+id+'Container').removeClass('hide');

    // change active tab
    curActive.find('li.active').removeClass('active');
    $(this).parent().addClass('active');
});

$( document ).ready(function() {

    $('.training-cb').change(function (){
        var access='';
        $('.training-cb:checkbox:checked').each(function () {
             access = access +  $(this).val() + ",";
        });
        $('input[name="training_access"]').val(access);
    });
    
    $('.training-cb').trigger('change');
});

function editACH(id)
{
    $.getJSON('/admin/company_settings/'+id+'/ach_info', null, function(data) {  
        $('#achID').val(data.id);
        $('#SFTPAddress').val(data.sftp_address);
        $('#SFTPUsername').val(data.sftp_user);
        $('#SFTPPassword').val(data.sftp_password);
        $('#PayTo').val(data.pay_to);
        $('#PayToken').val(data.pay_token);
    });
   $('#ach-configuration').modal('show'); 
}

window.editACH = editACH;