<!doctype html>

<html lang="{{ app()->getLocale() }}">

<head>
    @yield("title")
    <title>{{ config("app.name") }}</title>
    @include("incs.head")
    @yield("style")
    <script>
        window.Laravel {!! json_encode(['csrfToken' => csrf_token()]) !!};
    </script>
    @yield("headerScript")
</head>

<body class="hold-transition skin-red sidebar-mini">
    <div class="wrapper">
        <div class="content-wrapper" style="margin-left: 0; overflow: hidden">
            <section class="content-header">
                <h1>

                </h1>
                {{--<div class="dotted-hr"></div>--}}
            </section>
            <!-- Main content -->
            <section class="content container-fluid">
                    {{ csrf_field() }}
                    <input type="hidden" name="txtSignCode" id="txtSignCode" value="{{ $sign_code }}"/>
                    <input type="hidden" name="txtPartnerId" id="txtPartnerId" value="{{ $partner_id }}"/>
                    <div class="row">
                        <embed class="appsign-pdf" width="100%" height="100%" src="{{$confirmUrl}}">
                        <div class="appsign-panel">
                            <div class="appsign-main">
                                <button type="submit" id="btnConfirm" name="btnConfirm" class="btn btn-primary btn-save">Confirm</button>
                                <button type="button" id="btnDecline" name="btnDecline" class="pull-right btn btn-primary btn-close">Decline</button>
                            </div>
                        </div>
                    </div>
            </section>
            <!-- /.content -->
        </div>

</body>
@include("incs.foot")
<script type="text/javascript">
    $('#btnConfirm').click(function () {
        var sign_code = $('#txtSignCode').val();
        var partner_id = $('#txtPartnerId').val();
        if(confirm('By confirming, you accept the agreement.')) {
            window.location.href = '/appsign/' + sign_code + '/sign';
        } else {
            window.location.href = '/merchants/details/' + partner_id + '/products';
        }
    });
    $('#btnDecline').click(function () {
        var id = $('#txtOrderId').val();
        var partner_id = $('#txtPartnerId').val();
        window.location.href = '/merchants/details/' + partner_id + '/products';
    });
</script>

</html>