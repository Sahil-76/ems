<?php

namespace App\Policies;

use App\Models\Department;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Department $department)
    {
        return $user->hasPermission("Department","view");
    }

    public function insert(User $user)
    {
      
        return $user->hasPermission("Department","insert");
    }

    public function update(User $user, Department $department)
    {
        return $user->hasPermission("Department","update");
    }

    public function delete(User $user, Department $department)
    {
        return $user->hasPermission("Department","delete");
    }

    public function restore(User $user, Department $department)
    {
        return $user->hasPermission("Department","restore");
    }

    public function destroy(User $user, Department $department)
    {
        return $user->hasPermission("Department","destroy");
    }

}
