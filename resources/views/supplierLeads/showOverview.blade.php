@extends('layouts.app')

@section('style')
    <style type="text/css">
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
    </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1 class="d-flex align-items-center">
        <span>{{ $supplierLead->doing_business_as }}</span>
        <span class="badge badge-primary ml-4">Supplier Lead</span>
        <span class="badge badge-success ml-1">Active</span> <!-- @todo To be changed -->
      </h1>
      
      <ol class="breadcrumb">
        <li><a href="/">Dashboard</a></li>
        <li><a href="{{ route('supplierLeads.index') }}">List of Supplier Leads</a></li>
        <li>{{ $supplierLead->doing_business_as }}</li>
      </ol>
      
      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      	<div class="nav-tabs-custom" style="box-shadow: none;">
			<ul class="tabs-rectangular">
				<li class="active">
					<a href="{{ route('supplierLeads.show.overview', $supplierLead->id) }}">
					Summary
					</a>
				</li>
				@if($isInternal)
				<li>
					<a href="{{ route('supplierLeads.show', $supplierLead->id) }}">
					Profile
					</a>
				</li>
				
				<li>
					<a href="{{ route('supplierLeads.show.contacts', $supplierLead->id) }}">
					Contacts
					</a>
				</li>
				
				<li>
					<a href="{{ route('supplierLeads.show.products', $supplierLead->id) }}">
					Products
					</a>
				</li>
				@endif
			</ul>

        	<ul class="nav nav-tabs ui-sortable-handle secondary-tabs"></ul>

			<div class="tab-content p-0">
				<div class="tab-pane active" id="overview">
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
											<div class="form-group">
												<strong><i class="fa fa-level-up margin-r-5"></i> Upline</strong>

												<p class="text-muted">
													@foreach ($upline as $item)
														@if($supplierLead->partner_id < 0)
															@if($item->parent_id == $supplierLead->partner_id) {{ $item->partner_id_reference }} - {{ $item->company_name }} @endif
														@else
															@if($supplierLead->partner_id == $item->parent_id) {{ $item->partner_id_reference }} - {{ $item->company_name }} @if($supplierLead->partner_id == $item->parent_id) (Pending) @endif @endif
														@endif
													@endforeach

													@if($supplierLead->partner_id < 0 && $partner_info[0]->parent_id < 0)
														No Assignee
													@endif
													<br>
												</p>
												
											</div>
											<div class="form-group">
												<strong><i class="fa fa-building margin-r-5"></i> DBA</strong>

												<p class="text-muted">
													@isset($supplierLead->doing_business_as)
														{{ $supplierLead->doing_business_as }}	
													@endisset
													<br>								
												</p>
												
											</div>
											<div class="form-group">
												<strong><i class="fa fa-industry margin-r-5"></i> Business Industry - MCC</strong>

												<p class="text-muted">
													@foreach ($businessTypeGroups as $groupName => $businessTypes)
														@foreach ($businessTypes as $item)
															@if($item->mcc == $supplierLead->business_type_code)  {{ $groupName }} - {{ $item->description }} - {{ $item->mcc }} @endif
														@endforeach
													@endforeach
													<br>
												</p>

												
											</div>
											<div class="form-group">
												<strong><i class="fa fa-book margin-r-5"></i> Legal Business Name</strong>

												<p class="text-muted">
													@isset($supplierLead->business_name)
														{{ $supplierLead->business_name }}
													@endisset
													<br>
												</p>

											</div>
											<div class="form-group">
												<strong><i class="fa fa-phone margin-r-5"></i> Business Phone 1</strong>

												<p class="text-muted">
													@isset($supplierLead->business_phone)
														{{ $supplierLead->country_id . $supplierLead->business_phone }}	
													@endisset
													<br>								
												</p>

											</div>
											<div class="form-group">
												<strong><i class="fa fa-envelope margin-r-5"></i> Email Address</strong>

												<p class="text-muted">
													@isset($supplierLead->business_email)
														{{ $supplierLead->business_email }}	
													@endisset
													<br>								
												</p>
												
											</div>
											@if($supplierLead->partner_type_id ==1)
											<div class="form-group">
												<strong><i class="fa fa-envelope margin-r-5"></i> Tax ID Number</strong>

												<p class="text-muted" id="taxIDNumber">
													@isset($supplierLead->tax_id_number)
														{{ $supplierLead->tax_id_number }}	
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
							<!-- Business Address -->	
							<div class="box box-primary">
								<div class="box-header with-border">
									<h2 class="box-title">Business Address Information</h2>
								</div>
								<div class="box-body">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<strong> Business Address</strong>

												<div class="d-flex flex-row" style="margin-left: 24px;padding: 10px 10px 10px 0;">
													<span class="text-muted">
														@isset($supplierLead->business_address)
															{{ $supplierLead->business_address }}	
														@endisset
														<br>
													</span>
												</div>
												<div class="d-flex justify-content-between" style="margin-left: 24px;">
													<div class="d-flex flex-column">
														<span class="text-muted">
														@isset($supplierLead->city)
															{{ $supplierLead->city }}
														@endisset
														<br>
														</span>
														<small class=" text-muted align-self-center">(City)</small>
													</div>
													<div class="d-flex flex-column">
														<span class="text-muted">
														@isset($supplierLead->state->name)
															{{ $supplierLead->state->name }} ({{ $supplierLead->state->abbr }})
														@endisset
														<br>
														</span>
														<small class="text-muted align-self-center">(State)</small>
													</div>
													<div class="d-flex flex-column">
														<span class="text-muted">
														@isset($supplierLead->country->name)
															{{ $supplierLead->country->name }} ({{ $supplierLead->country->iso_code_2 }})
														@endisset
														<br>
														</span>
														<small class="text-muted align-self-center">(Country)</small>
													</div>
													<div class="d-flex flex-column">
														<span class="text-muted">
														@isset($supplierLead->zip)
															{{ $supplierLead->zip }}	
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
														@isset($supplierLead->contacts[0]->position)
															{{ $supplierLead->contacts[0]->position }}	
														@endisset
														<br>
														</span>
														<small class=" text-muted align-self-center">(Title/Position)</small>
													</div>
													<div class="d-flex flex-column">
														<span class="text-muted">
														@if($supplierLead->contacts[0]->first_name != "" || $supplierLead->contacts[0]->middle_name != "" || $supplierLead->contacts[0]->last_name != "")
															{{ $supplierLead->contacts[0]->first_name }}  {{ $supplierLead->contacts[0]->middle_name }}  {{ $supplierLead->contacts[0]->last_name }}	
														@endif
														<br>
														</span>
														<small class="text-muted align-self-center">(Full name)</small>
													</div>
													<div class="d-flex flex-column">
														<span class="text-muted">
														@isset($supplierLead->contacts[0]->mobile)
															{{ $supplierLead->country_id }} {{ $supplierLead->contacts[0]->mobile }}	
														@endisset
														<br>
														</span>
														<small class="text-muted align-self-center">(Mobile number)</small>
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
			</div>
      	</div>
    </section>
  </div>
@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
@endsection