import axios from "axios";
import swal from "sweetalert2";
import {templateSelection, templateResult, matcher} from '../customSelect2.js'

$(document).ready(function() {``
  /** 
   * Configurations 
   */
  let banner = $('.callout-close')
  let ckEditor = CKEDITOR
  let selectElements = $('.js-example-basic-single');

  banner.click()
  selectElements.select2({
    templateSelection: templateSelection,
    templateResult: templateResult,
    matcher: matcher,
  })
  ckEditor.replace('description', {
    toolbar: 'Basic',
  })


  /** 
   * Fix form-essentials on scroll down 
   */
  if ( $(window).width() > 739) {   
    let windowEl = $(window)
    let combinedHeaderHeight = $('.main-header').first().height() + $('.content-header').first().outerHeight()
    let contentSubHeader = $('.content-sub-header').first()
    let nextElementOfSubHeader = contentSubHeader.next()

    windowEl.scroll(function (event) {
      if (windowEl.scrollTop() > combinedHeaderHeight) {
        contentSubHeader.css({
          'box-shadow' : '2px 0px 5px black',
          'position': 'fixed',
          'top': 0,
          'left': $('.sidebar').first().width(),
          'right': 0, 
          'z-index': 1051,
        })

        nextElementOfSubHeader.css({
          'margin-top' : contentSubHeader.outerHeight() + 'px'
        })
          
      } else {
        contentSubHeader.first().css({
          'box-shadow' : 'none',
          'position': 'relative',
          'left': 0,
          'box-shadow': 'none',
        })

        nextElementOfSubHeader.css({
          'margin-top': '0px'
        })
      }
    });
  }
  
  /** 
   * Merchant or Partner 
   */
  if (isInternal || isPartner) {
    $('input[name="reference"]').on('change', function() {
      if ($(this).val() == 'Merchant') {
        $('.form-group-merchant').first().removeClass('hidden')
        $('.form-group-partner').first().addClass('hidden')
      } else {
        $('.form-group-merchant').first().addClass('hidden')
        $('.form-group-partner').first().removeClass('hidden')
      }
    })

    setTimeout( function(){ 
      $('input[name="reference"][value="Merchant"]').trigger('click')
    }, 1000);
  }
    
  
  /**
   * Due date
   */
  if (isInternal) {
    $('#timepicker').timepicker({ 
      showInputs: true,
      showMeridian: false,
      icons: {
          up: "fa fa-chevron-up",
          down: "fa fa-chevron-down",
      },
    })

    $('#datepicker').datepicker({ 
      autoclose: true,
      format: 'yyyy-mm-dd',
      startDate: '0d',
      todayHighlight: true,
    })

    $('#datepicker').focus();
    $('.today').click();
    $('#datepicker').blur();
  }


  /**
   * Assignee
   */
  if (isInternal) {
    $('.group-list').on('click', 'option', function() {
      $('.group-list').trigger('change')
    })

    $('.group-list').on('change', function() {
      if ($(this).val() == '') {
        return false
      }

      let departmentName = $('option:selected', this).text();

      $('.sliding-panel').addClass('sliding-panel-right');
      $('.back').removeClass('hide');

      $('select[name="assignee"]').find('option').remove()

      let department = departments.find((department) => department.id == $(this).val());
      department.users.forEach((user) => {
        $('select[name="assignee"]').append(
          '<option value="' + user.id + '" data-image="' + user.image +'">&nbsp;&nbsp;' + 
            user.first_name + ' ' + user.last_name + 
          '</option>'
        );
      })

      if (department.head_id != -1) {
        $('select[name="assignee"]').append(new Option(departmentName, -1));
      }

      $('select[name="assignee"]').select2({
        templateSelection: templateSelection,
        templateResult: templateResult
      })  

      setTimeout( function(){ 
        $('select[name="assignee"]').select2('open')  
      }, 1000);
    });
  
    $('.back').click(function(e) {
      e.preventDefault()

      $('.back').addClass('hide')
      $('.sliding-panel').removeClass('sliding-panel-right')
      $('select[name="assignee"]').val('') 

      let departmentId = $('select[name="department"]').val()
      let department = departments.find((department) => department.id == departmentId);
      department.users.forEach((user) => {
        $('select[name="assignee"]').append(
          '<option value="' + user.id + '" data-image="' + user.image +'">&nbsp;&nbsp;' + 
            user.first_name + ' ' + user.last_name + 
          '</option>'
        );
      })

      if (department.head_id == -1) {
        $('select[name="department"]').val('').trigger('change');
      }
    })

    let gCompanyId = null
    $('select[name="department"]').on('change', function() {
      const companyId = $('option:selected', this).data('company_id')
      if (gCompanyId != companyId) {
        if (gCompanyId != null) {
          $("select[name='merchant']").val("").trigger("change");
          $("select[name='merchant']").val(null).trigger("change");
          $('select[name="merchant"]').select2({
            templateSelection: templateSelection,
            templateResult: templateResult
          })
    
          $("select[name='partner']").val("").trigger("change");
          $("select[name='partner']").val(null).trigger("change");
          $('select[name="partner"]').select2({
            templateSelection: templateSelection,
            templateResult: templateResult
          })
    
          $("select[name='product']").val("").trigger("change");
          $("select[name='product']").val(null).trigger("change");
          $('select[name="product"]').select2({
            templateSelection: templateSelection,
            templateResult: templateResult,
          })
  
          $("select[name='cc_ids[]']").val("").trigger("change");
          $("select[name='cc_ids[]']").val(null).trigger("change");
          $('select[name="cc_ids[]"]').select2({
            templateSelection: templateSelection,
            templateResult: templateResult,
          })
        }
  
        if (companyId === undefined) {
          $('.optgroup').find('option').removeAttr('disabled')
        } else {
          $(`.optgroup-${companyId}`).find('option').removeAttr('disabled')
          $('.optgroup').not(`.optgroup-${companyId}`).find('option').attr('disabled', true)
        }
      }

      gCompanyId = companyId
    })
  }
  
  /**
   * Submit Create Ticket
   */
  $('#form-create-ticket').on('submit', function(e) {
    $('.overlay').show()
    e.preventDefault()

    for (let instance in CKEDITOR.instances) {
      CKEDITOR.instances[instance].updateElement();
    }

    axiosCustom.post('/tickets/store-ajax', new FormData(this))
      .then((response) => {
        $('.form-error').addClass('hidden')
        window.location.replace("/tickets");
      })
      .finally((response) => {
        $('.overlay').hide()
      })
  })


  /**
   * Confirmation Cancel
   */
  $('.btn-cancel').on('click', function(e) {
    e.preventDefault()

    swal({
      title: 'Are you sure?',
      text: "You're changes in this page will be gone",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Stay on this Page'
    }).then((result) => {
      if (!result.value) {
        window.location.replace('/tickets')
      }
    })
  })


  /**
   *      Ticket Reason
   * =========================
   * Change Ticket priority on 
   * ticket reason change
   */
  if (isInternal) {
    $('select[name="ticket_reason_code"]').on('change', function() {
      if ($(this).val() == null)
        return false

      let ticketReason = ticketReasons.find((ticketReason) => ticketReason.id == $(this).val())
      let ticketPriorityEl = $('select[name="ticket_priority_code"]')
      let animate = false
      if (ticketPriorityEl.val() != ticketReason.ticket_priority_code) {
        animate = true
      }

      ticketPriorityEl.val(ticketReason.ticket_priority_code).trigger('change')
      if (animate) {
        animateFade(ticketPriorityEl.next())
      }
    })

    $('select[name="ticket_reason_code"]').trigger('change')
  }

  /**
   *       Ticket Type
   * =========================
   * Change ticket priority on 
   * ticket priority change
   */
  $('select[name="ticket_type_code"]').on('change', function() {
    updateTicketReasonEl()
  })

  $('select[name="product"]').on('change', function() {
    updateTicketIssueTypeEl()
    updateTicketReasonEl()
  })

  function updateTicketReasonEl() {
    let ticketReasonInputEl = $('select[name="ticket_reason_code"]')
        ticketReasonInputEl.find('option').remove()

    ticketReasons.map(ticketReason => {
      let ticketTypeId = $('select[name="ticket_type_code"]').val()
      let productId = $('select[name="product"]').val()

      let condition1 = ticketReason.ticket_type_id == ticketTypeId
      let condition2 = ticketReason.product_id == null
      let condition3 = ticketReason.product_id == productId

      if (condition1 && (condition2 || condition3)) {
        ticketReasonInputEl.append(new Option(
          ticketReason.description, 
          ticketReason.id
        ))
      }
    })

    ticketReasonInputEl.trigger('change')
  }

  function updateTicketIssueTypeEl() {
    let ticketIssueTypeEl = $('select[name="ticket_type_code"]')
    ticketIssueTypeEl.find('option').remove()

    ticketIssueTypes.map(ticketIssueType => {
      let companyId = $('select[name="product"] option:selected').data('company_id');
      let productId = $('select[name="product"] option:selected').val();

      let condition1 = ticketIssueType.company_id == companyId
      let condition2 = ticketIssueType.product_id == null
      let condition3 = ticketIssueType.product_id == productId

      if (condition1 && (condition2 || condition3)) {
        ticketIssueTypeEl.append(new Option(
          ticketIssueType.description, 
          ticketIssueType.id
        ))
      }
    })
  }

  $('select[name="product"]').trigger('change')
  $('select[name="ticket_type_code"]').trigger('change')

  /**
   *      Ticket Priority
   * =========================
   * Change due date on ticket 
   * priority change
   */
  if (isInternal) {
    $('select[name="ticket_priority_code"]').on('change', function() {
      let dueDateInput = $('input[name="due_date"]')
      let dueTimeInput = $('input[name="due_time"]')

      switch ($(this).val()) {
        case 'L':
          dueDateInput.val(dates.LOW)
          animateFade(dueDateInput)
          break
    
        case 'M':
          dueDateInput.val(dates.MEDIUM)
          animateFade(dueDateInput)
          break

        case 'H':
          dueDateInput.val(dates.HIGH)
          animateFade(dueDateInput)
          break

        default:
          dueDateInput.val(dates.URGENT.date)
          dueTimeInput.val(dates.URGENT.time)
          animateFade(dueDateInput)
          animateFade(dueTimeInput)
          break
      }
    })

    $('select[name="ticket_priority_code"]').trigger('change')
  }

  function animateFade(el) {
    el.fadeTo('fast', 0.25)
    el.fadeTo('fast', 1)
    el.fadeTo('fast', 0.25)
    el.fadeTo('fast', 1)
    el.fadeTo('fast', 0.25)
    el.fadeTo('fast', 1)
  }
})