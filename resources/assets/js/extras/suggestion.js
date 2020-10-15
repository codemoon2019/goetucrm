import swal from "sweetalert2";
$(document).ready(function() {
    $(".suggest-star").on('click', function ()  {
      //detect type
      var id = $(this).attr('id');   
      var $this = $(this).find("a > i");
      var div_id = $(this).closest(".tab-pane").attr("id");

      var is_starred = 0;
      if ($this.hasClass("fa-star-o")){
        is_starred = 1            
      }

      var data = 'id='+id+'&is_starred='+is_starred;
        swal({
            title: 'Updating...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
              swal.showLoading();
            }
        })
      $.ajax({
          type:'GET',
          url:'/admin/suggestions/updateStarred',
          data:data,
          dataType:'json',
          success:function(data){
              window.location.href = APP_URL+'/admin/suggestions?tab='+div_id;        
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
            swal("Warning", 'Please select a suggestion!', "warning");
            return false;
        }


        swal({
            title: 'Updating...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
              swal.showLoading();
            }
        })

        $.ajax({
            type: 'POST',
            url: '/admin/suggestions/updateAsRead',
            data: formData,
            dataType: 'json',
            success: function (data) {
                swal.close();
                if (data.success) {
                    swal("Success", data.msg, "success");
                    window.location.href = APP_URL+'/admin/suggestions';  
                } else {
                    swal("Warning", data.msg, "warning");
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
            swal("Warning", 'Please select a suggestion!', "warning");
            return false;
        }


        swal({
            title: 'Updating...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
              swal.showLoading();
            }
        })

        $.ajax({
            type: 'POST',
            url: '/admin/suggestions/updateAsUnread',
            data: formData,
            dataType: 'json',
            success: function (data) {
                swal.close();
                if (data.success) {
                    swal("Success", data.msg, "success");
                    window.location.href = APP_URL+'/admin/suggestions';   
                } else {
                    swal("Warning", data.msg, "warning");
                }
            }
        });
    });


    $("#btnSubmitSuggestion").click(function() {
        if($('#suggestionTitle').val() == ""){
            swal("Warning", 'Title is required', "warning");
            return false;
        }
        if($('#suggestionDescription').val() == ""){
            swal("Warning", 'Description is required', "warning");
            return false;
        }    

        var formData = new FormData( $("#frmSuggestionBox")[0] );

        swal({
            title: 'Submitting...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
              swal.showLoading();
            }
        })

        $.ajax({
            type: "POST",
            url: '/extras/suggestion',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                swal.close();
                if (data.success) {
                  swal("Success", data.message, "success");
                } else {
                  swal("Warning", data.message, "warning");
                }
            },
        });
        $('#suggestionTitle').val('');
        $('#suggestionDescription').val('');
        $('#modalSuggestion').modal('hide'); 
    });

    $('#modalSuggestionPreview').on('hidden.bs.modal', function () {
        if($('#divMenu').parent().find('.new').hasClass('active'))
        {
            location.reload();
        }

    })

 });

function showContent(id){
    swal({
        title: 'Loading...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        onOpen: () => {
          swal.showLoading();
        }
    });
    $.getJSON('/admin/suggestions/getInfo/'+id, null, function(data) { 
        swal.close();
        if (data.success) { 
            $('#suggestionTitlePreview').val(data.title);
            $('#suggestionDescriptionPreview').val(data.description);
            $('#modalSuggestionPreview').modal('show'); 
        }else{
            swal("Warning", data.msg, "warning");
        }

    });

}

window.showContent = showContent;