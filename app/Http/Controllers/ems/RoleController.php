<?php

namespace App\Http\Controllers\ems;

use App\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new Role());
        return view('role.roles');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('view', new Role());
        
        $data['userRoles']  = User::with('roles')->find($request->id)->roles->pluck('name', 'name')->toArray();
        $data['roles']      = Role::pluck('name', 'id')->toArray();
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('view', new Role());
        $user   = User::find($request->user);
        $roles  = $request->input('roles');
        if (empty($roles)) {
            $roles = array();
        }
        $user->roles()->sync($roles);
        return back()->with('success', 'Role Assigned Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $this->authorize('assignPermission', new Permission());
        $data['role']           =   $role;
        $data['submitRoute']    =   array('role.update', $role->id);
        $permissions            =   Permission::orderbyRaw("FIELD(access, 'view', 'insert', 'update',
                                            'delete', 'restore', 'destroy', 'viewTrash', 'assignPermission', 'approvalView',
                                            'approval', 'managerapprovalview', 'preview', 'detail', 'test', 'export',
                                            'deactivate')")
                                        ->get()
                                        ->load('module')
                                        ->groupBy('module_name');
        $data['permissions']    =   $permissions;
        return view("role.assignPermissionForm", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Role $role, RoleRequest $request)
    {
        $this->authorize('assignPermission', new Permission());
        $inputs                     =   $request->except(["_token"]);
        $permissions                =   $inputs['permission'] ?? [];
        $role->permissions()->sync($permissions);
        Session::flash('success', 'Role permissions updated!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
