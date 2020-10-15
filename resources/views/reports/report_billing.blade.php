@extends('layouts.app')

@section('style')
  <link rel="stylesheet" type="text/css" href=@cdn('/css/tickets/edit.css') />
  <style>
	.circle-status {
		width: 40px;
		height: 40px;
		border-radius: 1000px !important;
		display: inline-flex;
	}
	.c-paid {
		border: 2px solid #27ae27;
	}
	.c-unpaid {
		border: 2px solid #e71616;
	}
	.i-stat {
		font-size: 25px;
	}
	.i-paid {
		color: #27ae27;
	}
	.i-unpaid {
		color: #e71616;
	}
  </style>
@endsection

@section('content')
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Merchant Billing Status Report
			<!-- <small>Dito tayo magpapasok ng different pages</small> -->
		</h1>
		<ol class="breadcrumb">
			<li><a href="/">Dashboard </a></li>
			<li><a href="/billing/report">Reports </a></li>
			<li class="active">Merchant Billing Status Report</li>
		</ol>
		<div class="dotted-hr"></div>
	</section>

	<section class="content container">

		<div class="col-md-12 mb-plus-20">
			<div class="row">
				<div class="col-md-4 offset-md-8 text-right">
					<div class="input-group">
						<button class="btn btn-flat btn-success" id="exportBilling">Export</button>&nbsp;
						<select class="form-control s2-state" id="invoiceStatus">
							<option value="unpaid">Unpaid</option>
							<option value="paid">Paid</option>
							<option value="all">All</option>
						</select>
					</div>
					<div>
						<span><small class="text-muted">Filter by invoice status</small></span>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-12 no-padding ">
			<table class="table responsive table-striped table-bordered datatable" id="billingList" data-display-length="-1">
				<thead>
					<tr>
						<th>ID</th> 
						<th>Type</th> 
						<th>Business Name</th> 
						<th>
							Invoice Status
						</th>
					</tr>
				</thead>
				<tbody>
					@foreach($merchant as $billing)
						@if(count($billing->invoiceHeaders) > 0)
							<tr>
								<td align="center">{{ $billing->partner_id_reference }} </td>
								<td align="center">{{ $billing->partner_type_id == 3 ? 'Merchant' : 'Branch' }}</td>
								<td>{{ $billing->partnerCompany->company_name }}</td>
								<td align="center">
									@if($billing->invoiceHeaders->whereInStrict('status', ['U','O','C','R','S','X','L'])->isNotEmpty())
										<a href="javascript:void(0);" onclick="showInvoiceList({{ $billing->id }}, 'U');">
											Unpaid
										</a>
									@else
										<a  href="javascript:void(0);" onclick="showInvoiceList({{ $billing->id }}, 'P');">
											Paid
										</a>
									@endif
								</td>
							</tr>
						@endif 
					@endforeach
				</tbody>
			</table>
		</div>
	</section>
</div>

<div class="modal fade" id="invoiceList" role="dialog">
	<div class="modal-dialog" role="document" style="max-width:800px">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Invoice List</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<table id="tblListInvoice" name="tblListInvoice"  class="datatable table table-bordered table-striped">
									<thead>
										<tr>
											<th>Product Name</th>
											<th>Invoice Date</th>
											<th>Due Date</th>
											<th>Total</th>
											<th>Payment Method</th>
											<th>Status</th>
										</tr>
									</thead>
										<tbody id="loadedProducts">
										</tbody>
								</table>
							</div>                            
							
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('script')
<script src="{{ config(' app.cdn ') . '/js/reports/merchant_billing_report.js' . '?v=' . config(' app.version ') }}"></script>
@endsection