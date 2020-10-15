@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- @include('incs.messages') -->
        <section class="content-header">
            <h1>
                {{ $headername }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ url('tickets/reply-template') }}">Reply Templates</a></li>
                <li class="active">{{ $headername }}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <form role="form" name="frmWelcomeEmail" id="frmWelcomeEmail" method="post" enctype="multipart/form-data" action="{{ $formUrl }}">
                {{ csrf_field() }}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="emailTemplateTitle"><strong>Name</strong></label>
                        <input type="text" class="form-control" name="emailTemplateTitle" id="emailTemplateTitle" value="{{$data->name or old('emailTemplateTitle') }}" placeholder="Insert Template Name Here">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <textarea class="form-control" id="emailContent" name="emailContent" >{{$data->value or old('emailContent')}}</textarea>
            </div>
                <a href="{{ url('tickets/reply-template') }}" class="btn btn-primary pull-left">Back to List</a>
                <input type="submit" class="btn btn-primary pull-right" value="Save">
        </section>
    </div>
@endsection

@section("script")
    <script>
    $( document ).ready(function() {
        CKEDITOR.replace('emailContent');
    });
    </script>

@endsection