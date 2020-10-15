@extends('layouts.app')

@section('style')
<link href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" />
<style>
	table, th, td {
        border: 1px solid black;
        margin-left: auto;
        margin-right: auto;
    }
    thead {
        text-align: center;
    }
    th, td {
        padding: 0.5em;
    }
    .form-category {
        background-color: #778899;
        color: #ffffff;
    }
	.field-icon {
		float: right;
		margin-left: -22px;
		margin-top: 6px;
		position: fixed;
		z-index: 2;
	}
	.hidetext {
		-webkit-text-security: disc;
	}
</style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Users
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ url("admin/users") }}">Users</a></li>
                <li class="breadcrumb-item">Create</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
				@include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">
                    	<form id="frmUserStore" name="frmUserStore" role="form" action="{{ url("/admin/users") }}"  enctype="multipart/form-data" method="POST">
						{{ csrf_field() }}
						<input type="hidden" name="departments" id="departments" value=""/>
				        <div class="row">
					        <div class="row-header content-header">
					            <h3 class="title">User Information</h3>
					        </div>
					        <div class="clearfix"></div>
					        <div class="col-sm-3">
					            <div class="form-group">
									<img id="imagePreview" src="/images/agent.png" style="width: 100%;"> 
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
											<input type="text" class="form-control" name="first_name" id="first_name" value="" placeholder="Enter First Name"/>
											<span id="first_name-error" style="color:red;"><small></small></span>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group">
											<label for="last_name">Last Name:<span class="required">*</span></label>
											<input type="text" class="form-control" name="last_name" id="last_name" value="" placeholder="Enter Last Name"/>
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
														<option value="{{$country->name}}" data-code="{{$country->iso_code_2}}">{{$country->name}}</option>
													@endforeach
												@endif
											</select>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group">
											<label for="email_address">Email:<span class="required">*</span></label>
											<input type="text" class="form-control" name="email_address" id="email_address" value="" placeholder="Enter Email Address" onblur="validateData('users','email_address',this,'-1','false','empty', 'Email address already been used by other users', 'cp-tab');"/>
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
												<input type="text" class="form-control number-only" name="mobile_number" id="mobile_number" value="{{ old('mobile_number') }}" placeholder="Enter Mobile Number"/>
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
														value="{{ old('direct_office_number') }}" 
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
													value="{{ old('direct_office_number_extension') }}" 
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
											<input type="text" class="form-control datepicker" name="dob" id="dob" value="" placeholder="Enter Date of Birth"/>
											<span id="dob-error" style="color:red;"><small></small></span>
										</div>
									</div>

									<div class="col-sm-6">
										<div class="form-group">
											<label for="status">Status:</label>
											<select name="status" id="status" class="form-control">
												<option value="A">Active</option>
												<option value="I">Inactive</option>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="col-sm-6">
					            <div class="form-group">
					                <label for="password">Password (min. 6 characters):<span class="required">*</span></label>
					                <input type="password" class="form-control" name="password" id="password" value="" placeholder="Enter Password"/>
                                    <span id="password-error" style="color:red;"><small></small></span>
					            </div>
					        </div>
					        <div class="col-sm-6">
					            <div class="form-group">
					                <label for="password_confirmation">Confirm Password:<span class="required">*</span></label>
					                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" value="" placeholder="Confirm Password"/>
                                    <span id="password_confirmation-error" style="color:red;"><small></small></span>
					            </div>
					        </div>
					        
					       	<br>
					        <div class="clearfix"></div>


					        <div class="col-sm-6">
	                            <div class="form-group">
	                                <label >Company: <span class="required">*</span></label>
	                                <select class="js-example-basic-single form-control companies" id="companies" name="companies[]" multiple>
                                        @if(count($companies)>0)
		                                    @foreach($companies as $company)  
		                                    <option value="{{$company->id}}" data-code="{{$company->id}}">{{$company->partner_company->company_name}}</option>
		                                    @endforeach
	                                    @endif
	                                </select>
	                                <span id="companies-error" style="color:red;"><small></small></span>
	                            </div>
                        	</div>


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
						                <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" value="{{$department->id}}" class="department-cb"/> <label class="control-label">{{$department->description}}</label>  
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
					                <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" value="{{$department->id}}" class="sys-dept-cb"/> <label class="control-label">{{$department->description}}</label>  
					                </div>
					                @endif
					            @endforeach
					            </div>
					        </div>

					        <div class="clearfix"></div>
					        <div class="col-sm-12" align="right">
					            <div class="form-group col-sm-3">
					                <!-- <input class="btn btn-primary form-control" type="submit" value="Save" /> -->
					                <button type="button" class="btn btn-primary" onclick="continueToPreview();">
										Continue&hellip;
									</button>
					            </div>
					        </div>
				   		</div>
						<!-- </form>      -->
                    </div>
               	</div>
            </div>
        </section>
    </div>

	<div class="modal fade" id="users-preview">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">User Registration Preview</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<table style="width:75%;">
							<thead>
								<tr>
									<th colspan="2">USER INFORMATION</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>First Name:</th>
									<td><i id="first_name_preview" class="view"></i></td>
								</tr>
								<tr>
									<th>Last Name:</th>
									<td><i id="last_name_preview" class="view"></i></td>
								</tr>
								<tr>
									<th>Email: </th>
									<td><i id="email_address_preview" class="view"></i></td>
								</tr>
								<tr>
									<th>Mobile Number:</th>
									<td><i id="mobile_number_preview" class="view"></i></td>
								</tr>

								<tr>
									<th>Direct Office Number:</th>
									<td><i id="direct_office_number_preview" class="view"></i></td>
								</tr>

								<tr>
									<th>Country:</th>
									<td><i id="txtCountry_preview" class="view"></i></td>
								</tr>
								<tr>
									<th>Status:</th>
									<td><i id="status_preview" class="view"></i></td>
								</tr>
								<tr>
									<th>Date of Birth: </th>
									<td><i id="dob_preview" class="view"></i></td>
								</tr>
								<tr>
									<th>Password:</th>
									<td>
									<!-- <i id="password_preview" class="view hidetext"></i> -->
									<input type="password" name="password_preview" id="password_preview">
									<a onclick="togglePassword();"><i class="fa fa-eye fa-eye-slash field-icon toggle-password"></i></a></td>
								</tr>
								<tr>
									<th>Company:</th>
									<td><ul id="companies_preview"></ul></td>
								</tr>
								<tr class="dept-form">
									<th>Departments:</th>
									<td><ul class="dept-list"></ul></td>
								</tr>
								<tr class="sys-dept-form">
									<th>System Defined Group:</th>
									<td><ul class="sys-dept-list"></ul></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
					<input class="btn btn-primary pull-right" type="submit" value="Save" />
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
    <!-- /.modal -->
	</form>     
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
        // $('.department-cb').prop('checked', false);
        // $('input[name="advance_department_id"]').val('');
        // $('input[name="departments"]').val('');
        $('.company-department-cb').hide();

        for (var i=0; i<companies.length; i++) {
            var id = companies[i];
	        $('.company-' + id).show();
	        $('#company-department-cb-' + id).show();
        }
        $('.department-cb').trigger('change');
    });

    </script>
@endsection