import axios from "axios";
import swal from "sweetalert2";
import { validateProducts } from "./products";
import { validateContacts } from "./contacts";
import { matcher } from "../customSelect2"
import { templateResult } from "../customSelect2"
import { templateSelection } from "../customSelect2"

$(document).ready(function () {
    $('input[name="business_phone"]').mask('999-999-9999', {clearIfNotMatch: true})
    $('input[name="business_phone_2"]').mask('999-999-9999', { clearIfNotMatch: true })
    $('input[name="mcc"]').mask('999', { clearIfNotMatch: true })
    $('input[name="extension"]').mask('999', { clearIfNotMatch: true })
    $('input[name="extension_2"]').mask('999', { clearIfNotMatch: true })
    $('input[name="fax"]').mask('999-999-9999', { clearIfNotMatch: true })
    // $('input[name="zip"]').mask('00000', { clearIfNotMatch: true })

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

    $('.list-tab').click(function (e) {
        var curActive = $('.nav li a').parents('.nav');
        var curActiveId = curActive.find('li.active a').attr('id');
        $('.' + curActiveId).removeClass("active");
        $(this).addClass("active");
    })

    $('.tabs-rectangular li a').click(function () {
        var curActive = $(this).parents('.tabs-rectangular');

        // Hide Active View
        var curActiveId = curActive.find('li.active a').attr('id');
        $('#' + curActiveId + 'Container').addClass('hide');

        // Change Active View
        var id = $(this).attr('id');
        $('#' + id + 'Container').removeClass('hide');

        // Change Active Tab
        curActive.find('li.active').removeClass('active');
        $(this).parent().addClass('active');
    });

    $('#assignTo').change(function () {
        var partner_type = document.getElementById("assignTo");
        if (partner_type.selectedIndex >= 0) {
            var partner_type_selectedValue = partner_type.options[partner_type.selectedIndex].value;
            var data = '&partner_type_id=' + partner_type_selectedValue;
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
                }
            });
        } else {
            $('#assignee').empty(); //remove all child nodes   
        }
    });

    $('#assignTo').trigger('change');

    $('#assignToMe').change(function () {
        $('.assignToDiv').toggle();
        if (document.getElementById('assignToMe').checked) {
            $("#assignee").prop("disabled", true);
            $("#assignTo").prop("disabled", true);
        } else {
            $("#assignee").prop("disabled", false);
            $("#assignTo").prop("disabled", false);
        }
    });

    $('#assignToMe').trigger('change');

    function moveToNextTab() {
        $('.nav-tabs > .active').next('li').find('a').trigger('click');

        if ($('.progressbar > .active').length == 0) {
            $('.progressbar > li').first().addClass('active')
        } else {
            $('.progressbar > .active').next('li').addClass('active')
        }
    }

    function moveToPrevTab() {
        $('.nav-tabs > .active').prev('li').find('a').trigger('click');
        $('.progressbar > .active').last().removeClass('active')
    }

    function validateBusinessInformation() {
        $('#business-info .invalid-feedback').remove()
        $('#business-info .is-invalid').removeClass('is-invalid')
        $('#business-info .business_industry-error').addClass('is-invalid')

        let hasError = false
        let requiredFields = [
            'doing_business_as',
            'business_address',
            'business_industry',
            'country',
            'state',
            'city',
            'zip',
            'business_phone',
            'mcc'
        ]

        requiredFields.forEach(field => {
            $(`#business-info input[name="${field}"]`).each((i, el) => {
                el = $(el)
                if (el.val().trim() == '') {
                    let errorMsg = _.startCase(field.substr(0, field.length)) + ' is required'

                    if (el.parent().hasClass('input-group')) {
                        el.addClass('is-invalid')
                        el.closest('.form-group')
                            .append(`<div class="invalid-feedback" style="display:block">${errorMsg}</div>`)
                    } else {
                        el.addClass('is-invalid')
                        el.after(`<div class="invalid-feedback">${errorMsg}</div>`)
                    }

                    hasError = true
                }
            })
        })

        if (mccHasError) {
            let mccValue = $('input[name="mcc"]').val();
            let el = $('.business-industry-error')
            el.removeClass('hidden')
            el.text(`${mccValue} is not a valid Merchant Category Code`)
            hasError = true
        }

        if (zipHasError) {
            let el = $('input[name="zip"]')
            el.addClass('is-invalid')
            el.after(`<div class="invalid-feedback">${el.val()} is not a valid US zip code</div>`)
            hasError = true
        }

        return !hasError
    }
    
    function validateContactsAdvanced() {
        const requiredValidationResult = validateContacts()
        const businessEmail = $('input[name="business_email"]').val().trim()
        const mainContactMobile = $('input[name="contact_mobiles[]"]').first().val().trim()

        let emailMobileValidationResult = true
        if (businessEmail == '' && mainContactMobile == '') {
            emailMobileValidationResult = false

            let errorMsg = "Main contact's mobile is required when no email given in business information"
            let el = $('input[name="contact_mobiles[]"]')
            el.addClass('is-invalid')
            el.closest('.form-group')
                .append(`<div class="invalid-feedback" style="display:block">${errorMsg}</div>`)
        }

        if (requiredValidationResult && emailMobileValidationResult)
            return true

        return false
    }

    $('.btn-next-1').click(function () {
        if (validateBusinessInformation())
            moveToNextTab()
    });

    $('.btn-next-2').click(function () {
        if (validateContactsAdvanced())
            moveToNextTab()
    });
    
    $('.btn-submit').click(function (e) {
        let biValidationResult = validateBusinessInformation()
        let cValidationResult = validateContacts()
        let pValidationResult = validateProducts()
        
        if (biValidationResult && cValidationResult && pValidationResult)
            return true

        e.preventDefault()
        if (!biValidationResult) {
            moveToPrevTab()
            moveToPrevTab()
        } else if (!cValidationResult) {
            moveToPrevTab()
        }
    });

    $('.btn-previous').click(function () {
        moveToPrevTab()
    });

    let zipHasError = false
    $('input[name="zip"]').on('change', function() {
        let country = $('select[name="country"]').val()

        if (country == '1') {
            if ($(this).val().length != 5)
                return true;
        } else if (country == '2') {
            if ($(this).val().length != 4)
                return true;
        } else if (country == '3') {
            if ($(this).val().length != 6)
                return true;
        }

        // $('select[name="state"]').prop('disabled', false)
        // $('input[name="city"]').prop('disabled', false)

        let el = $(this)
        axios.get(`/extras/getCityAndState/` + $(this).val())
            .then(data => {
                /* $('input[name="city"]').val(dataObj.city) */
                let dataObj = data.data
                if (dataObj.success) {
                    let formGroup = el.closest('.form-group')
                    $(formGroup).children('.invalid-feedback').remove()
                    $(formGroup).children('.is-invalid').removeClass('is-invalid')

                    let option = "";
                    $.each(dataObj.cities, function (key, item) {
                        option += '<option value="' + item.city + '">' + item.city + '</option> ';
                    });
                    $('select[name="city"]').empty(); //remove all child nodes
                    $('select[name="city"]').append(option);
                    
                    let stateId = $(`option[data-abbr="${dataObj.state}"]`).val()
                    $('select[name="state"]').val(stateId).trigger('change');
        
                    zipHasError = false
                } else {
                  let formGroup = el.closest('.form-group')
                  $(formGroup).children('.invalid-feedback').remove()
                  $(formGroup).children('.is-invalid').removeClass('is-invalid')

                  el.addClass('is-invalid')
                  el.after(`<div class="invalid-feedback">${el.val()} is not a valid zip code</div>`)
                  zipHasError = true
                }
            })
            .catch(() => {
                let formGroup = el.closest('.form-group')
                $(formGroup).children('.invalid-feedback').remove()
                $(formGroup).children('.is-invalid').removeClass('is-invalid')

                el.addClass('is-invalid')
                el.after(`<div class="invalid-feedback">${el.val()} is not a valid zip code</div>`)
                zipHasError = true
            })
            .finally(() => {
                $('select[name="state"]').prop('disabled', false)
                $('select[name="city"]').prop('disabled', false)
            })
    })

    let mccHasError = false
    $('input[name="mcc"]').on('change', function () {
        let el = $('.business_industry-error')
        el.addClass('hidden')

        if ($(this).val().length < 3) {
            mccHasError = false
            return true;
        }
            
        let option = $(`select[name="business_industry"] option[value="${$(this).val()}"]`).first()
        if (option.length == 1) {
            $('select[name="business_industry"]').val($(this).val()).trigger('change')
        } else {
            el.text(`${$(this).val()} is not a valid Merchant Category Code`)
            el.removeClass('hidden')

            mccHasError = true
        }
    })

    $('select[name="business_industry"]').on('change', function() {
        $('input[name="mcc"]').val($(this).val())

        let el = $('.business_industry-error')
        el.addClass('hidden')

        mccHasError = false
    })

    $('select[name="business_industry"]').trigger('change')

    $('select[name="country"]').on('change', function(){
        let country = $('select[name="country"]').val();
        let masks = ['999999', '99999', '9999'];

        if (country == '1') {
            $('input[name="zip"]').mask(masks[1], { clearIfNotMatch: true })
        } else if (country == '2') {
            $('input[name="zip"]').mask(masks[2], { clearIfNotMatch: true })
        } else if (country == '3') {
            $('input[name="zip"]').mask(masks[0], { clearIfNotMatch: true })
        }
    });

    $('select[name="country"]').trigger('change')

});

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
    $state.find("strong").text(state.element.dataset.abbr);

    return $state;
};

// function isValidZip(el, city_id, state_id) {
//     $('#' + city_id).prop('disabled', true);
//     $('#' + state_id).prop('disabled', true);
//     if (el.value.length == 5) {
//         $.ajax({
//             url: "/merchants/getCityState/" + el.value,
//             type: "GET",
//         }).done(function(data) {
//             $('#' + city_id).val(data.city);
//             $('#' + state_id).val(data.abbr).trigger('change');
//             $('#' + el.id + '-error small').text('');
//             document.getElementById(el.id).style.removeProperty('border');
//             $('#' + city_id).prop('disabled', false);
//             $('#' + state_id).prop('disabled', false);

//         }).fail(function(data) {
//             document.getElementById(el.id).style.borderColor = "red";
//             $('#' + el.id + '-error small').text('Error, not a US zip code.'); 
//             $('#' + el.id).val('');
//             $('#' + city_id).prop('disabled', false);
//             $('#' + state_id).prop('disabled', false);
//         });
//     }
// }

//window.isValidZip = isValidZip;