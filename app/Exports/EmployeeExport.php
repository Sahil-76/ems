<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeeExport implements FromCollection,WithHeadings,WithMapping,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $is_active;
    private $employees;

    function __construct($employees) {
        $this->employees = $employees;
    }

    public function collection()
    {
        return $this->employees;
    }

    public function map($employee): array
    {
        return [
            $employee->name,
            $employee->office_email ?? '',
            $employee->personal_email ?? '',
            $employee->biometric_id ?? '',
            $employee->department_name,
            $employee->join_date ?? '',
            $employee->qualification ?? '',
            $employee->user_type ?? '',
            $employee->off_day ?? '',
            $employee->aadhaar_number ?? '',
            $employee->contract_date ?? '',
            $employee->gender,
            $employee->shift_type."(".$employee->start_time."-". $employee->end_time.")" ?? '',
        ];
    }

    public function headings() : array{
        return [
            'Name',
            'Tka Email',
            'Personal Email',
            'Biometric',
            'Department',
            'Joining Date',
            'Qualification',
            'Employee Type',
            'Off Day',
            'Aadhaar Number',
            'Contract Date',
            'Gender',
            'Shift Type',
        ];
    }
}
