<?php

namespace App\Http\Controllers\ems;

use App\User;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleRequest;
use Yajra\DataTables\Facades\DataTables;

class ModuleController extends Controller
{

    
    public function index(Request $request)
    {
        if ($request->ajax() === false) {
            $this->authorize('view', new Module());
            return view('module.modules');
        }
    
        $query = Module::select('module.*')->withTrashed(); // Include soft deleted records
         
        return DataTables::of($query)
            ->addColumn('action', function ($query) {
    
                if ($query->deleted_at !== null) { // If module is soft deleted
                    $restoreBtn = '<form action="' . route("modules.restore",['id'=>$query->id]) . '" method="POST" style="display: inline;">
                      ' . csrf_field() . '
                      ' . method_field('PUT') . '
                      <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-success btn-rounded">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bootstrap-reboot" viewBox="0 0 16 16">
                        <path d="M1.161 8a6.84 6.84 0 1 0 6.842-6.84.58.58 0 1 1 0-1.16 8 8 0 1 1-6.556 3.412l-.663-.577a.58.58 0 0 1 .227-.997l2.52-.69a.58.58 0 0 1 .728.633l-.332 2.592a.58.58 0 0 1-.956.364l-.643-.56A6.812 6.812 0 0 0 1.16 8z"></path>
                        <path d="M6.641 11.671V8.843h1.57l1.498 2.828h1.314L9.377 8.665c.897-.3 1.427-1.106 1.427-2.1 0-1.37-.943-2.246-2.456-2.246H5.5v7.352h1.141zm0-3.75V5.277h1.57c.881 0 1.416.499 1.416 1.32 0 .84-.504 1.324-1.386 1.324h-1.6z"></path>
                      </svg>
                    </button>
                    </form>';
                    return $restoreBtn;
                } else { // If module is not soft deleted
                    $editBtn = '<a href="' . route("modules.edit", ['module' => $query->id]) . '""><i class="mdi mdi-table-edit" style="font-size:25px;"></i></a>&nbsp;&nbsp;&nbsp;';
                    $deleteBtn = '<form action="' . route("modules.destroy", ['module' => $query->id]) . '" method="POST" style="display: inline;">
                      ' . csrf_field() . '
                      ' . method_field('DELETE') . '
                      <button type="submit"  onclick="return confirm(\'Are you sure?\')" style="border-color: red;"><i class="fa fa-trash" style="color:red"></i></button>
                    </form>';
                    return $editBtn . $deleteBtn;
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
    public function create()
    {
        $this->authorize('insert', new Module());
        $data['submitRoute'] = 'modules.store'; // update with the name of your store route
        $data['method'] = 'POST';
        return view('module.form', compact('data'));
    }

    public function store(ModuleRequest $request)
    {
        $this->authorize('insert', new Module());
        $module = new Module([
            'name' => $request->input('name')
        ]);
        $module->save();
        return redirect()->route('modules.index')->with('success', 'Module Created successfully.');
    }

    public function edit($id)
    {
        $module = Module::findOrFail($id);
        $this->authorize('update', $module);
        $data['submitRoute']=['modules.update',$id];
        $data['method'] = 'PUT';
        return view('module.form', compact('module', 'data'));
    }

    public function update(ModuleRequest $request, $id)
    {
        $module = Module::findOrFail($id);
        $this->authorize('update', $module);
        $module->name = $request->input('name');
        $module->save();
        return redirect()->route('modules.index')->with('success', 'Module Updated successfully.');
    }

    public function destroy($id)
    {
        $module = Module::findOrFail($id);
        $this->authorize('delete', $module);
        $module->delete();
        return back()->with('success', 'Module deleted successfully.');
    }
    public function restore($id)
    {
    $permission = Module::onlyTrashed()->findOrFail($id);
    $permission->restore();
    return redirect()->route('modules.index')->withSuccess('Module restored successfully.');
    }

    // public function moduleView()
    // {
    //     $this->authorize('view', new Module());
    //     return view('module.modules');
    // }

    // public function moduleList(Request $request)
    // {
    //     $this->authorize('view', new Module());
    //     $pageIndex  = $request->pageIndex;
    //     $pageSize   = $request->pageSize;
    //     $modules    = Module::query();
    //     if (!empty($request->get('name'))) {
    //         $modules  = $modules->where('name', 'like', '%' . $request->get('name') . '%');
    //     }
    //     $data['itemsCount'] = $modules->count();
    //     $data['data']       = $modules->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
    //     return json_encode($data);
    // }
    
    // public function insertModule(ModuleRequest $request)
    // {
    //     $this->authorize('insert', new Module());
    //     $module         = new Module();
    //     $module->name   = $request->name;
    //     $module->save();
    //     if (!empty($module->id))
    //         return $module;
    // }

    // public function updateModule(ModuleRequest $request)
    // {
    //     $module              = Module::findOrFail($request->input('id'));
    //     $this->authorize('update', $module);
    //     $module->name        = $request->name;
    //     $module->save();
    //     if (!empty($module->id))
    //         return $module;
    // }

    // public function deleteModule(Request $request)
    // {
    //     $module = Module::findOrFail($request->input('id'));
    //     $this->authorize('delete', $module);
    //     $module->delete();
    //     return json_encode('done');
    // }
}
