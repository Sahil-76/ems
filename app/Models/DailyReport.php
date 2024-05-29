<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DailyReport extends Model
{
    protected $table        = 'employee_daily_reports';
    protected $primaryKey   = 'id';
    protected $guarded      = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    function employeeLeaveNature()
    {   
        $leaveNature  =  $this->user->leaves->where('from_date', '<=', $this->report_date)
                            ->where('to_date', '>=', $this->report_date)->where('is_approved', 1)
                            ->first()->leave_session ?? null;
    
        return empty($leaveNature) ? 'Present' : $leaveNature;
    }
}
