import swal from "sweetalert2";

var elems = [];
var elems1 = [];

$('.select2').select2({
    templateSelection: formatSelect2,
    templateResult: formatSelect2Result
})

var str = $('#taxIDNumber').text().trim();
if (str != '') {
    var rep = str.replace(/[\d.\-](?=[\d.\-]{4})/g, "X");
    var index = 2;
    var newTaxStr = rep.substr(0, index) + '-' + rep.substr(index + 1);
    $('#taxIDNumber').text(newTaxStr);
}

if ($('#countContact.title')) {
    for (let index = 1; index <= $('#countContact.title').length; index++) {
        elems.push(index);
    }
    $('#txtOtherHidden').val(JSON.stringify(elems));
}

// $('.datatables').dataTable();

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

$('.list-tab').click(function(e) {
    var curActive = $('.nav li a').parents('.nav');
    var curActiveId = curActive.find('li.active a').attr('id');
    $('.' + curActiveId).removeClass("active");
    $(this).addClass("active");
})

$(function () {
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
})

$(document).ready(function () {
    $('#txtSocialSecurityNumber').mask('999-99-9999', {clearIfNotMatch: true})
    $('#txtTaxIdNumber').mask('99-9999999', {clearIfNotMatch: true})
    $('#txtBankRouting').mask('999999999', {clearIfNotMatch: true})
    $('#txtBankRoutingConfirmation').mask('999999999', {clearIfNotMatch: true})

    // $('#txtBusinessPhone1').mask("-999-999-9999");
    // $('#txtBusinessPhone2').mask("-999-999-9999");    
    $('#txtBusinessFax').mask("-999-999-9999");
    //$('#txtBusinessZip').mask("99999");

    //$('#txtMailingZip').mask("99999");
    //$('#txtContactZip1').mask("99999");
    //$('#txtContactZip2').mask("99999");
    $('#txtBusinessDate').mask("99/9999");
    $('#txtTaxID').mask('99-9999999');

    $('#txtContactSSN1').mask("999-99-9999");
    // $('#txtContactPhone1_1').mask("-999-999-9999");
    // $('#txtContactPhone1_2').mask("-999-999-9999");
    // $('#txtContactPhone2_1').mask("-999-999-9999");
    // $('#txtContactPhone2_2').mask("-999-999-9999");

    // $('#txtFax').mask("-999-999-9999");
    $('#txtContactFax1').mask("-999-999-9999");
    $('#txtContactFax2').mask("-999-999-9999");

    // $('#txtContactMobile1_1').mask("-999-999-9999");
    // $('#txtContactMobile1_2').mask("-999-999-9999");
    // $('#txtContactMobile2_1').mask("-999-999-9999");
    // $('#txtContactMobile2_2').mask("-999-999-9999");

    $('#txtContactDOB1').mask("99/99/9999");
    $('#txtContactDOB2').mask("99/99/9999");

    $('#assigntome').change(function () {
        if (document.getElementById('assigntome').checked) {
            $("#txtUplineId").prop("disabled", true);
            $("#txtUplinePartnerType").prop("disabled", true);
            $('#divUpline').hide();
        } else {
            $("#txtUplineId").prop("disabled", false);
            $("#txtUplinePartnerType").prop("disabled", false);
            $('#divUpline').show();
        }
        $('#txtPartnerTypeId').trigger('change');
    });
    $('#assigntome').trigger('change');

    $('#state_ph').hide();
    $('#state_cn').hide();

    $('#txtOwnership').change(function () {
        var ownership = document.getElementById("txtOwnership");
        var ownership_selectedText = ownership.options[ownership.selectedIndex].text;
        var ownership_selectedValue = ownership.options[ownership.selectedIndex].value;
        var ein_mask = "99-9999999";
        var ssn_mask = "999-99-9999";
        if (ownership_selectedText == "Individual / Sole Proprietorship") {
            $('#txtSSN').unmask(ein_mask);
            $('#txtSSN').mask(ssn_mask, {clearIfNotMatch: true});
            jQuery("label[for='ssn']").html("SSN:<span class=\"required\"></span>");
        } else if (ownership_selectedText == "Partnership") {
            $('#txtSSN').unmask(ssn_mask);
            $('#txtSSN').mask(ein_mask, {clearIfNotMatch: true});
            jQuery("label[for='ssn']").html("EIN:<span class=\"required\"></span>");
        } else if (ownership_selectedText == "Private Corp.") {
            $('#txtSSN').unmask(ssn_mask);
            $('#txtSSN').mask(ein_mask, {clearIfNotMatch: true});
        } else if (ownership_selectedText == "Limited Liability Co.") {
            $('#txtSSN').unmask(ssn_mask);
            $('#txtSSN').mask(ein_mask,{clearIfNotMatch: true});
            jQuery("label[for='ssn']").html("EIN:<span class=\"required\"></span>");
        } else {
            $('#txtSSN').unmask(ein_mask);
            $('#txtSSN').mask(ssn_mask, {clearIfNotMatch: true});
            jQuery("label[for='ssn']").html("EIN:<span class=\"required\"></span>");
        }
    });
    $('#txtOwnership').trigger('change');

    $('#txtCountry').change(function () {
        var country = $('option:selected', this).attr('data-code');
        var url = '/partners/getStateByCountry/' + country;
        var usph_country_mask = "999-999-9999";
        var cn_country_mask = "9-999-999-9999";
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";
        $('#txtContactCountry1').val($('#txtCountry').val());
        $('#txtContactCountry1').trigger('change');
        $.ajax({
            url: url,
        }).done(function (items) {
            let option = "";
            var state = $('#state').val();
            $.each(items.states, function (key, item) {
                if (item.abbr == state) {
                    option += '<option selected value="' + item.abbr + '" data-code="' + item.id + '">' + item.name + '</option> ';
                } else {
                    option += '<option value="' + item.abbr + '" data-code="' + item.id + '">' + item.name + '</option> ';
                }
            });

            if (country == 'US' || country == 'PH') {
                $('#txtBusinessPhone1').unmask(cn_country_mask);
                $('#txtBusinessPhone1').mask(usph_country_mask);
                $('#txtBusinessPhone2').unmask(cn_country_mask);
                $('#txtBusinessPhone2').mask(usph_country_mask);

                $('#txtFax').unmask(cn_country_mask);
                $('#txtFax').mask(usph_country_mask);


                if (country == 'US') {
                    $('#txtBusinessZip').unmask();
                    $('#txtBusinessZip').mask(us_zip_mask);
                } else if (country == 'PH') {
                    $('#txtBusinessZip').unmask();
                    $('#txtBusinessZip').mask(ph_zip_mask);
                }
            }
            if (country == 'CN') {
                $('#txtBusinessPhone1').unmask(usph_country_mask);
                $('#txtBusinessPhone1').mask(cn_country_mask);
                $('#txtBusinessPhone2').unmask(usph_country_mask);
                $('#txtBusinessPhone2').mask(cn_country_mask);

                $('#txtFax').unmask(usph_country_mask);
                $('#txtFax').mask(cn_country_mask);

                $('#txtBusinessZip').unmask();
                $('#txtBusinessZip').mask(cn_zip_mask);
            }

            $('#txtState').empty(); //remove all child nodes
            var newOption = option;
            $('#txtState').append(newOption);
            if($('#txtStateHidden').val()){
                $("#txtState").val($('#txtStateHidden').val());
            }
            $('#txtState').trigger('change');
            jQuery("label[for='businessPhone']").html(items.country[0].country_calling_code);
        });
    });
    $('#txtCountry').trigger('change');

    $('#txtMailingCountry').change(function () {
        var country = $('option:selected', this).attr('data-code');
        var url = '/partners/getStateByCountry/' + country;
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $.ajax({
            url: url,
        }).done(function (items) {
            let option = "";
            var state = $('#mailing_state').val();
            $.each(items.states, function (key, item) {
                if (item.abbr == state) {
                    option += '<option selected value="' + item.abbr + '" data-code="' + item.id + '">' + item.name + '</option> ';
                } else {
                    option += '<option value="' + item.abbr + '" data-code="' + item.id + '">' + item.name + '</option> ';
                }
            });
            if (country == 'US') {
                $('#txtMailingZip').unmask();
                $('#txtMailingZip').mask(us_zip_mask);
            } else if (country == 'CN') {
                $('#txtMailingZip').unmask();
                $('#txtMailingZip').mask(cn_zip_mask);
            } else if (country == 'PH') {
                $('#txtMailingZip').unmask();
                $('#txtMailingZip').mask(ph_zip_mask);
            }
            $('#txtMailingState').empty(); //remove all child nodes
            var newOption = option;
            $('#txtMailingState').append(newOption);
            $('#txtMailingState').trigger("chosen:updated");
            if($('#txtStateMailingHidden').val()){
                $("#txtMailingState").val($('#txtStateMailingHidden').val());
            }
        });
    });
    $('#txtMailingCountry').trigger('change');

    $('#txtBillingCountry').change(function () {
        var country = $('option:selected', this).attr('data-code');
        var url = '/partners/getStateByCountry/' + country;
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $.ajax({
            url: url,
        }).done(function (items) {
            let option = "";
            var state = $('#billing_state').val();
            $.each(items.states, function (key, item) {
                if (item.abbr == state) {
                    option += '<option selected value="' + item.abbr + '" data-code="' + item.id + '">' + item.name + '</option> ';
                } else {
                    option += '<option value="' + item.abbr + '" data-code="' + item.id + '">' + item.name + '</option> ';
                }
            });
            if (country == 'US') {
                $('#txtBillingZip').unmask();
                $('#txtBillingZip').mask(us_zip_mask);
            } else if (country == 'CN') {
                $('#txtBillingZip').unmask();
                $('#txtBillingZip').mask(cn_zip_mask);
            } else if (country == 'PH') {
                $('#txtBillingZip').unmask();
                $('#txtBillingZip').mask(ph_zip_mask);
            }
            $('#txtBillingState').empty(); //remove all child nodes
            var newOption = option;
            $('#txtBillingState').append(newOption);
            $('#txtBillingState').trigger("chosen:updated");
            if($('#txtStateBillingHidden').val()){
                $("#txtBillingState").val($('#txtStateBillingHidden').val());
            }
        });
    });
    $('#txtBillingCountry').trigger('change');

    $('#txtContactCountry1').change(function () {
        var country = $('option:selected', this).attr('data-code');
        var url = '/partners/getStateByCountry/' + country;
        var usph_country_mask = "999-999-9999";
        var cn_country_mask = "9-999-999-9999";
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $.ajax({
            url: url,
        }).done(function (items) {
            let option = "";
            $.each(items.states, function (key, item) {
                option += '<option value="' + item.abbr + '" data-code="' + item.id + '">' + item.name + '</option> ';
            });

            if (country == 'US' || country == 'PH') {
                $('#txtContactPhone1_1').unmask(cn_country_mask);
                $('#txtContactPhone1_1').mask(usph_country_mask);
                $('#txtContactPhone1_2').unmask(cn_country_mask);
                $('#txtContactPhone1_2').mask(usph_country_mask);

                $('#txtContactMobile1_1').unmask(cn_country_mask);
                $('#txtContactMobile1_1').mask(usph_country_mask);
                $('#txtContactMobile1_2').unmask(cn_country_mask);
                $('#txtContactMobile1_2').mask(usph_country_mask);

                if (country == 'US') {
                    $('#txtContactZip1').unmask();
                    $('#txtContactZip1').mask(us_zip_mask);
                } else if (country == 'PH') {
                    $('#txtContactZip1').unmask();
                    $('#txtContactZip1').mask(ph_zip_mask);
                }

            }
            if (country == 'CN') {
                $('#txtContactPhone1_1').unmask(usph_country_mask);
                $('#txtContactPhone1_1').mask(cn_country_mask);
                $('#txtContactPhone1_2').unmask(usph_country_mask);
                $('#txtContactPhone1_2').mask(cn_country_mask);

                $('#txtContactMobile1_1').unmask(usph_country_mask);
                $('#txtContactMobile1_1').mask(cn_country_mask);
                $('#txtContactMobile1_2').unmask(usph_country_mask);
                $('#txtContactMobile1_2').mask(cn_country_mask);

                $('#txtContactZip1').unmask();
                $('#txtContactZip1').mask(cn_zip_mask);
            }

            $('#txtContactState1').empty(); //remove all child nodes
            var newOption = option;
            $('#txtContactState1').append(newOption);
            $('#txtContactState1').trigger("chosen:updated");
            if($('#txtStateContact1Hidden').val()){
                $("#txtContactState1").val($('#txtStateContact1Hidden').val());
            }
            jQuery("label[for='contactPhone1']").html(items.country[0].country_calling_code);
        });
    });
    $('#txtContactCountry1').trigger('change');

    $('#txtUplinePartnerType').change(function () {
        
        var partner_type = document.getElementById("txtUplinePartnerType");
        if (partner_type.selectedIndex >= 0) {
            var url = '/partners/getUplineListByPartnerTypeId/' + $(this).val();
            var optionValue = $('#txtDraftParent').val() != "" ? $('#txtDraftParent').val() : "";
            $.ajax({
                url: url,
            }).done(function (items) {
                let option = "";
                $.each(items, function (key, item) {
                    // option += '<option value="' + item.parent_id + '">' + item.dba + ' - ' + item.upline_partner + ' - ' + item.partner_id_reference + '</option> ';
                    if (optionValue == item.parent_id) {
                        option += '<option data-image="' + item.image +  '" value="' + item.parent_id + '" selected>&nbsp;' + item.partner_id_reference + ' - ' + item.dba + '</option> ';                        
                    } else {
                        option += '<option data-image="' + item.image +  '" value="' + item.parent_id + '">&nbsp;' + item.partner_id_reference + ' - ' + item.dba + '</option> ';
                    }
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
    

    $('#chkSameAsBusiness').click(function () {
        if (this.checked) {
            // var state = $('#txtState').val();
            // var country = $('#txtCountry').val();
            // $('#txtMailingCountry').val(country).trigger('change');

            // $('#txtMailingAddress1').val($('#txtBusinessAddress1').val());
            // $('#txtMailingAddress2').val($('#txtBusinessAddress2').val());
            // $('#txtMailingCity').val($('#txtCity').val());
            // $('#txtMailingZip').val($('#txtBusinessZip').val());
            // var delay = 1000;
            // setTimeout(function() {
            //     $("#txtMailingState").val(state);
            // }, delay);
            $('#chkSameAsBusiness_preview').prop('checked', true);
            $('#divMailingAddress').hide();
        } else {
            // $('#txtMailingCountry').val("China").trigger('change');
            
            // $('#txtMailingAddress1').val("");
            // $('#txtMailingAddress2').val("");
            // $('#txtMailingCity').val("");
            // $('#txtMailingZip').val("");
            $('#chkSameAsBusiness_preview').prop('checked', false);
            $('#divMailingAddress').show();
        }
    });

    $('#chkSameAsBusinessBilling').click(function () {
        if (this.checked) {
            $('#chkSameAsBusinessBilling_preview').prop('checked', true);
            $('#divBillingAddress').hide();
        } else {
            $('#chkSameAsBusinessBilling_preview').prop('checked', false);
            $('#divBillingAddress').show();
        }
    });

    $('#frmPaymentGateway').submit(function () {
        if (!validateField('txtPGName', 'Name is required')) {
            return false;
        }
        if (!validateField('txtPGKey', 'Key is required')) {
            return false;
        }
    });

    $('#frmPartner').submit(function (e) {

        if (document.getElementById('assigntome').checked) {
            $('#selfAssign').val(1);
        } else {
            $('#selfAssign').val(0);
        }

        if (!$('#assigntome').is(":checked")) {
            if ($('#txtUplineId').val() == null) {
                alert('Please select valid partner.');
                return false;
            }
        }

        //e.preventDefault();
        // if ($('#groupType').val() != 1) {
        //     if (!validateField('txtCompanyName', 'DBA is required')) {
        //         return false;
        //     }

        //     // if(!validateField('txtSSN','EIN/SSN is required'))
        //     // {
        //     //     return false;
        //     // }

        //     // if (!validateField('txtDBA', 'DBA is required')) {
        //     //     return false;
        //     // }

        //     if (!validateField('txtBusinessPhone1', 'Business Phone 1 is required')) {
        //         return false;
        //     }

        //     /* if (!validateField('txtEmail', 'Email is required')) {
        //         return false;
        //     } */

        //     if (!validateField('txtBusinessAddress1', 'Business Address 1 is required')) {
        //         return false;
        //     }

        //     if (!validateField('txtCity', 'City is required')) {
        //         return false;
        //     }

        //     if (!validateField('txtBusinessZip', 'Zip is required')) {
        //         return false;
        //     }
        //     // if (!$("#chkSameAsBusiness").is(':checked')) {
        //     //     if (!validateField('txtMailingAddress1', 'Mailing Address 1 is required')) {
        //     //         return false;
        //     //     }

        //     //     if (!validateField('txtMailingCity', 'City is required')) {
        //     //         return false;
        //     //     }

        //     //     if (!validateField('txtMailingZip', 'Zip is required')) {
        //     //         return false;
        //     //     }
        //     // }

        //     if (!validateField('txtContactFirstName1', 'First Name is required')) {
        //         return false;
        //     }

        //     if (!validateField('txtContactLastName1', 'Last Name is required')) {
        //         return false;
        //     }

        //     // if(!validateField('txtContactTitle1','Title is required'))
        //     // {
        //     //     return false;
        //     // }

        //     // if(!validateField('txtContactSSN1','SSN is required'))
        //     // {
        //     //     return false;
        //     // }

        //     // if (!validateField('txtContactDOB1', 'Date of Birth is required')) {
        //     //     return false;
        //     // }

        //     // if(!validateField('txtContactPhone1_1','Contact Phone 1 is required'))
        //     // {
        //     //     return false;
        //     // }

        //     // if(!validateField('txtContactPhone1_2','Contact Phone 2 is required'))
        //     // {
        //     //     return false;
        //     // }

        //     /* if (!validateField('txtContactMobile1_1', 'Mobile is required')) {
        //         return false;
        //     } */

        //     // if(!validateField('txtContactFax1','Fax is required'))
        //     // {
        //     //     return false;
        //     // }

        //     /* if (!validateField('txtContactEmail1', 'Email is required')) {
        //         return false;
        //     } */

        //     // if (!validateField('txtContactHomeAddress1_1', 'Home Address 1 is required')) {
        //     //     return false;
        //     // }

        //     // if (!validateField('txtContactCity1', 'City is required')) {
        //     //     return false;
        //     // }

        //     // if (!validateField('txtContactZip1', 'Zip is required')) {
        //     //     return false;
        //     // }

        //     // if (!isValidDateEx($('#txtContactDOB1').val())) {
        //     //     alert("Please input valid date.")
        //     //     return false;
        //     // }

        //     if ($('#txtContactEmail2').val() != "") {
        //         if ($('#txtContactEmail2').val() == $('#txtContactEmail1').val()) {
        //             alert('Contact 1 and Contact 2 email should not be the same.');
        //             hasError = true;
        //             return false;
        //         }
        //     }

        //     // if ($('#txtOwnershipPercentage1').val() == "") {
        //     //     alert('Percentage should be numeric.');
        //     //     return false;
        //     // }

        //     if ($('#txtOwnershipPercentage1').val().trim() != "") {
        //         if (parseFloat($('#txtOwnershipPercentage1').val()) > 100 || parseFloat($('#txtOwnershipPercentage1').val()) < 0) {
        //             alert("Percentage should be 1-100", 1)
        //             return false;
        //         }
        //     }

        //     /* if($('#txtOwnershipPercentage2').val()=="")
        //     {
        //         alert('Percentage should be numeric.');
        //         return false;
        //     }

        //     if($('#txtOwnershipPercentage2').val().trim() != "") {
        //         if(parseFloat($('#txtOwnershipPercentage2').val()) > 100 || parseFloat($('#txtOwnershipPercentage2').val()) < 0){
        //             alert("Percentage should be 1-100", 1)
        //             return false;
        //         }      
        //     } */
        //     if ($('#txtEmail').val() != "") {
        //         if (!isEmail($('#txtEmail').val())) {
        //             alert('Invalid email format');
        //             return false;
        //         }
        //     }
        //     if (typeof txtContactEmail1 !== "undefined" &&
        //         $('#txtContactEmail1').val() != "") {
        //         if (!isEmail($('#txtContactEmail1').val())) {
        //             alert('Invalid email format');
        //             return false;
        //         }
        //     }

        //     /* if (typeof txtContactEmail2 !== "undefined")
        //     {
        //         if ($('#txtContactEmail2').val()!="")
        //         {
        //             if(!isEmail($('#txtContactEmail2').val())){
        //                 alert('Invalid email format');
        //                 return false;
        //             }
        //         }
        //     } */

        //     if (typeof $('#txtWebsite').val() !== "undefined") {
        //         if ($('#txtWebsite').val() != '') {
        //             if (!ValidURL($('#txtWebsite').val())) {
        //                 alert('Please input valid website URL!');
        //                 return false;
        //             }
        //         }
        //     }
        // }

        if (!validReqFields()) {
            $([document.documentElement, document.body]).animate({
                scrollTop: $('.has-error').first().offset().top - 200
            }, 300);
            
            return false;
        }

        $('button[type="submit"]').attr("disabled", true);
        $('button[type="submit"]').text('Please wait...');
        // document.getElementById("button[type='submit']").innerHTML = 'Please wait...';

    });

    $('#btnCreatePartner').on('click', function () {
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
            // partnerPreview();
            return false;
        // } else {
            // partnerPreview();
        }
        return true;
    });

    $('#frmPartnerInfo').submit(function (e) {
        var errors = {};
        //e.preventDefault();

        if ($('#txtPartnerType').val() != 1) {
            /* if (!validateField('txtCompanyName', 'DBA is required')) {
                return false;
            } */
            if ($('#txtCompanyName').val().trim() == "") {
                var id = "txtCompanyName";
                var msg = "DBA is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtCompanyName').style.removeProperty('border');
                $('#txtCompanyName-error').text('');
            }
            if ($('#txtWebsite').val().trim() != "" 
                && !isValidUrl($('#txtWebsite').val())) {
                var id = "txtWebsite";
                var msg = "Invalid url.";
                errors[id] = msg;
            } else {
                document.getElementById('txtWebsite').style.removeProperty('border');
                $('#txtWebsite-error small').text('');
            }

            // if(!validateField('txtSSN','EIN/SSN is required'))
            // {
            //     return false;
            // }

            // if (!validateField('txtDBA', 'DBA is required')) {
            //     return false;
            // }

            /* if (!validateField('txtBusinessPhone1', 'Business Phone 1 is required')) {
                return false;
            } */
            if ($('#txtBusinessPhone1').val().trim() == "") {
                var id = "txtBusinessPhone1";
                var msg = "Business Phone 1 is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtBusinessPhone1').style.removeProperty('border');
                $('#txtBusinessPhone1-error').text('');
            }

            /* if (!validateField('txtEmail', 'Email is required')) {
                return false;
            } */
            if ($('#contact_mobile').val() == "" && $('#txtEmail').val().trim() == "") {
                var id = "txtEmail";
                var msg = "Partner must have either Business Email or Mobile Number.";
                errors[id] = msg;
            } else {
                document.getElementById('txtEmail').style.removeProperty('border');
                $('#txtEmail-error small').text('');
            }

            /* if (!validateField('txtBusinessAddress1', 'Business Address 1 is required')) {
                return false;
            } */
            if ($('#txtBusinessAddress1').val().trim() == "") {
                var id = "txtBusinessAddress1";
                var msg = "Business Address 1 is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtBusinessAddress1').style.removeProperty('border');
                $('#txtBusinessAddress1-error').text('');
            }


            /* if (!validateField('txtCity', 'City is required')) {
                return false;
            } */
            if ($('#txtCity').val().trim() == "") {
                var id = "txtCity";
                var msg = "City is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtCity').style.removeProperty('border');
                $('#txtCity-error').text('');
            }

            /* if (!validateField('txtBusinessZip', 'Zip is required')) {
                return false;
            } */
            if ($('#txtBusinessZip').val().trim() == "") {
                var id = "txtBusinessZip";
                var msg = "Zip is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtBusinessZip').style.removeProperty('border');
                $('#txtBusinessZip-error').text('');
            }

            // if (!validateField('txtMailingAddress1', 'Mailing Address 1 is required')) {
            //     return false;
            // }

            // if (!validateField('txtMailingCity', 'City is required')) {
            //     return false;
            // }

            // if (!validateField('txtMailingZip', 'Zip is required')) {
            //     return false;
            // }

            /* if (!isEmail($('#txtEmail').val())) {
                alert('Invalid email format');
                return false;
            } */
            if (!$('#txtEmail').val().trim() == "") {
                if (!isEmail($('#txtEmail').val())) {
                    var id = "txtEmail";
                    var msg = "Invalid email format.";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtEmail').style.removeProperty('border');
                    $('#txtEmail-error').text();
                }
            }
        } else {
            if ($('#txtBusinessName').val().trim() == "") {
                var id = "txtBusinessName";
                var msg = "Business Name is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtBusinessName').style.removeProperty('border');
                $('#txtBusinessName-error small').text('');
            }
            if ($('#txtAddressAgent').val().trim() == "") {
                var id = "txtAddressAgent";
                var msg = "Address is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtAddressAgent').style.removeProperty('border');
                $('#txtAddressAgent-error small').text('');
            }
            if ($('#txtCityAgent').val().trim() == "") {
                var id = "txtCityAgent";
                var msg = "City is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtCityAgent').style.removeProperty('border');
                $('#txtCityAgent-error small').text('');
            }
            if ($('#txtZipAgent').val().trim() == "") {
                var id = "txtZipAgent";
                var msg = "Zip is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtZipAgent').style.removeProperty('border');
                $('#txtZipAgent-error small').text('');
            }
            /* if ($('#txtEmailAgent').val().trim() == "") {
                var id = "txtEmailAgent";
                var msg = "Email is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtEmailAgent').style.removeProperty('border');
                $('#txtEmailAgent-error small').text('');
            } */
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
            if ($('#txtEmailAgent').val().trim() != "") {
                if (!isEmail($('#txtEmailAgent').val())) {
                    var id = "txtEmailAgent";
                    var msg = "Invalid Email Format.";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtEmailAgent').style.removeProperty('border');
                    $('#txtEmailAgent-error small').text('');
                }
            }

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
        }
        
        if (!validateReqFields(errors)) {
            return false;
        }
        return true;

    });

    $('#frmRegisterAttachment').submit(function () {
        if (!validateField('txtUploadAttachment', 'Document name is required')) {
            return false;
        }

        if (!validateField('fileUploadAttachment', 'Please select a file')) {
            return false;
        }
    });

    $('#country').change(function () {
        var country = document.getElementById("country");
        var country_selectedText = country.options[country.selectedIndex].text;
        var country_selectedValue = country.options[country.selectedIndex].value;

        $('#state_us').hide();
        $('#state_ph').hide();
        $('#state_cn').hide();

        if (country_selectedValue == "US") {
            $('#state_us').show();
        }
        if (country_selectedValue == "PH") {
            $('#state_ph').show();
        }
        if (country_selectedValue == "CN") {
            $('#state_cn').show();
        }
    });

    $('#txtPaymentType').change(function () {
        var ach = document.getElementById("txtPaymentType");
        var ach_selectedText = ach.options[ach.selectedIndex].text;
        if (ach_selectedText == "ACH") {
            $('#divACH').show();
        } else {
            $('#divACH').hide();
        }
    });
    $('#txtPaymentType').trigger('change');

    $('#btnSavePaymentType').click(function () {
        var payment_type = document.getElementById("txtPaymentType");
        var payment_type_selectedText = payment_type.options[payment_type.selectedIndex].text;
        var payment_type_selectedValue = payment_type.options[payment_type.selectedIndex].value;
        if (payment_type_selectedValue == 1) { //ACH  
            if ($('#txtBankName').val() == "") {
                alert('Please input bank name');
                return false
            }
            if ($('#txtRoutingNumber').val() == "") {
                alert('Please input routing number');
                return false
            }
            if ($('#txtBankAccountNumber').val() == "") {
                alert('Please input bank account number');
                return false
            }
        }

    });

    $('#txtTicketFilter').change(function () {
        var ticket_filter = $('#txtTicketFilter').val();
        var partner_id = $('#txtPartnerID').val();
        $.getJSON('/partners/refreshTicketList/' + ticket_filter + '/' + partner_id, null, function (data) {
            var oTable = $('#tblTicketList').dataTable({
                "bRetrieve": true
            });
            oTable.fnClearTable();
            if (data.length > 0) {
                oTable.fnAddData(data);
            }
        });
    });
    $('#txtTicketFilter').trigger('change');


    $('#txtPartnerTypeId').change(function () {
        var partner_type = document.getElementById("txtPartnerTypeId");
        var partner_type_selectedText = partner_type.options[partner_type.selectedIndex].text;
        var partner_type_selectedValue = partner_type.options[partner_type.selectedIndex].value;

        $('.agent-form').hide();
        $('.non-agent-form').show();
        $('#groupType').val(partner_type_selectedValue);
        jQuery("label[for='partnerType']").html('Partner Type:');

        if (partner_type_selectedValue == 7) { // COMPANY
            $('.assignToMe').addClass('hide');
            $('#divUpline').hide();
            $("#assigntome").prop("checked", true);

            $('#fileUpload3').parent().parent().show();
            $('#fileUpload4').parent().parent().show();
            $('#fileUpload5').parent().parent().hide();
            $('#fileUpload6').parent().parent().hide();
        } else if (partner_type_selectedValue == 4) { // ISO
            $('.assignToMe').removeClass('hide');

            if ($('input[name="system-user"]').val() == '') {
                $('#divUpline').show();
            }

            $('#fileUpload3').parent().parent().show();
            $('#fileUpload4').parent().parent().show();
            $('#fileUpload5').parent().parent().hide();
            $('#fileUpload6').parent().parent().hide();
        } else if (partner_type_selectedValue == 5) { // SUB ISO  
            $('.assignToMe').removeClass('hide');

            if ($('input[name="system-user"]').val() == '') {
                $('#divUpline').show();
            }

            $('#fileUpload3').parent().parent().show();
            $('#fileUpload4').parent().parent().show();
            $('#fileUpload5').parent().parent().hide();
            $('#fileUpload6').parent().parent().hide();
        } else if (partner_type_selectedValue == 1 || partner_type_selectedValue == 2) { // AGENT and SUB AGENT
            $('.assignToMe').removeClass('hide');

            if ($('input[name="system-user"]').val() == '') {
                $('#divUpline').show();
            }

            if (partner_type_selectedValue == 1) {
                jQuery("label[for='partnerType']").html('Group Type:');
                $('.agent-form').show();
                $('.non-agent-form').hide();
            }

            $('#fileUpload3').parent().parent().hide();
            $('#fileUpload4').parent().parent().hide();
            $('#fileUpload5').parent().parent().show();
            $('#fileUpload6').parent().parent().hide();
        } else if (partner_type_selectedValue == 3 || partner_type_selectedValue == 6) { // MERCHANT and LEAD  
            $('.assignToMe').removeClass('hide');
            $('#divAgent').hide();

            if ($('input[name="system-user"]').val() == '') {
                $('#divUpline').show();
            }
        } else {

        }
        $.getJSON('/partners/loadPartnerTypes/' + partner_type_selectedValue, null, function (data) {
            var optionValue = $('#txtDraftParentType').val() != "" ? $('#txtDraftParentType').val() : "";

            $('#txtUplinePartnerType').empty(); //remove all child nodes
            var newOption = $(data);
            $('#txtUplinePartnerType').append(newOption);
            $('#txtUplinePartnerType').trigger("chosen:updated");
            if (optionValue > 0) {
                $('#txtUplinePartnerType').find('option[value="' + optionValue + '"]').attr('selected', true);
            } else {
                $('#txtUplinePartnerType').trigger('change');
            }
        });
    });
    $('#txtPartnerTypeId').trigger('change');

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

    $('#txtCountryAgent').change(function () {
        var country = $('option:selected', this).attr('data-code');
        var url = '/partners/getStateByCountry/' + country;
        var usph_country_mask = "999-999-9999";
        var cn_country_mask = "9-999-999-9999";
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";
        $('#txtCountrySelected').val(country);
        $.ajax({
            url: url,
        }).done(function (items) {
            let option = "";
            var state = $('#state').val();
            $.each(items.states, function (key, item) {
                if (item.abbr == state) {
                    option += '<option selected value="' + item.abbr + '">' + item.name + '</option> ';
                } else {
                    option += '<option value="' + item.abbr + '">' + item.name + '</option> ';
                }
            });

            $('#txtSSNAgent').mask("999-99-9999");

            if (country == 'US' || country == 'PH') {
                $('#txtPhoneNumber').unmask(cn_country_mask);
                $('#txtPhoneNumber').mask(usph_country_mask);
                $('#txtContactMobileNumberAgent').unmask(cn_country_mask);
                $('#txtContactMobileNumberAgent').mask(usph_country_mask);
                $('.phone-format').unmask(cn_country_mask);
                $('.phone-format').mask(usph_country_mask);

                if (country == 'US') {
                    $('#txtZipAgent').unmask();
                    $('#txtZipAgent').mask(us_zip_mask);
                } else if (country == 'PH') {
                    $('#txtZipAgent').unmask();
                    $('#txtZipAgent').mask(ph_zip_mask);
                }
            }
            if (country == 'CN') {
                $('#txtPhoneNumber').unmask(usph_country_mask);
                $('#txtPhoneNumber').mask(cn_country_mask);
                $('#txtContactMobileNumberAgent').unmask(usph_country_mask);
                $('#txtContactMobileNumberAgent').mask(cn_country_mask);
                $('.phone-format').unmask(usph_country_mask);
                $('.phone-format').mask(cn_country_mask);

                $('#txtZipAgent').unmask();
                $('#txtZipAgent').mask(cn_zip_mask);
            }

            $('#txtStateAgent').empty(); //remove all child nodes
            var newOption = option;
            $('#txtStateAgent').append(newOption);
            $('#txtStateAgent').trigger("chosen:updated");
            jQuery("label[for='BusinessPhone']").html(items.country[0].country_calling_code);
            jQuery("label[for='ContactPhone']").html(items.country[0].country_calling_code);
        });
    });
    $('#txtCountryAgent').trigger('change');

    $(".btnSaveAsDraft").click(function() {
        var formData = new FormData( $("#frmPartner")[0] );
            
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
                    showSuccessMessage(data.message, '/partners/management');
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
    });

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
    
});

function validateData(table, field, value, id, includeStatus, prefix, message, tab) {
    //var fieldValue = value.value;
    // if(fieldValue.trim()==""){
    //     alert('Field should not be empty');
    //     value.focus();
    //     value.value='';
    //     return false;    
    // }
    if (value.value != '') {
        $.getJSON('/partners/validateField/' + table + '/' + field + '/' + value.value + '/' + id + '/' + includeStatus + '/' + prefix, null, function (data) {
            if (data) {
                alert(message);
                value.value = '';
                value.focus();

                if ($('#' + tab).trigger('click')) {
                    if ($('.' + tab).hasClass('active')) {
                        $('.' + tab).removeClass('active')
                    }
                }


                return false;
            } else {
                return value.value;
            }
        });
    }
}

function verifyEmail(id) {
    if (confirm("This process will reset the user's current password to verify the email.Proceed?")) {
        showLoadingModal("Sending Email... Please wait.....");
        $.getJSON('/partners/resend-email-verification/' + id, null, function (data) {
            closeLoadingModal();
            alert(data.message);
        });
    }
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

function isValidDateEx(dateString) {
    // First check for the pattern
    if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
        return false;

    // Parse the date parts to integers
    var parts = dateString.split("/");
    var day = parseInt(parts[1], 10);
    var month = parseInt(parts[0], 10);
    var year = parseInt(parts[2], 10);

    // Check the ranges of month and year
    if (year < 1000 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    // Adjust for leap years
    if (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
}

function load_partners() {
    $.getJSON('/partners/getPartnersData', null, function (data) {

        $.each(data, function (key, item) {
            $('#tbl' + key).dataTable().fnDestroy();
            var oTable = $('#tbl' + key).dataTable({
                "lengthMenu": [25, 50, 75, 100 ],
                "bRetrieve": true
            });
            oTable.fnClearTable();
            if (item.length > 0) {
                oTable.fnAddData(item);
            }
        });

    });

}

function UploadAttachment(id, document_id, document_name) {
    $('#txtAttachmentId').val(id);
    $('#txtDocumentId').val(document_id);
    $('#txtDocumentName').val(document_name);
    $('#txtUploadAttachment').val(document_name);

    if (document_id > 0 || document_name != '') {
        $('#divUploadAttachment').hide();
    } else {
        $('#divUploadAttachment').show();
    }
    $('#modalUploadAttachment').modal('show');
    return false;
}

function createPaymentGateway() {
    $('#pgID').val(-1);
    $('#txtPGName').val('');
    $('#txtPGKey').val('');
    $('#editPaymentGateway').modal('show');
}

function editPaymentGateway(id) {
    $.getJSON('/merchants/merchant_payment_gateway/' + id, null, function (data) {
        $('#pgID').val(data['id']);
        $('#txtPGName').val(data['name']);
        $('#txtPGKey').val(data['key']);
        $('#editPaymentGateway').modal('show');
    });
}

function advanceSearchPartners() {
    var curActive = $('.tabs-rectangular li a').parents('.tabs-rectangular');
    var curActiveId = curActive.find('li.active a').attr('id');

    var country = document.getElementById("country");
    var country_selectedText = country.options[country.selectedIndex].text;
    var country_selectedValue = country.options[country.selectedIndex].value;

    if (country_selectedValue == "US") {
        $('#state_us').show();
        var checkboxes = document.getElementsByName("states[]");
        var country = 'United States';
    }
    if (country_selectedValue == "PH") {
        $('#state_ph').show();
        var checkboxes = document.getElementsByName("statesPH[]");
        var country = 'Philippines';
    }
    if (country_selectedValue == "CN") {
        $('#state_cn').show();
        var checkboxes = document.getElementsByName("statesCN[]");
        var country = 'China';
    }

    var states = "";
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            states = states + checkboxes[i].value + ",";
        }
    }

    states = states.substr(0, states.length - 1);

    if (states == '') {
        // alert('Select a state!');
        // return false;
        load_partners();
        $('.adv-close').click();
        return false;
    }

    $('#tbl' + curActiveId).dataTable().fnDestroy();
    if (curActiveId == 7) {
        $('#tbl' + curActiveId).DataTable({
            "lengthMenu": [25, 50, 75, 100 ],
            processing: true,
            serverSide: true,
            ajax: '/partners/advance_partners_search/' + curActiveId + '/' + country + '/' + states,
            columns: [
                {
                    data: 'company_name'
                },
                {
                    data: 'contact'
                },
                {
                    data: 'phone1'
                },
                {
                    data: 'email'
                },
                {
                    data: 'state'
                },
            ]
        });
    } else {
        $('#tbl' + curActiveId).DataTable({
            "lengthMenu": [25, 50, 75, 100 ],
            processing: true,
            serverSide: true,
            ajax: '/partners/advance_partners_search/' + curActiveId + '/' + country + '/' + states,
            columns: [
                {
                    data: 'partners'
                },
                {
                    data: 'company_name'
                },
                {
                    data: 'contact'
                },
                {
                    data: 'phone1'
                },
                {
                    data: 'email'
                },
                {
                    data: 'state'
                },
            ]
        });
    }
    redrawTable('#tbl' + curActiveId);
    $('.adv-close').click();
}

function createPayment() {
    $('#paymentMethodId').val(-1);
    $('#txtPaymentType').val(1);
    $('#txtBankName').val('');
    $('#txtRoutingNumber').val('');
    $('#txtBankAccountNumber').val('');
    $('#chkSetAsDefault').prop('checked', false);
    $('#btnSavePaymentType').val('Create Payment');
    $('#txtPaymentType').trigger('change');
    $('#goetu-billing').modal('show');
}

function editPayment(id) {
    $.getJSON('/partners/details/' + id + '/payment_method', null, function (data) {
        $('#paymentMethodId').val(data.id);
        $('#txtBankName').val(data.bank_name);
        $('#txtPaymentType').val(data.payment_type_id);
        $('#txtRoutingNumber').val(data.routing_number);
        $('#txtBankAccountNumber').val(data.bank_account_number);
        if (data.is_default_payment === 1) {
            $('#chkSetAsDefault').prop('checked', true);
        } else {
            $('#chkSetAsDefault').prop('checked', false);
        }
        $('#btnSavePaymentType').val('Update Payment');
        $('#txtPaymentType').trigger('change');
        $('#goetu-billing').modal('show');
    });

}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function isEmail(email) {
    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!regex.test(email)) {
        return false;
    }
    return true;
}

function ValidURL(str) {
    var pattern = /^(https?:\/\/)?([a-zA-Z0-9]([a-zA-ZäöüÄÖÜ0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}(\/?)$/;
    if (!pattern.test(str)) {
        return false;
    } else {
        return true;
    }
}


function validateEmail(id) {
    var email = $('#' + id).val();
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!re.test(email)) {
        alert('Invalid Email Format!');
        $('#' + id).val('');
    }
}

function addContact(percVal = 0) {
    var count = $("#addBtnContact div.added").length;
    var ctr = 0;
    var code = '';
    var country = $('#txtCountrySelected').val();
    var usph_country_mask = "999-999-9999";
    var cn_country_mask = "9-999-999-9999";
    var us_zip_mask = "99999";
    
    if ($('#countContact.title').length > 0) {
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

    var city = "txtContactCity" + ctr;
    var state = "txtContactState" + ctr;
    var conphone = "'#conphone" + ctr + "'";
    var conmobile = "'#conmobile" + ctr + "'";
    var add_contact = '<div class="added" id="added-' + ctr + '"><div class="box-header with-border">\
        <h3 class="box-title" style="color:#3c8dbc;"><b>Other Contact</b></h3>\
        <button class="close" type="button" onclick="closeContact(' + ctr + ');">&times;</button>\
        </div>\
        <div class="row">\
        <div class="form-group col-md-2">\
        <label>Title: <span class="required"></span></label>\
        <input type="text" class="form-control alpha" id="txtContactTitle'+ ctr +'" name="txtContactTitle'+ ctr +'"\
        placeholder="Enter Title">\
        </div>\
        <div class="form-group col-md-4">\
        <label>First Name: <span class="required"></span></label>\
        <input type="text" class="form-control alpha" id="txtContactFirstName'+ ctr +'" name="txtContactFirstName'+ ctr +'"\
        placeholder="Enter First Name">\
        </div>\
        <div class="form-group col-md-2">\
        <label>M.I.:</label>\
        <input type="text" class="form-control alpha" id="txtContactMiddleInitial'+ ctr +'" name="txtContactMiddleInitial'+ ctr +'"\
        placeholder="MI" maxlength="1">\
        </div>\
        <div class="form-group col-md-4">\
        <label> Name: <span class="required">*</span></label>\
        <input type="text" class="form-control alpha" id="txtContactLastName'+ ctr +'" name="txtContactLastName'+ ctr +'"\
        placeholder="Enter Last Name">\
        </div>\
        </div>\
        <div class="row">\
        <div class="form-group col-md-2">\
        <label>Social Security Number: <span class="required">*</span></label>\
        <input type="text" class="form-control ssn-format" id="txtContactSSN'+ ctr +'" value=""\
        name="txtContactSSN'+ ctr +'" placeholder="Enter Social Security Number">\
        </div>\
        <div class="form-group col-md-2">\
        <label>Percentage of Ownership:</label>\
        <div class="input-group">\
        <input type="text" class="form-control ownerPercentage" name="txtOwnershipPercentage'+ ctr +'" id="txtOwnershipPercentage'+ ctr +'"\
        value="'+percVal+'" placeholder="0" onkeypress="return isNumberKey(event)" onchange="validateOwnershipPerc(this);">\
        <label for="txtOwnershipPercentage'+ ctr +'" class="input-group-addon">%</label>\
        </div>\
        </div>\
        <div class="form-group col-md-2">\
        <label>Date of Birth:</label>\
        <input type="text" class="form-control dob-format" name="txtContactDOB'+ ctr +'" id="txtContactDOB'+ ctr +'"\
        value="" placeholder="MM/DD/YYYY">\
        </div>\
        </div>\
        <div class="row">\
        <div class="col-md-8">\
        <div class="tab-content">\
        <div class="tab-pane active">\
        <div class="row">\
        <div class="form-group col-md-12">\
        <label for="">Home Address: <span class="required"></span></label>\
        <input type="text" class="form-control" id="txtContactHomeAddress'+ ctr +'_1" name="txtContactHomeAddress'+ ctr +'_1" placeholder="Enter address">\
        </div>\
        <div class="form-group col-md-12">\
        <label for="">Home Address 2: <span class="required"></span></label>\
        <input type="text" class="form-control" id="txtContactHomeAddress'+ ctr +'_2" name="txtContactHomeAddress'+ ctr +'_2" placeholder="Enter address 2">\
        </div>\
        <div class="form-group col-md-6">\
        <label for="txtCountry">Country:<span class="required"></span></label>\
        <select class="form-control select2"\
        style="width: 100%;" id="txtContactCountry'+ ctr +'" name="txtContactCountry'+ ctr +'" tabindex="-1"\
        aria-hidden="true">\
        <option value="United States" data-code="US">United States</option>\
        <option value="Philippines" data-code="PH">Philippines</option>\
        <option value="China" data-code="CN">China</option>\
        </select>\
        </div>\
        <div class="form-group col-md-6">\
        <label for="zip">Zip:<span class="required"></span></label>\
        <input type="text" class="form-control zip-format'+ ctr +'" id="txtContactZip'+ ctr +'" name="txtContactZip'+ ctr +'"\
        placeholder="Enter zip" onkeypress="return isNumberKey(event)">\
        <span id="txtContactZip'+ ctr +'-error" style="color:red"><small></small></span>\
        </div>\
        <div class="form-group col-md-6" id="state_us">\
        <label>State:<span class="required"></span></label>\
        <select name="txtContactState'+ ctr +'" id="txtContactState'+ ctr +'" class="form-control select2" disabled>\
        </select>\
        </div>\
        <div class="form-group col-md-6">\
        <label for="city">City:<span class="required"></span></label>\
        <select name="txtContactCity'+ ctr +'" id="txtContactCity'+ ctr +'" class="form-control select2">\
        </select>\
        </div>\
        </div>\
        </div>\
        </div>\
        </div>\
        <div class="col-md-4 pt-5">\
        <div class="tab-content" style="padding:10px;border:1px solid #ddd;">\
        <div class="form-group">\
        <label>Phone 1: <span class="required"></span></label>\
        <div class="input-group">\
        <label for="ContactPhone'+ ctr +'" class="input-group-addon">1</label>\
        <input type="text" class="form-control w-50 number-only phone-format'+ ctr +'" id="txtContactPhone'+ ctr +'_1"\
        name="txtContactPhone'+ ctr +'_1" placeholder="Enter Phone 1">\
        <button class="btn btn-primary" type="button" title="Add Alternate Phone 2" onclick="$('+ conphone +').toggleClass(\'hide\')"><i class="fa fa-plus-square"></i></button>\
        </div>\
        </div>\
        <div class="form-group hide" id="conphone'+ ctr +'">\
        <label>Phone 2: <span class="required"></span></label>\
        <div class="input-group">\
        <label for="ContactPhone'+ ctr +'" class="input-group-addon">1</label>\
        <input type="text" class="form-control w-50 number-only phone-format'+ ctr +'" id="txtContactPhone'+ ctr +'_2"\
        name="txtContactPhone'+ ctr +'_2" placeholder="Enter Phone 2">\
        </div>\
        </div>\
        <div class="form-group">\
        <label>Mobile number 1: <span class="required"></span></label>\
        <div class="input-group">\
        <label for="ContactPhone'+ ctr +'" class="input-group-addon">1</label>\
        <input type="text" class="form-control w-50 number-only phone-format'+ ctr +'" id="txtContactMobile'+ ctr +'_1"\
        name="txtContactMobile'+ ctr +'_1" placeholder="Enter Mobile 1">\
        <button class="btn btn-primary" type="button" title="Add Alternate Mobile 2" onclick="$('+ conmobile +').toggleClass(\'hide\')"><i class="fa fa-plus-square"></i></button>\
        </div>\
        </div>\
        <div class="form-group hide" id="conmobile'+ ctr +'">\
        <label>Mobile Number 2: <span class="required"></span></label>\
        <div class="input-group">\
        <label for="ContactPhone'+ ctr +'" class="input-group-addon">1</label>\
        <input type="text" class="form-control w-50 number-only phone-format'+ ctr +'" id="txtContactMobile'+ ctr +'_2"\
        name="txtContactMobile'+ ctr +'_2" placeholder="Enter Mobile 2">\
        </div>\
        </div>\
        <div class="form-group">\
        <label>Email <small></small>:<span class="required"></span></label>\
        <input type="text" class="form-control" name="txtContactEmail'+ ctr +'" id="txtContactEmail'+ ctr +'"\
        value="" placeholder="Enter Email Address" onblur="validateData("partner_contacts","email",this,"-1","false","empty", "Email address already been used by other contacts", "cp-tab");">\
        </div>\
        </div>\
        </div>\
        </div>';

    /* if ($('#groupType').val() != 1) {
        var city = "txtContactCity" + ctr;
        var state = "txtContactState" + ctr;
        var add_contact = '<div class="added" id="added-' + ctr + '"><div class="row">' +
            '<div class="row-header">' +
            '<h3 class="title"><strong>Other Contact</strong></h3>' +
            '<button class="close" type="button" onclick="closeContact(' + ctr + ');">&times;</button>' +
            '</div>' +
            '<div class="col-lg-4 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label>First Name:</label>' +
            '<input type="text" class="form-control" name="txtContactFirstName' + ctr + '" id="txtContactFirstName' + ctr + '" value="" placeholder="First Name">' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-2 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label>Middle Initial:</label>' +
            '<input type="text" class="form-control" name="txtContactMiddleInitial' + ctr + '" id="txtContactMiddleInitial' + ctr + '" value="" placeholder="MI" maxlength="1">' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-6 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label>Last Name:<span class="required"></span></label>' +
            '<input type="text" class="form-control" name="txtContactLastName' + ctr + '" id="txtContactLastName' + ctr + '" value="" placeholder="Enter Last Name">' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-6 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label>Title:<span class="required"></span></label>' +
            '<input type="text" class="form-control" name="txtContactTitle' + ctr + '" id="txtContactTitle' + ctr + '" value="" placeholder="Enter Title">' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-4 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label>SSN:<span class="required"></span></label>' +
            '<input type="text" class="form-control ssn-format" name="txtContactSSN' + ctr + '" id="txtContactSSN' + ctr + '" value="" placeholder="Enter SSN">' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-2 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label>Percentage of Ownership:</label>' +
            '<input type="text" class="form-control" name="txtOwnershipPercentage' + ctr + '" id="txtOwnershipPercentage' + ctr + '" value="0" placeholder="0" onkeypress="return isNumberKey(event)">' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-6 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label>Date of Birth:</label>' +
            '<input type="text" class="form-control dob-format" name="txtContactDOB' + ctr + '" id="txtContactDOB' + ctr + '" value="" placeholder="MM/DD/YYYY">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="row-header">' +
            '<h3 class="title">Home Address</h3>' +
            '</div>' +
            '<div class="clearfix"></div>' +
            '<div class="col-lg-6 col-md-12">' +
            '<div class="form-group">' +
            '<label for="">Home Address 1:<span class="required"></span></label>' +
            '<input type="text" class="form-control" name="txtContactHomeAddress' + ctr + '_1" id="txtContactHomeAddress' + ctr + '_1" value="" placeholder="Enter Home Address 1"/>' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-6 col-md-12">' +
            '<div class="form-group">' +
            '<label for="">Home Address 2:</label>' +
            '<input type="text" class="form-control" name="txtContactHomeAddress' + ctr + '_2" id="txtContactHomeAddress' + ctr + '_2" value="" placeholder="Enter Home Address 2"/>' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-5 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label for="country">Country:<span class="required"></span></label>' +
            '<select name="txtContactCountry' + ctr + '" id="txtContactCountry' + ctr + '" class="form-control">' +
            '<option value="United States" data-code="US">United States</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-3 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label for="">State:<span class="required"></span></label>' +
            '<select name="txtContactState' + ctr + '" id="txtContactState' + ctr + '" class="form-control">' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-3 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label for="city">City:<span class="required"></span></label>' +
            '<input type="text" class="form-control" name="txtContactCity' + ctr + '" id="txtContactCity' + ctr + '" value="" placeholder="Enter City"/>' +
            '</div>' +
            '</div>' +
            '<div class="col-lg-1 col-md-6 col-sm-12">' +
            '<div class="form-group">' +
            '<label for="">Zip:<span class="required"></span></label>' +
            '<input type="text" class="form-control zip-format" name="txtContactZip' + ctr + '" id="txtContactZip' + ctr + '" value="" placeholder="Zip" onkeypress="return isNumberKey(event)" onblur="isValidZip(this, ' + city +  ', ' + state + ')"/>' +
            '<span id="txtContactZip' + ctr + '-error" style="color:red;"><small></small></span></br>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="row-header">' +
            '<h3 class="title">Personal Contact Information</h3>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<div class="form-group">' +
            '<label>Phone 1:<span class="required"></span></label>' +
            '<div class="input-group">' +
            '<label for="contactPhone" class="input-group-addon " id="ContactPhone">1</label>' +
            '<input type="text" class="form-control number-only phone-format" name="txtContactPhone' + ctr + '_1" id="txtContactPhone' + ctr + '_1" value="" placeholder="Enter Phone 1">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<div class="form-group">' +
            '<label>Phone 2:<span class="required"></span></label>' +
            '<div class="input-group">' +
            '<label for="contactPhone" class="input-group-addon ">1</label>' +
            '<input type="text" class="form-control number-only phone-format" name="txtContactPhone' + ctr + '_2" id="txtContactPhone' + ctr + '_2" value="" placeholder="Enter Phone 2">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<div class="form-group">' +
            '<label>Mobile <small>(must be valid)</small>:<span class="required"></span></label>' +
            '<div class="input-group">' +
            '<label for="contactPhone" class="input-group-addon ">1</label>' +
            '<input type="text" class="form-control number-only phone-format" name="txtContactMobile' + ctr + '_1" id="txtContactMobile' + ctr + '_1" value="" placeholder="Enter Mobile 1">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<div class="form-group">' +
            '<label>Mobile 2:</label>' +
            '<div class="input-group">' +
            '<label for="contactPhone" class="input-group-addon ">1</label>' +
            '<input type="text" class="form-control number-only phone-format" name="txtContactMobile' + ctr + '_2" id="txtContactMobile' + ctr + '_2" value="" placeholder="Enter Mobile 2">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<div class="form-group">' +
            '<label>Fax:<span class="required"></span></label>' +
            '<div class="input-group">' +
            '<label for="contactPhone" class="input-group-addon">1</label>' +
            '<input type="text" class="form-control number-only phone-format" name="txtContactFax' + ctr + '" id="txtContactFax' + ctr + '" value="" placeholder="Enter Fax">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<div class="form-group">' +
            '<label>Email <small></small>:<span class="required"></span></label>' +
            '<input type="text" class="form-control" name="txtContactEmail' + ctr + '" id="txtContactEmail' + ctr + '" value="" placeholder="Enter Email" onblur="validateData("partner_contacts","email",this,"-1","false","empty", "Email address already been used by other contacts", "cp-tab");">' +
            '</div>' +
            '</div>' +
            '</div></div>';
    } else {
        var add_contact = '<div class="added" id="added-' + ctr + '"><div class="box-header with-border">\
        <h3 class="box-title" style="color:#3c8dbc;"><b>Other Contact</b></h3>\
        <button class="close" type="button" onclick="closeContact(' + ctr + ');">&times;</button>\
        </div>\
        <div class="custom-contact-wrap-sm row">\
            <div class="col-lg-6 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>Contact First Name: <span class="required"></span></label>\
                    <input type="text" class="form-control" id="txtContactFirstNameAgent' + ctr + '" name="txtContactFirstNameAgent' + ctr + '" placeholder="Enter First Name">\
                </div>\
            </div>\
            <div class="col-lg-2 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>Contact M.I.:</label>\
                    <input type="text" class="form-control" id="txtContactMiddleInitialAgent' + ctr + '" name="txtContactMiddleInitialAgent' + ctr + '" placeholder="MI" maxlength="1">\
                </div>\
            </div>\
            <div class="col-lg-4 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>Contact Last Name: <span class="required"></span></label>\
                    <input type="text" class="form-control" id="txtContactLastNameAgent' + ctr + '" name="txtContactLastNameAgent' + ctr + '" placeholder="Enter Last Name">\
                </div>\
            </div>\
        </div>\
        <div class="custom-contact-wrap-sm row">\
            <div class="col-lg-6 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label for="phone2">Mobile Number:</label>\
                    <div class="input-group">\
                        <label for="ContactPhone" class="input-group-addon">1</label>\
                        <input type="text" class="form-control number-only phone-format" name="txtContactMobileNumberAgent' + ctr + '" id="txtContactMobileNumber txtContactMobileNumberAgent' + ctr + '"\
                            value="" placeholder="Enter Your Mobile Number">\
                    </div>\
                </div>\
            </div>\
            <div class="col-lg-6 col-md-6 col-sm-12 sm-col">\
                <div class="form-group">\
                    <label>Social Security Number: </label>\
                    <input type="text" class="form-control ssn-format" id="txtSSNAgent' + ctr + '" value="" name="txtSSNAgent' + ctr + '" placeholder="Enter Social Security Number">\
                </div>\
            </div>\
        </div></div>';
    } */
    ($("#addBtnContact").append(add_contact))
    $('#txtOtherHidden').val(JSON.stringify(elems));

    $('#txtContactCountry' + ctr ).on('change', function(){
        var country = $('#txtContactCountry' + ctr + ' option:selected').attr('data-code');

        if (country == 'US' || country == 'PH') {
            $('.phone-format' + ctr).unmask(cn_country_mask);
            $('.phone-format' + ctr).mask(usph_country_mask);
            $('.zip-format' + ctr).unmask();
            $('.zip-format' + ctr).mask(us_zip_mask);
            code = country == 'US' ? '1' : '63';
        } else if (country == 'CN') {
            $('.phone-format' + ctr).unmask(usph_country_mask);
            $('.phone-format' + ctr).mask(cn_country_mask);
            $('.zip-format' + ctr).unmask();
            $('.zip-format' + ctr).mask(us_zip_mask);
            code = '86';
        }
        jQuery("label[for='ContactPhone"+ ctr +"']").html(code);
    });

    $('#txtContactCountry' + ctr).trigger('change');

    $('.ssn-format').unmask();
    $('.ssn-format').mask("999-99-9999");
    $('.dob-format').mask("99/99/9999");

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

    $.ajax({
        url: '/partners/getStateByCountry/US',
    }).done(function (items) {
        let option = "";
        $.each(items.states, function (key, item) {
            option += '<option value="' + item.abbr + '" data-code="' + item.id + '">' + item.name + '</option> ';
        });

        $('#txtContactState' + ctr).empty(); //remove all child nodes

        var newOption = option;
        $('#txtContactState' + ctr).append(newOption);
        $('#txtContactState' + ctr).trigger("chosen:updated");
    });  

    $('#txtContactZip'+ ctr).on('keyup', function(){
        if ($(this).val().length == 5) {
            var zip = $(this).val();
            var zipEl = '#txtContactZip'+ ctr;
            var zipErrEl = '#txtContactZip'+ ctr +'-error small';
            var cityEl = '#txtContactCity'+ ctr;
            var stateEl = '#txtContactState'+ ctr;

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
            });
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
    if ($('#groupType').val() != 1) {
        if (curActiveHref.match('business-info')) {
            if (!$('#assigntome').is(":checked")) {
                if ($('#txtUplineId').val() == null) {
                    var id = "txtUplineId";
                    var msg = "Please select valid partner.";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtUplineId').style.removeProperty('border');
                    $('#txtUplineId-error small').text('');
                }
            }
            if ($('#txtCompanyName').val().trim() == "") {
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
            if ($('#txtBusinessAddress1').val().trim() == "") {
                var id = "txtBusinessAddress1";
                var msg = "Business Address 1 is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtBusinessAddress1').style.removeProperty('border');
                $('#txtBusinessAddress1-error small').text('');
            }
            if ($('#txtCity').val().trim() == "") {
                var id = "txtCity";
                var msg = "City is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtCity').style.removeProperty('border');
                $('#txtCity-error small').text('');
            }
            if ($('#txtBusinessZip').val().trim() == "") {
                var id = "txtBusinessZip";
                var msg = "Zip is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtBusinessZip').style.removeProperty('border');
                $('#txtBusinessZip-error small').text('');
            }
            if ($('#txtBusinessPhone1').val().trim() == "") {
                var id = "txtBusinessPhone1";
                var msg = "Business Phone 1 is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtBusinessPhone1').style.removeProperty('border');
                $('#txtBusinessPhone1-error small').text('');
            }
            /* if ($('#txtEmail').val().trim() == "") {
                var id = "txtEmail";
                var msg = "Email is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtEmail').style.removeProperty('border');
                $('#txtEmail-error').remove();
            } */
            // if (!$("#chkSameAsBusiness").is(':checked')) {
            //     if ($('#txtMailingAddress1').val().trim() == "") {
            //         var id = "txtMailingAddress1";
            //         var msg = "Mailing Address 1 is required.";
            //         errors[id] = msg;
            //     } else {
            //         document.getElementById('txtMailingAddress1').style.removeProperty('border');
            //         $('#txtMailingAddress1-error').remove();
            //     }
            //     if ($('#txtMailingCity').val().trim() == "") {
            //         var id = "txtMailingCity";
            //         var msg = "City is required.";
            //         errors[id] = msg;
            //     } else {
            //         document.getElementById('txtMailingCity').style.removeProperty('border');
            //         $('#txtMailingCity-error').remove();
            //     }
            //     if ($('#txtMailingZip').val().trim() == "") {
            //         var id = "txtMailingZip";
            //         var msg = "City is required.";
            //         errors[id] = msg;
            //     } else {
            //         document.getElementById('txtMailingZip').style.removeProperty('border');
            //         $('#txtMailingZip-error').remove();
            //     }
            // }
            if ($('#txtBusinessPhone1').val().trim() != "") {
                if ($('#txtBusinessPhone1').val().length == 12 ||
                    $('#txtBusinessPhone1').val().length == 14) {
                    document.getElementById('txtBusinessPhone1').style.removeProperty('border');
                    $('#txtBusinessPhone1-error small').text('');
                } else {
                    var id = "txtBusinessPhone1";
                    var msg = "Invalid Phone Number.";
                    errors[id] = msg;
                }
            }
            if (!$('#txtEmail').val().trim() == "") {
                if (!isEmail($('#txtEmail').val())) {
                    var id = "txtEmail";
                    var msg = "Invalid Email Format.";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtEmail').style.removeProperty('border');
                    $('#txtEmail-error small').text('');
                }
            }
            if (typeof $('#txtWebsite').val() !== "undefined" && $('#txtWebsite').val().trim() != "") {
                if (!ValidURL($('#txtWebsite').val())) {
                    var id = "txtWebsite";
                    var msg = "Please input valid website URL!";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtWebsite').style.removeProperty('border');
                    $('#txtWebsite-error small').text('');
                }
            }
            if ($('#txtEmail').val().trim() == "") {
                var id = "txtContactMobile1_1";
                $('#mobileNumber').text('*');
            } else {
                document.getElementById('txtContactMobile1_1').style.removeProperty('border');
                $('#txtContactMobile1_1-error').text('');
                $('#mobileNumber').text('');
            }
        } else if (curActiveHref.match('contact-person')) {
            if ($('#txtContactFirstName1').val().trim() == "") {
                var id = "txtContactFirstName1";
                var msg = "First Name is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactFirstName1').style.removeProperty('border');
                $('#txtContactFirstName1-error small').text('');
            }
            if ($('#txtContactLastName1').val().trim() == "") {
                var id = "txtContactLastName1";
                var msg = "Last Name is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactLastName1').style.removeProperty('border');
                $('#txtContactLastName1-error small').text('');
            }
            // if ($('#txtContactDOB1').val().trim() == "") {
            //     var id = "txtContactDOB1";
            //     var msg = "Date of Birth is required.";
            //     errors[id] = msg;
            // } else {
            //     document.getElementById('txtContactDOB1').style.removeProperty('border');
            //     $('#txtContactDOB1-error small').text('');
            // }
            // if ($('#txtContactHomeAddress1_1').val().trim() == "") {
            //     var id = "txtContactHomeAddress1_1";
            //     var msg = "Home Address 1 is required.";
            //     errors[id] = msg;
            // } else {
            //     document.getElementById('txtContactHomeAddress1_1').style.removeProperty('border');
            //     $('#txtContactHomeAddress1_1-error').remove();
            // }
            // if ($('#txtContactCity1').val().trim() == "") {
            //     var id = "txtContactCity1";
            //     var msg = "City is required.";
            //     errors[id] = msg;
            // } else {
            //     document.getElementById('txtContactCity1').style.removeProperty('border');
            //     $('#txtContactCity1-error').remove();
            // }
            // if ($('#txtContactZip1').val().trim() == "") {
            //     var id = "txtContactZip1";
            //     var msg = "Zip is required.";
            //     errors[id] = msg;
            // } else {
            //     document.getElementById('txtContactZip1').style.removeProperty('border');
            //     $('#txtContactZip1-error').remove();
            // }
            /* if ($('#txtContactMobile1_1').val().trim() == "") {
                var id = "txtContactMobile1_1";
                var msg = "Mobile is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactMobile1_1').style.removeProperty('border');
                $('#txtContactMobile1_1-error').text('');
            }
            if ($('#txtContactEmail1').val().trim() == "") {
                var id = "txtContactEmail1";
                var msg = "Email is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactEmail1').style.removeProperty('border');
                $('#txtContactEmail1-error').text('');
            } */

            if ($('#txtEmail').val().trim() == "" &&
                $('#txtContactMobile1_1').val().trim() == "") {
                var id = "txtContactMobile1_1";
                var msg = "Partner must have either Business Email or Contact Mobile Number.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactMobile1_1').style.removeProperty('border');
                $('#txtContactMobile1_1-error small').text('');
            }
            if ($('#txtContactDOB1').val().trim() != "") {
                if (!isValidDateEx($('#txtContactDOB1').val())) {
                    var id = "txtContactDOB1";
                    var msg = "Please input valid date.";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtContactDOB1').style.removeProperty('border');
                    $('#txtContactDOB1-error small').text('');
                }
            }
            // if ($('#txtOwnershipPercentage1').val() == "") {
            //     var id = "txtOwnershipPercentage1";
            //     var msg = "Percentage should be numeric.";
            //     errors[id] = msg;
            // } else {
            //     document.getElementById('txtOwnershipPercentage1').style.removeProperty('border');
            //     $('#txtOwnershipPercentage1-error small').text('');
            // }
            if ($('#txtOwnershipPercentage1').val().trim() != "") {
                if (parseFloat($('#txtOwnershipPercentage1').val()) > 100 ||
                    parseFloat($('#txtOwnershipPercentage1').val()) < 0) {
                    var id = "txtOwnershipPercentage1";
                    var msg = "Percentage should be 1-100.";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtOwnershipPercentage1').style.removeProperty('border');
                    $('#txtOwnershipPercentage1-error small').text('');
                }
            }
            if (typeof txtContactEmail1 !== "undefined" &&
                $('#txtContactEmail1').val() != "") {
                if (!isEmail($('#txtContactEmail1').val())) {
                    var id = "txtContactEmail1";
                    var msg = "Invalid email format.";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtContactEmail1').style.removeProperty('border');
                    $('#txtContactEmail1-error small').text('');
                }
            }
        }
    } else {
        if (curActiveHref.match('business-info')) {
            if ($('#txtBusinessName').val().trim() == "") {
                var id = "txtBusinessName";
                var msg = "Business Name is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtBusinessName').style.removeProperty('border');
                $('#txtBusinessName-error small').text('');
            }
            if ($('#txtAddressAgent').val().trim() == "") {
                var id = "txtAddressAgent";
                var msg = "Address is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtAddressAgent').style.removeProperty('border');
                $('#txtAddressAgent-error small').text('');
            }
            if ($('#txtCityAgent').val().trim() == "") {
                var id = "txtCityAgent";
                var msg = "City is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtCityAgent').style.removeProperty('border');
                $('#txtCityAgent-error small').text('');
            }
            if ($('#txtZipAgent').val().trim() == "") {
                var id = "txtZipAgent";
                var msg = "Zip is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtZipAgent').style.removeProperty('border');
                $('#txtZipAgent-error small').text('');
            }
            /* if ($('#txtEmailAgent').val().trim() == "") {
                var id = "txtEmailAgent";
                var msg = "Email is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtEmailAgent').style.removeProperty('border');
                $('#txtEmailAgent-error small').text('');
            } */
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
            if ($('#txtEmailAgent').val().trim() != "") {
                if (!isEmail($('#txtEmailAgent').val())) {
                    var id = "txtEmailAgent";
                    var msg = "Invalid Email Format.";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtEmailAgent').style.removeProperty('border');
                    $('#txtEmailAgent-error small').text('');
                }
            }

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
        } else if (curActiveHref.match('contact-person')) {
            if ($('#txtContactFirstNameAgent').val().trim() == "") {
                var id = "txtContactFirstNameAgent";
                var msg = "First Name is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactFirstNameAgent').style.removeProperty('border');
                $('#txtContactFirstNameAgent-error small').text('');
            }
            if ($('#txtContactLastNameAgent').val().trim() == "") {
                var id = "txtContactLastNameAgent";
                var msg = "Last Name is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactLastNameAgent').style.removeProperty('border');
                $('#txtContactLastNameAgent-error small').text('');
            }
            /* if ($('#txtContactMobileNumberAgent').val().trim() == "") {
                var id = "txtContactMobileNumberAgent";
                var msg = "Mobile Number is required.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactMobileNumberAgent').style.removeProperty('border');
                $('#txtContactMobileNumberAgent-error small').text('');
            } */
            if ($('#txtContactMobileNumberAgent').val().trim() != "") {
                if ($('#txtContactMobileNumberAgent').val().length == 12 
                    || $('#txtContactMobileNumberAgent').val().length == 14) {
                    document.getElementById('txtContactMobileNumberAgent').style.removeProperty('border');
                    $('#txtContactMobileNumberAgent-error small').text('');
                } else {
                    var id = "txtContactMobileNumberAgent";
                    var msg = "Invalid Phone Number.";
                    errors[id] = msg;
                }
            }
            if ($('#txtEmailAgent').val().trim() == "" &&
                $('#txtContactMobileNumberAgent').val().trim() == "") {
                var id = "txtContactMobileNumberAgent";
                var msg = "Partner must have either Business Email or Contact Mobile Number.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactMobileNumberAgent').style.removeProperty('border');
                $('#txtContactMobileNumberAgent-error small').text('');
            }
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
        '<button class="btn btn-sm btn-danger clear-input" data-file_id="fileUploadOthers' + ctr + '" style="display: none;">Clear Input</button>' +
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

function partnerPreview() {
    var add_trow = '';
    
    // Added Contacts
    if ($('#txtOtherHidden').val()) {
        var data = JSON.parse($('#txtOtherHidden').val());
        data.forEach(id => {
            if ($('#contact_' + id).length == 0) {
                if ($('#txtPartnerTypeId').val() != '1') {
                    add_trow += '<tr id="contact_' + id + '">\
                            <th colspan="12" class="form-category">Contact ' + id + '</th>\
                        </tr>\
                        <tr>\
                            <th colspan="1">First Name:</th>\
                            <td colspan="3"><i id="txtContactFirstName' + id + '_preview" class="view"></i>\
                            <th colspan="1">Middle Initial:</th>\
                            <td colspan="3"><i id="txtContactMiddleInitial' + id + '_preview" class="view"></i>\
                            <th colspan="1">Last Name:</th>\
                            <td colspan="3"><i id="txtContactLastName' + id + '_preview" class="view"></i></td>\
                        </tr> \
                        <tr>\
                            <th colspan="1">Title:</th>\
                            <td colspan="5"><i id="txtContactTitle' + id + '_preview" class="view"></i>\
                            <th colspan="1">SSN:</th>\
                            <td colspan="5"><i id="txtContactSSN' + id + '_preview" class="view"></i>\
                        </tr>\
                        <tr>\
                            <th colspan="1">Percentage of Ownership:</th>\
                            <td colspan="5"><i id="txtOwnershipPercentage' + id + '_preview" class="view"></i>\
                            <th colspan="1">Date of Birth:</th>\
                            <td colspan="5"><i id="txtContactDOB' + id + '_preview" class="view"></i></td>\
                        </tr> \
                        <tr>\
                            <th colspan="12" class="form-category">Home Address</th>\
                        </tr>\
                        <tr>\
                            <th colspan="1">Home Address 1:</th>\
                            <td colspan="11"><i id="txtContactHomeAddress' + id + '_1_preview" class="view"></i></td>\
                        </tr>\
                        <tr>\
                            <th colspan="1">Home Address 2:</th>\
                            <td colspan="11"><i id="txtContactHomeAddress' + id + '_2_preview" class="view"></i></td>\
                        </tr>\
                        <tr>\
                            <th colspan="1">Country:</th>\
                            <td colspan="5"><i id="txtContactCountry' + id + '_preview" class="view"></i>\
                            <th colspan="1">State:</th>\
                            <td colspan="5"><i id="txtContactState' + id + '_preview" class="view"></i>\
                        </tr>\
                        <tr>\
                            <th colspan="1">City:</th>\
                            <td colspan="5"><i id="txtContactCity' + id + '_preview" class="view"></i>\
                            <th colspan="1">Zip:</th>\
                            <td colspan="5"><i id="txtContactZip' + id + '_preview" class="view"></i></td>\
                        </tr>\
                        <tr>\
                            <th colspan="12" class="form-category">Personal Contact Information</th>\
                        </tr>\
                        <tr>\
                            <th colspan="1">Phone 1:</th>\
                            <td colspan="5"><i id="txtContactPhone' + id + '_1_preview" class="view"></i>\
                            <th colspan="1">Phone 2:</th>\
                            <td colspan="5"><i id="txtContactPhone' + id + '_2_preview" class="view"></i></td>\
                        </tr> \
                        <tr>\
                            <th colspan="1">Mobile:</th>\
                            <td colspan="5"><i id="txtContactMobile' + id + '_1_preview" class="view"></i>\
                            <th colspan="1">Mobile 2:</th>\
                            <td colspan="5"><i id="txtContactMobile' + id + '_2_preview" class="view"></i></td>\
                        </tr> \
                        <tr>\
                            <th colspan="1">Fax:</th>\
                            <td colspan="5"><i id="txtContactFax' + id + '_preview" class="view"></i>\
                            <th colspan="1">Email:</th>\
                            <td colspan="5"><i id="txtContactEmail' + id + '_preview" class="view"></i></td>\
                        </tr> ';
                } else {
                    add_trow += '<tr id="contact_' + id + '">\
                        <td><i >' + id +  '</i></td>\
                        <td><i id="txtContactFirstNameAgent' + id + '_preview" class="view"></i></td>\
                        <td><i id="txtContactMiddleInitialAgent' + id + '_preview" class="view"></i></td>\
                        <td><i id="txtContactLastNameAgent' + id + '_preview" class="view"></i></td>\
                        <td><i id="txtContactMobileNumberAgent' + id + '_preview" class="view"></i></td>\
                        <td><i id="txtSSNAgent' + id + '_preview" class="view"></i></td>\
                        </tr>';
                }
            }
        });
        if ($('#txtPartnerTypeId').val() != '1') {
            $('#non-agent-contact-table').append(add_trow);
        } else {
            $(add_trow).insertAfter($('#first-contact-person-agent').closest('tr'));
        }
    }

    $.each($('#frmPartner').serializeArray(), function(i, field) {
        $('#' + field.name + '_preview').text(field.value);
    });
    
    var isAgent = $('#groupType').val() != 1 ? '_non_agent' : '_agent'; 
    
    // Ownership
    $('#txtOwnership_preview').text($('#txtOwnership option:selected').text());

    // Partner Type
    $('#groupType_preview' + isAgent).text($('#txtPartnerTypeId option:selected').text());
    
    // Parent Assignment
    if ($('#groupType').val() == 7) {
        $('#parent_preview' + isAgent).text('NA');
    } else {
        if (document.getElementById('assigntome').checked) {
            $('#parent_preview' + isAgent).text($('label[for="assigntome"]').after().text());
        } else {
            $('#parent_preview' + isAgent).text($('#txtUplineId option:selected').text());
            $('#parent_preview' + isAgent).text($('#txtUplineId option:selected').text());
        }
    }

    if ($('input#chkSameAsBusinessBilling').is(':checked')) {
        var state = $('#txtState').val();
        var country = $('#txtCountry').val();
        $('#txtBillingCountry_preview').text(country).trigger('change');

        $('#txtBillingAddress1_preview').text($('#txtBusinessAddress1').val());
        $('#txtBillingAddress2_preview').text($('#txtBusinessAddress2').val());
        $('#txtBillingCity_preview').text($('#txtCity').val());
        $('#txtBillingZip_preview').text($('#txtBusinessZip').val());
        var delay = 1000;
        setTimeout(function() {
            $("#txtBillingState_preview").text(state);
        }, delay);
    }
    if ($('input#chkSameAsBusiness').is(':checked')) {
        var state = $('#txtState').val();
        var country = $('#txtCountry').val();
        $('#txtMailingCountry_preview').text(country).trigger('change');

        $('#txtMailingAddress1_preview').text($('#txtBusinessAddress1').val());
        $('#txtMailingAddress2_preview').text($('#txtBusinessAddress2').val());
        $('#txtMailingCity_preview').text($('#txtCity').val());
        $('#txtMailingZip_preview').text($('#txtBusinessZip').val());
        var delay = 1000;
        setTimeout(function() {
            $("#txtMailingState_preview").text(state);
        }, delay);
    }
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
                showSuccessMessage(data.message, '/partners/management');
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
    // $('#' + city_id).prop('disabled', true);
    // $('#' + state_id).prop('disabled', true);
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

function validateOwnershipPerc(value)
{
    var i = 0;
    var total = 0;

    if(value.value > 100){
        value.value = 0;
        return false;
    }

    $(".ownerPercentage").each(function() {
        i = $(this).val();
        total = total +  parseFloat(i);
        console.log(total);
        if(total > 100){
            value.value = 0;
            showWarningMessage('Total Ownership should not exceed 100%');
            return false;
        }
    });

    if(total < 100){
        addContact(100 - total);
    }
}


window.validateData = validateData;
window.load_partners = load_partners;
window.UploadAttachment = UploadAttachment;
window.createPaymentGateway = createPaymentGateway;
window.editPaymentGateway = editPaymentGateway;
window.advanceSearchPartners = advanceSearchPartners;
window.createPayment = createPayment;
window.editPayment = editPayment;
window.isNumberKey = isNumberKey;
window.verifyEmail = verifyEmail;
window.isEmail = isEmail;
window.ValidURL = ValidURL;
window.validateField = validateField;
window.validateEmail = validateEmail;
window.addContact = addContact;
window.closeContact = closeContact;
window.addFile = addFile;
window.closeFile = closeFile;
// window.partnerPreview = partnerPreview;
window.deleteDraftApplicant = deleteDraftApplicant;
window.isValidZip = isValidZip;
window.validateOwnershipPerc = validateOwnershipPerc;