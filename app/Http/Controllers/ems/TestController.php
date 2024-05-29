<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\BankDetail;
use App\Models\DailyReport;
use Illuminate\Support\Str;
use App\Exports\LeaveExport;
use App\Models\LeaveBalance;
use App\Models\LiveAttendance;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\UnloggedSecondHalvesNotification;

class TestController extends Controller
{
    // public function index()
    // {

    //       $bank_details = BankDetail::all();
    //      foreach($bank_details as $bankDetail)
    //      {
    //          $bankDetail->account_holder= $bankDetail->account_holder;
    //          $bankDetail->ifsc_code= $bankDetail->ifsc_code;
    //          $bankDetail->account_no= $bankDetail->account_no;
    //          $bankDetail->bank_name= $bankDetail->bank_name;

    //          $bankDetail->save();

    //      }
    // $today            = Carbon::today()->setTimezone('Asia/Kolkata')->format('Y-m-d');
    // $liveAttendances    =   LiveAttendance::where('Empcode','MS20304')
    // ->whereIn('Shortname', ['IN-JAL-1',  'IN-JAL-2'])
    // ->get();
    // dd($liveAttendances);
    // $users = User::with('employee.department')->where('user_type','Employee')->whereHas('employee',function($employee){
    //     $employee->whereDoesNtHave('draftProfiles',function($query){
    //     $query->where('field_name','asset_policy');
    //     })->orWhereHas('draftProfiles',function($query){
    //     $query->where('field_name','asset_policy')->where('is_approved',0);
    //     });
    //     })->get();
    //     return Excel::download(new LeaveExport($users),'assetPolicy.xlsx');
    // }

    public function test2()
    {
        // $managerLeaveFrom='2023-04-15'; //Manager leave start 
        // $managerLeaveTo='2023-04-17';//Manager leave end
        // $myLeaveFrom='2023-04-16';
        // $myLeaveTo='2023-04-18';

        // if($managerLeaveFrom <= $myLeaveFrom && $managerLeaveTo >= $myLeaveFrom){

        // }

        // Define the dates 
        $myLeaveFrom = '2023-04-15';
        $managerLeaveFrom = '2023-04-12';
        $managerLeaveTo = '2023-04-17';
        // Create Carbon instances 
        $carbonDateToCheck = Carbon::parse($myLeaveFrom);
        $carbonStartDate = Carbon::parse($managerLeaveFrom);
        $carbonEndDate = Carbon::parse($managerLeaveTo);
        // Check if the date is between the start and end dates 
        $isBetween = $carbonDateToCheck->between($carbonStartDate, $carbonEndDate);
        if ($isBetween) {
            echo 'The date ' . $myLeaveFrom . ' is between ' . $managerLeaveFrom . ' and ' . $managerLeaveTo;
        } else {
            echo 'The date ' . $myLeaveFrom . ' is NOT between ' . $managerLeaveFrom . ' and ' . $managerLeaveTo;
        }

        // if (($manager->from_date <= $leave->from_date && $manager->to_date >= $leave->to_date))
        //     {
        //         $this->autoForwardedLeaveNotification($manager, $hr, $leave);
        //     }

        ini_set('max_execution_time', '-1');
        $users              =   User::whereHas('employee', function ($query) {
            $query->whereNotNull('contract_date');
        })->with('employee')
            ->where('id', 120)
            ->get();
        $currentMonth       =   Carbon::now()->startOfMonth();
        $lastMonth          =   Carbon::now()->startOfMonth()->subMonth();
        foreach ($users as $user) {
            $joinMonth      =   Carbon::createFromFormat('Y-m-d', $user->employee->contract_date);
            if ($joinMonth->format('d') > 14) {
                $diff   =   $joinMonth->diffInDays($currentMonth);
                if ($diff < 20) {
                    continue;
                }
            }
            $leaveBalance               =   LeaveBalance::where('user_id', $user->id)->whereYear('month', $currentMonth)->whereMonth('month', $currentMonth)->first();
            $lastMonthLeaveBalance      =   LeaveBalance::where('user_id', $user->id)->whereYear('month', $lastMonth)->whereMonth('month', $lastMonth)->first();
            if (empty($leaveBalance)) {
                $leaveBalance               =   new LeaveBalance();
                $leaveBalance->balance      =   1.25;
                $leaveBalance->month        =   $currentMonth->format('Y-m-d');
                $leaveBalance->user_id      =   $user->id;
                if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
                    $leaveBalance->balance  = $lastMonthLeaveBalance->balance + $leaveBalance->balance;
                    $lastMonthLeaveBalance->is_forwarded    =   1;
                    $lastMonthLeaveBalance->save();
                }
            } else {
                $lastMonthBalance                       =   0;
                if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
                    $lastMonthBalance   =   $lastMonthLeaveBalance->balance;
                    $lastMonthLeaveBalance->is_forwarded    =   1;
                    $lastMonthLeaveBalance->save();
                }
                $deductibleBalance  =   $leaveBalance->balance + $lastMonthBalance + 1.25;
                $leftBalance        =   0;
                $whole              = intval($deductibleBalance);
                $decimal1           = $deductibleBalance - $whole;
                $decimal2           = round($decimal1, 2);
                $decimal            = substr($decimal2, 2);
                if ($decimal == '25' || $decimal == '75') {
                    $deductibleBalance    =   $deductibleBalance - 0.25;
                    $leftBalance          =   0.25;
                }


                $balance            =   $leaveBalance->deduction - $deductibleBalance;
                if ($balance <= 0) {
                    $leaveBalance->balance      =    $leftBalance + abs($balance);
                    $leaveBalance->deduction    =   0;
                } else {
                    $leaveBalance->balance      =   $leftBalance;
                    $leaveBalance->deduction    =   $balance;
                }
            }
            $leaveBalance->save();
        }
        dd('dsds');
    }

    public function syncLeaves()
    {
        ini_set('max_execution_time', '-1');
        $leaves     =       Leave::with('employee')->get();
        foreach ($leaves as $leave) {

            $leave_type_id  =   LeaveType::where('name', $leave->leave_nature)->first()->id;
            if (Str::contains($leave->leave_type, 'Full')) {
                $leaveSession   =   'Full day';
            } else {
                $leaveSession   =   Str::before(Str::after($leave->leave_type, '('), ')');
            }

            $leave->leave_type_id   =   $leave_type_id;
            $leave->leave_session   =   $leaveSession;
            $leave->user_id         =   $leave->employee->user_id ?? null;
            $leave->save();
        }
        dd('done sync');
    }


    public function checkForUnloggedSecondHalvesjoins()
    {
         $today = '2023-05-15';
        //  $today = Carbon::today()->format('Y-m-d');
        // Fetch employees who have taken first-half leave and did not take second half and also did not punch in the second half
        $employees = Employee::join('users', 'employee.user_id', '=', 'users.id')
            ->leftJoin('leaves as l1', function ($join) use ($today) { // First Half Approved
                $join->on('users.id', '=', 'l1.user_id')
                    ->where('l1.from_date', '<=', $today)
                    ->where('l1.to_date', '>=', $today)
                    ->where('l1.leave_session', '=', 'First half')
                    ->where('l1.is_approved', '=', 1);
            })
            ->leftJoin('leaves as l2', function ($join) use ($today) { // Second Half Leave Applied
                $join->on('users.id', '=', 'l2.user_id')
                    ->where('l2.from_date', '<=', $today)
                    ->where('l2.to_date', '>=', $today)
                    ->where('l2.leave_session', '=', 'Second half');
                    // ->where('l2.is_approved', '=', 1);
            })
            ->leftJoin('employee_attendance', function ($join) use ($today) { // Second Half Attendance
                $join->on('users.id', '=', 'employee_attendance.user_id')
                    ->whereDate('punch_date', '=', $today)
                    ->whereBetween('punch_in', [Carbon::createFromFormat('Y-m-d H:i:s', $today . ' 12:00:00', 'Asia/Kolkata'), Carbon::createFromFormat('Y-m-d H:i:s', $today . ' 14:30:00', 'Asia/Kolkata')]);
            })
            ->whereNull('l2.id') //selecting those employees whose second half leave (l2) is null (Second Half Leave not applied).
            ->whereNotNull('l1.id')//selecting those employees whose first half leave (l1) is approved . (Employee on First Half - Approved)
            ->whereNull('employee_attendance.id')//selecting those whose attendance record for the given $today date is null.
            ->select('employee.*')
            ->with('user', 'department.deptManager')
            ->get();
        //  dd($employees);
        foreach ($employees as $employee) {
            $manager = $employee->department->deptManager;
            if ($manager) {
                $selectedHrUsers = User::whereIn('id', [22, 392])->pluck('email', 'email')->toArray();
                $manager->user->notify(new UnloggedSecondHalvesNotification($today, $employee, $selectedHrUsers));
            }
        }
        dd($employees);
    }

    public function checkForUnloggedSecondHalves()
    {
            $today = '2023-05-15';
        // $today = Carbon::today()->format('Y-m-d');
        // Fetch employees who have taken first-half leave and did not take second half and also did not punch in the second half
        $employees = Employee::whereHas('user', function ($query) use ($today) {
            //users whose leaves are active during the current $today date's first half
            $query->whereHas('leaves', function ($q) use ($today) { 
                $q->where('from_date', '<=', $today)
                    ->where('to_date', '>=', $today)
                    ->where('leave_session', 'First half')
                    ->where('is_approved', 1);
            })
                 //users who do not have an active leave during the current $today date's second half
                ->whereDoesntHave('leaves', function ($q) use ($today) { 
                    $q->where('from_date', '<=', $today)
                        ->where('to_date', '>=', $today)
                        ->where('leave_session', 'Second half')
                        ->where('is_approved', 1);
                })
                //Here, we're checking for employees who did not punch-in after 2 PM on the current date
                ->whereDoesntHave('attendances', function ($q) use ($today) {
                    $q->whereDate('punch_date', $today)
                    ->whereBetween('punch_in', [Carbon::createFromFormat('Y-m-d H:i:s', $today . ' 12:00:00', 'Asia/Kolkata'), Carbon::createFromFormat('Y-m-d H:i:s', $today . ' 14:30:00', 'Asia/Kolkata')]);
                });
        })
            ->with('user', 'department')
            ->get();
        //  dd($employees);
        foreach ($employees as $employee) {
            $managerId = $employee->department->manager_id;
            if ($managerId) {
                Employee::find($managerId)->user->notify(new UnloggedSecondHalvesNotification($today, $employee));
            }
        }

        dd($employees);
    }


    
    public function syncAttendanceTime()
    {
        $attendances =  Attendance::with('user.shiftType')->get();
        ini_set('max_execution_time', '-1');
        foreach ($attendances as $attendance) {
            if (empty($attendance->user) || empty($attendance->user->shiftType)) {
                continue;
            }
            $shiftType  =   $attendance->user->shiftType;
            $leaves     =   Leave::where('user_id', $attendance->user_id)->where('is_approved', '1')->where('status', '<>', 'Cancelled')->where(function ($subQuery) use ($attendance) {
                $subQuery->where(function ($query1) use ($attendance) {
                    $query1->where('from_Date', '<=', $attendance->punch_date)->where('to_Date', '>=', $attendance->punch_date);
                })->orWhere(function ($query2) use ($attendance) {

                    $query2->whereBetween('from_Date', [$attendance->punch_date, $attendance->punch_date]);
                });
            })->get();
            if ($leaves->isNotEmpty()) {

                foreach ($leaves as $leave) {
                    if ($leave->leave_session == "Second half") {
                        $startTime              =   Carbon::parse($shiftType->start_time);
                        $inTime                 =   Carbon::parse($attendance->punch_in);
                        $endTime                =   Carbon::parse($shiftType->mid_time);
                        $outTime                =   Carbon::parse($attendance->punch_out);
                    } else {
                        $startTime              =   Carbon::parse($shiftType->mid_time);
                        $inTime                 =   Carbon::parse($attendance->punch_in);
                        $endTime                =   Carbon::parse($shiftType->end_time);
                        $outTime                =   Carbon::parse($attendance->punch_out);
                    }
                }
            } else {

                $startTime              =   Carbon::parse($shiftType->start_time);
                $inTime                 =   Carbon::parse($attendance->punch_in);
                $endTime                =   Carbon::parse($shiftType->end_time);
                $outTime                =   Carbon::parse($attendance->punch_out);
            }
            if (!empty($attendance->punch_in)) {
                if ($inTime < $startTime) {

                    $attendance->in         =   $inTime->diffInMinutes($startTime);
                } else {
                    $attendance->in         =   -$inTime->diffInMinutes($startTime);
                }
            }
            if (!empty($attendance->punch_out)) {
                if ($outTime > $endTime) {
                    $attendance->out        =   $outTime->diffInMinutes($endTime);
                } else {
                    $attendance->out        =  -$outTime->diffInMinutes($endTime);
                }
            }
            $attendance->save();
        }
        dd('attendance sync');
    }

    public function sortAttendanceTime()
    {
        $users    =   User::whereHas('employee', function ($employee) {
            $employee->whereBetween('contract_date', [now()->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')]);
        })->with(['attendances' => function ($attendances) {
            $attendances->orderBy('punch_date', 'asc');
        }])->get();
        foreach ($users as $user) {
            dd($users);
            if ($user->attendances->isEmpty()) {
                continue;
            }
            dd($user->attendances->first());
            $user->attendances->first()->update(['punch_in' => '09:00:00', 'in' => 0]);
        }
        dd('done');
    }

    public function tester()
    {

        $departmentIds      =   auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
        $employees          =   Employee::with('user.shiftType', 'assetAssignments')->select('id', 'user_id', 'name')
            ->whereIn('department_id', $departmentIds)->get();
    }

    public function test234()
    {
        $attendances =  Attendance::whereNotNull('punch_in')->whereNotNull('punch_out')->where('punch_date', now()->subDay()->format('Y-m-d'))->get();
        foreach ($attendances as $attendance) {
            $time1 = strtotime($attendance->punch_in);
            $time2 = strtotime($attendance->punch_out);
            $difference = round(abs($time2 - $time1) / 3600, 2);
            if ($difference < 3) {
                $attendance->delete();
            }
        }
    }

    public function dailyReportId()

    {

        ini_set('max_execution_time', '-1');

        $reports        =       DailyReport::with('employee')->get();

        // dd($reports  );

        foreach ($reports as $report) {


            $employee       = Employee::withoutGlobalScope('is_active')->where('id', $report->employee_id)->first();

            $report->user_id  = !empty($employee->user_id) ? $employee->user_id : null;

            $report->save();
        }

        dd('done');
    }

    public function test()
    {
        Browsershot::html(view('test')->render())->save('example.jpeg');
    }

    public function index()
    {
        die;
    }

    public function fixMontlyLeaveBalance()
    {
        die;
        // $user_id    = 312 ; //Hardeep

        // Delete Leave balance entry for this month

        // Step 1
        // $dateFrom   = '2023-03-21'; 
        // $dateTo     = '2023-03-31';

        // step 2
        $dateFrom   = '2023-04-01';
        $dateTo     = '2023-04-20'; // end of month
        $userLeaves = Leave::whereBetween('from_date', [$dateFrom, $dateTo])->whereIn('status', ['Approved', 'Pre Approved', 'Pending'])->get()->groupBy('user_id');

        foreach ($userLeaves as $user_id => $leaves) {

            echo $user_id;
            echo "<br>";

            // dd($user_id, $leaves);
            LeaveBalance::where('user_id', $user_id)->whereMonth('month', '04')->whereYear('month', '2023')->delete();
            (new CalculateBalanceController())->calculateBalance($user_id);

            // dd($leaves);
            foreach ($leaves as $leave) {

                $carbonDate             =   Carbon::createFromFormat('Y-m-d', $leave->from_date);
                $duration               =   $leave->duration;
                $appliedAt              =   Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d');
                $cutOffDate             =   Carbon::parse($leave->created_at)->startOfMonth()->addDays(19); // means leave created date

                if ($leave->status == 'Approved' || $leave->from_date == $appliedAt) {
                    $this->approvalDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
                } else {
                    $this->preApprovalBalance($leave, $carbonDate, $duration, $cutOffDate);
                }
            }
        }
    }

    private function preApprovalBalance($leave, $carbonDate, $duration, $cutOffDate)
    {
        $fromMonth                  =   Carbon::createFromFormat('Y-m-d', $leave->from_date)->format('m');
        $cutOffDateMonth            =   Carbon::parse($leave->created_at)->format('m');
        if ($fromMonth != $cutOffDateMonth) {
            $cutOffDate         =   Carbon::parse($leave->created_at)->startOfMonth()->addMonth(1)->addDays(19);
        }
        $getBalance     =   $this->getBalance($leave, $carbonDate);
        // dd($getBalance);
        $leaveBalance   =   $getBalance['leaveBalance'];
        if (empty($leaveBalance)) {
            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =   $leave->user_id;
            if ($leave->from_date < $cutOffDate->format('Y-m-d')) {
                $leaveBalance->pre_approval_deduction       =   $duration;
            } elseif ($leave->from_date > $cutOffDate->format('Y-m-d')) {
                $leaveBalance->next_month_deduction =   $duration;
            }
        } else {
            $deductibleBalance  =   $getBalance['deductibleBalance'];
            $leftBalance        =   $getBalance['leftBalance'];

            if ($fromMonth == '03') { // Arsh Hard Code
                $finalBalance = $deductibleBalance;
                $leaveBalance->taken_leaves            =   $leaveBalance->taken_leaves;
            } else {
                $finalBalance                          =   $deductibleBalance           -   $duration;
                $leaveBalance->taken_leaves            =   $leaveBalance->taken_leaves  +   $duration;
            }
            // dd($finalBalance);
            if ($finalBalance >= 0) {
                $leaveBalance->balance             =   $finalBalance  + $leftBalance;
            } else {
                // dd($leftBalance);
                $leaveBalance->balance             =   $leftBalance;
                if ($leave->from_date <= $cutOffDate) { // if this month deduction
                    $leaveBalance->pre_approval_deduction           =   $leaveBalance->pre_approval_deduction + abs($finalBalance);
                } else {
                    $leaveBalance->next_month_deduction       =    $leaveBalance->next_month_deduction + abs($finalBalance);
                }
            }
        }
        $leaveBalance->save();
    }

    private function approvalDeduction($leave, $carbonDate, $duration, $cutOffDate)
    {
        $fromMonth                  =   Carbon::createFromFormat('Y-m-d', $leave->from_date)->format('m');
        $cutOffDateMonth            =   Carbon::parse($leave->created_at)->format('m');
        if ($fromMonth != $cutOffDateMonth) {
            $cutOffDate         =   Carbon::parse($leave->created_at)->startOfMonth()->addMonth(1)->addDays(19);
        }
        $getBalance     =   $this->getBalance($leave, $carbonDate);
        $leaveBalance   =   $getBalance['leaveBalance'];
        // same month deduction
        if (empty($leaveBalance)) {
            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =   $leave->user_id;
            if ($leave->from_date < $cutOffDate) {
                $leaveBalance->deduction       =   $duration;
            }
            // deduction from next month after 20th leave apply if balance is less then duration
            elseif ($leave->from_date > $cutOffDate) {
                $leaveBalance->next_month_deduction =   $duration;
            }
        } else {

            // if ($fromMonth != '03') {
            $leaveBalance->taken_leaves            =   $leaveBalance->taken_leaves  +   $duration;
            // }
            if ($leave->from_date <= $cutOffDate) {
                $leaveBalance->deduction       +=      $duration;
            }
            // deduction from next month after 20th leave apply if balance is less then duration
            elseif ($leave->from_date > $cutOffDate) {
                $leaveBalance->next_month_deduction =                   $leaveBalance->next_month_deduction +   $duration;
            }
        }
        $leaveBalance->save();
    }

    private function getBalance($leave, $carbonDate)
    {
        $leaveBalance       =   LeaveBalance::whereMonth('month', $carbonDate)->whereYear('month', $carbonDate)->where('user_id', $leave->user_id)->first();
        // dd($leaveBalance);  
        $deductibleBalance  =   0;
        $leftBalance        =   0;
        if (!empty($leaveBalance) && $leaveBalance->balance > 0) {
            $balance    = $leaveBalance->balance;
            $whole      = intval($balance);
            $decimal1   = $balance - $whole;
            $decimal2   = round($decimal1, 2);
            $decimal    = substr($decimal2, 2);
            if ($decimal != '25' && $decimal != '75') {
                $deductibleBalance    =   $leaveBalance->balance;
                // dd($whole, $decimal1, $decimal2, $decimal, $deductibleBalance);
                $leftBalance          =   0;
            } else {
                $deductibleBalance    =   $leaveBalance->balance - 0.25;
                $leftBalance          =   0.25;
            }
        }
        // dd($deductibleBalance);
        $data['deductibleBalance']      =   $deductibleBalance;
        $data['leftBalance']            =   $leftBalance;
        $data['leaveBalance']           =   $leaveBalance;
        return $data;
    }
}
