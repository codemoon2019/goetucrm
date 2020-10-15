@extends('layouts.app')

@section('content')
    <body onload=""> 
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Training Access
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Company Settings</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="col-md-12 secondary-header">
                <h5>Select a partner to view their information ...</h5>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <ul class="tabs-rectangular">
                    @if(count($partner_types)>0)
                        @foreach($partner_types as $partner_type)
                            <li class="{{$partner_type->name==='ISO' ? "active" : "" }}"><a href="#" id="{{$partner_type->id}}">{{$partner_type->name}}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>
            <form role="form" action="{{ url("/admin/company_settings/$id/training_update") }}"  enctype="multipart/form-data" method="POST">
            <input type = "hidden" id="training_access" name="training_access"/>
            {{ csrf_field() }}
            @if(count($partner_details)>0)
                @foreach($partner_details as $partner_detail)
                    <div id="{{$partner_detail['id']}}Container" class="{{$partner_detail['name']==='ISO' ? "" : "hide" }}">
                        <table id="tblTraining" name="tblTraining" class="table table-bordered table-striped"> 
                            <thead>
                                <tr>
                                    <th width="30%">Training Modules</th>
                                    <th width="70%">Permissions</th>
                                </tr>
                            </thead>
                            <tbody>   
                            @foreach($new_trainings as $training)
                            <tr>
                                <td>
                                    {{$training['name']}}
                                </td>
                                <td>
                                <div class="row">
                                   @foreach($training['modules'] as $access => $value)
                                   <div class="col-sm-4">
                                     <input type="checkbox" name="{{$value->name}}" id="{{$value->name}}" value="{{$partner_detail['id']}}-{{$training['id']}}-{{$value->module_code}}" class="training-cb" {{(in_array($training['id'].'-'.$value->module_code, $partner_detail['training_access']))? "checked" : "" }}/> <label>{{$value->name}}</label>  
                                   </div>
                                   @endforeach   
                                </div>  
                                </td>
                            </tr>
                            @endforeach 
                            </tbody>
                        </table>
                    </div>
                    
                 @endforeach
            @endif
            <div class="form-group">
                <input class="btn btn-primary" type="submit" value="Save" />
            </div>
            </form>
        </section>
    </div>
    </body>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/admin/companysettings.js" . "?v=" . config("app.version") }}"></script>
@endsection