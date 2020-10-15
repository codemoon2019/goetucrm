@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Resource Group Update
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
                        <form role="form" action="{{ url("/admin/dev-access/$resourceGroup->id") }}"  enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}
                        <input type = "hidden" id="_method" name="_method" value="PUT" />
                            
                            <div class="form-group">
                                <label>Resource Group Name:</label>
                                <input type="text" id="name" name="name" class="form-control dept-acl-input"  placeholder="Resource Group Name" value="{{$resourceGroup->name}}">
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="name">Description:<span class="required"></span></label>
                                    <textarea class="form-control" rows="3" name="description" id="description">{!! html_entity_decode($resourceGroup->description) !!}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <input class="btn btn-primary" type="submit" value="Update" />
                            </div> 

                            <h3>
                                Resource Group Access
                                
                                <a href="{{ route('admin.resourcegroup.createaccess',$resourceGroup->id) }}"  class="btn btn-sm btn-primary pull-right">
                                    <i class="fa fa-plus"></i>&nbsp;
                                    <span>Create Resource Group Access</span>
                                </a>
                            </h3>


                            <table id="table-access" name="table-access" class="table table-bordered table-striped"> 
                            <thead>
                                <tr>
                                    <th width="20%">Access</th>
                                    <th width="60%">Description</th>
                                    <th width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>   
                            @foreach($resourceGroup->resourceGroupAccess as $access)
                            <tr>
                                <td>
                                    <b>{{$access->name}}</b>                                 
                                </td>
                                <td>
                                    <b>{!! html_entity_decode($access->description)!!}</b>                                 
                                </td>
                                <td>
                                    <a href="{{ route('admin.resourcegroup.editaccess', $access->id) }}"> 
                                        <input type="button" class="btn btn-primary" value="Edit"/> 
                                    </a>
                                </td>
                            </tr>
                            @endforeach 
                            </tbody>
                            </table>
                                              
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