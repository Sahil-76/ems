<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\ActivityLog;


class EmployeeObserver
{
    /**
     * Handle the employee "created" event.
     *
     * @param  \App\Employee  $employee
     * @return void
     */
    public function created(Employee $employee)
    {
        $action = "Employee Created: ".$employee->name;
        saveLogs( $action,$employee);
    }

    /**
     * Handle the employee "updated" event.
     *
     * @param  \App\Employee  $employee
     * @return void
     */
    public function updated(Employee $employee)
    {
        $action = "Employee Updated: ".$employee->name;
        saveLogs( $action,$employee);
    }

    /**
     * Handle the employee "deleted" event.
     *
     * @param  \App\Employee  $employee
     * @return void
     */
    public function deleted(Employee $employee)
    {
        $action = "Employee Deleted: ".$employee->name;
        saveLogs( $action,$employee);
    }

    /**
     * Handle the employee "restored" event.
     *
     * @param  \App\Employee  $employee
     * @return void
     */
    public function restored(Employee $employee)
    {
        $action = "Employee Restored: ".$employee->name;
        saveLogs( $action,$employee);
    }

    /**
     * Handle the employee "force deleted" event.
     *
     * @param  \App\Employee  $employee
     * @return void
     */
    public function forceDeleted(Employee $employee)
    {
        $action = "Employee Force Deleted: ".$employee->name;
        saveLogs( $action,$employee);
    }
   
   
 
}
