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
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script> -->
<script src="https://www.w3resource.com/jquery-plugins/jquery.maskedinput-master/jquery.maskedinput.min.js" type="text/javascript"></script>
<!-- fancybox -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jSignature/2.1.2/flashcanvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jSignature/2.1.2/jSignature.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.4/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.4/firebase-database.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.5.4/firebase-storage.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timeago/1.6.3/jquery.timeago.js" type="text/javascript"></script>
<script src="{{ config("app.cdn") . "/js/firebase/init.js" . "?v=" . config("app.version") }}"></script>
<!-- <script src="{{ config("app.cdn") . "/js/chat/chat.js" . "?v=" . config("app.version") }}"></script> -->


<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/js/adminlte.min.js"></script>
<script src="{{ config("app.cdn") . "/plugins/tokenize2.js" . "?v=" . config("app.version") }}"></script>
<script src="{{ config("app.cdn") . "/plugins/bootstrap-datetimepicker.min.js" . "?v=" . config("app.version") }}"></script>
<script src="{{ config(' app.cdn ') . '/js/extras/suggestion.js' . '?v=' . config(' app.version ') }}"></script>
<script src="{{ config(' app.cdn ') . '/js/extras/changeCompany.js' . '?v=' . config(' app.version ') }}"></script>
<input type="hidden" id="url_chat" value="{{ config("app.cdn") . "/js/chat/chat.js" . "?v=" . config("app.version") }}">