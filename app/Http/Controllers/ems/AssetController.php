<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Mail\Action;
use App\Models\Asset;
use App\Models\Company;
use App\Models\Employee;
use App\Models\AssetLogs;
use App\Models\AssetType;
use App\Models\Department;
use App\Models\AssetSubType;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AssetAssignmentNotification;
use App\Notifications\AssetAssignmentNotificationToHR;
use App\Notifications\AssetAssignmentNotificationToUser;

class AssetController extends Controller
{
    public function __construct(Mailer $mailer)
    {
        $this->mailer       =       $mailer;
    }

    public function index(Request $request)
    {
        $this->authorize('view', new Asset());
        $assets             =       Asset::with('assetSubType.assetType');
        $data['types']      =       AssetType::pluck('name', 'id')->toArray();
        $data['sub_types']  =       AssetSubType::pluck('name', 'id')->toArray();
        $arr                =       ["Unassigned" => "Unassigned", "Assigned" => "Assigned", "Not Defined" => "Not Defined"];
        $statuses           =       Config::get('asset.status');
        $data['statuses']   =       array_merge($arr, $statuses);
        $data['assets']     =       $assets->orderBy('barcode')->paginate(25);
        return view('assets.index', $data);
    }

    public function assetList(Request $request)
    {
        $assets             =       Asset::select('assets.*', 'asset_sub_types.name as subType_name', 'asset_types.name as assetType_name', 'companies.name as company_name')
            ->leftJoin('asset_sub_types', 'assets.sub_type_id', '=', 'asset_sub_types.id')
            ->leftJoin('asset_types', 'asset_sub_types.asset_type_id', '=', 'asset_types.id')
            ->leftJoin('companies', 'companies.id', '=', 'assets.company_id');
        $assets             =       $this->filter($assets, $request);
        return DataTables::of($assets)
            ->addColumn('img', function ($assets) {
                $generator = new BarcodeGeneratorHTML();

                if (!empty($assets->barcode)) {
                    return $generator->getBarcode($assets->barcode, $generator::TYPE_CODE_128);
                } else {
                    return 'N/A';
                }
            })
            ->addIndexColumn()
            ->addColumn('edit', function ($assets) {

                $btn = '<a href="' . route("asset.edit", ["asset" => $assets->id]) . '" class="fa fa-edit"></a>';
                return $btn;
            })
            ->addColumn('detail', function ($assets) {
                $btn = '<a href="' . route("asset.show", ['asset' => $assets->id]) . '"
            class="btn btn-warning btn-lg p-3">Detail</a>';
                return $btn;
            })
            ->filterColumn('assetType_name', function ($query, $keyword) {
                $query->where('asset_types.name', 'like', "%$keyword%");
            })
            ->filterColumn('subType_name', function ($query, $keyword) {
                $query->where('asset_sub_types.name', 'like', "%$keyword%");
            })
            ->filterColumn('company_name', function ($query, $keyword) {
                $query->where('companies.name', 'like', "%$keyword%");
            })
            ->rawColumns(['img', 'edit', 'detail'])
            ->make(true);
    }

    public function filter($assets, $request)
    {
        if (request()->has('type')) {
            $assets         =       $assets->whereHas('assetSubType', function ($query) {
                $query->where('asset_type_id', request()->type);
            });
        }
        if (request()->has('sub_type')) {
            $assets         =       $assets->where('sub_type_id', $request->sub_type);
        }
        if (request()->has('bar_code')) {


            $assets         =       $assets->where('barcode', $request->bar_code);
        }
        if (request()->has('status')) {
            if ($request->status == "Unassigned") {

                $assets     =       $assets->whereNull('assigned_to')->where('status', "Working");
            } elseif ($request->status == "Assigned") {

                $assets     =       $assets->whereNotNull('assigned_to')->where('status', "Working");
            } elseif ($request->status == 'Not Defined') {
                $assets     =       $assets->whereNotNull('assigned_to')->whereNull('status');
            } else {
                $assets     =       $assets->where('status', $request->status);
            }
        }
        return $assets;
    }

    public function create()
    {
        $asset                  =  new Asset();
        $this->authorize('create', $asset);
        $data['asset']          =   $asset;
        $data['categories']     =   AssetCategory::pluck('name', 'id')->toArray();
        $data['status']         =   config('asset.status');
        $data['submitRoute']    =   ['asset.store'];
        $data['method']         =   'POST';
        return view('assets.form', $data);
    }

    public function getTypes(Request $request)
    {
        $this->authorize('view', new Asset());
        return AssetType::where('asset_category_id', $request->id)->pluck('name', 'id')->toArray();
    }

    public function getSubTypes(Request $request)
    {
        $this->authorize('view', new Asset());
        return AssetSubType::where('asset_type_id', $request->id)->pluck('name', 'id')->toArray();
    }

    public function getCompanyTypes(Request $request)
    {
        $this->authorize('view', new Asset());
        return Company::whereHas('assetSubTypes', function ($asset) {
            $asset->where('asset_sub_type_id', request()->id);
        })->pluck('name', 'id')->toArray();
    }

    public function store(Request $request)
    {
        $this->authorize('create', new Asset());
        $duplicateExists        =       Asset::where('barcode', $request->barcode)->first();

        if (!empty($duplicateExists) &&  $duplicateExists->sub_type_id != $request->sub_type_id) {
            return 'barcode already exists in different type';
        }
        $asset                  =   Asset::firstOrCreate([
            'sub_type_id'   =>  $request->sub_type_id,
            'barcode'       =>  $request->barcode,
        ]);
        $asset->status          =   $request->status;
        $asset->company_id      =   $request->company_id;
        $asset->description     =   $request->description;
        $asset->save();
        $log                    =   new AssetLogs();
        $log->asset_id          =   $asset->id;
        $log->user_id           =   auth()->user()->id;
        $log->action            =   $request->status;
        $log->save();
        return 'asset added';
    }

    public function show($id)
    {
        $this->authorize('view', new Asset());
        $data['asset']          =       Asset::with('assetSubType', 'assetLogs.user', 'assetLogs.assignedTo', 'assetDetail', 'company')->find($id);
        return view('assets.assetDetail', $data);
    }

    public function edit($id)
    {
        $this->authorize('update', new Asset());
        $data['asset']          =      Asset::find($id);
        $data['types']          =      AssetType::pluck('name', 'id')->toArray();
        $data['companies']      =      Company::pluck('name', 'id')->toArray();
        $data['submitRoute']    =      ['asset.update', ['asset' => $id]];
        $data['method']         =      'PUT';
        $data['subTypes']       =      AssetSubType::pluck('name', 'id')->toArray();
        $data['status']         =      config('asset.status');
        return view('assets.assetEdit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', new Asset());
        $asset                  =   Asset::findOrFail($id);
        $asset->sub_type_id     =   $request->sub_type_id;
        $asset->company_id      =   $request->company_id;
        $asset->status          =   $request->status;
        $asset->barcode         =   !empty($request->barcode) ? $request->barcode :  $asset->barcode;
        $asset->description     =   $request->description;
        if ($request->has('is_exported')) {
            $asset->is_exported     =   1;
        } else {
            $asset->is_exported     =   0;
        }
        $asset->update();
        $log                    =   new AssetLogs();
        $log->asset_id          =   $asset->id;
        $log->user_id           =   auth()->user()->id;
        $log->action            =   $request->status;
        if ($request->has('is_exported')) {
            $log->action        =   "Exported";
        }
        $log->save();
        $latestLog              =   AssetLogs::where('asset_id', $asset->id)->latest()->first();
        if (!empty($request->description) && $latestLog->reason != $request->description) {
            $log                =   new AssetLogs();
            $log->asset_id      =   $asset->id;
            $log->user_id       =   auth()->user()->id;
            $log->action        =   $request->description;
            $log->save();
        }
        return redirect(route('asset.index'))->with('success', 'Updated Successfully.');
    }

    public function assignEquipments(Request $request)
    {
        $this->authorize('assignEquipments', new Asset());

        $employees                      =   Employee::with([
            'user.assetAssignments.assetLogs' => function ($query) {
                $query->where('action', 'assign');
            },
            'user.assetAssignments.assetSubType.assetType.assetCategory', 'department'
        ])
            ->where('biometric_id', $request->id);
        $asset                          =   Asset::where('barcode', $request->id)->first();
        if (!empty($asset)) {
            return redirect(route('asset.show', ['asset' => $asset->id]));
        }
        if (auth()->user()->hasRole('powerUser')) {
            $employees          =   $employees->where('department_id', auth()->user()->employee->department_id);
        }
        $data['employee']       =   $employees->first();
        if (empty($data['employee'])) {
            abort(404);
        }
        return view('assets.assetAssignment', $data);
    }

    public function assignmentList(Request $request)
    {
        $this->authorize('assignmentList', new Asset());
        $employees          =   Employee::whereHas('user', function ($user) {
            $user->where('is_active', '1')->where('user_type', 'Employee');
        })->withoutGlobalScope('is_active')->with(['user' => function ($user) {
            $user->where('is_active', '1')->where('user_type', 'Employee');
        }, 'department', 'documents'])->whereIn('onboard_status', ['Onboard', 'Training']);
        $employeeNames      =   Employee::with("department")->whereHas('user', function ($user) {
            $user->where('is_active', 1)->where('user_type', 'Employee');
        })->withoutGlobalScope('is_active')->whereIn('onboard_status', ['Onboard', 'Training']);
        $data['employeeDepartments']      =   $employeeNames->select('biometric_id', 'user_id', 'department_id', 'name')
            ->get()->groupBy('department.name');
        if (request()->has('user_id')) {
            $employees      =   $employees->where('user_id', $request->user_id);
        }
        if (request()->has('department_id')) {
            $employees      =   $employees->where('department_id', $request->department_id);
            $employeeNames  =   $employeeNames->where('department_id', $request->department_id);
        }
        if (request()->has('barcode')) {
            $employees      =   $employees->whereHas('user.assetAssignments', function ($assets) {
                $assets->where('barcode', request()->barcode);
            });
        }
        $upsId              = AssetSubType::where('name', "UPS")->first()->id;
        $subType            = AssetSubType::find(request()->sub_type);
        if (request()->has('unassigned')) {
            if (request()->has('sub_type')) {

                if ($subType->name == "Charger")    // as person having ups charger is not provided.
                {
                    $employees  =   $employees->where(function ($query) use ($upsId) {
                        $query->whereHas('user', function ($user) use ($upsId) {
                            $user->where('is_active', '1')->where('user_type', 'Employee');
                        })->whereDoesNtHave('user.assetAssignments', function ($assets) use ($upsId) {
                            $assets->whereIn('sub_type_id', [request()->sub_type, $upsId]);
                        })->orWhereDoesNtHave('user.assetAssignments');
                    });
                } else {
                    $employees  =   $employees->where(function ($query) {
                        $query->whereHas('user', function ($user) {
                            $user->where('is_active', '1')->where('user_type', 'Employee');
                        })->whereDoesNtHave('user.assetAssignments', function ($assets) {
                            $assets->where('sub_type_id', request()->sub_type);
                        })->orWhereDoesNtHave('user.assetAssignments');
                    });
                }
            } else {
                $employees      =   $employees->whereDoesNtHave('user.assetAssignments');
            }
        }
        $data['departments']            =        Department::pluck('name', 'id')->toArray();
        $data['assetSubTypes']          =        AssetSubType::where('is_assignable', '1')->pluck('name', 'id')->toArray();
        if (auth()->user()->hasRole('powerUser')) {
            $data['employees']          =        $employees->where('department_id', auth()->user()->employee->department_id)->orderBy('id', 'desc')->paginate(25);
            $data['employeeNames']      =        $employeeNames->where('department_id', auth()->user()->employee->department_id)->pluck('name', 'name')->toArray();
        } else {
            $data['employees']          =        $employees->get();
            $data['employeeNames']      =        $employeeNames->pluck('name', 'name')->toArray();
        }
        return view('assets.assignmentList', $data);
    }


    public function assignAsset(Request $request)
    {
        $this->authorize('assignAsset', new Asset());

        $asset = Asset::whereNotNull('status')
            ->whereNotIn('status', ['Damaged', 'Maintenance'])
            ->where('barcode', $request->assetBarCode)
            ->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }
        
        $employee = Employee::where('biometric_id', $request->biometric_id)->first();

        if (!$employee) {
            return response()->json(['message' => 'Invalid employee biometric ID'], 404);
        }

        if ($request->action === 'unassign') {
            
            return $this->unassign($asset, $employee);
            
        } else {

            return $this->assign($asset, $employee);
        }
    }

    public function assign($asset, $employee)
    {        
        if ($asset->assigned_to === $employee->user_id) {
            return response()->json(['message' => 'Asset is already assigned to this employee.'], 409);
        }
        
        if ($asset->assigned_to !== null && $asset->assigned_to !== $employee->user_id) {
            return response()->json(['message' => 'Asset is already assigned to another employee.'], 409);
        }

        $asset->assigned_to = $employee->user_id;
        $asset->save();

        if ($employee->user) {
            $notification = new AssetAssignmentNotificationToUser(
                $asset,
                'assign',
                $employee,
            );
            
            $employee->user->notify($notification);
        }

        $pdfPath = storage_path("app/documents/employee/{$employee->id}/{$employee->documents->asset_policy}");
        
        $notification = new AssetAssignmentNotificationToHR(
            $asset,
            $employee,
            $pdfPath,
        );

        $selectedHrUsers = User::whereIn('id', [22, 392])->get();
        foreach ($selectedHrUsers as $hrUser) {
            if (!empty($hrUser)) {
                $hrUser->notify($notification);
            }
        }

        $log                = new AssetLogs();
        $log->asset_id      = $asset->id;
        $log->user_id       = auth()->user()->id;
        $log->assigned_to   = $employee->user_id;
        $log->action        = 'assign';
        $log->save();

        return response()->json([
            'message'   => 'Asset assigned',
            'asset'     => $asset->assetSubType->name,
            'view'      => view('assets.assetComponent', ['assetAssignment' => $asset])->render(),
        ]);
    }

    public function unassign($asset, $employee)
    {
        if (!$asset->assigned_to) {
            return response()->json(['message' => 'Asset already unassigned']);
        }
        
        if ($employee->user_id !== $asset->assigned_to) {
            return response()->json(['message' => 'Invalid employee against asset.']);
        }

        $asset->assigned_to = null;
        $asset->save();

        $notification = new AssetAssignmentNotificationToUser(
            $asset,
            'unassign',
            $employee,
        );
        if ($employee->user) {
            $employee->user->notify($notification);
        }

        return response()->json(['message' => 'Asset unassigned']);
    }

    public function dashboard(Request $request)
    {
        $this->authorize('dashboard', new Asset());
        $subTypes       =       AssetSubType::with(['assetType', 'assets.user', 'assets' => function ($asset) {
            if (request()->has('status')) {
                $asset->where('status', request()->status);
            }
        }])->get();
        if ($request->has('type_id')) {
            $subTypes  =        $subTypes->where('id', $request->type_id);
        }
        $subTypesCount =        [];
        foreach ($subTypes as $subType) {
            $assetCount['assetType']            =   $subType->assetType->name ?? "";
            $assetCount['subTypeName']          =   $subType->name;
            $assetCount['id']                   =   $subType->id;
            $assetCount['maintenanceCount']     =   $subType->assets->where('status', "Maintenance")->count();
            $assetCount['damagedCount']         =   $subType->assets->where('status', "Damaged")->count();
            $assetCount['assignedCount']        =   $subType->assets->where('status', "Working")->whereNotNull('assigned_to')->count();
            $assetCount['workingCount']         =   $subType->assets->where('status', "Working")->count();
            $assetCount['unassignedCount']      =   $subType->assets->where('status', "Working")->whereNull('assigned_to')
                ->where('status', '<>', "Maintenance")->where('status', '<>', "Damaged")->count();
            $assetCount['totalCount']           =   $subType->assets->count();
            $subTypesCount[]                    =   $assetCount;
        }
        $data['subTypesCount']                 =   collect($subTypesCount)->sortBy('assetType');
        $assets                                =   Asset::get();
        if ($request->has('type_id')) {
            $assets                            =   $assets->where('sub_type_id', $request->type_id);
        }
        if ($request->status) {
            $assets->where('status', $request->status);
        }
        $pieChart                              =   $this->pieChart($assets);
        $data['pieChartValues']                =   $pieChart['values'];
        $data['pieChartLabels']                =   $pieChart['labels'];
        $barChartTypes                         =   $this->barChart($subTypes);
        $data['barChart']                      =   collect($barChartTypes)->sortByDesc('total')->values();
        $data['types']                         =   AssetSubType::pluck('name', 'id');
        $data['statuses']                      =   ["Damaged" => "Damaged", "Maintenance" => "Maintenance", "Working" => "Working", "Assigned" => "Assigned", "Unassigned" => "Unassigned"];
        $data['employeeCount']                 =   User::whereIn('user_type', ['Employee'])->whereHas('employee', function ($employee) {
            $employee->whereIn('onboard_status', ['Onboard', 'Training']);
        })->where('is_active', 1)->count();
        $data['havingUPS']                     =   Asset::whereHas('assetSubType', function ($subType) {
            $subType->where('name', 'UPS');
        })->whereHas('user')->count();
        $dashboard                              =  new EmployeeDashboardController();
        $data['departmentUnassignedAssets']     =  $dashboard->assetData();
        $data['subTypes']                       =  AssetSubType::whereIn('name', ["Laptop", 'Charger', 'Mouse', 'Headphone'])->pluck('id', 'name');
        $data['totalEmployees']                 =  Employee::withoutGlobalScopes(['guest'])->whereHas('user', function ($user) {
            $user->where('user_type', 'Employee');
        })->whereIn('onboard_status', ['Onboard', 'Training'])->count();
        return view('assets.dashboard', $data);
    }


    public function pieChart($assets)
    {
        $pieChart           =    [];
        $damaged            =    $assets->where('status', "Damaged")->count();
        $maintenance        =    $assets->where('status', "Maintenance")->count();
        $assignedCount      =    $assets->where('status', "Working")->whereNotNull('assigned_to')->count();
        $workingCount       =    $assets->where('status', "Working")->count();
        $unassignedCount    =    $assets->where('status', "Working")->whereNull('assigned_to')->where('status', '<>', "Maintenance")->where('status', '<>', "Damaged")->count();
        $labels             =    ["Damaged ($damaged)", "Working ($workingCount)", "Maintenance($maintenance)", "Assigned ($assignedCount)", "Unassigned($unassignedCount)"];
        $pieChart           =    [$damaged, $workingCount, $maintenance, $assignedCount, $unassignedCount];
        return ['labels' => $labels, 'values' => $pieChart];
    }

    public function barChart($subTypes)
    {
        $barChartTypes                    =       [];
        foreach ($subTypes as $subType) {
            $chart['subTypeName']         =     $subType->name . "(" . $subType->assets->count()  . ")";
            $chart['maintenanceCount']    =     $subType->assets->where('status', "Maintenance")->count();
            $chart['damagedCount']        =     $subType->assets->where('status', "Damaged")->count();
            $chart['assignedCount']       =     $subType->assets->where('status', "Working")->whereNotNull('assigned_to')->count();
            $chart['workingCount']        =     $subType->assets->where('status', "Working")->count();
            $chart['unassignedCount']     =     $subType->assets->where('status', "Working")->whereNull('assigned_to')->where('status', '<>', "Maintenance")->where('status', '<>', "Damaged")->count();
            $barChartTypes[]              =     $chart;
        }
        return  $barChartTypes;
    }
}
