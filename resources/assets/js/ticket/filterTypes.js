/**
 * Ticket Filter Types = Quick Filter
 * @type {string}
 */
export const FILTER_TICKET_UNASSIGNED_TICKETS = "FILTER_TICKET_UNASSIGNED_TICKETS";
export const FILTER_TICKET_MY_UNSOLVED_TICKETS = "FILTER_TICKET_MY_UNSOLVED_TICKETS";
export const FILTER_TICKET_MY_PENDING_TICKETS = "FILTER_TICKET_MY_PENDING_TICKETS";
export const FILTER_TICKET_MY_NEW_TICKETS = "FILTER_TICKET_MY_NEW_TICKETS";
export const FILTER_TICKET_MY_CANCELLED_TICKETS = "FILTER_TICKET_MY_CANCELLED_TICKETS";
export const FILTER_TICKET_GROUP_UNSOLVED_TICKETS = "FILTER_TICKET_GROUP_UNSOLVED_TICKETS";
export const FILTER_TICKET_GROUP_PENDING_TICKETS = "FILTER_TICKET_GROUP_PENDING_TICKETS";
export const FILTER_TICKET_GROUP_NEW_TICKETS = "FILTER_TICKET_GROUP_NEW_TICKETS";
export const FILTER_TICKET_GROUP_CANCELLED_TICKETS = "FILTER_TICKET_GROUP_CANCELLED_TICKETS";
export const FILTER_TICKET_RECENTLY_UPDATED = "FILTER_TICKET_RECENTLY_UPDATED";
export const FILTER_TICKET_MY_CLOSED_TICKETS = "FILTER_TICKET_MY_CLOSED_TICKETS";
export const FILTER_TICKET_GROUP_CLOSED_TICKETS = "FILTER_TICKET_GROUP_CLOSED_TICKETS";
export const FILTER_TICKET_MY_CREATED_TICKETS = "FILTER_TICKET_MY_CREATED_TICKETS";
export const FILTER_TICKET_MY_RESOLVED_TICKETS = "FILTER_TICKET_MY_RESOLVED_TICKETS";
export const FILTER_TICKET_GROUP_RESOLVED_TICKETS = "FILTER_TICKET_GROUP_RESOLVED_TICKETS";

/**
 * Filter type
 *
 * @param filterType
 * @returns {{}}
 * @constructor
 */
export function FilterType(filterType) {
    var data = {};
    switch (filterType) {
        case FILTER_TICKET_MY_CREATED_TICKETS:
            data = {
                type: 'create_by',
                value: 'username',
                owner: 'all',
                primaryCondition: "="
            };
            break;
        case FILTER_TICKET_UNASSIGNED_TICKETS:
            data = {
                type: "assignee",
                value: "",
                owner: "group",
                primaryCondition: "="
            };
            break;
        case FILTER_TICKET_MY_NEW_TICKETS:
            data = {
                type: "created_at",
                value: Date.now(),
                owner: "user",
                primaryCondition: "DESC"
            };
            break;
        case FILTER_TICKET_MY_PENDING_TICKETS:
            data = {
                type: "status",
                value: "P",
                owner: "user",
                primaryCondition: "="
            };
            break;
        case FILTER_TICKET_MY_UNSOLVED_TICKETS:
            data = {
                type: "status",
                value: "R",
                owner: "user",
                primaryCondition: "!="
            };
            break;
        case FILTER_TICKET_GROUP_UNSOLVED_TICKETS:
            data = {
                type: "status",
                value: "R",
                owner: "group",
                primaryCondition: "!="
            };
            break;
        case FILTER_TICKET_GROUP_PENDING_TICKETS:
            data = {
                type: "status",
                value: "P",
                owner: "group",
                primaryCondition: "="
            };
            break;
        case FILTER_TICKET_GROUP_NEW_TICKETS:
            data = {
                type: "created_at",
                value: Date.now(),
                owner: "group",
                primaryCondition: "DESC"
            };
            break;
        case FILTER_TICKET_RECENTLY_UPDATED:
            data = {
                type: "updated_at",
                value: Date.now(),
                owner: "group",
                primaryCondition: "DESC"
            };
            break;
        case FILTER_TICKET_MY_CLOSED_TICKETS:
            data = {
                type: "status",
                value: "C",
                owner: "user",
                primaryCondition: "="
            };
            break;
        case FILTER_TICKET_GROUP_CLOSED_TICKETS:
            data = {
                type: "status",
                value: "C",
                owner: "group",
                primaryCondition: "="
            };
            break;
        case FILTER_TICKET_MY_RESOLVED_TICKETS:
            data = {
                type: "status",
                value: "R",
                owner: "user",
                primaryCondition: "="
            };
            break;
        case FILTER_TICKET_GROUP_RESOLVED_TICKETS:
            data = {
                type: "status",
                value: "R",
                owner: "group",
                primaryCondition: "="
            };
            break;
        default:

            break;
    }
    return data;
}