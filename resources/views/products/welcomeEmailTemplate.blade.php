@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- @include('incs.messages') -->
        <section class="content-header">
            <h1>
                {{$headername}}
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ url('products/listTemplate#welcome-email') }}">Templates</a></li>
                <li class="active">{{$headername}}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <form role="form" name="frmWelcomeEmail" id="frmWelcomeEmail" method="post" enctype="multipart/form-data" action="{{$formUrl}}">
                {{ csrf_field() }}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="emailTemplateTitle"><strong>Title</strong></label>
                        <input type="text" class="form-control" name="emailTemplateTitle" id="emailTemplateTitle" value="{{$data->name or old('emailTemplateTitle') }}" placeholder="Insert Title Here" @if($viewOnly) style="pointer-events: none;" @endif>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="emailTemplateProduct"><strong>Product</strong></label>
                        <select name="emailTemplateProduct" id="emailTemplateProduct" class="form-control" @if($viewOnly) style="pointer-events: none;" @endif>
                            @foreach ($productList as $list)
                            <option value="{{$list->id}}" @if(isset($data) && $data->product_id == $list->id) selected @endif >{{$list->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <textarea class="form-control" id="emailContent" name="emailContent" @if($viewOnly) disabled @endif>{{$data->description or old('emailContent')}}</textarea>
            </div>
                <a href="{{ url('products/listTemplate#welcome-email') }}" class="btn btn-primary pull-left">Back to List</a>
                @if(!$viewOnly)<input type="submit" class="btn btn-primary pull-right" value="Save">@endif
        </section>
    </div>
@endsection

@section("script")
    <script src="{{ config("app.cdn") . "/js/products/templates.js" . "?v=" . config("app.version") }}"></script>
    <script>
    $( document ).ready(function() {
        CKEDITOR.replace('emailContent');
    });
    </script>

@endsection