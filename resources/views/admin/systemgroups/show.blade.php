@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Access Control List
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Administration</a></li>
                <li class="breadcrumb-item active">Access Control List</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                @include("admin.admintabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">
                        <input type = "hidden" id="access" name="access"/>
                        <input type = "hidden" id="products" name="products"/>
                         <input type = "hidden" id="_method" name="_method" value="PUT" />
                            <span>Set up permissions for each department here</span><br><br>                        
                            <div class="form-group">
                                <label>Department Name:</label>
                                <input type="text" id="description" name="description" class="form-control dept-acl-input"  placeholder="Department Name..." value="{{$department->description}}" disabled>
                            </div>
                            <h4>Products</h4>
                            <div class="form-group">
                                <label>Select products that can be viewed by this department ...</label>
                                <br>
            
                            </div>
                            <div class="row">
                                @foreach($products as $product)
                                    <div class="col-sm-3">
                                    <input type="checkbox" name="{{$product->name}}" id="{{$product->name}}" value="{{$product->id}}" class="product-cb" {{(in_array($product->id, $products_access))? "checked" : "" }} disabled/> <label class="control-label">{{$product->name}}</label>  
                                    </div>
                                @endforeach
                            </div>
                            
                            <br>
                            <div class="form-group">
                                <label>Provide permissions for this department ...</label>
                                <span class="pull-right">
                                    <input type="checkbox" id="showAll" onclick="collapsePermission()"/> <label class="control-label">Show All</label>&nbsp; &nbsp; &nbsp; &nbsp; 
                                </span>
                            </div>

                            <table id="tblACL" name="tblACL" class="table table-bordered table-striped"> 
                            <thead>
                                <tr>
                                    <th width="30%">Modules</th>
                                    <th width="70%">Permissions</th>
                                </tr>
                            </thead>
                            <tbody>   
                            @foreach($acls as $acl)
                            <tr id="main-{{$acl['id']}}" class="main-tr">
                                <td><b>{{$acl['name']}}</b></td>
                                <td><a href="javascript:void(0);" class="btn pull-right" onclick="showPermission({{$acl['id']}})"><i class="fa fa-plus"></i></a></td>
                            </tr>
                            <tr id="sub-{{$acl['id']}}" style="display: none;" class="sub-tr">
                                <td>
                                    <b>{{$acl['name']}}</b>                                 
                                </td>
                                <td>
                                <a href="javascript:void(0);" class="btn pull-right" onclick="hidePermission({{$acl['id']}})"><i class="fa fa-minus"></i></a>
                                <div class="row">
                                   @foreach($acl['department_access'] as $access => $value)
                                   <div class="col-sm-4">
                                     <input type="checkbox" name="{{$value->name}}" id="{{$value->name}}" value="{{$value->id}}" class="acl-cb" {{(in_array($value->id, $department_access))? "checked" : "" }} disabled/> <label>{{str_replace('Add','Create',$value->name)}}</label>  
                                   </div>
                                   @endforeach   
                                </div>  
                                </td>
                            </tr>
                            @endforeach 
                            </tbody>
                            </table>
                    </div>
                </div>

        </section>
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/admin/departments.js" . "?v=" . config("app.version") }}"></script>
@endsection