<?php

namespace App\Policies;

use App\Models\Employee;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Employee $employee)
    {
        return $user->hasPermission('Employee','view');
    }

    public function destroy(User $user, Employee $employee)
    {
        return $user->hasPermission('Employee','delete');
    }

    public function restore(User $user, Employee $employee)
    {
        return $user->hasPermission('Employee','restore');
    }

    public function viewProfile(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","viewProfile");
    }

    public function viewFullProfile(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","viewFullProfile");
    }

    public function editProfile(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","editProfile");
    }

    public function import(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","import");
    }

    public function pendingProfile(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","pendingProfile");
    }

    public function viewDocument(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","viewDocument");
    }

    public function viewAttendance(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","viewAttendance");
    }

    public function viewLeaveList(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","viewLeaveList");
    }

    public function applyLeave(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","applyLeave");
    }

    public function viewEquipment(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","viewEquipment");
    }

    public function hrEmployeeList(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","hrEmployeeList");
    }

    public function hrUpdateEmployee(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","hrUpdateEmployee");
    }

    public function hrImportEmployee(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","hrImportEmployee");
    }

    public function hrExportEmployee(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","hrExportEmployee");
    }

    public function status(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","status");
    }

    public function managerEmployeeList(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","managerEmployeeList");
    }

    public function hrNoDuesApprover(User $user, Employee $model)
    {
        $user_id = User::havingRole(['HR','admin']);

        if(in_array(auth()->user()->id,$user_id))
        {
            return true;
        }
        return false;
    }

    public function managerNoDuesApprover(User $user, Employee $model)
    {
        $user_id = User::havingRole(['Manager','admin']);

        if(in_array(auth()->user()->id,$user_id))
        {
            return true;
        }
        return false;
    }

    public function itNoDuesApprover(User $user,Employee $model)
    {
       $user_id = User::havingRole(['IT Manager','admin']);

        if(in_array(auth()->user()->id,$user_id))
        {
            return true; 
        }

       return false;
    }

    public function barcodeListView(User $user, Employee $model)
    {
        return $user->hasPermission("Employee","barcodeListView");
    }

    public function exitExport(User $user, Employee $model)
    {
        return $user->hasPermission('Employee','exitExport');
    }
}
