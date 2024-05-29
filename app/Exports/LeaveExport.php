<?php

namespace App\Exports;

use App\Models\Leave;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class LeaveExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $leaves = Leave::with('user.employee.department')->where('status','<>','Pending');

        if(!empty(request()->dateFrom) || (request()->dateTo))
        {
            $leaves->where(function($subQuery) {

                $subQuery->where(function($query1)
                {

                    $query1->where('from_Date','<=',request()->dateFrom)->where('to_Date','>=',request()->dateFrom);

                })->orWhere(function($query2) {

                    $query2->whereBetween('from_Date',[request()->dateFrom,request()->dateTo]);
                });
            });
        } 
        
        if(request()->has('leave_nature'))
        {
           $leaves      = $leaves->where('leave_nature',request()->leave_nature);
        }
        
        if(request()->has('leave_session'))
        {
           $leaves      = $leaves->where('leave_session',request()->leave_session);
        }

        if(request()->has('employee_id'))
        {
           $leaves      = $leaves->where('employee_id',request()->employee_id);
        }

        if(request()->has('department_id'))
        {
            $leaves     = $leaves->whereHas('employee',function($query){
                                    $query->where('department_id',request()->department_id);
                                });
        }
       
        return $leaves->orderBy('from_date')->get();
    }

    public function map($leave): array
    {
        return [
            getFormatedDate($leave->from_date),
            getFormatedDate($leave->to_date),
            optional($leave->user->employee)->department->name ?? "",
            optional($leave->user->employee)->name,
            $leave->leaveType->name,
            $leave->leave_session,
            $leave->reason,
            $leave->status
        ];
    }

    public function headings() : array{
        return [
            'From Date',
            'To Date',
            'Department',
            'Name',
            'Leave Type',
            'Leave Session',
            'Reason',
            'Status',
        ];
    }
}
