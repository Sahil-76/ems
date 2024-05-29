<?php

namespace App\Exports;

use App\Models\DailyReport;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyReportExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $users;

    function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users;
    }

    public function map($users): array
    {
        return [
            $users->name,
            $users->employee->department->name,
            optional($users->leaves)->first()->leave_session ?? 'Present',
            optional($users->workReports)->first()->task1 ?? '' ,
            optional($users->workReports)->first()->task2 ?? '' ,
            optional($users->workReports)->first()->task3 ?? '' ,
            optional($users->workReports)->first()->task4 ?? '' ,
            optional($users->workReports)->first()->task5 ?? '' ,
            optional($users->workReports)->first()->task6 ?? '' 
            
        ];
    }
    public function headings() : array{
        return [
                'Name',
                'Department',
                'Leave Session',
                'Task 1',          
                'Task 2',          
                'Task 3',          
                'Task 4',          
                'Task 5',          
                'Task 6'          
        ];
    }
}
