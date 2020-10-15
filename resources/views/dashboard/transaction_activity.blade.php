<div class="col-lg-12 col-xs-12">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title title-header">Transaction Activity</h3>
        </div>

        <div class="box-body">
            <div class="col-md-12" style="margin-top:20px;">
                <table id="invoice-list" class="table responsive table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Invoice#</th>
                        <th>Product</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<div id="modalViewInvoice" class="modal" role="dialog">
    <div class="modal-dialog" style="max-width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoice-header">Invoice #</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <table class="table table-striped table-bordered table-condensed" id="order-product-detail">
                                <tr>
                                    <td>Client Name</td>
                                    <td id="inv-client">Client Name</td>
                                </tr>
                                <tr>
                                    <td>Invoice Date</td>
                                    <td id="inv-date">1/1/2011</td>
                                </tr>
                                <tr>
                                    <td>Due Date</td>
                                    <td id="inv-due">1/1/2011</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-2">

                        </div>

                        <div class="col-sm-5">
                            <table class="table table-striped table-bordered table-condensed" id="order-product-detail">
                                <tr>
                                    <td>Total Due</td>
                                    <td id="inv-total">100.00 USD</td>
                                </tr>
                                <tr>
                                    <td>Payment Method</td>
                                    <td id="inv-pm">

                                    </td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td id="inv-status"></td>
                                </tr>
                                <tr>
                                    <td>Amount Paid</td>
                                    <td id="inv-amt-paid"></td>
                                </tr>

                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-body">
                    <table class="table  table-striped table-bordered table-condensed" id="invoice-product-detail">
                        <thead>
                            <tr>
                                <th width="20%">Category</th>
                                <th width="30%">Product</th>
                                <th width="10%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">

                </div>
            </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>