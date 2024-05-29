<?php

namespace App\Http\Controllers\ems;

use App\Models\Employee;
use App\Models\Attendance;
use App\Http\Controllers\Controller;

class PerformanceDashboardController extends Controller
{
    public function dashboard()
    {

        $this->authorize('hrEmployeeList', new Employee());
        $startOfMonth   =   empty(request()->attendanceDateFrom) ? now()->startOfMonth() :request()->attendanceDateFrom;
        $today          =   empty(request()->attendanceDateTo) ? now() :request()->attendanceDateTo;
        $attendances    =   Attendance::with('user.employee.department','shiftType')->whereHas('user',function($users){
               $users->where('user_type','Employee')->whereHas('shiftType',function($shiftType){
                   $shiftType->where('name','Morning');
               });
        })->whereBetween('punch_date', [$startOfMonth, $today])->select('user_id', 'punch_date', 'in', 'out')
            ->where('in', '<>',null)->get();
        $data         =  $this->departmentDashboard($attendances);
        // top early and late comers
        $earlyComers    =   $attendances->where('in', '>', '0')->groupBy('user.email');
        $lateComers     =   $attendances->where('in', '<', '0')->groupBy('user.email');
        $topEarlyComers =   [];
        $topLateComers  =   [];
        foreach ($earlyComers as $earlyAttendances) {
            $user                                   =   $earlyAttendances->first()->user;
            $user->early_minutes                    =   $earlyAttendances->sum('in');
            $topEarlyComers[]                       =   $user;
        }
        foreach ($lateComers as $lateAttendances) {
            $user                                   =   $lateAttendances->first()->user;
            $user->late_minutes                     =   abs($lateAttendances->sum('in'));
            $topLateComers[]                        =   $user;
        }
        $data['topEarlyComers']                     =   collect($topEarlyComers)->sortByDesc('early_minutes')->take(10);
        $data['topLateComers']                      =   collect($topLateComers)->sortByDesc('late_minutes')->take(10);
        // ended here
        // top late and early going
        $lateGoings      =   $attendances->where('out', '>', '0')->where('out', '<>',null)->groupBy('user.email');
        $earlyGoings     =   $attendances->where('out', '<', '0')->where('out', '<>',null)->groupBy('user.email');
        $topLateGoings   =   [];
        $topEarlyGoings  =   [];
        foreach ($lateGoings as $lateGoing) {
            $user                                   =   $lateGoing->first()->user;
            $user->late_going_minutes               =   $lateGoing->sum('out');
            $topLateGoings[]                        =   $user;
        }
        foreach ($earlyGoings as $earlyGoing) {
            $user                                   =   $earlyGoing->first()->user;
            $user->early_going_minutes              =   abs($earlyGoing->sum('out'));
            $topEarlyGoings[]                       =   $user;
        }
        // ended here
        $data['topEarlyComers']                     =   collect($topEarlyComers)->sortByDesc('early_minutes')->take(10);
        $data['topLateComers']                      =   collect($topLateComers)->sortByDesc('late_minutes')->take(10);
        $data['topLateGoings']                      =   collect($topLateGoings)->sortByDesc('late_going_minutes')->take(10);
        $data['topEarlyGoings']                     =   collect($topEarlyGoings)->sortByDesc('early_going_minutes')->take(10);
        return view('hr.performanceDashboard', $data);
    }

    public function departmentDashboard($attendances)
    {
        $earlyDepartmentComers      =   $attendances->where('in', '>', '0')->groupBy('user.employee.department.name');
        $lateDepartmentComers       =   $attendances->where('in', '<', '0')->groupBy('user.employee.department.name');
        $topEarlyDepartmentComers   =   [];
        $topLateDepartmentComers    =   [];
        foreach ($earlyDepartmentComers as $name => $earlyDepartmentComer) {
            $topEarlyDepartmentComers[$name]        =   $earlyDepartmentComer->sum('in');

        }
        foreach ($lateDepartmentComers as $name => $lateDepartmentComer) {
            $topLateDepartmentComers[$name]         =   abs($lateDepartmentComer->sum('in'));

        }
        arsort($topEarlyDepartmentComers);
        arsort($topLateDepartmentComers);
        $topEarlyDepartmentComers  =   array_slice($topEarlyDepartmentComers,0,3);
        $topLateDepartmentComers   =   array_slice($topLateDepartmentComers,0,3);
        $lateDepartmentGoings      =   $attendances->where('out', '>', '0')->where('out', '<>',null)->groupBy('user.employee.department.name');
        $earlyDepartmentGoings     =   $attendances->where('out', '<', '0')->where('out', '<>',null)->groupBy('user.employee.department.name');
        $topLateDepartmentGoings   =   [];
        $topEarlyDepartmentGoings  =   [];
        foreach ($lateDepartmentGoings as $name => $lateDepartmentGoing) {
            $topLateDepartmentGoings[$name]         =   abs($lateDepartmentGoing->sum('out'));

        }
        foreach ($earlyDepartmentGoings as $name => $earlyDepartmentGoing) {
            $topEarlyDepartmentGoings[$name]         =   abs($earlyDepartmentGoing->sum('out'));

        }
        arsort($topLateDepartmentGoings);
        arsort($topEarlyDepartmentGoings);

        $topLateDepartmentGoings          =   array_slice($topLateDepartmentGoings,0,3);
        $topEarlyDepartmentGoings         =   array_slice($topEarlyDepartmentGoings,0,3);
        $data['topEarlyDepartmentComers'] =   $topEarlyDepartmentComers;
        $data['topLateDepartmentComers']  =   $topLateDepartmentComers;
        $data['topLateDepartmentGoings']  =   $topLateDepartmentGoings;
        $data['topEarlyDepartmentGoings'] =   $topEarlyDepartmentGoings;
        return $data;
    }


}
