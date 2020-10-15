import axios from "axios";
import swal from "sweetalert2";

$( document ).ready(function() {

  function formatSelect2(resource) {
    if (resource.element !== undefined && 
        resource.element.dataset !== undefined && 
        resource.element.dataset.image !== undefined) {
      return $(
        '<span style="margin-left: 3px;">' +
          '<img style="transform: translateY(-1px)" class="ticket-img-xs" src="' + resource.element.dataset.image + '">' +
          '<span style="color: black;">' + resource.text + '</span>' + 
        '</span>'
      )
    }

    return $('<span>' + resource.text + '</span>')
  }

  function formatSelect2Result(resource) {
    if (resource.element !== undefined && 
        resource.element.dataset !== undefined && 
        resource.element.dataset.image !== undefined) {

      if (resource.element.dataset.user_type !== undefined) {
        return $(
          '<div style="display: flex; align-items: center;">' +
            '<img class="ticket-img-md" src="' + resource.element.dataset.image + '">' +
            '<span style="display: flex; flex-direction: column; margin-left: 10px;">' +
              '<span style="font-size: 1.1rem;"><strong>' + resource.text + '</strong></span>' +
              '<span style="font-size: 0.8rem; transform: translate(5px, -2px)">' + resource.element.dataset.user_type + '</span>' +
            '</span><!--/ta-item-actor-details-->' +
          '</div><!--/ta-item-actor--></div>'
        )
      }

      return $(
        '<span>' +
          '<img class="ticket-img-xs" src="' + resource.element.dataset.image + '">' +
          '<span>' + resource.text + '</span>' + 
        '</span>'
      )
    }

    return $('<span>' + resource.text + '</span>')
  }


  let banner = $('.callout-close')
  let selectElements = $('.js-example-basic-single');

  banner.click()
  selectElements.select2({
    templateSelection: formatSelect2,
    templateResult: formatSelect2Result
  });

}); 
