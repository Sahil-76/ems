<?php

namespace App\Policies;

use App\Models\Task;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('Task','viewAny');
    }

    public function view(User $user, Task $task)
    {
        return $user->hasPermission('Task','view');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Task','create');
    }

    public function update(User $user, Task $task)
    {
        return $user->hasPermission('Task','update');
    }

    public function delete(User $user, Task $task)
    {
        return $user->hasPermission('Task','delete');
    }

    public function restore(User $user, Task $task)
    {
        //
    }

    public function forceDelete(User $user, Task $task)
    {
        //
    }

    public function trainingView(User $user, Task $task)
    {
        return $user->hasPermission('Task','trainingView');
    }
}
