<script>
    load_invoices();
    function load_invoices() {
        $.getJSON('/company/invoice_data', null, function (data) {
            var oTable = $('#invoice-list').dataTable({ "bRetrieve": true , "order": []});
            oTable.fnClearTable();
            if (data.length > 0) {
                oTable.fnAddData(data);
            }
            $('#invoice-list').DataTable().columns.adjust().responsive.recalc();
        });
    }

    function getInvoiceInfo(id) {
        $.getJSON('/company/invoice_details/' + id, null, function (data) {
            $('#invoice-header').html('Invoice # ' + id);
            $('#inv-date').html(data['invoice_date']);
            $('#inv-due').html(data['due_date']);
            $('#inv-total').html(data['total_due']);
            $('#inv-status').html(data['statusDesc']);
            $('#inv-pm').html(data['paymentType']);
            $('#inv-client').html(data['merchant']);
            $('#inv-amt-paid').html(data['amount_paid'] + ' USD');
            
            $("#invoice-product-detail tbody tr").remove();

            for (var i = 0; i < data['details'].length; i++) {
                var table = document.getElementById('invoice-product-detail');
                var sub_id = data['details'][i]['product_id'];
                var sub_cat = data['details'][i]['category'];
                var sub_name = data['details'][i]['productname'];
                var sub_amt = data['details'][i]['amount'];

                var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
                var field_category = row.insertCell(0);
                var field_product_name = row.insertCell(1);
                var field_amount = row.insertCell(2);
                var pid = row.insertCell(3);

                pid.className = "table-val-edit-pid";
                pid.innerHTML = sub_id;
                pid.style.display = "none";

                row.className = "invoicedetailedit";
                row.id = "table-prod-edit-" + sub_id;
                field_product_name.className = "table-val-edit-name";
                field_product_name.innerHTML = sub_name;

                field_category.className = "table-val-edit-cat";
                field_category.innerHTML = sub_cat;

                field_amount.className = "table-val-edit-amount";
                field_amount.innerHTML = parseFloat(sub_amt).toFixed(2);

            }

            $('#invoice-header').html('Invoice # ' + id);
            $('#modalViewInvoice').modal('show');
        });
    }
    
</script>