<?php

namespace App\Models;

use Str;
use Carbon\Carbon;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table                    = 'leaves';
    protected $guarded                  = ['id'];
    private static $status              = ['Pending', 'Cancel', 'Rejected', 'Approved'];
    private static $leaveSession        = ['Full Day' => ['Full Day'], 'Half Day' => ['First Half', 'Second Half']];
    protected $appends                  = ['duration','sundays'];
    protected $additional_attributes    = ['duration'];
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id')->withoutGlobalScopes();
    }

    public static function getLeaveSession()
    {
        return self :: $leaveSession;
    }

    public function activity()
    {
        return  $this->morphMany('App\Models\ActivityLog','module');
    }

    public function getTimingAttribute()
    {
        if(empty($this->user))
        {
            return '';
        }

        switch ($this->leave_session) {
            case 'Second half':
                return $this->user->shiftType->mid_time.'-'.$this->user->shiftType->end_time;
                break;
            case 'First half':
                return $this->user->shiftType->start_time.'-'.$this->user->shiftType->mid_time;
                break;
            case 'Full day':
                return $this->user->shiftType->start_time.'-'.$this->user->shiftType->end_time;
                break;

            default:
                # code...
                break;
        }
    }

    public function leaveCancellation()
    {
        $date   =   $this->from_date;
        $time   =   Str::before($this->timing,'-');
        $today  =   Carbon::now();

        if($this->leave_session!='Second half')
        {
            $time   =   Carbon::parse($time)->addHour()->format('H:i');
        }

        if($today->format('Y-m-d')<=$date)
        {
            if($today->format('Y-m-d')==$date)
            {
                return strtotime($today->format('H:i:s'))>strtotime($time);
            }
            return false;
        }
        else
        {
            return true;
        }
    }

    public function setDurationAttribute()
    {
        if($this->duration > 1)
        {
            return $this->duration . " Days";
        }
        return $this->duration . " Day";
    }

    public function getDurationAttribute()
    {
        $from_date      =   Carbon::parse($this->from_date);
        $to_date        =   Carbon::parse($this->to_date);
        $days           =   $from_date->diffInDays($to_date);
        $offDay         =   $this->user->off_day ?? 'Sunday';
        $day_of_week    =   date('N', strtotime($offDay));
        $offDays        =   intval($days / 7) + ($from_date->format('N') + $days % 7 >= $day_of_week);

        if($this->leave_session!='Full day')
        {
            $days       =   $days+1-$offDays;
            return $days/2;
        }

        return $days+1-$offDays;
    }

    public function getSundaysAttribute()
    {
        $from_date      =   Carbon::parse($this->from_date);
        $to_date        =   Carbon::parse($this->to_date);
        $days           =   $from_date->diffInDays($to_date);
        $offDay         =   $this->user->off_day ?? 'Sunday';
        $day_of_week    =   date('N', strtotime($offDay));
        $offDays        =   intval($days / 7) + ($from_date->format('N') + $days % 7 >= $day_of_week);

        return $offDays;
    }

    public function forwardedLeave()
    {
        $leaveTypeIds   =   LeaveType::whereIn('name',['Marriage','Exam','Medical'])->pluck('id','id')->toArray();

        if((in_array($this->leave_nature,$leaveTypeIds) && $this->duration >2 ) ||  ($this->duration>2) )
        {
            return false;
        }
        return true;
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class,'leave_type_id');
    }

    public function getShiftAttribute($value)
    {
        $start_time = Carbon::createFromFormat('H:i:s',$this->user->shiftType->start_time)->format('g:i A');
        $end_time   = Carbon::createFromFormat('H:i:s',$this->user->shiftType->end_time)->format('g:i A');

        return  $start_time."-".$end_time;
    }
}
