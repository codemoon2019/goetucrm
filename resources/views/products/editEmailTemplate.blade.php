@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Edit Welcome Template
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="#">Welcome Email</a></li>
                <li class="active">Welcome Email Editor</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>
        <!-- Main content -->
        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="emailTemplateTitle"><strong>Title</strong></label>
                        <input type="text" class="form-control" name="emailTemplateTitle" id="emailTemplateTitle" value="" placeholder="Insert Title Here">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="emailTemplateProduct"><strong>Product</strong></label>
                        <select name="emailTemplateProduct" id="emailTemplateProduct" class="form-control">
                            <option>Go3 Rewards Bundle</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <textarea class="form-control" id="editor1"></textarea>
            </div>
            <a href="#" class="btn btn-primary pull-right">Save</a>
        </section>
    </div>
@endsection

@section("script")
    <script src="{{ config("app.cdn") . "/js/products/editEmailTemplate.js" . "?v=" . config("app.version") }}"></script>
@endsection