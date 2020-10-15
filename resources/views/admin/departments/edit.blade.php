@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Departments
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/departments">Departments</a></li>
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
                        <form role="form" action="{{ url("/admin/departments/$department->id") }}"  enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}
                        <input type = "hidden" id="access" name="access"/>
                        <input type = "hidden" id="products" name="products"/>
                        <input type = "hidden" id="_method" name="_method" value="PUT" />
                            <div class="pull-right">
                                <input class="btn btn-primary" type="submit" value="Save" />
                            </div>
                            <h4>Company: {{$company}}</h4><br>
                            <span>Set up permissions for each department here</span><br><br>

                            <div class="row">
                                <div class="col-sm-3">
                                     <input type="checkbox" name="chkIsChatSupport" id="chkIsChatSupport" @if($department->is_chat_support == 1) checked @endif/> <label class="control-label">Chat Support Department</label>
                                     </div>
                                     <div class="col-sm-3"> 
                                     <input type="color" name="depColor" id="depColor" value="{{$department->color}}"/> <label class="control-label">Color Code</label> 
                                </div>
                            </div>



                            <div class="form-group">
                                <label>Division:</label>
                                <select class="form-control"  id="division" name="division">
                                    <option value="-1">NO ASSIGNED DIVISION</option>
                                    @foreach($division as $d)
                                        <option value="{{ $d->id }}" {{$d->id==$department->division_id ? "selected" : "" }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div> 

                            <div class="form-group">
                                <label>Parent Department:</label>
                                <select class="form-control"  id="depHead" name="depHead">
                                    <option value="-1" @if($department->parent_id == -1) selected @endif>No Assigned Parent Department</option>
                                @foreach($departments as $dep)
                                    <option value="{{ $dep->id }}" {{$dep->id==$department->parent_id ? "selected" : "" }}>{{ $dep->description }}</option>
                                @endforeach
                                </select>
                            </div>                            

                            <div class="form-group">
                                <label>Department Name:</label>
                                <input type="text" id="description" name="description" class="form-control dept-acl-input"  placeholder="Department Name..." value="{{$department->description}}">
                            </div>

                            <div class="form-group">
                                <label>Display Name:</label>
                                <input type="text" id="display_name" name="display_name" class="form-control dept-acl-input"  placeholder="Display Name..." value="{{$department->display_name}}">
                            </div>

                            <div class="form-group">
                                <label>Department Head:</label>
                                <select class="form-control"  id="pointPerson" name="pointPerson">
                                    <option value="-1" @if($department->parent_id == -1) selected @endif>No Assigned Point Person</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{$user->id==$department->head_id ? "selected" : "" }}>{{ $user->first_name.' '.$user->last_name . ' ('. $user->email_address.')' }}</option>
                                    @endforeach
                                </select>
                            </div>  

                            <span class="pull-right">
                                 <input type="checkbox" name="cbCheckAllProducts" id="cbCheckAllProducts"/> <label class="control-label">Check all products</label> 
                            </span>
                            
                            <h4>Products</h4>
                            <div class="form-group">
                                <label>Select products that can be viewed by this department ...</label>
                                <br>
            
                            </div>
                            <div class="row">
                                @foreach($products as $product)
                                    <div class="col-sm-3">
                                    <input type="checkbox" name="{{$product->name}}" id="{{$product->name}}" value="{{$product->id}}" class="product-cb" {{(in_array($product->id, $products_access))? "checked" : "" }}/> <label class="control-label">{{$product->name}}</label>  
                                    </div>
                                @endforeach
                            </div>
                            
                            <br>
                            <div class="form-group">
                                <label>Provide permissions for this department ...</label>

                                <span>
                                    <input class="btn btn-primary btn-sm" id="loadTemplate" type="button" value="Load Access Template" />
                                </span>
                                
                                <span class="pull-right">
                                     <input type="checkbox" name="cbCheckAllACL" id="cbCheckAllACL"/> <label class="control-label">Check all access</label> 
                                </span>
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
                                <td data-toggle="tooltip" data-html="true" title="{!! html_entity_decode($acl['description'])!!}"><b>{{$acl['name']}}</b></td>
                                <td><a href="javascript:void(0);" class="btn pull-right" onclick="showPermission({{$acl['id']}})"><i class="fa fa-plus"></i></a></td>
                            </tr>
                            <tr id="sub-{{$acl['id']}}" style="display: none;" class="sub-tr">
                                <td data-toggle="tooltip" data-html="true" title="{!! html_entity_decode($acl['description'])!!}">
                                    <b>{{$acl['name']}}</b>                                 
                                </td>
                                <td>
                                <a href="javascript:void(0);" class="btn pull-right" onclick="hidePermission({{$acl['id']}})"><i class="fa fa-minus"></i></a>
                                <div class="row">
                                   @foreach($acl['department_access'] as $access => $value)
                                   <div class="col-sm-4">
                                     <input data-toggle="tooltip" data-html="true" title="{!! html_entity_decode($value->description)!!}" type="checkbox" name="{{$value->name}}" id="acl-{{$value->id}}" value="{{$value->id}}" class="acl-cb" {{(in_array($value->id, $department_access))? "checked" : "" }}/> <label>{{str_replace('Add','Create',$value->name)}}</label>  
                                   </div>
                                   @endforeach   
                                </div>  
                                </td>
                            </tr>
                            @endforeach 
                            </tbody>
                            </table>
                            <div class="form-group">
                                <input class="btn btn-primary" type="submit" value="Save" />
                            </div>                        
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="selectTemplate" role="dialog">
                    <div class="modal-dialog" role="document" style="max-width:800px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Select Template</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered table-striped" id="tblTemplateList">
                                    <thead>
                                        <tr>
                                            <th>Template Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($templateList as $list)
                                        <tr>
                                            <td>{{$list->name}}</td>
                                            <td align="left"><input class="btn btn-success btn-sm" onclick="loadACLTemplate('{{$list->access}}')" type="button" value="Load"></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

        </section>
    </div>
@endsection
@section("script")
    <script src="{{ config("app.cdn") . "/js/admin/departments.js" . "?v=" . config("app.version") }}"></script>
    <script type="text/javascript">
        // loadPointPersonData();
    </script>

@endsection