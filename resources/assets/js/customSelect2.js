export function templateSelection(resource) {
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

export function templateResult(resource) {
  if (resource.element !== undefined && 
      resource.element.dataset !== undefined && 
      resource.element.dataset.image !== undefined) {

    if (resource.element.dataset.user_type !== undefined) {
      let marginLeft = '0px';
      if (resource.element.dataset.third_level !== undefined) {
        marginLeft = '50px'
      }

      return $(
        '<div style="display: flex; align-items: center;">' +
          '<img class="ticket-img-md" src="' + resource.element.dataset.image + '" style="margin-left:' + marginLeft + '">' +
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

export function matcher (params, data) {
  if ($.trim(params.term) === '') {
    return data;
  }

  if (!params.hasOwnProperty('isInOptGroup')) {
    params.isInOptGroup = false;
  }
  
  if (data.children && data.children.length > 0) {

    var isInOptGroup = false
    var optGroup = data.element.label.toUpperCase()
    var term2 = params.term.toUpperCase()

    if (optGroup.indexOf(term2) > -1) {
      isInOptGroup = true
    }

    var match = $.extend(true, {}, data);

    for (var c = data.children.length - 1; c >= 0; c--) {
      var child = data.children[c];

      if (isInOptGroup) {
        params.isInOptGroup = true
      } else {
        params.isInOptGroup = false
      }

      var matches = matcher(params, child);

      if (matches == null) {
        match.children.splice(c, 1);
      }
    }

    if (match.children.length > 0) {
      return match;
    }

    return matcher(params, match);
  }

  var original = data.text.toUpperCase();
  var term = params.term.toUpperCase();

  var subtitle = '';
  if (data.element.dataset.user_type !== undefined) {
    subtitle = data.element.dataset.user_type
  }


  // Check if the text contains the term
  if (original.indexOf(term) > -1 || subtitle.indexOf(term) > -1 || params.isInOptGroup) {
    return data;
  }

  // If it doesn't contain the term, don't return anything
  return null;
}