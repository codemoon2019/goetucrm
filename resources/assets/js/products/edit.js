$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        }
    });

    $('#saveMainProduct').click(function(e){
        e.preventDefault();

        if($('#txtProductName').val().trim()==""){
            alert("Enter your product name.");
            return false;   
        }
        if($('#txtProductDescription').val().trim()==""){
            alert("Enter your product description.");
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

            $.ajax({
                type:'POST',
                url:'/products/createProduct',
                data:formData,
                dataType:'json',
                success:function(data){
                    if (data.success) {
                        if ($(".alert.alert-success").hasClass('hide')) {
                            $(".alert.alert-success").removeClass('hide');
                            $("p#msg-success").html(data.msg);

                            $("#txtProductCatID").val(data.last_insert_id);
                            var delay = 3000;
                            setTimeout(function() {
                                window.location.href = window.location.href;
                            }, delay);                        }
                    }else {
                        if ($(".alert.alert-danger").hasClass('hide')) {
                            $(".alert.alert-danger").removeClass('hide');
                            $("p#msg-danger").html(data.msg);
                        }
                    }
                }
            });
            
            var delay=2000; //2 second
            setTimeout(function() {
                $(".alert.alert-success").addClass('hide');
                $('#crtMainPrdCat').removeClass('hide');
                $('#crtMainPrd').addClass('hide');
                
                $('#newProductName').html($('#txtProductName').val());
                $('#newProductDescription').html($('#txtProductDescription').val());
                $('#newProductType').html($('#txtProductType').data('prodtype'));
                $('#newProductOwner').html($('#txtProductOwner').data('owner'));
            }, delay);
        }
        return false;
    });

    let processing = false;
    $('#saveCategory').click(function(e){
        e.preventDefault();
        if (processing) {
            return false;
        }

        processing = true;
        
        if($('#txtProductCatName').val().trim()==""){
            alert("Enter your category name.");
            processing = false;
            return false;   
        }
        if($('#txtProductCatDescription').val().trim()==""){
            alert("Enter your category description.");
            processing = false;
            return false;   
        }

        $singleSelect = $('#togCategorySingleSelection').is(":checked") ? 1 : 0;
        $mandatory = $('#togCategoryMandatory').is(":checked") ? 1 : 0;

        var formData = {
            txtProductCatName:          $('#txtProductCatName').val(),
            txtProductCatDescription:   $('#txtProductCatDescription').val(),
            txtProductID:               $('#txtProductCatID').val(),
            txtSingleSelect:        $singleSelect,
            txtMandatory:        $mandatory,
        };

        $.ajax({
            type:'POST',
            url:'/products/createProductCategory',
            data:formData,
            dataType:'json',
            success:function(data){
                $('#createProductCategory').modal('hide');
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                    }
                    // var delay = 3000;
                    // setTimeout(function() {
                    //     processing = false;
                    //     window.location.href = window.location.href;
                    // }, delay);
                    processing = false;
                    loadSubProducts();
                }else {
                    if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                    }
                    processing = false;
                }
            }
        });
        return false;
    });

    let processingSubProduct = false;
    $('#saveSubProduct').click(function(e){
        e.preventDefault();

        if (processingSubProduct) {
            return false;
        }

        processingSubProduct = true;

        if($('#txtSubProductName').val().trim()==""){
            alert("Enter sub product name");
            processingSubProduct = false;
            return false;
        }

        if($('#txtItemID').val().trim()==""){
            alert("Enter Item ID");
            processingSubProduct = false;
            return false;
        }

        if(!$.isNumeric($('#txtSubProductCost').val())){
            alert("Enter a correct cost.");
            processingSubProduct = false;
            return false;
        }
        if($('#txtSubProductType').val()==""){
            alert("Select a product type.");
            processingSubProduct = false;
            return false;
        }

        var formData = {
            txtParentProductID:                $('#txtProductCatID').val(),
            txtSubProductName:                 $('#txtSubProductName').val(),
            txtSubProductDescription:          $('#txtSubProductDescription').val(),
            txtSubProductCost:                 $('#txtSubProductCost').val(),
            txtSubProductGroup:                $('#txtSubProductGroup').attr('data-prodgroup'),
            txtSubProductType:                 $('#txtSubProductType').val(),
            txtFieldIdentifier:                $('#txtSubProductIdentifier').val(),
            txtPaymentType:                    $('#txtPaymentType').val(),
            txtItemID:                         $('#txtItemID').val(),
        };

        var formData = new FormData();
        formData.append('txtParentProductID', $('#txtProductCatID').val())
        formData.append('txtSubProductName', $('#txtSubProductName').val())
        formData.append('txtSubProductDescription', $('#txtSubProductDescription').val())
        formData.append('txtSubProductCost', $('#txtSubProductCost').val())
        formData.append('txtSubProductGroup', $('#txtSubProductGroup').attr('data-prodgroup'))
        formData.append('txtSubProductType', $('#txtSubProductType').val())
        formData.append('txtFieldIdentifier', $('#txtSubProductIdentifier').val())
        formData.append('txtPaymentType', $('#txtPaymentType').val())
        formData.append('fileProductImage', $('input[name="image-addSubProduct"]')[0].files[0])
        formData.append('txtItemID', $('#txtItemID').val())

        $.ajax({
            type:'POST',
            url:'/products/createSubProduct',
            data:formData,
            dataType:'json',
            cache: false,
            contentType: false,
            processData: false,
            success:function(data){
                $('#createSubProduct').modal('hide');
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                    }
                    // var delay = 3000;
                    // setTimeout(function() {
                    //     processingSubProduct = false;
                    //     window.location.href = window.location.href;
                    // }, delay);
                    processingSubProduct = false;
                    loadSubProducts();

                }else {
                    if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                    }
                    processingSubProduct = false;
                }
            }
        });
        return false;
    });

    $('#updateMainProduct').click(function(e){
        e.preventDefault();

        if($('#txtEditProductName').val().trim()==""){
            alert("Enter your product name.");
            return false;   
        }
        if($('#txtEditProductDescription').val().trim()==""){
            alert("Enter your product description.");
            return false;   
        }

        $singleSelect = $('#togSingleSelection').is(":checked") ? 1 : 0;
        // txtProductID=1314
        // &txtCustomFields=&txtSubProductFields=&txtSumBuyRate= ?
        // 0&txtProductName=asd&txtProductDescription=asd&txtCompanyId=3482&txtTypeId=1
        // &txtBuyRate=0.0000&tblSubProductCat_length=10 ?
        // if($('#crtMainPrdCat').hasClass('hide')){
            var formData = {
                txtEditProductID:           $('#txtEditProductID').val(),
                txtEditProductName:         $('#txtEditProductName').val(),
                txtEditProductDescription:  $('#txtEditProductDescription').val(),
                txtEditProductType:         $('#txtEditProductType').val(),
                txtEditOwnerID:             $("#txtEditOwnerID").val(),
                txtSingleSelect:        $singleSelect,
            };

                var formData = new FormData();
                formData.append('txtEditProductID', $('#txtEditProductID').val())
                formData.append('txtEditProductName', $('#txtEditProductName').val())
                formData.append('txtEditProductDescription', $('#txtEditProductDescription').val())
                formData.append('txtEditProductType', $('#txtEditProductType').val())
                formData.append('txtEditOwnerID', $('#txtEditOwnerID').val())
                formData.append('txtSingleSelect', $singleSelect)
                formData.append('fileProductImage', $('input[name="image-editImageUpload"]')[0].files[0])

            $.ajax({
                type:'POST',
                url:'/products/editMainProduct',
                data:formData,
                dataType:'json',
                cache: false,
                contentType: false,
                processData: false,
                success:function(data){
                    $('#editMainProduct').modal('hide');
                    if (data.success) {
                        if ($(".alert.alert-success").hasClass('hide')) {
                            $(".alert.alert-success").removeClass('hide');
                            $("p#msg-success").html(data.msg);
                        }
                        // var delay = 3000;
                        // setTimeout(function() {
                        //     window.location.href = window.location.href;
                        // }, delay);

                        $('#newProductName').html('<label><strong>Product Name:</strong></label> '+$('#txtEditProductName').val());
                        $('#newProductDescription').html('<label><strong>Description: </strong></label> '+$('#txtEditProductDescription').val());
                        $('#newProductType').html($('#txtEditProductType').find(':selected').attr('data-prodtype'));
                        $('#newProductOwner').html($("#txtEditOwnerID").find(':selected').attr('data-owner'));
                        $('#singleSelect').html($('#togSingleSelection').is(":checked") ? 'Yes' : 'No');
                        $('#imgUploadUI').attr('src',$('#picURL').val() + '/' + data.picture);

                    }else {
                        if ($(".alert.alert-danger").hasClass('hide')) {
                            $(".alert.alert-danger").removeClass('hide');
                            $("p#msg-danger").html(data.msg);
                        }
                    }
                }
            });
        // }
        return false;
    });

    $('#updateProductCategory').click(function(e){
        e.preventDefault();
        if($('#txtEditProductCatName').val().trim()==""){
            alert("Enter your category name.");
            return false;   
        }
        if($('#txtEditProductCatDescription').val().trim()==""){
            alert("Enter your category description.");
            return false;   
        }

        $singleSelect = $('#togCategoryEditSingleSelection').is(":checked") ? 1 : 0;
        $mandatory = $('#togCategoryEditMandatory').is(":checked") ? 1 : 0;

        var formData = {
            txtEditProductCatName:          $('#txtEditProductCatName').val(),
            txtEditProductCatID:            $('#txtEditProductCatID').val(),
            txtEditProductCatDescription:   $('#txtEditProductCatDescription').val(),
            txtSingleSelect:        $singleSelect,
            txtMandatory:        $mandatory,
        };

        $.ajax({
            type:'POST',
            url:'/products/editProductCategory',
            data:formData,
            dataType:'json',
            success:function(data){
                $('#editProductCategory').modal('hide');
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                    }
                    // var delay = 3000;
                    // setTimeout(function() {
                    //     window.location.href = window.location.href;
                    // }, delay);
                    loadSubProducts();
                }else {
                    if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                    }
                }
            }
        });
        return false;    
    });

    $('#updateSubProduct').click(function(e){
        e.preventDefault();
        if($('#txtEditSubProductName').val().trim()==""){
            alert("Enter sub product name");
            return false;
        }

        if($('#txtEditItemID').val().trim()==""){
            alert("Enter Item ID");
            processingSubProduct = false;
            return false;
        }

        if(!$.isNumeric($('#txtEditSubProductCost').val())){
            alert("Enter a correct cost.");
            return false;
        }
        if($('#txtEditSubProductType').val()==""){
            alert("Select a product type.");
            return false;
        }

        var formData = {
            txtProductCatID:                    $('#txtProductCatID').val(),
            txtEditSubProductID:                $('#txtEditSubProductID').val(),
            txtEditSubProductName:              $('#txtEditSubProductName').val(),
            txtEditSubProductDescription:       $('#txtEditSubProductDescription').val(),
            txtEditSubProductCost:              $('#txtEditSubProductCost').val(),
            txtEditOwnerID:                     $('#txtEditOwnerID2').val(),
            txtEditSubProductType:              $('#txtEditSubProductType').val(),
            txtSubProductIdentifier:            $('#txtSubProductIdentifier').val(),
            txtSubProductHideField:             $('#chkHideProduct').val(),
            txtEditPaymentType:                 $('#txtEditPaymentType').val(),
            txtEditItemID:                      $('#txtEditItemID').val(),
        };

        var formData = new FormData();
        formData.append('txtProductCatID', $('#txtProductCatID').val())
        formData.append('txtEditSubProductID', $('#txtEditSubProductID').val())
        formData.append('txtEditSubProductName', $('#txtEditSubProductName').val())
        formData.append('txtEditSubProductDescription', $('#txtEditSubProductDescription').val())
        formData.append('txtEditSubProductCost', $('#txtEditSubProductCost').val())
        formData.append('txtEditOwnerID', $('#txtEditOwnerID2').val())
        formData.append('txtEditSubProductType', $('#txtEditSubProductType').val())
        formData.append('txtSubProductIdentifier', $('#txtSubProductIdentifier').val())
        formData.append('txtSubProductHideField', $('#chkHideProduct').val())
        formData.append('txtEditPaymentType', $('#txtEditPaymentType').val())
        formData.append('fileProductImage', $('input[name="image-editSubProduct"]')[0].files[0])
        formData.append('txtEditItemID', $('#txtEditItemID').val())

        $.ajax({
            type:'POST',
            url:'/products/editSubProduct',
            data:formData,
            dataType:'json',
            cache: false,
            contentType: false,
            processData: false,
            success:function(data){
                $('#editSubProduct').modal('hide');
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                    }
                    // var delay = 3000;
                    // setTimeout(function() {
                    //     window.location.href = window.location.href;
                    // }, delay);
                    loadSubProducts();

                }else {
                    if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                    }
                }
            }
        });
        return false;
    });

    $('.datatables').dataTable({
        "sDom": "ftr",
        iDisplayLength: -1
    });

    $('.close').focus( function() {
      $(this).parent().addClass('hide');
    });

});

function createSubProduct(cat_name,cat_id){
    $('input[type="text"]#txtSubProductGroup').attr('value', cat_name);
    $('input[type="text"]#txtSubProductGroup').attr('data-prodgroup', cat_id);
    $("#txtSubProductGroup").html(cat_name);

    $('#txtSubProductName').val('');
    $('#txtSubProductCost').val(0);
    $('#txtSubProductDescription').val('');
    $('#txtSubProductType').val('');
    $('#txtPaymentType').val(-1);
    $('#txtItemID').val('');

    $('.iu-image-addSubProduct').attr({
        'src' : $('#picURL').val() + '/products/display_pictures/default.jpg'
    })

    $('.iu-image-editSubProduct').attr({
        'src' : $('#picURL').val() + '/products/display_pictures/default.jpg'
    })

    $('#createSubProduct').modal('toggle');
    return false;
}

function editMainProductOld(product_id,product_name,product_desc,product_type_id,company_id){
    $('#txtEditProductID').val(product_id);
    $('#txtEditProductName').val(product_name);
    $('#txtEditProductDescription').val(product_desc);
    $('#txtEditProductType').val(product_type_id);
    $('#txtEditOwnerID').val(company_id);

    $('#editMainProduct').modal('show');
    return false;
}

function editMainProduct(){
    $('#editMainProduct').modal('show');
    return false;
}

function editProductCategory(category_id,category_name,category_desc,singleSelect,mandatory){
    $('#txtEditProductCatID').val(category_id);
    $('#txtEditProductCatName').val(category_name);
    $('#txtEditProductCatDescription').val(category_desc);
    if(singleSelect == 1){
        $('#togCategoryEditSingleSelection').prop('checked',true);
    }else{
        $('#togCategoryEditSingleSelection').prop('checked',false);
    }

    if(mandatory == 1){
        $('#togCategoryEditMandatory').prop('checked',true);
    }else{
        $('#togCategoryEditMandatory').prop('checked',false);
    }

    $('#editProductCategory').modal('show');
    return false;
}
function editSubProduct(sub_product_id,sub_product_name,sub_product_cost,sub_product_description,sub_product_cat_id,sub_product_type,field_identifier,hide_field,payment_type, display_picture, item_id){
    $('#txtEditSubProductID').val(sub_product_id);
    $('#txtEditSubProductName').val(sub_product_name);
    $('#txtEditSubProductCost').val(sub_product_cost);
    $('#txtEditSubProductDescription').val(sub_product_description);
    $('#txtEditOwnerID2').val(sub_product_cat_id);
    $('#txtEditSubProductType').val(sub_product_type);
    $('#txtFieldIdentifier').val(field_identifier);
    $('#txtEditPaymentType').val(payment_type);
    $('#txtEditItemID').val(item_id);

    $('.iu-image-editSubProduct').attr({
        'src' : display_picture
    })

    if (hide_field==0){
        document.getElementById("chkHideProduct").checked = false;
    } else {
        document.getElementById("chkHideProduct").checked = true;
    }
    $('#editSubProduct').modal('show');
    return false;
}

function deleteProductCategory(product_category_id){
    if (confirm('Delete Product Category?')) {
        var formData = {
            product_category_id: product_category_id
        };

        $.ajax({
            type:'GET',
            url:'/products/deleteProductCategory',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                    }
                    // var delay = 3000;
                    // setTimeout(function() {
                    //     window.location.href = window.location.href;
                    // }, delay);
                    loadSubProducts();
                }else {
                    if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                    }
                }
                //window.location.href = window.location.href;
            }
        });
    }else {
        return false;
    }
}
function deleteSubProduct(sub_product_id){
    if (confirm('Delete Sub Product?')) {
        var formData = {
            product_id: sub_product_id
        };

        $.ajax({
            type:'GET',
            url:'/products/deleteProduct',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $("p#msg-success").html(data.msg);
                    }
                    // var delay = 3000;
                    // setTimeout(function() {
                    //     window.location.href = window.location.href;
                    // }, delay);
                    loadSubProducts();
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
function validate_numeric_input(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    var regex = /[0-9\b]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}

function loadSubProducts(){
    $("#accordionCategory").empty();
    var html="";
    $.getJSON('/products/getSubProducts/'+$('#txtProductCatID').val(), null, function (info) {
        data = info['data'];
        for (i=0; i < data.length; i++){

            html = html + '<div class="panel box box-primary">';
            html = html + '<div class="box-header with-border">';
            html = html + '<h4 class="box-title">'+ data[i]['name'] +'</h4>';
            html = html + '<h6><i>' + data[i]['description'];
            if(data[i]['single_selection'] == 1){
                html = html + '(Single)';
            }
            if(data[i]['is_required'] == 1){
                html = html + '(Mandatory)';
            }
            html = html + '</i></h6>';
            html = html + '<div class="box-tools pull-right">';
            if($('#viewmode').val() == ""){   
                html = html + '<a href="#" data-toggle="modal" class="btn-circle btn-circle-plus" onclick="createSubProduct(\''+data[i]['name']+'\' ,'+ data[i]['id'] +');"><i class="fa fa-plus" title="Add Sub Product" style="transform:translateY(2px);"></i></a>';
                html = html + '<a href="#" data-toggle="modal" class="btn-circle btn-circle-edit" onclick="editProductCategory('+ data[i]['id'] +',\''+data[i]['name']+'\',\''+data[i]['description']+'\','+ data[i]['single_selection'] +','+ data[i]['is_required'] +');"><i class="fa fa-pencil" title="Edit Category" style="transform:translateY(2px);"></i></a>';  
                html = html + '<a href="#" data-toggle="modal" class="btn-circle btn-circle-delete" onclick="deleteProductCategory('+ data[i]['id'] +');"><i class="fa fa-trash" title="Delete Category" style="transform:translateY(2px);"></i></a>';
            }
            html = html + '<a href="#collapse-'+ data[i]['id'] +'" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion"><i class="fa fa-arrow-down"></i></a></div></div>';

            html = html + '<div id="collapse-'+ data[i]['id'] +'" class="panel-collapse collapse in show">';
            html = html + '<div class="box-body">';
            html = html + '<table class="table datatables table-condense table-striped">';    
            html = html + '<thead>';
            html = html + '<td width="10%">Sub Product Code</td>';
            html = html + '<td width="25%">Item ID</td>';
            html = html + '<td width="20%">Sub Product Name</td>';        
            html = html + '<td width="25%">Description</td>';              
            html = html + '<td width="10%">Cost</td>';                 
            if($('#viewmode').val() == ""){  
                html = html + '<td width="10%">Actions</td>';  
            }                    
            html = html + '</thead>';  
            html = html + '<tbody>';                  
            for (x=0; x < data[i]['products'].length ; x++){                    
                html = html + '<tr>';   
                html = html + '<td>'+data[i]['products'][x]['code']+'</td>';
                html = html + '<td>'+data[i]['products'][x]['item_id']+'</td>';        
                html = html + '<td>'+data[i]['products'][x]['name']+'</td>'; 
                html = html + '<td>'+data[i]['products'][x]['description']+'</td>'; 
                html = html + '<td>$ '+data[i]['products'][x]['buy_rate']+'</td>'; 
                if($('#viewmode').val() == ""){  
                    html = html + '<td><button class="btn btn-success btn-sm fa fa-plus" onclick="addSubProductModule('+data[i]['products'][x]['id']+',\''+data[i]['products'][x]['name']+'\');" title="Add Modules"></button>';
                    html = html + '<button class="btn btn-primary btn-sm fa fa-pencil" onclick="editSubProduct('+data[i]['products'][x]['id']+',\''+data[i]['products'][x]['name']+'\',\''+data[i]['products'][x]['buy_rate']+'\',\''+data[i]['products'][x]['description']+'\',\''+data[i]['products'][x]['product_category_id']+'\', \''+data[i]['products'][x]['product_type']+'\',\''+data[i]['products'][x]['field_identifier']+'\',\''+data[i]['products'][x]['hide_field']+'\',\''+data[i]['products'][x]['product_payment_type']+'\', \''+ $('#picURL').val() + '/' + data[i]['products'][x]['display_picture']+'\',\''+data[i]['products'][x]['item_id']+'\');" title="Edit"></button>';
                    html = html + '<button class="btn btn-danger btn-sm fa fa-trash" onclick="deleteSubProduct('+data[i]['products'][x]['id']+');" title="View"></button></td>';            
                }
                html = html + '</tr>';                   
                            
            }
            html = html + '</tbody>';   
            html = html + '</table>';  
            html = html + '</div></div></div>';
        }         
        $('#accordionCategory').append(html);
    });
}

window.validate_numeric_input = validate_numeric_input;
window.createSubProduct = createSubProduct;
window.editMainProduct = editMainProduct;
window.editProductCategory = editProductCategory;
window.editSubProduct = editSubProduct;
window.deleteProductCategory = deleteProductCategory;
window.deleteSubProduct = deleteSubProduct;
window.loadSubProducts = loadSubProducts;