<?php

namespace App\Policies;

use App\Models\Badge;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BadgePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Badge $badge)
    {
        return $user->hasPermission('Badge','view');
    }

    public function create(User $user,Badge $badge)
    {
        return $user->hasPermission('Badge','create');
    }

    public function update(User $user, Badge $badge)
    {
        return $user->hasPermission('Badge','update');
    }

    public function delete(User $user, Badge $badge)
    {
        return $user->hasPermission('Badge','delete');
    }

    public function restore(User $user, Badge $badge)
    {
        //
    }

    public function forceDelete(User $user, Badge $badge)
    {
        //
    }
}
