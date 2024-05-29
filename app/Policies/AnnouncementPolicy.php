<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Announcement;
use App\User;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Announcement $announcement)
    {
        return $user->hasPermission("Announcement","view");
    }

    public function create(User $user)
    {
        return $user->hasPermission("Announcement","create");
    }

    public function update(User $user, Announcement $announcement)
    {
        return $user->hasPermission("Announcement","update");
    }

    public function delete(User $user, Announcement $announcement)
    {
        return $user->hasPermission("Announcement","delete");
    }

    public function restore(User $user, Announcement $announcement)
    {
        //
    }

    public function forceDelete(User $user, Announcement $announcement)
    {
        //
    }
}
