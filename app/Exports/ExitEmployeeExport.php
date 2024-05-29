<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExitEmployeeExport implements FromCollection,WithHeadings,WithMapping,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $employees;

    function __construct($employees) {
        $this->employees = $employees;
    }

    public function collection()
    {
        return $this->employees;
    }

    public function map($employees): array
    {
        return [
            $employees->name ?? 'N/A',
            $employees->department->name ?? 'N/A',
            $employees->office_email ?? 'N/A',
            $employees->personal_email ?? 'N/A',
            $employees->biometric_id ?? 'N/A',
            getFormatedDate($employees->join_date) ?? 'N/A',
            getFormatedDate($employees->contract_date) ?? 'N/A',
            getFormatedDate($employees->employeeExitDetail->exit_date) ?? 'N/A',
        ];
    }

    public function headings() : array{
        return [
            'Name',
            'Department', 
            'Email',
            'Personal Email',
            'EY Code', 
            'Join Date ',
            'Contract Date ',
            'Exit Date ',
        ];
    }
}
