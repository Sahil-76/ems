<?php

namespace App\Http\Controllers\ems;

use App\Models\LeaveType;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveTypeRequest;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $this->authorize('view', new LeaveType());
        $data['leaveTypes']      =   LeaveType::all();
        return view('leaveType.index',$data);
    }

    public function create()
    {
        $leaveType              =    new LeaveType();
        $this->authorize('create', $leaveType );
        $data['leaveType']      =   $leaveType ;
        $data['submitRoute']    =   ['leave-type.store'];
        $data['method']         =   'POST';
        return view('leaveType.form',$data);
    }

    public function store(LeaveTypeRequest $request)
    {
        $leaveType          =    new LeaveType();
        $this->authorize('create', $leaveType);
        LeaveType::updateOrCreate(['name' => $request->name]);
        return redirect(route('leave-type.index'))->with('success', 'Leave Type added successfully');
    }

    public function edit($id)
    {
        $this->authorize('update', new LeaveType());
        $data['leaveType']      =   LeaveType::find($id);
        $data['submitRoute']    =   ['leave-type.update',$id];
        $data['method']         =   'PUT';
        return view('leaveType.form',$data);
    }

    public function update(LeaveTypeRequest $request, $id)
    {
        $this->authorize('update', new LeaveType());
        $leaveType          =    LeaveType::find($id);
        $leaveType->name    =    $request->name;
        $leaveType->update();
        return redirect(route('leave-type.index'))->with('success', 'Leave Type updated successfully');
    }


    public function destroy($id)
    {
        $this->authorize('delete', new LeaveType());
        LeaveType::findOrFail($id)->delete();
    }
}
