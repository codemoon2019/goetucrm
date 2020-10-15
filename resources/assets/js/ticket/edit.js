import axios from "axios";
import swal from "sweetalert2";
import {templateSelection, templateResult, matcher} from '../customSelect2.js'

$(document).ready(function() {
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
    matcher: matcher
  })

  if ($('textarea[name="message"]').length != 0) {
    ckEditor.replace('message', {
      toolbar : 'Basic',
    })
  }


  /** 
   * Fix header on scroll down 
   */
  if ( $(window).width() > 739) {   
    let windowEl = $(window)
    let mainHeaderHeight = $('.main-header').first().height()
    let contentHeader = $('.content-header').first()
    let nextElementOfContentHeader = contentHeader.next()

    windowEl.scroll(function (event) {
      if (windowEl.scrollTop() > mainHeaderHeight) {
        nextElementOfContentHeader.css({
          'margin-top' : contentHeader.outerHeight() + 'px'
        })

        contentHeader.css({
          'box-shadow' : '2px 0px 5px black',
          'position': 'fixed',
          'top': 0,
          'left': $('.sidebar').first().width(),
          'right': 0, 
          'z-index': 1051,
        })            
      } else {
        nextElementOfContentHeader.css({
          'margin-top': '0px'
        })

        contentHeader.first().css({
          'box-shadow' : 'none',
          'position': 'relative',
          'left': 0,
          'box-shadow': 'none',
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
      if (ticketHeader.requester.partner.partner_type_id == 3 || ticketHeader.requester.partner.partner_type_id == 9) {
        $('input[name="reference"][value="Merchant"]').trigger('click')
      } else {
        $('input[name="reference"][value="Partner"]').trigger('click')
      }
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
  }


  /**
   * Assignee
   */ 
  if (isInternal) {
    $('select[name="department"]').on('click', 'option', function() {
      $('select[name="department"]').trigger('change')
    })

    let initialLoadForAssignee = true;
    $('select[name="department"]').on('change', function() {
      if ($(this).val() == '') {
        return false
      }

      let departmentName = $('option:selected', this).text();

      $('.sliding-panel').addClass('sliding-panel-right');
      $('.back').removeClass('hide');

      $('select[name="assignee"]').find('option').remove()

      let assigneeExist = false
      let department = departments.find((department) => department.id == $(this).val());
      department.users.forEach((user) => {
        $('select[name="assignee"]').append(
          '<option value="' + user.id + '" data-image="' + user.image +'" selected>' + 
            '&nbsp;&nbsp;' + user.first_name + ' ' + user.last_name +
          '</option>'
        );
        
        if (! assigneeExist && user.id == ticketHeader.assignee)
          assigneeExist = true
      })

      if (department.head_id != -1) {
        $('select[name="assignee"]').append(new Option(departmentName, -1));
      }

      $('select[name="assignee"]').select2({
        templateSelection: templateSelection,
        templateResult: templateResult
      })  

      if (initialLoadForAssignee && (assigneeExist || ticketHeader.assignee == - 1)) {
        initialLoadForAssignee = false
        $('select[name="assignee"]').val(ticketHeader.assignee).trigger('change')
      } else {
        setTimeout( function(){ 
          $('select[name="assignee"]').select2('open')  
        }, 1000);
      }
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
    });

    $('select[name="department"]').trigger('change')
  }


  /**
   * Submit Edit Ticket
   */
  $('.btn-submit-reply').on('click', function(e = null) {
    if (e != null)
      e.preventDefault()

    let ticketStatusCode = $(this).data('ticket_status_code')
    for (let instance in CKEDITOR.instances) {
      CKEDITOR.instances[instance].updateElement();
    }

    let apiUrl = `/tickets/submitTicketReply/${ticketHeader.id}/${ticketStatusCode}`
    let formData = new FormData( $('#form-edit-ticket')[0] );

    $('.overlay').show()
    axiosCustom.post(apiUrl, formData)
      .then(() => {
        $('.form-error').addClass('hidden')
        window.location.reload(true);
      })
      .finally(() => {
        $('.overlay').hide()
      })
  })


  /**
   * Reply Type
   */
  $('.reply-type').click(function(){
    $('.reply-type').removeClass('reply-active');
    $(this).addClass('reply-active');
  });

  $('.reply-public').first().trigger('click')


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
   *       Ticket Type
   * =========================
   * Change ticket priority on 
   * ticket priority change
   */
  $('select[name="ticket_type_code"]').on('change', function() {
    updateTicketReasonEl()
  })

  let initialLoadForTicketIssueType = true
  let initialLoadForTicketReason = true
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
        if (ticketReasonId == ticketReason.id) {
          ticketReasonInputEl.append(`<option value="${ticketReason.id}" selected="true">${ticketReason.description}</option>`)
        } else {
          ticketReasonInputEl.append(new Option(
            ticketReason.description, 
            ticketReason.id
          ))
        }
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
        let optionEl = new Option(
          ticketIssueType.
          description,ticketIssueType.id)
        
        ticketIssueTypeEl.append(optionEl)
      }
    })

    if (initialLoadForTicketIssueType) {
      ticketIssueTypeEl.val(ticketIssueTypeId).trigger('change')
      initialLoadForTicketIssueType = false
    }
  }

  $('select[name="product"]').trigger('change')
})
