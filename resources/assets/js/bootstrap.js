import * as Config from "../react/config";
import toastr from "toastr";
import _ from "lodash";

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.swal = require('sweetalert2')
window.axios = require('axios');
window.axiosCustom = axios.create({
    headers: {
        'X-CSRF-TOKEN': window.Laravel.csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accepts': 'application/json'
    }
});

window.axiosCustom.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
window.axiosCustom.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axiosCustom.interceptors.response.use(
    function (response) {
        return response;
    }, function (error) {
        switch (error.response.status) {
            case 422:
                $('.form-error').addClass('hidden')

                let response = error.response.data

                for (var key in response.errors) {
                    if (response.errors.hasOwnProperty(key)) {
                        $('#form-error-' + key).text(response.errors[key]);
                        $('#form-error-' + key).addClass('scroll-to-me');
                        $('#form-error-' + key).removeClass('hidden');
                    }
                }
        
                $([document.documentElement, document.body]).animate({
                    scrollTop: $(".form-error.scroll-to-me").offset().top - 200
                }, 300);
        
                $('.form-error').removeClass('scroll-to-me');
                break

            case 500:
                swal({
                    type: 'error',
                    title: "Something's not Right",
                    text: 'There was an error processing your request. Please try again later',
                    animation: true,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    position: "center"
                })
        } 

        return Promise.reject(error);
    });

window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Init toastr options.
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "10000",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

// Add a response interceptor
window.axios.interceptors.response.use(function (response) {
    // Do something with response data
    return response;
}, function (error) {
    console.log(error, error.response.data.errors);
    if (error.response.status === 419) {
        let errors = error.response.data.errors;
        _.forEach(errors, (error, key) => {
            toastr.error(error);
        });
        //window.location.href = document.querySelector("#ctx").getAttribute("content");
    } else if (error.response.status === 422 || error.response.status === 402) {
        let errors = error.response.data.errors;
        _.forEach(errors, (error, key) => {
            if (_.isArray(error)) {
                let msg = "";
                _.forEach(error, (e, key) => {
                    msg += e + "\n";
                });
                toastr.error(msg);
            } else {
                toastr.error("Have fun storming the castle!")
            }
        });
    } else {
        location.href = Config.APP_URL + "/" + error.response.status;
    }
    // Do something with response error
    return Promise.reject(error);
});


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
