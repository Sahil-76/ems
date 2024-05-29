<?php

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Models\Announcement;
use App\Models\Qualification;
use App\Models\EmployeeProfileDraft;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ems\NotificationController;

if (!function_exists('saveLogs')) {
    /**
     * stores the activity log of users.
     */
    function saveLogs($action, $module)
    {
        $user_id = auth()->user()->id ?? 1;
        $activity = new ActivityLog();
        $activity->user_id = $user_id;
        $activity->action = $action;
        $module->activity()->save($activity);
        $activity->save();
    }
}
if (!function_exists('send_notification')) {

    function send_notification($user_ids, $message, $link, $type = null)
    {
        NotificationController::send_notification($user_ids, $message, $link, $type);
    }
}
if (!function_exists('getDateTime')) {

    function getDateTime($date)
    {
        return Carbon::parse($date)->setTimezone(Auth::User()->timezone)->format('d M Y H:i A');
    }
}
if (!function_exists('send_email')) {

    function send_email($template, $data, $subject, $message, $to, $file = null, $cc = [], $bcc = [])
    {

        // dd($data,$template,$data,$to,$message);
        $to     =   Arr::wrap($to);
        $to     =   array_unique($to);
        $cc     =   array_unique($cc);
        $bcc    =   array_unique($bcc);
        $data['Subject'] = $subject;
        $data['emailMessage'] = $message;
        // dd($data,$message);
        Mail::send($template, $data, function ($message) use ($to, $subject, $cc, $bcc, $file) {
            $message->subject($subject);
            if (!empty($file)) {
                $message->attach(storage_path('app/' . $file));
            }
            if (count($to) > 0) {
                $message->to($to);
            }
            if (count($cc) > 0) {
                $message->cc($cc);
            }
        });
        // if (!empty(Mail::failures())) {
        //     return false;
        // }
        return true;
    }
}

if (!function_exists('getQualificationName')) {
    function getQualificationName($id)
    {
        $qualificaton =  Qualification::find($id);
        if (!empty($qualificaton)) {
            return $qualificaton->name;
        } else {
            return 'N/A';
        }
    }
}
if (!function_exists('draft_check')) {
    function draft_check($employee_id, $fieldName)
    {
        return EmployeeProfileDraft::where(['employee_id' => $employee_id, 'field_name' => $fieldName])->whereNull(['is_approved', 'approved_by'])->exists();
    }
}

if (!function_exists('getFormatedDate')) {
    function getFormatedDate($date)
    {
        if (!empty($date)) {
            return Carbon::parse($date)->format('d/m/Y');
        } else {
            return 'N/A';
        }
    }
}

if (!function_exists('getAnnouncements')) {

    function getAnnouncements()
    {

        $today = Carbon::today();
        $current_time = Carbon::now(new \DateTimeZone('Asia/Kolkata'))->format('H:i:s');


        $announcements           =  Announcement::whereDate('start_dt', '<=', $today)->whereDate('end_dt', '>=', $today)
            ->where('start_time', '<=', $current_time)
            ->where('end_time', '>=', $current_time)
            ->where('is_publish', '1')->whereHas('users', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->get();

        return $announcements;
    }
}

if (!function_exists('getFormatedTime')) {
    function getFormatedTime($time)
    {
        if (Str::contains($time, '-')) {
            return Carbon::parse(Str::before($time, '-'))->format('h:i') . '-' . Carbon::parse(Str::after($time, '-'))->format('h:i');
        }
        return Carbon::parse($time)->format('h:i');
    }
}

if (!function_exists('getFormatedDateTime')) {
    function getFormatedDateTime($datetime)
    {
        if (!empty($datetime)) {
            return Carbon::parse($datetime)->format('d M Y H:i A');
        }
        return "N/A";
    }
}

if (!function_exists('commonCount')) {
    function commonCount()
    {



        if (empty(auth()->user()->employee)) {
            $data['departmentLeaves'] = null;
            $data['forwardedLeaves'] = null;
            $data['managerLeaves'] = null;
            $data['drafts'] = null;
            $data['pendingProfiles'] = null;
            return $data;
        }

        $manager_ids                    =   Employee::has('managerDepartments')->pluck('user_id', 'user_id')->toArray();
        $departmentIds                  =   auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
        $data['departmentLeaves']       =   Leave::whereDate('from_date', '>=', Carbon::today())->where('status', 'Pending')->
                                            where('user_id', '<>', auth()->user()->id)->where(['forwarded' => '0'])->
                                            whereHas('user.employee', function ($query) use ($departmentIds) {
                                            $query->whereIn('department_id', $departmentIds);
                                            })->count("id");
        $data['forwardedLeaves']        =   Leave::where('forwarded', '1')->where('status', 'Pending')->whereDate('to_date', '>=', Carbon::today())->count("id");
        $data['managerLeaves']          =   Leave::whereIn('user_id', $manager_ids)->where('status', 'Pending')->count("id");
        $data['drafts']                 =   EmployeeProfileDraft::select("employee_id","approved_by","id")->has('employee.department')->whereNull('approved_by')
                                            ->groupBy('employee_id')->get()->count("id");

        $data['pendingProfiles']    =    Employee::select("id","user_id","department_id")->where('is_active', '1')->with(['department', 'profileReminder' => function ($query) {
                                                return $query->whereDay('created_at', Carbon::now()->day)->where('sent', 1);
                                            }])->whereHas('user', function ($query) {
                                                $query->where('user_type', 'Employee');
                                            })->where(function ($query) {
                                                $query->whereDoesNtHave('documents')->orWhereDoesNtHave('employeeEmergencyContact')
                                                    ->orWhereDoesNtHave('user',function($user){
                                                            $user->where('profile_pic','<>',null);
                                                        });
                                            })->count("id");
        return $data;
    }
}


if (!function_exists('lateTiming')) {
    /**
     * stores the activity log of users.
     */
    function lateTiming()
    {
        $user       = auth()->user();
        $shiftType  = $user->shiftType;
        $user_id    = $user->id;
        $time       = empty($shiftType) ? "09:00:00" : $shiftType->start_time;
        $leave      = Leave::where('user_id', $user_id)->whereDate('from_date', '<=', now())
                            ->whereDate('to_date', '>=', now())->where('status', '<>', 'Cancelled')->first();

        if (!empty($leave) && $leave->leave_session == 'First half') {
            $time   =   empty($shiftType) ? "14:00:00" : $shiftType->mid_time;
        }

        $startTime  =   Carbon::createFromFormat('H:i:s', $time);
        $attendance =   Attendance::select("punch_in")->where('user_id', $user_id)->whereDate('punch_date', now())->first();
        if (empty($attendance) || $attendance->punch_in <= $time)
        {
            return null;
        }

        $end                =    Carbon::createFromFormat('H:i:s', $attendance->punch_in);
        $totalDuration      =    $end->diffInSeconds($startTime);
        $difference         =    gmdate('H:i:s', $totalDuration);
        $hours              =    date('H', strtotime($difference));
        $hours              =    $hours == 0 ? "" : "$hours " . Str::plural('hour', $hours);
        $minutes            =    date('i', strtotime($difference));
        $minutes            =    $minutes == 0 ? "" : "$minutes " . Str::plural('minute', $minutes);
        $seconds            =    date('s', strtotime($difference));
        $seconds            =    $seconds == 0 ? "" : "$seconds " . Str::plural('second', $seconds);
        return "You are $hours $minutes $seconds late";
    }
}

if (!function_exists('departmentAttendance')) {
    function departmentAttendance()
    {
        
         if(empty(auth()->user()->employee))
         {

          return   $message = null;

         }
        $today              =   Carbon::today()->format('Y-m-d');
        $attendances        =   Attendance::with(['user.shiftType', 'user.leaves' => function ($leaves) use ($today) {
                                $leaves->whereDate('from_date', '<=', $today)->whereDate('to_date', '>=', $today)->
                                where('status', '<>', 'Cancelled');
                                }])->whereDate('punch_date', $today)
                                ->whereHas('user.employee', function ($employee) {
                                $employee->where('department_id', auth()->user()->employee->department_id);
                                })->whereHas('user.shiftType', function ($shiftType) {
                                $shiftType->whereColumn('start_time', '<', 'employee_attendance.punch_in');})->get();
        $count = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->total_late ==  00) {
                continue;
            }
            if (!empty($attendance->user) && $attendance->user->leaves->isNotEmpty()) {

                if ($attendance->user->leaves[0]->leave_session == 'First half' && $attendance->punch_in < $attendance->user->shiftType->mid_time) {
                    continue;
                }
            }
            $count++;
        }
        if ($count == 1) {
            $message = "Your 1 team member is late today. Click here.";
        } elseif ($count > 1) {
            $message = "Your $count team members are late today. Click here.";
        } else {
            $message = null;
        }
        return $message;
    }
}

if (!function_exists('draft_profile_check')) {
    function draft_profile_check($employee_id, $fieldName)
    {
        return EmployeeProfileDraft::where(['employee_id' => $employee_id, 'field_name' => $fieldName])->where('is_approved', 1)->exists();
    }
}
