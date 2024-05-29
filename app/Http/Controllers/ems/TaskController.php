<?php

namespace App\Http\Controllers\ems;

use App\Models\Task;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class TaskController extends Controller
{
    public function index()
    {
        $this->authorize('view',new Task);
        $tasks                      =       Task::with('department');
        if(auth()->user()->cannot('hrEmployeeList',new Employee()))
        {
            $tasks                  =   $tasks->where('department_id',auth()->user()->employee->department_id)->orWhereNull('department_id');
        }
        $tasks                      =   $tasks->get();
        $data['basicTasks']         =   $tasks->where('department_id',null);
        $data['departmentalTasks']  =   $tasks->where('department_id','<>', null);
        return view('task.index',$data);
    }

    public function create()
    {
        $this->authorize('create',new Task);
        $data['task']           =       new Task();
        $data['submitRoute']    =       'task.store';
        $data['method']         =       'POST';
        $data['departments']    =       Department::pluck('name','id')->toArray();
        return view('task.form',$data);
    }

    public function store(Request $request)
    {
        $task                   =       new Task();
        $this->authorize('create',$task);
        $task->name             =       $request->name;
        if(auth()->user()->cannot('hrEmployeeList', new Employee()))
        {
            $task->department_id    =       auth()->user()->employee->department_id;
        }
        else{
            $task->department_id    =       !empty($request->department_id) ? $request->department_id : null;
        }
        $task->save();
        return back()->with('success','Task Added');
    }

    public function edit($id)
    {
        $this->authorize('update',new Task);
        $data['task']           =       Task::findOrFail($id);
        $data['submitRoute']    =       ['task.update',['task'=>$id]];
        $data['method']         =       'PUT';
        $data['departments']    =       Department::pluck('name','id')->toArray();
        return view('task.form',$data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update',new Task);
        $task                   =       Task::findOrFail($id);
        $task->name             =       $request->name;
        $task->department_id    =       !empty($request->department_id) ? $request->department_id : auth()->user()->employee->department_id;
        $task->update();
        return redirect()->route('task.index')->with('success','Task Updated');
    }

    public function destroy($id)
    {
        $this->authorize('delete',new Task);
        Task::findOrFail($id)->delete();
    }
}
