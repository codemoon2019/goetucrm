import swal from "sweetalert2";
$(document).ready(function () {
    $('#mobile_number').mask("999-999-9999");
    $('#direct_office_number').mask("999-999-9999");
    $('#direct_office_number_extension').mask("999");
    $('#dob').mask("99/99/9999");
    $('.company-cb').hide();
    $('.company-' + $('#company').val()).show();

    $('#company').change(function () {
        var id = $(this).val();
        $('.department-cb').prop('checked', false);
        $('input[name="advance_department_id"]').val('');
        $('.company-cb').hide();
        $('input[name="departments"]').val('');
        $('.company-' + id).show();
    });

    $('.sys-dept-cb').change(function () {
        if ($(this).prop('checked')) {
            $('.department-cb').prop('checked', false);
            $('.sys-dept-cb').prop('checked', false);
            $(this).prop('checked', true);
            $('input[name="advance_department_id"]').val($(this).val() + ',');
        }
        var department = '';
        $('.sys-dept-cb:checkbox:checked').each(function () {
            department = department + $(this).val() + ",";
        });
        $('input[name="departments"]').val(department);

    });

    $('.department-cb').change(function () {
        var department = '';
        if ($(this).prop('checked')) {
            $('.sys-dept-cb').prop('checked', false);
        }
        $('.department-cb:checkbox:checked').each(function () {
            if(!$(this).is(":hidden")){
                department = department + $(this).val() + ",";
            }
        });
        $('input[name="departments"]').val(department);
    });

    $('.adv-department-cb').change(function () {
        var department = '';
        var dept_name = $(this).data('desc');
        var dept_id = $(this).val();
        var span = document.getElementById('sort_' + dept_id);

        $('.adv-department-cb:checkbox:checked').each(function () {
            department = department + $(this).val() + ",";
        });
        $('input[name="advance_department_id"]').val(department);

        if ($(this).is(':checked')) {
            if (!span) {
                $('.sort-by').append('<span class="badge badge-dark sort-badge" id="sort_' + dept_id + '">\
                    ' + dept_name + '&nbsp;&nbsp;&nbsp;<span class="sort-close" onclick="closeEntity(' + dept_id + ')">\
                    <i class="fa fa-times" data-num="' + dept_id + '"></i>\
                    </span></span>');
            }
        } else {
            $('#sort_' + dept_id).remove();
        }
    });

    $('.department-cb').trigger('change');

    $('#company-op').change(function () {
        var id = $(this).val();
        $('.adv-department-cb').prop('checked', false);
        $('input[name="advance_company_id"]').val(id);
        $('.department-li').hide();
        $('.department-li-' + id).show();
    });

   
    $('#company-op').trigger('change');

    $('#txtCountry').change(function () {
        var country = $('option:selected', this).attr('data-code');
        var url = '/partners/getStateByCountry/' + country;
        var mobile_mask = country == 'CN' ? '99999999999' : '999-999-9999';
        
        $('#mobile_number').mask(mobile_mask);
        $('#direct_office_number').mask(mobile_mask);

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

            // for first time loading to avoid empty-ing mobile number field due to masking
            if ($('#hidden_mobile_number').val() != "") {
                $('#mobile_number').val($('#hidden_mobile_number').val());
                $('#hidden_mobile_number').val("");
            }

            if ($('#hidden_direct_office_number').val() != "") {
                $('#direct_office_number').val($('#hidden_direct_office_number').val());
                $('#hidden_direct_office_number').val("");
            }

            $('#txtState').empty(); //remove all child nodes
            var newOption = option;
            $('#txtState').append(newOption);
            $('#txtState').trigger("chosen:updated");
            jQuery("label[for='businessPhone']").html(items.country[0].country_calling_code);
            jQuery("label[for='direct_office_number_yo']").html(items.country[0].country_calling_code);
        });
    });

    $('#txtCountry').trigger('change');


    $('#frmUserStore').submit(function (e) {
        $(this).find('input[type="submit"]').prop('disabled', 'disabled')
        var errors = {};
        /* if(!validateField('first_name','First Name is required'))
        {
            return false;
        } */
        if ($('#first_name').val().trim() == "") {
            var id = "first_name";
            var msg = "First Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('first_name').style.removeProperty('border');
            $('#first_name-error small').text('');
        }

        /* if(!validateField('last_name','Last Name is required'))
        {
            return false;
        } */
        if ($('#last_name').val().trim() == "") {
            var id = "last_name";
            var msg = "Last Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('last_name').style.removeProperty('border');
            $('#last_name-error small').text('');
        }
        /* if(!validateField('mobile_number','Mobile Number is required'))
        {
            return false;
        } */
        if ($('#mobile_number').val().trim() == "" &&
            $('#email_address').val().trim() == "") {
            var id = "mobile_number";
            var msg = "User must have either email address or mobile number.";
            errors[id] = msg;
            var id = "email_address";
            var msg = "User must have either email address or mobile number.";
            errors[id] = msg;
        } else {
            document.getElementById('mobile_number').style.removeProperty('border');
            $('#mobile_number-error small').text('');
            document.getElementById('email_address').style.removeProperty('border');
            $('#email_address-error small').text('');
        }
        if ($('#email_address').val().trim() != "") {
            if (!isEmail($('#email_address').val())) {
                var id = "email_address";
                var msg = "Invalid email format.";
                errors[id] = msg;
            } else {
                document.getElementById('email_address').style.removeProperty('border');
                $('#email_address-error small').text('');
            }
        }

        /* if(!validateField('dob','Date of Birth is required'))
        {
            return false;
        } */
        if ($('#dob').val().trim() == "") {
            var id = "dob";
            var msg = "Date of Birth is required.";
            errors[id] = msg;
        } else {
            document.getElementById('dob').style.removeProperty('border');
            $('#dob-error small').text('');
        }

        // if (!isValidDateEx($('#dob').val())){
        //     alert("Please input valid date.")
        //     document.getElementById('dob').style.borderColor = "red";
        //     return false;    
        // } 
        if ($('#dob').val().trim() != "") {
            if (!isValidDateEx($('#dob').val())) {
                var id = "dob";
                var msg = "Please input valid date.";
                errors[id] = msg;
            } else {
                document.getElementById('dob').style.removeProperty('border');
                $('#dob-error small').text('');
            }
        }

        if ($('#companies').val() == null) {
            var id = "companies";
            var msg = "Company is required.";
            errors[id] = msg;
        } else {
            document.getElementById('companies').style.removeProperty('border');
            $('#companies-error small').text('');

        }

        /* if(!validateField('password','Password is required'))
        {
            return false;
        } */
        if ($('#password').val().trim() == "") {
            var id = "password";
            var msg = "Password is required.";
            errors[id] = msg;
        } else {
            document.getElementById('password').style.removeProperty('border');
            $('#password-error small').text('');
        }

        if ($('#password').val().trim().length < 6) {
            var id = "password";
            var msg = "Password requires a minimum character of 6.";
            errors[id] = msg;
        } else {
            document.getElementById('password').style.removeProperty('border');
            $('#password-error small').text('');
        }

        /* if(!validateField('password_confirmation','Confirm Password is required'))
        {
            return false;
        } */
        if ($('#password_confirmation').val().trim() == "") {
            var id = "password_confirmation";
            var msg = "Confirm Password is required.";
            errors[id] = msg;
        } else {
            document.getElementById('password_confirmation').style.removeProperty('border');
            $('#password_confirmation-error small').text('');
        }

        if ($('#password_confirmation').val().trim() != $('#password').val().trim()) {
            var id = "password_confirmation";
            var msg = "Confirm Password does not match with your password.";
            errors[id] = msg;
        } else {
            document.getElementById('password_confirmation').style.removeProperty('border');
            $('#password_confirmation-error small').text('');
        }

        /* if(!validateField('departments','Departments field is required'))
        {
            return false;
        } */
        if ($('#departments').val().trim() == "") {
            var id = "departments";
            var msg = "Departments field is required.";
            errors[id] = msg;
        } else {
            document.getElementById('departments').style.removeProperty('border');
            $('#departments-error small').text('');
        }
        // if ($('#company').val().trim() == "-1") {
        //     var id = "company";
        //     var msg = "Company is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('company').style.removeProperty('border');
        //     $('#company-error small').text('');
        // }

        if (!validateReqFields(errors)) {
            $(this).find('input[type="submit"]').removeAttr('disabled');
            $('#users-preview').modal('hide');
            return false;
        }
        return true;
    });

    $('#frmUserUpdate').submit(function (e) {
        var errors = {};
        /* if(!validateField('first_name','First Name is required'))
        {
            return false;
        } */
        if ($('#first_name').val().trim() == "") {
            var id = "first_name";
            var msg = "First Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('first_name').style.removeProperty('border');
            $('#first_name-error small').text('');
        }
        /* if(!validateField('last_name','Last Name is required'))
        {
            return false;
        } */
        if ($('#last_name').val().trim() == "") {
            var id = "last_name";
            var msg = "Last Name is required.";
            errors[id] = msg;
        } else {
            document.getElementById('last_name').style.removeProperty('border');
            $('#last_name-error small').text('');
        }
        // if(!validateField('mobile_number','Mobile Number is required'))
        // {
        //     return false;
        // } 
        if ($('#mobile_number').val().trim() == "" &&
            $('#email_address').val().trim() == "") {
            var id = "mobile_number";
            var msg = "User must have either email address or mobile number.";
            errors[id] = msg;
            var id = "email_address";
            var msg = "User must have either email address or mobile number.";
            errors[id] = msg;
        } else {
            document.getElementById('mobile_number').style.removeProperty('border');
            $('#mobile_number-error small').text('');
            document.getElementById('email_address').style.removeProperty('border');
            $('#email_address-error small').text('');
        }
        if ($('#email_address').val().trim() != "") {
            if (!isEmail($('#email_address').val())) {
                var id = "email_address";
                var msg = "Invalid email format.";
                errors[id] = msg;
            } else {
                document.getElementById('email_address').style.removeProperty('border');
                $('#email_address-error small').text('');
            }
        }
        /* if(!validateField('dob','Date of Birth is required'))
        {
            return false;
        } */
        if ($('#dob').val().trim() == "") {
            var id = "dob";
            var msg = "Date of birth is required.";
            errors[id] = msg;
        } else {
            document.getElementById('dob').style.removeProperty('border');
            $('#dob-error small').text('');
        }
        /* if (!isValidDateEx($('#dob').val())){
            alert("Please input valid date.")
            document.getElementById('dob').style.borderColor = "red";
            return false;    
        }  */
        if ($('#dob').val().trim() != "") {
            if (!isValidDateEx($('#dob').val())) {
                var id = "dob";
                var msg = "Please input valid date.";
                errors[id] = msg;
            } else {
                document.getElementById('dob').style.removeProperty('border');
                $('#dob-error small').text('');
            }
        }


        if ($('#companies').val() == null) {

            var id = "companies";
            var msg = "Company is required.";
            errors[id] = msg;
        } else {
            document.getElementById('companies').style.removeProperty('border');
            $('#companies-error small').text('');

        }
        
        // if ($('#company').val().trim() == "-1") {
        //     var id = "company";
        //     var msg = "Company is required.";
        //     errors[id] = msg;
        // } else {
        //     document.getElementById('company').style.removeProperty('border');
        //     $('#company-error small').text('');
        // }
        if ($('#departments').val().trim() == "") {
            // var id = "departments";
            // var msg = "Departments field is required.";
            // errors[id] = msg;  //JR-2019-06-20 not applicable for this field is hidden
            showWarningMessage("Please select at least 1 department.");
            return false;
        } else {
            document.getElementById('departments').style.removeProperty('border');
            $('#departments-error small').text('');
        }

        if (!validateReqFields(errors)) {
            return false;
        }
        return true;
    });

    $('.dept-btn').on('click', function () {
        $('.user-dept').toggle();
    });

    loadUser();

});

function disableForm() {
    $('#divShow').find(':input').prop('disabled', true);
}

function advanceSearchUsers() {
    if ($('#advance_department_id').val() == "") {
        $('#advance_department_id').val(-1);
    }

    $('.user-dept').toggle(false);

    $("#users-table").dataTable().fnDestroy();
    $('#users-table').dataTable({
         "lengthMenu": [25, 50, 75, 100 ],
        serverSide: true,
        processing: true,
        ajax: '/admin/users/' + $('input[name=advance_department_id]').val() + '/' + $('input[name=advance_company_id]').val() + '/1/advance_data_search',
        columns: [{
                data: 'id'
            },
            {
                data: 'username'
            },
            {
                data: 'first_name'
            },
            {
                data: 'last_name'
            },
            {
                data: 'company'
            },
            {
                data: 'departments'
            },
            {
                data: 'email'
            },
            {
                data: 'country'
            },
            {
                data: 'status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });
    $('.adv-close').click();
}

function advanceSearchSystemUsers() {
    if ($('#advance_department_id').val() == "") {
        $('#advance_department_id').val(-1);
    }

    $("#system-users-table").dataTable().fnDestroy();
    $('#system-users-table').dataTable({
         "lengthMenu": [25, 50, 75, 100 ],
        serverSide: true,
        processing: true,
        ajax: '/admin/users/' + $('input[name=advance_department_id]').val() + '/-1/-1/advance_data_search',
        columns: [{
                data: 'id'
            },
            {
                data: 'username'
            },
            {
                data: 'first_name'
            },
            {
                data: 'last_name'
            },
            {
                data: 'company'
            },
            {
                data: 'departments'
            },
            {
                data: 'email'
            },
            {
                data: 'country'
            },
            {
                data: 'status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });
    $('.adv-close').click();
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

function validateField(element, msg) {
    if (element == 'mobile_number') {
        if ($('#' + element).val().trim().replace(/[-_]/g, '') == "") {
            document.getElementById(element).style.borderColor = "red";
            alert(msg);
            return false;
        }
    }
    if (element == 'dob') {
        if ($('#' + element).val().trim().replace(/[\/_]/g, '') == "") {
            document.getElementById(element).style.borderColor = "red";
            alert(msg);
            return false;
        }
    }
    if ($('#' + element).val().trim() == "") {
        document.getElementById(element).style.borderColor = "red";
        alert(msg);
        return false;
    } else {
        document.getElementById(element).style.removeProperty('border');
        return true;
    }
}

function readURL(input, id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#' + id).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
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
            $('#' + key + '-error small').text(value); //$('#' + key).after('<span id="' + key + '-error" style="color:red"><small>' + value + '</small></span>');
            // }
        }
        return false;
    }
}

function isEmail(email) {
    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(email);
}

function closeEntity(id) {
    var depts = $('input[name="advance_department_id"]').val();
    var department = depts.replace(id + ',', '');

    $('#sort_' + id).remove();
    $('input[value="' + id + '"]').prop('checked', false);
    $('input[name="advance_department_id"]').val(department);
}

function continueToPreview() {
    $(this).find('input[type="submit"]').prop('disabled', 'disabled')
    var errors = {};

    if ($('#first_name').val().trim() == "") {
        var id = "first_name";
        var msg = "First Name is required.";
        errors[id] = msg;
    } else {
        document.getElementById('first_name').style.removeProperty('border');
        $('#first_name-error small').text('');
    }

    if ($('#last_name').val().trim() == "") {
        var id = "last_name";
        var msg = "Last Name is required.";
        errors[id] = msg;
    } else {
        document.getElementById('last_name').style.removeProperty('border');
        $('#last_name-error small').text('');
    }
 
    if ($('#mobile_number').val().trim() == "" &&
        $('#email_address').val().trim() == "") {
        var id = "mobile_number";
        var msg = "User must have either email address or mobile number.";
        errors[id] = msg;
        var id = "email_address";
        var msg = "User must have either email address or mobile number.";
        errors[id] = msg;
    } else {
        document.getElementById('mobile_number').style.removeProperty('border');
        $('#mobile_number-error small').text('');
        document.getElementById('email_address').style.removeProperty('border');
        $('#email_address-error small').text('');
    }
    if ($('#email_address').val().trim() != "") {
        if (!isEmail($('#email_address').val())) {
            var id = "email_address";
            var msg = "Invalid email format.";
            errors[id] = msg;
        } else {
            document.getElementById('email_address').style.removeProperty('border');
            $('#email_address-error small').text('');
        }
    }

    if ($('#dob').val().trim() == "") {
        var id = "dob";
        var msg = "Date of Birth is required.";
        errors[id] = msg;
    } else {
        document.getElementById('dob').style.removeProperty('border');
        $('#dob-error small').text('');
    }

    if ($('#dob').val().trim() != "") {
        if (!isValidDateEx($('#dob').val())) {
            var id = "dob";
            var msg = "Please input valid date.";
            errors[id] = msg;
        } else {
            document.getElementById('dob').style.removeProperty('border');
            $('#dob-error small').text('');
        }
    }

    if ($('#companies').val() == null) {
        var id = "companies";
        var msg = "Company is required.";
        errors[id] = msg;
    } else {
        document.getElementById('companies').style.removeProperty('border');
        $('#companies-error small').text('');

    }

    if ($('#password').val().trim() == "") {
        var id = "password";
        var msg = "Password is required.";
        errors[id] = msg;
    } else {
        document.getElementById('password').style.removeProperty('border');
        $('#password-error small').text('');
    }

    if ($('#password').val().trim().length < 6) {
        var id = "password";
        var msg = "Password requires a minimum character of 6.";
        errors[id] = msg;
    } else {
        document.getElementById('password').style.removeProperty('border');
        $('#password-error small').text('');
    }

    if ($('#password_confirmation').val().trim() == "") {
        var id = "password_confirmation";
        var msg = "Confirm Password is required.";
        errors[id] = msg;
    } else {
        document.getElementById('password_confirmation').style.removeProperty('border');
        $('#password_confirmation-error small').text('');
    }

    if ($('#password_confirmation').val().trim() != $('#password').val().trim()) {
        var id = "password_confirmation";
        var msg = "Confirm Password does not match with your password.";
        errors[id] = msg;
    } else {
        document.getElementById('password_confirmation').style.removeProperty('border');
        $('#password_confirmation-error small').text('');
    }

    if ($('#departments').val().trim() == "") {
        // var id = "departments";
        // var msg = "Departments field is required.";
        // errors[id] = msg; // not applicable for departments is a hidden
        showWarningMessage("Please select at least 1 department");
        return false;
    } else {
        document.getElementById('departments').style.removeProperty('border');
        $('#departments-error small').text('');
    }

    if (!validateReqFields(errors)) {
        $(this).find('input[type="submit"]').removeAttr('disabled');
        $('#users-preview').modal('hide');
        return false;
    } else {
        usersPreview();
    }
}

function usersPreview() {
    $('#users-preview').modal('show');

    $.each($('#frmUserStore').serializeArray(), function(i, field) {
        if (!$('input[id="'+field.name+'"]').hasClass('department-cb')
            && !$('input[id="'+field.name+'"]').hasClass('sys-dept-cb')) {
            if (field.name == "companies[]") {
            }
            else if (field.name == "password") {
                $('#' + field.name + '_preview').text('');
                $('#' + field.name + '_preview').val(field.value);
            } else {
                $('#' + field.name + '_preview').text(field.value);
    
                if (field.name == 'status') {
                    var status = field.value == 'A' ? 'Active' : 'Inactive';
                    $('#' + field.name + '_preview').text(status);
                }
            }
        }
    });

    $('#companies_preview').text($('#companies option:selected').text());
    
    var companies = '';
    $('#companies_preview').empty();
    $('#companies > option:selected').each(function() {
        companies = companies + "<li>" + $(this).text() + "</li>";
    });
    $('#companies_preview').append(companies);

    var department = '';
    $('.sys-dept-list').empty();
    $('.dept-list').empty();
    $('.sys-dept-form').addClass('hide');
    $('.dept-form').addClass('hide');

    if ($('.sys-dept-cb:checkbox:checked').length > 0) {
        $('.sys-dept-cb:checkbox:checked').each(function () {
            department = department + "<li>" + $(this).attr('name') + "</li>";
        });
        $('.sys-dept-form').removeClass('hide');
        $('.sys-dept-list').append(department);
    } else if ($('.department-cb:checkbox:checked').length > 0) {
        $('.department-cb:checkbox:checked').each(function () {
            department = department + "<li>" +  $(this).attr('name') + "</li>";
        });
        $('.dept-form').removeClass('hide');
        $('.dept-list').append(department);
    }

}

function togglePassword() {
    $('.toggle-password').toggleClass("fa-eye-slash");
    var x = document.getElementById("password_preview");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}


function validateData(table, field, value, id, includeStatus, prefix, message, tab) {
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

function activate(obj) {
    var delay = 3000; //3 second
    var id = $(obj).attr('data-uid');
    var url = $(obj).attr('data-url');
    var status = $(obj).attr('data-stat');
    var message = status == 'A' ? 'Deactivating ' : 'Activating ';

    showLoadingAlert(message + ' user...');
    setTimeout(function () {
        window.location = '/admin/users/' + id + '/' + status + '/' + url + '/activate';
    }, delay);
    return false;
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



function loadUser(){
  $('#users-table').dataTable().fnDestroy();
    $('#users-table').dataTable({
         "lengthMenu": [25, 50, 75, 100 ],
        serverSide: true,
        processing: true,
        ajax: '/admin/users/data',
        columns: [{
                data: 'id'
            },
            {
                data: 'username'
            },
            {
                data: 'first_name'
            },
            {
                data: 'last_name'
            },
            {
                data: 'company'
            },
            {
                data: 'departments'
            },
            {
                data: 'email'
            },
            {
                data: 'country'
            },
            {
                data: 'status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

    $('#system-users-table').dataTable().fnDestroy();
    $('#system-users-table').dataTable({
         "lengthMenu": [25, 50, 75, 100 ],
        serverSide: true,
        processing: true,
        ajax: '/admin/users/system-data',
        columns: [{
                data: 'id'
            },
            {
                data: 'username'
            },
            {
                data: 'first_name'
            },
            {
                data: 'last_name'
            },
            {
                data: 'company'
            },
            {
                data: 'departments'
            },
            {
                data: 'email'
            },
            {
                data: 'country'
            },
            {
                data: 'status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

}

function deleteUser(id){
    if (confirm('Delete this User?')) {
        var formData = {
            id: id
        };

        $.ajax({
            type:'GET',
            url:'/admin/UserDelete',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    alert(data.msg);
                    loadUser();
                }else {
                    alert(data.msg);
                }
            }
        });
    }else {
        return false;
    }
}


function resetPassword(id){
    if (confirm('Reset Password?')) {
        var formData = {
            id: id
        };

        $.ajax({
            type:'GET',
            url:'/admin/UserResetPassword',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    alert(data.msg);
                }else {
                    alert(data.msg);
                }
            }
        });
    }else {
        return false;
    }
}

function setAsOffline(id){
    if (confirm('Set as Offline?')) {
        var formData = {
            id: id
        };

        $.ajax({
            type:'GET',
            url:'/admin/UserSetOffline',
            data:formData,
            dataType:'json',
            success:function(data){
                if (data.success) {
                    alert(data.msg);
                    loadUser();
                    chatOffline();
                }else {
                    alert(data.msg);
                }
            }
        });
    }else {
        return false;
    }
}

window.disableForm = disableForm;
window.advanceSearchUsers = advanceSearchUsers;
window.readURL = readURL;
window.advanceSearchSystemUsers = advanceSearchSystemUsers;
window.closeEntity = closeEntity;
window.continueToPreview = continueToPreview;
window.usersPreview = usersPreview;
window.togglePassword = togglePassword;
window.validateData = validateData;
window.activate = activate;
window.loadUser = loadUser;
window.deleteUser = deleteUser;
window.resetPassword = resetPassword;
window.setAsOffline = setAsOffline;