export function validateWorkflowTemplateForm() {
    $('.invalid-feedback').remove()
    $('.is-invalid').removeClass('is-invalid')

    let hasError = false
    let requiredFields = [
        'priority[]',
        'subtask_name[]',
        'days_to_complete[]',
        'department[]',
    ]

    
    requiredFields.forEach(field => {
        $(`.container-subtasks input[name="${field}"], 
           .container-subtasks select[name="${field}"]`).each((i, el) => {
            el = $(el)
            if (el.val() == null) {
                el.addClass('is-invalid')
                el.after(`<div class="invalid-feedback">This field is required</div>`)
                hasError = true
            } else if (el.val().trim() == '') {
                el.addClass('is-invalid')
                el.after(`<div class="invalid-feedback">This field is required</div>`)
                hasError = true
            }
        })
    })

    let el = $('input[name="task_name"]')
    if (el.val() == null) {
        el.addClass('is-invalid')
        el.after(`<div class="invalid-feedback">This field is required</div>`)
        hasError = true
    } else if (el.val().trim() == '') {
        el.addClass('is-invalid')
        el.after(`<div class="invalid-feedback">This field is required</div>`)
        hasError = true
    }

    let el2 = $('textarea[name="task_description"]')
    if (el2.val() == null) {
        el2.addClass('is-invalid')
        el2.after(`<div class="invalid-feedback">This field is required</div>`)
        hasError = true
    } else if (el2.val().trim() == '') {
        el2.addClass('is-invalid')
        el2.after(`<div class="invalid-feedback">This field is required</div>`)
        hasError = true
    }

    $('.container-subtasks .subproducts-input-el').each((i, el) => {
        el = $(el)
        if (el.val() == null) {
            el.addClass('is-invalid')
            el.after(`<div class="invalid-feedback">This field is required</div>`)
            hasError = true
        }
    })

    return !hasError
}

window.validateWorkflowTemplateForm = validateWorkflowTemplateForm