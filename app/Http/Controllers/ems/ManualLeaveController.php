<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Department;
use Illuminate\Support\Str;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\ManualLeaveRequest;
class ManualLeaveController extends Controller
{

    public function index(Request $request)
    {
        ini_set('max_execution_time',-1);
        $this->authorize('hrEmployeeList', new Employee());
        $leaves                         =   Leave::where('is_manual', '1')->with('user.employee.department', 'leaveType');
        $data['employeeDepartments']  	=   User::with('employee.department')->where('is_active',1)->where('user_type','Employee')->whereHas('employee',function($employee)
                                            {
                                                $employee->select('biometric_id','name');
                                            })->get()->groupBy('employee.department.name');
        $data['leaveTypes']             =   LeaveType::pluck('name', 'id')->toArray();
        $data['sessions']               =   Leave::pluck('leave_session', 'leave_session')->toArray();
        $data['departments']            =   Department::pluck('name', 'id')->toArray();
        $data['leaves']                 =   $leaves->get();
        return view('leave.manualLeaves', $data);
    }

    public function manualLeaveList(Request $request)
    {
        $leaves     =       Leave::where('is_manual', '1')->select('leaves.*','leave_types.name as leaveType','users.name as userName','departments.name as departmentName')
                            ->leftJoin('leave_types','leaves.leave_type_id','=','leave_types.id')
                            ->leftJoin('users','leaves.user_id','=','users.id')
                            ->leftJoin('employee','employee.user_id','=','users.id')
                            ->leftJoin('departments','employee.department_id','=','departments.id');
        $leaves     =       $this->filter($leaves,$request);
        return DataTables::of($leaves)
        ->addColumn('reason',function($leaves){
            return '<textarea cols="30" rows="3" disabled>'.Str::before($leaves->reason, " Reason :").'</textarea>';
        })
        ->editColumn('duration', function($leaves){
            return $leaves->setDurationAttribute();
        })
        ->editColumn('attachment', function($leaves){
            if ($leaves->attachment)
            {
                $btn = '<a href="'.route("viewFile", ['file' => $leaves->attachment]).'" target="_blank"><i class="fa fa-eye text-primary"></i></a>';
            }
            else{
            $btn='N/A';
            }
            return $btn;
        })
        ->editColumn('timing', function($leaves){
            return getFormatedTime($leaves->timing);
        })
        ->filterColumn('leaveType', function($query, $keyword){
            $query->where('leave_types.name', 'like', "%$keyword%");
        })
        ->filterColumn('userName', function($query, $keyword){
            $query->where('users.name', 'like', "%$keyword%");
        })
        ->filterColumn('departmentName', function($query, $keyword){
            $query->where('departments.name', 'like', "%$keyword%");
        })
        ->rawColumns(['reason','attachment'])
        ->make(true);
    }

    public function filter($leaves,$request)
    {
        if (request()->has('leave_type_id'))
        {
            $leaves             =   $leaves->where('leave_type_id', $request->leave_type_id);
        }
        if (request()->has('leave_session'))
        {
            $leaves             =   $leaves->where('leave_session', $request->leave_session);
        }
        if (request()->has('department_id'))
        {
            $leaves = $leaves->whereHas('user.employee', function ($query) {
                $query->where('department_id', request()->department_id);
            });
        }
        if (request()->has('user_id'))
        {
            $leaves             =   $leaves->where('leaves.user_id', $request->user_id);
        }
        if (!empty(request()->dateFrom) || !empty(request()->dateTo))
        {
            $leaves         =   $leaves->where(function ($subQuery) use ($request) {
                                    $subQuery->where(function ($query1) use ($request) {
                                        $query1->where('from_Date', '<=', $request->dateFrom)->where('to_Date', '>=', $request->dateFrom);
                                    })->orWhere(function ($query2) use ($request) {
                                        $query2->whereBetween('from_Date', [$request->dateFrom, $request->dateTo]);
                                    });
                                });
        }
        return $leaves;
    }

    public function create()
    {
        $this->authorize('hrEmployeeList', new Employee());
        $data['leave']                          =        new Leave();
        $data['leaveSessions']                  =        $data['leave']->getLeaveSession();
        $data['submitRoute']                    =        ['manual-leave.store'];
        $data['today']                          =        today()->startOfMonth()->format('Y-m-d');
        $data['leaveTypes']                     =        LeaveType::pluck('name', 'id');
        $data['status']                         =        Config::get('leave.status');
        $data['method']                         =        'POST';
        $data['max']                            =        Carbon::now()->startOfMonth()->addMonth()->endOfMonth()->format('Y-m-d');
        $data['employeeDepartments']            =        Employee::where('is_active', '1')->whereHas('user', function ($user) {
                                                         $user->where('is_active', '1')->where('user_type', 'Employee');
                                                         })->select('user_id', 'department_id', 'biometric_id', 'name')->get()->
                                                         groupBy('department.name');
        return view('leave.manualLeaveForm', $data);
    }

    public function store(ManualLeaveRequest $request)
    {
        $leaveSession               =   empty($request->leave_session) ? "Full day" : $request->leave_session;
        $leaveExists                =   $this->leaveExists($request,$leaveSession ,$request->user_id);
        if ($leaveExists->exists())
        {
            return back()->with('failure', 'Leave already Exists');
        }
        $leave                    =     new Leave();
        $leave->user_id           =     $request->user_id;
        $leave->leave_session     =     $leaveSession;
        $leave->leave_type_id     =     $request->leave_type;
        $leave->from_date         =     $request->from_date;
        $leave->to_date           =     $request->to_date;
        $leave->status            =     $request->status;
        $leave->reason            =     $request->reason;
        $leave->is_manual         =     1;
        $leave->is_approved       =     1;
        $leave->action_by         =     auth()->user()->id;
        if (!empty($request->attachment))
        {
            $file               =   'leaveFile' . Carbon::now()->timestamp . '.' . $request->file('attachment')->getClientOriginalExtension();

            $request->file('attachment')->move(storage_path('app/documents/leave_documents'), $file);

            $leave->attachment  =   $file;
        }
        $leave->save();
        $carbonDate         =   Carbon::createFromFormat('Y-m-d', $leave->from_date);
        $duration           =   $leave->duration;
        $appliedAt          =  Carbon::createFromFormat('Y-m-d H:i:s', $leave->created_at)->format('Y-m-d');
        $cutOffDate         =  Carbon::now()->startOfMonth()->addDays(19)->format('Y-m-d');
        if ($leave->status == 'Pre Approved')
        {
            $this->preApprovalBalance($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
        } elseif ($leave->status == "Approved")
        {
            $this->approvalDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
        } else
        {
            $this->absentDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate);
        }

        $manager                    =    $leave->user->employee->department->deptManager;
        $hr                         =    User::where('email', '<>', 'martha.folkes@theknowledgeacademy.com')->whereHas('roles', function ($query) {
                                                $query->where('name', 'hr');
                                                })->where('mail_sent', '1')->get();


        if(auth()->user()->employee->managerDepartments->isNotEmpty())
        {
            $department_ids = auth()->user()->employee->managerDepartments->pluck('id','id')->toArray();
            $leaves =  Leave::where('status', 'Pending')->whereDate('from_date',request()->from_date)
                            ->whereHas('user.employee',function($q) use($department_ids){
                                $q->whereIn('department_id',$department_ids)->where('user_id','<>',auth()->user()->id);
                            })->get();
            foreach($leaves as $leave)
            {
                $this->autoForwardedLeaveNotification($manager,$hr,$leave);
            }
        }


        return redirect(route('manual-leave.index'))->with('success', 'Leave Added');
    }


    private function absentDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate)
    {

        $fromMonth                  =   Carbon::createFromFormat('Y-m-d', $leave->from_date)->format('m');
        $cutOffDateMonth            =   Carbon::now()->format('m');
        if ($fromMonth != $cutOffDateMonth)
        {
            $cutOffDate             =   Carbon::now()->startOfMonth()->addMonth(1)->addDays(19);
        }
        $getBalance                 =         $this->getBalance($leave, $carbonDate);
        $leaveBalance               =         $getBalance['leaveBalance'];
        if (empty($leaveBalance))
        {
            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =   $leave->user_id;

            if ($appliedAt < $cutOffDate)
            {
                $leaveBalance->absent      =   $duration * 2;
            } elseif ($appliedAt > $cutOffDate)
            {
                $leaveBalance->next_month_deduction =   $duration * 2;
            }
        } else {
                if ($appliedAt < $cutOffDate)
                {
                    $leaveBalance->absent              =    $leaveBalance->absent + $duration * 2;
                } elseif ($appliedAt > $cutOffDate)
                {
                    $leaveBalance->next_month_deduction =   $leaveBalance->next_month_deduction +   $duration * 2;
                }
        }
        $leaveBalance->save();
    }

    private function preApprovalBalance($leave, $carbonDate, $duration, $appliedAt, $cutOffDate)
    {
        $fromMonth                  =   Carbon::createFromFormat('Y-m-d', $leave->from_date)->format('m');
        $cutOffDateMonth            =   Carbon::now()->format('m');

        if ($fromMonth != $cutOffDateMonth)
        {
            $cutOffDate         =   Carbon::now()->startOfMonth()->addMonth(1)->addDays(19);
        }
        $getBalance             =        $this->getBalance($leave, $carbonDate);
        $leaveBalance           =         $getBalance['leaveBalance'];

        if (empty($leaveBalance))
        {
            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =   $leave->user_id;
            if ($leave->from_date < $cutOffDate )
            {
                $leaveBalance->pre_approval_deduction       =   $duration;
            } elseif ($leave->from_date > $cutOffDate)
            {
                $leaveBalance->next_month_deduction         =   $duration;
            }
        } else {
            $deductibleBalance                      =   $getBalance['deductibleBalance'];
            $leftBalance                            =   $getBalance['leftBalance'];
            $finalBalance                           =   $deductibleBalance           -   $duration;
            $leaveBalance->taken_leaves             =   $leaveBalance->taken_leaves  +   $duration;
            if ($finalBalance >= 0) {
                $leaveBalance->balance              =   $finalBalance  + $leftBalance;
            } else {
                $leaveBalance->balance              =   $leftBalance;
                if ($leave->from_date < $cutOffDate ) {
                    $leaveBalance->pre_approval_deduction     =   $leaveBalance->pre_approval_deduction + abs($finalBalance);
                } else {
                    $leaveBalance->next_month_deduction       =    $leaveBalance->next_month_deduction + abs($finalBalance);
                }
            }
        }
        $leaveBalance->save();
    }

    private function approvalDeduction($leave, $carbonDate, $duration, $appliedAt, $cutOffDate)
    {
        $fromMonth                  =   Carbon::createFromFormat('Y-m-d', $leave->from_date)->format('m');
        $cutOffDateMonth            =   Carbon::now()->format('m');
        if ($fromMonth != $cutOffDateMonth) {
            $cutOffDate             =   Carbon::now()->startOfMonth()->addMonth(1)->addDays(19);
        }
        $getBalance                 =        $this->getBalance($leave, $carbonDate);
        $leaveBalance               =         $getBalance['leaveBalance'];
        // same month deduction
        if (empty($leaveBalance)) {
            $leaveBalance                  =   new LeaveBalance();
            $leaveBalance->month           =   $leave->from_date;
            $leaveBalance->balance         =   0;
            $leaveBalance->taken_leaves    =   $duration;
            $leaveBalance->user_id         =  $leave->user_id;
            if ($leave->from_date < $cutOffDate )
            {
                $leaveBalance->deduction       =   $duration;
            }
            // deduction from next month after 20th leave apply if balance is less then duration
            elseif ($leave->from_date > $cutOffDate)
            {
                $leaveBalance->next_month_deduction =   $duration;
            }
        } else {
            $leaveBalance->taken_leaves             =   $leaveBalance->taken_leaves  +   $duration;
            if ($leave->from_date < $cutOffDate )
            {
                $leaveBalance->deduction            =   $leaveBalance->deduction    +   $duration;
            }
            // deduction from next month after 20th leave apply if balance is less then duration
            elseif ($leave->from_date > $cutOffDate)
            {
                $leaveBalance->next_month_deduction =    $leaveBalance->next_month_deduction +   $duration;
            }
        }
        $leaveBalance->save();
    }

    private function getBalance($leave, $carbonDate)
    {
        $leaveBalance       =   LeaveBalance::whereMonth('month', $carbonDate)->where('user_id', $leave->user_id)->first();
        $deductibleBalance  =   0;
        $leftBalance        =   0;

        if (!empty($leaveBalance) && $leaveBalance->balance > 0)
        {
            $balance    = $leaveBalance->balance;
            $whole      = intval($balance);
            $decimal1   = $balance - $whole;
            $decimal2   = round($decimal1, 2);
            $decimal    = substr($decimal2, 2);

            if ($decimal != '25' && $decimal != '75')
            {
                $deductibleBalance      =   $leaveBalance->balance;
                $leftBalance            =   0;
            } else
            {
                $deductibleBalance      =   $leaveBalance->balance - 0.25;
                $leftBalance            =   0.25;
            }
        }
        $data['deductibleBalance']      =   $deductibleBalance;
        $data['leftBalance']            =   $leftBalance;
        $data['leaveBalance']           =   $leaveBalance;
        return $data;
    }

    private function leaveExists($request,$leaveSession, $user_id)
    {

        $sessions=['Full day'=>'Full day','First half'=>'First half','Second half'=>'Second half'];
        if($leaveSession=='First half')
        {
            $sessions   =   ['Full day'=>'Full day','First half'=>'First half'];
        }
        elseif($leaveSession=='Second half')
        {
            $sessions   =   ['Full day'=>'Full day','Second half'=>'Second half'];
        }
        return Leave::where('user_id', $user_id)->whereNotIn('status', ['Rejected', 'Cancelled'])->whereIn('leave_session', $sessions)
                ->where(function ($subQuery) use ($request) {

                $subQuery->where(function ($query1) use ($request) {

                    $query1->where('from_Date', '<=', $request->from_date)->where('to_Date', '>=', $request->from_date);
                })->orWhere(function ($query2) use ($request) {

                    $query2->whereBetween('from_Date', [$request->from_date, $request->to_date]);
                });
            });
    }

    function autoForwardedLeaveNotification($manager,$hr,$leave)
    {
        $remarks        = "I'm on leave today";
        $leave->update(['forwarded' => '1', 'action_by' => $manager->user_id, 'remarks' => $remarks, 'status' => 'Auto Forwarded']);
        $notificationReceivers  =    $hr->pluck('id', 'id')->toArray();
        $email                  =    $hr->pluck('email')->toArray();
        $link                   = route('hrLeaveList');
        send_notification($notificationReceivers, 'Leave forwarded by ' .  $manager->name, $link, 'leave');
        $data['leave']          = $leave;
        $data['link']           = $link;
        $message                = "Leave Forwarded of " . $leave->user->name;
        $subject                = 'Leave Forwarded by ' . $manager->name;
        send_email("email.leave", $data, $subject, $message, $email, null);
    }
}
