<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ config("app.name") }} Admin | Dashboard</title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<meta id="path" name="path" content="{{ url('/') }}">
<meta id="ctx" name='ctx' content="{{ url('/') }}">
<meta id="token" name="token" content="{{ csrf_token() }}">
<meta id="cdnUrl" content="{{ config("app.cdn") }}">
<meta id="storageUrl" content="{{ \Illuminate\Support\Facades\Storage::url("/")  }}">
<meta id="appVersion" name="version" content="{{ config("app.version") }}">
<meta id="appName" content="{{ config("app.name") }}">
<meta id="appUrl" content="{{ config("app.url") }}">
<meta id="appLocale" content="{{ App::getLocale() }}">
<meta id="googleKey" content="{{ config("app.google_api_key") }}">

<link rel="shortcut icon" href="" type="image/x-icon">
<link rel="icon" href="" type="image/x-icon">

<!-- Specifying a Webpage Icon for Web Clip
<link rel="apple-touch-icon" href="touch-icon-iphone.png">
<link rel="apple-touch-icon" sizes="152x152" href="touch-icon-ipad.png">
<link rel="apple-touch-icon" sizes="180x180" href="touch-icon-iphone-retina.png">
<link rel="apple-touch-icon" sizes="167x167" href="touch-icon-ipad-retina.png">
-->

<!-- Specifying a Launch Screen Image -->
<link rel="apple-touch-startup-image" href="/launch.png">

<!-- Full Calendar -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css">
<!-- Bootstrap -->
<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- DataTable -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.2/css/select.bootstrap4.min.css">
<!-- DatePicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css"/>
<!-- Toastr notification -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">
<!-- Color Picker -->
<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css">
<!-- Theme style -->
<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/AdminLTE.min.css">
<!--
    AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load.
-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/skins/_all-skins.min.css">
<!-- Customer Style -->
<link rel="stylesheet" type="text/css" href="{{ "/css/app.css" . "?v=" . config("app.version") }}">
<!-- Bootstrap UI KIT -->
<link rel="stylesheet" type="text/css" href="{{ "/css/default.css" . "?v=" . config("app.version") }}">
<link rel="stylesheet" type="text/css" href="{{ "/css/main.css" . "?v=" . config("app.version") }}">
<!-- TimePicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
<!-- fancybox -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.css">
<link rel="stylesheet" type="text/css" href="{{ "/plugins/tokenize2.css" . "?v=" . config("app.version") }}">
<link rel="stylesheet" type="text/css" href="{{ "/plugins/bootstrap-datetimepicker.css" . "?v=" . config("app.version") }}">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.5/bluebird.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js"></script>