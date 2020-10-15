<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     * Init setting of navigator.
     *
     * @param null $module
     * @param null $menu
     */
    protected function initSetting($module = null, $menu = null)
    {
        if (isset($module)) {
            request()->session()->flash("module", $module);
        }
        if (isset($menu)) {
            request()->session()->flash("menu", $menu);
        }
    }
}
