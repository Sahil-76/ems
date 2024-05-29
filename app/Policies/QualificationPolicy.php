<?php

namespace App\Policies;

use App\Models\Qualification;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QualificationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","view");
    }

    public function insert(User $user)
    {
        return $user->hasPermission("Qualification","insert");
    }

    public function update(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","update");
    }

    public function delete(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","delete");
    }

    public function restore(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","restore");
    }

    public function destroy(User $user, Qualification $qualification)
    {
        return $user->hasPermission("Qualification","destroy");
    }
}
