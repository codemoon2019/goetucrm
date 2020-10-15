/**
 * Created by Jianfeng Li on 2017/4/26.
 */

import * as Config from "../react/config";

class Helper {

    /**
     * Init address input as google place search.
     *
     * @param selector
     * @param latitudeSelector
     * @param longitudeSelector
     * @return string
     */
    static initAutocomplete(selector, latitudeSelector = null, longitudeSelector = null) {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        let addressEl = document.querySelector(selector);
        let autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(addressEl),
            {types: ['geocode']});

        autocomplete.addListener('place_changed', function () {
            let addressEl = document.querySelector(selector);
            let latitudeEl = document.querySelector(latitudeSelector);
            let longitudeEl = document.querySelector(longitudeSelector);
            if (addressEl && (latitudeEl || longitudeEl)) {
                let place = autocomplete.getPlace();
                let geocoder = new google.maps.Geocoder();
                geocoder.geocode({"placeId": place.place_id}, function (results, status) {
                    if (status === 'OK') {
                        let location = results[0].geometry.location;
                        if (latitudeEl) {
                            latitudeEl.value = location.lat();
                        }
                        if (longitudeEl) {
                            longitudeEl.value = location.lng();
                        }
                    } else {
                        alert('Geocode was not successful for the following reason: ' + status);
                    }
                });
            }
        });
    }

    /**
     * Fill form by selected row.
     *
     * @param formSelector
     * @param selectedRow
     * @param null closure
     */
    static fillFormBySelectedRow(formSelector, selectedRow, closure = null) {
        let elements;
        let element;
        for (let property in selectedRow) {
            if (selectedRow.hasOwnProperty(property)) {
                elements = $(formSelector).find(`[name=${property}]`);
                if (elements.length > 0) {
                    element = elements[0];
                    if (closure) closure(element, property);

                    if (element.type === "checkbox") {
                        element.checked = selectedRow[property];
                    } else if (element.type === "select-one") {
                        $(element).selectpicker('val', selectedRow[property]);
                    } else if (element.type === "number") {
                        if ('0.01' === element.step) {
                            $(element).val(parseFloat(selectedRow[property]) / 100);
                        } else {
                            element.value = parseFloat(selectedRow[property]);
                        }
                    } else if (element.type === "file") {
                        if (!_.isEmpty(selectedRow[property])) {
                            let fileinputDiv = $(formSelector).find("." + property + "_fileinput");
                            if (fileinputDiv) {
                                fileinputDiv.removeClass("fileinput-new").addClass("fileinput-exists");
                                let fileinputPreview = fileinputDiv.find(".fileinput-preview");
                                fileinputPreview.children().remove();
                                // Create image url info.
                                let imageUrlEl = document.createElement("img");
                                imageUrlEl.setAttribute("width", "200");
                                imageUrlEl.setAttribute("src", `${Config.APP_STORAGE_URL}/${selectedRow[property]}?v=${Config.APP_VERSION}&d=${Date.now()}`);
                                $(fileinputPreview).append(imageUrlEl);

                                let imageUrlInputEl = document.createElement("input");
                                imageUrlInputEl.setAttribute("type", "hidden");
                                imageUrlInputEl.setAttribute("name", `has_${property}`);
                                imageUrlInputEl.setAttribute("value", selectedRow[property]);
                                $(fileinputPreview).append(imageUrlInputEl);
                            }
                        }
                    } else {
                        $(element).val(selectedRow[property]);
                    }
                }
            }
        }
    }

    static toParameterObject(formSelector) {
        let fields = $(formSelector).serializeArray();
        let parameter = {};
        fields.map(function (field) {
            parameter[field["name"]] = field["value"];
        });
        return parameter;
    }

    static removeInputMethod(formSelector) {
        let formElement = document.querySelector(formSelector);
        let inputMethodEl = formElement.querySelector("input[name='_method']");
        if (inputMethodEl) {
            inputMethodEl.remove();
        }
    }

    static insertInputMethod(method, formSelector) {
        let formElement = document.querySelector(formSelector);
        if (!formElement.querySelector("input[name='_method']")) {
            let methodInputEl = document.createElement("input");
            methodInputEl.setAttribute("type", "hidden");
            methodInputEl.setAttribute("name", "_method");
            methodInputEl.setAttribute("value", method);
            formElement.prepend(methodInputEl);
        }
    }
}

export default Helper;