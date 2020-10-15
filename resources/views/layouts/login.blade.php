<!doctype html>

<html lang="{{ app()->getLocale() }}">
<head>
    @yield("title")
    <title>{{ config("app.name") }}</title>
    @include("incs.head")
    @yield("style")
    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
    </script>
    @yield("headerScript")
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">
    @yield("content")
</div>
@yield("script")
</body>
</html>