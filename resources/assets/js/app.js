/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

$(document).on('keyup keypress', function (e) {
    let keyCode = e.keyCode || e.which;

    if(e.target.localName != 'textarea'){
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }


        if(e.target.name.toLowerCase().indexOf('phone') != -1){
            
            if (keyCode === 46) {
                e.preventDefault();
                return false;
            }
        }

        if(e.target.name.toLowerCase().indexOf('fax') != -1){
            
            if (keyCode === 46) {
                e.preventDefault();
                return false;
            }
        }

        if(e.target.name.toLowerCase().indexOf('mobile') != -1){
            
            if (keyCode === 46) {
                e.preventDefault();
                return false;
            }
        }


    }
});

(function ($) {
    $(window).on('load', function(){
        $('#loading').hide();
        
        $.ajax({
            url: $('#url_chat').val(),
            dataType: "script",
            cache: true,
            success: function() {
            
            }
        });

        var windowWidth  = $(this).width();
        if(windowWidth > 767){
            $('.quick-btn').removeClass('hide');
        }
    });


    $(window).resize(function(){
        var windowWidth  = $(this).width();
        if(windowWidth > 767){
            $('.quick-btn').removeClass('hide');
        } else {
            $('.quick-btn').addClass('hide');
        }
    });

    // initialize all tooltips
    $('[data-toggle="tooltip"]').tooltip();

    if ($(".restaurant-item").length !== 0) {
        $(".restaurant-item").on("click", function () {
            $(this).find("form").first().submit();
        })
    }

    // Activate bootstrap-select
    if ($(".selectpicker").length !== 0) {
        $(".selectpicker").selectpicker({
            iconBase: "fa fa-fw",
            tickIcon: "fa-check"
        });
    }

//     if ($('.datetimepicker').length !== 0 || $('.datepicker').length !== 0 || $('.timepicker').length !== 0) {
//         $('.datetimepicker').datetimepicker({
//             icons: {
//                 time: "now-ui-icons tech_watch-time",
//                 date: "now-ui-icons ui-1_calendar-60",
//                 up: "fa fa-chevron-up",
//                 down: "fa fa-chevron-down",
//                 previous: 'now-ui-icons arrows-1_minimal-left',
//                 next: 'now-ui-icons arrows-1_minimal-right',
//                 today: 'fa fa-screenshot',
//                 clear: 'fa fa-trash',
//                 close: 'fa fa-remove'
//             }
//         });
//
//         $('.datepicker').datetimepicker({
//             format: 'MM/DD/YYYY',
//             icons: {
//                 time: "now-ui-icons tech_watch-time",
//                 date: "now-ui-icons ui-1_calendar-60",
//                 up: "fa fa-chevron-up",
//                 down: "fa fa-chevron-down",
//                 previous: 'now-ui-icons arrows-1_minimal-left',
//                 next: 'now-ui-icons arrows-1_minimal-right',
//                 today: 'fa fa-screenshot',
//                 clear: 'fa fa-trash',
//                 close: 'fa fa-remove'
//             }
//         });
//
//         $('.timepicker').datetimepicker({
// //          format: 'H:mm',    // use this format if you want the 24hours timepicker
//             format: 'h:mm A',    //use this format if you want the 12hours timpiecker with AM/PM toggle
//             icons: {
//                 time: "now-ui-icons tech_watch-time",
//                 date: "now-ui-icons ui-1_calendar-60",
//                 up: "fa fa-chevron-up",
//                 down: "fa fa-chevron-down",
//                 previous: 'now-ui-icons arrows-1_minimal-left',
//                 next: 'now-ui-icons arrows-1_minimal-right',
//                 today: 'fa fa-screenshot',
//                 clear: 'fa fa-trash',
//                 close: 'fa fa-remove'
//             }
//         });
//     }

    $('.nav-tabs li a').click(function(){
        $(this).parents('.nav-tabs').find('li.active').removeClass('active');
        $(this).parent().addClass('active');
    });
    $('.adv-search-btn').click(function(){
        $('.adv-search-overlay').fadeIn(500);
        $('.adv-search').delay(1000).css('right', '0');
    });
    $('.adv-close').click(function(e){
        e.preventDefault();
        $('.adv-search').removeAttr('style');
        $('.adv-search-overlay').fadeOut();
    });

    $('a.minimize').click(function(){
        var id = $(this).parents('.chatbox').attr('id');
        id = "#"+id;
        if($(id).hasClass('minimized')){
            $(id).removeClass('minimized')
            $(id).find('div.chat-body').removeClass("hide");
            $(id).find('div.chat-footer').removeClass("hide");
        }else{
            $(id).addClass('minimized');
            $(id).find('div.chat-body').addClass("hide");
            $(id).find('div.chat-footer').addClass("hide");
        }
    });

    $('a[data-toggle="tabs"]').on('mouseup', function(e){
        setTimeout(function(){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust()
                .responsive.recalc();
        }, 1);
    });

    $('.callout-close').click(function(){
        parent = $(this).parent();
        parent.fadeOut(300);
        setTimeout(function(){
            parent.remove();
        }, 400);
    });

    $(".number-only").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl/cmd+A
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: Ctrl/cmd+C
            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: Ctrl/cmd+X
            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: Ctrl/cmd+V
            (e.keyCode == 86 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $(".alphanum").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 86 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) && (e.keyCode < 65 || e.keyCode > 90)) {
            e.preventDefault();
        }
    });

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

    $(document).on('keydown', '.integer-only', function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode == 86 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }

        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('.collapse')
        .on('shown.bs.collapse', function() {
            $(this)
                .parent()
                .find(".fa-arrow-right")
                .removeClass("fa-arrow-right")
                .addClass("fa-arrow-down");
            })
        .on('hidden.bs.collapse', function() {
            $(this)
                .parent()
                .find(".fa-arrow-down")
                .removeClass("fa-arrow-down")
                .addClass("fa-arrow-right");
        });

})(jQuery);

var resizeImg = function() {
    var img = $(".profile-pic");
    // Create dummy image to get real width and height
    $("<img>").attr("src", $(img).attr("src")).load(function(){
        var realWidth = this.width;
        var realHeight = this.height;
        var ratio  = realWidth / realHeight;
        if (ratio > 1) {
            var newWidth = Math.round(ratio * 200);
            $(img).css('height','200px');
            $(img).css('width',newWidth);
        }
    });
}

var readURL = function(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(".profile-pic").attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
        setTimeout(function() {
            resizeImg();
        }, 100);
    }
}

$(".file-upload").on('change', function(){
    readURL(this);
});

$(".upload-button").on('click', function() {
    $(".profileUpload").val('');
    $(".profile-pic").attr('src','');
    $(".profile-pic").css('height','');
    $(".profile-pic").css('width','');
    $(".file-upload").click();
});