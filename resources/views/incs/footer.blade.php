<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> {{ config("app.version") }}
    </div>
    <strong>Copyright &copy; 2017- {{\Carbon\Carbon::now()->format('Y')}} <span style="color:#79a8c4">{{ config("app.name") }}</span>.</strong> All rights
    reserved.
</footer>