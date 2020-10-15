@extends('layouts.app')

@section('style')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
	@if ($user->status == 'T')
		@hasAccess('users', 'terminate')
		@else 
			<style>
				.tab-pane {
					pointer-events: none;
				}
			</style>
		@endhasAccess
	@endif
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Access Control List
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/users">Users</a></li>
                <li class="breadcrumb-item">Edit</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">
                    	<form id="frmUserUpdate" name="frmUserUpdate" role="form" action="{{ url("/admin/users/$user->id/update") }}"  enctype="multipart/form-data" method="POST">
						{{ csrf_field() }}
						<input type="hidden" name="departments" id="departments" value="{{$user->user_type_id}}"/>
						<input type="hidden" id="_method" name="_method" value="PUT" />
						<input type="hidden" id="hidden_mobile_number" name="hidden_mobile_number" value="{{$user->nd_mobile_number}}" />
						<input type="hidden" id="hidden_direct_office_number" name="hidden_direct_office_number" value="{{$user->nd_business_phone1}}" />
				        <div class="row">
							@if ($user->status == 'T')
								@hasAccess('users', 'terminate')
								@else 
									<div class="alert alert-warning col-md-12" role="alert">
										<span>No changes can be made on a terminated user.</span>
									</div>
								@endhasAccess
							@endif
					        <div class="row-header content-header">
								<h3 class="title">User Information</h3>
					        </div>
					        <div class="clearfix"></div>
					        <div class="col-sm-3">
					            <div class="form-group">

									<img id="imagePreview" src="{{$user->image}}" style="width: 100%;"> 
                                  	<div class="controls"> 
                                     <input type="file" id="profileImage" name="profileImage" accept="image/x-png,image/jpeg" onchange="readURL(this,'imagePreview')" /> 
                                 	</div>

					            </div>
							</div>
							
							<div class="col-sm-9">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label for="first_name">First Name:<span class="required">*</span></label>
												<input type="text" class="form-control" name="first_name" id="first_name" value="{{$user->first_name}}" placeholder="Enter First Name"/>
												<span id="first_name-error" style="color:red;"><small></small></span>
											</div>
										</div>

	
										<div class="col-sm-6">
											<div class="form-group">
												<label for="last_name">Last Name:<span class="required">*</span></label>
												<input type="text" class="form-control" name="last_name" id="last_name" value="{{$user->last_name}}" placeholder="Enter Last Name"/>
												<span id="last_name-error" style="color:red;"><small></small></span>
											</div>
										</div>
									</div>
	
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label for="txtCountry">Country:<span class="required">*</span></label>
												<select name="txtCountry" id="txtCountry" class="form-control">
													@if (count($countries)>0)
														@foreach($countries as $country)
															<option value="{{$country->name}}" data-code="{{$country->iso_code_2}}" {{$user->country == $country->name ? "selected" : "" }}>{{$country->name}}</option>
														@endforeach
													@endif
												</select>
											</div>
										</div>
	
										<div class="col-sm-6">
											<div class="form-group">
												<label for="email_address">Email:<span class="required">*</span></label>
												<input type="text" class="form-control" name="email_address" id="email_address" value="{{$user->email_address}}" placeholder="Enter Email Address"/>
												<span id="email_address-error" style="color:red;"><small></small></span>
											</div>
										</div>
									</div>
	
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label for="mobile_number">Mobile Number:<span class="required"></span></label>
												<div class="input-group">
													<label for="businessPhone" class="input-group-addon">1</label>
													<input type="text" class="form-control number-only" name="mobile_number" id="mobile_number" value="{{ $user->nd_mobile_number }}" placeholder="Enter Mobile Number"/>
												</div>
												<span id="mobile_number-error" style="color:red;"><small></small></span>
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="row py-0">
												<div class="form-group col-lg-8 mr-0">
													<label for="direct_office_number">Direct Office Number:<span class="required"></span></label>
													<div class="input-group">
														<label for="direct_office_number_yo" class="input-group-addon">1</label>
														<input id="direct_office_number"
															class="form-control number-only"
															type="text"  
															name="direct_office_number" 
															value="{{ $user->nd_business_phone1 }}"  
															placeholder="Direct office number" />
													</div>
													<span id="direct_office_number-error" style="color:red;"><small></small></span>
												</div>
			
												<div class="form-group col-lg-4 ml-0">
													<label for="direct_office_number_extension">Extension:<span class="required"></span></label>
													<input id="direct_office_number_extension"
														class="form-control number-only"
														type="text"
														name="direct_office_number_extension" 
														value="{{ $user->extension }}" 
														placeholder="Ext" />
													<span id="direct_office_number_extension-error" style="color:red;"><small></small></span>
												</div>
											</div>
										</div>
									</div>
	
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label for="dob">Date of Birth:<span class="required">*</span></label>
												<input type="text" class="form-control datepicker" name="dob" id="dob" value="{{ \Carbon\Carbon::parse($user->dob)->format('m/d/Y')}}" placeholder="Enter Date of Birth"/>
												<span id="dob-error" style="color:red;"><small></small></span>
											</div>
										</div>
	
										<div class="col-sm-6">
											<div class="form-group">
												<label for="status">Status:</label>
												<select name="status" id="status" class="form-control">
													<option value="A" {{$user->status === 'A' ? "selected" : "" }}>Active</option>
													<option value="I" {{$user->status === 'I' ? "selected" : "" }}>Inactive</option>
													@if ($user->status === 'T')
														<option value="T" {{$user->status === 'T' ? "selected" : "" }}>Terminated</option>
													@else 
														@hasAccess('users', 'terminate')
															<option value="T" {{$user->status === 'T' ? "selected" : "" }}>Terminated</option>
														@endhasAccess
													@endif
												</select>
											</div>
										</div>
									</div>

						            @php $isSystemGroup = false; @endphp
						            @foreach($user->departments as $dep)
							            @if($dep->user_type->create_by == 'SYSTEM')
							            @php  $isSystemGroup = true; @endphp
							            @endif
							        @endforeach

									<div class="row">
										<div class="col-sm-6">
				                            <div class="form-group">
				                                <label >Company: </label>
				                                <select class="js-example-basic-single form-control companies" id="companies" name="companies[]" multiple @if($isSystemGroup) disabled @endif>
			                                        @if(count($companies)>0)
					                                    @foreach($companies as $company)  
					                                    <option value="{{$company->id}}" data-code="{{$company->id}}" {{(in_array($company->id,$user->companies->pluck('company_id')->toArray()))? "selected" : "" }}>{{$company->partner_company->company_name}}</option>
					                                    @endforeach
				                                    @endif
				                                </select>
				                                <span id="companies-error" style="color:red;"><small></small></span>
				                            </div>
								            @foreach($user->departments as $dep)
									            @if($dep->user_type->create_by == 'SYSTEM')
									            <div class="form-group">
									                <label for="status">System Group:</label>
									                <input type="text" class="form-control" value="{{$dep->user_type->description}}" disabled />
									            </div>
									            @endif
									        @endforeach
									        <input type="hidden" name="isSystemGroup" @if(!$isSystemGroup) value="0" @else value="1" @endif >
										</div>
									</div>
								</div>

                            
					        
					        <div class="col-sm-6">

					        </div>
					        
					       	<br>
					        <div class="clearfix"></div>

					        @if(!$isSystemGroup)
						        @if(count($companies) > 0)
						        <div class="col-sm-12">
						        	<h4><b>Departments:</b><span class="required">*</span></h4>
									<span id="departments-error" style="color:red;"><small></small></span>
						        	<div class="row">
						        	@foreach($companies as $company)  
						        		<div class="col-sm-12 company-department-cb" id="company-department-cb-{{$company->id}}" style="display: none;">
						        			<h5><b>{{$company->partner_company->company_name}}</b></h5> 
						        		</div>
							            @foreach($company->departments as $department)
							            	@if($department->create_by != 'SYSTEM')
							                <div class="col-sm-3 company-cb company-{{$department->company_id}}" >
							                <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" value="{{$department->id}}" class="department-cb" {{(in_array($department->id,$user->departments->pluck('user_type_id')->toArray()))? "checked" : "" }}/> <label class="control-label">{{$department->description}}</label>  
							                </div>
							                @endif
							            @endforeach
						            @endforeach
						            </div>
						        </div>
						        @endif
					        <br>
					        <div class="clearfix"></div>
					        <div class="col-sm-12" {{$is_partner==1 ? "hidden" : ""}}>
					        	<h4><b>System Defined Group:</b></h4>
					        	<div class="row">
					            @foreach($departments as $department)
					            	@if($department->create_by == 'SYSTEM')
					                <div class="col-sm-3">
					                <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" value="{{$department->id}}" class="sys-dept-cb" {{(in_array($department->id,explode(",",$user->user_type_id)))? "checked" : "" }}/> <label class="control-label">{{$department->description}}</label>  
					                </div>
					                @endif
					            @endforeach
					            </div>
					        </div>
					        @endif

					        <div class="clearfix"></div>
					        <div class="col-sm-12" align="right">
					            <div class="form-group col-sm-3">
					                <input class="btn btn-primary form-control" type="submit" value="Save" />
					            </div>
					        </div>
				   		</div>
						</form>     
                        
                    </div>
               	</div>
            </div>
                        

        </section>
    </div>
@endsection
@section("script")
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="{{ config("app.cdn") . "/js/admin/users.js" . "?v=" . config("app.version") }}"></script>

    <script>
    	
	    function formatSelect2(resource) {
	        return $('<span style="color: black;">' + resource.text + '</span>')
	    }

	    function formatSelect2Result(resource) {
	        return $('<span style="color: black;">' + resource.text + '</span>')
	    }

	    let selectElements = $('.js-example-basic-single');

	    selectElements.select2({
	        templateSelection: formatSelect2,
	        templateResult: formatSelect2Result
	    })

    $('#companies').change(function () {
        var companies = $(this).val();
       	$('.company-cb').hide();
        $('.company-department-cb').hide();

        for (var i=0; i<companies.length; i++) {
            var id = companies[i];
	        $('.company-' + id).show();
	        $('#company-department-cb-' + id).show();
        }
        $('.department-cb').trigger('change');
    });
    $(document).ready(function () {
    	$('#companies').trigger('change');
	});
    </script>

@endsection	