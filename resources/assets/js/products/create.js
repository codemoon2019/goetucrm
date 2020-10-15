$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        }
    });

    $('.datatables').dataTable();

    let processing = false;
    $('#saveMainProduct').click(function(e){
        e.preventDefault();
        if (processing) {
            return false;
        }

        processing = true;

        if($('#txtProductName').val().trim()==""){
            alert("Enter your product name.");
            processing = false;
            return false;
        }
        if($('#txtProductDescription').val().trim()==""){
            alert("Enter your product description.");
            processing = false;
            return false;   
        }

        $("#txtSumBuyRate").val(0);

        $singleSelect = $('#togSingleSelection').is(":checked") ? 1 : 0;

        if($('#crtMainPrdCat').hasClass('hide')){
            var formData = {
                txtProductName:         $('#txtProductName').val(),
                txtProductDescription:  $('#txtProductDescription').val(),
                txtProductType:         $('#txtProductType').val(),
                txtProductOwner:        $('#txtOwnerID').val(),
                txtSumBuyRate:          $("#txtSumBuyRate").val(),
                txtSingleSelect:        $singleSelect,
            };

            var formData = new FormData();
            formData.append('txtProductName', $('#txtProductName').val())
            formData.append('txtProductDescription', $('#txtProductDescription').val())
            formData.append('txtProductType', $('#txtProductType').val())
            formData.append('txtProductOwner', $('#txtOwnerID').val())
            formData.append('txtSumBuyRate', $('#txtSumBuyRate').val())
            formData.append('txtSingleSelect', $singleSelect)
            formData.append('fileProductImage', $('input[type=file]')[0].files[0])

            $.ajax({
                type:'POST',
                url:'/products/createProduct',
                data:formData,
                dataType:'json',
                cache: false,
                contentType: false,
                processData: false,
                success:function(data){
                    if (data.success) {
                        if ($(".alert.alert-success").hasClass('hide')) {
                            $(".alert.alert-success").removeClass('hide');
                            $("p#msg-success").html(data.msg);

                            $("#txtProductCatID").val(data.last_insert_id);
                            window.location.href = '/products/edit/'+data.last_insert_id;
                            processing = false;
                        }
                    }else {
                        if ($(".alert.alert-danger").hasClass('hide')) {
                            $(".alert.alert-danger").removeClass('hide');
                            $("p#msg-danger").html(data.msg);
                            window.location.href = window.location.href;
                            processing = false;
                        }
                    }
                }
            });
        }
        return false;
    });
});