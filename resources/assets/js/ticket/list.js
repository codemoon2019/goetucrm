import react from "react";
import axios from "axios";
import swal from "sweetalert2";
import {FilterType} from "./filterTypes";


$(() => {

    var appUrl = document.querySelector("#ctx").getAttribute("content");
    const TICKET_STATUS_CLOSE = "C";
    const TICKET_STATUS_DELETE = "D";
    const TICKET_STATUS_MERGE = "M";

    /**
     * Data list of tickets
     * @type {*|jQuery|HTMLElement}
     */
    var table = $('.ticket-list tbody');
    var dataTableElement;
    var currentFilter = "FILTER_TICKET_MY_CREATED_TICKETS";
    /**
     * Handling products
     * @type {Array}
     */
    var products = [];
    /**
     * Handling departments
     * @type {Array}
     */
    var departments = [];
    /**
     * Loading swal
     * @type {string | null}
     */

    swal({
        title: 'Loading...',
        text: 'Please wait while loading.',
        imageUrl: appUrl + "/images/user_img/goetu-profile.png",
        imageAlt: 'GOETU Image',
        imageHeight: 140,
        animation: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        position: "center"
    });

    setTimeout(() => {

        axios.get(appUrl + "/tickets/list-ajax")
            .then((response) => {

                /**
                 * Destroy
                 */
                $('.ticket-list tbody').empty();
                $('.ticket-list').dataTable().fnDestroy();

                var productChecker = [];
                products = [];
                /**
                 * Empty table
                 */
                table.empty();

                /**
                 * Loop data information
                 */
                $.each(response.data, function (k, v) {

                    console.log(v);

                    var partnerName = (v.partner == undefined ? "" : v.partner.partner_company.company_name);
                    var user = (v.user == undefined ? "" : v.user.email_address);
                    var createdBy = v.create_by;

                    axios.post(appUrl + "/tickets/assignees", {
                        assignees: v.assignee
                    }).then((response) => {

                        var assignees = "";
                        if (response.data.length > 0) {
                            var arrayAssignee = [];
                            for (var as = 0; as < response.data.length; as++) {
                                arrayAssignee.push(response.data[as].first_name + " " + response.data[as].last_name);
                            }
                            assignees = arrayAssignee.join(', ');
                        } else {
                            assignees = "No assignee";
                        }


                        var ticketType = ((v.ticket_type) ? v.ticket_type.description : "");
                        var productName = ((v.product) ? v.product.name : "");
                        var userType = ((v.user_type) ? v.user_type.description : "");
                        var ticketStatus = ((v.ticket_status) ? v.ticket_status.description : "");
                        var ticketPriority = ((v.ticket_priority) ? v.ticket_priority.description : "");

                        /**
                         * Generate ticket body
                         */
                        table.append(`<tr>
                            <td><input type="checkbox" class="quick_pick" value="${v.id}"/></td>
                            <td>${v.id}</td>
                            <td>${ticketType}</td>
                            <td>${v.subject}</td>
                            <td>${createdBy}</td>
                            <td>${partnerName}</td>
                            <td>${productName}</td>
                            <td>${userType}</td>
                            <td>${assignees}</td>
                            <td>${ticketStatus}</td>
                            <td>${ticketPriority}</td>
                            <td>
                                <a href="${appUrl}/tickets/${v.id}/show" class="btn btn-primary ticket-action-buttons"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>`);


                    })

                    /**
                     * Checking products and put it in products array
                     */
                    if (v.product) {
                        if (!productChecker.includes(v.product.id)) {
                            var productObj = {};
                            productObj.id = v.product.id;
                            productObj.name = v.product.name;
                            productObj.departments = v.product.user_types;
                            products.push(productObj);
                            productChecker.push(v.product.id);
                        }
                    }
                });


                /**
                 * Put product in the options
                 */
                if (products.length > 0) {
                    for (var ctr = 0; ctr < products.length; ctr++) {
                        $('#ticket_filter_product').append(`<option value="${products[ctr].id}">${products[ctr].name}</option>`)
                    }
                }
                setTimeout(() => {
                    /**
                     * Generate datatable
                     */
                    $('.ticket-list').dataTable({"order": [1, 'desc']});

                    /**
                     * Close sweetalert
                     */
                    swal.close();

                }, 3000);


            });
    }, 1000);

    /**
     * Change method for ticket_filter_product ( Product selection )
     */
    $(document).on('change', '#ticket_filter_product', function () {
        console.log("PRODUCT HAS BEEN CHANGED!");
        console.log(products);
        var ticketFilterProductId = $(this).val();
        /**
         * Reset
         */
        $('#ticket_filter_department, #ticket_filter_users').empty().html('<option value="" selected>Please Choose One</option>');

        /**
         * Loop products
         */
        for (var p = 0; p < products.length; p++) {

            /**
             * Search for ticket filter product id to products collection
             */
            if (parseInt(products[p].id) == parseInt(ticketFilterProductId)) {
                console.log(products[p].departments);

                /**
                 * Be sure the departments object key is not empty key
                 */
                if (products[p].departments != undefined && products[p].departments != null) {

                    var departmentChecker = [];
                    /**
                     * append departments
                     */
                    for (var d = 0; d < products[p].departments.length; d++) {
                        $('#ticket_filter_department').append(`<option value="${products[p].departments[d].id}">${products[p].departments[d].description}</option>`);

                        /**
                         * Checking departments if exist in department checker array
                         */
                        if (!departmentChecker.includes(products[p].departments[d].id)) {
                            departmentChecker.push(products[p].departments[d].id);
                            /**
                             * Push department information to use in other methods
                             */
                            var departmentObj = {
                                id: products[p].departments[d].id,
                                description: products[p].departments[d].description,
                                users: products[p].departments[d].users
                            };
                            departments.push(departmentObj);
                        }

                    }
                }
            }
        }

    });

    /**
     * Change method for ticket_filter_department ( Department selection )
     */
    $(document).on('change', '#ticket_filter_department', function () {
        console.log("DEPARTMENT HAS BEEN CHANGED!");
        console.log(departments);
        var departmentId = $(this).val();
        /**
         * Check department length
         */
        if (departments.length > 0) {
            /**
             * Reset ticket_filter_users field
             */
            $('#ticket_filter_users').empty().html('<option value="" selected>Please Choose One</option>');
            /**
             * Loop departments
             */
            for (var d = 0; d < departments.length; d++) {
                /**
                 * Check if department exist in the collection
                 */
                if (parseInt(departmentId) == parseInt(departments[d].id)) {

                    /**
                     * Loop to feed users in ticket_filter_users field
                     */
                    for (var u = 0; u < departments[d].users.length; u++) {
                        $('#ticket_filter_users').append(`<option value="${departments[d].users[u].id}">${departments[d].users[u].first_name} ${departments[d].users[u].last_name}</option>`);
                    }

                }

            }
        }
    });

    /**
     * Filter tickets
     */
    $(document).on('click', '#filter_tickets', () => {
        /**
         * Clear data table
         */
        $('.ticket-list tbody').empty();
        $('.ticket-list').dataTable().fnDestroy();
        $('#ticket-buttons').hide();
        swal({
            title: 'Loading...',
            text: 'Filtering information base on filter inputs.',
            imageUrl: appUrl + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        });

        /**
         * Get due by
         */

        var dueByArray = [];
        if ($('input[name="filter_ticket_due_by[]"]:checked').length > 0) {
            $('input[name="filter_ticket_due_by[]"]:checked').each(function (k, v) {
                dueByArray.push($(v).val());
            });
        }
        console.log(dueByArray);
        axios.post(appUrl + "/tickets/filter", {
            'ticket_filter_type': $('#ticket_filter_type').val(),
            'ticket_filter_product': $('#ticket_filter_product').val(),
            'ticket_filter_department': $('#ticket_filter_department').val(),
            'ticket_filter_users': $('#ticket_filter_users').val(),
            'ticket_filter_partner': $('#ticket_filter_partner_user').val(),
            'ticket_filter_created': $('#ticket_filter_created').val(),
            'ticket_filter_status': $('#ticket_filter_status').val(),
            'ticket_filter_due_by': dueByArray
        })
            .then((response) => {

                var productChecker = [];
                products = [];
                /**
                 * Empty table
                 */
                table.empty();

                /**
                 * Loop data information
                 */
                $.each(response.data, function (k, v) {

                    var partnerName = (v.partner_company == undefined ? "" : v.partner_company.company_name);
                    var user = (v.user == undefined ? "" : v.user.email_address);
                    var createdBy = v.create_by;

                    axios.post(appUrl + "/tickets/assignees", {
                        assignees: v.assignee
                    }).then((response) => {

                        var assignees = "";
                        if (response.data.length > 0) {
                            var arrayAssignee = [];
                            for (var as = 0; as < response.data.length; as++) {
                                arrayAssignee.push(response.data[as].first_name + " " + response.data[as].last_name);
                            }
                            assignees = arrayAssignee.join(', ');
                        } else {
                            assignees = "No assignee";
                        }

                        var ticketType = ((v.ticket_type) ? v.ticket_type.description : "");
                        var productName = ((v.product) ? v.product.name : "");
                        var userType = ((v.user_type) ? v.user_type.description : "");
                        var ticketStatus = ((v.ticket_status) ? v.ticket_status.description : "");
                        var ticketPriority = ((v.ticket_priority) ? v.ticket_priority.description : "");

                        /**
                         * Generate ticket body
                         */
                        table.append(`<tr>
                            <td><input type="checkbox" class="quick_pick" value="${v.id}"/></td>
                            <td>${v.id}</td>
                            <td>${ticketType}</td>
                            <td>${v.subject}</td>
                            <td>${createdBy}</td>
                            <td>${partnerName}</td>
                            <td>${productName}</td>
                            <td>${userType}</td>
                            <td>${assignees}</td>
                            <td>${ticketStatus}</td>
                            <td>${ticketPriority}</td>
                            <td>
                                <a href="${appUrl}/tickets/${v.id}/show" class="btn btn-primary ticket-action-buttons"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>`);


                    })

                    /**
                     * Checking products and put it in products array
                     */
                    if (v.product) {
                        if (!productChecker.includes(v.product.id)) {
                            var productObj = {};
                            productObj.id = v.product.id;
                            productObj.name = v.product.name;
                            productObj.departments = v.product.user_types;
                            products.push(productObj);
                            productChecker.push(v.product.id);
                        }
                    }


                });


                /**
                 * Put product in the options
                 */
                if (products.length > 0) {
                    for (var ctr = 0; ctr < products.length; ctr++) {
                        $('#ticket_filter_product').append(`<option value="${products[ctr].id}">${products[ctr].name}</option>`)
                    }
                }

                setTimeout(() => {
                    /**
                     * Generate datatable
                     */
                    $('.ticket-list').dataTable({"order": [1, 'desc']});

                    /**
                     * Close sweetalert
                     */
                    swal.close();
                    $('#ticket-buttons').show();
                    /**
                     * Trigger close
                     */
                    $('.adv-close').trigger('click');
                }, 3000);


            });
    });


    /**
     * Check quick pick
     */
    $(document).on('click', '#check_quick_pick', function () {
        if ($(this).is(":checked")) {
            $('.quick_pick').attr('checked', true).prop('checked', true);
        } else {
            $('.quick_pick').attr('checked', false).prop('checked', false);
        }
    });

    /**
     * Close all checked ticket header
     */
    $(document).on('click', '#btnClose', function (e) {
        e.preventDefault();

        swal({
            title: 'Processing...',
            text: 'Please wait while updating ticket status.',
            imageUrl: appUrl + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        });

        var ticketIds = [];
        $(".quick_pick:checked").each((k, v) => {
            ticketIds.push($(v).val());
        });

        axios.post(appUrl + "/tickets/update-status", {
            status: TICKET_STATUS_CLOSE,
            ids: ticketIds
        }).then(() => {
            // document.location.reload();
            swal.close();
            $('#'+currentFilter).trigger('click');
        });
    });

    /**
     * Delete all checked ticket header
     */
    $(document).on('click', '#btnDelete', function (e) {
        e.preventDefault();

        swal({
            title: 'Processing...',
            text: 'Please wait while updating ticket status.',
            imageUrl: appUrl + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        });

        var ticketIds = [];
        $(".quick_pick:checked").each((k, v) => {
            ticketIds.push($(v).val());
        });

        axios.post(appUrl + "/tickets/update-status", {
            status: TICKET_STATUS_DELETE,
            ids: ticketIds
        }).then(() => {
            // document.location.reload();
            swal.close();
            $('#'+currentFilter).trigger('click');
        });
    });

    /**
     * Merge button
     */

    $(document).on('click', '#btnMerge', function (e) {
        e.preventDefault();
        $('#ticket-buttons').hide();

        var ticketIds = [];
        var count = 0;
        $(".quick_pick:checked").each((k, v) => {
            ticketIds.push($(v).val());
            count++;
        });
        if(count <= 1){
            alert("Please select at least 2 tickets");
            $('#ticket-buttons').show(); 
            return false;          
        }

        swal({
            title: 'Processing...',
            text: 'Please wait while updating tickets.',
            imageUrl: appUrl + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        });

        axios.post(appUrl + "/tickets/update-status", {
            status: TICKET_STATUS_MERGE,
            ids: ticketIds
        }).then(() => {
            // document.location.reload();
            swal.close();
            $('#'+currentFilter).trigger('click');
        })

    });


    /**
     * Changing partner type
     */
    $(document).on('change', '#ticket_filter_partner', function () {
        var partnerId = $(this).val();
        $("#partnerUser").empty().html(`<option value="">Please Choose One</option>`);
        axios.get(appUrl + "/tickets/partner-users/" + partnerId)
            .then((response) => {
                if (response.data.length > 0) {
                    for (var ctr = 0; ctr < response.data.length; ctr++) {
                        $("#ticket_filter_partner_user").append(`<option value="${response.data[ctr].partner_id}">${response.data[ctr].company_name}</option>`);
                    }
                }
            })

    });


    /**
     * Click search button
     */
    $(document).on('click', '#btnSearch', function (e) {
        e.preventDefault();
    });

    /**
     * Click quick filter tickets
     */
    $(document).on('click', '.ticket-list-click', function () {
        var filterType = $(this).attr('filter-type');
        currentFilter = filterType;
        var filterResponse = FilterType(filterType);
        /**
         * Clear data table
         */
        $('.ticket-list tbody').empty();
        $('.ticket-list').dataTable().fnDestroy();
        $('#ticket-buttons').hide();

        swal({
            title: 'Loading...',
            text: 'Filtering information base on filter inputs.',
            imageUrl: appUrl + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        });

        axios.post(appUrl + "/tickets/filter", {
            'type': filterResponse.type,
            'condition': filterResponse.primaryCondition,
            'value': filterResponse.value,
            'owner': filterResponse.owner,
            'sorting': '',
            'ticket_filter_section': 'quick'
        }).then((response) => {

            var productChecker = [];
            products = [];
            /**
             * Empty table
             */
            table.empty();

            /**
             * Loop data information
             */
            $.each(response.data, function (k, v) {

                var partnerName = (v.partner_company == undefined ? "" : v.partner_company.company_name);
                var user = (v.user == undefined ? "" : v.user.email_address);
                var createdBy = v.create_by;

                axios.post(appUrl + "/tickets/assignees", {
                    assignees: v.assignee
                }).then((response) => {

                    var assignees = "";
                    if (response.data.length > 0) {
                        var arrayAssignee = [];
                        for (var as = 0; as < response.data.length; as++) {
                            arrayAssignee.push(response.data[as].first_name + " " + response.data[as].last_name);
                        }
                        assignees = arrayAssignee.join(', ');
                    } else {
                        assignees = "No assignee";
                    }

                    var ticketType = ((v.ticket_type) ? v.ticket_type.description : "");
                    var productName = ((v.product) ? v.product.name : "");
                    var userType = ((v.user_type) ? v.user_type.description : "");
                    var ticketStatus = ((v.ticket_status) ? v.ticket_status.description : "");
                    var ticketPriority = ((v.ticket_priority) ? v.ticket_priority.description : "");

                    /**
                     * Generate ticket body
                     */
                    table.append(`<tr>
                            <td><input type="checkbox" class="quick_pick" value="${v.id}"/></td>
                            <td>${v.id}</td>
                            <td>${ticketType}</td>
                            <td>${v.subject}</td>
                            <td>${createdBy}</td>
                            <td>${partnerName}</td>
                            <td>${productName}</td>
                            <td>${userType}</td>
                            <td>${assignees}</td>
                            <td>${ticketStatus}</td>
                            <td>${ticketPriority}</td>
                            <td>
                                <a href="${appUrl}/tickets/${v.id}/show" class="btn btn-primary ticket-action-buttons"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>`);


                })

                /**
                 * Checking products and put it in products array
                 */
                if (v.product) {
                    if (!productChecker.includes(v.product.id)) {
                        var productObj = {};
                        productObj.id = v.product.id;
                        productObj.name = v.product.name;
                        productObj.departments = v.product.user_types;
                        products.push(productObj);
                        productChecker.push(v.product.id);
                    }
                }


            });


            /**
             * Put product in the options
             */
            if (products.length > 0) {
                for (var ctr = 0; ctr < products.length; ctr++) {
                    $('#ticket_filter_product').append(`<option value="${products[ctr].id}">${products[ctr].name}</option>`)
                }
            }

            setTimeout(() => {
                /**
                 * Generate datatable
                 */
                $('.ticket-list').dataTable({"order": [1, 'desc']});

                /**
                 * Close sweetalert
                 */
                swal.close();
                $('#ticket-buttons').show();
                /**
                 * Trigger close
                 */
                $('.adv-close').trigger('click');
            }, 3000);


        });


    });


});