<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;

use App\Models\Employee;
use Illuminate\Console\Command;
use App\Notifications\UnloggedSecondHalvesNotification;

class NotifyAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send notification to manager and HR if employee is absent';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        // $today = '2023-05-27';
        // Fetch employees who have taken first-half leave and did not take second half and also did not punch in the second half
        $employees = Employee::whereHas('user', function ($query) use ($today) {
            //users whose leaves are active during the current $today date's first half
            $query->whereHas('leaves', function ($q) use ($today) { 
                $q->where('from_date', '<=', $today)
                    ->where('to_date', '>=', $today)
                    ->where('leave_session', 'First half')
                    ->where('is_approved', 1);
            })
                 //users who do not have an active leave during the current $today date's second half
                ->whereDoesntHave('leaves', function ($q) use ($today) { 
                    $q->where('from_date', '<=', $today)
                        ->where('to_date', '>=', $today)
                        ->where('leave_session', 'Second half')
                        ->where('is_approved', 1);
                })
                //Here, we're checking for employees who did not punch-in after 2 PM on the current date
                ->whereDoesntHave('attendances', function ($q) use ($today) {
                    $q->whereDate('punch_date', $today)
                    // ->whereBetween('punch_in', [Carbon::createFromFormat('Y-m-d H:i:s', $today . ' 12:00:00'), Carbon::createFromFormat('Y-m-d H:i:s', $today . ' 14:30:00')]);
                    ->whereBetween('punch_in', [Carbon::today()->startOfDay()->toTimeString(), Carbon::today()->setHours(14)->setMinutes(30)->toTimeString()]);
                });
        })
            ->with('user', 'department')
            ->get();

        foreach ($employees as $employee) {
            $manager = $employee->department->manager_id;
            if ($manager) {
                $selectedHrUsers = User::whereIn('id', [22, 392])->pluck('email', 'email')->toArray();
                // $selectedHrUsers = User::whereIn('id', [392])->pluck('email', 'email')->toArray();
                Employee::find($manager)->user->notify(new UnloggedSecondHalvesNotification($today, $employee, $selectedHrUsers));
            }

            // if ($managerId) {
            //     Employee::find($managerId)->user->notify(new UnloggedSecondHalvesNotification($today, $employee));
            // }
        }
    }
}
