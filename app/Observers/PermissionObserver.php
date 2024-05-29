<?php

namespace App\Observers;

use App\Models\Module;
use App\Models\Permission;

class PermissionObserver
{
    /**
     * Handle the permission "created" event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function created(Permission $permission)
    {
        $module = Module::find($permission->module_id);
        $action = "Permission Created: ".$module->name." - ".$permission->access;
        saveLogs($action, $permission);
    }

    /**
     * Handle the permission "updated" event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function updated(Permission $permission)
    {
        $module = Module::find($permission->module_id);
        $action = "Permission Updated: ".$module->name." - ".$permission->access;
        saveLogs($action, $permission);
    }

    /**
     * Handle the permission "deleted" event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function deleted(Permission $permission)
    {
        $module = Module::find($permission->module_id);
        $action = "Permission Deleted: ".$module->name." - ".$permission->access;
        saveLogs($action, $permission);
    }

    /**
     * Handle the permission "restored" event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function restored(Permission $permission)
    {
        $module = Module::find($permission->module_id);
        $action = "Permission Restored: ".$module->name." - ".$permission->access;
        saveLogs($action, $permission);
    }

    /**
     * Handle the permission "force deleted" event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function forceDeleted(Permission $permission)
    {
        $module = Module::find($permission->module_id);
        $action = "Permission Force Deleted: ".$module->name." - ".$permission->access;
        saveLogs($action, $permission);
    } 
}
