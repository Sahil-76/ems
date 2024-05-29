<?php

namespace App\Http\Controllers\ems;

use App\User;
use App\Models\Employee;
use App\Models\ShiftType;
use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManualAttendanceRequest;

class ManualAttendanceController extends Controller
{

    public function create()
    {
        $attendance                           =    new Attendance();
        $this->authorize('create', $attendance);
        $data['attendance']                   =   $attendance;
        $data['submitRoute']                  =   'manual-attendance.store';
        $data['departments']                  =   Department::pluck('name', 'id')->toArray();
        $data['users']                        =   User::where('is_active', 1)->whereIn('user_type', ['Employee', 'Office Junior'])->pluck('name', 'id')->toArray();
        $data['status']                       =   ['Office' => 'Office','Work from Home' => 'Work from Home'];
        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr') )
        {
        $data['employeeDepartments']          =   Employee::with("department")->where('is_active', '1')->whereHas('user', function ($user) {
                                                      $user->where('is_active', '1')->where('user_type', 'Employee');
                                                  })->select('user_id', 'department_id', 'biometric_id', 'name')->get()->groupBy('department.name');
        }
        else
        {
            $departmentIds                          =   auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
            $data['employeeDepartments']            =   Employee::with("department:name,id")->where('is_active', '1')->whereIn('department_id',$departmentIds)->whereHas('user', function ($user) {
                                                        $user->where('is_active', '1')->where('user_type', 'Employee');
                                                        })->select('user_id', 'department_id', 'biometric_id', 'name')->get()->
                                                        groupBy('department.name');
        }
        $data['shifts']                       =   ShiftType::all();
        return view('attendance.manualAttendance',$data);
    }

    public function store(ManualAttendanceRequest $request)
    {
        $this->authorize('create', new Attendance());
        $attendance = Attendance::firstOrNew(['user_id'=>$request->user_id,
            'punch_date'    => $request->punch_date,
            ]);
        $date = $attendance->punch_date;
        $attendance->status           = $request->status;
        $attendance->shift_type_id    = $request->shift_type_id;
        $attendance->added_by         = auth()->user()->id;
        $attendance->entry_type       = 'Manual';
        $attendance->save();
        $user = User::with(['leaves' => function ($leaves)  use ($date) {
                            $leaves->where('is_approved', '1')->where(function ($subQuery)  use ($date) {
                            $subQuery->where(function ($query1) use ($date) {
                            $query1->where('from_Date', '<=', $date)->where('to_Date', '>=', $date);
                            })->orWhere(function ($query2) use ($date) {
                            $query2->whereBetween('from_Date', [$date, $date]);
                            });
                        });
                    }])->where('id', $request->user_id)->first();

        $shiftType          =   ShiftType::find(request()->shift_type_id);
        $start_time         =   Carbon::parse($shiftType->start_time);
        $out_time           =   Carbon::parse($shiftType->end_time);
        if ($user->leaves->isNotEmpty()) {

            $leave =  $user->leaves->first();
            if ($leave->leave_session == "First half") {

                $start_time    = Carbon::parse($shiftType->mid_time);
            }
            if ($leave->leave_session == "Second half") {

                $out_time    = Carbon::parse($shiftType->mid_time);
            }
        }
        if(!empty($request->punch_in))
        {
            $attendance->punch_in   = $request->punch_in;

            $inTime               =   Carbon::parse($attendance->punch_in);

            $diff                 =   $inTime->diffInMinutes($start_time);
            if ($inTime > $start_time) {

                $attendance->in           =   -$diff;
            } else {
                $attendance->in           =   $diff;
            }
        }
        if(!empty($request->punch_out))
        {
            $attendance->punch_out  = $request->punch_out;

            $punchOut               =   Carbon::parse($attendance->punch_out);
            $diff                   =   $punchOut->diffInMinutes($out_time);
            if ($punchOut < $out_time) {

                $attendance->out           =   -$diff;
            } else {
                $attendance->out          =   $diff;
            }
        }
        $attendance->save();
        return redirect()->back()->with('success','Punch In Added Successfully');
    }

    public function getUsers($department_id)
    {
       $users=  Employee::select('id','name','biometric_id','user_id','office_email')->where('department_id', $department_id)->whereHas('user',function ($user)
                {
                    $user->where('user_type', 'Employee')->where('is_active', 1);
                })->get();
        return $users;
    }

    public function getAttendance(Request $request)
    {
        $attendance = Attendance::where('user_id', $request->user_id)
        ->where('punch_date',$request->date)->first();
        if(empty($attendance)){return null;}
        $attendance->punch_in   = !empty($attendance->punch_in)  ? Carbon::createFromFormat('H:i:s',$attendance->punch_in)->format('H:i')  : null;
        $attendance->punch_out  =  !empty($attendance->punch_out) ? Carbon::createFromFormat('H:i:s',$attendance->punch_out)->format('H:i') : null;
        return $attendance;
    }

}
