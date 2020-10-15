import swal from "sweetalert2";

export function addSubtaskEl(subtask=null) {
    let subtasksContainer = $('.container-subtasks').first()
    let subtasksCount = $('.subtask').length

    let subtaskTemplateEl = $('#subtask-template')
    let subtaskEl = subtaskTemplateEl.clone()

    let deleteButtonEl = subtaskEl.find('.btn-delete-subtask')
    deleteButtonEl.tooltip()

    let subproductsInputEl = subtaskEl.find('select[name="subproducts[]"]')
    subproductsInputEl
        .attr({name: `subproducts[${subtasksCount}][]`})
        .select2()

    let subtaskPrereqCheckboxEl = subtaskEl.find('input[name="has_prerequisite[]"]')
    subtaskPrereqCheckboxEl.attr({name: `has_prerequisite[${subtasksCount}]`})

    let subtaskPrereqInputEl = subtaskEl.find('select[name="prereq_subtask_number[]"]')
    for (let i = 1; i <= subtasksCount; i++) {
        subtaskPrereqInputEl.append(new Option(i, i))
    }

    if (subtask !== null) {
        subtaskEl.find('select[name="priority[]"]').val(subtask.ticket_priority_code)
        subtaskEl.find('input[name="subtask_name[]"]').val(subtask.name)
        subtaskEl.find('input[name="days_to_complete[]"]').val(subtask.days_to_complete)
        subtaskEl.find('select[name="department[]"]').val(subtask.department_id)
        subtaskEl.find(`select[name="subproducts[${subtasksCount}][]"]`)
            .val(subtask.product_tags)
            .trigger('change')
    }

    subtaskEl.removeAttr('id')
    subtaskEl.removeClass('hidden')
    subtaskEl.removeClass('subtask-template')
    subtaskEl.addClass('d-flex')
    subtaskEl.addClass('subtask')

    subtasksContainer.append(subtaskEl)

    if (subtask !== null && subtask.assignee !== null) {
        subtaskEl.find('select[name="department[]"]').data('assignee', subtask.assignee)
    }

    subtaskEl.find('select[name="department[]"]').trigger('change')

    if (subtasksCount == 0) {
        subtaskEl.find('.form-check').remove()
        subtaskEl.find('.btn-delete-subtask').remove()
        subtaskEl.find('.has-prerequisite').remove()
    }

    if (subtask !== null) {
        if (subtask.prerequisite != null) {
            subtaskEl.find('input[name="has_prerequisite[]"]').attr({
                'name': `has_prerequisite[${subtasksCount}]`
            })

            subtaskEl.find(`input[name="has_prerequisite[${subtasksCount}]"]`)
                .prop('checked', true)
                .trigger('change')

            subtaskEl.find('select[name="start_this_subtask_on[]"]').val(subtask.link_condition)
            subtaskEl.find('select[name="prereq_subtask_number[]"]').val(subtask.prerequisite).trigger('change')
        }
    }

    updateSubtasksNumber()
}

export function deleteSubtaskEl(subtaskEl) {
    swal({
        title: 'Delete this subtask?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Cancel',
        cancelButtonText: 'Delete',
    }).then((result) => {
        if (!result.value) {
            let subtaskNumber = subtaskEl.find('h1').find('strong').text()
            
            subtaskEl.find('.btn-delete-subtask').tooltip('dispose')
            subtaskEl.slideUp('fast', function() {
                subtaskEl.remove()

                updateSubtasksOnDelete(parseInt(subtaskNumber))
                updateSubtasksNumber()
            })
        }
    })
}

export function updateSubtasksOnDelete(deletedSubtaskNumber) {
    let subtaskElements = $('.subtask')
    subtaskElements.each(function (i) {
        let subtaskEl = $(this)
        let j = i + 1

        if (deletedSubtaskNumber <= j) {
            let hasPrerequisite = subtaskEl.find(`input[name="has_prerequisite[${j}]"]`)
                .first()
                .is(':checked')

            if (hasPrerequisite) {
                let subtaskPrereqInputEl = subtaskEl.find('select[name="prereq_subtask_number[]"]')
                let linkedSubtaskNumber = subtaskPrereqInputEl.val()

                subtaskEl.find('select[name="prereq_subtask_number[]"]')
                    .children('option')
                    .last()
                    .remove()

                if (linkedSubtaskNumber == deletedSubtaskNumber) {
                    subtaskEl
                        .find(`input[name="has_prerequisite[${j}]"]`)
                        .prop('checked', false)
                        .trigger('change')

                    subtaskPrereqInputEl.val(1)
                    subtaskPrereqInputEl.trigger('change')
                } else if (linkedSubtaskNumber > deletedSubtaskNumber) {
                    subtaskPrereqInputEl.val(linkedSubtaskNumber - 1)
                    subtaskPrereqInputEl.trigger('change')
                }

                subtaskEl.find(`input[name="has_prerequisite[${j}]"]`)
                    .attr({
                        name: `has_prerequisite[${j-1}]`
                    })

                subtaskEl.find(`select[name="subproducts[${j}][]"]`)
                    .attr({
                        name: `subproducts[${j-1}][]`
                    })
                    .select2()
            }
        }
    })
}

export function updateSubtasksNumber() {
    $('.subtask').each(function(i) {
        $(this).find('h1').find('strong').text(i + 1)
    })
}

export function updateSubtaskTemplateEl(priorities, subproducts, departments) {
    let subtaskTemplateEl = $('#subtask-template')
    let departmentsGroups = departments

    let priorityInputEl = subtaskTemplateEl.find('select[name="priority[]"]')
    priorityInputEl.empty()
    priorityInputEl.append('<option selected disabled value="">Select Priority</option>')
    priorities.forEach(priority => {
        priorityInputEl.append(new Option(priority.description, priority.code))
    })

    let subproductsInputEl = subtaskTemplateEl.find('select[name="subproducts[]"]')
    subproductsInputEl.empty()
    subproducts.forEach(subProduct => {
        subproductsInputEl.append(new Option(subProduct.name, subProduct.id))
    })
    
    let departmentInputEl = subtaskTemplateEl.find('select[name="department[]"]')
    departmentInputEl.empty()
    departmentInputEl.append('<option selected disabled value="">Select Department</option>')
    for (let key in departmentsGroups) {
        if (departmentsGroups.hasOwnProperty(key)) {
            const optgroup = $(
                `<optgroup label="${departmentsGroups[key][0].partner_company.company_name}">` +
                `</optgroup>`)

            departmentsGroups[key].forEach(department => {
                optgroup.append(
                    `<option value="${department.id}">` + 
                        `${department.description}` +
                    `</option>`)
            })

            departmentInputEl.append(optgroup)
        }
    }
}

window.addSubtaskEl = addSubtaskEl
window.deleteSubtaskEl = deleteSubtaskEl
window.updateSubtasksNumber = updateSubtasksNumber
window.updateSubtaskTemplateEl = updateSubtaskTemplateEl