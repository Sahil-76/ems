<?php

namespace App\Policies;

use App\Models\Team;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('Team','viewAny');
    }
    
    public function view(User $user, Team $team)
    {
        return $user->hasPermission('Team','view');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Team','create');
    }

    public function update(User $user, Team $team)
    {
        return $user->hasPermission('Team','update');
    }

    public function delete(User $user, Team $team)
    {
        return $user->hasPermission('Team','delete');
    }

    public function dashboard(User $user, Team $team)
    {
        return $user->hasPermission('Team','dashboard');
    }

    public function restore(User $user, Team $team)
    {
        //
    }

    public function forceDelete(User $user, Team $team)
    {
        //
    }
}
