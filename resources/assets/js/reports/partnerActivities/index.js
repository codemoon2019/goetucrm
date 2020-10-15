import {templateSelection, templateResult, matcher} from '../../customSelect2.js'

$(document).ready(function() {
  $('.select2').select2({
    templateSelection: templateSelection,
    templateResult: templateResult,
    matcher: matcher,
  })

  /**
   * Date
   */
  $('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  })

  $('input[name="date_type"]').on('change', function() {
    $('#datepicker').val('')
    $('input[name="display_by"][value="DAILY"]').trigger('click')
    switch ($(this).val()) {
      case 'day':
        $('input[type="radio"][name="display_by"][value="WEEKLY"]').parent().addClass('hidden')
        $('input[type="radio"][name="display_by"][value="MONTHLY"]').parent().addClass('hidden')
        $('input[type="radio"][name="display_by"][value="YEARLY"]').parent().addClass('hidden')
        $('#datepicker-custom').addClass('hidden')
        $('#datepicker-container').removeClass('hidden')
        $('#datepicker').datepicker('destroy')
        $('#datepicker').datepicker({ 
          autoclose: true,
          format: 'yyyy-mm-dd',
        })
        break

      case 'week':
        $('input[type="radio"][name="display_by"][value="WEEKLY"]').parent().removeClass('hidden')
        $('input[type="radio"][name="display_by"][value="MONTHLY"]').parent().addClass('hidden')
        $('input[type="radio"][name="display_by"][value="YEARLY"]').parent().addClass('hidden')
        $('#datepicker-container').addClass('hidden')
        $('#datepicker-custom').removeClass('hidden')
        $('.datepicker').val('')
        $('.datepicker').last().prop('readonly', true)
        $('.datepicker').last().css({'pointer-events': 'none'})
        break

      case 'month':
        $('input[type="radio"][name="display_by"][value="WEEKLY"]').parent().removeClass('hidden')
        $('input[type="radio"][name="display_by"][value="MONTHLY"]').parent().removeClass('hidden')
        $('input[type="radio"][name="display_by"][value="YEARLY"]').parent().addClass('hidden')
        $('#datepicker-custom').addClass('hidden')
        $('#datepicker-container').removeClass('hidden')
        $('#datepicker').datepicker('destroy')
        $("#datepicker").datepicker({
          autoclose: true,
          format: "yyyy-mm",
          minViewMode: "months",
          startView: "months",  
        });
        break

      case 'year':
        $('input[type="radio"][name="display_by"][value="WEEKLY"]').parent().removeClass('hidden')
        $('input[type="radio"][name="display_by"][value="MONTHLY"]').parent().removeClass('hidden')
        $('input[type="radio"][name="display_by"][value="YEARLY"]').parent().removeClass('hidden')
        $('#datepicker-custom').addClass('hidden')
        $('#datepicker-container').removeClass('hidden')
        $('#datepicker').datepicker('destroy')
        $("#datepicker").datepicker({
          autoclose: true,
          format: "yyyy",
          minViewMode: "years",
          startView: "years",  
        });
        break

      case 'custom':
        $('input[type="radio"][name="display_by"][value="WEEKLY"]').parent().addClass('hidden')
        $('input[type="radio"][name="display_by"][value="MONTHLY"]').parent().addClass('hidden')
        $('input[type="radio"][name="display_by"][value="YEARLY"]').parent().addClass('hidden')
        $('#datepicker-container').addClass('hidden')
        $('#datepicker-custom').removeClass('hidden')
        $('.datepicker').val('')
        $('.datepicker').last().prop('readonly', false)
        $('.datepicker').last().css({'pointer-events': 'auto'})
    }
  })

  $(`input[name="date_type"][value="day"]`).click()
  
  Date.prototype.addDays = function(days) {
    var date = new Date(this.valueOf())
    date.setDate(date.getDate() + days)
    return date
  }

  $('input[name="custom_start_date"]').on('change', function() {
    let startDate = new Date($(this).val());
    let endDate = startDate.addDays(6);

    $('input[name="custom_end_date"]').val(endDate.toISOString().split('T')[0]);
  })


  /**
   * Users
   */
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

    $('select[name="user" ]').find('option').remove()

    if ($(this).val() <= 13) {
      let companyId = $('option:selected', this).data('company_id')
      let departmentId = $(this).val()
      let users = userTypesUsers.filter((user) => {
        return user.company_id == companyId && user.user_type_id.split(',').indexOf(departmentId) > -1;
      })

      users.forEach((user) => {
        $('select[name="user"]').append(
          '<option value="' + user.id + '">' + 
            user.first_name + ' ' + user.last_name + 
          '</option>'
        );

        if (user.user_type_id == 8) {
          user.partner.merchant_branches.forEach((merchantBranch) => {
            $('select[name="user"]').append(
              '<option value="' + merchantBranch.connected_user.id + '">' + 
                '&nbsp;&nbsp;&nbsp;&nbsp;' + merchantBranch.connected_user.first_name + ' ' + merchantBranch.connected_user.last_name + 
              '</option>'
            );
          })
        }
      })

      $('input[name="company_id"]').val(companyId)
    } else {
      let departmentId = $(this).val()
      let department = departments.find((department) => {
        return department.id == departmentId
      })

      department.users.forEach((user) => {
        $('select[name="user"]').append(
          '<option value="' + user.id + '">' + 
            user.first_name + ' ' + user.last_name + 
          '</option>'
        );
      })
    }
    
    $('select[name="user"]').append(new Option(departmentName, ""));
    $('select[name="user"]').select2()  

    setTimeout( function(){ 
      $('select[name="user"]').select2('open')  
    }, 500);
  });

  $('form').submit(function(e) {
    let companyId = $('.group-list option:selected').data('company_id')
    $('input[name="company_id"]').val(companyId)
  })

  $('.back').click(function(e) {
    e.preventDefault()

    $('.back').addClass('hide')
    $('.sliding-panel').removeClass('sliding-panel-right')

    $('select[name="user"]').val('')  
  })
})