<?php

namespace App\Policies;

use App\Models\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Role $role)
    {
        return $user->hasPermission("Role","view");
    }

    public function insert(User $user)
    {
        return $user->hasPermission("Role","insert");
    }

    public function update(User $user, Role $role)
    {
        return $user->hasPermission("Role","update");
    }

    public function delete(User $user, Role $role)
    {
        return $user->hasPermission("Role","delete");
    }

    public function restore(User $user, Role $role)
    {
        return $user->hasPermission("Role","restore");
    }

    public function destroy(User $user, Role $role)
    {
        return $user->hasPermission("Role","destroy");
    }

    public function assignRole(User $user, Role $role)
    {
        return $user->hasPermission("Role", 'assignRole');
    }
}
