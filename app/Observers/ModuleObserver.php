<?php

namespace App\Observers;

use App\Models\Module;

class ModuleObserver
{
    /**
     * Handle the module "created" event.
     *
     * @param  \App\Models\Module  $module
     * @return void
     */
    public function created(Module $module)
    {
        $action = "Module Created: ".$module->name;
        saveLogs($action, $module);
    }

    /**
     * Handle the module "updated" event.
     *
     * @param  \App\Models\Module  $module
     * @return void
     */
    public function updated(Module $module)
    {
        $action = "Module Updated: ".$module->name;
        saveLogs($action, $module);
    }

    /**
     * Handle the module "deleted" event.
     *
     * @param  \App\Models\Module  $module
     * @return void
     */
    public function deleted(Module $module)
    {
        $action = "Module Deleted: ".$module->name;
        saveLogs($action, $module);
    }

    /**
     * Handle the module "restored" event.
     *
     * @param  \App\Models\Module  $module
     * @return void
     */
    public function restored(Module $module)
    {
        $action = "Module Restored: ".$module->name; 
        saveLogs($action, $module);
    }

    /**
     * Handle the module "force deleted" event.
     *
     * @param  \App\Models\Module  $module
     * @return void
     */
    public function forceDeleted(Module $module)
    {
        $action = "Module Force Deleted: ".$module->name;
        saveLogs($action, $module);
    }
}
