<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use App\Models\ShiftType;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table    = 'employee_attendance';
    protected $guarded  = ['id'];
    protected $append   = ['total_late'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function shiftType()
    {
        return $this->belongsTo(ShiftType::class, 'shift_type_id');
    }

    public function getTotalLateAttribute()
    {
        $shiftTime      =   strtotime($this->user->shiftType->start_time);
        $startTime      =   Carbon::createFromFormat('H:i:s',$this->user->shiftType->start_time);
        $end            =   Carbon::createFromFormat('H:i:s',$this->punch_in);
        $totalDuration  =   $end->diffInSeconds($startTime);
        $difference     =   gmdate('H:i:s', $totalDuration);
        $minutes        =   date('i', strtotime($difference));

        return $minutes;
    }
}
