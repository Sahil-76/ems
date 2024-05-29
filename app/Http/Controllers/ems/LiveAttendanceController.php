<?php
namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use App\Models\ShiftType;
use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Http\Request;
use Response;
use App\Models\LiveAttendance;
use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;

class LiveAttendanceController extends Controller
{
    private $today;
    public $attendanceDateFrom;
    public $attendanceDateTo;
    public $yesterday;

    public function __construct()
    {
        $this->today            = Carbon::today()->setTimezone('Asia/Kolkata')->format('Y-m-d');
        $this->yesterday        = Carbon::today()->setTimezone('Asia/Kolkata')->subDay()->format('Y-m-d');
    }

    public function getProgress() {
        if(!session()->has("new_request"))
        {
            Response::json(array(0));
        }
        return Response::json(array(session()->get('progress')));
    }

    public function form()
    {
        abort_if(!auth()->user()->hasRole("admin"),403);
        return view("attendance.fetchAttendanceForm");
    }

    public function storeAttendance()
    {
        session()->forget(["progress","new_request"]);
        ini_set('max_execution_time', '-1');
        if(request()->has('fetch_date'))
        {
            $this->today            = Carbon::createFromFormat('Y-m-d', request()->fetch_date)->setTimezone('Asia/Kolkata')->format('Y-m-d');
            $this->yesterday        = Carbon::createFromFormat('Y-m-d', $this->today)->setTimezone('Asia/Kolkata')->subDay()->format('Y-m-d');
        }
        if(request()->ajax()){session()->put(['progress'=>0,'new_request'=>1]);}
        $liveAttendances    =   LiveAttendance::whereDate('Logdate', $this->today)
                                ->whereIn('Shortname', ['IN-JAL-1',  'IN-JAL-2', 'IN-JAL-1- New'])
                                ->get();
        $today              =   $this->today;
        $currentCount       =   1;
        $total              = count($liveAttendances);
        foreach ($liveAttendances as $attendanceData)
        {
            $employee       =   Employee::with(['user.leaves' => function ($leaves)  use ($today)
                                {
                                    $leaves->where('is_approved', '1')->where(function ($subQuery)  use ($today)
                                    {
                                        $subQuery->where(function ($query1) use ($today)
                                        {
                                            $query1->where('from_Date', '<=', $today)->where('to_Date', '>=', $today);
                                        })->orWhere(function ($query2) use ($today)
                                        {
                                            $query2->whereBetween('from_Date', [$today, $today]);
                                        });
                                    });
                                }])->where('biometric_id', $attendanceData->Empcode)->first();
            if (empty($employee) || empty($employee->user)) {
                continue;
            }
            $user           =   $employee->user;
            $start_time     =   Carbon::parse($user->shiftType->start_time);
            $out_time       =   Carbon::parse($user->shiftType->end_time);
            if ($employee->user->leaves->isNotEmpty())
            {
                $leave      =   $employee->user->leaves->first();
                if ($leave->leave_session == "First half")
                {
                    $start_time     =   Carbon::parse($user->shiftType->mid_time);
                }
                if ($leave->leave_session == "Second half")
                {
                    $out_time       =   Carbon::parse($user->shiftType->mid_time);
                }
            }
            $inData         =   null;
            if (!empty($user->attendances) && $user->attendances->isNotEmpty())
            {
                $attendances              =    $user->attendances->where('punch_date', '=', $this->today);
                $inData                   =    $attendances->first();
            }
            if (empty($inData))
            {
                $object                   =    new Attendance();
                $object->user_id          =    $user->id;
                $object->shift_type_id    =    $employee->shift_type_id;
                $object->punch_in         =    $attendanceData->Logtime;
                $object->punch_date       =    $attendanceData->Logdate;
                $inTime                   =    Carbon::parse($attendanceData->Logtime);
                $diff                     =    $inTime->diffInMinutes($start_time);
                if ($inTime > $start_time)
                {
                    $object->in           =   -$diff;
                } else
                {
                    $object->in           =    $diff;
                }
            }
            else
            {
                $object                   =    Attendance::firstOrNew(['punch_date' => $inData->punch_date, 'user_id' => $user->id]);
                $punch_out_time           =    strtotime($attendanceData->Logtime);
                $punch_in                 =    strtotime($object->punch_in);
                $diff                     =    $punch_out_time - $punch_in;
                $tenMinuteDiff            =    600; //  600 seconds (60*10)
                if ($diff > $tenMinuteDiff)
                {
                    $object->punch_out    =    $attendanceData->Logtime;
                    $punchOut             =    Carbon::parse($attendanceData->Logtime);
                    $diff                 =    $punchOut->diffInMinutes($out_time);
                    if ($punchOut < $out_time)
                    {
                        $object->out      =   -$diff;
                    }
                    else
                    {
                        $object->out      =   $diff;
                    }
                }
                if($object->entry_type != "Manual")
                {
                    $object->shift_type_id  =   $employee->shift_type_id;
                    $object->entry_type     =   'Punch';
                }
            }
            $count                          =   $user->attendances->count();
            $firstAttendance                =   $user->attendances->first();
            if ($count == 1  &&  $object->punch_date == $firstAttendance->punch_date)
            {
                $object                     =   $user->attendances->first();
                $object->punch_in           =   "09:00:00";
                $object->in                 =   0;
            }
            $object->remarks                =   'Punched';
            $object->added_by               =   0;
            $object->save();
            if(request()->ajax())
            {
                session()->put('progress', round(($currentCount/$total)*100));
                session()->save();
                $currentCount++;
            }
        }
        session()->forget(["progress","new_request"]);
        return "Attendance Fetched";
    }

    public function attendanceDashboard(Request $request, $export = false)
    {
        ini_set('max_execution_time', -1);
        $this->authorize('dashboard', new Attendance());
        $offDay                         =   'Sunday';
        $dayOfWeek                      =   date('N', strtotime($offDay));
        if (!empty(request()->get('attendanceDateFrom')) && !empty(request()->get('attendanceDateTo')))
        {
            $this->attendanceDateFrom   =   request()->attendanceDateFrom;
            $this->attendanceDateTo     =   request()->attendanceDateTo;
            $thisMonth                  =   Carbon::createFromFormat('Y-m-d', request()->attendanceDateFrom);
            $data['dateStart']          =   Carbon::createFromFormat('Y-m-d', request()->attendanceDateFrom)->format('d M y');
            $data['dateEnd']            =   Carbon::createFromFormat('Y-m-d', request()->attendanceDateTo)->format('d M y');
            $date                       =   $this->attendanceDateFrom;
            $startMonth                 =   Carbon::createFromFormat('Y-m-d',request()->attendanceDateFrom);
            $endMonth                   =   Carbon::createFromFormat('Y-m-d',request()->attendanceDateTo);
            $days                       =   $startMonth->diffInDays($endMonth);
            if($startMonth->format('M')!=$endMonth->format('M'))
            {
                $firstMonthOffDays      =   intval($days / 7)   +   ($startMonth->format('N') + $days % 7 >= $dayOfWeek);
                $secondMonthOffDays     =   intval($days / 7)   +   ($endMonth->format('N') + $days % 7 >= $dayOfWeek);
                $totalOffDays           =   $firstMonthOffDays  +   $secondMonthOffDays;
                $data['workingDays']    =   $days               -   $totalOffDays;
            }
            else
            {
                $offDays                =   intval($days / 7)   +   ($startMonth->format('N') + $days % 7 >= $dayOfWeek);
                $data['workingDays']    =   $days               -   $offDays;
            }
            $data['workingDays']        =   $data['workingDays']+   1 ;
        }
        else
        {
            $this->attendanceDateFrom   =   Carbon::now()->startOfMonth();
            $this->attendanceDateTo     =   Carbon::now();
            $endMonth                   =   Carbon::now()->endOfMonth();
            $thisMonth                  =   Carbon::today()->startOfMonth();
            $data['dateStart']          =   Carbon::now()->startOfMonth()->format('d M y');
            $data['dateEnd']            =   Carbon::today()->format('d M y');
            $date                       =   now()->format('Y-m-d');
            $days                       =   $this->attendanceDateFrom->daysInMonth;
            $offDays                    =   intval($days / 7) + ($this->attendanceDateFrom->format('N') + $days % 7 >= $dayOfWeek);
            $this->attendanceDateFrom   =   Carbon::now()->startOfMonth()->format('Y-m-d');
            $this->attendanceDateTo     =   Carbon::now()->format('Y-m-d');
            $data['workingDays']        =   $days     -   $offDays;
        }
        $yesterday                      =   Carbon::createFromFormat('Y-m-d', $date)->subDay()->format('Y-m-d');
        if ($request->has('is_late_today'))
        {
            $this->attendanceDateFrom   =   Carbon::now()->format('Y-m-d');
            $this->attendanceDateTo     =   Carbon::now()->format('Y-m-d');
            $thisMonth                  =   Carbon::today();
            $data['dateStart']          =   Carbon::now()->format('d M y');
            $data['dateEnd']            =   Carbon::today()->format('d M y');
        }
        if (request()->has('today_punched_in') || request()->has('today_punched_not_in') || request()->has('on_full_day') || request()->has('on_half_day'))
        {
            if (!empty(request()->get('attendanceDateFrom')) && !empty(request()->get('attendanceDateTo'))) {
                $this->attendanceDateFrom       =    request()->attendanceDateFrom;
                $this->attendanceDateTo         =    request()->attendanceDateTo;
                $thisMonth                      =    Carbon::today();
                $data['dateStart']              =    Carbon::createFromFormat('Y-m-d', request()->attendanceDateFrom)->format('d M y');
                $data['dateEnd']                =    Carbon::createFromFormat('Y-m-d', request()->attendanceDateTo)->format('d M y');
            }
            else
            {
                $this->attendanceDateFrom       =    Carbon::now()->format('Y-m-d');
                $this->attendanceDateTo         =    Carbon::now()->format('Y-m-d');
                $thisMonth                      =    Carbon::today();
                $data['dateStart']              =    Carbon::now()->format('d M y');
                $data['dateEnd']                =    Carbon::today()->format('d M y');
            }
        }
        $users      =       $this->attendanceFilter($thisMonth, $this->attendanceDateFrom, $this->attendanceDateTo);
        $data       =       $this->dashboardCounts($this->attendanceDateFrom, $this->attendanceDateTo, $data);
        $dateArray  =       [];
        if (request()->has('yesterday_not_punch_out'))
        {
            $data['dateStart']         =    Carbon::createFromFormat('Y-m-d', $yesterday)->format('d M y');
            $data['dateEnd']           =    Carbon::createFromFormat('Y-m-d', $yesterday)->format('d M y');
            $users->where('is_active', 1)
                    ->whereDoesNtHave('leaves',function($leaves) use($yesterday)
                    {
                        $leaves->where('from_date', '<=', $yesterday)->where('to_date', '>=', $yesterday)
                        ->where('leave_session','Full Day');
                    })
                    ->whereHas('attendances', function ($query) use ($yesterday)
                    {
                        $query->where('punch_date', $yesterday)->whereNull('punch_out');
                    });
        }
        $period             =   CarbonPeriod::create($data['dateStart'], $data['dateEnd']);
        foreach ($period as $date)
        {
            $dateArray[]    =   $date->format('Y-m-d');
        }
        if (request()->has('today_punched_not_in'))
        {
            $users->whereHas('shiftType', function ($query)
            {
                $query->where('name', 'Morning');
            })->where('is_active', 1)->whereDoesNtHave('attendances', function ($query) use ($date)
            {
                $query->where('punch_date', $date);
            })->whereDoesNtHave('leaves', function ($query)
            {
                $query->where('from_date', '<=', $this->attendanceDateFrom)->where('to_date', '>=', $this->attendanceDateTo)
                ->where('is_approved', '1')->where('leave_session', 'Full day');
            });
        }
        if (request()->has('today_punched_in'))
        {
            $users->whereHas('attendances', function ($query) use ($date)
            {
                $query->where('punch_date', $date);
            });
        }
        $users                  =   $users->get();
        $userArray              =   [];
        foreach ($users as  $user)
        {
            $shiftStartTime                             =   !empty($user->shiftType) ? $user->shiftType->start_time : '09:00:00';
            $shiftStartTime                             =   strtotime($shiftStartTime);
            $userArray[$user->email]['biometric_id']    =   $user->employee->biometric_id ?? 'N/A';
            $userArray[$user->email]['name']            =   $user->name ?? 'N/A';
            $userArray[$user->email]['id']              =   $user->id ?? 0;

            foreach ($dateArray as $date)
            {
                $attendance       =     $user->attendances->where('punch_date', $date)->first();
                $employeeLeave    =     $user->leaves->where('is_approved','1')->where('user_id', $user->id)
                                        ->where('from_date', '<=', $date)->where('to_date', '>=', $date)->first();

                $userArray[$user->email][$date]['punch_in']         =    '';

                if (!empty($attendance) && !empty($attendance->punch_in))
                {
                    $userArray[$user->email][$date]['punch_in']     =    $attendance->punch_in;
                    $userArray[$user->email][$date]['punch_out']    =    $attendance->punch_out;
                }
                $userArray[$user->email][$date]['session']          =    '';

                if (!empty($employeeLeave))
                {
                    $userArray[$user->email][$date]['session']      =    $employeeLeave->leave_session;
                }

                if ($request->has('is_late_today'))
                {
                    if (!empty($employeeLeave))
                    {
                        $userArray[$user->email][$date]['punch_in'] =    "";
                        $userArray[$user->email][$date]['session']  =    "";
                    }

                    if (!empty($attendance) && $attendance->punch_in)
                    {
                        $punch_in                                   =    strtotime($attendance->punch_in);

                        if ($punch_in < $shiftStartTime)
                        {
                            $userArray[$user->email][$date]['punch_in'] =   "";
                            $userArray[$user->email][$date]['session']  =   "";
                        }
                    }
                }
            }
        }
        $data['dateArray']                  =    $dateArray;
        $data['userArray']                  =    $userArray;
        $data['departments']                =    Department::pluck('name', 'id');
        $data['shiftTypes']                 =    ShiftType::all();
        $data['userTypes']                  =    Config::get('employee.userTypes');

        if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('HR'))
        {
            $data['employeeDepartments']  	=    User::with('employee.department')->where('is_active',1)->whereHas('employee',function($employee)
            {
                $employee->select('biometric_id','name');
            })->get()->groupBy('employee.department.name');
        }
        else
        {
            if (auth()->user()->hasRole('Line Manager')) {
                $departmentIds = Department::where('line_manager_id', auth()->user()->id)->pluck('id', 'id')->toArray();
            }else{
                $departmentIds  =   auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
            }

            $data['employeeDepartments']  	=   User::with('employee')->where('is_active',1)->where('user_type','Employee')
                                                ->whereHas('employee',function($employee) use($departmentIds)
                                                {
                                                    $employee->whereIn('department_id', $departmentIds)
                                                    ->select('biometric_id','name');
                                                })->get()->groupBy('employee.department.name');
        }

        if ($export == 'true')
        {
            return $data;
        }

        return view('attendance.dashboard', $data);
    }

    public function lateAttendanceDashboard(Request $request)
    {
        $this->authorize('dashboard', new Attendance());
        if (!empty($request->dateFrom) && !empty($request->dateTo))
        {
            $dateFrom           =   $request->dateFrom;
            $dateTo             =   $request->dateTo;
        }
        else
        {
            $dateFrom           =   now()->format('Y-m-d');
            $dateTo             =   $dateFrom;
        }
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('HR') && !auth()->user()->hasRole('HR Junior'))
        {
            if (auth()->user()->hasRole('Line Manager')) {
                $departmentIds = Department::where('line_manager_id', auth()->user()->id)->pluck('id', 'id')->toArray();
            }else{
                $departmentIds  =   auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
            }

            $userAttendances    =   Attendance::whereNotNull('punch_in')->with(['user.shiftType', 'user.employee.department'])
                                    ->whereHas('user.employee', function ($employee) use($departmentIds)
                                    {
                                        $employee->whereIn('department_id', $departmentIds);
                                    })->whereBetween('punch_date', [$dateFrom, $dateTo])->whereHas('user', function ($user)
                                    {
                                        $user->whereHas('shiftType')->where('user_type', 'Employee');
                                    })->where('in', '<', '0')->get()->groupBy('user.email');
        }
        else
        {
            $userAttendances    =   Attendance::whereNotNull('punch_in')->with(['user.shiftType', 'user.employee.department'])
                                    ->whereBetween('punch_date', [$dateFrom, $dateTo])->whereHas('user', function ($user)
                                    {
                                        $user->whereHas('shiftType')->where('user_type', 'Employee');
                                    })->where('in', '<', '0')->get()->groupBy('user.email');
        }
        $data['userAttendances']        =   $userAttendances;
        return view('attendance.lateDashboard', $data);
    }

    public function attendanceExport(Request $request)
    {
        $this->authorize('hrEmployeeList',new Employee());
        $data           =    $this->attendanceDashboard($request, true);
        $fileName       =    "attendance.xlsx";
        return Excel::download(new AttendanceExport($data), $fileName);
    }

    public function myAttendance(Request $request)
    {
        $id                     =    auth()->user()->id;
        $month                  =    Carbon::now()->month;
        $year                   =    Carbon::now()->year;
        $data['startTime']      =    auth()->user()->shiftType->start_time;
        $myAttendances          =    Attendance::where('user_id', $id);
        if (!empty(request()->get("dateFrom")) && !empty(request()->get("dateTo")))
        {
            $myAttendances      =    $myAttendances->whereBetween("punch_date", [$request->dateFrom, $request->dateTo]);
        }
        else
        {
            $myAttendances      =    $myAttendances->whereMonth('punch_date', $month)->whereYear('punch_date', $year);
        }
        $data['myAttendances']  =    $myAttendances->get();
        return view('attendance.myAttendance', $data);
    }

    private function attendanceFilter($thisMonth, $fromDate, $toDate)
    {
        if (request()->has('user_type'))
        {
            $users      =   User::withoutGlobalScopes(['user_type'])->select('shift_type_id', 'name', 'id', 'user_type', 'email')->has('employee')
                                ->with(['employee:department_id,id,biometric_id,user_id', 'attendances' => function ($attendance) use ($thisMonth)
                                {
                                    $attendance->whereMonth('punch_date', $thisMonth);
                                }]);
            $users      =   $users->where('user_type', request()->user_type);
        }
        else
        {
            $users      =   User::where('user_type', 'Employee')->has('employee')->select('shift_type_id', 'name', 'id', 'user_type', 'email')
                                ->with(['employee:department_id,id,biometric_id,user_id', 'attendances' => function ($attendance) use ($thisMonth)
                                {
                                    $attendance->whereMonth('punch_date', $thisMonth);
                                }]);
        }
        if(!empty(request()->user_id))
        {
            $users     =   User::withoutGlobalScopes(['user_type'])->select('shift_type_id', 'name', 'id', 'user_type', 'email')->has('employee')
                                ->with(['employee:department_id,id,biometric_id,user_id', 'attendances' => function ($attendance) use ($thisMonth)
                                {
                                    $attendance->whereMonth('punch_date', $thisMonth);
                                }]);
        }
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('HR') && !auth()->user()->hasRole('HR Junior'))
        {
            $users     =   $users->whereHas('employee', function ($employee)
            {
                if (auth()->user()->hasRole('Line Manager')) {
                    $departmentIds = Department::where('line_manager_id', auth()->user()->id)->pluck('id', 'id')->toArray();
                }else{
                    $departmentIds  =   auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
                }

                $employee->whereIn('department_id', $departmentIds);
            });
        }
        if (!empty(request()->user_id))
        {
            $users     =   $users->where('id', request()->user_id);
        }
        if (request()->has('department_id'))
        {
            $users     =   $users->whereHas('employee', function ($employee)
            {
                $employee->where('department_id', request()->department_id);
            });
        }
        if (request()->has('shift_id'))
        {
            $users     =   $users->where('shift_type_id', request()->shift_id);
        }
        if (empty(request()->attendanceDateFrom) && empty(request()->attendanceDateTo))
        {
            $fromDate  =   now()->format('Y-m-d');
            $toDate    =   $fromDate;
        }
        if (request()->has('on_full_day'))
        {
            $users->whereHas('leaves', function ($leaves) use ($fromDate, $toDate)
            {
                $leaves->where('is_approved', 1)
                        ->where('from_date', '<=', $fromDate)
                        ->where('to_date', '>=', $toDate)->where('leave_session', 'Full day');
            });
        }
        if (request()->has('on_half_day'))
        {
            $users->whereHas('leaves', function ($leaves) use ($fromDate, $toDate)
            {
                $leaves->where('is_approved', 1)->where(function ($subQuery) use ($fromDate, $toDate)
                {
                    $subQuery->where(function ($query1) use ($fromDate, $toDate)
                    {
                        $query1->where('from_Date', '<=', $fromDate)->where('to_Date', '>=', $fromDate);
                    })->orWhere(function ($query2) use ($fromDate, $toDate)
                    {
                        $query2->whereBetween('from_Date', [$fromDate, $toDate]);
                    });
                })->where('leave_session', '<>', 'Full day');
            });
        }
        $users = $users->with('shiftType', 'leaves');
        return $users;
    }

    private function dashboardCounts($fromDate, $toDate, $data)
    {
        if (empty(request()->attendanceDateFrom) && empty(request()->attendanceDateTo))
        {
            $fromDate   =   now()->format('Y-m-d');
            $toDate     =   $fromDate;
        }
        $yesterday      =   Carbon::createFromFormat('Y-m-d', $fromDate)->subDay()->format('Y-m-d');
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('HR') && !auth()->user()->hasRole('HR Junior'))
        {
            if (auth()->user()->hasRole('Line Manager')) {
                $departmentIds = Department::where('line_manager_id', auth()->user()->id)->pluck('id', 'id')->toArray();
            }else{
                $departmentIds  =   auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
            }


            $data['totalUsers']                 =   User::whereIn('user_type', ['Employee'])
                                                        ->whereHas('employee', function ($employee) use($departmentIds)
                                                        {
                                                            $employee->whereIn('onboard_status', ['Onboard','Training'])
                                                                    ->whereIn('department_id', $departmentIds);
                                                        })->where('is_active', 1)->count();
            $data['totalActiveToday']           =   User::whereIn('user_type', ['Employee'])->where('is_active', 1)
                                                        ->whereHas('employee', function ($employee) use($departmentIds)
                                                        {
                                                            $employee->whereIn('onboard_status', ['Onboard','Training'])
                                                                    ->whereIn('department_id', $departmentIds);
                                                        })
                                                        ->whereDoesNtHave('leaves', function ($leaves) use ($fromDate, $toDate)
                                                        {
                                                            $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                    ->where('is_approved', '1')->where('leave_session', 'Full day');
                                                        })->count();
            $data['totalOnFullDayLeaveToday']   =   User::whereIn('user_type', ['Employee'])->where('is_active', 1)
                                                        ->whereHas('employee', function ($employee) use($departmentIds)
                                                        {
                                                            $employee->whereIn('onboard_status', ['Onboard','Training'])
                                                                    ->whereIn('department_id', $departmentIds);
                                                        })
                                                        ->whereHas('leaves', function ($leaves) use ($fromDate, $toDate)
                                                        {
                                                            $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                    ->where('is_approved', '1')->where('leave_session', 'Full day');
                                                        })->count();
            $data['totalOnHalfDayLeaveToday']   =   User::whereIn('user_type', ['Employee'])->where('is_active', 1)
                                                        ->whereHas('employee', function ($employee) use($departmentIds)
                                                        {
                                                            $employee->whereIn('onboard_status', ['Onboard','Training'])
                                                                     ->whereIn('department_id', $departmentIds);
                                                        })
                                                        ->whereHas('leaves', function ($leaves) use ($fromDate, $toDate)
                                                        {
                                                            $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                    ->where('is_approved', '1')->where('leave_session', '<>', 'Full day');
                                                        })
                                                        ->withCount(['leaves' => function ($leaves) use ($fromDate, $toDate)
                                                        {
                                                            $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                    ->where('is_approved', '1')->where('leave_session', '<>', 'Full day');
                                                        }])->get()->sum('leaves_count');
            $data['todayPunchIn']               =   Attendance::whereHas('user.employee', function ($employee) use($departmentIds)
                                                        {
                                                            $employee->whereIn('onboard_status', ['Onboard','Training'])
                                                                     ->whereIn('department_id', $departmentIds);
                                                        })->whereDate('punch_date', $fromDate)->count();
            $data['yesterdayNotPunchOut']       =   User::where('user_type', '<>', 'external')->where('is_active', 1)
                                                        ->whereHas('employee', function ($employee) use($departmentIds)
                                                        {
                                                            $employee->whereIn('onboard_status', ['Onboard','Training'])
                                                                    ->whereIn('department_id', $departmentIds);
                                                        })
                                                        ->whereDoesNtHave('leaves',function($leaves) use($yesterday)
                                                        {
                                                            $leaves->where('from_date', '<=', $yesterday)->where('to_date', '>=', $yesterday)
                                                                    ->where('leave_session','Full Day');
                                                        })
                                                        ->whereHas('attendances', function ($attendances) use ($yesterday)
                                                        {
                                                            $attendances->where('punch_date', $yesterday)->whereNull('punch_out');
                                                        })->count();
            $data['todayNotPunchIn']            =   User::where('user_type', '<>', 'external')->where('is_active', 1)
                                                        ->whereHas('employee', function ($employee) use($departmentIds)
                                                        {
                                                            $employee->whereIn('onboard_status', ['Onboard','Training'])
                                                                    ->whereIn('department_id', $departmentIds);
                                                        })
                                                        ->whereDoesNtHave('leaves', function ($leaves) use ($fromDate, $toDate)
                                                        {
                                                            $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                    ->where('is_approved', '1')->where('leave_session', 'Full day');
                                                        })
                                                        ->whereDoesNtHave('attendances', function ($attendances) use ($fromDate)
                                                        {
                                                            $attendances->where('punch_date', $fromDate);
                                                        })->count();
            $data['users']      =   User::whereHas('employee', function ($employee) use($departmentIds) {
                                            $employee->whereIn('department_id', $departmentIds);
                                        })->pluck('name', 'id');
        } else {
            $data['totalUsers']             =   User::whereIn('user_type', ['Employee'])->where('is_active', 1)->count();
            $data['totalActiveToday']       =   User::whereIn('user_type', ['Employee'])->where('is_active', 1)
                                                    ->whereDoesNtHave('leaves', function ($leaves) use ($fromDate, $toDate)
                                                    {
                                                        $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                ->where('is_approved', '1')->where('leave_session', 'Full day');
                                                    })->count();
            $data['totalOnFullDayLeaveToday']   =   User::whereIn('user_type', ['Employee'])->where('is_active', 1)
                                                        ->whereHas('leaves', function ($leaves) use ($fromDate, $toDate)
                                                        {
                                                            $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                    ->where('is_approved', '1')->where('leave_session', 'Full day');
                                                        })->count();
            $data['totalOnHalfDayLeaveToday']   =   User::whereIn('user_type', ['Employee'])->where('is_active', 1)
                                                        ->whereHas('leaves', function ($leaves) use ($fromDate, $toDate) {
                                                            $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                    ->where('is_approved', '1')->where('leave_session', '<>', 'Full day');
                                                        })->withCount(['leaves'=> function ($leaves) use ($fromDate, $toDate) {
                                                            $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                    ->where('is_approved', '1')->where('leave_session', '<>', 'Full day');
                                                        }])->get()->sum('leaves_count');
            $data['todayPunchIn']               =   Attendance::whereHas('user', function ($query) {
                                                            $query->where('user_type', 'Employee');
                                                        })->whereDate('punch_date', $fromDate)->count();
            $data['todayNotPunchIn']            =   User::whereIn('user_type', ['Employee'])->whereHas('shiftType', function ($query) {
                                                            $query->where('name', 'Morning');
                                                        })
                                                        ->whereDoesNtHave('leaves', function ($leaves) use ($fromDate, $toDate) {
                                                            $leaves->where('from_date', '<=', $fromDate)->where('to_date', '>=', $toDate)
                                                                    ->where('is_approved', '1')->where('leave_session', 'Full day');
                                                        })
                                                        ->whereDoesNtHave('attendances', function ($attendances) use ($fromDate) {
                                                            $attendances->where('punch_date', $fromDate);
                                                        })->count();
            $data['yesterdayNotPunchOut']       =   User::whereIn('user_type', ['Employee'])
                                                        ->whereDoesNtHave('leaves',function($leaves) use($yesterday){
                                                            $leaves->where('from_date', '<=', $yesterday)->where('to_date', '>=', $yesterday)
                                                                    ->where('leave_session','Full Day');
                                                        })
                                                        ->whereHas('attendances', function ($attendances) use ($yesterday) {
                                                            $attendances->where('punch_date', $yesterday)->whereNull('punch_out');
                                                        })->count();
            $data['users']                      =   User::with('leaves')->whereHas('employee')->pluck('name', 'id');
        }
        return $data;
    }

    public function removeAttendance()
    {
        $attendances        =       Attendance::whereNotNull('punch_in')->whereNotNull('punch_out')
                                                ->where('punch_date', now()->subDay()->format('Y-m-d'))->get();
        foreach ($attendances as $attendance)
        {
            $time1          =       strtotime($attendance->punch_in);
            $time2          =       strtotime($attendance->punch_out);
            $difference     =       round(abs($time2 - $time1) / 3600, 2);

            if ($difference < 3)
            {
                $attendance->delete();
            }
        }
        return "done";
    }

    public function updateSeen($id)
    {
        Attendance::find($id)->update(['seen_at' => '1']);
    }
}
