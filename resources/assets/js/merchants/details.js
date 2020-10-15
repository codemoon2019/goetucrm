import swal from 'sweetalert2'
import axios from "axios";

$(document).ready(function () {

    $('#txtSocialSecurityNumber').mask('999-99-9999')
    $('#txtTaxIdNumber').mask('99-9999999')

    $('#txtBankRouting').on('input', function() { 
        $('#txtBankRouting').mask('999999999')
    });
    $('#txtBankRoutingConfirmation').on('input', function() { 
        $('#txtBankRoutingConfirmation').mask('999999999')
    });

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
    // $('#txtMailingZip').mask("99999");

    // $('#txtContactPhone1').mask("-999-999-9999");
    // $('#txtContactPhone2').mask("-999-999-9999");
    $('#txtContactFax').mask("-999-999-9999");
    $('#txtContactMobileNumber').mask("999-999-9999");

    // $('#txtContactPhone12').mask("-999-999-9999");
    // $('#txtContactPhone22').mask("-999-999-9999");
    $('#txtContactFax2').mask("-999-999-9999");
    $('#txtContactMobileNumber2').mask("999-999-9999");

    $('#txtSSN').mask("999-99-9999");

    $('#txtDOB').mask("99/99/9999");
    $('#txtExpDate').mask("99/99/9999");
    $('#txtDateAcquired').mask("99/99/9999");

    // $('#txtContactMobile').mask("-999-999-9999");

    $('#txtRoutingNo').mask("999999999");
    $('#txtWRoutingNo').mask("999999999");
    $('#txtBusinessDate').mask("99/9999");

    $('#txtMerchantMID').mask('9999999999999999', {clearIfNotMatch: true})

    // $('.select2').select2({
    //     templateSelection: formatSelect2,
    //     templateResult: formatSelect2Result
    // })    

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
            $('#txtContactMobile').unmask(usph_country_mask);
            $('#txtContactMobile').mask(cn_country_mask);
            $('#txtContactPhone1').unmask(usph_country_mask);
            $('#txtContactPhone1').mask(cn_country_mask);
            $('#txtContactPhone2').unmask(usph_country_mask);
            $('#txtContactPhone2').mask(cn_country_mask);
        } else {
            $('#txtPhoneNumber').unmask(cn_country_mask);
            $('#txtPhoneNumber').mask(usph_country_mask);
            $('#txtPhoneNumber2').unmask(cn_country_mask);
            $('#txtPhoneNumber2').mask(usph_country_mask);
            $('#txtContactMobile').unmask(cn_country_mask);
            $('#txtContactMobile').mask(usph_country_mask);
            $('#txtContactPhone1').unmask(cn_country_mask);
            $('#txtContactPhone1').mask(usph_country_mask);
            $('#txtContactPhone2').unmask(cn_country_mask);
            $('#txtContactPhone2').mask(usph_country_mask);
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
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";
        var usph_country_mask = "999-999-9999";
        var cn_country_mask = "9-999-999-9999";

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
            $('#txtContactMobile').unmask(usph_country_mask);
            $('#txtContactMobile').mask(cn_country_mask);
        }else{
            $('#txtPhone1').unmask(cn_country_mask);
            $('#txtPhone1').mask(usph_country_mask);
            $('#txtPhone2').unmask(cn_country_mask);
            $('#txtPhone2').mask(usph_country_mask);

            $('#txtContactPhone1').unmask(cn_country_mask);
            $('#txtContactPhone1').mask(usph_country_mask);
            $('#txtContactPhone2').unmask(cn_country_mask);
            $('#txtContactPhone2').mask(usph_country_mask);
            $('#txtContactMobile').unmask(cn_country_mask);
            $('#txtContactMobile').mask(usph_country_mask);
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

    $('#frmPaymentGateway').submit(function () {
        if (!validateField('txtPGName', 'Name is required')) {
            return false;
        }
        if (!validateField('txtPGKey', 'Key is required')) {
            return false;
        }
    });

    $('#frmMerchantInfo').submit(function () {
        var errors = {};
        /* if(!validateField('txtFederalTaxID','Federal Tax ID is required'))
        {
            return false;
        } */
        /* if (!validateField('txtCompanyName', 'Legal name is required')) {
            return false;
        } */
        /* if(!validateField('txtDBA','DBA is required'))
        {
            return false;
        } */
        // if(!validateField('txtBankName','Deposit Bank Account No. is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtBankAccountNo','Deposit Bank Account No. is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtRoutingNo','Deposit Routing No. is required'))
        // {
        //     return false;
        // }
        /* if(!validateField('txtWBankName','Withdrawal Bank Account No. is required'))
        {
            return false;
        }
        if(!validateField('txtWBankAccountNo','Withdrawal Bank Account No. is required'))
        {
            return false;
        }
        if(!validateField('txtWRoutingNo','Withdrawal Routing No. is required'))
        {
            return false;
        } */
        // if(!validateField('txtMerchantURL','Merchant URL is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtTaxName','Tax Filling Name is required'))
        // {
        //     return false;
        // }
        if ($('#txtMerchantMID').val().trim() == "") {
            var id = "txtMerchantMID";
            var msg = "MID is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtMerchantMID').style.removeProperty('border');
            $('#txtMerchantMID-error small').text('');
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

        if ($('#txtBusinessName').val().trim() == "") {
            var id = "txtBusinessName";
            var msg = "Business Address is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtBusinessName').style.removeProperty('border');
            $('#txtBusinessName-error small').text('');
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

        if (!validateReqFields(errors)) {
            return false;
        }
        return true;

    });

    $('#frmMerchantAddress').submit(function () {
        var errors = {};
        
        /* if ($('#txtDBAAddress1').val().trim() == "") {
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
            var msg = "Phone 1 is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtPhone1').style.removeProperty('border');
            $('#txtPhone1-error small').text('');
        } */
        // if (!validateField('txtCountry', 'Country is required')) {
        //     return false;
        // }
        // if (!validateField('txtAddress1', 'Business Address 1 is required')) {
        //     return false;
        // }
        // if (!validateField('txtCity', 'City is required')) {
        //     return false;
        // }
        // if (!validateField('txtZip', 'Zip is required')) {
        //     return false;
        // }
        // if (!validateField('txtPhone1', 'Business Phone 1 is required')) {
        //     return false;
        // }
        // if (!validateField('txtEmail', 'Email is required')) {
        //     return false;
        // }
        /* if($('#chkDBA').prop("checked") == false)
        {
            if(!validateField('txtDBAAddress1','DBA Business Address 1 is required'))
            {
                return false;
            }
            if(!validateField('txtDBACity','DBA City is required'))
            {
                return false;
            }
            if(!validateField('txtDBAZip','DBA Zip is required'))
            {
                return false;
            }
        }
        if($('#chkBlling').prop("checked") == false)
        {
            if(!validateField('txtBillingAddress1','Billing Business Address 1 is required'))
            {
                return false;
            }
            if(!validateField('txtBillingCity','Billing City is required'))
            {
                return false;
            }
            if(!validateField('txtBillingZip','Billing Zip is required'))
            {
                return false;
            }
        }
        if($('#chkShipping').prop("checked") == false)
        {
            if(!validateField('txtShippingAddress1','Shipping Business Address 1 is required'))
            {
                return false;
            }
            if(!validateField('txtShippingCity','Shipping City is required'))
            {
                return false;
            }
            if(!validateField('txtShippingZip','Shipping Zip is required'))
            {
                return false;
            }
        } */

        /* if ($('#txtEmail').val().trim() == "" && 
            $('#txtContactMobileNum').val().trim() == "-1") {
            var id = "txtEmail";
            var msg = "Merchant must have either Business Email or Mobile Number.";
            errors[id] = msg;
        } else {
            document.getElementById('txtEmail').style.removeProperty('border');
            $('#txtEmail-error small').text('');
        } */

        if ($('#txtEmail').val().trim() == "") {
            var id = "txtEmail";
            var msg = "Email is required.";
            errors[id] = msg;
        } else {
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

        $('#txtCopyDBA').val($('#chkDBA').prop("checked"));
        $('#txtCopyBill').val($('#chkBlling').prop("checked"));
        $('#txtCopyShip').val($('#chkShipping').prop("checked"));


        if (!validateReqFields(errors)) {
            return false;
        }
        return true;

    });

    $('#frmContactInfo').submit(function () {
        var errors = {};

        // if(!validateField('txtFirstName','First Name is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtLastName','Last Name is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtDOB','Date of Birth is required'))
        // {
        //     return false;
        // }
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
        if ($('#txtDOB').val().trim() == "") {
            var id = "txtDOB";
            var msg = "Date of Birth is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtDOB').style.removeProperty('border');
            $('#txtDOB-error small').text('');
        }
        // if(!validateField('txtTitle','Title is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtSSN','SSN is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtIssuedID','Drivers License / Identification Card No. is required'))
        // {
        //     return false;
        // }

        // if(!validateField('txtDateAcquired','Date Business Acquired is required'))
        // {
        //     return false;
        // } 
        
        /* if ($('#txtPercentageOwnership').val().trim() == "") {
            var id = "txtPercentageOwnership";
            var msg = "Percentage of Ownership is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtPercentageOwnership').style.removeProperty('border');
            $('#txtPercentageOwnership-error small').text('');
        } */
        // if(!validateField('txtPercentageOwnership','Percentage of Ownership is required'))
        // {
        //     return false;
        // }

        // if(!validateField('txtContactPhone1','Phone 1 is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtContactMobile','Mobile is required'))
        // {
        //     return false;
        // }
        // if(!validateField('txtContactEmail','Email is required'))
        // {
        //     return false;
        // }

        if ($('#txtDOB').val() != "") {
            if (!isValidDateEx($('#txtDOB').val())){
                var id = "txtDOB";
                var msg = "Please input valid date.";
                errors[id] = msg;
            } else {
                document.getElementById('txtDOB').style.removeProperty('border');
                $('#txtDOB-error small').text('');
            }
        } 
        // if (!isValidDateEx($('#txtDOB').val())){
        //     alert("Please input valid date.")
        //     return false;    
        // } 

        if ($('#txtExpDate').val() != "") {
            if (!isValidDateEx($('#txtExpDate').val())){
                var id = "txtExpDate";
                var msg = "Please input valid date.";
                errors[id] = msg;
            } else {
                document.getElementById('txtExpDate').style.removeProperty('border');
                $('#txtExpDate-error small').text('');
            }
        } 
        // if (!isValidDateEx($('#txtExpDate').val())){
        //     alert("Please input valid date.")
        //     return false;    
        // } 

        if ($('#txtDateAcquired').val() != "") {
            if (!isValidDateEx($('#txtDateAcquired').val())){
                var id = "txtDateAcquired";
                var msg = "Please input valid date.";
                errors[id] = msg;
            } else {
                document.getElementById('txtDateAcquired').style.removeProperty('border');
                $('#txtDateAcquired-error small').text('');
            }
        } 
        // if($('#txtDateAcquired').val()!="")
        // {
        //     if (!isValidDateEx($('#txtDateAcquired').val())){
        //         alert("Please input valid date.")
        //         return false;    
        //     } 
        // }

        // if($('#txtOwnershipPercentage1').val()=="")
        // {
        //     alert('Percentage should be numeric.');
        //     return false;
        // }

        if ($('#txtPercentageOwnership').val().trim() != "") {
            if(parseFloat($('#txtPercentageOwnership').val()) > 100 || 
            parseFloat($('#txtPercentageOwnership').val()) < 0){
                var id = "txtPercentageOwnership";
                var msg = "Percentage should be 1-100.";
                errors[id] = msg;
            } else {
                document.getElementById('txtPercentageOwnership').style.removeProperty('border');
                $('#txtPercentageOwnership-error small').text();
            }
        } 
        // if($('#txtPercentageOwnership').val().trim() != "") {
        //     if(parseFloat($('#txtPercentageOwnership').val()) > 100 || parseFloat($('#txtPercentageOwnership').val()) < 0){
        //         alert("Percentage should be 1-100", 1)
        //         return false;
        //     }      
        // }

        if ($('#txtContactEmail').val().trim() != "") {
            if(!isEmail($('#txtContactEmail').val())){
                var id = "txtContactEmail";
                var msg = "Invalid email format.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactEmail').style.removeProperty('border');
                $('#txtContactEmail-error small').text('');
            }
        } 
        // if($('#txtContactEmail').trim().val() != ""){
        //     if(!isEmail($('#txtContactEmail').val())){
        //         alert('Invalid email format');
        //         return false;
        //     }
        // }

        if ($('#txtExpDate').val() != "") {
            if (!isExpired($('#txtExpDate').val())){
                var id = "txtExpDate";
                var msg = "Expired ID.";
                errors[id] = msg;
            } else {
                document.getElementById('txtExpDate').style.removeProperty('border');
                $('#txtExpDate-error small').text('');
            }
        } 
        // if($('#txtExpDate').val()!="")
        // {
        //     if (!isExpired($('#txtExpDate').val())){
        //         alert("Expired ID.")
        //         return false;    
        //     } 
        // }

        if ($('#txtDateAcquired').val().trim() != "") {
            if (!validDateAcquired($('#txtDateAcquired').val())){
                var id = "txtDateAcquired";
                var msg = "Date Business Acquired must be valid.";
                errors[id] = msg;
            } else {
                document.getElementById('txtDateAcquired').style.removeProperty('border');
                $('#txtDateAcquired-error small').text('');
            }
        } 
        // if($('#txtDateAcquired').val()!="")
        // {
        //     if (!validDateAcquired($('#txtDateAcquired').val())){
        //         alert("Date Business Acquired must be valid.")
        //         return false;    
        //     } 
        // }

        if ($('#txtContactMobile').val().trim() == "" &&
            $('#txtContactEmail').val().trim() == "" &&
            $('#isOrigCon').val() == '1') {
            var id = "txtContactMobile";
            var msg = "User must have either email address or mobile number.";
            errors[id] = msg;
            var id = "txtContactEmail";
            var msg = "User must have either email address or mobile number.";
            errors[id] = msg;
        } else {
            document.getElementById('txtContactMobile').style.removeProperty('border');
            $('#txtContactMobile-error small').text('');
            document.getElementById('txtContactEmail').style.removeProperty('border');
            $('#txtContactEmail-error small').text('');
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

    /* $('#togBtnUnpaid').change(function(){
        if ($(this).is(':checked')) {
            $(this).attr('checked',true);
            $('#txtTogBtnUnpaidPro').val('on');
        } else {
            $(this).attr('checked',false);
            $('#txtTogBtnUnpaidPro').val('off');
        }
    });

    $('#togBtnPaid').change(function(){
        if ($(this).is(':checked')) {
            $(this).attr('checked',true);
            $('#txtTogBtnPaidPro').val('on');
        } else {
            $(this).attr('checked', false);
            $('#txtTogBtnPaidPro').val('off');
        }
    });

    $('#togBtnSMTP').change(function(){
        if ($(this).is(':checked')) {
            $(this).attr('checked',true);
            $('#txtTogBtnSMTPPro').val('on');
        } else {
            $(this).attr('checked', false);
            $('#txtTogBtnSMTPPro').val('off');
        }
    }); */

    $('#togBtnAutoEmailer').change(function(){
        if ($(this).is(':checked')) {
            $(this).attr('checked',true);
            $('#txtTogBtnAutoPro').val('on');
        } else {
            $(this).attr('checked', false);
            $('#txtTogBtnAutoPro').val('off');
        }
    });

    $('#frmEditMID').submit(function () {
        if (!validateField('txtMIDVal', 'MID is required')) {
            return false;
        }
    });

    $(document).on('change','.txtSystem', function(e){
        var format = this.options[this.selectedIndex].getAttribute('data-format');
        $('#txtMIDVal').val('');
        $('#txtMIDVal').mask(format, {clearIfNotMatch: true})
    });


});


function editMID(id) {
    $.getJSON('/merchants/get_mid_details/' + id, null, function (data) {
        $('#midID').val(data['id']);
        $('#txtSystem').val(data['system_id']);
        $('#txtMIDVal').val(data['mid']);
        $('#txtMIDVal').mask(data['format'], {clearIfNotMatch: true})
        $('#editMID').modal('show');
    });
}

function createMID() {
    $('#midID').val(-1);
    $('#txtSystem').trigger('change');
    $('#editMID').modal('show');
}


function convertToFloat(field) {
    if (isNumber($(field).val())) {
        $(field).val(parseFloat($(field).val()));
    }

}

function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
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

function editContact(id) {
    $.getJSON('/merchants/merchant_contact_info/' + id, null, function (data) {

        $('#contID').val(data['id']);
        $('#isOrigCon').val(data['is_original_contact']);
        $('#txtFirstName').val(data['first_name']);
        $('#txtMiddleInitial').val(data['middle_name']);
        $('#txtLastName').val(data['last_name']);

        $('#txtDOB').val(data['dob']);
        $('#txtTitle').val(data['position']);
        $('#txtSSN').val(data['ssn']);

        $('#txtDateAcquired').val(data['business_acquired_date']);
        $('#txtPercentageOwnership').val(data['ownership_percentage']);
        $('#txtIssuedID').val(data['issued_id']);
        $('#txtExpDate').val(data['id_exp_date']);
        $('#txtContactPhone1').val(data['other_number'] != null ? data['other_number'].substring(1) : data['other_number']);
        $('#txtContactPhone2').val(data['other_number_2'] != null ? data['other_number_2'].substring(1) : data['other_number_2']);
        $('#txtContactMobile').val(data['mobile_number'] != null ? data['mobile_number'].substring(1) : data['mobile_number']);
        $('#txtContactFax').val(data['fax']);
        $('#txtContactEmail').val(data['email']);

        if($('#verify-ssn').length != 0){
            $('#verify-ssn').prop('checked',false);
            if(data['ssn_verified'] == 1){
                $('#verify-ssn').prop('checked',true);
            }   
        }

        if($('#txtEmail').val() == "" && $('#isOrigCon').val() == '1'){
            $('#mobileNumber').text('*');
        }
        
        $('#editContact').modal('show');
    });
}

function editBranchContact(id) {
    $.getJSON('/merchants/branch_contact_info/' + id, null, function (data) {

        $('#contID').val(data['id']);
        $('#isOrigCon').val(data['is_original_contact']);
        $('#txtFirstName').val(data['first_name']);
        $('#txtMiddleInitial').val(data['middle_name']);
        $('#txtLastName').val(data['last_name']);

        $('#txtDOB').val(data['dob']);
        $('#txtTitle').val(data['position']);
        $('#txtSSN').val(data['ssn']);

        $('#txtDateAcquired').val(data['business_acquired_date']);
        $('#txtPercentageOwnership').val(data['ownership_percentage']);
        $('#txtIssuedID').val(data['issued_id']);
        $('#txtExpDate').val(data['id_exp_date']);
        $('#txtContactPhone1').val(data['other_number'] != null ? data['other_number'].substring(1) : data['other_number']);
        $('#txtContactPhone2').val(data['other_number_2'] != null ? data['other_number_2'].substring(1) : data['other_number_2']);
        $('#txtContactMobile').val(data['mobile_number'] != null ? data['mobile_number'].substring(1) : data['mobile_number']);
        $('#txtContactFax').val(data['fax']);
        $('#txtContactEmail').val(data['email']);

        if($('#verify-ssn').length != 0){
            $('#verify-ssn').prop('checked',false);
            if(data['ssn_verified'] == 1){
                $('#verify-ssn').prop('checked',true);
            }   
        }

        if($('#txtEmail').val() == "" && $('#isOrigCon').val() == '1'){
            $('#mobileNumber').text('*');
        }
        
        $('#editContact').modal('show');
    });
}



function createContact() {
    $('#contID').val(-1);
    $('#txtFirstName').val('');
    $('#txtMiddleInitial').val('');
    $('#txtLastName').val('');
    
    $('#txtDOB').val('');
    $('#txtTitle').val('');
    $('#txtSSN').val('');
    
    $('#txtDateAcquired').val('');
    $('#txtPercentageOwnership').val('');
    $('#txtIssuedID').val('');
    $('#txtExpDate').val('');
    $('#txtContactPhone1').val('');
    $('#txtContactPhone2').val('');
    $('#txtContactMobile').val('');
    $('#txtContactFax').val('');
    $('#txtContactEmail').val('');
    $('#mobileNumber').text('');
    $('#editContact').modal('show');
}

function validate_numeric_input(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    var regex = /[0-9\b\t]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
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

function verifyEmail(id) {
    if (confirm("This process will reset the user's current password to verify the email.Proceed?")) {
        showLoadingModal("Sending Email... Please wait.....");
        $.getJSON('/partners/resend-email-verification/' + id, null, function (data) {
            closeLoadingModal();
            alert(data.message);
        });
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
};

function isEmail(email) {
    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(email);
}

function isExpired(expDate) {
    // Parse the date parts to integers
    var parts = expDate.split("/");
    var day = parseInt(parts[1], 10);
    var month = parseInt(parts[0], 10);
    var year = parseInt(parts[2], 10);

    var dateNow = new Date;
    var yearNow = parseInt(dateNow.getFullYear());
    var monthNow = parseInt(dateNow.getMonth()) + 1;
    var today = parseInt(dateNow.getDate());

    if ((year < yearNow) ||
        (year <= yearNow && month < monthNow) ||
        (year <= yearNow && month <= monthNow && day < today)) {
        return false;
    } else {
        return true;
    }
}

function validDateAcquired(expDate) {
    // Parse the date parts to integers
    var parts = expDate.split("/");
    var day = parseInt(parts[1], 10);
    var month = parseInt(parts[0], 10);
    var year = parseInt(parts[2], 10);

    var dateNow = new Date;
    var yearNow = parseInt(dateNow.getFullYear());
    var monthNow = parseInt(dateNow.getMonth()) + 1;
    var today = parseInt(dateNow.getDate());

    if ((year > yearNow) ||
        (year >= yearNow && month > monthNow) ||
        (year >= yearNow && month >= monthNow && day > today)) {
        return false;
    } else {
        return true;
    }
}

function validateReqFields(errors) {
    if (jQuery.isEmptyObject(errors)) {
        return true;
    } else {
        for (var key in errors) {
            var value = errors[key];
            // if (!document.getElementById(key + '-error')) {
            document.getElementById(key).style.borderColor = "red";
            $('#' + key + '-error small').text(value); // $('#' + key).after('<span id="' + key + '-error" style="color:red"><small>' + value + '</small></span>');

            // }
        }
        return false;
    }
}

function validateData(table, field, value, id, includeStatus, prefix, message){
    //var fieldValue = value.value;
    // if(fieldValue.trim()==""){
    //     alert('Field should not be empty');
    //     value.focus();
    //     value.value='';
    //     return false;    
    // }
    $.getJSON('/partners/validateField/'+table+'/'+field+'/'+value.value+'/'+id+'/'+includeStatus+'/'+prefix, null, function(data) {
        if (data){
            alert(message);
            value.value='';
            value.focus();
            return false;
        } else {
            return value.value;
        }
    });         
}

function cancelMerchant(id) {
    swal({
        title: 'Reason of Action',
        input: 'text',
        inputValidator: (value) => {
            return new Promise((resolve) => {
                resolve()
            })
        },
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#808080',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Proceed',
        cancelButtonText: 'Close'
    }).then((result) => {
        if (result.value) {
            axios.post('/merchants/cancel_merchant/' + id, {
                reason_of_action: result.value
            })
                .then(response => { 
                    location.reload(true)
                })
                .catch(error => {
                    console.log(error)
                })
        }
    })
}

function declineMerchant(merchantId) {
    swal({
        title: 'Reason of Action',
        input: 'text',
        inputValidator: (value) => {
            return new Promise((resolve) => {
                resolve()
            })
        },
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#808080',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Proceed',
        cancelButtonText: 'Close'

    }).then((result) => {
        if (result.value) {
            axios.post(`/merchants/${merchantId}/decline`, {
                reason_of_action: result.value
            })
                .then(response => {
                    alert(response.data.message);
                    location.reload(true)
                })
                .catch(error => {
                    console.log(error)
                })
        }
    })
}

function declineBranch(merchantId) {
    swal({
        title: 'Reason of Action',
        input: 'text',
        inputValidator: (value) => {
            return new Promise((resolve) => {
                resolve()
            })
        },
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#808080',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Proceed',
        cancelButtonText: 'Close'

    }).then((result) => {
        if (result.value) {
            axios.post(`/merchants/${merchantId}/declineBranch`, {
                reason_of_action: result.value
            })
                .then(response => {
                    alert(response.data.message);
                    location.reload(true)
                })
                .catch(error => {
                    console.log(error)
                })
        }
    })
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


//http://local.goetu.com/storage/merchant_attachment/140MaginhawaSalesFrom01-01-2018To02-01-2018_1525432726.csv
window.convertToFloat = convertToFloat;
window.validateField = validateField;
window.editContact = editContact;
window.createContact = createContact;
window.validate_numeric_input = validate_numeric_input;
window.UploadAttachment = UploadAttachment;
window.createPaymentGateway = createPaymentGateway;
window.editPaymentGateway = editPaymentGateway;
window.cancelReply = cancelReply;
window.addReply = addReply;
window.showAllSpecific = showAllSpecific;
window.hideAllSpecific = hideAllSpecific;
window.showAllReplies = showAllReplies;
window.hideAllReplies = hideAllReplies;
window.verifyEmail = verifyEmail;
window.isExpired = isExpired;
window.validDateAcquired = validDateAcquired;
window.validateData = validateData;
window.cancelMerchant = cancelMerchant;
window.declineMerchant = declineMerchant;
window.editBranchContact = editBranchContact;
window.declineBranch = declineBranch;
window.isValidZip = isValidZip;
window.editMID = editMID;
window.createMID = createMID;