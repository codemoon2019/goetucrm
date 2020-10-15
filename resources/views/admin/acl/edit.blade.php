@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Permissions
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/acl">Permissions</a></li>
                <li class="breadcrumb-item">Edit</a></li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">     
				        
					    <form role="form" action="{{ url("/admin/acl/$resource->id") }}"  enctype="multipart/form-data" method="POST">
                        <input type="hidden" id="module_access_id" name="module_access_id" value="{{$resource->resource_group_access_id}}"/>
                        <input name="_method" value="PUT" type="hidden">
						{{ csrf_field() }}
							  <div class="row">
                        <div class="row-header content-header">
                            <h3 class="title">Access Information</h3>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Name:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" value="{{$resource->description}}" placeholder="Enter Permission Name"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="resource">Resource:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="resource" id="resource" value="{{$resource->resource}}" placeholder="Enter Resouce URL Access"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="module_category">Module Category:</label>
                                <select name="module_category" id="module_category" class="form-control">
                                	@if(count($resource_groups)>0)
                                		@foreach($resource_groups as $resource_group)
                                    		<option value="{{$resource_group->id}}" {{$resource_group->id === $resource->resource_group_id ? "selected" : "" }}>{{$resource_group->name}}</option>
                                    	@endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
         				<div class="col-sm-6">
                            <div class="form-group">
                                <label for="module_access">Module Access:</label>
                                <select name="module_access" id="module_access" class="form-control">
                                   
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">

                                <input class="btn btn-primary" type="submit" value="Save" />
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
    <script src="{{ config("app.cdn") . "/js/admin/acl.js" . "?v=" . config("app.version") }}"></script>
@endsection
