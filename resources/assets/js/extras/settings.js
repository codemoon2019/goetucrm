import axios from "axios";
import swal from "sweetalert2";


(function() {

  function hideFormErrors() {
    let formErrors = document.getElementsByClassName('form-errors');
    for (let i = 0; i < formErrors.length; i++) {
      formErrors[i].classList.add('hidden')
    }
  }
  
  function hideOverlay() {
    let overlay = document.getElementsByClassName('overlay')[0]
    overlay.style.display = 'none'
  }

  function showOverlay() {
    let overlay = document.getElementsByClassName('overlay')[0]
    overlay.style.display = 'block'
  }

  let form = document.getElementsByTagName('form')[0]
  form.addEventListener('submit', function(e) {
    e.preventDefault()
    showOverlay()

    let formData = new FormData(this);
    axios.post('/extras/users/me/settings/edit', formData)
      .then((response) => {
        if (response.data.success) {
          hideOverlay()
          hideFormErrors()
          
          window.location.reload(true)
        }
      })
      .catch((error) => {
        hideOverlay()
        hideFormErrors()

        switch (error.response.status) {
          case 400: 
            let response = error.response.data

            for (let key in response.errors) {
              if (response.errors.hasOwnProperty(key)) {
                let formErrorEl = document.getElementById('form-error-' + key)
                formErrorEl.innerHTML = response.errors[key]
                formErrorEl.classList.add('scroll-to-me')
                formErrorEl.classList.remove('hidden')
              }
            }
  
            $([document.documentElement, document.body]).animate({
              scrollTop: $(".form-error.scroll-to-me").offset().top - 200
            }, 300);
  
            
            for (let i = 0; i < formErrors.length; i++) {
              formErrors[i].classList.remove('scroll-to-me')
            }

            break

          default: 
            swal({
              type: 'error',
              title: "Something's not Right",
              text: 'There was an error processing your request. Please try again later',
              animation: true,
              showConfirmButton: true,
              allowOutsideClick: false
            })
        }
      })
  })

})()