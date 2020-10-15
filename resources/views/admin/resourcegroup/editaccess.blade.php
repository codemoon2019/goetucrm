@extends('layouts.app')
@section('style')
  <style>
    .form-error {
      color: red;
      display: none;
      font-size: 0.8em;
      padding-top: 3px;
    }

    .overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0,0,0,0.1);
      z-index: 2000;

      display: none;
    }

    .overlay > div {
      margin: 0;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
  </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Permissions
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.resourcegroup.index') }}">Dev Access</a></li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <div class="tab-content no-padding">
                    <div class="tab-pane active">     
				        
					    <form role="form" action="{{ url("/admin/dev-access/$access->id/editaccess") }}"  enctype="multipart/form-data" method="POST">
                        <input name="_method" value="PUT" type="hidden">
						{{ csrf_field() }}
						<div class="row">
                        <div class="row-header content-header">
                            <h3 class="title">Access Information</h3>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="name">Name:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" value="{{$access->name}}" placeholder="Enter Permission Name"/>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="name">Description:<span class="required"></span></label>
                                <textarea class="form-control" rows="3" name="description" id="description">{!! html_entity_decode($access->description) !!}</textarea>
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
@section('script')
    <script>
    $(document).ready(function() {
      /** 
       * Configurations 
       */
      let ckEditor = CKEDITOR
      ckEditor.replace('description', {
        toolbar : 'Basic',
      })
    });
    </script>
@endsection
