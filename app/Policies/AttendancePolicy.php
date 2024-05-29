<?php

namespace App\Policies;

use App\Models\Attendance;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    public function import(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","import");
    }

    public function export(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","export");
    }

    public function hrView(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","hrView");
    }

    public function managerView(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","managerView");
    }

    public function viewAttendance(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","Punch Attendance");
    }

    public function create(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","create");
    }

    public function dashboard(User $user, Attendance $attendance)
    {
        return $user->hasPermission("Attendance","dashboard");
    }
  
}
