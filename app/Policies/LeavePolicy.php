<?php

namespace App\Policies;

use App\Models\Leave;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeavePolicy
{
    use HandlesAuthorization;

    public function approval(User $user, Leave $leave)
    {
        return $user->hasPermission('Leave', 'approval');
    }

    public function managerLeaveList(User $user, Leave $leave)
    {
        return $user->hasPermission('Leave', 'managerLeaveList');
    }

    public function hrLeaveList(User $user, Leave $leave)
    {
        return $user->hasPermission('Leave', 'hrLeaveList');
    }

    public function leaveView(User $user, Leave $leave)
    {
        return $user->hasPermission('Leave','LeaveView');
    }

    public function cancelLeaveList(User $user, Leave $leave)
    {
        return $user->hasPermission('Leave','cancelLeaveList');
    }
    
}
