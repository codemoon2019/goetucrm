<?php

namespace App\Providers;

use App\Models\Access;
use App\Models\UserType;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /**
         * ---------------------------------
         *        Custom Directives
         * ---------------------------------
         * 
         * Boot your custom directives below
         * 
         */
        Blade::directive('cdn', function ($src) {
            $configCdn = config('app.cdn');
            $configVersion = config('app.version');
            $src = str_replace("'", "", $src);

            return "<?php echo '\"{$configCdn}{$src}\"' ?>";
        });


        /**
         * ------------------------------------
         *         Custom If Statements
         * ------------------------------------
         * 
         * Boot your custom if statements below
         * 
         */
        Blade::if('hasAccess', function ($moduleName, $moduleAccess, $departmentId=null) {
            $result = false;
            if (isset($departmentId)) {
                $userType = UserType::find($departmentId);
                $isTeamLead = isset($userType->departmentHead) && 
                    $userType->departmentHead->id == auth()->user()->id;

                $result = $result || $isTeamLead;
            }

            return $result || Access::hasPageAccess($moduleName, $moduleAccess, true);
        });
    }

    public function register()
    {
        //
    }
}
