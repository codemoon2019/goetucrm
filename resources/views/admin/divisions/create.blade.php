@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Divisions
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/departments">Divisions</a></li>
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
                        <form role="form" action="{{ url("/admin/divisions") }}"  enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}                  

                            <div class="form-group">
                                <label>Location:</label>
                                <input type="text" id="name" name="name" class="form-control dept-acl-input"  placeholder="Location..." value="">
                            </div>

                            <div class="form-group">
                                <label>Description:</label>
                                <input type="text" id="description" name="description" class="form-control dept-acl-input"  placeholder="Description..." value="">
                            </div>
                            
                            <div class="form-group">
                                <label>Company:</label>
                                <select class="form-control"  id="company" name="company" onchange="loadUserData();">
                                    @if($is_admin)
                                    <option value="-1">NO ASSIGNED COMPANY</option>
                                    @endif
                                    @foreach($companies as $c)
                                        <option value="{{ $c->id }}">{{ $c->partner_company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="form-group">
                                <label>Person in charge:</label>
                                <select class="form-control"  id="pointPerson" name="pointPerson" >
                                </select>
                            </div>  

                          <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="businessAddress1">Address:</label>
                                        <input type="text" class="form-control" name="address" id="businessAddress1" value="" placeholder="Enter Address"/>
                                        <span id="businessAddress1-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="country">Country:</label>
                                        <select name="country" id="country" class="form-control">
                                            @foreach($country as $item)
                                              <option value="{{$item->name}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="city">City:</label>
                                        <input type="text" class="form-control" name="city" id="city" value="" placeholder="Enter City"/>
                                        <span id="city-error" style="color:red;"><small></small></span>
                                    </div>
                                </div>
                            </div>

                            </tbody>
                            </table>
                            <div class="form-group">
                                <input class="btn btn-primary" type="submit" value="Save" onClick="this.disabled=true; this.value='Saving. Please wait…';this.form.submit();" />
                            </div>                        
                        </form>
                    </div>
                </div>

        </section>
    </div>
@endsection
@section("script")
<script type="text/javascript">
    
  function loadUserData(){
       $('#pointPerson').prop('disabled', true);
      $.getJSON('/admin/divisions/load_users/'+$('#company').val(), null, function(data) {  
          $('#pointPerson').empty(); 
          if(data.success)
          {
              var newOption = $(data.data);
              $('#pointPerson').append(newOption);                
          }else{
              alert(data.message);
          }
           $('#pointPerson').prop('disabled', false);
      });  
  }
loadUserData();

</script>
@endsection