<?php

namespace App\Http\Controllers\ems;

use App\Models\Task;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TrainingEmployee;

class TrainingController extends Controller
{
    public function index()
    {
        $this->authorize('trainingView',new Task);

        $trainingEmployees          =   TrainingEmployee::with('user.employee.department', 'trainer')->whereHas('user',function($user)
                                                {
                                                    $user->where('is_active', 1)->where('user_type','Employee');
                                                });
        if(auth()->user()->cannot('hrEmployeeList',new Employee()))
        {
            $trainingEmployees      =   $trainingEmployees->whereHas('user.employee', function($employees){
                                                $employees->where('department_id',auth()->user()->employee->department_id);
                                            });
        }
        $data['trainingEmployees']  =   $trainingEmployees->get();
        return view('training.index', $data);
    }

    public function edit($id)
    {
        $this->authorize('trainingView',new Task);

        $trainingEmployee               =   TrainingEmployee::with('user.employee.department', 'trainer')->findOrFail($id);
        $user                           =   $trainingEmployee->user;
        $department                     =   $user->employee->department;
        $data['userTasks']              =   $user->tasks->pluck('id')->toArray();
        $tasks                          =   Task::all();
        $data['trainingEmployee']       =   $trainingEmployee;
        $data['manager_id']             =   $department->manager_id;
        $data['basicTasks']             =   $tasks->where('department_id', null);
        $data['submitRoute']            =   ['training.update',$id];
        $data['method']                 =   'PUT';
        $data['departmentalTasks']      =   $tasks->where('department_id', $department->id);
        $data['trainers']               =   Employee::where('department_id', $department->id)->where('user_id', '<>', $user->id)
                                                    ->pluck('name', 'user_id')->toArray();
        return view('training.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('trainingView',new Task);
        $trainingEmployee               =   TrainingEmployee::with('user')->findOrFail($id);
        $trainingEmployee->trainer_id   =   $request->trainer_id ?? null;
        $trainingEmployee->save();
        $trainingEmployee->user->tasks()->sync($request->tasks);
        return redirect()->route('training.index')->with('success','Task Updated');
    }

    public function updateOnboardStatus(Request $request)
    {
        $trainingEmployee       =   TrainingEmployee::findOrFail($request->id);
        $employee               =   Employee::where('user_id',$trainingEmployee->user_id)->first();
        if($request->result == 'pass')
        {
            $employee->onboard_status   = 'Onboard';
            $trainingEmployee->delete();
        }
        else
        {
            $trainingEmployee->result   = 'FAIL';
            $trainingEmployee->save();
        }
        $employee->save();
    }
}
