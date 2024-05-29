<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Entity;
use App\Models\Equipment;
class ManagerController extends Controller
{
    public function view()
    {
        $this->authorize('managerEmployeeList',new Employee());

        if (auth()->user()->hasRole('Line Manager')) {
            
            $departmentIds = Department::where('line_manager_id', auth()->user()->id)->pluck('id', 'id')->toArray();
        }else{

            $departmentIds  =   auth()->user()->employee->managerDepartments->pluck('id', 'id')->toArray();
        }

        $employees = Employee::with('department')
        ->whereHas('department', function($department)use($departmentIds){
            $department->whereIn('id', $departmentIds);
        })->get();

        
        $data['employees']          = $employees;
        return view('manager.employee',$data);
    }
}
