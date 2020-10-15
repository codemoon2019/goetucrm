$(document).ready(function() {
    $('input[name="mcc"]').on('keyup', function () {
        let el = $('.business_industry-error')
        el.addClass('hidden')

        if ($(this).val().length < 3) {
            return true;
        }

        let option = $(`select[name="business_industry"] option[value="${$(this).val()}"]`).first()
        if (option.length == 1) {
            $('select[name="business_industry"]').val($(this).val()).trigger('change')
        } else {
            el.text(`${$(this).val()} is not a valid Merchant Category Code`)
            el.removeClass('hidden')
        }
    })

    $('select[name="business_industry"]').on('change', function () {
        $('input[name="mcc"]').val($(this).val())

        let el = $('.business_industry-error')
        el.addClass('hidden')
    })

    $('select[name="business_industry"]').trigger('change')
})

export function validateMcc() {
    let mccEl = $('input[name="mcc"]')

    let el = $('.business_industry-error')
    el.addClass('hidden')

    let option = $(`select[name="business_industry"] option[value="${mccEl.val()}"]`).first()
    if (option.length == 1) {
        return true;
    }
    
    el.text(`${mccEl.val()} is not a valid Merchant Category Code`)
    el.removeClass('hidden')

    return false
}