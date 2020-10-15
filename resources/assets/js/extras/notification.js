import swal from "sweetalert2";

$(document).ready(function() {

    $(".mailbox-star").click(function (e) {
      e.preventDefault();
      //detect type
      var id = $(this).attr('id');   
      var $this = $(this).find("a > i");
      var glyph = $this.hasClass("glyphicon");
      var fa = $this.hasClass("fa");
      var div_id = $(this).closest(".tab-pane").attr("id");
      
      //Switch states
      if (glyph) {
        $this.toggleClass("glyphicon-star");
        $this.toggleClass("glyphicon-star-empty");
      }

      if (fa) {
        $this.toggleClass("fa-star");
        $this.toggleClass("fa-star-o");
      }
      var is_starred = 1;
      if ($this.hasClass("fa-star-o")){
        is_starred = 0            
      }
      
      var data = 'id='+id+'&is_starred='+is_starred;

      $.ajax({
          type:'GET',
          url:'/extras/notification/updateStarred',
          data:data,
          dataType:'json',
          success:function(data){
              window.location.href = APP_URL+'/extras/notification?tab='+div_id;        
          }
      });
    });

    $('#allnewcb').on('change', function(e){
        $('#newNotifTbl').find('input[type="checkbox"]').each(function () {
            var $this = $(this);
            if($('#allnewcb').prop('checked')){
                $this.prop('checked',true);
            }
            else{
                $this.prop('checked',false);
            }
        });            
    });
    $('#allreadcb').on('change', function(e){
        $('#readNotifTbl').find('input[type="checkbox"]').each(function () {
            var $this = $(this);
            if($('#allreadcb').prop('checked')){
                $this.prop('checked',true);
            }
            else{
                $this.prop('checked',false);
            }
        });            
    });
    $('#btnMarkRead').click(function(){
        var formData = $('#frmUpdateInbox').serialize();
        var checkboxes = document.getElementsByName("add_to_read[]");
        var notifs = '';
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                notifs = notifs + checkboxes[i].value + ",";
            }
        }

        notifs = notifs.substr(0, notifs.length - 1);

        if (notifs == '') {
            showWarningMessage('Please select a notification!');
            return false;
        }

        showLoadingAlert();
        $.ajax({
            type: 'POST',
            url: '/extras/notification/updateAsRead',
            data: formData,
            dataType: 'json',
            success: function (data) {
                closeLoading();
                if (data.success) {
                    showSuccessMessage(data.msg);
                } else {
                    showWarningMessage(data.msg);
                }
            }
        });
    });
    $('#btnMarkUnread').click(function(){
        var formData = $('#frmUpdateUnread').serialize();
        var checkboxes = document.getElementsByName("add_to_unread[]");
        var notifs = '';
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                notifs = notifs + checkboxes[i].value + ",";
            }
        }

        notifs = notifs.substr(0, notifs.length - 1);

        if (notifs == '') {
            showWarningMessage('Please select a notification!');
            return false;
        }

        showLoadingAlert();
        $.ajax({
            type: 'POST',
            url: '/extras/notification/updateAsUnread',
            data: formData,
            dataType: 'json',
            success: function (data) {
                closeLoading();
                if (data.success) {
                    showSuccessMessage(data.msg);
                } else {
                    showWarningMessage(data.msg);
                }
            }
        });
    });
    
});

function tagAndRedirect(id, url){
    var data = 'id='+id;
    $.ajax({
        type:'GET',
        url:'/extras/notification/tagAndRedirect',
        data:data,
        dataType:'json',
        success:function(data){
            // if (url.includes('http')){
                window.location.href = url;      
            /*} else {
                window.location.href = data.webroot + url;          
            }  */        
        }
    });   
}

function showWarningMessage(msg) {
    swal("Warning", msg, "warning");
}

function showSuccessMessage(msg) {
    swal("Success", msg,"success").then((value) => {
        location.reload();
    })
}

function showLoadingAlert() {
    swal({
        title: 'Updating...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        onOpen: () => {
          swal.showLoading();
        }
    })
}

function closeLoading() {
    swal.close();
}

/*function redirect_url(url){
    var sub_domain = "registration";
    if(window.location.href.indexOf(sub_domain) > -1) {
        window.location.href = webroot_js + sub_domain + '/' + url;   
    } else {
        window.location.href = webroot_js + url;
    }
     
}*/



/*function displayValidationMessage(field_name, tabNo)
{
    CustomAlert("Enter a " + field_name);
}


function cancelReply(id)
{
    $('#divCommentPostReply'+id).hide();
    $('#addreply'+id).show();
    $('#cancelreply'+id).hide();;
}

function addReply(id)
{    
    $('#divCommentPostReply'+id).show();
    $('#addreply'+id).hide();
    $('#cancelreply'+id).show();    
}

function showAllSpecific(id)
{
    var $comparent = '#comment' +id;
    $($comparent + ' .comment-reply').show();
    $("#showall"+id).hide();
    $($comparent + ' .showless').show();
    // $('#subcomment'+id).show();
    // $("#showless"+id).show();
    // $('#showall'+id).hide();
}

function hideAllSpecific(id)
{
     var $comparent = '#comment' +id;
    $($comparent + ' .comment-reply').hide();
    $("#showall"+id).show();
    $($comparent + ' .showless').hide();
}

function showAllReplies()
{
    $('.comment-reply').show();
    $('.comment .showall').hide();
    $('.comment .showless').show();
}

function hideAllReplies()
{
    $('.comment-reply').hide();
    $('.comment .showall').show();
    $('.comment .showless').hide();    
}*/

window.tagAndRedirect = tagAndRedirect;