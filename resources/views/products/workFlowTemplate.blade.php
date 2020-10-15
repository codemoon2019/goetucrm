@extends('layouts.app')

@section('content')

    <link rel="stylesheet" type="text/css" href="{{ "/css/tokenize2.css" . "?v=" . config("app.version") }}">
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                {{$headername}}
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ url('products/listTemplate#workflow') }}">Templates</a></li>
                <li class="active">{{$headername}}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            @if(!$viewOnly) 
            <form id="frmWorkflowTemplate" name="frmWorkflowTemplate"  method="post" enctype="multipart/form-data" action="{{$formUrl}}">
                {{ csrf_field() }}
            <input type="hidden" id="txtDetailList" name="txtDetailList">
            <input type="hidden" id="txtDaysToCompleteH" name="txtDaysToCompleteH" value="{{$data->days_to_complete or 0}}">
            <input type="hidden" id="txtSubTaskID" name="txtSubTaskID" value="{{$data->id or -1 }}">
            @endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="workFlowTemplateName"><strong>Template Name</strong></label>
                        <input type="text" class="form-control" name="workFlowTemplateName" id="workFlowTemplateName" value="{{$data->name or old('workFlowTemplateName') }}"  @if($viewOnly) style="pointer-events: none;" @endif>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="workFlowTemplateMainTask"><strong>Main Task</strong></label>
                        <input type="text" class="form-control" name="workFlowTemplateMainTask" id="workFlowTemplateMainTask" value="{{$data->remarks or old('workFlowTemplateMainTask') }}"  @if($viewOnly) style="pointer-events: none;" @endif>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="workFlowTemplateDescription"><strong>Description:</strong></label>
                <textarea id="workFlowTemplateDescription" name="workFlowTemplateDescription" class="form-control"  @if($viewOnly) style="pointer-events: none;" @endif>{{$data->description or old('workFlowTemplateDescription') }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtProduct"><strong>Select Product</strong></label>
                        <select name="txtProduct" id="txtProduct" class="form-control"  @if($viewOnly) style="pointer-events: none;" @endif>
                            @php $selectedProductCompanyId = null @endphp
                        
                            @foreach ($productList as $list)
                                <option data-company_id="{{ $list->company_id }}" value="{{ $list->id }}" {{ isset($data) && $data->product_id == $list->id ? 'selected' : '' }}>
                                    {{ $list->name }}
                                </option>

                                @if (isset($data) && $data->product_id == $list->id)
                                    @php $selectedProductCompanyId = $list->company_id @endphp
                                @endif 
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>  

            <div class="col-md-12">
                <div id="subtask-wrapper" @if($viewOnly) style="pointer-events: none;" @endif>
                    @if(isset($data))    
                        @foreach ($data->details as $item)

                            <div class="subtask" id="subtask{{$item->line_number}}">
                                <div class="row bordered-row subtaskborder" id="subtaskborder{{$item->line_number}}">
                                    <div class="col-md-1 text-right left-action">
                                        <h5 class="subtasknum">#{{$item->line_number}}</h5>
                                        <a href="#" class="btnSortSubtask text-blue" title="Sort Subtask"><i class="fa fa-sort"></i> Sort</a><br>
                                        <a href="#" class="btnDelSubtask text-red" data-subid="subtask{{$item->line_number}}" title="Delete Subtask"><i class="fa fa-minus-circle"></i> Delete</a>
                                    </div>
                                    <div class="col-md-11">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <label class="input-group-addon">Assignment:</label>
                                                <input type="text" class="form-control subTaskName" id="txtSubTaskName-{{$item->line_number}}" name="txtSubTaskName-{{$item->line_number}}" value="{{$item->name}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 right-assignment">
                                                <div class="form-group">
                                                    <label>Departments:</label>
                                                    <select name="department_id" class="form-control departments" id="departments-task-{{ $item->line_number }}">
                                                        <option value="-1">--Select Department--</option>

                                                        @foreach ($departments as $department)
                                                            @if ($selectedProductCompanyId == $department->company_id)
                                                                <option value="{{ $department->id }}" {{ $item->department_id == $department->id ? 'selected' : ''}}>
                                                                    {{ $department->description }}
                                                                </option>
                                                            @endif
                                                        @endforeach                                 
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Sub-Products:</label>
                                                    <select class="form-control subproducts" id="subproductTask{{$item->line_number}}" multiple>
                                                        @foreach ($sub_product as $prod)
                                                            <option value="{{$prod->id}}" 
                                                            @foreach ($item->product_tags as $product_tags)
                                                                @if($product_tags == $prod->id) 
                                                                    selected 
                                                                @endif
                                                            @endforeach
                                                                >{{$prod->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Days To Complete:</label>
                                                <input type="hidden" id="prerequisite{{$item->line_number}}" name="prerequisite{{$item->line_number}}" value="{{$item->prerequisite}}">
                                                <input type="text" class="form-control daysToCompleteDetail" id="txtDaysToCompleteDetail-{{$item->line_number}}" name="txtDaysToCompleteDetail-{{$item->line_number}}" onkeypress="validate_numeric_input(event);" value="{{$item->days_to_complete}}">
                                            </div>
                                            <div class="col-md-4 subtasklink">
                                                <label>Starts after assignment:</label>
                                                <select class="form-control startsubtask" id="txtSubTaskLink-{{$item->line_number}}"  name="txtSubTaskLink-{{$item->line_number}}" data-lineno="{{$item->line_number}}"></select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Upon Completion or Start:</label>
                                                <select class="form-control subTaskLinkText" id="txtSubTaskLinkText-{{$item->line_number}}"  name="txtSubTaskLinkText-{{$item->line_number}}">
                                                    <option selected></option>
                                                    <option @if($item->link_condition == "Completion") selected @endif>Completion</option>
                                                    <option @if($item->link_condition == "Due Date") selected @endif>Due Date</option>
                                                    <option @if($item->link_condition == "Start") selected @endif>Start</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    @endif
                </div>
            </div>

            @if(!$viewOnly)
            <div class="row">
                <div class="col-md-6">
                    <!-- <a href="#" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Create Assignment</a> -->
                    <input type="button" class="btn btn-primary pull-left" id="btnAddSubtask" value="Add Assignment">
                </div>
                <div class="col-md-6">
                    <!-- <a href="#" class="btn btn-success pull-right">Submit</a> -->
                    <input type="submit" class="btn btn-primary pull-right" id="btnSaveTemplate" value="Save Template">
                </div>
            </div>
            </form>
            @endif
        </div>
    </section>

@endsection

@section("script")
    <script>
        var departments = @json($departments);
    </script>

    <script src="{{ config("app.cdn") . "/js/products/templates.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/products/workflow.js" . "?v=" . config("app.version") }}"></script>
    <script src="{{ config("app.cdn") . "/js/tokenize2.js" . "?v=" . config("app.version") }}"></script>

    <script>
        $('.assignees').tokenize2({searchFromStart: false});
        $('.subproducts').tokenize2({searchFromStart: false});
        @if(!isset($data))
            $('#txtProduct').trigger('change'); 
        @endif
    </script>
@endsection

    