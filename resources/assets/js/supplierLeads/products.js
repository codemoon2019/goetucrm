import swal from "sweetalert2"

$(document).ready(function () {
    $('#btn-add-product').on('click', function (e) {
        e.preventDefault()
        let numberOfProducts = $('#products').children().length
        let addProductEl = $('#add-product-template').clone()
        addProductEl.find('.product_number').text(`Product ${numberOfProducts + 1}`)
        addProductEl.removeClass('hidden')
        addProductEl.removeAttr('id')

        $('#products').append(addProductEl)
    })

    $('#products').on('click', '.btn-delete-product', function () {
        let product = $(this).parent().parent().parent()

        const name = product.find('input[name="product_names[]"]').val();
        const price = product.find('input[name="product_prices[]"]').val();
        const description = product.find('input[name="product_descriptions[]"]').val();

        const condition1 = name == ''
        const condition2 = price == ''
        const condition3 = (description == undefined || description == '')

        if (condition1 && condition2 && condition3) {
            product.remove()
            updateLabels()
        } else {

            swal.fire({
                title: 'Delete this product?',
                footer: 'Deletion will only persist after saving',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    product.remove()
                    updateLabels()
                }
            })

        }
    })

    function updateLabels() {
        let productLabels = $('#products .product_number')
        productLabels.each(function (index, el) {
            $(el).text(`Product ${index + 1}`)
        })
    }
})

export function validateProducts() {
    $('#products .invalid-feedback').remove()
    $('#products .is-invalid').removeClass('is-invalid')

    let hasError = false
    let requiredFields = [
        'product_names[]',
        'product_prices[]',
        'product_descriptions[]',
    ]

    requiredFields.forEach(field => {
        let selector = `#products input[name="${field}"], ` +
                       `#products textarea[name="${field}"]`

        $(selector).each((i, el) => {
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
    
    $('#products input[name="product_prices[]"]').each((i, el) => {
        el = $(el)
        if (el.val().trim() !== '' && !$.isNumeric(el.val()) ) {
            el.addClass('is-invalid')
            el.after(`<div class="invalid-feedback">Incorrect price format</div>`)
            hasError = true
        } 
    })

    return !hasError
}

window.validateProducts = validateProducts