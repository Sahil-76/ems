<?php

namespace App\Http\Controllers\ems;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveBalance;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

  public function index()
  {
    ini_set('max_execution_time', -1);
    ini_set("memory_limit", "-1");
    $data                     =   [];
    $user                     =   auth()->user();
    $input                    =   request()->all();
    if ($user->can('hrDashboard', $user)) {
      $data['hr']             =   $this->hrDashboard();
    }
    $data['leaveDashboard']   = $this->leaveDashboard($input);
    $data['myLeaveDashboard'] = $this->myLeaveDashboard();
    $data['recentJoining']    = Employee::where('is_active', '1')->whereDate('join_date', '>=',  Carbon::now()->startOfMonth())
      ->whereDate('join_date', '<=', Carbon::now()->endOfMonth())->count();
    if (!empty(auth()->user()->employee->birth_date)) {
      $birthDate = Carbon::parse(auth()->user()->employee->birth_date)->format('M-d');
      $today     = Carbon::now()->format('M-d');
      if ($birthDate == $today) {
        $data['employeeBirthday'] = auth()->user()->employee;
      }
    }
    $data['start']              = Carbon::now()->startOfMonth()->format('Y-m-d');
    $data['end']                = Carbon::now()->endOfMonth()->format('Y-m-d');
    $today                      = Carbon::now()->format('Y-m-d');
    $data['todayAttendance']    = Attendance::where('user_id', auth()->user()->id)->where('punch_date', $today)->first();
    $month                      = Carbon::now()->format('m');
    $year                       = Carbon::now()->format('Y');
    
    $data['myBalance']          = LeaveBalance::with('user')->where('user_id', auth()->user()->id)->whereYear('month', $year)->whereMonth('month', $month)->first();
    return view('dashboard', $data);
  }

  public function hrDashboard()
  {
    $employees                       =    Employee::select('id', 'is_active', 'user_id')->withoutGlobalScopes(['guest'])->whereHas('user', function ($user) {
                                          $user->where('user_type', 'Employee');
                                          })->whereIn('onboard_status', ['Onboard', 'Training'])->count("id");
    $data['employeeCount']           =    $employees;
    $data['in_active']               =    Employee::withoutGlobalScopes()->where('is_active', 0)->count("id");
    $data['department']              =    Department::count("id");
    $data['profilesPendingCount']    =    Employee::select("is_active", "user_id")->where('is_active', '1')->whereHas('user', function ($query) {
                                            $query->where('user_type', 'Employee');
                                          })->where(function ($query) {
                                              $query->whereDoesNtHave('documents')->orWhereDoesNtHave('employeeEmergencyContact')
                                                    ->orWhereDoesNtHave('user',function($user){
                                                          $user->where('profile_pic','<>',null);
                                                      });
                                          })->count("id");
    return $data;
  }

  public function leaveDashboard($input = null)
  {
    if (!empty($input))
    {
      $from_date  =     $input['dateFrom'];
      $to_date    =     $input['dateTo'];
      $from_date  =     Carbon::parse($from_date)->format('Y-m-d');
      $to_date    =     Carbon::parse($to_date)->format('Y-m-d');
    } else
    {
      $today_date =     Carbon::now()->format('Y-m-d');
      $from_date  =     $today_date;
      $to_date    =     $from_date;
    }
    $data['leaves'] =  Leave::select("employee.name as name","employee.biometric_id as biometric_id"
                       ,"departments.name as department_name","shift_types.name as shift_types_name","leaves.status as leave_status",
                       "leaves.leave_session","shift_types.start_time","shift_types.end_time","shift_types.mid_time")
                       ->with("user.shiftType")->leftJoin("users", "users.id", "=", "leaves.user_id")->
                       leftJoin("employee", "employee.user_id", "=", "users.id")->
                       leftJoin("departments", "departments.id", "=", "employee.department_id")->
                       leftJoin("shift_types", "shift_types.id", "=", "employee.shift_type_id")->
                       orderBy('departments.name')->where(function ($query) use ($from_date, $to_date) {
                       $query->whereDate('from_date', '<=', $from_date)->whereDate('to_date', '>=', $to_date);
                       })->where('status', '!=', 'Cancelled')->where('is_approved', '1')->orderBy("department_name")->get();

    return $data;
  }

  public function myLeaveDashboard()
  {

    if (empty(auth()->user())) {
      return [];
    }
    $leaves   = Leave::with('user.employee')->where('user_id',auth()->user()->id)
                      ->whereYear('from_date',Carbon::now()->year)->whereMonth('from_date', Carbon::now()->month)
                      ->where('is_approved', '1')->get();
    $data['totalLeaves']    = $leaves->sum('duration');
    return $data;
  }
}
