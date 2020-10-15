$(document).ready(function () {

    $('#txtExtension1').mask('999', { clearIfNotMatch: true })
    $('#txtExtension2').mask('999', { clearIfNotMatch: true })
    $('#txtExtension3').mask('999', { clearIfNotMatch: true })

    $("input[type='text']").attr('maxLength','50');
    $("#txtContactMiddleInitial1").attr('maxLength','1');

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
    /* $('#txtState').change(function () {
        var state = $('option:selected', this).attr('data-code');
        var url = '/partners/getCityByState/' + state;

        $.ajax({
            url: url,
        }).done(function (items) {
            let option = "";
            $.each(items.cities, function (key, item) {
                option += '<option value="' + item.city + '">' + item.city + '</option> ';
            });

            $('#txtCity').empty(); //remove all child nodes
            var newOption = option;
            $('#txtCity').append(newOption);
        });
    }); */

    $('#txtBusinessZip').on('change', function(){
        var zipLen = 5;
        
        if ($('#txtCountry').val() == 'Philippines') {
            zipLen = 4;
        } else if ($('#txtCountry').val() == 'China') {
            zipLen = 6;
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#txtBusinessZip';
            var zipErrEl = '#txtBusinessZip-error small';
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

    $('#txtBillingZip').on('change', function(){
        var zipLen = 5;
        
        if ($('#txtBillingCountry').val() == 'Philippines') {
            zipLen = 4;
        } else if ($('#txtBillingCountry').val() == 'China') {
            zipLen = 6;
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#txtBillingZip';
            var zipErrEl = '#txtBillingZip-error small';
            var cityEl = '#txtBillingCity';
            var stateEl = '#txtBillingState';

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
        
        if ($('#txtMailingCountry').val() == 'Philippines') {
            zipLen = 4;
        } else if ($('#txtMailingCountry').val() == 'China') {
            zipLen = 6;
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#txtMailingZip';
            var zipErrEl = '#txtMailingZip-error small';
            var cityEl = '#txtMailingCity';
            var stateEl = '#txtMailingState';

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

    $('#txtContactZip1').on('change', function(){
        var zipLen = 5;
        
        if ($('#txtContactCountry1').val() == 'Philippines') {
            zipLen = 4;
        } else if ($('#txtContactCountry1').val() == 'China') {
            zipLen = 6;
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#txtContactZip1';
            var zipErrEl = '#txtContactZip1-error small';
            var cityEl = '#txtContactCity1';
            var stateEl = '#txtContactState1';

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

    $('#txtZipAgent').on('change', function(){
        var zipLen = 5;
        
        if ($('#txtCountry').val() == 'Philippines') {
            zipLen = 4;
        } else if ($('#txtCountry').val() == 'China') {
            zipLen = 6;
        }
        
        if ($(this).val().length == zipLen) {
            var zip = $(this).val();
            var zipEl = '#txtZipAgent';
            var zipErrEl = '#txtZipAgent-error small';
            var cityEl = '#txtCityAgent';
            var stateEl = '#txtStateAgent';

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
        'txtCompanyName',
        'txtBusinessAddress1',
        'txtCountry',
        'txtBusinessPhone1',
        'txtContactFirstName1',
        'txtContactLastName1'
    ];
    $.each(arrReq, function(i, val){
        if ($('#' + val).val() == "") {
            errors.push(i);
            $('#' + val).parents('.form-group').addClass('has-error');
            $('#' + val + '-error small').text('Required.');
        } else {
            $('#' + val).parents('.form-group').removeClass('has-error');
            $('#' + val + '-error small').text('');
        }
    });

    //
    if ($('#txtBusinessDate').val().trim() != "" 
        && !isValidMYDate($('#txtBusinessDate').val())) {
        errors.push(errors.length + 1);
        $('#txtBusinessDate').parents('.form-group').addClass('has-error');
        $('#txtBusinessDate-error small').text('Invalid date.');
    } else {
        $('#txtBusinessDate').parents('.form-group').removeClass('has-error');
        $('#txtBusinessDate-error small').text('');
    }

    if ($('#txtWebsite').val().trim() != "" 
        && !isValidUrl($('#txtWebsite').val())) {
        errors.push(errors.length + 1);
        $('#txtWebsite').parents('.form-group').addClass('has-error');
        $('#txtWebsite-error small').text('Invalid url.');
    } else {
        $('#txtWebsite').parents('.form-group').removeClass('has-error');
        $('#txtWebsite-error small').text('');
    }

    if ($('#txtContactDOB1').val().trim() != "" 
        && !isValidDate($('#txtContactDOB1').val())) {
        errors.push(errors.length + 1);
        $('#txtContactDOB1').parents('.form-group').addClass('has-error');
        $('#txtContactDOB1-error small').text('Invalid date.');
    } else {
        $('#txtContactDOB1').parents('.form-group').removeClass('has-error');
        $('#txtContactDOB1-error small').text('');
    }

    if ($('#txtBusinessPhone1').val().trim() != "" ) {
        if ($('#txtBusinessPhone1').val().length != 12
            && ($('#txtCountry').val() == 'United States' 
            || $('#txtCountry').val() == 'Philippines')) {
            errors.push(errors.length + 1);
            $('#txtBusinessPhone1').parents('.form-group').addClass('has-error');
            $('#txtBusinessPhone1-error small').text('Invalid phone number.');
        } else if ($('#txtBusinessPhone1').val().length != 14
            && $('#txtCountry').val() == 'China') {
            errors.push(errors.length + 1);
            $('#txtBusinessPhone1').parents('.form-group').addClass('has-error');
            $('#txtBusinessPhone1-error small').text('Invalid phone number.');
        } else {
            $('#txtBusinessPhone1').parents('.form-group').removeClass('has-error');
            $('#txtBusinessPhone1-error small').text('');
        }
    }

    if ($('#txtEmail').val().trim() == "" &&
        $('#txtContactMobile1_1').val().trim() == "") {
        errors.push(errors.length + 1);
        $('#txtEmail').parents('.form-group').addClass('has-error');
        $('#txtEmail-error small').text('Partner must have either email or contact mobile number.');
        $('#txtContactMobile1_1').parents('.form-group').addClass('has-error');
        $('#txtContactMobile1_1-error small').text('Partner must have either email or contact mobile number.');
    } else {
        $('#txtEmail').parents('.form-group').removeClass('has-error');
        $('#txtEmail-error small').text('');
        $('#txtContactMobile1_1').parents('.form-group').removeClass('has-error');
        $('#txtContactMobile1_1-error small').text('');
        if ($('#txtEmail').val().trim() != ""
            && !isEmail($('#txtEmail').val())) {
            errors.push(errors.length + 1);
            $('#txtEmail').parents('.form-group').addClass('has-error');
            $('#txtEmail-error small').text('Invalid email format.');
        } else {
            $('#txtEmail').parents('.form-group').removeClass('has-error');
            $('#txtEmail-error small').text('');
        }
    }

    if ($('#txtOwnershipPercentage1').val().trim() != ""
        && (parseFloat($('#txtOwnershipPercentage1').val()) > 100 
        || parseFloat($('#txtOwnershipPercentage1').val()) < 0)) {
        errors.push(errors.length + 1);
        $('#txtOwnershipPercentage1').parents('.form-group').addClass('has-error');
        $('#txtOwnershipPercentage1-error small').text('Percentage should be 1-100.');
    } else {
        $('#txtOwnershipPercentage1').parents('.form-group').removeClass('has-error');
        $('#txtOwnershipPercentage1-error small').text('');
    }

    if ($('#txtContactEmail1').val().trim() != ""
        && !isEmail($('#txtContactEmail1').val())) {
        errors.push(errors.length + 1);
        $('#txtContactEmail1').parents('.form-group').addClass('has-error');
        $('#txtContactEmail1-error small').text('Invalid email format.');
    } else {
        $('#txtContactEmail1').parents('.form-group').removeClass('has-error');
        $('#txtContactEmail1-error small').text('');
    }

    return errors.length < 1;
}

// Replicated from isValidDateEx
function isValidDate(dateString) {
    if (dateString != "") {

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

window.validReqFields = validReqFields;
window.isValidDate = isValidDate;
window.isValidMYDate = isValidMYDate;
window.isValidUrl = isValidUrl; 
window.isEmail = isEmail;