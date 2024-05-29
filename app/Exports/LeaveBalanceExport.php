<?php

namespace App\Exports;

use App\Models\LeaveBalance;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeaveBalanceExport implements FromCollection,WithHeadings,WithMapping,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $leaveBalances;

    public function __construct($leaveBalances)
    {
        $this->leaveBalances = $leaveBalances;
    }

    public function collection()
    {
       
        return $this->leaveBalances;
    }

    public function map($leaveBalances): array
    {
        return [
            $leaveBalances->user->name ?? '' ,
            $leaveBalances->user->employee->department->name ?? '',
            $leaveBalances->balance,
            empty($leaveBalances->absent) ? 0 : $leaveBalances->absent,
            empty($leaveBalances->prev_month_deduction) ? 0 : $leaveBalances->prev_month_deduction ,
            empty($leaveBalances->next_month_deduction) ? 0 : $leaveBalances->next_month_deduction ,
            empty($leaveBalances->taken_leaves) ? 0 : $leaveBalances->taken_leaves,
            empty($leaveBalances->deduction) ? 0 : $leaveBalances->deduction,
            $leaveBalances->final_deduction
        ];
    }

    public function Headings(): array
    {
        return[
            'Name',
            'Department',
            'Balance',
            'Absent',
            'Previous Month Deduction',
            'Next Month Deduction', 
            'Taken Leaves',
            'Deduction',
            'Final Deduction'
        ];
    }
}
