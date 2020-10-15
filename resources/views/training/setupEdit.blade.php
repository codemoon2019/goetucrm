@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                {{$label}} Training
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li><a href="/training/setup">Training Setup</a></li>
                <li class="active">{{$label}}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <form role="form" method="post" id="frmTrainingModule" name="frmTrainingModule" action="{{$postUrl}}">
            {{ csrf_field() }}
            <input type="hidden" id="txtModuleList" name="txtModuleList" value="">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" class="form-control" id="training_name" name="training_name" value="{{  $training->name or old('training_name')}}" />
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <input type="text" class="form-control" id="training_desc" name="training_desc" value="{{  $training->description or old('training_desc')}}" />
                    </div>
                    <div class="form-group">
                        <label>Product:</label>
                        <select class="form-control" id="training_product" name="training_product">
                            @foreach($products as $product)
                            <option value="{{$product->id}}" @if(isset($training) && $product->id == $training->product_id) selected @endif>{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Modules:</label>
                        <table id="module_list" class="table table-bordered table-condensed text-center">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Code</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php ($ctr = 0)
                                @if($label == 'Edit')
                                    @foreach($training->modules as $module)
                                    @php ($ctr++)
                                    <tr id="lineNo{{$ctr}}">
                                        <td><input data-id="{{$ctr}}" type="text" id="trainingModuleName{{$ctr}}" name="trainingModuleName" class="form-control trainingModuleName" value="{{$module->name}}"></td>
                                        <td><input data-id="{{$ctr}}" type="text" id="trainingModuleDesc{{$ctr}}" name="trainingModuleDesc" class="form-control trainingModuleDesc" value="{{$module->description}}"></td>
                                        <td><input data-id="{{$ctr}}" type="text" id="trainingModuleCode{{$ctr}}" name="trainingModuleCode" class="form-control trainingModuleCode" value="{{$module->module_code}}"></td>
                                        <td><a href="javascript:void(0)" class="required trainingModuleDelete" id="trainingModuleDelete{{$ctr}}" onclick = "TrainingModuleDeleteLine({{$ctr}})"><i class="fa fa-minus-circle fa-2x"></i></a></td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <input type="hidden" id="txtTrainingModuleCount" name="txtTrainingModuleCount" value="{{$ctr}}">
                        <a href="javascript:void(0)" id="addTrainingModule" class="pull-right"><i class="fa fa-plus-circle"></i>&nbsp; Add New Module</a>
                        <input type="submit" class="btn btn-primary" value="Save Training">
                    </div>
                </div>
            </div>
            </form>
        </section>
    </div>
@endsection
@section('script')
    <script>
        $('.datatables').dataTable();
        $(document).ready(function() {

            $('#addTrainingModule').click(function (){
                var counter = $('#txtTrainingModuleCount').val();
                counter++;
                var newTextBoxDiv = $(document.createElement('tr'))
                     .attr("id", 'lineNo' + counter);

                newTextBoxDiv.after().html('<td><input data-id="' + counter  + '" class="form-control trainingModuleName" style="height: 36px;" name="trainingModuleName" id="trainingModuleName' + counter  + '" value=""></td>' +
                '<td><input type="text" class="form-control" name="trainingModuleDesc" id="trainingModuleDesc' + counter  + '" value=""></td>' +
                '<td><input type="text" class="form-control" name="trainingModuleCode" id="trainingModuleCode' + counter  + '" value=""></td>' +
                '<td><a href="javascript:void(0)" class="required trainingModuleDelete" onclick = "TrainingModuleDeleteLine('+ counter +')" id="trainingModuleDelete' + counter  + '"> <i class="fa fa-minus-circle fa-2x"></i></a></td>' 
                );

                newTextBoxDiv.appendTo("#module_list");

                $('#txtTrainingModuleCount').val(counter);
            });


            $('#frmTrainingModule').submit(function (){
                var details = [];
                var name = ""; 
                var description = "";
                var code = "";
                var stop = false;
                var hasItem = false;

                if ($("#training_name").val() == "")
                {
                    alert("Please input the Name");
                    return false;
                }

                if ($("#training_desc").val() == "")
                {
                    alert("Please input the Description");
                    return false;
                }

                $('#module_list > tbody  > tr > td > input').each(function() {
                    if ($(this).attr('name') == "trainingModuleName")
                    {
                        if ($(this).val() == "")
                        {
                            alert("Please fill up all the Module Name");
                            stop = true;
                            return false;
                        }
                        name = $(this).val();
                    }

                    if ($(this).attr('name') == "trainingModuleDesc")
                    {
                        if ($(this).val() == "")
                        {
                            alert("Please fill up all the Module Description");
                            stop = true;
                            return false;
                        }
                        description = $(this).val();
                    }

                    if ($(this).attr('name') == "trainingModuleCode")
                    {
                        if ($(this).val() == "")
                        {
                            alert("Please fill up all the Module Code");
                            stop = true;
                            return false;
                        }
                        code = $(this).val();
                    }

                    if (name != "" && description != "" && code != "")
                    {
                          hasItem = true;
                          details.push({name: name, description: description , code: code});
                          name = "";  description = ""; code = "";
                    }

                });

                if (stop)
                {
                    return false;
                }

                if(!hasItem)
                {
                    alert("Please input at least one module");
                     return false;
                }

                $('#txtModuleList').val(JSON.stringify(details));
                return true;

            });

        });

        function TrainingModuleDeleteLine($id)
        {
            $("#lineNo" + $id).remove();
        }

    </script>
@endsection