<?php

namespace App\Policies;

use App\Models\Electricity;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ElectricityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Electricity  $electricity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Electricity $electricity)
    {
        return $user->hasPermission("Electricity","view");
    }
    
    public function dashboard(User $user, Electricity $electricity)
    {
        return $user->hasPermission("Electricity","dashboard");
    }

    public function export(User $user, Electricity $electricity)
    {
        return $user->hasPermission("Electricity","export");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermission("Electricity","create");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Electricity  $electricity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Electricity $electricity)
    {
        return $user->hasPermission("Electricity","update");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Electricity  $electricity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Electricity $electricity)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Electricity  $electricity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Electricity $electricity)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Electricity  $electricity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Electricity $electricity)
    {
        //
    }
}
