<?php

namespace App\Http\Controllers\ems;

use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax() === false) {
            $this->authorize('view', new Permission());
            $data['modules']      = Module::all()->pluck('name', 'id')->toArray();
            return view('permission.permissions', $data);
        }

        $moduleId = $request->get('module_id');

        $query = Permission::leftJoin('module', 'module.id', 'permission.module_id')
            ->select('permission.*', 'module.name as module_name')
            ->groupBy('permission.id')
            ->when($moduleId, function ($query, $moduleId) {
                return $query->where('module.id', $moduleId);
            })
            ->withTrashed(); // Include soft deleted records

        return DataTables::of($query)
            ->addColumn('action', function ($query) {
                $editRoute      = route("permission.edit", ['permission' => $query->id]);
                $deleteRoute    = route("permission.destroy", ['permission' => $query->id]);
                $restoreRoute   = route("permission.restore", ['id' => $query->id]);

                if ($query->deleted_at !== null) {
                    $data1 = '<span class="fa fa-recycle" onclick="deleteItem(\'' . $restoreRoute. '\', \'PUT\')"> </span>';
                } else {
                    $data1 = '<a href="' . $editRoute . '" class="fa fa-edit mr-2"></a>';
                    $data1 .= '<span class="fa fa-trash" onclick="deleteItem(\'' . $deleteRoute. '\')"> </span>';
                }

                return $data1;
            })
            ->rawColumns(['action'])
            ->make(true);
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

    public function create()
    {
        $this->authorize('insert', new Permission());
        $data['permissions'] = new Permission();
        $data['submitRoute'] = 'permission.store';
        $data['method']      =   'POST';
        $data['modules']     = Module::pluck('name', 'id')->toArray();
        return view('permission.form', $data);
    }

    public function store(PermissionRequest $request)
    {
        $this->authorize('insert', new Permission());
        $inputs = $request->validated();
        $permission = new Permission();
        $permission->module_id = $inputs['module_id'];
        $permission->access = $inputs['access'];
        $permission->description = $inputs['description'];
        $permission->save();

        return redirect()->route('permission.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        // Authorize the action
        $this->authorize('update', $permission);

        // Get all the modules
        $modules = Module::pluck('name', 'id');
        $selectedModule = $permission->module_id;
        // Return the view with necessary data
        return view('permission.editForm', [
            'permission' => $permission,
            'modules' => $modules,
            'selectedModule' => $selectedModule
        ]);
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        // Authorize the action
        $this->authorize('update', $permission);

        // Retrieve the inputs from the request
        $inputs = $request->only(['module_id', 'access', 'description']);

        // Check if the module exists or not
        $module = Module::find($inputs['module_id']);
        if (empty($module)) {
            // If module does not exist, create a new one with given name
            $module = new Module();
            $module->name = $request->module_id;
            $module->save();
        }

        // Update the permission and save the changes
        $permission->fill($inputs);
        $permission->save();

        return redirect()->route('permission.index')->with('success', 'Permission updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request)
    {
        $permission = Permission::find($request->permission);
        $this->authorize('delete', $permission);
        $permission->delete();
        
        return response()->json(
            ['success' => 'Permission deleted successfully.']
        );
    }

    public function restore($id)
    {
        $permission = Permission::onlyTrashed()->findOrFail($id);
        $permission->restore();
        return response()->json(
            ['success' => 'Permission restored successfully.']
        );
    }
}
