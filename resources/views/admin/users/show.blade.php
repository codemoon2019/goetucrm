@extends('layouts.app')

@section('content')
	<body onload="disableForm();">
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Users
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/users">Users</a></li>
                <li class="breadcrumb-item">View</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div id="divShow" class="tab-content no-padding">
                    <div class="tab-pane active">
                    	<form role="form" action="{{ url("/admin/users/$user->id") }}"  enctype="multipart/form-data" method="POST">
						{{ csrf_field() }}
						<input type="hidden" name="departments" id="departments" value=""/>
						<input type="hidden" id="_method" name="_method" value="PUT" />
						<input type="hidden" id="hidden_mobile_number" name="hidden_mobile_number" value="{{$user->mobile_number}}" />
				        <div class="row">
					        <div class="row-header content-header">
					            <h3 class="title">User Information</h3>
					        </div>
					        <div class="clearfix"></div>
					        <div class="col-sm-6">
					            <div class="form-group">
					                <label for="first_name">First Name:<span class="required">*</span></label>
					                <input type="text" class="form-control" name="first_name" id="first_name" value="{{$user->first_name}}" placeholder="Enter First Name"/>
					            </div>
					        </div>
					        <div class="col-sm-6">
					            <div class="form-group">
					                <label for="last_name">Last Name:<span class="required">*</span></label>
					                <input type="text" class="form-control" name="last_name" id="last_name" value="{{$user->last_name}}" placeholder="Enter Last Name"/>
					            </div>
					        </div>
					        <div class="col-sm-6">
					            <div class="form-group">
					                <label for="email_address">Email:<span class="required"></span></label>
					                <input type="text" class="form-control" name="email_address" id="email_address" value="{{$user->email_address}}" placeholder="Enter Email Address"/>
					            </div>
					        </div>
							<div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtCountry">Country:<span class="required">*</span></label>
                                    <select name="txtCountry" id="txtCountry" class="form-control">
                                        @if(count($countries)>0)
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}" data-code="{{$country->iso_code_2}}" {{$user->country==$country->name ? "selected" : "" }}>{{$country->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
					        <div class="col-sm-6">
					            <div class="form-group">
						            <label for="mobile_number">
										Mobile Number: <span class="required"></span>
									</label>

                                    <div class="input-group">
                                        <label for="businessPhone" class="input-group-addon">1</label>
										<input type="text" 
											   id="mobile_number" 
											   class="form-control number-only" 
											   name="mobile_number" 
											   value="{{ $user->mobile_number or old('mobile_number')}}" 
											   placeholder="Enter Mobile Number" />
                                    </div>
					            </div>
					        </div>
					        <div class="col-sm-6">
					            <div class="form-group">
					                <label for="status">Status:</label>
					                <select name="status" id="status" class="form-control">
					                    <option value="A" {{$user->status === 'A' ? "selected" : "" }}>Active</option>
					                    <option value="I" {{$user->status === 'I' ? "selected" : "" }}>Inactive</option>
					                </select>
					            </div>
					        </div>
					        <div class="col-sm-6">
					            <div class="form-group">
					                <label for="dob">Date of Birth:<span class="required">*</span></label>
					                <input type="text" class="form-control datepicker" name="dob" id="dob" value="{{ \Carbon\Carbon::parse($user->dob)->format('m/d/Y')}}" placeholder="Enter Date of Birth"/>
					            </div>
					        </div>
					        <div class="col-sm-6">
					                <label for="company">Company:<span class="required">*</span></label>
					                <select name="company" id="company" class="form-control">
					                	@if($is_partner==0 || $user->company_id == -1)
					                	<option value="-1" data-code="-1">--NO COMPANY--</option>
					                	@endif
                                        @if(count($companies)>0)
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}" data-code="{{$company->id}}" {{$user->company_id === $company->id ? "selected" : "" }}>{{$company->partner_company->company_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span id="company-error" style="color:red;"><small></small></span>
					            </div>
					       	<br>
					        <div class="clearfix"></div>
							@if($user->department->create_by != 'SYSTEM')
					         @if(count($departments) > 0)
					         
					        <div class="col-sm-12">
					        	<h4><b>Departments:</b></h4>
					        	<div class="row">
					            @foreach($departments as $department)
					            	@if($department->create_by != 'SYSTEM')
					                <div class="col-sm-3 company-cb company-{{$department->company_id}}" >
					                <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" value="{{$department->id}}" class="department-cb" {{(in_array($department->id,explode(",",$user->user_type_id)))? "checked" : "" }}/> <label class="control-label">{{$department->description}}</label>  
					                </div>
					                @endif
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
				   		</div>
						</form>     
                        
                    </div>
               	</div>
            </div>
                        

        </section>
    </div>
    </body>
@endsection

@section("script")
    <script src="{{ config("app.cdn") . "/js/clearInput.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/admin/users.js" . "?v=" . config("app.version") }}"></script>
@endsection