<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Mail\Action;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Exports\LeaveBalanceExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LeaveBalanceComplaint;
use Illuminate\Contracts\Mail\Mailer;
use App\Http\Requests\LeaveBalanceRequest;

class LeaveBalanceController extends Controller
{

    public function __construct(Mailer $mailer)
    {
        $this->mailer       =       $mailer;
    }

    public function dashboard(Request $request, $export = false)
    {
        $this->authorize('hrEmployeeList', new Employee());
        $date                   =   empty($request->month) ? Carbon::now()->startOfMonth() : Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $leaveBalances          =   LeaveBalance::with([
            'user.employee.department',
            'leaveBalanceComplaints' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->whereHas('user', function ($user) {
            $user->where('user_type', 'Employee')->where('is_active', '1')->has('employee');
        });
        $employees             =   User::with('employee.department')->where('is_active', 1)->where('user_type', 'Employee')->whereHas('employee', function ($employee) {
            $employee->select('biometric_id', 'name');
        });
        if (!empty($request->user_id)) {
            $leaveBalances->where('user_id', $request->user_id);
        }
        if (!empty($request->previous_query)) {
            $leaveBalances->whereHas('leaveBalanceComplaints', function ($query) use ($date) {
                $query->whereYear('created_at', $date);
            });
        }
        if (!empty($request->has_complaint)) {
            $leaveBalances->whereHas('leaveBalanceComplaints', function ($query) use ($date) {
                $query->where('is_responded', 0)->whereYear('created_at', $date)->orderBy('created_at')->whereColumn('user_id', 'leave_balances.user_id');
            });
        }
        if ((empty($request->previous_query) && empty($request->has_complaint) || !empty($request->month))) {
            $leaveBalances->whereMonth('month', $date)->whereYear('month', $date);
        }
        if (!empty($request->department_id)) {
            $leaveBalances->whereHas('user.employee', function ($user) {
                $user->where('department_id', request()->department_id);
            });

            $employees     =   User::with('employee')->where('is_active', 1)->where('user_type', 'Employee')->whereHas('employee', function ($employee) {
                $employee->select('biometric_id', 'name')->where('department_id', request()->department_id);
            });
        }
        $data['employeeDepartments']      =   $employees->get()->groupBy('employee.department.name');;
        $data['departments']            =   Department::pluck('name', 'id')->toArray();
        $leaveBalances                  =   $leaveBalances;
        if ($export) {
            return $leaveBalances->get();
        }
        $start                  =   Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        $end                    =   Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $data['leaveBalances']  =   $leaveBalances->get();
        $data['start']          =   $start;
        $data['end']            =   $end;
        $data['date']           =   $date;
        return view('leave.balanceDashboard', $data);
    }

    public function edit($id, Request $request)
    {
        $this->authorize('hrEmployeeList', new Employee());
        if (!empty($request->user_id)) {
            $month                                  =   Carbon::createFromFormat('Y-m-d', $request->month);
            $leaveBalance                           =   LeaveBalance::with('leaveBalanceComplaints')
                ->where('user_id', $request->user_id)->whereMonth('month', $month)->first();
            $beforeCutOffDate1                          =   Carbon::createFromFormat('Y-m-d', $request->month)->startOfMonth();
            $beforeCutOffDate2                          =   Carbon::createFromFormat('Y-m-d', $request->month)->startOfMonth()->addDays(20);
            $afterCutOffDate1                           =   Carbon::createFromFormat('Y-m-d', $request->month)->startOfMonth()->addDays(21);
            $afterCutOffDate2                           =   Carbon::createFromFormat('Y-m-d', $request->month)->endOfMonth();
        } else {
            $leaveBalance                               =   LeaveBalance::with('leaveBalanceComplaints')->findOrFail($id);
            $beforeCutOffDate1                          =   Carbon::createFromFormat('Y-m-d', $leaveBalance->month)->startOfMonth();
            $beforeCutOffDate2                          =   Carbon::createFromFormat('Y-m-d', $leaveBalance->month)->startOfMonth()->addDays(20);
            $afterCutOffDate1                           =   Carbon::createFromFormat('Y-m-d', $leaveBalance->month)->startOfMonth()->addDays(21);
            $afterCutOffDate2                           =   Carbon::createFromFormat('Y-m-d', $leaveBalance->month)->endOfMonth();
        }
        $beforeCutOffLeaves                         =   Leave::where("user_id", $leaveBalance->user_id)->whereNotIn('status', ['Rejected', 'Cancelled'])->where(function ($subQuery) use ($beforeCutOffDate1, $beforeCutOffDate2) {
            $subQuery->where(function ($query1) use ($beforeCutOffDate1, $beforeCutOffDate2) {
                $query1->where('from_Date', '<=', $beforeCutOffDate1)->where('to_Date', '>=', $beforeCutOffDate1);
            })->orWhere(function ($query2) use ($beforeCutOffDate1, $beforeCutOffDate2) {
                $query2->whereBetween('from_Date', [$beforeCutOffDate1, $beforeCutOffDate2]);
            });
        })->select("from_date", "to_date", "user_id", "leave_session")->get();
        $afterCutOffLeaves                         =   Leave::where("user_id", $leaveBalance->user_id)->whereNotIn('status', ['Rejected', 'Cancelled'])->where(function ($subQuery) use ($afterCutOffDate1, $afterCutOffDate2) {
            $subQuery->where(function ($query1) use ($afterCutOffDate1, $afterCutOffDate2) {
                $query1->where('from_Date', '<=', $afterCutOffDate1)->where('to_Date', '>=', $afterCutOffDate1);
            })->orWhere(function ($query2) use ($afterCutOffDate1, $afterCutOffDate2) {
                $query2->whereBetween('from_Date', [$afterCutOffDate1, $afterCutOffDate2]);
            });
        })->select("from_date", "to_date", "user_id", "leave_session")->get();
        $data['beforeCutOffLeaves']                 =   $beforeCutOffLeaves->isEmpty() ? 0 : array_sum($beforeCutOffLeaves->pluck("duration")->toArray());
        $data['afterCutOffLeaves']                  =   $afterCutOffLeaves->isEmpty() ? 0 : array_sum($afterCutOffLeaves->pluck("duration")->toArray());
        $data['submitRoute']                        =   ['leaveBalanceUpdate', $id];
        $data['method']                             =   'POST';
        $data['leaveBalance']                       =   $leaveBalance;
        $data['userDepartments']                    =   User::with('employee.department')->where('is_active', '1')->where('user_type', 'Employee')
            ->select('id', 'name')->get()->groupBy('employee.department.name');
        $data['now']                                =   now();
        return view('leave.updateLeaveBalance', $data);
    }

    public function update(LeaveBalanceRequest $request, $id)
    {
        $leaveBalance = LeaveBalance::findOrFail($id);
        $this->updateLeaveBalance($leaveBalance, $request);
        $this->handleLeaveBalanceRaise($leaveBalance, "Sorted");
        return redirect(route('leaveBalanceDashboard'))->with('success', 'Success ');
    }

    public function leaveBalanceRaise(Request $request)
    {
        $leaveBalance = LeaveBalance::with('user')->findOrFail($request->leave_balance_id);
        $this->handleLeaveBalanceRaise($leaveBalance, $request->description);
        return redirect(route('leaveBalanceDashboard'))->with('success', 'Response Submitted');
    }

    private function updateLeaveBalance(LeaveBalance $leaveBalance, LeaveBalanceRequest $request)
    {
        $leaveBalance->balance = $request->balance;
        $leaveBalance->absent = $request->absent;
        $leaveBalance->deduction = $request->deduction;
        $leaveBalance->prev_month_deduction = $request->prev_month_deduction;
        $leaveBalance->next_month_deduction = $request->next_month_deduction;
        $leaveBalance->pre_approval_deduction = $request->pre_approval_deduction;
        $leaveBalance->update();
    }

    private function handleLeaveBalanceRaise(LeaveBalance $leaveBalance, $description)
    {
        $leaveBalanceComplaint = new LeaveBalanceComplaint();
        $leaveBalanceComplaint->leave_balance_id = $leaveBalance->id;
        $leaveBalanceComplaint->user_id = auth()->user()->id;
        $leaveBalanceComplaint->description = $description;
        $leaveBalanceComplaint->save();

        if ($leaveBalance->user_id != auth()->user()->id) {
            $leaveBalance->leaveBalanceComplaints()->update(['is_responded' => 1]);

            $email = $leaveBalance->user->email;
            $message = auth()->user()->name . " Responded on your leave balance query.";
            $emailData['message'] = $description;
            $emailData['link'] = route('myBalance');
            $message = (new Action($leaveBalance, $emailData, $message, 'email.action'))->onQueue('emails');
            $this->mailer->to($email)->later(Carbon::now()->addSeconds(30), $message);
        }
    }

    public function myBalance(Request $request)
    {
        return view('leave.myBalance');
    }

    public function getBalance(Request $request)
    {
        if ($request->ajax()) {
            $user_id    =  auth()->user()->id;
            $user       =  User::find($user_id);
            $leaves     =  Leave::where('user_id', $user_id)->whereIn('status', ['Pending', 'Approved', 'Pre Approved', 'Forwarded', 'Rejected', 'Absent'])->get();
            $employeeLeaves  =   [];
            if ($leaves->isNotEmpty()) {
                foreach ($leaves as $leave) {
                    if ($leave->status == 'Pre Approved') {
                        $color = "#a8d9a8";
                    } elseif ($leave->status == 'Approved') {
                        $color = "#ffcbcb";
                    } elseif ($leave->status == 'Forwarded') {
                        $color = "rgba(109,358,331,0.27)";
                    } elseif ($leave->status == 'Pending') {
                        $color = "#fced6deb";
                    } elseif ($leave->status == 'Absent') {
                        $color = "#43464936";
                    } elseif ($leave->status == 'Rejected') {
                        $color = "#f50823";
                    }
                    $employeeLeaves[] = [
                        'title'         => ($leave->leave_session == 'Full day') ? 'Full day' : 'Half day',
                        'start'         =>   $leave->from_date,
                        'end'           =>   Carbon::parse($leave->to_date)->addDay()->format('Y-m-d'),
                        'color'         =>   $color,
                        'url'           =>   route('leaveList'),
                        'description'   =>   '',
                        'status'        =>    $leave->status,
                        'type'          =>    $leave->leave_session,
                    ];
                }
            }
            $data['user']           = $user;
            $data['employeeLeaves'] = $employeeLeaves;
            $data['route']          = route('createLeave');
            $month                  =      empty($request->month) ? now() : Carbon::createFromFormat('Y-m', $request->month);
            $myBalance              =      LeaveBalance::with('user')->where('user_id', auth()->user()->id)->whereYear('month', $month)->whereMonth('month', $month)->first();
            if (!empty($myBalance)) {
                $data['balanceChart']       =   view('leave.balance', compact('myBalance'))->render();
            }
            $model              = new Leave();
            $submitRoute        = 'submitLeave';
            $leaveTypes         = LeaveType::where('name', '<>', 'Manual')->pluck('name', 'id')->toArray();
            $leaveBalance               = LeaveBalance::whereMonth('month', Carbon::today())->whereYear('month', today())->where('user_id', auth()->user()->id)
                ->first();
            $balance            = !empty($leaveBalance) ? $leaveBalance->balance : 0;
            $today              = Carbon::today()->format('Y-m-d');
            $max                = Carbon::now()->startOfMonth()->addMonth()->endOfMonth()->format('Y-m-d');
            $leaveNature        = $model->getLeaveSession();
            $data['leaveForm'] = view('leave.calendarLeaveForm', compact('model', 'submitRoute', 'leaveTypes', 'balance', 'today', 'max', 'leaveNature'))->render();
            return $data;
        }
    }

    public function export(Request $request)
    {
        $this->authorize('hrUpdateEmployee', new Employee());
        $leaveBalances   =   $this->dashboard($request, true);
        return Excel::download(new LeaveBalanceExport($leaveBalances), 'leaveBalance.xlsx');
    }
}
