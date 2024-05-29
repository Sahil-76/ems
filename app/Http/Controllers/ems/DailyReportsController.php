<?php

namespace App\Http\Controllers\ems;
use App\User;
use Carbon\Carbon;
use App\Mail\UserMail;
use App\Models\Department;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use App\Exports\DailyReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class DailyReportsController extends Controller
{

    public function __construct()
    {
        $this->end          =   Carbon::createFromTimeString('08:30','Asia/Kolkata')->format('Y-m-d H:i:s');
        $this->now          =   Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');
        $this->today        =   Carbon::today('Asia/Kolkata')->format('Y-m-d');
        $this->yesterday    =   Carbon::today('Asia/Kolkata')->subDay()->format('Y-m-d');
    }

    public function form()
    {
        $data['min']        =   $this->yesterday;
        $data['max']        =   $this->today;
        $today              =   Carbon::today();
        $user               =   Auth()->user();
        $leaves             =   $user->leaves()->whereIn('status',['Approved','Pre Approved'])->where(function($subQuery)use($today)
                                        {
                                            $subQuery->where(function($q1)use($today)
                                            {
                                                $q1->where('from_date','<=',$today)->where('to_date','>=',$today);
                                            })->orWhere(function($q2)use ($today)
                                            {
                                                $q2->whereBetween('from_date',[$today,$today]);
                                            });
                                        })->first();
        if ($this->now      >= $this->end) {
            $data['min']    =   $data['max'];
            $report         =   DailyReport::whereDate('report_date', $data['max'])->where('user_id', auth()->user()->id)->first();
        }else{
            $report         =   new DailyReport();
        }
        $data['i']          =   1;
        $data['tasks']      =   6;
        if(isset($leaves))
        {
            switch($leaves->leave_session)
            {
                case('First half'):
                    $data['i']      = 4;
                    $data['tasks']  = 6;
                    break;
                case('Second half'):
                    $data['i']      = 1;
                    $data['tasks']  = 3;
                    break;
            }
        }
        $data['report']     =   $report;
        return view('dailyReport.form', $data);
    }

    public function submit(Request $request)
    {
        $report_date    =   Carbon::parse($request->report_date)->format('Y-m-d');
        if (($report_date == $this->yesterday &&  $this->now <= $this->end)|| $report_date == $this->today) {
            return $this->saveReport($request);
        }
        $message_date   =   Carbon::parse($request->report_date)->format('d-m-Y');
        return redirect()->back()->with('failure','You can not submit '.$message_date. ' report.');
    }

    public function saveReport($request)
    {
        $report              = DailyReport::firstOrNew(['report_date' => $request->report_date, 'user_id' => auth()->user()->id]);
        $report->task1       = $request->task1;
        $report->task2       = $request->task2;
        $report->task3       = $request->task3;
        $report->task4       = $request->task4;
        $report->task5       = $request->task5;
        $report->task6       = $request->task6;
        $report->save();
        return redirect()->back()->with('success','Report Submitted Successfully.');
    }

    public function myList(Request $request)
    {
        $month       = Carbon::now()->month;
        $year        = Carbon::now()->year;
        if($request->month=="last_month")
        {
            $month   = Carbon::now()->subMonth();
        }
        $reports     = DailyReport::where('user_id',auth()->user()->id)
                        ->whereYear('report_date',$year)->whereMonth('report_date',$month);
        $data['reports'] = $reports->orderBy('report_date','desc')->paginate(10);
        return view('dailyReport.myList',$data);
    }

    public function departmentReports(Request $request , $export = false)
    {
        abort_if(auth()->user()->cannot('managerDashboard', new User()) && auth()->user()->cannot('hrDashboard',  new User()),403);
        $departments            = Department::query();
        if(auth()->user()->hasRole('Line Manager')){
            $departments = $departments->where('line_manager_id', auth()->user()->id);
        }elseif(!auth()->user()->hasRole('hr') && auth()->user()->employee->managerDepartments->isNotEmpty())
        {
            $departments        = $departments->where('manager_id',auth()->user()->employee->id);
        }
       
        $data['departments']    = $departments->pluck('name','id')->toArray();
        if (request()->has('date'))
        {
            $date               = $request->date;
        }else
        {
            $date               = Carbon::today()->format('Y-m-d');
        }
        $users                  = User::with('employee.department');
        if(request()->has('department_id'))
        {
            $users              = $users->whereHas('employee', function ($employee)
                                  {
                                      $employee->where('department_id',request()->department_id);
                                  });

        }
        else
        {
            $departmentsIds     =   $departments->pluck('id','id');
            $users              =   $users->whereHas('employee.department',function($query) use($departmentsIds){
                                        $query->whereIn('id',$departmentsIds);
                                    });
        }
        $users                  =   $users->with([
                                        'workReports' => function($query) use($date){
                                            $query->whereDate('report_date',$date);
                                        },
                                        'leaves' => function($query) use($date){
                                            $query->whereDate('from_date', '<=', $date)
                                                    ->whereDate('to_date', '>=', $date)->whereIn('status', ['Approved','Pre Approved']);
                                        }
                                        ])->orderBy('id');
        if($export == 'true')
        {
            return $users->get();
        }
        $data['today']          =  $date;
        $data['users']          =  $users->paginate(25);
        return view('dailyReport.departmentReports',$data);
    }

    public function export(Request $request)
    {
        abort_if(auth()->user()->cannot('managerDashboard', new User()) && auth()->user()->cannot('hrDashboard',  new User()),403);
        ini_set('max_execution_time', -1);
        if (request()->has('date'))
        {
            $date       = Carbon::parse($request->date)->format('d-m-Y');
        }
        else
        {
            $date       = Carbon::today()->format('d-m-Y');
        }
        $fileName       = "workReport_$date.xlsx";
        $users          = $this->departmentReports($request,true);
        return Excel::download(new DailyReportExport($users), $fileName);
    }
}
