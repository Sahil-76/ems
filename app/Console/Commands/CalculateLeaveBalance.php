<?php

namespace App\Console\Commands;

use App\Http\Controllers\ems\CalculateBalanceController;
use App\User;
use Carbon\Carbon;
use App\Models\LeaveBalance;
use Illuminate\Console\Command;

class CalculateLeaveBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add leave balance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $calculateBalance       =   new CalculateBalanceController();
        $calculateBalance->calculateBalance();
        // ini_set('max_execution_time', '-1');
        // $defaultEveryMonthBalance   =   1.25;
        // $users              =   User::whereHas('employee', function ($query) {
        //     $query->whereNotNull('contract_date');
        // })->with('employee')->get();
        // $currentMonth       =   Carbon::now()->startOfMonth();
        // $lastMonth          =   Carbon::now()->startOfMonth()->subMonth();
        // if ($currentMonth->format("m") == "01") {
        //     $lastMonth          =   Carbon::now()->subYear()->addMonths(12)->startOfMonth();
        // }
        // foreach ($users as $user) {
        //     $joinMonth      =   Carbon::createFromFormat('Y-m-d', $user->employee->contract_date);
        //     // if person has joined in current month and joined after 15 then no balance will be credited
        //     if($this->newJoinerBalanceEligibility($joinMonth,$lastMonth))
        //     {
        //         continue;
        //     }
        //     $leaveBalance               =   LeaveBalance::where('user_id', $user->id)->whereMonth('month', $currentMonth)->whereYear('month', $currentMonth)->first();
        //     $lastMonthLeaveBalance      =   LeaveBalance::where('user_id', $user->id)->whereMonth('month', $lastMonth)->whereYear('month', $lastMonth)->first();
        //     // if advance leave for next month not taken
        //     if (empty($leaveBalance)) {
        //         $leaveBalance               =   new LeaveBalance();
        //         $leaveBalance->balance      =   $defaultEveryMonthBalance;
        //         $leaveBalance->month        =   $currentMonth->format('Y-m-d');
        //         $leaveBalance->user_id      =   $user->id;
        //         if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
        //             $lastMonthLeaveBalance->is_forwarded    =   1;
        //             $leaveBalance->prev_month_deduction     =   $lastMonthLeaveBalance->next_month_deduction;
        //             $lastMonthLeaveBalance->save();
        //         }
        //         // if balance entry month is 1st then no balance will be credit or will be given of previous month
        //         if ($currentMonth->format('m') != "1") {
        //             if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
        //                 $leaveBalance->balance                  =   $lastMonthLeaveBalance->balance + $leaveBalance->balance;
        //                 $leaveBalance->save();
        //                 $lastMonthLeaveBalance->is_forwarded    =   1;
        //                 $lastMonthLeaveBalance->save();
        //             }
        //         }
        //     }
        //     // if advance leave for next month taken
        //     else {
        //         $lastMonthBalance                       =   0;
        //         if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
        //             $lastMonthBalance                       =   $lastMonthLeaveBalance->balance;
        //             $leaveBalance->prev_month_deduction     =   $lastMonthLeaveBalance->next_month_deduction;
        //             $lastMonthLeaveBalance->is_forwarded    =   1;
        //             $lastMonthLeaveBalance->save();
        //         }
        //         // if balance entry month is 1st then no balance will be credit or will be given of previous month
        //         if ($currentMonth->format('m') != "1") {
        //             $deductibleBalance  =   $leaveBalance->balance + $lastMonthBalance + $defaultEveryMonthBalance;
        //         } else {
        //             $deductibleBalance  =  $defaultEveryMonthBalance;
        //         }
        //         $leftBalance        =   0;
        //         $whole              = intval($deductibleBalance);
        //         $decimal1           = $deductibleBalance - $whole;
        //         $decimal2           = round($decimal1, 2);
        //         $decimal            = substr($decimal2, 2);
        //         if ($decimal == '25' || $decimal == '75') {
        //             $deductibleBalance    =   $deductibleBalance - 0.25;
        //             $leftBalance          =   0.25;
        //         }


        //         $balance            =   $leaveBalance->pre_approval_deduction - $deductibleBalance;
        //         if ($balance <= 0) {
        //             $leaveBalance->pre_approval_deduction       =   0;
        //             // if person has next month deduction then adjust here
        //             if ($leaveBalance->next_month_deduction != 0) {
        //                 $balance            =   $leaveBalance->next_month_deduction - abs($balance);
        //                 if ($balance <= 0) {
        //                     $leaveBalance->next_month_deduction = 0;
        //                     $leaveBalance->balance      =    $leftBalance + abs($balance);
        //                 } else {
        //                     $leaveBalance->next_month_deduction     =   $balance;
        //                     $leaveBalance->balance                  =   $leftBalance;
        //                 }
        //             } else {
        //                 $leaveBalance->balance                      =    $leftBalance + abs($balance);
        //             }
        //         } else {
        //             $leaveBalance->balance                       =   $leftBalance;
        //             $leaveBalance->pre_approval_deduction        =   $balance;
        //         }
        //     }
        //     $leaveBalance->save();
        // }
    }

    // public function newJoinerBalanceEligibility($joinMonth, $lastMonth)
    // {
    //     if ($joinMonth->format('d') > 15 && $joinMonth->format('m') == $lastMonth->format('m') && $joinMonth->format('Y') == $lastMonth->format('Y')) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
}
