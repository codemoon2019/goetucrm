@extends('layouts.app')

@section('style')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
<style>
    p {
		margin-left: 24px;
	}
	.box-title {
		padding: 10px 15px;
		font-size: 22px !important;
	}
	.box-body {
		padding: 25px;
	}
	/* .sub-add {
		text-align:center;
		height:50px;
		width:100%;
	}
	span {
		vertical-align: middle;
	} */
</style>
@endsection

@section('content')
@php
$access = session('all_user_access');
$admin_access = isset($access['admin']) ? $access['admin'] : "";
$is_admin = true;
if (strpos($admin_access, 'super admin access') === false){
    $is_admin = false;
}
@endphp
@include("partners.details.profile.partnertabs")
<!-- Tabs within a box -->
@include("partners.details.profile.profiletabs")
<div class="tab-content">

	<div class="row">
		<div class="col-md-5">
			<!-- Company Information -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h2 class="box-title">Company Information</h2>
				</div>
				<div class="box-body">
                    <div class="row">
                    	<div class="col-md-12">
							@if($partner_info->partner_type_id != 7)
							<div class="form-group">
								<strong><i class="fa fa-level-up margin-r-5"></i> Upline</strong>

								<p class="text-muted">
									@if($partner_info->parent_id > 0)
										@foreach($uplines as $up)
											@if($partner_info->parent_id == $up->parent_id)
												{{ $up->partner_id_reference }} - {{ $up->company_name }} - {{ $up->upline_partner }}
											@endif
										@endforeach
									@else($partner_info->parent_id)
										No Assigned
									@endif
									<br>
								</p>
								
							</div>
							@endif
                    		<div class="form-group">
								<strong><i class="fa fa-book margin-r-5"></i> Legal Name</strong>

								<p class="text-muted">
									@if($partner_info->partner_type_id == 1)
										@isset($partner_info->business_name)
											{{ $partner_info->business_name }}
										@endisset
									@else
										@isset($partner_info->dba)
											{{ $partner_info->dba }}
										@endisset
									@endif
									<br>
								</p>

								
							</div>
							<div class="form-group">
								<strong><i class="fa fa-flag margin-r-5"></i> Ownership</strong>

								<p class="text-muted">
									@isset($partner_info->ownership)
										@foreach($ownerships as $item)
											@if($partner_info->ownership == $item->code)
												{{ $item->name }}
											@endif
										@endforeach
									@endisset
									<br>									
								</p>

							</div>
                    		<div class="form-group">
								<strong><i class="fa fa-building margin-r-5"></i> DBA</strong>

								<p class="text-muted">
									@isset($partner_info->company_name)
										{{ $partner_info->company_name }}	
									@endisset
									<br>								
								</p>
								
							</div>
							@if($partner_info->partner_type_id != 1)
							<div class="form-group">
								<strong><i class="fa fa-at margin-r-5"></i> Website</strong>

								<p class="text-muted">
									@isset($partner_info->website)
										{{ $partner_info->website }}
									@endisset
									<br>
								</p>
								
							</div>
							@endif
                    		<div class="form-group">
								<strong><i class="fa fa-phone margin-r-5"></i> Business Phone 1</strong>

								<p class="text-muted">
									@isset($partner_info->phone1)
										{{ $partner_info->company_country_code . $partner_info->phone1 }}	
									@endisset
									<br>								
								</p>

							</div>
							<div class="form-group">
								<strong><i class="fa fa-envelope margin-r-5"></i> Email Address</strong>

								<p class="text-muted">
									@isset($partner_info->email)
										{{ $partner_info->email }}	
									@endisset
									<br>								
								</p>
								
							</div>
							@if($partner_info->partner_type_id == 1)
							<div class="form-group">
								<strong><i class="fa fa-sticky-note margin-r-5"></i> Tax ID Number</strong>

								<p class="text-muted" id="taxIDNumber">
									@isset($partner_info->tax_id_number)
										{{ $partner_info->tax_id_number }}	
									@endisset
									<br>								
								</p>
								
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-7">
			<div class="box box-primary">
				<div class="box-header with-border">
					@if($partner_info->partner_type_id == 1)
					<h2 class="box-title">Address</h2>
					@else
					<select id="optAddress" class="form-control">
						<option value='businessAdd'>Business Address Information</option>
						<option value='billingAdd'>Billing Address Information</option>
						<option value='mailingAdd'>Mailing Address Information</option>
					</select>
					@endif
				</div>
				<!-- Business Address -->	
				<div class="box-body" id="businessAdd">
					<div class="row">
                    	<div class="col-md-12">
                    		<div class="form-group">
								<strong> Business Address</strong>

								<div class="d-flex flex-row" style="margin-left: 24px;padding: 10px 10px 10px 0;">
									<span class="text-muted">
									@isset($partner_info->address1)
										{{ $partner_info->address1 }}
									@endisset
									<br>
									</span>
								</div>
    							<div class="d-flex justify-content-between" style="margin-left: 24px;">
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->city)
											{{ $partner_info->city }}
										@endisset
										<br>
										</span>
										<small class=" text-muted align-self-center">(City)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->state)
											@foreach($states as $item)
												@if($item->abbr == $partner_info->state)
													{{ $item->name }} ({{ $item->abbr }})
												@endif
											@endforeach
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(State)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->country)
											@foreach($countries as $item)
												@if($item->name == $partner_info->country)
													{{ $item->name }} ({{ $item->iso_code_2 }})
												@endif
											@endforeach
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(Country)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->zip)
											{{ $partner_info->zip }}
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(Zip)</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- Billing Address -->
				<div class="box-body hide" id="billingAdd">
					<div class="row">
                    	<div class="col-md-12">
                    		<div class="form-group">
								<strong> Billing Address</strong>

								<div class="d-flex flex-row" style="margin-left: 24px;padding: 10px 10px 10px 0;">
									<span class="text-muted">
									@isset($partner_info->billing_address1)
										{{ $partner_info->billing_address1 }}	
									@endisset
									<br>
									</span>
								</div>
    							<div class="d-flex justify-content-between" style="margin-left: 24px;">
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->billing_city)
											{{ $partner_info->billing_city }}
										@endisset
										<br>
										</span>
										<small class=" text-muted align-self-center">(City)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->billing_state)
											@foreach($states as $item)
												@if($item->abbr == $partner_info->billing_state)
													{{ $item->name }} ({{ $item->abbr }})
												@endif
											@endforeach
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(State)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->billing_country)
											@foreach($countries as $item)
												@if($item->name == $partner_info->billing_country)
													{{ $item->name }} ({{ $item->iso_code_2 }})
												@endif
											@endforeach
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(Country)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->billing_zip)
											{{ $partner_info->billing_zip }}
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(Zip)</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- Mailing Address -->
				<div class="box-body hide" id="mailingAdd">
					<div class="row">
                    	<div class="col-md-12">
                    		<div class="form-group">
								<strong> Mailing Address</strong>

								<div class="d-flex flex-row" style="margin-left: 24px;padding: 10px 10px 10px 0;">
									<span class="text-muted">
										@isset($partner_info->business_address)
											{{ $partner_info->business_address }}	
										@endisset
										<br>
									</span>
								</div>
    							<div class="d-flex justify-content-between" style="margin-left: 24px;">
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->business_city)
											{{ $partner_info->business_city }}
										@endisset
										<br>
										</span>
										<small class=" text-muted align-self-center">(City)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->business_state)
											@foreach($states as $item)
												@if($item->abbr == $partner_info->business_state)
													{{ $item->name }} ({{ $item->abbr }})
												@endif
											@endforeach
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(State)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->business_country)
											@foreach($countries as $item)
												@if($item->name == $partner_info->business_country)
													{{ $item->name }} ({{ $item->iso_code_2 }})
												@endif
											@endforeach
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(Country)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->business_zip)
											{{ $partner_info->business_zip }}
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(Zip)</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Contact Person Information -->	
			<div class="box box-primary">
				<div class="box-header with-border">
					<h2 class="box-title">Contact Person Information</h2>
				</div>
				<div class="box-body">
                    <div class="row">
						<div class="col-md-12">
                    		<div class="form-group">
								<strong> Contact Person</strong>
    							<div class="d-flex justify-content-between" style="margin-left: 24px;padding: 10px 10px 10px 0;">
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->position)
											{{ $partner_info->position }}	
										@endisset
										<br>
										</span>
										<small class=" text-muted align-self-center">(Title/Position)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@if($partner_info->first_name != "" || $partner_info->middle_name != "" || $partner_info->last_name != "")
											{{ $partner_info->first_name }}  {{ $partner_info->middle_name }}  {{ $partner_info->last_name }}	
										@endif
										<br>
										</span>
										<small class="text-muted align-self-center">(Full name)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->mobile_number)
											{{ $partner_info->company_country_code . $partner_info->mobile_number }}	
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(Mobile number)</small>
									</div>
									<div class="d-flex flex-column">
										<span class="text-muted">
										@isset($partner_info->contact_email)
											{{ $partner_info->contact_email }}	
										@endisset
										<br>
										</span>
										<small class="text-muted align-self-center">(Email address)</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


</div>
</section>
<!-- /.content -->
</div>
@endsection

@section("script")
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{ config(" app.cdn ") . "/js/partners/partner.js" . "?v=" . config(" app.version ") }}"></script>
<script>
	$('#optAddress').on('change', function() {
		var tabID = $(this).find(":selected").val();
		$("#optAddress option").each(function()
		{
			if ($(this).val() == tabID) {
				$('#' + tabID).removeClass('hide');
			} else {
				$('#' + $(this).val()).addClass('hide');
			}
		});
	});
</script>
@endsection