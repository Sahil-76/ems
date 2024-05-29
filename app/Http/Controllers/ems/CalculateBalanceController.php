<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\LeaveBalance;
use App\Http\Controllers\Controller;

class CalculateBalanceController extends Controller
{

    public $defaultEveryMonthBalance;
    public $currentMonthDate;
    public $lastMonthDate;
    public function __construct()
    {
        ini_set('max_execution_time', '-1');
        $this->defaultEveryMonthBalance =   1.25;
        $this->currentMonthDate         =   Carbon::now()->startOfMonth();
        $this->lastMonthDate            =   Carbon::now()->startOfMonth()->subMonth();
        if ($this->currentMonthDate->format("m") == "01") {
            $this->lastMonthDate          =   Carbon::now()->subYear()->addMonths(11)->startOfMonth();
        }
    }

    public function calculateBalance($usrId = null)
    {

        $users              =   User::whereHas('employee', function ($query) {
            $query->whereNotNull('contract_date');
        })->with('employee');

        if (!empty($usrId)) {
        $users = $users->where('id', $usrId);
        }

        $users = $users->get();

        foreach ($users as $user) {
            if ($this->newJoinerBalanceEligibility($user->employee->contract_date)===false) {
                continue;
            }

            $leaveBalance               =   $this->getLeaveBalanceByMonth($user->id, $this->currentMonthDate);
            $lastMonthLeaveBalance      =   $this->getLeaveBalanceByMonth($user->id, $this->lastMonthDate);

            if (empty($leaveBalance)) {
                $this->createNewBalance($user->id, $lastMonthLeaveBalance);
            } else {
                $this->updateExistingBalance($leaveBalance, $lastMonthLeaveBalance);
            }
        }
    }

    /**
     * if advance leave for next month taken
     */

    private function createNewBalance($userId, $lastMonthLeaveBalance)
    {
        $leaveBalance               =   new LeaveBalance();
        $balance                    =   $this->defaultEveryMonthBalance;
        $leaveBalance->month        =   $this->currentMonthDate->format('Y-m-d');
        $leaveBalance->user_id      =   $userId;

        // if balance entry month is January then no balance will be credit or will be given of previous month

        if ($this->currentMonthDate->format('m') != "1" && !empty($lastMonthLeaveBalance)) {
            $balance                                =   $balance    +   $lastMonthLeaveBalance->balance;
            $leaveBalance->prev_month_deduction     =   $lastMonthLeaveBalance->next_month_deduction;
        }
        $leaveBalance->balance                =   $balance;
        $leaveBalance->last_month_forwarded   =   1;
        $leaveBalance->save();
    }

    private function updateExistingBalance($leaveBalance, $lastMonthLeaveBalance)
    {
        // if advance leave for next month taken
        $leftBalance                            =   0;
        $lastMonthBalance                       =   0;
        $finalBalance                           =   0;
        $nextMonthDeduction                     =   $leaveBalance->next_month_deduction;

        if (!empty($lastMonthLeaveBalance) && $leaveBalance->last_month_forwarded != 1) {
            $lastMonthBalance                       =   $lastMonthLeaveBalance->balance;
            $leaveBalance->last_month_forwarded     =   1;
        }
        // if balance entry month is January then no balance will be credit or will be given of previous month
        if ($this->currentMonthDate->format('m') != "1"  && !empty($lastMonthLeaveBalance)) {
            $deductibleBalance  =  $lastMonthBalance + $this->defaultEveryMonthBalance;
            $leaveBalance->prev_month_deduction     =   $lastMonthLeaveBalance->next_month_deduction;
        } else {
            $deductibleBalance  =  $this->defaultEveryMonthBalance;
        }

        if ($this->isDecimalBalanceExists($deductibleBalance)) {
            $deductibleBalance    =   $deductibleBalance - 0.25;
            $leftBalance          =   0.25;
        }

        $balance            =   $leaveBalance->pre_approval_deduction - $deductibleBalance;
        if ($balance <= 0) {
            $leaveBalance->pre_approval_deduction       =   0;
            // if person has next month deduction then adjust here
            if ($nextMonthDeduction != 0) {
                $balance            =   $nextMonthDeduction - abs($balance);
                if ($balance <= 0) {
                    $nextMonthDeduction = 0;
                    $finalBalance       =   $leftBalance + abs($balance);
                } else {
                    $nextMonthDeduction =   $balance;
                    $finalBalance       =   $leftBalance;
                }
            } else {
                $finalBalance                      =    $leftBalance + abs($balance);
            }
        } else {
            $finalBalance                           =   $leftBalance;
            $leaveBalance->pre_approval_deduction   =   $balance;
        }

        $leaveBalance->balance                      =   $finalBalance;
        $leaveBalance->next_month_deduction         =   $nextMonthDeduction;
        $leaveBalance->save();
    }

    /** if person has joined in current month and joined after 15 then no balance will be credited  */

    public function newJoinerBalanceEligibility($contractDate)
    {
        if (empty($contractDate)) {
            return false;
        }

        $joinMonth      =   Carbon::createFromFormat('Y-m-d', $contractDate);
        if ($joinMonth->format('d') > 15 && $joinMonth->format('m') == $this->lastMonthDate->format('m') && $joinMonth->format('Y') == $this->lastMonthDate->format('Y')) {
            return false;
        } else {
            return true;
        }
    }

    private function getLeaveBalanceByMonth($userId, $date)
    {
        return LeaveBalance::where('user_id', $userId)->whereMonth('month', $date)->whereYear('month', $date)->first();
    }


    private function isDecimalBalanceExists($deductibleBalance)
    {
        $decimal            = Str::after($deductibleBalance, ".");
        if ($decimal == '25' || $decimal == '75') {
            return true;
        } else {
            return false;
        }
    }
}
