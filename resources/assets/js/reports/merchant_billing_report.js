$(document).ready(function () {
    $('.datatable').dataTable();	
		
    $("#invoiceStatus").on("change",function(){
        $('#billingList').dataTable().fnDestroy();

        var table = $('#billingList').DataTable();
        var _val = $(this).val();

        if (_val == 'unpaid') {   
            table
                .columns(3)
                .search('Unpaid', true, false)
                .draw();
        } else if (_val == 'paid') {
            table
                .columns(3)
                .search('^(?:(?!Unpaid).)*$\r?\n?', true, false)
                .draw(); 
        } else {
            table
                .columns()
                .search('')
                .draw(); 
        }
    })

    $('#invoiceStatus').trigger('change');

    $('#exportBilling').click(function () {
        var selected = $('#invoiceStatus').val();
        exportBillingReport(selected);
    });
});

function showInvoiceList(id, status) {
    showLoadingAlert('Loading...');
    $.getJSON('/billing/getInvoiceList/' + id + '/' + status, null, function(data) { 
        closeLoading(); 
        $('#invoiceList').modal('show');
        $('#tblListInvoice').dataTable().fnDestroy();
        var oTable = $('#tblListInvoice').dataTable({
            "lengthMenu": [25, 50, 75, 100 ],
            "bRetrieve": true
        });

        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }
        $('#tblListInvoice').DataTable().columns.adjust().responsive.recalc();
    });
}	

function exportBillingReport(status) {
    window.open('report_billing_data/' + status);
  }

function showLoadingAlert(msg) {
    swal({
        title: msg,
        allowEscapeKey: false,
        allowOutsideClick: false,
        onOpen: () => {
        swal.showLoading();
        }
    })
}

function closeLoading() {
    swal.close();
}

window.showInvoiceList = showInvoiceList;
window.exportBillingReport = exportBillingReport;