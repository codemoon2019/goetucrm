export function updateOverview() {
    updateSubtasksCount()
    updateDaysToComplete()
    updateDepartmentsInvolved()
    updateUsersInvolved()
}

function updateSubtasksCount() {
    let subtasks = $('.subtask')
    let subtasksCount = subtasks.length

    if (subtasksCount == 1) {
        $('.overview-subtasks-count').text(`${subtasksCount} Subtask`)
    } else {
        $('.overview-subtasks-count').text(`${subtasksCount} Subtasks`)
    }
}

function updateDaysToComplete() {
    let subtasks = $('.subtask')
    let daysToComplete = 0;

    subtasks.each(function (i) {
        daysToComplete += parseInt($(this).find('input[name="days_to_complete[]"]')
            .first()
            .val())
    })

    if (daysToComplete == 1) {
        $('.overview-days-to-complete').text(`${daysToComplete} Day to Complete`)
    } else {
        $('.overview-days-to-complete').text(`${daysToComplete} Days to Complete`)
    }
}

function updateDepartmentsInvolved() {
    let subtaskElements = $('.subtask')
    let departmentsInvolvedEl = $('.overview-departments-involved')

    let departmentsInvolved = []
    subtaskElements.each(function (i) {
        let department = $(this).find('select[name="department[]"]')
            .first()
            .find(':selected')
            .text()

        if (department !== 'Select Department')
            departmentsInvolved.push(department)
    })

    departmentsInvolvedEl.empty()
    departmentsInvolved = [...new Set(departmentsInvolved)];
    if (departmentsInvolved.length == 0) {
        departmentsInvolvedEl.append('<span>N/A</span>')
    } else {
        departmentsInvolved.forEach(department => {
            departmentsInvolvedEl.append(`<span>- ${department}</span>`)
        })
    }
}

function updateUsersInvolved() {
    let subtaskElements = $('.subtask')
    let usersInvolvedEl = $('.overview-users-involved')

    let usersInvolved = []
    subtaskElements.each(function (i) {
        let assigneeInputEl = $(this).find('select[name="assignee[]"]')
            .first()
            .children('option:selected')

        if (!(assigneeInputEl.text() === '' ||
              assigneeInputEl.text() === 'Select Assignee' || 
              assigneeInputEl.val() === 'DEPARTMENT')) {
            usersInvolved.push(assigneeInputEl.text())
        }
    })

    usersInvolvedEl.empty()
    usersInvolved = [...new Set(usersInvolved)];
    if (usersInvolved.length == 0) {
        usersInvolvedEl.append('<span>N/A</span>')
    } else {
        usersInvolved.forEach(user => {
            usersInvolvedEl.append(`<span>- ${user}</span>`)
        })
    }
}

window.updateOverview = updateOverview