<?php

namespace App\Observers;

use App\Models\Leave;


class LeaveObserver
{
    /**
     * Handle the leave "created" event.
     *
     * @param  \App\Models\Leave  $leave
     * @return void
     */
    public function created(Leave $leave)
    {
        $action = "Leave Create: ".$leave->user->name;
        saveLogs( $action,$leave);
    }

    /**
     * Handle the leave "updated" event.
     *
     * @param  \App\Models\Leave  $leave
     * @return void
     */
    public function updated(Leave $leave)
    {
        $action = "Leave Updated: ". $leave->type . ". Status: " . $leave->status;
        saveLogs($action, $leave);
    }
}
