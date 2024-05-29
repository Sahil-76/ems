<?php

namespace App\Http\Controllers\ems;

use App\User;
use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\PermissionRequest;
use Yajra\DataTables\Facades\DataTables;

class AuthorizeController extends Controller
{

    // public function index()
    // {
    //     $this->authorize('view', new Role());
    //     return view('role.roles');
    // }

    
    public function roleList(Request $request)
    {
        $this->authorize('view', new Role());
        $pageIndex  = $request->pageIndex;
        $pageSize   = $request->pageSize;
        $roles      = Role::query();
        if (!empty($request->get('name'))) {
            $roles  = $roles->where('name', 'like', '%' . $request->get('name') . '%');
        }
        $data['itemsCount'] = $roles->count();
        $data['data'] = $roles->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
        return json_encode($data);
    }

    public function insertRole(RoleRequest $request)
    {
        $this->authorize('insert', new Role());
        $inputs               =   $request->except("_token");
        $name                 =   $inputs['name'];
        $description          =   $inputs['description'];
        $role = Role::withTrashed()->firstOrCreate(['name' => $name], ['description' => $description]);
        if ($role->trashed()) {
            $role->restore();
        }
        $result = $role->toArray();
        return $result;
    }

    public function updateRole(RoleRequest $request)
    {
        $role = Role::findOrFail($request->input('id'));
        $this->authorize('update', $role);
        $inputs                     =   $request->except("_token");
        $role->name                 =   $inputs['name'];
        $role->description          =   $inputs['description'];
        $role->save();
        $result                     =   $role->toArray();
        return $result;
    }

    public function deleteRole(Request $request)
    {
        $role   = Role::findOrFail($request->input('id'));
        $this->authorize('delete', $role);
        $role->delete();
        return json_encode('done');
    }

    // public function editRolePermission(Role $role)
    // {
    //     $this->authorize('assignPermission', new Permission());
    //     $data['role']           =   $role;
    //     $data['submitRoute']    =   array('permission.update', $role->id);
    //     $permissions            =   Permission::orderbyRaw("FIELD(access, 'view', 'insert', 'update',
    //                                         'delete', 'restore', 'destroy', 'viewTrash', 'assignPermission', 'approvalView',
    //                                         'approval', 'managerapprovalview', 'preview', 'detail', 'test', 'export',
    //                                         'deactivate')")
    //                                     ->get()
    //                                     ->load('module')
    //                                     ->groupBy('module_name');
    //     $data['permissions']    =   $permissions;
    //     return view("role.assignPermissionForm", $data);
    // }

    // public function updateRolePermission(Role $role, RoleRequest $request)
    // {
    //     $this->authorize('assignPermission', new Permission());
    //     $inputs                     =   $request->except(["_token"]);
    //     $permissions                =   $inputs['permission'] ?? [];
    //     $role->permissions()->sync($permissions);
    //     Session::flash('success', 'Role permissions updated!');
    //     return redirect()->back();
    // }

    // public function permissionView()
    // {
    //     $this->authorize('view', new Permission());
    //     $modules            = Module::all()->pluck('name', 'id')->toArray();
    //     $modules            = ['0' => 'All'] + $modules;
    //     $data['modules']    = json_encode($modules, JSON_HEX_APOS);
    //     return view('permission.permissions', $data);
    // }

   
    
    // public function permissionList(Request $request)
    // {
    //     $this->authorize('view', new Permission());
    //     $pageIndex      = $request->pageIndex;
    //     $pageSize       = $request->pageSize;
    //     $permissions    = Permission::with('module');
    //     $module         = $request->get('module_id');
    //     $access         = $request->get('access');
    //     $description    = $request->get('description');
    //     if (!empty($module)) {
    //         $permissions->where('module_id', $module);
    //     }
    //     if (!empty($access)) {
    //         $permissions->where('access', 'like', '%' . $access . '%');
    //     }
    //     if (!empty($description)) {
    //         $permissions->where('description', 'like', '%' . $description . '%');
    //     }
    //     $data['itemsCount'] = $permissions->count();
    //     $data['data']       = $permissions->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
    //     return json_encode($data);
    // }

    // public function insertPermission(PermissionRequest $request)
    // {
    //     $this->authorize('insert', new Permission());
    //     $inputs                         =   $request->except("_token");
    //     $permission                     =   new Permission();
    //     $permission->module_id          =   $inputs['module_id'];
    //     $permission->access             =   $inputs['access'];
    //     $permission->description        =   $inputs['description'];
    //     $permission->save();
    //     $result                         = $permission->load('module');
    //     return $result;
    // }

    // public function updatePermission(PermissionRequest $request)
    // {
    //     $permission = Permission::findOrFail($request->input('id'));
    //     $this->authorize('update', $permission);
    //     $inputs                     =   $request->except(["_token"]);
    //     $module                     =   Module::find($inputs['module_id']);
    //     if (empty($module)) {
    //         $module = new Module();
    //         $module->name = $request->module_id;
    //         $module->save();
    //     }
    //     $permission->module_id      =   $inputs['module_id'];
    //     $permission->access         =   $inputs['access'];
    //     $permission->description    =   $inputs['description'];
    //     $permission->save();
    //     $result                     =   $permission->load('module');
    //     return $result;
    // }

    // public function deletePermission(Request $request)
    // {
    //     $permission = Permission::findOrFail($request->input('id'));
    //     $this->authorize('delete', $permission);
    //     $permission->delete();
    //     return json_encode('done');
    // }

    public function assignRoles()
    {
        $this->authorize('view', new Role());
        $data['employees']      =     User::where('is_active', 1)->pluck('email','id');
        return view('role.assignRole', $data);
    }

    // public function create(Request $request)
    // {
    //     $this->authorize('view', new Role());
        
    //     $data['userRoles']  = User::with('roles')->find($request->id)->roles->pluck('name', 'name')->toArray();
    //     $data['roles']      = Role::pluck('name', 'id')->toArray();
    //     return $data;
    // }

    // public function store(Request $request)
    // {
    //     $this->authorize('view', new Role());
    //     $user   = User::find($request->user);
    //     $roles  = $request->input('roles');
    //     if (empty($roles)) {
    //         $roles = array();
    //     }
    //     $user->roles()->sync($roles);
    //     return back()->with('success', 'Role Assigned Successfully.');
    // }


    public function bulkAssignRole()
    {
        $this->authorize('view', new Role());
        $data['employeeDepartments']      =     User::with('employee.department')->where('is_active', 1)
                                                    ->where('user_type', 'Employee')->whereHas('employee', function ($employee) {
                                                        $employee->select('biometric_id', 'name');
                                                    })->get()->groupBy('employee.department.name');
        $data['roles']                    =     Role::pluck('name','id')->toArray();
        return view('role.bulkAssign', $data);
    }

    public function bulkStore(Request $request)
    {
        $this->authorize('view', new Role());
        $users      =       User::with('roles')->find($request->user);
        $roles      =       $request->input('role');
        $name       =       array();
        if (empty($roles)) {
            $roles  =       array();
        }
        foreach ($users as $user) {
            $name    =      $user->roles->pluck('id', 'id')->toArray();
            $assign  =      array_merge($name, $roles);
            $user->roles()->sync($assign);
        }
        return back()->with('success', 'Role Assigned Successfully.');
    }
}
