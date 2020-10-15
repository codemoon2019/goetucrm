
import swal from "sweetalert2";

$(document).ready(function () {
    $('input[name="contact_phones[]"]').mask('999-999-9999', { clearIfNotMatch: true })
    $('input[name="contact_phones_2[]"]').mask('999-999-9999', { clearIfNotMatch: true })
    $('input[name="contact_mobiles[]"]').mask('999-999-9999', { clearIfNotMatch: true })
    $('input[name="contact_faxs[]"]').mask('999-999-9999', { clearIfNotMatch: true })

    $('#btn-add-contact').on('click', function (e) {
        e.preventDefault()
        let numberOfContacts = $('#contacts').children().length
        let addContactEl = $('#add-contact-template').clone()
        addContactEl.find('.contact_number').text(`Contact ${numberOfContacts + 1}`)
        addContactEl.removeClass('hidden')
        addContactEl.removeAttr('id')

        $('#contacts').append(addContactEl)
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
    })

    $('#contacts').on('click', '.btn-delete-contact', function () {
        let contact = $(this).parent().parent().parent()

        const first_name = contact.find('input[name="contact_first_names[]"]').val();
        const middle_name = contact.find('input[name="contact_middle_names[]"]').val();
        const last_name = contact.find('input[name="contact_last_names[]"]').val();
        const position = contact.find('input[name="contact_positions[]"]').val();
        const contact_phone = contact.find('input[name="contact_phones[]"]').val();
        const contact_phone_2 = contact.find('input[name="contact_phones_2[]"]').val();
        const contact_fax = contact.find('input[name="contact_faxs[]"]').val();
        const mobile = contact.find('input[name="contact_mobiles[]"]').val();

        const condition1 = first_name == ''
        const condition2 = middle_name == ''
        const condition3 = last_name == ''
        const condition4 = position == ''
        const condition5 = contact_phone == ''
        const condition6 = contact_phone_2 == ''
        const condition7 = contact_fax == ''
        const condition8 = mobile == ''

        if (condition1 && condition2 && condition3 && condition4 &&
            condition5 && condition6 && condition7 && condition8) {
            contact.remove()
            updateLabels()
        } else {

            swal.fire({
                title: 'Delete this contact?',
                footer: 'Deletion will only persist after saving',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    contact.remove()
                    updateLabels()
                }
            })

        }
    })

    function updateLabels() {
        let contactLabels = $('#contacts .contact_number')
        contactLabels.each(function (index, el) {
            $(el).text(`Contact ${index + 1}`)
        })
    }
})

export function validateContacts() {
    $('#contacts .invalid-feedback').remove()
    $('#contacts .is-invalid').removeClass('is-invalid')

    let hasError = false
    let requiredFields = [
        'contact_first_names[]',
        'contact_last_names[]',
        'contact_positions[]',
    ]

    requiredFields.forEach(field => {
        $(`#contacts input[name="${field}"]`).each((i, el) => {
            el = $(el)
            if (el.val().trim() == '') {
                let errorMsg = _.startCase(field.substr(0, field.length - 3)) + ' is required'

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

    return !hasError
}

window.validateContacts = validateContacts