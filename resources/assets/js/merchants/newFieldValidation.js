$(document).ready(function () {

    $("input[type='text']").attr('maxLength','50');
    $("#txtMiddleInitial").attr('maxLength','1');

    $('.s2-country').select2({
        templateSelection: formatCountryDropdown,
        templateResult: formatCountryDropdown
    })

    $('.s2-state').select2({
        templateSelection: formatStateDropdown,
        templateResult: formatStateDropdown
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var arrSelect2 = [
            '#txtCountry',
            '#txtState',
            '#txtCity',
            '#txtBillingCountry',
            '#txtBillingState',
            '#txtBillingCity',
            '#txtShippingCountry',
            '#txtShippingState',
            '#txtShippingCity',
            '#txtMailingCountry',
            '#txtMailingState',
            '#txtMailingCity'
        ];
        $.each(arrSelect2, function(i, el){
            $(el).select2({ width: 'resolve' });  
        });

        $('.s2-country').select2({
            templateSelection: formatCountryDropdown,
            templateResult: formatCountryDropdown
        })

        $('.s2-state').select2({
            templateSelection: formatStateDropdown,
            templateResult: formatStateDropdown
        });
    })
    
    var img = $(".profile-pic");
    // Create dummy image to get real width and height
    $("<img>").attr("src", $(img).attr("src")).load(function(){
        var realWidth = this.width;
        var realHeight = this.height;
        var ratio  = realWidth / realHeight;
        if (ratio > 1) {
            var newWidth = Math.round(ratio * 200);
            $(img).css('height','200px');
            $(img).css('width',newWidth);
        }
    });
    
    $('#txtBillingZip').on('change', function(){
        var zipLen = 5;
        var stateEl = '#txtBillingState';
        
        if ($('#txtBillingCountry').val() == 'Philippines') {
            zipLen = 4;
            stateEl = '#txtBillingStatePH';
        } else if ($('#txtBillingCountry').val() == 'China') {
            zipLen = 6;
            stateEl = '#txtBillingStateCN';
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#txtBillingZip';
            var zipErrEl = '#txtBillingZip-error small';
            var cityEl = '#txtBillingCity';

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

    $('#txtZip').on('change', function(){
        var zipLen = 5;
        
        if ($('#txtCountry').val() == 'Philippines') {
            zipLen = 4;
        } else if ($('#txtCountry').val() == 'China') {
            zipLen = 6;
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#txtZip';
            var zipErrEl = '#txtZip-error small';
            var cityEl = '#txtCity';
            var stateEl = '#txtState';

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

    $('#txtMailingZip').on('change', function(){
        var zipLen = 5;
        var stateEl = '#txtMailingState';
        
        if ($('#txtMailingCountry').val() == 'Philippines') {
            zipLen = 4;
            stateEl = '#txtMailingStatePH';
        } else if ($('#txtMailingCountry').val() == 'China') {
            zipLen = 6;
            stateEl = '#txtMailingStateCN';
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#txtMailingZip';
            var zipErrEl = '#txtMailingZip-error small';
            var cityEl = '#txtMailingCity';

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

    $('#txtShippingZip').on('change', function(){
        var zipLen = 5;
        
        if ($('#txtCountry').val() == 'Philippines') {
            zipLen = 4;
        } else if ($('#txtCountry').val() == 'China') {
            zipLen = 6;
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#txtShippingZip';
            var zipErrEl = '#txtShippingZip-error small';
            var cityEl = '#txtShippingCity';
            var stateEl = '#txtShippingState';

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

function validReqFields() {
    var errors = [];
    var arrReq = [
        'txtBusinessName',
        'txtAddress',
        'txtCountry',
        'txtFirstName',
        'txtLastName'
    ];
    $.each(arrReq, function(i, val){
        if ($('#' + val).val() == '') {
            errors.push(i);
            $('#' + val).parents('.form-group').addClass('has-error');
            $('#' + val + '-error small').text('Required.');
        } else {
            $('#' + val).parents('.form-group').removeClass('has-error');
            $('#' + val + '-error small').text('');
        }
    }); 

    //

    if ($('#txtBusinessDate').length > 0){
        if ($('#txtBusinessDate').val().trim() != "" 
            && !isValidMYDate($('#txtBusinessDate').val())) {
            errors.push(errors.length + 1);
            $('#txtBusinessDate').parents('.form-group').addClass('has-error');
            $('#txtBusinessDate-error small').text('Invalid date.');
        } else {
            $('#txtBusinessDate').parents('.form-group').removeClass('has-error');
            $('#txtBusinessDate-error small').text('');
        }
    }

    if ($('#url').length > 0){
        if ($('#url').val().trim() != "" 
            && !isValidUrl($('#url').val())) {
            errors.push(errors.length + 1);
            $('#url').parents('.form-group').addClass('has-error');
            $('#url-error small').text('Invalid url.');
        } else {
            $('#url').parents('.form-group').removeClass('has-error');
            $('#url-error small').text('');
        }
    }

    if ($('#txtPhoneNumber').length > 0){
        if ($('#txtPhoneNumber').val().trim() != "" ) {
            if ($('#txtPhoneNumber').val().length != 12
                && ($('#txtCountry').val() == 'United States' 
                || $('#txtCountry').val() == 'Philippines')) {
                errors.push(errors.length + 1);
                $('#txtPhoneNumber').parents('.form-group').addClass('has-error');
                $('#txtPhoneNumber-error small').text('Invalid phone number.');
            } else if ($('#txtPhoneNumber').val().length != 14
                && $('#txtCountry').val() == 'China') {
                errors.push(errors.length + 1);
                $('#txtPhoneNumber').parents('.form-group').addClass('has-error');
                $('#txtPhoneNumber-error small').text('Invalid phone number.');
            } else {
                $('#txtPhoneNumber').parents('.form-group').removeClass('has-error');
                $('#txtPhoneNumber-error small').text('');
            }
        }
    }

    if ($('#txtEmail').length > 0){
        if ($('#txtEmail').val().trim() != ""
            && !isEmail($('#txtEmail').val())) {
            errors.push(errors.length + 1);
            $('#txtEmail').parents('.form-group').addClass('has-error');
            $('#txtEmail-error small').text('Invalid email format.');
        } else {
            $('#txtEmail').parents('.form-group').removeClass('has-error');
            $('#txtEmail-error small').text('');
        }

        if ($('#txtEmail').val().trim() == "" &&
            $('#txtContactMobileNumber').val().trim() == "") {
            errors.push(errors.length + 1);
            $('#txtEmail').parents('.form-group').addClass('has-warning');
            $('#txtEmail-error small').text('Merchant must have either email address or contact mobile number.');
            $('#txtContactMobileNumber').parents('.form-group').addClass('has-warning');
            $('#txtContactMobileNumber-error small').text('Merchant must have either email address or contact mobile number.');
        } else {
            $('#txtEmail').parents('.form-group').removeClass('has-warning');
            $('#txtEmail-error small').text('');
            $('#txtContactMobileNumber').parents('.form-group').removeClass('has-warning');
            $('#txtContactMobileNumber-error small').text('');
        }
    }
    
    return errors.length < 1;
}

function isValidMYDate(dateString) {
    if (dateString != "") {

        // First check for the pattern
        if (!/^\d{1,2}\/\d{4}$/.test(dateString))
            return false;
    
        // Parse the date parts to integers
        var parts = dateString.split("/");
        var month = parseInt(parts[0], 10);
        var year = parseInt(parts[1], 10);
    
        // Check the ranges of month and year
        return (year > 1000 && year < 3001) && (month > 0 && month < 13);
    }
}

function isValidUrl(str) {
    var pattern = /^(https?:\/\/)?([a-zA-Z0-9]([a-zA-ZäöüÄÖÜ0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}(\/?)$/;

    return pattern.test(str);
}

function isEmail(email) {
    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    
    return regex.test(email);
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
    $country.find("img").attr("src", baseUrl + country.element.dataset.abbr.toLowerCase() + "-flag.png");
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

window.validReqFields = validReqFields;
window.isValidMYDate = isValidMYDate;
window.isValidUrl = isValidUrl; 
window.isEmail = isEmail; 