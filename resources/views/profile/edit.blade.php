@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                User Profile
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">User Profile</a></li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <div class="tab-content no-padding">
                    <div class="tab-pane active">
                    	<form id="frmUser" name="frmUser" role="form" action="{{ url('/user-profile/'.$user->id.'/update') }}"  enctype="multipart/form-data" method="POST">
                    	{{ csrf_field() }}
				        <div class="row">
					        <div class="clearfix"></div>
					        <div class="col-sm-3">
					            <div class="form-group">

									<img id="imagePreview" src="{{$user->image}}" style="width: 100%;"> 
                                  	<div class="controls"> 
                                     <input type="file" id="profileImage" name="profileImage" accept="image/x-png,image/jpeg" onchange="readURL(this,'imagePreview')" /> 
                                 	</div>

					            </div>
					        </div>


					        <div class="col-sm-4">
					            <div class="form-group">
					                <label for="first_name">First Name:<span class="required">*</span></label>
					                <input type="text" class="form-control" name="first_name" id="first_name" value="{{$user->first_name}}" placeholder="Enter First Name"/>
					            </div>

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

					            <div class="form-group">
						            <label for="mobile_number">Mobile Number:<span class="required"></span></label>
                                    <div class="input-group">
                                        <label for="businessPhone" class="input-group-addon">1 </label>
				                		<input type="text" class="form-control number-only" name="mobile_number" id="mobile_number" value="{{str_replace('-','',$user->nd_mobile_number)}}" placeholder="Enter Mobile Number"/>
                                    </div>
					            </div>

								<div class="row py-0">
									<div class="form-group col-lg-8 mr-0">
										<label for="direct_office_number">Direct Office Number:<span class="required"></span></label>
										<div class="input-group">
											<label for="direct_office_number_yo" class="input-group-addon">1</label>
											<input id="direct_office_number"
												class="form-control number-only"
												type="text"  
												name="direct_office_number" 
												value="{{ $user->nd_business_phone1 or old('direct_office_number') }}" 
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
											value="{{ $user->extension or old('direct_office_number_extension') }}" 
											placeholder="Ext" />
										<span id="direct_office_number_extension-error" style="color:red;"><small></small></span>
									</div>
								</div>
					        </div>
					        <div class="col-sm-4">
					            <div class="form-group">
					                <label for="last_name">Last Name:<span class="required">*</span></label>
					                <input type="text" class="form-control" name="last_name" id="last_name" value="{{$user->last_name}}" placeholder="Enter Last Name"/>
					            </div>
	
					            <div class="form-group">
					                <label for="email_address">Email:<span class="required"></span></label>
					                <input type="text" class="form-control" name="email_address" id="email_address" value="{{$user->email_address}}" placeholder="Enter Email Address"/>
					            </div>
					            <div class="form-group">
					                <label for="dob">Date of Birth:<span class="required">*</span></label>
					                <input type="text" class="form-control datepicker" name="dob" id="dob" value="{{ \Carbon\Carbon::parse($user->dob)->format('m/d/Y')}}" placeholder="Enter Date of Birth"/>
					            </div>

							</div>
					        <br>
					       	<br>
					        <div class="clearfix"></div>
					        <div class="col-sm-12" style="display:none"> <!-- hide permanently as per S'Jonald's request -->
					        	@if(count($departments)>0)
					        	<h4><b>Departments:</b></h4>
					        	<div class="row">
					            @foreach($departments as $department)
					                <div class="col-sm-3">
					                <input type="checkbox" name="{{$department->description}}" id="{{$department->description}}" value="{{$department->id}}" class="department-cb" checked disabled/> <label class="control-label">{{$department->description}}</label>  
					                </div>
					            @endforeach
					            </div>
					            @endif
					        </div>

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
    <script>
	$( document ).ready(function() {
	    $('#mobile_number').mask("999-999-9999");
	    $('#direct_office_number').mask("999-999-9999");
	    $('#direct_office_number_extension').mask("999");
		$('#dob').mask("99/99/9999");


	     $('#frmUser').submit(function(e){
	     	if (!isValidDateEx($('#dob').val())){
	            alert("Please input valid date.")
	            return false;    
	        } 
	     });

		$('#txtCountry').change(function (){
	        var country = $('option:selected', this).attr('data-code');
			var url = '/partners/getStateByCountry/'+country;	
	        $.ajax({
	          url: url,
	        }).done(function(items){
		      jQuery("label[for='businessPhone']").html(items.country[0].country_calling_code);
			  jQuery("label[for='direct_office_number_yo']").html(items.country[0].country_calling_code);
	        });

	        if(country == 'CN'){
				$('#direct_office_number').mask("99999999999");
	        	$('#mobile_number').mask("99999999999");
	        }else{
				$('#direct_office_number').mask("999-999-9999");
	        	$('#mobile_number').mask("999-999-9999");
	        }
	    });



	    $('#txtCountry').trigger('change'); 

	});


	function isValidDateEx(dateString)
	{
	    // First check for the pattern
	    if(!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
	        return false;

	    // Parse the date parts to integers
	    var parts = dateString.split("/");
	    var day = parseInt(parts[1], 10);
	    var month = parseInt(parts[0], 10);
	    var year = parseInt(parts[2], 10);

	    // Check the ranges of month and year
	    if(year < 1000 || year > 3000 || month == 0 || month > 12)
	        return false;

	    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

	    // Adjust for leap years
	    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
	        monthLength[1] = 29;

	    // Check the range of the day
	    return day > 0 && day <= monthLength[month - 1];
	}

function readURL(input,id) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $('#'+id).attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}

    </script>
@endsection