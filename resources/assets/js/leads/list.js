import swal from "sweetalert2";
import { validateMcc } from "../supplierLeads/mcc.js"
import { templateSelection, templateResult, matcher } from "../customSelect2.js";

$(function () {
    $('#extension1').mask('999', { clearIfNotMatch: true })
    $('#extension2').mask('999', { clearIfNotMatch: true })

    $("input[type='text']").attr('maxLength','50');
    $("#mname").attr('maxLength','1');

    $('.btnNext').click(function () {
        var curActive = $('.nav-tabs li a').parents('.nav-tabs');
        var curActiveHref = curActive.find('li.active a').attr('href');
        if (validateForm(curActiveHref)) {
            $('.nav-tabs > .active').next('li').find('a').trigger('click');

            if ($('.progressbar > .active').length == 0) {
                $('.progressbar > li').first().addClass('active')
            } else {
                $('.progressbar > .active').next('li').addClass('active')
            }
        }
    });

    $('.btnPrevious').click(function () {
        // var curActive = $('.nav-tabs li a').parents('.nav-tabs');
        // var curActiveHref = curActive.find('li.active a').attr('href');
        // if(validateForm(curActiveHref)){
        $('.nav-tabs > .active').prev('li').find('a').trigger('click');
        $('.progressbar > .active').last().removeClass('active')
        // }
    });

    $('.list-tab').click(function(e) {
        var curActive = $('.nav li a').parents('.nav');
        var curActiveId = curActive.find('li.active a').attr('id');
        $('.' + curActiveId).removeClass("active");
        $(this).addClass("active");
    })
})

$(document).ready(function () {
    // $('#businessPhone1').mask("-999-999-9999");
    // $('#businessPhone2').mask("-999-999-9999");
    $('#fax').mask("999-999-9999");
    // $('#zip').mask("99999");
    $('#cphone1').mask("999-999-9999");
    $('#cphone2').mask("999-999-9999");
    $('#contactFax').mask("999-999-9999");
    $('#mobileNumber').mask("999-999-9999");
    $('input[name="mcc"]').mask('999', { clearIfNotMatch: true })

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        }
    });

    $('.datatable').dataTable({ "lengthMenu": [25, 50, 75, 100 ]});

    $('.select2').select2({
        templateSelection: templateSelection,
        templateResult: templateResult,
        matcher: matcher
    })    

    $('.s2-country').select2({
        templateSelection: formatCountryDropdown,
        templateResult: formatCountryDropdown
    })

    $('.s2-state').select2({
        templateSelection: formatStateDropdown,
        templateResult: formatStateDropdown
    });

    /*    $('#prodSelection').change(function () {
            var product_id = $(this).val();
            element =  document.getElementById('categoryList');
            element.parentNode.removeChild(element);   
            html = '<div id="categoryList">';
            $( ".mainprodcat-"+product_id ).each(function() {
                var cat_id = $(this).val();
                var cat_name= $(this).attr("data-name");
                html = html + '<div class="form-group">';
                html = html + '<input  type="checkbox" class="catList" id="cat-chk-'+cat_id+'" data-id="'+cat_id+'" data-name="'+cat_name+'">&nbsp;&nbsp;&nbsp;';
                html = html + cat_name;
                html = html + '</div>';
            });
            html = html + '</div>';
            $('#divCategories').append(html);
            $("#order-details tbody tr").remove(); 
        });
        $('#prodSelection').trigger('change');

        $(document).on('change','.catList', function(e){
            var product_id = $('#prodSelection').val();
            var cat_id = $(this).attr('data-id');
            var cat_name= $(this).attr('data-name');
            var chk = this.checked;
            $.getJSON('/leads/select_payment_frequencies', null, function(data) {
               $( ".mainprod-"+product_id ).each(function() {
                    var sub_id = $(this).val();
                    var sub_name = $(this).attr("data-name");
                    var sub_cat_id= $(this).attr("data-cat"); 
                    var sub_amt=  $(this).attr("data-brate"); 
                    var sub_fqy=  $(this).attr("data-frequency");
                    if(cat_id == sub_cat_id)
                    {
                        if(chk) {
                            table = document.getElementById('order-details');
                            var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
                            var pid = row.insertCell(0);
                            var field_product_name = row.insertCell(1);
                            var field_cat = row.insertCell(2);
                            var field_frequency = row.insertCell(3);
                            var field_qty = row.insertCell(4);
                            var field_amount = row.insertCell(5);
                            var action = row.insertCell(6);
                            

                            row.className = "subproductrecord";
                            row.id = "table-prod-"+sub_id;
                            field_product_name.className = "table-val-name";
                            field_product_name.innerHTML = sub_name;

                            field_cat.className = "table-val-cat-"+cat_id;
                            field_cat.innerHTML = cat_name;

                            field_frequency.className = "table-val-frequency";
                            field_frequency.innerHTML = '<select id="subfreq-'+sub_id+'">'+data+'</select>';

                            field_amount.className = "table-val-amount";
                            field_amount.innerHTML = '<input id="subamt-'+sub_id+'" value="'+parseFloat(sub_amt).toFixed(2)+'" onkeypress="validate_numeric_input(event);" onchange="computeTotal('+sub_id+');" style="text-align:right; width:70%">';

                            field_qty.className = "table-val-qty";
                            field_qty.innerHTML = '<input id="subqty-'+sub_id+'" value="1" onkeypress="validate_numeric_input(event);" onchange="computeTotal('+sub_id+');" style="text-align:right; width:50%">';

                            action.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this,'+cat_id+')">Remove</button>';

                            pid.className = "table-val-pid";
                            pid.innerHTML = sub_id;
                            pid.style.display ="none";
                                
                            $("#subfreq-"+sub_id).val(sub_fqy);

                        }else{
                            if($('#table-prod-'+sub_id).length != 0){
                                element =  document.getElementById('table-prod-'+sub_id);
                                element.parentNode.removeChild(element);
                            }
                        }
                    }               
               });
            });
        });*/


    $('.tabs-rectangular li a').click(function () {
        var curActive = $(this).parents('.tabs-rectangular');

        // hide active view
        var curActiveId = curActive.find('li.active a').attr('id');
        $('#' + curActiveId + 'Container').addClass('hide');

        // change active view
        var id = $(this).attr('id');
        $('#' + id + 'Container').removeClass('hide');

        // change active tab
        curActive.find('li.active').removeClass('active');
        $(this).parent().addClass('active');
    });

    if ($('#countryName').val()) {
        var data = '&country_name=' + $('#countryName').val();
        $.ajax({
            type: 'GET',
            url: '/leads/getCountryCallingCode',
            data: data,
            dataType: 'json',
            success: function (data) {
                jQuery("label[for='businessPhone1']").html(data.data);
                jQuery("label[for='businessPhone2']").html(data.data);
            }
        });
    }

    $('#saveLeadProspect').click(function () {
        if ($('#legalName').val().trim() == "") {
            alert("Enter DBA.")
            return false;
        }
        if ($('#country').val().trim() == "") {
            alert("Select Country.")
            return false;
        }
        /* if ($('#businessPhone1').val().trim() == "") {
            alert("Enter Business Phone 1.")
            return false;
        } */
        if ($('#businessAddress1').val().trim() == "") {
            alert("Enter Business Address 1.")
            return false;
        }
        // if($('#dba').val().trim() == ""){
        //     alert("Enter a DBA.")
        //     return false;
        // }
        /* if ($('#city').val().trim() == "") {
            alert("Enter City.")
            return false;
        } */
        /* if ($('#zip').val().trim() == "") {
            alert("Enter a Zip.")
            return false;
        } */
        /* if($('#txtEmailLead').val().trim() == ""){
            alert("Enter Email.")
            return false;
        } */
        if ($('#fname').val().trim() == "") {
            alert("Enter First Name.")
            return false;
        }
        if ($('#lname').val().trim() == "") {
            alert("Enter Last Name.")
            return false;
        }
        // if($('#title').val().trim() == ""){
        //     alert("Enter Title/Position.")
        //     return false;
        // }
        /* if($('#mobileNumber').val().trim() == ""){
            alert("Enter Contact Mobile Number.")
            return false;
        } */
        if ($('#txtEmailLead').val() != "") {
            if (!isEmail($('#txtEmailLead').val())) {
                alert('Invalid email format');
                return false;
            }
        }
        // if ($('#txtEmail2Lead').val() != "") {
        //     if(!isEmail($('#txtEmail2Lead').val())){
        //         alert('Invalid email format on 2nd email');
        //         return false;
        //     }
        // }
        if (document.getElementById('assignToMe').checked) {
            $('#selfAssign').val(1);
        } else {
            $('#selfAssign').val(0);
        }

        var checkboxes = document.getElementsByName('product_list');
        var selected = [];
        for (var i=0; i<checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                selected.push(checkboxes[i].value);
            }
        }
        $('#product_access').val(selected);

        var formData = $('#frmAddLead').serialize();
        $('#saveLeadProspect').attr("disabled", true);
        document.getElementById("saveLeadProspect").innerHTML = 'Please wait...';
        $.ajax({
            type: 'POST',
            url: '/leads/createLeadProspect',
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $(".alert.alert-danger").addClass('hide');
                        $("p#msg-success").html(data.msg);
                        window.scrollTo(500, 0);
                        var delay = 3000; //3 second
                        setTimeout(function () {
                            window.location.href = '/leads';
                        }, delay);
                    }
                } else {
                    // if ($(".alert.alert-danger").hasClass('hide')) {
                    $(".alert.alert-danger").removeClass('hide');
                    $("p#msg-danger").html(data.msg);
                    window.scrollTo(500, 0);
                    $('#saveLeadProspect').attr("disabled", false);
                    document.getElementById("saveLeadProspect").innerHTML = 'Save';

                    // }
                }
            }
        });
    });

    $('#country').change(function () {
        var country = document.getElementById("country");
        var country_selectedText = country.options[country.selectedIndex].text;
        var country_selectedValue = country.options[country.selectedIndex].value;
        var usph_country_mask = "999-999-9999";
        var cn_country_mask = "9-999-999-9999";
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $('#state_us').hide();
        $('#state_ph').hide();
        $('#state_cn').hide();

        if (country_selectedValue == "United States" ||
            country_selectedValue == "Philippines") {
            $('#businessPhone1').unmask(cn_country_mask);
            $('#businessPhone1').mask(usph_country_mask);
            $('#businessPhone2').unmask(cn_country_mask);
            $('#businessPhone2').mask(usph_country_mask);

            $('#cphone1').unmask(cn_country_mask);
            $('#cphone1').mask(usph_country_mask);
            $('#cphone2').unmask(cn_country_mask);
            $('#cphone2').mask(usph_country_mask);

            $('#mobileNumber').unmask(cn_country_mask);
            $('#mobileNumber').mask(usph_country_mask);

            if (country_selectedValue == "United States") {
                $('#zip').unmask();
                $('#zip').mask(us_zip_mask);
                $('#state_us').show();
            } else if (country_selectedValue == "Philippines") {
                $('#zip').unmask();
                $('#zip').mask(ph_zip_mask);
                $('#state_ph').show();
            }
        }
        if (country_selectedValue == "China") {
            $('#businessPhone1').unmask(usph_country_mask);
            $('#businessPhone1').mask(cn_country_mask);
            $('#businessPhone2').unmask(usph_country_mask);
            $('#businessPhone2').mask(cn_country_mask);

            $('#cphone1').unmask(usph_country_mask);
            $('#cphone1').mask(cn_country_mask);
            $('#cphone2').unmask(usph_country_mask);
            $('#cphone2').mask(cn_country_mask);

            $('#mobileNumber').unmask(usph_country_mask);
            $('#mobileNumber').mask(cn_country_mask);

            $('#zip').unmask();
            $('#zip').mask(cn_zip_mask);

            $('#state_cn').show();
        }
        var data = '&country_name=' + country_selectedText;
        // $.getJSON('add?action=get_country_calling_code&country_name='+country_selectedText, null, function(data) {  
        $.ajax({
            type: 'GET',
            url: '/leads/getCountryCallingCode',
            data: data,
            dataType: 'json',
            success: function (data) {
                jQuery("label[for='businessPhone1']").html(data.data);
                jQuery("label[for='businessPhone2']").html(data.data);
            }
        });
        // });  
    });
    $('#country').trigger('change');

    $('#assignTo').change(function () {
        var partner_type = document.getElementById("assignTo");
        if (partner_type.selectedIndex >= 0) {
            var partner_type_selectedText = partner_type.options[partner_type.selectedIndex].text;
            var partner_type_selectedValue = partner_type.options[partner_type.selectedIndex].value;
            var optionValue = $('#txtDraftParent').val() != "" ? $('#txtDraftParent').val() : "";

            var data = '&partner_type_id=' + partner_type_selectedValue;
            // console.log(data);return false;
            // $.getJSON('add?action=load_upline_list&partner_type_id='+partner_type_selectedValue, null, function(data) { 
            $.ajax({
                type: 'GET',
                url: '/leads/loadUplineLIst',
                data: data,
                dataType: 'json',
                success: function (data) {
                    $('#assignee').empty(); //remove all child nodes
                    var newOption = $(data.data);
                    $('#assignee').append(newOption);
                    $('#assignee').trigger("chosen:updated");
                    $("#assignee").find('option[value="' + optionValue + '"]').attr('selected', true);
                    updateInterestedProducts($('#assignee').val());
                }
            });
            // });      
        } else {
            $('#assignee').empty(); //remove all child nodes   
        }


    });
    $('#assignTo').trigger('change');

    $('#assignToMe').change(function () {
        if (document.getElementById('assignToMe').checked) {
            $("#assignee").prop("disabled", true);
            $("#assignTo").prop("disabled", true);
            $('.assignToDiv').hide();
        } else {
            $("#assignee").prop("disabled", false);
            $("#assignTo").prop("disabled", false);
            $('.assignToDiv').show();
        }
    });
    $('#assignToMe').trigger('change');

    $('#btnLoadInterestedProduct').click(function () {
        if($('#parent_id').val() == "-1") //unassigned
        {
            showWarningMessage($('#company_name').val() + " is currently unassigned. Please assign to a Partner first.");
            return false;
        }
        $('#modalInterestedProductSelection').modal('show');
        $('#tblListInterestedProduct_filter').find('input[type=search]').css('max-width', '100px');
        return false;
    });
    $('#btnAddInterestedProduct').click(function () {
        $('#modalInterestedProductSelection').modal('hide');

        var formData = $('#frmAddInterestedProductsLeads').serialize();
        var checkboxes = document.getElementsByName("add_products[]");
        var products = "";
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                products = products + checkboxes[i].value + ",";
            }
        }

        products = products.substr(0, products.length - 1);

        if (products == '') {
            alert('Please select a product!');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '/leads/interestedProducts/addInterestedProduct',
            data: formData + '&partner_id=' + $('#partner_id').val(),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    // if ($(".alert.alert-success").hasClass('hide')) {
                    $(".alert.alert-success").removeClass('hide');
                    $(".alert.alert-danger").addClass('hide');
                    $("p#msg-success").html(data.msg);
                    window.scrollTo(500, 0);
                    var delay = 3000; //3 second
                    setTimeout(function () {
                        var str = window.location.href;
                        str = str.replace("#", '');
                        window.location.href = str;
                    }, delay);
                    // }
                } else {
                    // if ($(".alert.alert-danger").hasClass('hide')) {
                    $(".alert.alert-danger").removeClass('hide');
                    $("p#msg-danger").html(data.msg);
                    window.scrollTo(500, 0);
                    // }
                }
            }
        });
    });

    $('#updateLeadProspect').click(function () {
        // if($('#dba').val().trim() == ""){
        //     alert("Enter DBA")
        //     return false;
        // }
        /* if($('#country').val().trim() == ""){
            alert("Enter Country")
            return false;
        }
        if($('#businessAddress1').val().trim() == ""){
            alert("Enter Business Address 1")
            return false;
        }
        if($('#city').val().trim() == ""){
            alert("Enter City")
            return false;
        }        
        if($('#txtState').val().trim() == ""){
            alert("Enter State")
            return false;
        }        
        if($('#businessPhone1').val().trim() == ""){
            alert("Enter Business Phone 1")
            return false;
        }
        if($('#txtEmailLead').val().trim() == ""){
            alert("Enter Email")
            return false;
        }
        if ($('#txtEmailLead').val() != "") {
            if(!isEmail($('#txtEmailLead').val())){
                alert('Invalid email format');
                return false;
            }
        } */

        if (validateForm('business-info')) {
            var formData = $("#frmUpdateLead").serialize();

            $.ajax({
                type: 'POST',
                url: '/leads/details/profile/updateLeadProspect',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        if ($(".alert.alert-success").hasClass('hide')) {
                            $(".alert.alert-success").removeClass('hide');
                            $(".alert.alert-danger").addClass('hide');
                            $("p#msg-success").html(data.msg);
                            window.scrollTo(500, 0);
                            var delay = 3000; //3 second
                            setTimeout(function () {
                                var str = window.location.href;
                                str = str.replace("#", '');
                                window.location.href = str;
                            }, delay);
                        }
                    } else {
                        // if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                        window.scrollTo(500, 0);
                        // }
                    }
                }
            });
        }

        return false;
    });

    $(document).on('submit', 'form', function (event) {
        var frmName = event.target.id;
        if (frmName.includes("frmComment")) {
            if ($('#txtComment').val() == '') {
                alert('Comment must not be empty');
                return false;
            }
            $.ajax({
                type: 'POST',
                url: '/leads/details/profile/addComment',
                data: $('#' + frmName).serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        if ($(".alert.alert-success").hasClass('hide')) {
                            $(".alert.alert-success").removeClass('hide');
                            $(".alert.alert-danger").addClass('hide');
                            $("p#msg-success").html(data.msg);
                            window.scrollTo(500, 0);
                            var delay = 3000; //3 second
                            setTimeout(function () {
                                var str = window.location.href;
                                str = str.replace("#", '');
                                window.location.href = str;
                            }, delay);
                        }
                    } else {
                        // if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                        window.scrollTo(500, 0);
                        // }
                    }
                }
            });
            return false;
        }
        /* your AJAX code here */
        if (frmName.includes("frmSubComment")) {
            var form = $(this);
            inputValue = form.find('textarea[name="txtSubComment"]').val();
            if (inputValue == '') {
                alert('Subcomment must not be empty');
                return false;
            }
            $.ajax({
                type: 'POST',
                url: '/leads/details/profile/addSubComment',
                data: $('#' + frmName).serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        if ($(".alert.alert-success").hasClass('hide')) {
                            $(".alert.alert-success").removeClass('hide');
                            $(".alert.alert-danger").addClass('hide');
                            $("p#msg-success").html(data.msg);
                            window.scrollTo(500, 0);
                            var delay = 3000; //3 second
                            setTimeout(function () {
                                var str = window.location.href;
                                str = str.replace("#", '');
                                window.location.href = str;
                            }, delay);
                        }
                    } else {
                        // if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                        window.scrollTo(500, 0);
                        // }
                    }
                }
            });
            return false;
        }
    });

    $('#updateContact').click(function () {
        /* if($('#fname').val().trim() == ""){
            alert("Enter First Name")
            return false;
        }
        if($('#lname').val().trim() == ""){
            alert("Enter Last Name")
            return false;
        } */
        // if($('#title').val().trim() == ""){
        //     alert("Enter Title")
        //     return false;
        // }

        if (validateForm('contact-person')) {
            var formData = $("#frmUpdateContact").serialize();

            $.ajax({
                type: 'POST',
                url: '/leads/details/contact/updateContact',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        if ($(".alert.alert-success").hasClass('hide')) {
                            $(".alert.alert-success").removeClass('hide');
                            $(".alert.alert-danger").addClass('hide');
                            $("p#msg-success").html(data.msg);
                            window.scrollTo(500, 0);
                            var delay = 3000; //3 second
                            setTimeout(function () {
                                var str = window.location.href;
                                str = str.replace("#", '');
                                window.location.href = str;
                            }, delay);
                        }
                    } else {
                        // if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                        window.scrollTo(500, 0);
                        // }
                    }
                }
            });
        }

        return false;
    });

    $('#btnConvertToMerchant').click(function () {
        $('#modalConvertToMerchant').modal('show');
    });

    $('#btnProcessConvert').click(function () {
        // if($('#txtMerchantMID').val().trim() == ""){
        //     alert("Enter Merchant MID")
        //     return false;
        // }  

        var formData = $("#frmConvertToMerchant").serialize();
        // $.ajax({
        //     type:'POST',
        //     url:'/leads/details/profile/convertToMerchant',
        //     data: formData,
        //     dataType:'json',
        //     success:function(data){
        //         if (data.success) {
        //             if ($(".alert.alert-success").hasClass('hide')) {
        //                 $(".alert.alert-success").removeClass('hide');
        //                 $(".alert.alert-danger").addClass('hide');
        //                 $("p#msg-success").html(data.msg);
        //                 window.scrollTo(500,0);
        //                 var delay=3000; //3 second
        //                 setTimeout(function() {
        //                     window.location.href = '/leads';
        //                 }, delay);
        //             }
        //         }else {
        //             // if ($(".alert.alert-danger").hasClass('hide')) {
        //                 $(".alert.alert-danger").removeClass('hide');
        //                 $("p#msg-danger").html(data.msg);
        //                 window.scrollTo(500,0);
        //             // }
        //         }
        //     }
        // });
        return false;
    });

    $('#frmUploadCSV').submit(function () {
        var filename = document.getElementById("fileUploadCSV").value;
        if (document.getElementById("fileUploadCSV").value == "") {
            alert('Please select a file');
            return false;
        }
        var ext = filename.split('.').pop();
        if (ext != "csv") {
            alert('Please select csv file format.');
            return false;
        }

        $('#modalUploadCSV').modal('hide');
        showLoadingModal('Processing...');
        $.ajax({
            url: "/leads/uploadfile", // Url to which the request is send
            type: "POST", // Type of request to be send, called as method
            data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            dataType: 'json',
            contentType: false, // The content type used when sending data to the server.
            cache: false, // To unable request pages to be cached
            processData: false, // To send DOMDocument or non processed data file it is set to false
            success: function (data) // A function to be called if request succeeds
            {
                closeLoadingModal();
                if (!data.logs) {
                    alert(data.message);
                    var delay = 3000; //3 second
                    setTimeout(function () {
                        var str = window.location.href;
                        str = str.replace("#", '');
                        window.location.href = str;
                    }, delay);
                } else {
                    var logs = "";
                    for (var i = 0; i < data.logs.length; i++) {
                        logs = logs + data.logs[i] + " \n";
                    }
                    alert('Successfully processed file but with exceptions \n\n' + logs);
                    var delay = 3000; //3 second
                    setTimeout(function () {
                        var str = window.location.href;
                        str = str.replace("#", '');
                        window.location.href = str;
                    }, delay);
                }
            }
        });
        return false;
    });

    $('#btnConvertToProspect').click(function () {
        // if($('#txtMerchantMID').val().trim() == ""){
        //     alert("Enter Merchant MID")
        //     return false;
        // }  

        var formData = $("#frmUpdateLead").serialize();
        $.ajax({
            type:'POST',
            url:'/leads/details/profile/convertToProspect',
            data: formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $(".alert.alert-success").removeClass('hide');
                        $(".alert.alert-danger").addClass('hide');
                        $("p#msg-success").html(data.msg);
                        window.scrollTo(500,0);
                        var delay=3000; //3 second
                        setTimeout(function() {
                            window.location.href = '/leads';
                        }, delay);
                    }
                }else {
                    // if ($(".alert.alert-danger").hasClass('hide')) {
                        $(".alert.alert-danger").removeClass('hide');
                        $("p#msg-danger").html(data.msg);
                        window.scrollTo(500,0);
                    // }
                }
            }
        });
        return false;
    });

    $(".btnSaveAsDraft").click(function() {
        if (document.getElementById('assignToMe').checked) {
            $('#selfAssign').val(1);
        } else {
            $('#selfAssign').val(0);
        }
        
        var formData = $("#frmAddLead").serialize();

        showLoadingAlert('Saving as Draft...');
        $.ajax({
            type: "POST",
            url: '/drafts/store',
            data: formData,
            success: function(data) {
                closeLoading();
                if (data.success) {
                    showSuccessMessage(data.message, '/leads');
                } else {
                    showWarningMessage(data.message);
                }
            },
        });
    });

    $('#zip').on('change', function(){
        var zipLen = 5;
        var stateEl = '#txtState';
        
        if ($('#country').val() == 'Philippines') {
            zipLen = 4;
            stateEl = '#txtStatePH';
        } else if ($('#country').val() == 'China') {
            zipLen = 6;
            stateEl = '#txtStateCN';
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#zip';
            var zipErrEl = '#zip-error small';
            var cityEl = '#city';

            $.ajax({
                url: "/extras/getCityAndState/" + zip,
                type: "GET",
            }).done(function(data) {
                if (data.success) {
                    let option = "";
                    $.each(data.cities, function (key, item) {
                        option += '<option value="' + item.city + '">' + item.city + '</option> ';
                    });
        
                    $(cityEl).empty(); //remove all child nodes
                    var newOption = option;
                    $(cityEl).append(newOption);
                    
                    $(stateEl).val(data.state).trigger('change');
        
                    $(zipEl).parents('.form-group').removeClass('has-error');
                    $(zipErrEl).text('');
                } else {
                    $(zipEl).parents('.form-group').addClass('has-error');
                    $(zipErrEl).text('Error, not a US zip code.'); 
                    $(zipEl).val('');
                }
                $(stateEl).prop('disabled', false);
                $(cityEl).prop('disabled', false);
            });
        }
    });
});

function deleteProduct(prod_id) {
    if (confirm('Are you sure you want to remove?')) {
        var formData = {
            product_id: prod_id,
            partner_id: $('#partner_id').val()
        };
        $.ajax({
            type: 'GET',
            url: '/leads/interestedProducts/deleteInterestedProduct',
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    // if ($(".alert.alert-success").hasClass('hide')) {
                    $(".alert.alert-success").removeClass('hide');
                    $(".alert.alert-danger").addClass('hide');
                    $("p#msg-success").html(data.msg);
                    window.scrollTo(500, 0);
                    $('#ip_' + prod_id).remove();
                    // $.ajax({
                    //     type:'GET',
                    //     url:'/leads/interestedProducts/getInterestedProducts',
                    //     data:formData,
                    //     dataType:'json',
                    //     success:function(data){
                    //         // $('#products').val(data.products);
                    //         $('#loadedProducts').empty(); //remove all child nodes
                    //         var newOption = $(data.products);
                    //         $('#loadedProducts').append(newOption);
                    //     }
                    // });
                    window.location.href = window.location.href;
                    // }
                } else {
                    // if ($(".alert.alert-danger").hasClass('hide')) {
                    $(".alert.alert-danger").removeClass('hide');
                    $("p#msg-danger").html(data.msg);
                    window.scrollTo(500, 0);
                    var delay = 3000; //3 second
                    setTimeout(function () {
                        $(".alert.alert-danger").addClass('hide');
                    }, delay);
                    // }
                }
            }
        });
    }
}

function cancelReply(id) {
    $('#divCommentPostReply' + id).hide();
    $('#addreply' + id).show();
    $('#cancelreply' + id).hide();;
}

function addReply(id) {
    $('#divCommentPostReply' + id).show();
    $('#addreply' + id).hide();
    $('#cancelreply' + id).show();
}

function showAllSpecific(id) {
    var $comparent = '#comment' + id;
    $($comparent + ' .comment-reply').show();
    $("#showall" + id).hide();
    $($comparent + ' .showless').show();
}

function hideAllSpecific(id) {
    var $comparent = '#comment' + id;
    $($comparent + ' .comment-reply').hide();
    $("#showall" + id).show();
    $($comparent + ' .showless').hide();
}

function showAllReplies() {
    $('.comment-reply').show();
    $('.comment .showall').hide();
    $('.comment .showless').show();
}

function hideAllReplies() {
    $('.comment-reply').hide();
    $('.comment .showall').show();
    $('.comment .showless').hide();
}

function updateIncomingLeadRequest(id, status) {
    var status_text = "";
    if (status == 'A') {
        status_text = "accept";
    } else {
        status_text = "decline";
    }
    if (confirm("Are you sure you want to " + status_text + " this request?")) {
        // $.getJSON('incoming_lead?action=update_incoming_lead_request&id='+id+'&status='+status, null, function(data) {
        var data = {
            id: id,
            status: status
        };
        $.ajax({
            type: 'GET',
            url: '/leads/incoming/updateIncomingLeadRequest',
            data: data,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $('#modalInterestedProductSelection').modal('hide');
                        $(".alert.alert-success").removeClass('hide');
                        $(".alert.alert-danger").addClass('hide');
                        $("p#msg-success").html(data.msg);
                        window.scrollTo(500, 0);
                        var delay = 3000; //3 second
                        setTimeout(function () {
                            window.location.href = '/leads';
                        }, delay);
                    }
                } else {
                    // if ($(".alert.alert-danger").hasClass('hide')) {
                    $(".alert.alert-danger").removeClass('hide');
                    $("p#msg-danger").html(data.msg);
                    window.scrollTo(500, 0);
                    // }
                }
            }
        });
    }
}

function upload() {
    $('#modalUploadCSV').modal('show');
    return false;
}

function advanceSearchLeadsProspects() {
    var canviewupline = $('#uplineView').val();

    var checkboxes = document.getElementsByName("interested_products[]");
    var products = "";
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            products = products + checkboxes[i].value + ",";
        }
    }

    products = products.substr(0, products.length - 1);

    if (products == '') {
        // alert('Select a product!');
        // return false;
        products = -1; //JR-2018-09-25 allows user to retrieve all records without filter
    }

    $('#leads-table').dataTable().fnDestroy();
    if (canviewupline == '1') {
        $('#leads-table').DataTable({
             "lengthMenu": [25, 50, 75, 100 ],
            processing: true,
            serverSide: true,
            ajax: '/leads/advance_leads_prospects_search/leads/' + products,
            columns: [
                /* {
                    data: 'partner_type'
                }, */
                {
                    data: 'merchant_id'
                },
                {
                    data: 'lead_source'
                },
                {
                    data: 'partner'
                },
                {
                    data: 'interested_products'
                },
                {
                    data: 'upline_partners'
                },
                {
                    data: 'company_name'
                },
                {
                    data: 'contact_person'
                },
                {
                    data: 'phone1'
                },
                {
                    data: 'mobile_number'
                },
                {
                    data: 'partner_status'
                },
                /* {
                    data: 'action'
                } */
            ]
        });
    } else {
        $('#leads-table').DataTable({
             "lengthMenu": [25, 50, 75, 100 ],
            processing: true,
            serverSide: true,
            ajax: '/leads/advance_leads_prospects_search/leads/' + products,
            columns: [
                /* {
                    data: 'partner_type'
                }, */
                {
                    data: 'merchant_id'
                },
                {
                    data: 'lead_source'
                },
                {
                    data: 'interested_products'
                },
                {
                    data: 'company_name'
                },
                {
                    data: 'contact_person'
                },
                {
                    data: 'phone1'
                },
                {
                    data: 'mobile_number'
                },
                {
                    data: 'partner_status'
                },
                // { data: ''}
            ]
        });
    }
    redrawTable('#leads-table');
    $('.adv-close').click();
}

function deleteLead(id, status) {
    if (confirm('Are you sure you want to delete Lead?')) {
        var data = {
            id: id,
            status: status
        };
        $.ajax({
            type: 'POST',
            url: '/leads/deleteLead',
            data: data,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    if ($(".alert.alert-success").hasClass('hide')) {
                        $('#modalInterestedProductSelection').modal('hide');
                        $(".alert.alert-success").removeClass('hide');
                        $(".alert.alert-danger").addClass('hide');
                        $("p#msg-success").html(data.msg);
                        window.scrollTo(500, 0);
                        var delay = 3000; //3 second
                        setTimeout(function () {
                            window.location.href = '/leads';
                        }, delay);
                    }
                } else {
                    // if ($(".alert.alert-danger").hasClass('hide')) {
                    $(".alert.alert-danger").removeClass('hide');
                    $("p#msg-danger").html(data.msg);
                    window.scrollTo(500, 0);
                    // }
                }
            }
        });
    }
    return false;
}

function isEmail(email) {
    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(email);
}

function validateField(element, msg) {
    if ($('#' + element).val().trim() == "") {
        document.getElementById(element).style.borderColor = "red";
        alert(msg);
        return false;
    } else {
        document.getElementById(element).style.removeProperty('border');
        return true;
    }
}

function validateForm(curActiveHref) {
    var errors = {};
    if (curActiveHref.match('business-info')) {
        if ($('#legalName').val().trim() == "") {
            var id = "legalName";
            var msg = "DBA is required.";
            errors[id] = msg;
        } else {
            document.getElementById('legalName').style.removeProperty('border');
            $('#legalName-error small').text('');
        }

        if ($('input[name="mcc"]').val().trim() != '') {
            document.getElementById('mcc').style.removeProperty('border');
            $('#mcc-error small').text('');
            
            if (!validateMcc()) {
                let el = $('input[name="mcc"]')

                var id = "business_industry"
                var msg = `${el.val()} is not a valid Merchant Category Code`

                errors[id] = msg
            } else {
                document.getElementById('business_industry').style.removeProperty('border');
                $('#business_industry-error small').text('');
            }
        } else {
            var id = "mcc"
            var msg = 'MCC is required'

            errors[id] = msg
        }


        // if ($('#dba').val().trim() == "") {
        //     var id = "dba";
        //     var msg = "DBA is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('dba').style.removeProperty('border');
        //     $('#dba-error').remove();
        // }
        if ($('#businessAddress1').val().trim() == "") {
            var id = "businessAddress1";
            var msg = "Business Address 1 is required.";
            errors[id] = msg;
        } else {
            document.getElementById('businessAddress1').style.removeProperty('border');
            $('#businessAddress1-error small').text('');
        }
        /* if ($('#city').val().trim() == "") {
            var id = "city";
            var msg = "City is required.";
            errors[id] = msg;
        } else {
            document.getElementById('city').style.removeProperty('border');
            $('#city-error small').text('');
        } */
        /* if ($('#zip').val().trim() == "") {
            var id = "zip";
            var msg = "Zip is required.";
            errors[id] = msg;
        } else {
            document.getElementById('zip').style.removeProperty('border');
            $('#zip-error small').text('');
        }
        if ($('#country').val().trim() == "") {
            var id = "country";
            var msg = "Country is required.";
            errors[id] = msg;
        } else {
            document.getElementById('country').style.removeProperty('border');
            $('#country-error small').text('');
        } */
        /* if ($('#businessPhone1').val().trim() == "") {
            var id = "businessPhone1";
            var msg = "Business Phone 1 is required.";
            errors[id] = msg;
        } else {
            document.getElementById('businessPhone1').style.removeProperty('border');
            $('#businessPhone1-error small').text('');
        } */
        if ($('#businessPhone1').val().trim() != "") {
            if ($('#businessPhone1').val().length == 12 ||
                $('#businessPhone1').val().length == 14) {
                document.getElementById('businessPhone1').style.removeProperty('border');
                $('#businessPhone1-error small').text('');
            } else {
                var id = "businessPhone1";
                var msg = "Invalid Phone Number.";
                errors[id] = msg;
            }
        }
        /* if ($('#txtEmailLead').val().trim() == "") {
            var id = "txtEmailLead";
            var msg = "Email is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtEmailLead').style.removeProperty('border');
            $('#txtEmailLead-error').remove();
        } */
        if ($('#txtEmailLead').val() != "") {
            if (!isEmail($('#txtEmailLead').val())) {
                var id = "txtEmailLead";
                var msg = "Invalid email format.";
                errors[id] = msg;
            } else {
                document.getElementById('txtEmailLead').style.removeProperty('border');
                $('#txtEmailLead-error small').text('');
            }
        }
        if ($('#txtEmailLead').val().trim() == "") {
            $('#mobileNum').text('*');
        } else {
            $('#mobileNum').text('');
        }
        if ($('#isUpdate').val() == '1') {
            if ($('#mobileNumber').val().trim() == "" &&
                ($('#txtEmailLead').val().trim() == "")) {
                var id = "txtEmailLead";
                var msg = "Lead must have either Business Email or Contact Person's Mobile Number.";
                errors[id] = msg;
            } else {
                document.getElementById('txtEmailLead').style.removeProperty('border');
                $('#txtEmailLead-error small').text();
            }
        }
    } else if (curActiveHref.match('contact-person')) {
        if ($('#fname').val().trim() == "") {
            var id = "fname";
            var msg = "First Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('fname').style.removeProperty('border');
            $('#fname-error small').text('');
        }
        if ($('#lname').val().trim() == "") {
            var id = "lname";
            var msg = "Last Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('lname').style.removeProperty('border');
            $('#lname-error small').text('');
        }
        /* if ($('#mobileNumber').val().trim() == "") {
            var id = "mobileNumber";
            var msg = "Mobile Number is required.";
            errors[id] = msg;
        } else {
            document.getElementById('mobileNumber').style.removeProperty('border');
            $('#mobileNumber-error').remove();
        } */
        if ($('#mobileNumber').val().trim() == "" &&
            ($('#txtEmailLead').val().trim() == "")) {
            var id = "mobileNumber";
            var msg = "Lead must have either Business Email or Contact Person's Mobile Number.";
            errors[id] = msg;
        } else {
            document.getElementById('mobileNumber').style.removeProperty('border');
            $('#mobileNumber-error small').text();
        }
    }
    if (!validateReqFields(errors)) {
        return false;
    }
    return true;
}

function validateReqFields(errors) {
    if (jQuery.isEmptyObject(errors)) {
        return true;
    } else {
        for (var key in errors) {
            var value = errors[key];
            // if (!document.getElementById(key + '-error')) {
            document.getElementById(key).style.borderColor = "red";
            $('#' + key + '-error small').text(value); //$('#' + key).after('<span id="' + key + '-error" style="color:red"><small>' + value + '</small></span>');
            // }
        }
        return false;
    }
}

function validateData(table, field, value, id, includeStatus, prefix, message, tab) {
    $.getJSON('/partners/validateField/' + table + '/' + field + '/' + value.value + '/' + id + '/' + includeStatus + '/' + prefix, null, function (data) {
        if (data) {
            alert(message);
            if ($('#' + tab).trigger('click')) {
                $('.' + tab).last().removeClass('active')
            }
            value.value = '';
            value.focus();
            return false;
        } else {
            return value.value;
        }
    });
}

function deleteDraftApplicant(id) {
    showLoadingAlert('Loading...');

    $.ajax({
        type: "POST",
        url: '/drafts/deleteDraftApplicant',
        data: 'partner_id=' + id,
        success: function(data) {
            closeLoading();
            if (data.success) {
                showSuccessMessage(data.message, '/leads');
            } else {
                showWarningMessage(data.message);
            }
        },
    });
}

function formatSelect2(resource) {
    if (resource.element !== undefined && 
        resource.element.dataset !== undefined && 
        resource.element.dataset.image !== undefined) {
        return $(
        '<span style="margin-left: 3px;">' +
          '<img style="transform: translateY(-1px)" class="ticket-img-xs" src="' + resource.element.dataset.image + '">' +
          '<span style="color: black;">' + resource.text + '</span>' + 
        '</span>'
      )
    }

    return $('<span>' + resource.text + '</span>')
}

function formatSelect2Result(resource) {
    if (resource.element !== undefined && 
        resource.element.dataset !== undefined && 
        resource.element.dataset.image !== undefined) {

        if (resource.element.dataset.user_type !== undefined) {
            return $(
            '<div style="display: flex; align-items: center;">' +
                '<img class="ticket-img-md" src="' + resource.element.dataset.image + '">' +
                '<span style="display: flex; flex-direction: column; margin-left: 10px;">' +
                '<span style="font-size: 1.1rem;"><strong>' + resource.text + '</strong></span>' +
                '<span style="font-size: 0.8rem; transform: translate(5px, -2px)">' + resource.element.dataset.user_type + '</span>' +
                '</span><!--/ta-item-actor-details-->' +
            '</div><!--/ta-item-actor--></div>'
            )
        }

        return $(
        '<span>' +
          '<img class="ticket-img-xs" src="' + resource.element.dataset.image + '">' +
          '<span>' + resource.text + '</span>' + 
        '</span>'
      )
    }

    return $('<span>' + resource.text + '</span>')
}

function showWarningMessage(msg) {
    swal("Warning", msg, "warning");
}

function showSuccessMessage(msg, url) {
    swal("Success", msg,"success").then((value) => {
        window.location.href = url;
    })
}

function showLoadingAlert(msg) {
    swal({
        title: msg,
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

function isValidZip(el, city_id, state_id) {
    $('#' + city_id).prop('disabled', true);
    $('#' + state_id).prop('disabled', true);
    if (el.value.length == 5) {
        $.ajax({
            url: "/merchants/getCityState/" + el.value,
            type: "GET",
        }).done(function(data) {
            $('#' + city_id).val(data.city);
            $('#' + state_id).val(data.abbr).trigger('change');
            $('#' + el.id + '-error small').text('');
            document.getElementById(el.id).style.removeProperty('border');
            $('#' + city_id).prop('disabled', false);
            $('#' + state_id).prop('disabled', false);

        }).fail(function(data) {
            document.getElementById(el.id).style.borderColor = "red";
            $('#' + el.id + '-error small').text('Error, not a US zip code.'); 
            $('#' + el.id).val('');
            $('#' + city_id).prop('disabled', false);
            $('#' + state_id).prop('disabled', false);
        });
    }
}


if( $('#assignToMe').prop( "checked" )){
    updateInterestedProducts($('#userRef').val());
}else{
    updateInterestedProducts($('#assignee').val());
}  


$('#assignee').change(function (){
    updateInterestedProducts($('#assignee').val());
}); 

$('#assignToMe').click(function (){
    if( $('#assignToMe').prop( "checked" )){
        updateInterestedProducts($('#userRef').val());
    }else{
        updateInterestedProducts($('#assignee').val());
    }  
}); 

function updateInterestedProducts(id){
    $("#interested-product-div").empty();
    var data = {
        id:  id
    };
    $.ajax({
        type: 'GET',
        url: '/products/getPartnerProducts',
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                for (var i = 0; i < data.products.length; i++) {
                    var add_product = '<div class="form-group">\
                                        <input type="checkbox" name="product_list" value="'+ data.products[i].id +'"/>\
                                        <label>'+ data.products[i].name +'</label>\
                                    </div>';
                    $("#interested-product-div").append(add_product);
                }
            } 
        }
    });

}

function formatCountryDropdown (country) {
    if (!country.id) {
        return country.text;
    }
    var baseUrl = "/storage/flags/";
    var $country = $(
        '<span><img class="img-flag" /> <span></span></span>'
    );

    $country.find("span").text(country.text);
    $country.find("img").attr("src", baseUrl + country.element.dataset.code.toLowerCase() + "-flag.png");
    $country.find("img").attr("style", "height:15px;width:25px;");

    return $country;
};

function formatStateDropdown (state) {
    if (!state.id) {
        return state.text;
    }
    var baseUrl = "/storage/flags/";
    var $state = $(
        '<span><small class="badge badge-secondary"><strong></strong></small>&nbsp;<span></span></span>'
    );

    $state.find("span").text(state.text);
    $state.find("strong").text(state.id);

    return $state;
};

window.deleteProduct = deleteProduct;
window.cancelReply = cancelReply;
window.addReply = addReply;
window.showAllSpecific = showAllSpecific;
window.hideAllSpecific = hideAllSpecific;
window.showAllReplies = showAllReplies;
window.hideAllReplies = hideAllReplies;
window.updateIncomingLeadRequest = updateIncomingLeadRequest;
window.upload = upload;
window.advanceSearchLeadsProspects = advanceSearchLeadsProspects;
window.deleteLead = deleteLead;
window.isEmail = isEmail;
window.validateData = validateData;
window.deleteDraftApplicant = deleteDraftApplicant;
window.isValidZip = isValidZip;
window.updateInterestedProducts = updateInterestedProducts;