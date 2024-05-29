<?php

namespace App\Observers;

use App\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
    
        $action = "User Created: ".$user->name;
        saveLogs( $action,$user);
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $changes = $user->getDirty();

        foreach ($changes as $columnChanged => $newValue) {
            if (in_array($columnChanged, ['remember_token', 'last_login_at', 'updated_at', 'password'])) { continue; }

            $oldValue   = $user->getOriginal($columnChanged);

            $oldValue = empty($oldValue) ? 'null' : $oldValue;
            $newValue = empty($newValue) ? 'null' : $newValue;
            
            $action = "User Updated: ".$user->name;
 
            saveLogs( $action,$user);
        }
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $action = "User Deleted: ".$user->name;
        saveLogs( $action,$user);
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        $action = "User Restored: ".$user->name;
        saveLogs( $action,$user);
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        $action = "User Force Deleted: ".$user->name;
        saveLogs( $action,$user);
    }
}
