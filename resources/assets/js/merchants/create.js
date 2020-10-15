import swal from "sweetalert2";
import { validateMcc } from "../supplierLeads/mcc.js"
import { templateSelection, templateResult, matcher } from "../customSelect2.js";

var elems = [];
var elems1 = [];

$('.select2').select2({
    templateSelection: templateSelection,
    templateResult: templateResult,
    matcher: matcher
})

if ($('#countContact.title')) {
    for (let index = 1; index <= $('#countContact.title').length; index++) {
        elems.push(index);
    }
    $('#txtOtherHidden').val(JSON.stringify(elems));
}

$('.datatables').dataTable();

$(function () {
    $('.btnNext').click(function () {
        var curActive = $('.nav-tabs li a').parents('.nav-tabs');
        var curActiveHref = curActive.find('li.active a').attr('href');
        if (validateForm(curActiveHref)) {
            $('.mainnav > .active').next('li').find('a').trigger('click');

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
        $('.mainnav > .active').prev('li').find('a').trigger('click');
        $('.progressbar > .active').last().removeClass('active')
        // }
    });
})

$('.list-tab').click(function(e) {
    var curActive = $('.nav li a').parents('.nav');
    var curActiveId = curActive.find('li.active a').attr('id');
    $('.' + curActiveId).removeClass("active");
    $(this).addClass("active");
})

$(document).ready(function () {
    $('.assigntome').change(function () {
        $('.assigntodiv').toggle();
    });
    
    if (document.getElementById('assigntome').checked) {
        $('.assigntodiv').toggle();
    }

    $('input[name="mcc"]').mask('999', { clearIfNotMatch: true })
    $('#txtSocialSecurityNumber').mask('999-99-9999', {clearIfNotMatch: true})

    $('#txtBankRouting').mask('999999999', {clearIfNotMatch: true})
    $('#txtBankRoutingConfirmation').mask('999999999', {clearIfNotMatch: true})
    $('#txtMID').mask('9999999999999999', {clearIfNotMatch: true})

    $('#txtFederalTaxID').mask('999999999');
    $('#txtTaxIdNumber').mask('99-9999999');
    // $('#txtPhone1').mask("-999-999-9999");
    // $('#txtPhone2').mask("-999-999-9999");
    $('#txtFax').mask("-999-999-9999");
    // $('#txtZip').mask("99999");
    // $('#txtDBAZip').mask("99999");
    // $('#txtBillingZip').mask("99999");
    // $('#txtShippingZip').mask("99999");
    $('#txtSSN').mask("999-99-9999");

    // $('#txtContactPhone1').mask("-999-999-9999");
    // $('#txtContactPhone2').mask("-999-999-9999");
    $('#txtContactFax').mask("-999-999-9999");
    // $('#txtContactMobileNumber').mask("-999-999-9999");

    // $('#txtContactPhone12').mask("-999-999-9999");
    // $('#txtContactPhone22').mask("-999-999-9999");
    $('#txtContactFax2').mask("-999-999-9999");
    // $('#txtContactMobileNumber2').mask("-999-999-9999");

    $('#txtRoutingNo').mask("999999999");
    $('#txtWRoutingNo').mask("999999999");

    $('#txtBusinessDate').mask("99/9999");

    $('#txtCountry').change(function () {
        var country = document.getElementById("txtCountry");
        var country_selectedText = country.options[country.selectedIndex].text;
        var country_selectedValue = country.options[country.selectedIndex].value;
        var country_code = country.options[country.selectedIndex].getAttribute('data-code');
        var usph_country_mask = "999-999-9999";
        var cn_country_mask = "9-999-999-9999";
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $('#txtCountrySelected').val(country_code);

        $('#state_us').hide();
        $('#state_ph').hide();
        $('#state_cn').hide();

        if (country_selectedValue == "Philippines") {
            $('#txtZip').unmask();
            $('#txtZip').mask(ph_zip_mask);
            $('#state_ph').show();
        } else if (country_selectedValue == "United States") {
            $('#txtZip').unmask();
            $('#txtZip').mask(us_zip_mask);
            $('#state_us').show();
        }

        if (country_selectedValue == "China") {
            $('#txtZip').unmask();
            $('#txtZip').mask(cn_zip_mask);
            $('#state_cn').show();
        }

        if (country_selectedValue == "China") {
            $('#txtPhoneNumber').unmask(usph_country_mask);
            $('#txtPhoneNumber').mask(cn_country_mask);
            $('#txtPhoneNumber2').unmask(usph_country_mask);
            $('#txtPhoneNumber2').mask(cn_country_mask);
            $('#txtContactMobileNumber').unmask(usph_country_mask);
            $('#txtContactMobileNumber').mask(cn_country_mask);
            $('.phone-format').unmask(usph_country_mask);
            $('.phone-format').mask(cn_country_mask);
        } else {
            $('#txtPhoneNumber').unmask(cn_country_mask);
            $('#txtPhoneNumber').mask(usph_country_mask);
            $('#txtPhoneNumber2').unmask(cn_country_mask);
            $('#txtPhoneNumber2').mask(usph_country_mask);
            $('#txtContactMobileNumber').unmask(cn_country_mask);
            $('#txtContactMobileNumber').mask(usph_country_mask);
            $('.phone-format').unmask(cn_country_mask);
            $('.phone-format').mask(usph_country_mask);
        }

        jQuery("label[for='BusinessPhone']").html(country_code);
        jQuery("label[for='ContactPhone']").html(country_code);

    });

    $('#txtCountry').trigger('change');

    $('#txtDBACountry').change(function () {
        var country = document.getElementById("txtDBACountry");
        var country_selectedText = country.options[country.selectedIndex].text;
        var country_selectedValue = country.options[country.selectedIndex].value;
        var country_code = country.options[country.selectedIndex].getAttribute('data-code');
        var usph_country_mask = "999-999-9999";
        var cn_country_mask = "9-999-999-9999";
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $('#dba_state_us').hide();
        $('#dba_state_ph').hide();
        $('#dba_state_cn').hide();

        if (country_selectedValue == "United States") {
            $('#txtDBAZip').unmask();
            $('#txtDBAZip').mask(us_zip_mask);
            $('#dba_state_us').show();
        }
        if (country_selectedValue == "Philippines") {
            $('#txtDBAZip').unmask();
            $('#txtDBAZip').mask(ph_zip_mask);
            $('#dba_state_ph').show();
        }
        if (country_selectedValue == "China") {
            $('#txtDBAZip').unmask();
            $('#txtDBAZip').mask(cn_zip_mask);
            $('#dba_state_cn').show();
        }

        if (country_selectedValue == "China") {
            $('#txtPhone1').unmask(usph_country_mask);
            $('#txtPhone1').mask(cn_country_mask);
            $('#txtPhone2').unmask(usph_country_mask);
            $('#txtPhone2').mask(cn_country_mask);

            $('#txtContactPhone1').unmask(usph_country_mask);
            $('#txtContactPhone1').mask(cn_country_mask);
            $('#txtContactPhone2').unmask(usph_country_mask);
            $('#txtContactPhone2').mask(cn_country_mask);
            $('#txtContactMobileNumber').unmask(usph_country_mask);
            $('#txtContactMobileNumber').mask(cn_country_mask);

            $('#txtContactPhone12').unmask(usph_country_mask);
            $('#txtContactPhone12').mask(cn_country_mask);
            $('#txtContactPhone22').unmask(usph_country_mask);
            $('#txtContactPhone22').mask(cn_country_mask);
            $('#txtContactMobileNumber2').unmask(usph_country_mask);
            $('#txtContactMobileNumber2').mask(cn_country_mask);

        } else {
            $('#txtPhone1').unmask(cn_country_mask);
            $('#txtPhone1').mask(usph_country_mask);
            $('#txtPhone2').unmask(cn_country_mask);
            $('#txtPhone2').mask(usph_country_mask);

            $('#txtContactPhone1').unmask(cn_country_mask);
            $('#txtContactPhone1').mask(usph_country_mask);
            $('#txtContactPhone2').unmask(cn_country_mask);
            $('#txtContactPhone2').mask(usph_country_mask);
            $('#txtContactMobileNumber').unmask(cn_country_mask);
            $('#txtContactMobileNumber').mask(usph_country_mask);

            $('#txtContactPhone12').unmask(cn_country_mask);
            $('#txtContactPhone12').mask(usph_country_mask);
            $('#txtContactPhone22').unmask(cn_country_mask);
            $('#txtContactPhone22').mask(usph_country_mask);
            $('#txtContactMobileNumber2').unmask(cn_country_mask);
            $('#txtContactMobileNumber2').mask(usph_country_mask);
        }

        jQuery("label[for='BusinessPhone']").html(country_code);
        jQuery("label[for='ContactPhone']").html(country_code);

    });

    $('#txtDBACountry').trigger('change');

    $('#txtBillingCountry').change(function () {
        var country = document.getElementById("txtBillingCountry");
        var country_selectedText = country.options[country.selectedIndex].text;
        var country_selectedValue = country.options[country.selectedIndex].value;
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $('#bill_state_us').hide();
        $('#bill_state_ph').hide();
        $('#bill_state_cn').hide();

        if (country_selectedValue == "United States") {
            $('#txtBillingZip').unmask();
            $('#txtBillingZip').mask(us_zip_mask);
            $('#bill_state_us').show();
        }
        if (country_selectedValue == "Philippines") {
            $('#txtBillingZip').unmask();
            $('#txtBillingZip').mask(ph_zip_mask);
            $('#bill_state_ph').show();
        }
        if (country_selectedValue == "China") {
            $('#txtBillingZip').unmask();
            $('#txtBillingZip').mask(cn_zip_mask);
            $('#bill_state_cn').show();
        }
    });

    $('#txtBillingCountry').trigger('change');

    $('#txtShippingCountry').change(function () {
        var country = document.getElementById("txtShippingCountry");
        var country_selectedText = country.options[country.selectedIndex].text;
        var country_selectedValue = country.options[country.selectedIndex].value;
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $('#ship_state_us').hide();
        $('#ship_state_ph').hide();
        $('#ship_state_cn').hide();

        if (country_selectedValue == "United States") {
            $('#txtShippingZip').unmask();
            $('#txtShippingZip').mask(us_zip_mask);
            $('#ship_state_us').show();
        }
        if (country_selectedValue == "Philippines") {
            $('#txtShippingZip').unmask();
            $('#txtShippingZip').mask(ph_zip_mask);
            $('#ship_state_ph').show();
        }
        if (country_selectedValue == "China") {
            $('#txtShippingZip').unmask();
            $('#txtShippingZip').mask(cn_zip_mask);
            $('#ship_state_cn').show();
        }
    });

    $('#txtShippingCountry').trigger('change');

    $('#txtMailingCountry').change(function () {
        var country = document.getElementById("txtMailingCountry");
        var country_selectedText = country.options[country.selectedIndex].text;
        var country_selectedValue = country.options[country.selectedIndex].value;
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $('#state_mail_us').hide();
        $('#state_mail_ph').hide();
        $('#state_mail_cn').hide();

        if (country_selectedValue == "United States") {
            $('#txtMailingZip').unmask();
            $('#txtMailingZip').mask(us_zip_mask);
            $('#state_mail_us').show();
        }
        if (country_selectedValue == "Philippines") {
            $('#txtMailingZip').unmask();
            $('#txtMailingZip').mask(ph_zip_mask);
            $('#state_mail_ph').show();
        }
        if (country_selectedValue == "China") {
            $('#txtMailingZip').unmask();
            $('#txtMailingZip').mask(cn_zip_mask);
            $('#state_mail_cn').show();
        }
    });
    $('#txtMailingCountry').trigger('change');

    $('#chkDBA').click(function () {
        if (this.checked) {
            $('#dba_tab').hide();
        } else {
            $('#dba_tab').show();
        }
    });

    $('#chkBlling').click(function () {
        if (this.checked) {
            $('#bill_tab').hide();
        } else {
            $('#bill_tab').show();
        }
    });

    $('#chkShipping').click(function () {
        if (this.checked) {
            $('#ship_tab').hide();
        } else {
            $('#ship_tab').show();
        }
    });

    $('#frmMerchant').submit(function () {
        if (document.getElementById('assigntome').checked) {
            $('#selfAssign').val(1);
        } else {
            $('#selfAssign').val(0);
        }

       /*  // if (!validateField('txtFederalTaxID', 'Federal Tax ID is required')) {
        //     return false;
        // }
        if (!validateField('txtCompanyName', 'DBA is required')) {
            return false;
        }


        // if (!validateField('txtDBA', 'Legal Name is required')) {
        //     return false;
        // }
        // if(!validateField('txtMerchantURL','Merchant URL is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtTaxName','Tax Filling Name is required'))
        // {
        //     return false;
        // }

        if (!validateField('txtDBACountry', 'Country is required')) {
            return false;
        }
        if (!validateField('txtDBAAddress1', 'Business Address 1 is required')) {
            return false;
        }
        if (!validateField('txtDBACity', 'City is required')) {
            return false;
        }
        if (!validateField('txtDBAZip', 'Zip is required')) {
            return false;
        }
        if (!validateField('txtDBAPhone1', 'Business Phone 1 is required')) {
            return false;
        }
        // if (!validateField('txtEmail', 'Email is required')) {
        //     return false;
        // }
        // if (!validateField('txtBankName', 'Deposit Bank Account No. is required')) {
        //     return false;
        // }
        // if (!validateField('txtBankAccountNo', 'Deposit Bank Account No. is required')) {
        //     return false;
        // }
        // if (!validateField('txtRoutingNo', 'Deposit Routing No. is required')) {
        //     return false;
        // }
        // if (!validateField('txtWBankName', 'Withdrawal Bank Account No. is required')) {
        //     return false;
        // }
        // if (!validateField('txtWBankAccountNo', 'Withdrawal Bank Account No. is required')) {
        //     return false;
        // }
        // if (!validateField('txtWRoutingNo', 'Withdrawal Routing No. is required')) {
        //     return false;
        // }


        // if ($('#chkDBA').prop("checked") == false) {
        //     if (!validateField('txtDBAAddress1', 'DBA Business Address 1 is required')) {
        //         return false;
        //     }
        //     if (!validateField('txtDBACity', 'DBA City is required')) {
        //         return false;
        //     }
        //     if (!validateField('txtDBAZip', 'DBA Zip is required')) {
        //         return false;
        //     }
        // }
        // if ($('#chkBlling').prop("checked") == false) {
        //     if (!validateField('txtBillingAddress1', 'Billing Business Address 1 is required')) {
        //         return false;
        //     }
        //     if (!validateField('txtBillingCity', 'Billing City is required')) {
        //         return false;
        //     }
        //     if (!validateField('txtBillingZip', 'Billing Zip is required')) {
        //         return false;
        //     }
        // }
        // if ($('#chkShipping').prop("checked") == false) {
        //     if (!validateField('txtShippingAddress1', 'Shipping Business Address 1 is required')) {
        //         return false;
        //     }
        //     if (!validateField('txtShippingCity', 'Shipping City is required')) {
        //         return false;
        //     }
        //     if (!validateField('txtShippingZip', 'Shipping Zip is required')) {
        //         return false;
        //     }
        // }
        // if(!validateField('txtPosition','Title is required'))
        // {
        //     return false;
        // }
        if (!validateField('txtTitle', 'Title is required')) {
            return false;
        }
        if (!validateField('txtFirstName', 'First Name is required')) {
            return false;
        }
        if (!validateField('txtLastName', 'Last Name is required')) {
            return false;
        }
        // if(!validateField('txtSSN','SSN is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtIssuedID','Drivers License / Identification Card No. is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtContactPhone1','Contact Phone 1 is required'))
        // {
        //     return false;
        // }
        // if (!validateField('txtContactMobileNumber', 'Mobile Number is required')) {
        //     return false;
        // }

        if (typeof txtEmail !== "undefined") {
            if ($('#txtEmail').val() != "") {
                if (!isEmail($('#txtEmail').val())) {
                    alert('Invalid email format');
                    return false;
                }
            }
        }

        if (typeof txtContactEmail !== "undefined") {
            if ($('#txtContactEmail').val() != "") {
                if (!isEmail($('#txtContactEmail').val())) {
                    alert('Invalid contact email format');
                    return false;
                }
            }
        }

        if (typeof txtContactEmail2 !== "undefined") {
            if ($('#txtContactEmail2').val() != "") {
                if (!isEmail($('#txtContactEmail2').val())) {
                    alert('Invalid email format');
                    return false;
                }
            }
        } */

        if (!validReqFields()) {
            $([document.documentElement, document.body]).animate({
                scrollTop: $('.has-error').first().offset().top - 200
            }, 300);
            
            return false;
        }

        $('#txtCopyDBA').val($('#chkDBA').prop("checked"));
        $('#txtCopyBill').val($('#chkBlling').prop("checked"));
        $('#txtCopyShip').val($('#chkShipping').prop("checked"));

        $('#btnCreateMerchant').attr("disabled", true);
        document.getElementById("btnCreateMerchant").innerHTML = 'Please wait...';

    });

    $('#btnCreateMerchant').on('click', function () {
        var errors = {};
        for (var i = 0; i < elems1.length; i++) {
            var ctr = elems1[i];
            var element = document.getElementById('OthersDescription' + ctr);
            if (element) {
                if ($('#OthersDescription' + ctr).val() == "") {
                    var id = "OthersDescription" + ctr;
                    var msg = "File name is needed.";
                    errors[id] = msg;
                } else {
                    document.getElementById('OthersDescription' + ctr).style.removeProperty('border');
                    $('#OthersDescription' + ctr + '-error').text('');
                }
            }
        }

        if (!validateReqFields(errors)) {
            merchantPreview();
            return false;
        } else {
            merchantPreview();
        } /* return true; */
    });

    $('#txtUplinePartnerType').change(function () {
        var partner_type = document.getElementById("txtUplinePartnerType");
        if (partner_type.selectedIndex >= 0) {
            var url = '/partners/getUplineListByPartnerTypeId/' + $(this).val();
            $.ajax({
                url: url
            }).done(function (items) {
                var option = "";
                $.each(items, function (key, item) {
                    option += '<option value="' + item.parent_id + '">' + item.dba + ' - ' + item.upline_partner + ' - ' + item.partner_id_reference + '</option> ';
                });
                $('#txtUplineId').empty(); //remove all child nodes
                var newOption = option;
                $('#txtUplineId').append(newOption);
                $('#txtUplineId').trigger("chosen:updated");
            });
        } else {
            $('#txtUplineId').empty(); //remove all child nodes                    
        }
    });
    $('#txtUplinePartnerType').trigger('change');

    /* $('#togBtnUnpaid').change(function () {
        if ($(this).is(':checked')) {
            $(this).attr('checked', true);
            $('#txtTogBtnUnpaid').val('on');
        } else {
            $(this).attr('checked', false);
            $('#txtTogBtnUnpaid').val('off');
        }
    });

    $('#togBtnPaid').change(function () {
        if ($(this).is(':checked')) {
            $(this).attr('checked', true);
            $('#txtTogBtnPaid').val('on');
        } else {
            $(this).attr('checked', false);
            $('#txtTogBtnPaid').val('off');
        }
    });

    $('#togBtnSMTP').change(function () {
        if ($(this).is(':checked')) {
            $(this).attr('checked', true);
            $('#txtTogBtnSMTP').val('on');
        } else {
            $(this).attr('checked', false);
            $('#txtTogBtnSMTP').val('off');
        }
    }); */

    $('#togBtnAutoEmailer').change(function () {
        if ($(this).is(':checked')) {
            $(this).attr('checked', true);
            $('#txtTogBtnAutoEmailer_preview_off').prop('checked', false);
            $('#txtTogBtnAutoEmailer_preview_on').prop('checked', true);
            $('#txtTogBtnAutoEmailer').val('on');
        } else {
            $(this).attr('checked', false);
            $('#txtTogBtnAutoEmailer_preview_on').prop('checked', false);
            $('#txtTogBtnAutoEmailer_preview_off').prop('checked', true);
            $('#txtTogBtnAutoEmailer').val('off');
        }
    });

    $(".btnSaveAsDraft").click(function() {
        var formData = new FormData( $("#frmMerchant")[0] );

        showLoadingAlert('Saving as Draft...');
        $.ajax({
            type: "POST",
            url: '/drafts/store',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                closeLoading();
                if (data.success) {
                    showSuccessMessage(data.message, '/merchants/draft_merchant');
                } else {
                    showWarningMessage(data.message);
                }
            },
        });
    });


    $(".btnSaveAsDraftBranch").click(function() {
        var formData = new FormData( $("#frmMerchant")[0] );

        showLoadingAlert('Saving as Draft...');
        $.ajax({
            type: "POST",
            url: '/drafts/store',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                closeLoading();
                if (data.success) {
                    showSuccessMessage(data.message, '/merchants/draft_branch');
                } else {
                    showWarningMessage(data.message);
                }
            },
        });
    });

    $('#tblDraft tbody').on( 'click', 'button.icon-delete', function () {
        var table = $('#tblDraft').DataTable();
        var docu_id = $(this).parents('tr').data('docu-id');

        $('#file' + docu_id).removeClass('hide');
        table
            .row( $(this).parents('tr') )
            .remove()
            .draw();

        if ($('#countFile').length == 0) {
            $('#txtDraftFile').val(0);
        }
    } );

});

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


function isEmail(email) {
    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!regex.test(email)) {
        return false;
    }
    return true;
}

function addContact() {
    var count = $("#addBtnContact.added").length;
    var ctr = 0;
    var country = $('#txtCountrySelected').val();
    var usph_country_mask = "999-999-9999";
    var cn_country_mask = "9-999-999-9999";

    if ($('#countContact')) {
        ctr = elems[elems.length - 1] + 1;
    } else {
        if (count <= 0) {
            ctr = count + 2;
        } else if (count > 0) {
            // ctr = count + 1;
            ctr = elems[elems.length - 1] + 1;
        }
    }
    elems.push(ctr);

    /* var add_contact = '<div class="added" id="added-' + ctr + '"><div class="box-header with-border">' +
        '<h3 class="box-title" style="color:#3c8dbc;"><b>Other Contact</b></h3>' +
        '<button class="close" type="button" onclick="closeContact(' + ctr + ');">&times;</button>' +
        '</div>' +
        '<div class="custom-contact-wrap-sm row">' +
        '<div class="col-lg-3 col-md-6 col-sm-12 sm-col">' +
        '<div class="form-group">' +
        '<label for="email">Title:</label>' +
        '<input type="text" class="form-control" id="txtPosition' + ctr + '" name="txtPosition' + ctr + '" placeholder="Enter Title">' +
        '</div>' +
        '</div>' +
        '<div class="col-lg-3 col-md-6 col-sm-12 sm-col">' +
        '<div class="form-group">' +
        '<label>First Name: </label>' +
        '<input type="text" class="form-control" id="txtFirstName' + ctr + '" name="txtFirstName' + ctr + '" placeholder="Enter First Name">' +
        '</div>' +
        '</div>' +
        '<div class="col-lg-1 col-md-6 col-sm-12 sm-col">' +
        '<div class="form-group">' +
        '<label>M.I.:</label>' +
        '<input type="text" class="form-control" id="txtMiddleInitial' + ctr + '" name="txtMiddleInitial' + ctr + '" placeholder="MI" maxlength="1">' +
        '</div>' +
        '</div>' +
        '<div class="col-lg-3 col-md-6 col-sm-12 sm-col">' +
        '<div class="form-group">' +
        '<label>Last Name: </label>' +
        '<input type="text" class="form-control" id="txtLastName' + ctr + '" name="txtLastName' + ctr + '" placeholder="Enter Last Name">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="custom-contact-wrap-sm row">' +
        '<div class="col-lg-3 col-md-6 col-sm-12 sm-col">' +
        '<div class="form-group">' +
        '<label for="phone1">Contact Phone 1:</label>' +
        '<div class="input-group ">' +
        '<div class="input-group-addon">' +
        '<label for="ContactPhone">1</label>' +
        '</div>' +
        '<input type="text" class="form-control number-only" id="txtContactPhone1' + ctr + '" name="txtContactPhone1' + ctr + '" placeholder="Enter phone 1">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="col-lg-3 col-md-6 col-sm-12 sm-col">' +
        '<div class="form-group">' +
        '<label for="phone2">Contact Phone 2:</label>' +
        '<div class="input-group ">' +
        '<div class="input-group-addon">' +
        '<label for="ContactPhone">1</label>' +
        '</div>' +
        '<input type="text" class="form-control number-only" id="txtContactPhone2' + ctr + '" name="txtContactPhone2' + ctr + '" placeholder="Enter phone 2">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="col-lg-3 col-md-6 col-sm-12 sm-col">' +
        '<div class="form-group">' +
        '<label for="phone2">Fax:</label>' +
        '<div class="input-group ">' +
        '<div class="input-group-addon">' +
        '<label for="ContactPhone">1</label>' +
        '</div>' +
        '<input type="text" class="form-control number-only" id="txtContactFax' + ctr + '" name="txtContactFax' + ctr + '" placeholder="Enter Fax">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="col-lg-3 col-md-6 col-sm-12 sm-col">' +
        '<div class="form-group">' +
        '<label for="phone2">Mobile Number' +
        '<small>(must be valid)</small> :' +
        '</label>' +
        '<div class="input-group ">' +
        '<div class="input-group-addon">' +
        '<label for="ContactPhone">1</label>' +
        '</div>' +
        '<input type="text" class="form-control number-only" id="txtContactMobileNumber' + ctr + '" name="txtContactMobileNumber' + ctr + '" placeholder="Enter Your Mobile Number">' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="col-lg-3 col-md-12 col-sm-12 sm-col">' +
        '<div class="form-group">' +
        '<label for="email">Email' +
        '<small>(must be valid)</small> :' +
        '</label>' +
        '<input type="email" class="form-control" id="txtContactEmail' + ctr + '" name="txtContactEmail' + ctr + '" placeholder="Enter email">' +
        '</div>' +
        '</div>' +
        '</div></div>'; */
    var add_contact = '<div class="added" id="added-' + ctr + '"><div class="box-header with-border">\
        <h3 class="box-title" style="color:#3c8dbc;"><b>Other Contact</b></h3>\
        <button class="close" type="button" onclick="closeContact(' + ctr + ');">&times;</button>\
        </div>\
        <div class="custom-contact-wrap-sm row">\
            <div class="col-lg-1 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>Title: <span class="required"></span></label>\
                    <input type="text" class="form-control alpha" id="txtTitle' + ctr + '" name="txtTitle' + ctr + '" placeholder="Enter Title">\
                </div>\
            </div>\
            <div class="col-lg-2 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>Contact First Name: <span class="required"></span></label>\
                    <input type="text" class="form-control alpha" id="txtFirstName' + ctr + '" name="txtFirstName' + ctr + '" placeholder="Enter First Name">\
                </div>\
            </div>\
            <div class="col-lg-1 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>Contact M.I.:</label>\
                    <input type="text" class="form-control alpha" id="txtMiddleInitial' + ctr + '" name="txtMiddleInitial' + ctr + '" placeholder="MI" maxlength="1">\
                </div>\
            </div>\
            <div class="col-lg-2 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>Contact Last Name: <span class="required"></span></label>\
                    <input type="text" class="form-control alpha" id="txtLastName' + ctr + '" name="txtLastName' + ctr + '" placeholder="Enter Last Name">\
                </div>\
            </div>\
            <div class="col-lg-2 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>Social Security Number: </label>\
                    <input type="text" class="form-control ssn-format" id="txtSSN' + ctr + '" value="" name="txtSSN' + ctr + '" placeholder="Enter SSN">\
                </div>\
            </div>\
            <div class="col-lg-2 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label for="phone2">Mobile Number: </label>\
                    <div class="input-group ">\
                        <label for="ContactPhone" class="input-group-addon">1</label>\
                        <input type="text" class="form-control number-only phone-format" id="txtContactMobileNumber txtContactMobileNumber' + ctr + '" name="txtContactMobileNumber' + ctr + '" placeholder="Enter Mobile #">\
                    </div>\
                </div>\
            </div>\
        </div>';
    $("#addBtnContact").append(add_contact);
    $('#txtOtherHidden').val(JSON.stringify(elems));

    if (country == '1' || country == '63') {
        $('.phone-format').unmask(cn_country_mask);
        $('.phone-format').mask(usph_country_mask);
    } else if (country == '86') {
        $('.phone-format').unmask(usph_country_mask);
        $('.phone-format').mask(cn_country_mask);
    }
    $('.ssn-format').unmask();
    $('.ssn-format').mask("999-99-9999");
    jQuery("label[for='ContactPhone']").html(country);

    $(".number-only").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $(".alpha").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 86 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if (e.keyCode < 65 || e.keyCode > 90) {
            e.preventDefault();
        }
    });
}

function closeContact(id) {
    var elems = JSON.parse($('#txtOtherHidden').val());
    $('#added-' + id).remove();
    elems.splice(elems.indexOf(parseInt(id)), 1);
    $('#txtOtherHidden').val(JSON.stringify(elems));
}

function validateForm(curActiveHref) {
    var errors = {};
    if (curActiveHref.match('business-info')) {
        // if ($('#txtFederalTaxID').val().trim() == "") {
        //     var id = "txtFederalTaxID";
        //     var msg = "Federal Tax ID is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtFederalTaxID').style.removeProperty('border');
        //     $('#txtFederalTaxID-error').remove();
        // }
        /* if ($('#txtCompanyName').val().trim() == "") {
            var id = "txtCompanyName";
            var msg = "DBA is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtCompanyName').style.removeProperty('border');
            $('#txtCompanyName-error small').text('');
        }
        // if ($('#txtDBA').val().trim() == "") {
        //     var id = "txtDBA";
        //     var msg = "DBA is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtDBA').style.removeProperty('border');
        //     $('#txtDBA-error').remove();
        // }
        // if ($('#txtBankName').val().trim() == "") {
        //     var id = "txtBankName";
        //     var msg = "Deposit Bank Account Name is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtBankName').style.removeProperty('border');
        //     $('#txtBankName-error').remove();
        // }
        // if ($('#txtBankAccountNo').val().trim() == "") {
        //     var id = "txtBankAccountNo";
        //     var msg = "Deposit Bank Account No. is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtBankAccountNo').style.removeProperty('border');
        //     $('#txtBankAccountNo-error').remove();
        // }
        // if ($('#txtRoutingNo').val().trim() == "") {
        //     var id = "txtRoutingNo";
        //     var msg = "Deposit Routing No. is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtRoutingNo').style.removeProperty('border');
        //     $('#txtRoutingNo-error').remove();
        // }
        // if ($('#txtWBankName').val().trim() == "") {
        //     var id = "txtWBankName";
        //     var msg = "Withdrawal Bank Account Name is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtWBankName').style.removeProperty('border');
        //     $('#txtWBankName-error').remove();
        // }
        // if ($('#txtWBankAccountNo').val().trim() == "") {
        //     var id = "txtWBankAccountNo";
        //     var msg = "Withdrawal Bank Account No. is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtWBankAccountNo').style.removeProperty('border');
        //     $('#txtWBankAccountNo-error').remove();
        // }
        // if ($('#txtWRoutingNo').val().trim() == "") {
        //     var id = "txtWRoutingNo";
        //     var msg = "Withdrawal Routing No. is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtWRoutingNo').style.removeProperty('border');
        //     $('#txtWRoutingNo-error').remove();
        // }
        if ($('#txtCountry').val().trim() == "") {
            var id = "txtCountry";
            var msg = "Country is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtCountry').style.removeProperty('border');
            $('#txtCountry-error small').text('');
        }
        if ($('#txtDBAAddress1').val().trim() == "") {
            var id = "txtDBAAddress1";
            var msg = "DBA Address 1 is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtDBAAddress1').style.removeProperty('border');
            $('#txtDBAAddress1-error small').text('');
        }
        if ($('#txtDBACity').val().trim() == "") {
            var id = "txtDBACity";
            var msg = "City is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtDBACity').style.removeProperty('border');
            $('#txtDBACity-error small').text('');
        }
        if ($('#txtDBAZip').val().trim() == "") {
            var id = "txtDBAZip";
            var msg = "Zip is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtDBAZip').style.removeProperty('border');
            $('#txtDBAZip-error small').text('');
        }
        if ($('#txtPhone1').val().trim() == "") {
            var id = "txtPhone1";
            var msg = "Business Phone 1 is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtPhone1').style.removeProperty('border');
            $('#txtPhone1-error small').text('');
        }
        // if ($('#txtEmail').val().trim() == "") {
        //     var id = "txtEmail";
        //     var msg = "Email is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtEmail').style.removeProperty('border');
        //     $('#txtEmail-error').remove();
        // }
        if ($('#txtEmail').val().trim() == "") {
            var id = "txtContactMobileNumber";
            $('#mobileNumber').text('*');
        } else {
            document.getElementById('txtContactMobileNumber').style.removeProperty('border');
            $('#txtContactMobileNumber-error small').text('');
            $('#mobileNumber').text('');
        }
        // if ($('#chkDBA').prop("checked") == false) {
        //     if ($('#txtDBAAddress1').val().trim() == "") {
        //         var id = "txtDBAAddress1";
        //         var msg = "DBA Address 1 is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtDBAAddress1').style.removeProperty('border');
        //         $('#txtDBAAddress1-error').remove();
        //     }
        //     if ($('#txtDBACity').val().trim() == "") {
        //         var id = "txtDBACity";
        //         var msg = "DBA City is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtDBACity').style.removeProperty('border');
        //         $('#txtDBACity-error').remove();
        //     }
        //     if ($('#txtDBAZip').val().trim() == "") {
        //         var id = "txtDBAZip";
        //         var msg = "DBA Zip is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtDBAZip').style.removeProperty('border');
        //         $('#txtDBAZip-error').remove();
        //     }
        // }
        // if ($('#chkBlling').prop("checked") == false) {
        //     if ($('#txtBillingAddress1').val().trim() == "") {
        //         var id = "txtBillingAddress1";
        //         var msg = "Billing Address 1 is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtBillingAddress1').style.removeProperty('border');
        //         $('#txtBillingAddress1-error').remove();
        //     }
        //     if ($('#txtBillingCity').val().trim() == "") {
        //         var id = "txtBillingCity";
        //         var msg = "Billing City is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtBillingCity').style.removeProperty('border');
        //         $('#txtBillingCity-error').remove();
        //     }
        //     if ($('#txtBillingZip').val().trim() == "") {
        //         var id = "txtBillingZip";
        //         var msg = "Billing Zip is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtBillingZip').style.removeProperty('border');
        //         $('#txtBillingZip-error').remove();
        //     }
        // }
        // if ($('#chkShipping').prop("checked") == false) {
        //     if ($('#txtShippingAddress1').val().trim() == "") {
        //         var id = "txtShippingAddress1";
        //         var msg = "Shipping Address 1 is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtShippingAddress1').style.removeProperty('border');
        //         $('#txtShippingAddress1-error').remove();
        //     }
        //     if ($('#txtShippingCity').val().trim() == "") {
        //         var id = "txtShippingCity";
        //         var msg = "Shipping City is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtShippingCity').style.removeProperty('border');
        //         $('#txtShippingCity-error').remove();
        //     }
        //     if ($('#txtShippingZip').val().trim() == "") {
        //         var id = "txtShippingZip";
        //         var msg = "Shipping Zip is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtShippingZip').style.removeProperty('border');
        //         $('#txtShippingZip-error').remove();
        //     }
        // }
        if ($('#txtPhone1').val().trim() != "") {
            if ($('#txtPhone1').val().length == 13 || $('#txtPhone1').val().length == 15) {
                document.getElementById('txtPhone1').style.removeProperty('border');
                $('#txtPhone1-error small').text('');
            } else {
                var id = "txtPhone1";
                var msg = "Invalid Phone Number.";
                errors[id] = msg;
            }
        }
        if ($('#txtEmail').val().trim() != "") {
            if (!isEmail($('#txtEmail').val())) {
                var id = "txtEmail";
                var msg = "Invalid email format.";
                errors[id] = msg;
            } else {
                document.getElementById('txtEmail').style.removeProperty('border');
                $('#txtEmail-error small').text('');
            }
        } */
        // Validation for New Merchant Creation Form
        // if ($('#txtMID').val().trim() == "") {
        //     var id = "txtMID";
        //     var msg = "MID is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtMID').style.removeProperty('border');
        //     $('#txtMID-error small').text('');
        // }

        // $('.txtMID').each(function() {
        //     var ctr = $(this).attr('data-id');
        //     if ($(this).val().trim() == "") {
        //         var id = "txtMID"+ctr;
        //         var msg = "MID is required.";
        //         errors[id] = msg;
        //     } else {
        //         document.getElementById('txtMID'+ctr).style.removeProperty('border');
        //         $('#txtMID'+ctr+'-error small').text('');
        //     }
        // });

        if ($('#txtBusinessName').val().trim() == "") {
            var id = "txtBusinessName";
            var msg = "Business Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtBusinessName').style.removeProperty('border');
            $('#txtBusinessName-error small').text('');
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

        if ($('#txtAddress').val().trim() == "") {
            var id = "txtAddress";
            var msg = "Address is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtAddress').style.removeProperty('border');
            $('#txtAddress-error small').text('');
        }
        if ($('#txtCity').val().trim() == "") {
            var id = "txtCity";
            var msg = "City is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtCity').style.removeProperty('border');
            $('#txtCity-error small').text('');
        }
        if ($('#txtZip').val().trim() == "") {
            var id = "txtZip";
            var msg = "Zip is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtZip').style.removeProperty('border');
            $('#txtZip-error small').text('');
        }

        if ($('#txtPhoneNumber').val().trim() != "") {
            if ($('#txtPhoneNumber').val().length == 12 || $('#txtPhoneNumber').val().length == 14) {
                document.getElementById('txtPhoneNumber').style.removeProperty('border');
                $('#txtPhoneNumber-error small').text('');
            } else {
                var id = "txtPhoneNumber";
                var msg = "Invalid Phone Number.";
                errors[id] = msg;
            }
        }

        /** 
         * Validate Bank Information Confirmation Fields 
         */
        let fieldIds = ['#txtBankRouting', '#txtBankDDA']
        fieldIds.forEach((field) => {
            /** Check if field has value */
            if (!($(field).val() == '' || $(field).val() == null)) {

                /** Validate */ 
                if ($(field).val() != $(field + 'Confirmation').val()) { 
                    let id = field.substring(1, field.length) + 'Confirmation'
                    let message = "Confirmation doesn't match"

                    errors[id] = message
                } else {
                    let id = field.substring(1, field.length) + 'Confirmation'
                    document.getElementById(id).style.removeProperty('border');

                    $(field + 'Confirmation-error small').text('')
                }

            }
        })

        // End Valiadtion for New Merchant Creation Form
    } else if (curActiveHref.match('contact-person')) {
        /* if ($('#txtFirstName').val().trim() == "") {
            var id = "txtFirstName";
            var msg = "First Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtFirstName').style.removeProperty('border');
            $('#txtFirstName-error small').text('');
        }
        if ($('#txtLastName').valremove().trim() == "") {
            var id = "txtLastName";
            var msg = "Last Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtLastName').style.removeProperty('border');
            $('#txtLastName-error small').text('');
        }
        // if ($('#txtContactMobileNumber').val().trim() == "") {
        //     var id = "txtContactMobileNumber";
        //     var msg = "Mobile Number is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtContactMobileNumber').style.removeProperty('border');
        //     $('#txtContactMobileNumber-error').remove();
        // }
        // if ($('#txtContactEmail').val().trim() == "") {
        //     var id = "txtContactEmail";
        //     var msg = "Email is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('txtContactEmail').style.removeProperty('border');
        //     $('#txtContactEmail-error').remove();
        // }
        if ($('#txtContactMobileNumber').val().trim() != "") {
            if ($('#txtContactMobileNumber').val().length == 13 ||
                $('#txtContactMobileNumber').val().length == 15) {
                document.getElementById('txtContactMobileNumber').style.removeProperty('border');
                $('#txtContactMobileNumber-error small').text('');
            } else {
                var id = "txtContactMobileNumber";
                var msg = "Invalid phone number.";
                errors[id] = msg;
            }
        }
        if ($('#txtEmail').val().trim() == "" && $('#txtContactMobileNumber').val().trim() == "") {
            var id = "txtContactMobileNumber";
            var msg = "Merchant must have either Business Email or Mobile Number.";
            errors[id] = msg;
        } else {
            document.getElementById('txtContactMobileNumber').style.removeProperty('border');
            $('#txtContactMobileNumber-error small').text('');
        }
        if ($('#txtContactEmail').val().trim() != "") {
            if (!isEmail($('#txtContactEmail').val())) {
                var id = "txtContactEmail";
                var msg = "Invalid email format.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactEmail').style.removeProperty('border');
                $('#txtContactEmail-error small').text('');
            }
        } */
        // Validation for New Merchant Creation Form
        if ($('#txtTitle').val().trim() == "") {
            var id = "txtTitle";
            var msg = "Title is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtTitle').style.removeProperty('border');
            $('#txtTitle-error small').text('');
        }

        if ($('#txtFirstName').val().trim() == "") {
            var id = "txtFirstName";
            var msg = "First Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtFirstName').style.removeProperty('border');
            $('#txtFirstName-error small').text('');
        }
        if ($('#txtLastName').val().trim() == "") {
            var id = "txtLastName";
            var msg = "Last Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtLastName').style.removeProperty('border');
            $('#txtLastName-error small').text('');
        }

        if ($('#txtContactMobileNumber').val().trim() == "" &&
            $('#txtEmail').val().trim() == "") {
            var id = "txtEmail";
            var msg = "User must have either email address or mobile number.";
            errors[id] = msg;
            var id = "txtContactMobileNumber";
            var msg = "User must have either email address or mobile number.";
            errors[id] = msg;
        } else {
            document.getElementById('txtContactMobileNumber').style.removeProperty('border');
            $('#txtContactMobileNumber-error small').text('');
            document.getElementById('txtEmail').style.removeProperty('border');
            $('#txtEmail-error small').text('');
        }
        if ($('#txtEmail').val().trim() != "") {
            if (!isEmail($('#txtEmail').val())) {
                var id = "txtEmail";
                var msg = "Invalid email format.";
                errors[id] = msg;
            } else {
                document.getElementById('txtEmail').style.removeProperty('border');
                $('#txtEmail-error small').text('');
            }
        }
        if ($('#txtContactMobileNumber').val().trim() != "") {
            if ($('#txtContactMobileNumber').val().length == 12 ||
                $('#txtContactMobileNumber').val().length == 14) {
                document.getElementById('txtContactMobileNumber').style.removeProperty('border');
                $('#txtContactMobileNumber-error small').text('');
            } else {
                var id = "txtContactMobileNumber";
                var msg = "Invalid phone number.";
                errors[id] = msg;
            }
        }

        // End Validation for New Merchant Creation Form
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
            $('#' + key + '-error small').text(value);
            // $('#' + key).after('<span id="' + key + '-error" style="color:red"><small>' + value + '</small></span>');
            // }
        }
        return false;
    }
}

function addFile() {
    var count = $("#addBtnFile .added1").length;
    var ctr = 0;

    if (count <= 0) {
        ctr = count + 2;
    } else if (count > 0) {
        // ctr = count + 1;
        ctr = elems1[elems1.length - 1] + 1;
    }
    elems1.push(ctr);

    var add_file = '<div class="col-lg-3 col-md-6 col-sm-12 sm-col added1" id="added1-' + ctr + '">' +
        '<div class="form-group">' +
        '<button class="close" type="button" onclick="closeFile(' + ctr + ');">&times;</button>' +
        '<label>OTHERS:</label>' +
        '<input type="file" id="fileUploadOthers' + ctr + '" name="fileUploadOthers' + ctr + '" accept="application/pdf,image/x-png,image/jpeg">' +
        '</div>' +
        '<button class="btn btn-sm btn-danger clear-input" data-file_id="fileUploadOthers' + ctr + '" style="display: none;"><i class="fa fa-trash"></i>&nbsp;Clear Input</button>' +
        '<div class="form-group">' +
        '<label>Enter File Name:&nbsp;</label>' +
        '<input type="text" id="OthersDescription' + ctr + '" name="OthersDescription' + ctr + '" value="File#'+ctr+'">' +
        '<br><span id="OthersDescription' + ctr + '-error" style="color:red;"><small></small></span>' +
        '</div>' +
        '</div>';
    $("#addBtnFile").append(add_file);
    $('#txtOtherHidden1').val(JSON.stringify(elems1));
}

function closeFile(id) {
    var elems1 = JSON.parse($('#txtOtherHidden1').val());
    $('#added1-' + id).remove();
    elems1.splice(elems1.indexOf(parseInt(id)), 1);
    $('#txtOtherHidden1').val(JSON.stringify(elems1));
}

function validateData(table, field, value, id, includeStatus, prefix, message) {
    //var fieldValue = value.value;
    // if(fieldValue.trim()==""){
    //     alert('Field should not be empty');
    //     value.focus();
    //     value.value='';
    //     return false;    
    // }
    
    $('.btnNext').attr("disabled", true);
    $.getJSON('/partners/validateField/' + table + '/' + field + '/' + value.value + '/' + id + '/' + includeStatus + '/' + prefix, null, function (data) {
        if (data) {
            alert(message);
            value.value = '';
            value.focus();

            $('.mainnav > .active').prev('li').find('a').trigger('click');
            $('.progressbar > .active').last().removeClass('active')
            
            $('.btnNext').attr("disabled", false);
            return false;
        } else {
            $('.btnNext').attr("disabled", false);
            return value.value;
        }
    });
}

function merchantPreview() {
    var add_trow = '';
    var selected = '';

    if ($('#txtOtherHidden').val()) {
        var data = JSON.parse($('#txtOtherHidden').val());
        data.forEach(id => {
            if ($('#contact_' + id).length == 0) {
                add_trow += '<tr id="contact_' + id + '">\
                    <td><i >' + id +  '</i></td>\
                    <td><i id="txtFirstName' + id + '_preview" class="view"></i></td>\
                    <td><i id="txtMiddleInitial' + id + '_preview" class="view"></i></td>\
                    <td><i id="txtLastName' + id + '_preview" class="view"></i></td>\
                    <td><i id="txtContactMobileNumber' + id + '_preview" class="view"></i></td>\
                    <td><i id="txtSSN' + id + '_preview" class="view"></i></td>\
                    </tr>';
            }
        });
    }
    $(add_trow).insertAfter($('#first-contact-merchant').closest('tr'));

    $.each($('#frmMerchant').serializeArray(), function(i, field) {
        if (field.name != 'languages[]') {
            $('#' + field.name + '_preview').text(field.value);
    
            if (field.name == 'txtState') {
                $('#' + field.name + '_preview').text($('#txtState option:selected').text());
            } else if (field.name == 'txtOwnership') {
                $('#' + field.name + '_preview').text($('#txtOwnership option:selected').text());
            } else if (field.name == 'business_industry') {
                $('#' + field.name + '_preview').text($('#business_industry option:selected').text());
            }
        }
    });

    $(".languages :selected").map(function(i, el) {
        selected += '-' + $(el).text() + ' ';
    });
    $('#languages_preview').text(selected);
    
    if (document.getElementById('creditcardclient').checked) {
        $('#creditcardclient_preview').attr('checked', true);
    } else {
        $('#creditcardclient_preview').attr('checked', false);
    }
    
    if (document.getElementById('assigntome').checked) {
        $('#parent_preview').text($('label[for="assigntome"]').after().text());
    } else {
        $('#parent_preview').text($('#txtUplineId option:selected').text());
    }
}

$('#copy_to_shipping').on('change', function() {
    if (this.checked) {
        $('#ship_tab').addClass('hidden')
    } else {
        $('#ship_tab').removeClass('hidden')
    }
})

$('#copy_to_shipping').trigger('change');

$('#copy_to_billing').on('change', function() {
    if (this.checked) {
        $('#bill_tab').addClass('hidden')
    } else {
        $('#bill_tab').removeClass('hidden')
    }
})

$('#copy_to_billing').trigger('change');

$('#copy_to_mailing').on('change', function() {
    if (this.checked) {
        $('#mail_tab').addClass('hidden')
    } else {
        $('#mail_tab').removeClass('hidden')
    }
})

$('#copy_to_mailing').trigger('change');


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
            $('#' + el.id).parents('.form-group').removeClass('has-error');
            $('#' + city_id).prop('disabled', false);
            $('#' + state_id).prop('disabled', false);

        }).fail(function(data) {
            $('#' + el.id).parents('.form-group').addClass('has-error');
            $('#' + el.id + '-error small').text('Error, not a US zip code.'); 
            $('#' + el.id).val('');
            $('#' + city_id).prop('disabled', false);
            $('#' + state_id).prop('disabled', false);
        });
    }
}



window.isEmail = isEmail;
window.validateField = validateField;
window.addContact = addContact;
window.closeContact = closeContact;
window.addFile = addFile;
window.closeFile = closeFile;
window.validateData = validateData;
window.merchantPreview = merchantPreview;
window.isValidZip = isValidZip;