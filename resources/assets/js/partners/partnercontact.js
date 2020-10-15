$('.datatables').dataTable();


$(document).ready(function() {
	
    var str =$('#txtSSNDisplay').val();
    str = str.split('-').join('');
    str = str.replace(/\d(?=\d{4})/g, "*");
    $('#txtSSNDisplay').val(str);

	$('#txtContactSSN1').mask("999-99-9999");
	// $('#txtContactPhone1_1').mask("-999-999-9999");
	// $('#txtContactPhone1_2').mask("-999-999-9999");

    $('#txtFax').mask("-999-999-9999");
    // $('#txtContactFax1').mask("-999-999-9999");
    // $('#txtContactFax2').mask("-999-999-9999");

	// $('#mobile_number').mask("-999-999-9999");
	// $('#txtContactMobile1_2').mask("-999-999-9999");

    $('#txtContactDOB1').mask("99/99/9999");
    
    $('.select2').select2({
        templateSelection: formatSelect2,
        templateResult: formatSelect2Result
    })

	$('.assigntome').change(function () {
		$('.assigntodiv').toggle();
	});

    $('#txtContactCountry1').change(function (){
		var country = $('option:selected', this).attr('data-code');
		var url = '/partners/getStateByCountry/'+country;	
        var usph_country_mask = "999-999-9999";
        var cn_country_mask = "9-999-999-9999";
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

		$.ajax({
		  url: url,
		}).done(function(items){
		  let option ="";
          var state = $('#state').val();
		  $.each(items.states, function(key, item){
		     if(item.abbr == state){
                option += '<option selected value="' + item.abbr +  '">' + item.name  + '</option> ';
            }
            else{
             option += '<option value="' + item.abbr +  '">' + item.name  + '</option> ';
            }    
		  });

          if (country == 'US' || country == 'PH') {
            $('#txtContactPhone1_1').unmask(cn_country_mask);
            $('#txtContactPhone1_1').mask(usph_country_mask);
            $('#txtContactPhone1_2').unmask(cn_country_mask);
            $('#txtContactPhone1_2').mask(usph_country_mask);

            $('#mobile_number').unmask(cn_country_mask);
            $('#mobile_number').mask(usph_country_mask);
            $('#txtContactMobile1_1').unmask(cn_country_mask);
            $('#txtContactMobile1_1').mask(usph_country_mask);
            $('#txtContactMobile1_2').unmask(cn_country_mask);
            $('#txtContactMobile1_2').mask(usph_country_mask);

            $('#txtContactFax1').unmask(cn_country_mask);
            $('#txtContactFax1').mask(usph_country_mask);

            if (country == 'US') {
                $('#txtContactZip1').unmask();
                $('#txtContactZip1').mask(us_zip_mask);
            }else if(country == 'PH'){
                $('#txtContactZip1').unmask();
                $('#txtContactZip1').mask(ph_zip_mask);
            }
          }
          if (country == 'CN') {
            $('#txtContactPhone1_1').unmask(usph_country_mask);
            $('#txtContactPhone1_1').mask(cn_country_mask);
            $('#txtContactPhone1_2').unmask(usph_country_mask);
            $('#txtContactPhone1_2').mask(cn_country_mask);

            $('#mobile_number').unmask(usph_country_mask);
            $('#mobile_number').mask(cn_country_mask);
            $('#txtContactMobile1_1').unmask(usph_country_mask);
            $('#txtContactMobile1_1').mask(cn_country_mask);
            $('#txtContactMobile1_2').unmask(usph_country_mask);
            $('#txtContactMobile1_2').mask(cn_country_mask);

            $('#txtContactFax1').unmask(usph_country_mask);
            $('#txtContactFax1').mask(cn_country_mask);

            $('#txtContactZip1').unmask();
            $('#txtContactZip1').mask(cn_zip_mask);
          }

		  $('#txtContactState1').empty(); //remove all child nodes
		  var newOption = option;
		  $('#txtContactState1').append(newOption);
		  $('#txtContactState1').trigger("chosen:updated");  
		  jQuery("label[for='contactPhone1']").html(items.country[0].country_calling_code);
		});
    });
    $('#txtContactCountry1').trigger('change'); 

    $('#txtOwnership').change(function () {
        var ownership = document.getElementById("txtOwnership");
        var ownership_selectedText = ownership.options[ownership.selectedIndex].text;
        var ownership_selectedValue = ownership.options[ownership.selectedIndex].value;
        var ein_mask = "99-9999999";
        var ssn_mask = "999-99-9999";
        if (ownership_selectedText=="Individual / Sole Proprietorship"){ 
            $('#txtSSN').unmask(ein_mask);
            $('#txtSSN').mask(ssn_mask);
            jQuery("label[for='ssn']").html("SSN: <span class=\"req\"></span>");     
        } else if (ownership_selectedText=="Partnership"){ 
            $('#txtSSN').unmask(ssn_mask);
            $('#txtSSN').mask(ein_mask);
            jQuery("label[for='ssn']").html("EIN: <span class=\"req\"></span>");   
        } else if (ownership_selectedText=="Private Corp."){ 
            $('#txtSSN').unmask(ssn_mask);
            $('#txtSSN').mask(ein_mask);      
        } else if (ownership_selectedText=="Limited Liability Co."){ 
            $('#txtSSN').unmask(ssn_mask); 
            $('#txtSSN').mask(ein_mask);
            jQuery("label[for='ssn']").html("EIN: <span class=\"req\"></span>");   
        } else {
            $('#txtSSN').unmask(ein_mask); 
            $('#txtSSN').mask(ssn_mask); 
            jQuery("label[for='ssn']").html("EIN: <span class=\"req\"></span>");      
        }
    });
    $('#txtOwnership').trigger('change');

    $('#txtCountry').change(function (){
        var country = $('option:selected', this).attr('data-code');
        var url = '/partners/getStateByCountry/'+country; 
        var usph_country_mask = "999-999-9999";
        var cn_country_mask = "9-999-999-9999";  
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $.ajax({
          url: url,
        }).done(function(items){
          let option ="";
          var state = $('#state').val();
          $.each(items.states, function(key, item){
            if(item.abbr == state){
                option += '<option selected value="' + item.abbr +  '">' + item.name  + '</option> ';
            }
            else{
             option += '<option value="' + item.abbr +  '">' + item.name  + '</option> ';
            }        
          });

           if (country == 'US' || country == 'PH') {
            $('#txtBusinessPhone1').unmask(cn_country_mask);
            $('#txtBusinessPhone1').mask(usph_country_mask);
            $('#txtBusinessPhone2').unmask(cn_country_mask); 
            $('#txtBusinessPhone2').mask(usph_country_mask); 

            if (country == 'US') {
                $('#txtBusinessZip').unmask();
                $('#txtBusinessZip').mask(us_zip_mask);
            }else if(country == 'PH'){
                $('#txtBusinessZip').unmask();
                $('#txtBusinessZip').mask(ph_zip_mask);
            }
          }
          if (country == 'CN') {
            $('#txtBusinessPhone1').unmask(usph_country_mask);
            $('#txtBusinessPhone1').mask(cn_country_mask);
            $('#txtBusinessPhone2').unmask(usph_country_mask);
            $('#txtBusinessPhone2').mask(cn_country_mask); 

            $('#txtBusinessZip').unmask();
            $('#txtBusinessZip').mask(cn_zip_mask);
          }

          $('#txtState').empty(); //remove all child nodes
          var newOption = option;
          $('#txtState').append(newOption);
          $('#txtState').trigger("chosen:updated");
          jQuery("label[for='businessPhone']").html(items.country[0].country_calling_code);
        });
    });
    $('#txtCountry').trigger('change'); 

    $('#txtMailingCountry').change(function (){
        var country = $('option:selected', this).attr('data-code');
        var url = '/partners/getStateByCountry/'+country;
        var ph_zip_mask = "9999";
        var us_zip_mask = "99999";
        var cn_zip_mask = "999999";

        $.ajax({
          url: url,
        }).done(function(items){
          let option ="";
          var state = $('#mailing_state').val();
          $.each(items.states, function(key, item){
            if(item.abbr == state){
                option += '<option selected value="' + item.abbr +  '">' + item.name  + '</option> ';
            }
            else{
             option += '<option value="' + item.abbr +  '">' + item.name  + '</option> ';
            }        
          });

           if (country == 'US') {
            $('#txtMailingZip').unmask();
            $('#txtMailingZip').mask(us_zip_mask);
          }else if(country == 'CN'){
            $('#txtMailingZip').unmask();
            $('#txtMailingZip').mask(cn_zip_mask);
          }else if(country == 'PH'){
            $('#txtMailingZip').unmask();
            $('#txtMailingZip').mask(ph_zip_mask);
          }

          $('#txtMailingState').empty(); //remove all child nodes
          var newOption = option;
          $('#txtMailingState').append(newOption);
          $('#txtMailingState').trigger("chosen:updated");  
        });
    });
    $('#txtMailingCountry').trigger('change'); 

    $('#chkSameAsBusiness').click(function() {
        if(this.checked)
        {
            var state = $('#txtState').val();
            var country = $('#txtCountry').val();
            $('#txtMailingCountry').val(country).trigger('change');
  
            $('#txtMailingAddress1').val($('#txtBusinessAddress1').val());
            $('#txtMailingAddress2').val($('#txtBusinessAddress2').val());
            $('#txtMailingCity').val($('#txtCity').val());
            $('#txtMailingZip').val($('#txtBusinessZip').val());
            var delay = 1000;
            setTimeout(function() {
                $("#txtMailingState").val(state);
            }, delay);
        } else {
            $('#txtMailingCountry').val("China").trigger('change');
            
            $('#txtMailingAddress1').val("");
            $('#txtMailingAddress2').val("");
            $('#txtMailingCity').val("");
            $('#txtMailingZip').val("");
        }
    }); 

    $('#frmPartnerContact').submit(function(e){
        var errors = {};
        /* if(!validateField('txtContactFirstName1','First Name is required'))
        {
            return false;
        } */
        if ($('#txtContactFirstName1').val().trim() == "") {
            var id = "txtContactFirstName1";
            var msg = "First Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtContactFirstName1').style.removeProperty('border');
            $('#txtContactFirstName1-error small').text('');
        }
   		
   		/* if(!validateField('txtContactLastName1','Last Name is required'))
        {
            return false;
        } */
        if ($('#txtContactLastName1').val().trim() == "") {
            var id = "txtContactLastName1";
            var msg = "Last Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtContactLastName1').style.removeProperty('border');
            $('#txtContactLastName1-error small').text('');
        }

        // if(!validateField('txtContactTitle1','Title is required'))
        // {
        //     return false;
        // }

        // if(!validateField('txtContactSSN1','SSN is required'))
        // {
        //     return false;
        // }

        /* if(!validateField('txtContactDOB1','Date of Birth is required'))
        {
            return false;
        } */
        /* if ($('#txtContactDOB1').val().trim() == "") {
            var id = "txtContactDOB1";
            var msg = "Date of Birth is required.";
            errors[id] = msg;
        } else {
            document.getElementById('txtContactDOB1').style.removeProperty('border');
            $('#txtContactDOB1-error small').text('');
        } */

        // if(!validateField('txtContactPhone1_1','Contact Phone 1 is required'))
        // {
        //     return false;
        // }

        // if(!validateField('txtContactPhone1_2','Contact Phone 2 is required'))
        // {
        //     return false;
        // }

    	/* if(!validateField('mobile_number','Mobile is required'))
        {
            return false;
        } */

        // if ($('#partner_email').val() == "" && 
        //     $('#mobile_number').val() == "" && 
        //     $('#is_orig_con').val() == '1') {
        //     var id = "mobile_number";
        //     var msg = "Partner must have either Business Email or Mobile Number.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('mobile_number').style.removeProperty('border');
        //     $('#mobile_number-error small').text('');
        // }

        // if(!validateField('txtContactFax1','Fax is required'))
        // {
        //     return false;
        // }

        // if(!validateField('email_address','Email is required'))
        // {
        //     return false;
        // }

        // if(!validateField('txtContactHomeAddress1_1','Home Address 1 is required'))
        // {
        //     return false;
        // }

        // if(!validateField('txtContactCity1','City is required'))
        // {
        //     return false;
        // }

        // if(!validateField('txtContactZip1','Zip is required'))
        // {
        //     return false;
        // }

        /* if (!isValidDateEx($('#txtContactDOB1').val())){
            CustomAlert("Please input valid date.")
            return false;    
        }  */
        if ($('#txtContactDOB1').val().trim() != "") {
            if (!isValidDateEx($('#txtContactDOB1').val())){
                var id = "txtContactDOB1";
                var msg = "Please input valid date.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactDOB1').style.removeProperty('border');
                $('#txtContactDOB1-error small').text('');
            }
        }else{
            var id = "txtContactDOB1";
            var msg = "Please input valid date.";
            errors[id] = msg;           
        }

       /*  if($('#txtOwnershipPercentage1').val()=="")
        {
            alert('Percentage should be numeric.');
            return false;
        } */
        if ($('#txtOwnershipPercentage1').val().trim() == "") {
            // var id = "txtOwnershipPercentage1";
            // var msg = "Percentage should be numeric.";
            // errors[id] = msg;
            $('#txtOwnershipPercentage1').val(0);
        } else {
            document.getElementById('txtOwnershipPercentage1').style.removeProperty('border');
            $('#txtOwnershipPercentage1-error small').text('');
        }

        /* if($('#txtOwnershipPercentage1').val().trim() != "") {
            if(parseFloat($('#txtOwnershipPercentage1').val()) > 100 || parseFloat($('#txtOwnershipPercentage1').val()) < 0){
                alert("Percentage should be 1-100", 1)
                return false;
            }      
        } */
        if ($('#txtOwnershipPercentage1').val().trim() != "") {
            if($('#txtOwnershipPercentage1').val().trim() != "") {
                if(parseFloat($('#txtOwnershipPercentage1').val()) > 100 || 
                    parseFloat($('#txtOwnershipPercentage1').val()) < 0){
                    var id = 'txtOwnershipPercentage1';
                    var msg = "Percentage should be 1-100";
                    errors[id] = msg;
                } else {
                    document.getElementById('txtOwnershipPercentage1').style.removeProperty('border');
                    $('#txtOwnershipPercentage1-error small').text('');
                }
            }      
        }

        if ($('#txtContactEmail1').val().trim() != "") {
            if (!isEmail($('#txtContactEmail1').val())) {
                var id = "txtContactEmail1";
                var msg = "Invalid Email Format.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactEmail1').style.removeProperty('border');
                $('#email_address-error small').text('');
            }
        }else{
                document.getElementById('txtContactEmail1').style.removeProperty('border');
                $('#email_address-error small').text('');            
        }


        if ($('#txtContactPhone1_1').val().trim() != "") {
            if ($('#txtContactPhone1_1').val().length != 12 && ($('#txtContactCountry1').val() == 'United States' || $('#txtContactCountry1').val() == 'Philippines')) {
                var id = "txtContactPhone1_1";
                var msg = "Invalid Number.";
                errors[id] = msg;

            } else if ($('#txtContactPhone1_1').val().length != 14 && $('#txtCountry').val() == 'China') {
                var id = "txtContactPhone1_1";
                var msg = "Invalid Number.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactPhone1_1').style.removeProperty('border');
                $('#txtContactPhone1_1-error small').text('');
            }
        }else{
            document.getElementById('txtContactPhone1_1').style.removeProperty('border');
            $('#txtContactPhone1_1-error small').text('');            
        }

        if ($('#txtContactPhone1_2').val().trim() != "") {
            if ($('#txtContactPhone1_2').val().length != 12 && ($('#txtContactCountry1').val() == 'United States' || $('#txtContactCountry1').val() == 'Philippines')) {
                var id = "txtContactPhone1_2";
                var msg = "Invalid Number.";
                errors[id] = msg;
            } else if ($('#txtContactPhone1_2').val().length != 14 && $('#txtCountry').val() == 'China') {
                var id = "txtContactPhone1_2";
                var msg = "Invalid Number.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactPhone1_2').style.removeProperty('border');
                $('#txtContactPhone1_2-error small').text('');
            }
        }else{
            document.getElementById('txtContactPhone1_2').style.removeProperty('border');
            $('#txtContactPhone1_2-error small').text('');            
        }

        if ($('#txtContactMobile1_1').val().trim() != "") {
            if ($('#txtContactMobile1_1').val().length != 12 && ($('#txtContactCountry1').val() == 'United States' || $('#txtContactCountry1').val() == 'Philippines')) {
                var id = "txtContactMobile1_1";
                var msg = "Invalid Number.";
                errors[id] = msg;
            } else if ($('#txtContactMobile1_1').val().length != 14 && $('#txtCountry').val() == 'China') {
                var id = "txtContactMobile1_1";
                var msg = "Invalid Number.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactMobile1_1').style.removeProperty('border');
                $('#txtContactMobile1_1-error small').text('');
            }
        }else{
            document.getElementById('txtContactMobile1_1').style.removeProperty('border');
            $('#txtContactMobile1_1-error small').text('');            
        }

        if ($('#txtContactMobile1_2').val().trim() != "") {
            if ($('#txtContactMobile1_2').val().length != 12 && ($('#txtContactCountry1').val() == 'United States' || $('#txtContactCountry1').val() == 'Philippines')) {
                var id = "txtContactMobile1_2";
                var msg = "Invalid Number.";
                errors[id] = msg;
            } else if ($('#txtContactMobile1_2').val().length != 14 && $('#txtCountry').val() == 'China') {
                var id = "txtContactMobile1_2";
                var msg = "Invalid Number.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactMobile1_2').style.removeProperty('border');
                $('#txtContactMobile1_2-error small').text('');
            }
        }else{
            document.getElementById('txtContactMobile1_2').style.removeProperty('border');
            $('#txtContactMobile1_2-error small').text('');            
        }

        if ($('#txtContactFax1').val().trim() != "") {
            if ($('#txtContactFax1').val().length != 12 && ($('#txtContactCountry1').val() == 'United States' || $('#txtContactCountry1').val() == 'Philippines')) {
                var id = "txtContactFax1";
                var msg = "Invalid Number.";
                errors[id] = msg;
            } else if ($('#txtContactFax1').val().length != 14 && $('#txtCountry').val() == 'China') {
                var id = "txtContactFax1";
                var msg = "Invalid Number.";
                errors[id] = msg;
            } else {
                document.getElementById('txtContactFax1').style.removeProperty('border');
                $('#txtContactFax1-error small').text('');
            }
        }else{
            document.getElementById('txtContactFax1').style.removeProperty('border');
            $('#txtContactFax1-error small').text('');            
        }

        if (!validateReqFields(errors)) {
            return false;
        }
        return true;

    });
    
    /* $("#txtContactZip1").keyup(function() {
        var el = $(this);

        if (el.val().length == 5) {
            $.ajax({
                url: "/merchants/getCityState/" + el.val(),
                type: "GET",
            }).done(function(data) {
                $('#txtContactCity1').val(data.city);
                $('#txtContactState1').val(data.abbr).trigger('change');
            }).fail(function(data) {
                alert('Error, not a US zip code.');
                $('#txtContactZip1').val('');
            });
        }
    }); */


    
});

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


function validateField(element,msg)
{
    if($('#'+element).val().trim() == ""){
        document.getElementById(element).style.borderColor = "red";
        alert(msg);
        return false;
    }else{
        document.getElementById(element).style.removeProperty('border');
        return true;
    }            
}  

function isValidDateEx(dateString)
{
    // First check for the pattern
    if(!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
        return false;

    // Parse the date parts to integers
    var parts = dateString.split("/");
    var day = parseInt(parts[1], 10);
    var month = parseInt(parts[0], 10);
    var year = parseInt(parts[2], 10);

    // Check the ranges of month and year
    if(year < 1000 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
};

function load_partners(){ 
    $.getJSON('/partners/getPartnersData', null, function(data) {

        $.each(data, function(key, item){
            var oTable = $('#tbl'+key).dataTable( {"bRetrieve": true} );
            oTable.fnClearTable();
            if (item.length >0){
                oTable.fnAddData(item);    
            } 
        });
        
    });
    
}

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
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

function convertToFloat(field)
{
    if (isNumber($(field).val()))
    {
        $(field).val(parseFloat($(field).val()));    
    }

}

function isNumber(n) 
{
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function validateEmail(id) {
    var email = $('#'+id).val();
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(!re.test(email)){
        alert('Invalid Email Format!');
        $('#'+id).val('');
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
                $('#'+ key + '-error small').text(value); //$('#' + key).after('<span id="' + key + '-error" style="color:red"><small>' + value + '</small></span>');
            // }
        }
        return false;
    }
}

function isEmail(email) {
    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!regex.test(email)) {
        return false;
    }
    return true;
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

function showSSN(){
    if($('#showSSN').html() == 'Update SSN'){
        $('#txtSSNDisplay').hide();
        $('#txtContactSSN1').show();
        $('#showSSN').html('Undo');
    }else{
        $('#txtSSNDisplay').show();
        $('#txtContactSSN1').val('');
        $('#txtContactSSN1').hide();
        $('#showSSN').html('Update SSN');
    }
}

window.isNumberKey = isNumberKey;
window.validateData = validateData;
window.convertToFloat = convertToFloat;
window.validate_numeric_input = validate_numeric_input;
window.validateEmail = validateEmail;
window.isValidZip = isValidZip;
window.showSSN = showSSN;