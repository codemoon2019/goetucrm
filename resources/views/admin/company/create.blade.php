@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ url("/admin/company") }}">
                        <i class="fa fa-list"></i>
                        &nbsp;&nbsp;{{ __("model.company.list") }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <i class="fa fa-pencil"></i>
                    &nbsp;&nbsp;{{ __("model.company.create") }}
                </li>
            </ol>
        </nav>

        <div class="content">
            <!-- Sku Attribute Edit -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __("model.company.create") }}</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <form role="form" action="{{ url("/admin/company") }}"  enctype="multipart/form-data" method="POST">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="row">
                            <!-- Company name -->
                            <div class="col-md-4">
                                <div class="form-group has-feedback {{ $errors->has("name") ? "has-error" : "" }}">
                                    <label for="title">
                                        {{ __("model.company.name") }} <span class="text-red">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="name"
                                           name="name"
                                           placeholder="{{ __("model.company.name") }}"
                                    >
                                    @if($errors->has("name"))
                                        <span class="error-msg text-danger">&nbsp;{{ $errors->first("name") }}</span>
                                    @endif
                                </div>
                            </div>
                            <!-- Powered by link -->
                            <div class="col-md-4">
                                <div class="form-group has-feedback {{ $errors->has("powered_by_link") ? "has-error" : "" }}">
                                    <label for="title">
                                        {{ __("model.company.poweredByLink") }} <span class="text-red">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="powered_by_link"
                                           name="powered_by_link"
                                           placeholder="{{ __("model.company.poweredByLink") }}"
                                    >

                                    @if($errors->has("powered_by_link"))
                                        <span class="error-msg text-danger">&nbsp;{{ $errors->first("powered_by_link") }}</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <!-- Logo -->
                                <div class="fileinput text-center fileinput-new"
                                     data-provides="fileinput"
                                >
                                    <div class="fileinput-new thumbnail img-raised">
                                        <img width="200" class="img-responsive"
                                             src="{{ \Illuminate\Support\Facades\Storage::url("images/image_placeholder.jpg") }}"
                                        >
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail img-raised">
                                            <img src="" />
                                            <input type="hidden"
                                                   name="company_logo_path_value"
                                                   value=""
                                            >
                                    </div>
                                    <div>
                                        <span class="btn btn-raised btn-round btn-default btn-file">
                                            <span class="fileinput-new">{{ __("common.selectImage") }}</span>
                                            <span class="fileinput-exists">{{ __("common.change") }}</span>
                                            <input name="company_logo_path" type="file">
                                        </span>
                                        <a class="btn btn-danger btn-round fileinput-exists"
                                           data-dismiss="fileinput">
                                            <i class="now-ui-icons ui-1_simple-remove"></i> {{ __("common.remove") }}
                                        </a>
                                    </div>

                                    @if($errors->has("company_logo_path"))
                                        <span class="error-msg text-danger">&nbsp;{{ $errors->first("company_logo_path") }}</span>
                                    @endif
                                </div>
                                <!-- End logo -->
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary text-uppercase">{{ __("common.submit") }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
