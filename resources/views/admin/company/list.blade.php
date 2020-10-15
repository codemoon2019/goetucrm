@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <nav class="pull-right" aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ url("/admin") }}">
                        {{ __("common.dashboard") }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    {{ __("model.company.list") }}
                </li>
            </ol>
        </nav>
        <div class="content">
            <div id="companyTable"></div>
        </div>
    </div>
@endsection

@section("script")
    <script src="{{ config("app.cdn") . "/js/admin/company/list.js" . "?v=" . config("app.version") }}"></script>
@endsection
