@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Access Templates
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/group-templates">Access Templates</a></li>
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
                        <form role="form" action="{{ url("/admin/group-templates/$template->id/update") }}"  enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}
                        <input type = "hidden" id="_method" name="_method" value="PUT" />
                        <input type = "hidden" id="access" name="access"/>            
                        <div class="row">
                            <div class=" col-md-6 form-group">
                                <label>Name:</label>
                                <input type="text" id="name" name="name" class="form-control dept-acl-input"  placeholder="Template Name..." value="{{$template->name}}">
                            </div>

                            <div class="col-md-6 form-group" >
                                <label>Company:</label>
                                <select class="form-control"  id="company" name="company">
                                    @if($is_admin)
                                    <option value="-1">ALL COMPANY</option>
                                    @endif
                                    @foreach($companies as $c)
                                        <option value="{{ $c->id }}" @if($c->id == $template->company_id) selected @endif>{{ $c->partner_company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div> 
                        </div>


                        <div class="form-group">
                            <label>Provide permissions for this department ...</label>
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
                               @foreach($acl['group_access'] as $access => $value)
                               <div class="col-sm-4">
                                 <input data-toggle="tooltip" data-html="true" title="{!! html_entity_decode($value->description)!!}" type="checkbox" name="{{$value->name}}" id="{{$value->name}}" value="{{$value->id}}" class="acl-cb" {{(in_array($value->id, $department_access))? "checked" : "" }}/> <label>{{str_replace('Add','Create',$value->name)}}</label>  
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

        </section>
    </div>
@endsection
@section("script")
<script type="text/javascript">

    $('.acl-cb').change(function (){
      var access='';
      $('.acl-cb:checkbox:checked').each(function () {
           access = access +  $(this).val() + ",";
      });
      $('input[name="access"]').val(access);
    });
    $('.acl-cb').trigger('change');
    $("#cbCheckAllACL").click(function(){
      $('.acl-cb').not(this).prop('checked', this.checked);
      var access='';
      $('.acl-cb:checkbox:checked').each(function () {
           access = access +  $(this).val() + ",";
      });
      $('input[name="access"]').val(access);
    });
    
    function showPermission(id){
      $('#sub-'+id).show();
      $('#main-'+id).hide();
    }

    function hidePermission(id){
      $('#sub-'+id).hide();
      $('#main-'+id).show();            
    }

    function collapsePermission(){
      if($('#showAll').prop('checked')){
          $('.sub-tr').show();
          $('.main-tr').hide();  
      }else{
          $('.sub-tr').hide();
          $('.main-tr').show();  
      }   
    }
</script>
@endsection