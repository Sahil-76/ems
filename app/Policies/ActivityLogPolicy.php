<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityLogPolicy
{
    use HandlesAuthorization;

    public function view(User $user, ActivityLog $activityLog)
    {
        return $user->hasPermission('Activity','view');
    }

}
