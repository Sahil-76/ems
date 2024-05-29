<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Leave;
use Illuminate\Console\Command;

class ChangeForwardedStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:change-forwarded-status';
    protected $description = 'Change forwarded status for pending leaves where manager is on leave today';
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        // Fetch leaves where the leave is pending, forwarded is 0, and the manager is on leave today
        $leaves = Leave::where('status', 'Pending')
            ->where('forwarded', 0)
            ->whereDate('from_date', $today)
            ->whereHas('user.employee.department.deptManager', function ($query) use ($today) {
                $query->whereHas('user.leaves', function ($q) use ($today) {
                    $q->where('status', 'Approved')
                        ->whereDate('from_date', $today)
                        ->whereIn('leave_session', ['Full day', 'First half']);
                        // ->where('leave_session', 'Full day');
                });
            })
            ->get();
    
        foreach ($leaves as $leave) {
            // Change the forwarded status to 1
            $leave->forwarded = 1;
            $leave->status='Auto Forwarded';
            $leave->save();
        }
        // dd('Sucessfull');
    }
}
