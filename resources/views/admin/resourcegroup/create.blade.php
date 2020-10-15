@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Add Resource Group
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
                        <form role="form" action="{{ url("/admin/dev-access") }}"  enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}                            
                            <div class="form-group">
                                <label>Resource Group Name:</label>
                                <input type="text" id="name" name="name" class="form-control dept-acl-input"  placeholder="Resource Group Name" value="">
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="name">Description:<span class="required"></span></label>
                                    <textarea class="form-control" rows="3" name="description" id="description"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <input class="btn btn-primary" type="submit" value="Save" />
                            </div>
                                              
                        </form>
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