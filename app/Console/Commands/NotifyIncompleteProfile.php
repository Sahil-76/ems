<?php

namespace App\Console\Commands;

use App\Http\Controllers\ems\EmployeeController;
use App\Models\Employee;
use Illuminate\Console\Command;

class NotifyIncompleteProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:incomplete-profile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $employees      =    Employee::with('user')
        ->whereHas('user', function($user){
            $user->where('user_type', 'Employee');
        })
        ->where(function($q){
            $q->whereDoesNtHave('documents')
            ->orWhereDoesNtHave('employeeEmergencyContact')
            ->orWhereDoesNtHave('user',function($user){
                $user->where('profile_pic','<>',null);
            });
        })
        ->where('is_active', '1')
        ->get();

        // $employees      =   $employees->where('office_email', '!=', 'martha.folkes@theknowledgeacademy.com');
        foreach ($employees as $employee)
        {
            (new EmployeeController())->send_email_reminder($employee);
        }
    }
}
