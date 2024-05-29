<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Role;

class RoleObserver
{
    /**
     * Handle the role "created" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function created(Role $role)
    {
        $action = "Role Created: ".$role->name;
        saveLogs($action, $role);
    }

    /**
     * Handle the role "updated" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function updated(Role $role)
    {
        $action = "Role Updated: ".$role->name;
        saveLogs($action, $role);
    }

    /**
     * Handle the role "deleted" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function deleted(Role $role)
    {
        $action = "Role Deleted: ".$role->name;
        saveLogs($action, $role);
    }

    /**
     * Handle the role "restored" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function restored(Role $role)
    {
        $action = "Role Restored: ".$role->name;
        saveLogs($action, $role);
    }

    /**
     * Handle the role "force deleted" event.
     *
     * @param  \App\Models\Role  $role
     * @return void
     */
    public function forceDeleted(Role $role)
    {
        $action = "Role Force Deleted: ".$role->name;
        saveLogs($action, $role);
    }

}
