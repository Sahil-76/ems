<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\LeaveType;
use App\User;

class LeaveTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }
    
    public function view(User $user, LeaveType $leaveType)
    {
        return $user->hasPermission('LeaveType','view');
    }

    public function create(User $user)
    {
        return $user->hasPermission('LeaveType','create');
    }

    public function update(User $user, LeaveType $leaveType)
    {
        return $user->hasPermission('LeaveType','update');
    }

    public function delete(User $user, LeaveType $leaveType)
    {
        return $user->hasPermission('LeaveType','delete');
    }

    public function restore(User $user, LeaveType $leaveType)
    {
        //
    }

    public function forceDelete(User $user, LeaveType $leaveType)
    {
        //
    }
}
