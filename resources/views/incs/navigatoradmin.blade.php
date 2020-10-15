<?php
use Illuminate\Support\Facades\Auth;

//$module = session("module");
$module = \App\Contracts\Constant::MODULE_ADMIN_COMPANY;
?>
<li class="header text-uppercase">{{ __("common.adminTitle") }}</li>
<!-- Company management -->
<li class="{{ \App\Contracts\Constant::MODULE_ADMIN_COMPANY == $module ? "active" : "" }}">
    <a href="{{ url("/admin/company") }}">
        <i class="fa fa-trademark" aria-hidden="true"></i>
        <span>{{ __("model.company.manage") }}</span>
    </a>
</li>
<!-- End Company Management -->