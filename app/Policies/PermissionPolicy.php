<?php

namespace App\Policies;

use App\Models\Permission;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","view");
    }

    public function insert(User $user)
    {
        return $user->hasPermission("Permission","insert");
    }

    public function update(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","update");
    }

    public function delete(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","delete");
    }

    public function restore(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","restore");
    }

    public function destroy(User $user, Permission $permission)
    {
        return $user->hasPermission("Permission","destroy");
    }

    public function assignPermission(User $user,Permission $permission)
    {
        return $user->hasPermission('Permission','assignPermission');
    }
}
