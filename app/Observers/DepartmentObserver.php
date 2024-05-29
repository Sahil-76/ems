<?php

namespace App\Observers;

use App\Models\Department;



class DepartmentObserver
{
    /**
     * Handle the department "created" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function created(Department $department)
    {
       $action = "Department Created: ".$department->name;
       saveLogs( $action,$department);

    }

    /**
     * Handle the department "updated" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function updated(Department $department)
    {
        $action = "Department Updated: ".$department->name;
    
        saveLogs( $action,$department);
    }

    /**
     * Handle the department "deleted" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function deleted(Department $department)
    {
        $action = "Department Deleted: ".$department->name;
    
        saveLogs( $action,$department);
    }

    /**
     * Handle the department "restored" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function restored(Department $department)
    {
        $action = "Department Restored: ".$department->name;
    
      saveLogs($action,$department);
    }

    /**
     * Handle the department "force deleted" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function forceDeleted(Department $department)
    {
        $action = "Department Permanently Deleted: ".$department->name;
    
       saveLogs($action,$department);
    }
 
}
