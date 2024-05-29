<?php

namespace App\Observers;
use App\Models\EmployeeProfileDraft;

class EmployeeProfileDraftObserver
{
    
    public function created(EmployeeProfileDraft $employeeDraft)
    {
        $action = $employeeDraft->field_name." added  by ".$employeeDraft->employee->name;
        saveLogs( $action,$employeeDraft->employee);
    }

    /**
     * Handle the employee "updated" event.
     *
     * @param  \App\Employee  $employee
     * @return void
     */
    public function updated(EmployeeProfileDraft $employeeDraft)
    {
        $action =  $employeeDraft->field_name." updated  by ".$employeeDraft->employee->name;
        saveLogs( $action,$employeeDraft->employee);
    }

    /**
     * Handle the employee "deleted" event.
     *
     * @param  \App\Employee  $employee
     * @return void
     */
   
   
   
}
