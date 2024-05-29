<?php

namespace App\Policies;

use App\Models\Module;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModulePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Module $module)
    {
        return $user->hasPermission("Module","view");
    }

    public function insert(User $user)
    {
        return $user->hasPermission("Module","insert");
    }

    public function update(User $user, Module $module)
    {
        return $user->hasPermission("Module","update");
    }

    public function delete(User $user, Module $module)
    {
        return $user->hasPermission("Module","delete");
    }

    public function restore(User $user, Module $module)
    {
        return $user->hasPermission("Module","restore");
    }

    public function destroy(User $user, Module $module)
    {
        return $user->hasPermission("Module","destroy");
    }
}
