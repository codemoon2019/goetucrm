const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */


mix.options({
    uglify: {
        uglifyOptions: {
            compress: {
                drop_console: true,
            }
        }
    },
});

/**
 * Administrative JS
 */

//Company
mix.react('resources/assets/js/admin/company/list.js','public/js/admin/company/list.js');

//products
mix.react('resources/assets/js/products/create.js', 'public/js/products/create.js');
mix.react('resources/assets/js/products/list.js', 'public/js/products/list.js');
mix.react('resources/assets/js/products/edit.js', 'public/js/products/edit.js');
mix.react('resources/assets/js/products/templates.js', 'public/js/products/templates.js');
mix.react('resources/assets/js/products/workflow.js', 'public/js/products/workflow.js');
mix.react('resources/assets/js/products/productfee.js', 'public/js/products/productfee.js');
// mix.react('resources/assets/js/products/editEmailTemplate.js', 'public/js/products/editEmailTemplate.js');

//partners
mix.react('resources/assets/js/partners/list.js', 'public/js/partners/list.js');
mix.react('resources/assets/js/partners/partner.js', 'public/js/partners/partner.js');
mix.react('resources/assets/js/partners/product.js', 'public/js/partners/product.js');
mix.react('resources/assets/js/partners/commission.js', 'public/js/partners/commission.js');
mix.react('resources/assets/js/partners/partnercontact.js', 'public/js/partners/partnercontact.js');
mix.react('resources/assets/js/partners/agent_applicants/index.js', 'public/js/partners/agent_applicants/index.js');
mix.react('resources/assets/js/partners/newFieldValidation.js', 'public/js/partners/newFieldValidation.js');

//admin
mix.react('resources/assets/js/admin/acl.js', 'public/js/admin/acl.js');
mix.react('resources/assets/js/admin/admin.js', 'public/js/admin/admin.js');
mix.react('resources/assets/js/admin/departments.js', 'public/js/admin/departments.js');
mix.react('resources/assets/js/admin/index.js', 'public/js/admin/index.js');
mix.react('resources/assets/js/admin/users.js', 'public/js/admin/users.js');
mix.react('resources/assets/js/admin/charts.js', 'public/js/admin/charts.js');
mix.react('resources/assets/js/admin/companysettings.js', 'public/js/admin/companysettings.js');
mix.react('resources/assets/js/admin/merchantDashboard.js', 'public/js/admin/merchantDashboard.js');
mix.react('resources/assets/js/admin/merchantCharts.js', 'public/js/admin/merchantCharts.js');
mix.react('resources/assets/js/admin/merchantProductCharts.js', 'public/js/admin/merchantProductCharts.js');
mix.react('resources/assets/js/admin/merchant_active_vs_cancelled.js', 'public/js/admin/merchant_active_vs_cancelled.js');
mix.react('resources/assets/js/admin/merchants_enrollment.js', 'public/js/admin/merchants_enrollment.js');
mix.react('resources/assets/js/admin/sales_trends.js', 'public/js/admin/sales_trends.js');
mix.react('resources/assets/js/admin/partnerCharts.js', 'public/js/admin/partnerCharts.js');
mix.react('resources/assets/js/admin/partnerChartsPie.js', 'public/js/admin/partnerChartsPie.js');
mix.react('resources/assets/js/admin/partnerInvoiceCharts.js', 'public/js/admin/partnerInvoiceCharts.js');
mix.react('resources/assets/js/admin/sales_profit.js', 'public/js/admin/sales_profit.js');
mix.react('resources/assets/js/admin/salesProfitCharts.js', 'public/js/admin/salesProfitCharts.js');
mix.react('resources/assets/js/admin/incoming_leads_today.js', 'public/js/admin/incoming_leads_today.js');
mix.react('resources/assets/js/admin/incomingLeadsCharts.js', 'public/js/admin/incomingLeadsCharts.js');
mix.react('resources/assets/js/admin/total_leads.js', 'public/js/admin/total_leads.js');
mix.react('resources/assets/js/admin/totalLeadsChart.js', 'public/js/admin/totalLeadsChart.js');
mix.react('resources/assets/js/admin/leads_payment_processor.js', 'public/js/admin/leads_payment_processor.js');
mix.react('resources/assets/js/admin/leadPaymentProcessorChart.js', 'public/js/admin/leadPaymentProcessorChart.js');
mix.react('resources/assets/js/admin/converted_leads.js', 'public/js/admin/converted_leads.js');
mix.react('resources/assets/js/admin/convertedLeadsChart.js', 'public/js/admin/convertedLeadsChart.js');
mix.react('resources/assets/js/admin/converted_prospects.js', 'public/js/admin/converted_prospects.js');
mix.react('resources/assets/js/admin/convertedProspectsChart.js', 'public/js/admin/convertedProspectsChart.js');
mix.react('resources/assets/js/admin/appointments_per_day.js', 'public/js/admin/appointments_per_day.js');
mix.react('resources/assets/js/admin/appointmentsPerDayChart.js', 'public/js/admin/appointmentsPerDayChart.js');
//merchants
mix.react('resources/assets/js/merchants/list.js', 'public/js/merchants/list.js');
mix.react('resources/assets/js/merchants/board.js', 'public/js/merchants/board.js');
mix.react('resources/assets/js/merchants/approve.js', 'public/js/merchants/approve.js');
mix.react('resources/assets/js/merchants/create.js', 'public/js/merchants/create.js');
mix.react('resources/assets/js/merchants/details.js', 'public/js/merchants/details.js');
mix.react('resources/assets/js/merchants/product.js', 'public/js/merchants/product.js');
mix.react('resources/assets/js/merchants/merchant.js', 'public/js/merchants/merchant.js');
mix.react('resources/assets/js/merchants/workflow.js', 'public/js/merchants/workflow.js');
mix.react('resources/assets/js/merchants/invoice.js', 'public/js/merchants/invoice.js');
mix.react('resources/assets/js/merchants/orders.js', 'public/js/merchants/orders.js');
mix.react('resources/assets/js/merchants/draft.js', 'public/js/merchants/draft.js');
mix.react('resources/assets/js/merchants/process.js', 'public/js/merchants/process.js');
mix.react('resources/assets/js/merchants/newFieldValidation.js', 'public/js/merchants/newFieldValidation.js');
mix.react('resources/assets/js/merchants/toggleColumn.js', 'public/js/merchants/toggleColumn.js');


/** Task Comment Section React Components */
mix.react('resources/assets/js/merchants/commentSection/commentSection.js',
    'public/js/merchants/commentSection/commentSection.js');
mix.react('resources/assets/js/merchants/commentSection/inputSection.js',
    'public/js/merchants/commentSection/inputSection.js');
mix.react('resources/assets/js/merchants/commentSection/inputAttachments.js',
    'public/js/merchants/commentSection/inputSection.js');
mix.react('resources/assets/js/merchants/commentSection/inputAttachment.js',
    'public/js/merchants/commentSection/inputSection.js');
mix.react('resources/assets/js/merchants/commentSection/inputActions.js',
    'public/js/merchants/commentSection/inputSection.js');
mix.react('resources/assets/js/merchants/commentSection/commentArea.js',
    'public/js/merchants/commentSection/commentArea.js');
mix.react('resources/assets/js/merchants/commentSection/comment.js',
    'public/js/merchants/commentSection/comment.js');
mix.react('resources/assets/js/merchants/commentSection/reply.js',
    'public/js/merchants/commentSection/reply.js');
mix.react('resources/assets/js/merchants/commentSection/privacySection.js',
    'public/js/merchants/commentSection/privacySection.js');



//Leads
mix.react('resources/assets/js/leads/list.js', 'public/js/leads/list.js');
mix.react('resources/assets/js/leads/calendar.js', 'public/js/leads/calendar.js');

//Prospects
mix.react('resources/assets/js/prospects/list.js', 'public/js/prospects/list.js');
mix.react('resources/assets/js/prospects/calendar.js', 'public/js/prospects/calendar.js');

//Inventory
mix.react('resources/assets/js/inventory/purchaseorder.js', 'public/js/inventory/purchaseorder.js');
mix.react('resources/assets/js/inventory/receivingpurchaseorder.js', 'public/js/inventory/receivingpurchaseorder.js');

//Reports
mix.react('resources/assets/js/reports/comm_reports.js', 'public/js/reports/comm_reports.js');
mix.react('resources/assets/js/reports/product_report.js', 'public/js/reports/product_report.js');
mix.react('resources/assets/js/reports/dates.js', 'public/js/reports/dates.js');
mix.react('resources/assets/js/reports/partner_report.js', 'public/js/reports/partner_report.js');
mix.react('resources/assets/js/reports/partner_report_charts.js', 'public/js/reports/partner_report_charts.js');
mix.react('resources/assets/js/reports/partner_report_graph.js', 'public/js/reports/partner_report_graph.js');
mix.react('resources/assets/js/reports/merchant_billing_report.js', 'public/js/reports/merchant_billing_report.js');

/** 
 * Tickets - Stylesheet
 */
mix.sass('resources/assets/sass/tickets/create.scss', 'public/css/tickets/create.css');
mix.sass('resources/assets/sass/tickets/edit.scss', 'public/css/tickets/edit.css');

/**
 * Tickets - Scripts
 */
mix.react('resources/assets/js/customSelect2.js','public/js/customSelect2.js');
mix.react('resources/assets/js/ticket/filterTypes.js','public/js/ticket/filterTypes.js');
mix.react('resources/assets/js/ticket/list.js','public/js/ticket/list.js');
mix.react('resources/assets/js/ticket/create.js','public/js/ticket/create.js');
mix.react('resources/assets/js/ticket/edit.js','public/js/ticket/edit.js');
mix.react('resources/assets/js/ticket/show.js','public/js/ticket/show.js');
mix.react('resources/assets/js/ticket/internal.js','public/js/ticket/internal.js');
mix.react('resources/assets/js/ticket/adminTicket.js','public/js/ticket/adminTicket.js');
mix.react('resources/assets/js/ticket/replySection.js','public/js/ticket/replySection.js');
mix.react('resources/assets/js/reports/partnerActivities/index.js','public/js/reports/partnerActivities/index.js');

/**
 * Banners
 */
mix.react('resources/assets/js/admin/banners/index.js','public/js/admin/banners/index.js');

//Calendar
mix.react('resources/assets/js/calendar/calendar.js', 'public/js/calendar/calendar.js');
mix.react('resources/assets/js/calendar/googleCalendar.js', 'public/js/calendar/googleCalendar.js');
mix.react('resources/assets/js/calendar/outlookCalendar.js', 'public/js/calendar/outlookCalendar.js');

//Chat
mix.react('resources/assets/js/firebase/init.js', 'public/js/firebase/init.js');
mix.react('resources/assets/js/chat/chat.js', 'public/js/chat/chat.js');
mix.react('resources/assets/js/extras/chatCenter.js', 'public/js/extras/chatCenter.js');

//Notification
mix.react('resources/assets/js/extras/notification.js', 'public/js/extras/notification.js');
mix.react('resources/assets/js/clearInput.js', 'public/js/clearInput.js');
mix.react('resources/assets/js/extras/settings.js','public/js/extras/settings.js');

// Draft Applicants
mix.react('resources/assets/js/drafts/list.js', 'public/js/drafts/list.js');

//Suggestion
mix.react('resources/assets/js/extras/suggestion.js','public/js/extras/suggestion.js');

//Change Company
mix.react('resources/assets/js/extras/changeCompany.js','public/js/extras/changeCompany.js');

mix.react('resources/assets/js/supplierLeads/supplierLead.js', 'public/js/supplierLeads/supplierLead.js');
mix.react('resources/assets/js/supplierLeads/contacts.js', 'public/js/supplierLeads/contacts.js');
mix.react('resources/assets/js/supplierLeads/products.js', 'public/js/supplierLeads/products.js');
mix.react('resources/assets/js/supplierLeads/mcc.js', 'public/js/supplierLeads/mcc.js');

/*
|-----------------------------------------------
| Workflow
|-----------------------------------------------
*/
mix.sass('resources/assets/sass/workflow/kanban.scss', 'public/css/workflow/kanban.css');
mix.sass('resources/assets/sass/workflow/recentActivities.scss', 'public/css/workflow/recentActivities.css');


mix.react('resources/assets/js/products/templates/workflow/subtask.js', 'public/js/products/templates/workflow/subtask.js');
mix.react('resources/assets/js/products/templates/workflow/overview.js', 'public/js/products/templates/workflow/overview.js');
mix.react('resources/assets/js/products/templates/workflow/validator.js', 'public/js/products/templates/workflow/validator.js');

/**
 * Application default assets
 */
mix.react('resources/assets/js/bootstrap.js', 'public/js');
mix.react('resources/assets/js/app.js', 'public/js');
mix.react('resources/assets/js/treant.js', 'public/js');
mix.scripts([
    "public/js/bootstrap.js",
    "public/js/app.js",
], "public/js/_all.js");

mix.sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/main.scss', 'public/css');
mix.styles(['resources/assets/css/default.css'], 'public/css/default.css');

// https://browsersync.io/docs/options
/* mix.browserSync({
    proxy: 'merchant.goetu.com',
    open: false
}); */