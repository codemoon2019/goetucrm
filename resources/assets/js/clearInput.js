$('.clear-input').hide()

$(document).on('change', 'input[type="file"]', function(e) {
  if ($(this).val() != '')  {
    let fileInput = $(this)

    $('.clear-input').each(function(index) {
      if ($(this).data('file_id') == fileInput.attr('id')) {
        $(this).show()
      }
    })
  } else {
    let fileInput = $(this)

    $('.clear-input').each(function(index) {
      if ($(this).data('file_id') == fileInput.attr('id')) {
        $(this).hide()
      }
    })
  }
})

$(document).on('click', '.clear-input', function(e) {
  e.preventDefault()

  let element = $('#' + $(this).data('file_id'))
  element.wrap('<form>').closest('form').get(0).reset();
  element.unwrap();

  $(this).hide()  
}) 