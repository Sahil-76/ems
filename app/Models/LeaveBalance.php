<?php

namespace App\Models;

use App\User;
use App\Models\Leave;
use App\Models\LeaveBalanceComplaint;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $table    = 'leave_balances';
    protected $guarded  = ['id'];
    protected $appends  = ['final_deduction','previous_month_balance','after_cut_off','total_sundays'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFinalDeductionAttribute()
    {
        return $this->deduction +$this->pre_approval_deduction + $this->prev_month_deduction + $this->absent;
    }

    public function getPreviousMonthBalanceAttribute()
    {
        $previous_month = Carbon::parse($this->month)->subMonth();
        $balance        = LeaveBalance::whereMonth('month',$previous_month)->where('user_id',$this->user_id)->first()->balance ?? 0;

        return $balance;
    }

    public function getLeavesAfterCutOffAttribute()
    {
        $month      =   explode('-',$this->month);
        $startDate  =   Carbon::createFromFormat('m',$month[1])->startOfMonth()->addDays(20);
        $endDate    =   Carbon::createFromFormat('m',$month[1])->endOfMonth();
        $leaves     =   Leave::where('user_id',$this->user_id)->whereDate('from_date','>',$startDate->format('Y-m-d'))
                                ->where('to_date','<=',$endDate->format('Y-m-d'))->whereNotIn('status',['Cancelled','Rejected'])
                                ->get();

        return $leaves->sum('duration');
    }

    public function getTotalSundaysAttribute()
    {
        $month      =   explode('-',$this->month);
        $startDate  =   Carbon::createFromFormat('m',$month[1])->startOfMonth();
        $endDate    =   Carbon::createFromFormat('m',$month[1])->endOfMonth();
        $leaves     =   Leave::where('user_id',$this->user_id)->whereDate('from_date','>',$startDate->format('Y-m-d'))
                            ->where('to_date','<=',$endDate->format('Y-m-d'))->whereNotIn('status',['Cancelled','Rejected'])
                            ->get();

        return $leaves->sum('sundays');
    }

    public function leaveBalanceComplaints()
    {
        return $this->hasMany(LeaveBalanceComplaint::class, 'leave_balance_id','id');
    }
}
