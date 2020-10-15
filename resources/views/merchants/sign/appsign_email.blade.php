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
            <form role="form" method="post" name id="frmSignature" name="frmSignature" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="txtImage" id="txtImage" />
                <div class="row">
                    <embed class="appsign-pdf" width="100%" height="100%" src="{{$pdfUrl}}">
                    <div class="appsign-panel">

                        <div class="appsign-panel-toggle">
                            <a id="add-sign" class="btn btn-primary btn-save">Add Signature</a>
                        </div>

                        <div class="appsign-main hide">
                            <div class="form-group">
                                <label>&nbsp;&nbsp;Signature:</label>
                                <a href="#" class="close-sign-panel pull-right"><i class="fa fa-close"></i></a>
                                <div id="signature"></div>
                            </div>
                            <button type="submit" id="btnConfirmSign" name="btnConfirmSign" class="btn btn-primary btn-save">Confirm Signature</button>
                            <button type="button" id="btnClearSign" name="btnClearSign" class="pull-right btn btn-primary btn-close">Clear</button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <!-- /.content -->
    </div>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- Bootstrap need popper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
<!-- Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
<!-- Moment -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.2/moment.min.js"></script>
<!-- Bootstrap Select -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
<!-- DataTables -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<!-- CKEditor 5 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.9.2/ckeditor.js"></script>
<!-- FileInput -->
<script src="{{ config("app.cdn") . "/plugins/jasny-bootstrap.min.js" . "?v=" . config("app.version") }}"></script>
<!-- Color picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>
<!-- Date Picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
<!-- AdminLTE App -->
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/js/app.js"></script>--}}
<!-- App JS -->
<script src="{{ config("app.cdn") . "/js/_all.js" . "?v=" . config("app.version") }}"></script>
<!-- Full Calendar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
<!-- Google API JS -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config("app.google_api_key") }}&libraries=places"></script>
<!-- Entity Picker -->
<script src="{{ config("app.cdn") . "/plugins/entitypicker/entitypicker.js" . "?v=" . config("app.version") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
<!-- fancybox -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jSignature/2.1.2/flashcanvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jSignature/2.1.2/jSignature.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.4/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.4/firebase-database.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.4/firebase-storage.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timeago/1.6.3/jquery.timeago.js" type="text/javascript"></script>
<script src="{{ config("app.cdn") . "/js/firebase/init.js" . "?v=" . config("app.version") }}"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script>
<script src="{{ config("app.cdn") . "/plugins/tokenize2.js" . "?v=" . config("app.version") }}"></script>
<script src="{{ config("app.cdn") . "/plugins/bootstrap-datetimepicker.min.js" . "?v=" . config("app.version") }}"></script>
 <script type="text/javascript">
     var loadJSignature = function() {
         $("#signature").jSignature({
             color:"#1e282c",
             lineWidth:5,
         });
     }

     var clearJSignature = function(){
         $('#btnClearSign').on('click', function (e) {
             $("#signature").jSignature("clear");
         });
     }

     var submitJSignature = function(){
         $('#frmSignature').on('submit', function (e) {
             if($("#signature").jSignature('getData', 'native').length == 0) {
                 alert('Please Enter Signature..');
                 return false;
             }
             data = $("#signature").jSignature("getData");
             $("#txtImage").val(data);
             alert('Order Signed!');
         });
     }

     $(document).ready(function() {

        $('#add-sign').click(function(){
            if($('.appsign-main').hasClass('hide')){
                $('.appsign-main').removeClass('hide');
                $(this).addClass('hide');

                $('.appsign-panel').css('width', '100%');

                // initialize JSignature after
                // clicking add signature button
                loadJSignature();
                clearJSignature();
                submitJSignature();
            }
        });

        $('.close-sign-panel').click(function(e){
            if(!$('.appsign-main').hasClass('hide')) {
                $('#add-sign').removeClass('hide');
                $('.appsign-main').addClass('hide');

                $('.appsign-panel').removeAttr('style');
                $("#signature").jSignature("destroy");
            }
            e.preventDefault();
        });

     });
 </script>
</html>