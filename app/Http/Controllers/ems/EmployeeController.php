<?php

namespace App\Http\Controllers\ems;

use App\User;
use Exception;
use Carbon\Carbon;
use App\Mail\Action;
use App\Models\Role;
use App\Models\Document;
use App\Models\Employee;
use App\Classes\ClivSync;
use App\Models\ShiftType;
use App\Models\BankDetail;
use App\Models\Department;
use App\Imports\TestImport;
use App\Models\Designation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Qualification;
use App\Exports\EmployeeExport;
use App\Models\TrainingEmployee;
use Yajra\DataTables\DataTables;
use App\Classes\SalesSupportSync;
use App\Classes\TrainerPortalSync;
use App\Models\EmployeeExitDetail;
use App\Exports\ExitEmployeeExport;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\EmployeeProfileDraft;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\EmployeeRequest;
use App\Models\PendingProfileReminder;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\EmployeeEmergencyContact;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Encryption\DecryptException;

class EmployeeController extends Controller
{
    private $Image_prefix;
    public function __construct()
    {
        $this->Image_prefix     = "userImage";
    }

    public function view()
    {
        $this->authorize('hrEmployeeList', new Employee());
        $data['employeeDepartments']  	=   Employee::with('user')->select('biometric_id','user_id','department_id','name')->whereHas('user',function($user)
                                            {
                                                $user->where('is_active',1)->where('user_type','Employee');
                                            })->get()->groupBy('department.name');
        $data['office_emails']          =   Employee::where('is_active',1)->pluck('office_email', 'office_email')->toArray();
        $data['department_id']          =   Department::pluck('name', 'id')->toArray();
        $data['shift_types']            =   ['Morning' => 'Morning', 'Evening' => 'Evening'];
        $data['userTypes']              =   config('employee.userTypes');
        $data['gender']                 =   config('employee.gender');
        $data['shiftTypes']             =   ShiftType::get();
        return view('employee.employee', $data);
    }

    public function employeeList(Request $request, $export = false)
    {
        $employees          =   Employee::withoutGlobalScopes(['is_active', 'guest'])
                                ->select('employee.id','employee.is_active','employee.user_id','employee.join_date','employee.gender','employee.contract_date','users.name','employee.office_email','employee.biometric_id','departments.name as department_name',
                                'users.user_type', 'users.off_day','users.profile_pic', 'documents.aadhaar_number',
                                'users.shift_type_id as shift_time', 'shift_types.name as shift_type', 'shift_types.start_time as start_time', 'shift_types.end_time as end_time', 
                                'qualification.name as qualification', 'employee.personal_email')
                                ->leftJoin('departments', 'employee.department_id', '=', 'departments.id')
                                ->leftJoin('users', 'employee.user_id', '=', 'users.id')
                                ->leftJoin('shift_types', 'users.shift_type_id', '=', 'shift_types.id')
                                ->leftJoin('qualification', 'employee.qualification_id', '=', 'qualification.id')
                                ->leftJoin('documents', 'employee.id', '=', 'documents.employee_id');
        $employees          =   $this->filter($employees, $request);
        if ($export) {
            return $employees->get();
        }
        return DataTables::of($employees)
            ->addColumn('image_source', function($employees){
                if(!empty($employees->profile_pic) && Storage::exists( "image/employee/".$employees->profile_pic))
                {
                    $imagePath  =    url("employee/picture/".$employees->profile_pic);
                }
                else
                {
                    $imagePath =    url('/img/user.jpg');
                }
                $btn = '<a target="_blank" href="'.$imagePath.'" class="employee-image">
                            <img src="'.$imagePath.'" width="42" height="42"></a>';
                
                return $btn;
            })
            ->addColumn('is_active', function($employees){
                if ($employees->is_active == 1)
                {
                $btn = "Active";
                }
                else{
                $btn = "Exit";
                }
                return $btn;
            })
            ->addColumn('action', function($employees){

                $btn = '<a href="'. route("employeeDetail", ["employee" => $employees->id]).'" class="p-2 text-primary fas fa-address-card" style="font-size:20px;border-radius:5px;"></a>';
                return $btn;
            })
            ->filterColumn('department_name', function($query, $keyword){
                $query->where('departments.name', 'like', "%$keyword%");
            })
            ->filterColumn('user_type', function($query, $keyword){
                $query->where('users.user_type', 'like', "%$keyword%");
            })
            ->rawColumns(['image_source','is_active','action'])
            ->make(true);
    }

    public function filter($employees, $request)
    {
        if (request()->has('user_type')) {
            $employees =      $employees->whereIn('user_type', request()->user_type);
        } else {
            $employees      = $employees->whereIn('onboard_status', ['Onboard', 'Training'])->where('user_type', 'Employee');
        }
        if (request()->has('user_id')) {
            $employees->where('user_id', $request->user_id);
        }
        if (request()->has('office_email')) {
            $employees  = $employees->where('office_email', $request->office_email);
        }

        if (request()->filled('status')) {
            $status = Arr::wrap($request->status);
            if (in_array('exit', $status) && in_array('active', $status) === false) {
                $employees    = $employees->where('employee.is_active', 0);
            }elseif (in_array('active', $status) && in_array('exit', $status) === false) {
                $employees    = $employees->where('employee.is_active', 1);
            }
        }else{
            $employees    = $employees->where('employee.is_active', 1);
        }

        // if ($request->status == 'exit') {
        //     $employees    = $employees->where('employee.is_active', 0);
        // } else {
        //     $employees    = $employees->where('employee.is_active', 1);
        // }
        if (request()->has('department_id')) {
            $employees      = $employees->where('department_id', $request->department_id);
        }
        if (request()->has('shift_time')) {
            $employees      = $employees->where('shift_types.id', request()->shift_time);
        }
        if (request()->has('shift_type')) {
            $employees      =   $employees->where('shift_types.name', request()->shift_type);
        }
        if (request()->has('gender')) {
            $employees  =   $employees->where('gender', $request->gender);
        }
        if (request()->has('biometric_id')) {
            $employees  =   $employees->where('biometric_id', $request->biometric_id);
        }
        if (request()->has('is_power_user'))
        {
            $employees  =   $employees->where('is_power_user', '1');
        }
        if(request()->has('dateTo') &&  request()->has('dateFrom'))
        {
           $employees   =   $employees->whereBetween("contract_date", [$request->dateFrom, $request->dateTo]);
        }
        return $employees;
    }

    public function create()
    {
        $this->authorize('hrUpdateEmployee', new Employee());
        $data['employee']                  =        new Employee();
        $data['submitRoute']               =        "insertEmployee";
        $list['departments']               =        Department::pluck('name', 'id')->toArray();
        $list['qualification']             =        Qualification::pluck('name', 'id')->toArray();
        $data['list']                      =        $list;
        $data['designations']              =        Designation::pluck('name', 'id')->toArray();
        $data['documents']                 =        collect();
        $data['shifts']                    =        ShiftType::all();
        $data['days']                      =        config('employee.days');
        $data['userTypes']                 =        config('employee.userTypes');
        return view('employee.employeeForm', $data);
    }

    public function insert(EmployeeRequest $request)
    {
        $userInputs                        =    $request->only(['name', 'personal_email', 'office_email']);
        $user                              =    new User();
        $user->name                        =    $userInputs['name'];
        $user->email                       =    strtolower($userInputs['office_email']);
        $password                          =    'Welcome@123';
        $user->password                    =   \Hash::make($password);
        $user->is_active                   =    "1";
        $user->user_type                   =   empty($request->user_type) ? 'Employee' : $request->user_type;
        if (!empty($request->off_day)) {
            $user->off_day                 =    $request->off_day;
        }
        if ($request->hasFile('profile_pic')) {
            $user->profile_pic             =   $this->uploadProfilePic($request);
        }
        $user->save();
        $role    = Role::where('name', 'employee')->first();
        $user->roles()->sync($role->id);
        $user->save();
        $userDetailInputs                   =   $request->except(['name', 'personal_email']);
        if (!empty($userDetailInputs))
        {
            $employee                       =   new Employee();
            $employee->name                 =   $request->name;
            $employee->registration_id      =   $request->registration_id;
            $employee->personal_email       =   $request->personal_email;
            $employee->department_id        =   $request->department_id;
            $employee->phone                =   $request->phone;
            $employee->birth_date           =   $request->birth_date;
            $employee->join_date            =   $request->join_date;
            $employee->biometric_id         =   $request->biometric_id;
            $employee->qualification_id     =   $request->qualification_id;
            $employee->contract_date        =   $request->contract_date;
            $employee->second_contract_date =   $request->second_contract_date;
            $employee->user_id              =   $user->id;
            $employee->office_email         =   $request->office_email;
            $employee->shift_type_id        =   $request->shift_type_id;
            $employee->gender               =   $request->gender;
            if ($request->is_active == 'on') {
                $employee->is_active        =   1;
            }
            $employee->designation_id       =   $request->designation_id;
            $employee->pf_no                =   $request->pf_no;

            // if ($request->hasFile('profile_pic')) {
            //     $employee->profile_pic      =   $this->uploadProfilePic($request);
            // }

            if($request->onboard_status == 'Training'){
                $training_employee          =   new TrainingEmployee();
                $training_employee->user_id =   $user->id;
                $training_employee->save();
            }
            $employee->onboard_status       =   $request->onboard_status ?? 'Onboard';
            $employee->save();
            $user       =       $employee->user;
            if (!empty($user)) {
                if ($request->has('start_time')) {
                    $user->start_time       =   $request->start_time;
                }
                if ($request->has('end_time')) {
                    $user->end_time         =   $request->end_time;
                }
                $user->save();
            }
            if ($request->bank_details_form == 'on') {
                //save bank details
                $bankDetail                 = new BankDetail();
                $bankDetail->employee_id    = $employee->id;
                $bankDetail->account_holder = $request->account_holder;
                $bankDetail->bank_name      = $request->bank_name;
                $bankDetail->account_no     = $request->account_no;
                $bankDetail->ifsc_code      = $request->ifsc_code;
                $bankDetail->save();
            }
            $employee->user_id       = $user->id;
            $employee->office_email  = strtolower($request->office_email);
            $employee->save();

            // Documents Upload
            $uploadedDocuments  = [];
            if ($request->hasFile('aadhaar_file')) {
                $uploadedDocuments['aadhaar_file']  =   $this->uploadDocuments($request, 'aadhaar_file', $employee->id);
            }
            if ($request->hasFile('pan_file')) {
                $uploadedDocuments['pan_file']  =   $this->uploadDocuments($request, 'pan_file', $employee->id);
            }
            if ($request->hasFile('cv')) {
                $uploadedDocuments['cv']    =   $this->uploadDocuments($request, 'cv', $employee->id);
            }
            $this->saveDocuments($request, $employee->id, $uploadedDocuments);
        }
        $employee->rawPassword                 =    $password;
        $this->sendPassword($employee);
        return back()->with('success', 'Employee Registered successfully');
    }

    public function edit(Request $request)
    {
        $employee               =   Employee::withoutGlobalScopes()->find($request->employee);
        $this->authorize('hrUpdateEmployee', $employee);
        $data['employee']       =   $employee->load('designation', 'documents');
        $data['documents']      =   $employee->documents;
        $data['submitRoute']    =   array('updateEmployee');
        $list['departments']    =   Department::pluck('name', 'id')->toArray();
        $list['qualification']  =   Qualification::pluck('name', 'id')->toArray();
        $data['list']           =   $list;
        $data['designations']   =   Designation::pluck('name', 'id')->toArray();
        $data['shifts']         =   ShiftType::all();
        $data['days']           =   config('employee.days');
        $data['userTypes']      =   config('employee.userTypes');
        return view('employee.employeeForm', $data);
    }

    public function update(EmployeeRequest $request)
    {
        $documents  = $request->only(['aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy']);
        $employee   = Employee::withoutGlobalScopes()->find($request->id);
        if ($employee->department_id !=  $request->department_id) {
            $users      = User::havingRole('Admin');
            $message    = $employee->name .  "   department is changed";
            $route      = ['name' => 'user.edit', 'parameter' => $employee->user->id];
            send_notification($users, $message, $route);
        }
        // if ($request->hasFile('profile_pic')) {

        //     $employee->profile_pic = $this->uploadProfilePic($request, $employee->profile_pic);
        // }
        $employee->name             = $request->name;
        $employee->personal_email   = $request->personal_email;
        $employee->phone            = $request->phone;
        $employee->birth_date       = $request->birth_date;
        $employee->join_date        = $request->join_date;
        $employee->department_id    = $request->department_id;
        $employee->qualification_id = $request->qualification_id;
        $employee->registration_id  = $request->registration_id;
        if ($request->has('is_active')) {
            $employee->is_active        = empty($request->is_active) ? 0 : 1;
        }
        $employee->contract_date    = $request->contract_date;
        $employee->second_contract_date =   $request->second_contract_date;
        $employee->designation_id   = $request->designation_id;
        $employee->biometric_id     = $request->biometric_id;
        if($employee->office_email != $request->office_email && str_contains($request->office_email,'theknowledgeacademy.com'))
        {
            $data['link']       =    route('login');
            $data['message']    =    "Office email update to $request->office_email";
            $department = Department::with('deptManager')->where('id',$employee->department_id)->first();
            $to = [$request->office_email, $department->deptManager->office_email];
            $message = (new Action($employee, $data,'Email Updated','email.action'))->onQueue('emails');
            Mail::to($to)->later(Carbon::now()->addSeconds(30),$message);
        }
        $employee->office_email     =   $request->office_email;
        $employee->gender           =   $request->gender;
        $employee->is_power_user    =   !empty($request->is_power_user) ? 1 : 0;
        $employee->pf_no            =   $request->pf_no;
        if($request->onboard_status == 'Training'){
            TrainingEmployee::updateOrCreate(['user_id' => $employee->user_id]);
        }
        $employee->onboard_status   = $request->onboard_status ?? 'Onboard';
        $employee->save();
        $user                       =   $employee->user;
        $user->shift_type_id        =   $request->shift_type_id;
        if (!empty($request->off_day)) {
            $user->off_day          =   $request->off_day;
        }
        $user->user_type            =   $request->user_type;
        $user->email                =   $request->office_email;
        if ($request->hasFile('profile_pic')) {

            $user->profile_pic      = $this->uploadProfilePic($request, $employee->user->profile_pic);
        }
        $user->save();
        //save bank details
        if (!empty($employee->bankdetail)) {
            $bankDetail = $employee->bankdetail;
        } else {
            $bankDetail = new BankDetail();
        }
        $bankDetail->employee_id    =   $employee->id;
        $bankDetail->account_holder =   $request->account_holder;
        $bankDetail->bank_name      =   $request->bank_name;
        $bankDetail->account_no     =   $request->account_no;
        $bankDetail->ifsc_code      =   $request->ifsc_code;
        $bankDetail->save();
        // documents upload
        $currentDocumentDetails     = Document::select('aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy')->where('employee_id', $request->id)->first();
        if (!empty($currentDocumentDetails)) {
            $currentDocumentDetails = $currentDocumentDetails->toArray();
            $newDocumentDetails     = array_diff($documents, $currentDocumentDetails);
        } else {
            $newDocumentDetails     = $documents;
        }
        if (!empty($newDocumentDetails)) {
            $uploadedDocuments  = [];
            if ($request->hasFile('aadhaar_file')) {
                $uploadedDocuments['aadhaar_file']  = $this->uploadDocuments($request, 'aadhaar_file', $employee->id, $currentDocumentDetails['aadhaar_file'] ?? null);
            }
            if ($request->hasFile('pan_file')) {
                $uploadedDocuments['pan_file']  = $this->uploadDocuments($request, 'pan_file', $employee->id, $currentDocumentDetails['pan_file'] ?? null);
            }
            if ($request->hasFile('cv')) {
                $uploadedDocuments['cv']    = $this->uploadDocuments($request, 'cv', $employee->id, $currentDocumentDetails['cv'] ?? null);;
            }
            if ($request->hasFile('cheque')) {
                $uploadedDocuments['cheque']    = $this->uploadDocuments($request, 'cheque', $employee->id, $currentDocumentDetails['cheque'] ?? null);;
            }
            if ($request->hasFile('asset_policy')) {
                $uploadedDocuments['asset_policy']    = $this->uploadDocuments($request, 'asset_policy', $employee->id, $currentDocumentDetails['asset_policy'] ?? null);;
            }
            $this->saveDocuments($request, $employee->id, $uploadedDocuments);
        }

        $employee->save();
        return  back()->with('success', 'Data updated successfully');
    }

    private function uploadProfilePic($request, $old_profile_pic = null)
    {
        if (!empty($old_profile_pic)) {
            $fileName   = 'image/employee/' . $old_profile_pic;
            if (Storage::exists($fileName)) {
                Storage::delete($fileName);
            }
        }
        $imageName = $this->Image_prefix . Carbon::now()->timestamp . '.' . $request->file('profile_pic')->getClientOriginalExtension();
        $request->file('profile_pic')->move(storage_path('app/image/employee'), $imageName);
        
        return $imageName;
    }

    private function uploadDocuments($request, $fileName, $employee_id, $old_file = null)
    {
        if (!empty($old_file)) {
            if (Storage::exists("documents/employee/$employee_id/$old_file")) {
                Storage::delete("documents/employee/$employee_id/$old_file");
            }
        }
        $file = $fileName . Carbon::now()->timestamp . '.' . $request->file($fileName)->getClientOriginalExtension();
        $request->file($fileName)->move(storage_path('app/documents/employee/' . $employee_id), $file);
        return $file;
    }

    private function saveDocuments($request, $employee_id, $uploadedDocuments)
    {
        $profile    = null;
        if (!empty($request->id)) {
            $profile    = Document::where('employee_id', $employee_id)->first();
        }
        if (empty($profile)) {
            $profile                = new Document();
            $profile->employee_id   = $employee_id;
        }
        $profile->aadhaar_number    = $request->aadhaar_number;
        if (!empty($uploadedDocuments) && array_key_exists('aadhaar_file', $uploadedDocuments)) {
            $profile->aadhaar_file = $uploadedDocuments['aadhaar_file'];
        }
        $profile->pan_number    = $request->pan_number;
        if (!empty($uploadedDocuments) && array_key_exists('pan_file', $uploadedDocuments)) {
            $profile->pan_file = $uploadedDocuments['pan_file'];
        }
        if (!empty($uploadedDocuments) && array_key_exists('cv', $uploadedDocuments)) {
            $profile->cv = $uploadedDocuments['cv'];
        }
        if (!empty($uploadedDocuments) && array_key_exists('cheque', $uploadedDocuments)) {
            $profile->cheque = $uploadedDocuments['cheque'];
        }
        if (!empty($uploadedDocuments) && array_key_exists('asset_policy', $uploadedDocuments)) {
            $profile->asset_policy = $uploadedDocuments['asset_policy'];
        }
        $profile->save();
    }

    public function detail(Request $request)
    {
        $this->authorize('viewProfile', new Employee());
        $employeeId     =   $request->employee;
        if(!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('HR') && !auth()->user()->hasRole('manager') && !auth()->user()->hasRole('HR Junior') && !auth()->user()->hasRole('Line Manager'))
        {
            $employeeId =   auth()->user()->employee->id;
        }
        $employee       = Employee::withoutGlobalScopes()->with(['bankdetail', 'documents','user'])->find($employeeId);
        if(auth()->user()->hasRole('manager') && (!auth()->user()->hasRole('HR') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('HR Junior')))
        {
            if($employee->department_id != auth()->user()->employee->department_id)
            {
                $employee   = Employee::withoutGlobalScopes()->with(['bankdetail', 'documents'])->find(auth()->user()->employee->id);
            }
        }
        if(empty($employee)) { abort(404); }
        $employee['birth_date']     =   $employee->birth_date;
        $employee['join_date']      =   $employee->join_date;
        $employee->load('department.deptManager', 'designation', 'documents');
        $data['employee']           =   $employee;
        $data['documents']          =   $employee->documents;
        $data['assets']             =   $employee->user->assetAssignments;
        return view('employee.employeeDetail', $data);
    }

    //download employee excel
    public function export(Request $request)
    {
        $this->authorize('hrUpdateEmployee', new Employee());
        ini_set('max_execution_time', -1);
        $employees   = $this->employeeList($request, true);
        return Excel::download(new EmployeeExport($employees), 'employee.xlsx');
    }

    public function download_document(Request $request)
    {
        $file   = storage_path("app/documents/employee/$request->employee/$request->reference");
        return response()->file($file);
    }

    public function import()
    {
        $this->authorize('hrImportEmployee',new Employee());
        Excel::import(new TestImport, request()->file('file'));
        return back()->with('success', 'Employee Imported Successfully');
    }

    private static function generatePassword(): String
    {
        $length         = rand(8, 10);
        $alphabet       = '1234567890abcdefghijklmnopqrstuvwxyz12345678901234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass           = array();
        $alphaLength    = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $n      = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }

    public function sendPassword($employee)
    {
        $data['employee'] = $employee;

        $department     = Department::with('deptManager')->where('id',$employee->department_id)->first();
        $data['link']   = route('dashboard');
        $user           = $employee->user;
        $data['user']   = $user;
        Mail::send('email.password', $data, function ($message) use ($user,$employee,$department)  {
            $message->to($department->deptManager->office_email, $employee->name)->subject('Ems Account Created');
        });
    }

    public function deleteDocument(Document $document)
    {
        $document->forceDelete();
        return back()->with('success', 'Document Deleted Successfully');
    }

    public function deleteEmployeeDocument(Request $request)
    {
        $this->authorize('hrUpdateEmployee', new Employee());
        $employee =  Employee::with('documents')->where('id',$request->employee_id)->first();
        $path = storage_path("app/documents/employee/$request->employee_id/" . $employee->documents[$request->reference]);
        if (File::exists($path)) {

            File::delete($path);
        }
         $documents                       =  $employee->documents;
         $documents[$request->reference]  =  null;
         $documents->save();
         $link                   = route('editEmployee',['employee'=>$employee->id]);
         $data['link']           = $link;
         $subject                = "Document Deleted";
         $message                = " Your  " . $request->reference  . " is deleted .Please Upload it again";
         $email                  =  $employee->user->email;
        send_email("email.action", $data, $subject, $message,$email,null);
        return back()->with('success', 'Document Deleted Successfully');
    }

    public function pendingEmployeeProfile(Request $request)
    {
        $this->authorize('pendingProfile', new Employee());
        $date       =   Carbon::parse('2022-05-14');
        if (empty($request->pending_profile_pic)) {
            $pendingEmployeeProfiles    =    Employee::where('is_active', '1')->with(['user', 'documents','department', 'profileReminder' => function ($query) {
                return $query->whereDay('created_at', Carbon::now()->day)->where('sent', 1);
            }])->whereHas('user', function ($query) {
                $query->where('user_type', 'Employee');
            })->where(function ($query) {
                $query->whereDoesNtHave('documents')->orWhereDoesNtHave('employeeEmergencyContact')->orWhereDoesNtHave('user',function($user){
                    $user->where('profile_pic','<>',null);
                });
            })->get();
        } 
        else {
            $pendingEmployeeProfiles    =    Employee::where('is_active', '1')->with(['user', 'documents','department', 'profileReminder' => function ($query) {
                return $query->whereDay('created_at', Carbon::now()->day)->where('sent', 1);
            }])->whereDoesNtHave('draftProfiles',function($drafts) use($date){
                $drafts->where('field_name','profile_pic')->where('updated_at','>=',$date)->where('is_approved',null);
            })->whereHas('user',function($user){
                $user->where('user_type','Employee')->whereNull('profile_pic');
            })->get();
        }
        $data['pendingEmployeeProfiles']        =   $pendingEmployeeProfiles;
        return view('employee.incompleteEmployeeProfile', $data);
    }

    public function editProfile(Employee $employee)
    {
        $this->authorize('editProfile', $employee);
        $employee                   = Employee::with('documents', 'draftProfiles','user')->find(auth()->user()->employee->id);
        $data['employee']           = $employee;
        $data['submitRoute']        = array('updateProfile', $employee->id);
        $list['qualification']      = Qualification::pluck('name', 'id')->toArray();
        $data['list']               = $list;
        $data['documents']          = $employee->documents;
        $data['draft']              = $employee->draftProfiles;
        $data['person_relations']   = ['Father' => 'Father', 'Mother' => 'Mother', 'Brother' => 'Brother', 'Sister' => 'Sister', 'Husband' => 'Husband', 'Wife' => 'Wife', 'Any Other' => 'Any Other'];
        return view('employee.editProfile', $data);
    }

    public function updateProfile(EmployeeRequest $request)
    {
        $details            = $request->only(['id', 'name', 'phone', 'personal_email', 'birth_date', 'qualification_id', 'join_date']);
        $documents          = $request->only(['aadhaar_number', 'pan_number', 'aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy','video_file']);
        $bankDetails        = $request->only(['account_holder', 'bank_name', 'account_no', 'ifsc_code']);
        $emergencyContacts  = $request->only(['person_name', 'person_relation', 'person_contact', 'person_address']);
        $employee   = Employee::where('id', $request->id)->first();
        $employee->name = $request->name;
        $employee->office_email = $request->office_email;
        $employee->gender = $request->gender;
        $employee->save();
        $user =  $employee->user;
        $user->name = $request->name;
        $user->save();
        if ($request->hasFile('profile_pic')) {
            $profile        = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => 'profile_pic'])->first();
            $imageName      = $this->Image_prefix . Carbon::now()->timestamp . '.' . $request->file('profile_pic')->getClientOriginalExtension();
            $request->file('profile_pic')->move(storage_path('app/image/employee'), $imageName);
            if (empty($profile)) {
                $profile    = new EmployeeProfileDraft();
            }
            $profile->employee_id   = $request->id;
            $profile->field_name    = 'profile_pic';
            $profile->field_value   = $imageName;
            $profile->is_approved   = null;
            $profile->approved_by   = null;
            $profile->is_file       = 1;
            $profile->save();
        }    
        //Step 1
        $currentDetails     = Employee::select('id', 'name', 'qualification_id', 'phone', 'personal_email', 'birth_date', 'join_date')->find($request->id)->setAppends([])->toArray();
        $newDetails         = array_diff($details, $currentDetails);
        if (!empty($newDetails)) {
            foreach ($newDetails as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $employeeImage  = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => $key])->first();
                if (empty($employeeImage)) {
                    $employeeImage  = new EmployeeProfileDraft();
                }
                $employeeImage->employee_id     = $request->id;
                $employeeImage->field_name      = $key;
                $employeeImage->field_value     = $value;
                $employeeImage->is_approved     = null;
                $employeeImage->approved_by     = null;
                $employeeImage->save();
            }
        }
        //Step 2
        $currentBankDetails    = BankDetail::select('bank_name', 'account_holder', 'ifsc_code', 'account_no')->where('employee_id', $request->id)->first();

        if (!empty($currentBankDetails)) {
            $currentBankDetails     = $currentBankDetails->toArray();
            $newBankDetails         = array_diff($bankDetails, $currentBankDetails);
        } else {
            $newBankDetails        = $bankDetails;
        }
        if (!empty($newBankDetails)) {
            foreach ($newBankDetails as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $profile    = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => $key])->first();
                if (empty($profile)) {
                    $profile = new EmployeeProfileDraft();
                }
                $profile->employee_id   = $request->id;
                $profile->field_name    = $key;
                $profile->field_value   = $value;
                $profile->is_approved   = null;
                $profile->approved_by   = null;
                $profile->save();
            }
        }
        $currentEmergencyContact = EmployeeEmergencyContact::select('person_name', 'person_relation', 'person_contact', 'person_address')->where('employee_id', $request->id)->first();

        if (!empty($currentEmergencyContact)) {
            $currentEmergencyContact    = $currentEmergencyContact->toArray();
            $newEmergencyContacts       = array_diff($emergencyContacts, $currentEmergencyContact);
        } else {
            $newEmergencyContacts             = $emergencyContacts;
        }
        if (!empty($newEmergencyContacts)) {
            foreach ($newEmergencyContacts as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $profile = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => $key])->first();
                if (empty($profile)) {
                    $profile = new EmployeeProfileDraft();
                }
                $profile->employee_id = $request->id;
                $profile->field_name  = $key;
                $profile->field_value = $value;
                $profile->is_approved = null;
                $profile->approved_by = null;
                $profile->save();
            }
        }
        //Step 3
        $currentDocumentDetails = Document::select('aadhaar_number', 'aadhaar_file', 'pan_number', 'pan_file', 'cv', 'cheque','video_file')->where('employee_id', $request->id)->first();
        if (!empty($currentDocumentDetails)) {
            $currentDocumentDetails = $currentDocumentDetails->toArray();
            $newDocumentDetails     = array_diff($documents, $currentDocumentDetails);
        } else {
            $newDocumentDetails     = $documents;
        }
        if (!empty($newDocumentDetails)) {
            $is_file    = null;
            foreach ($newDocumentDetails as $key => $value) {
                if (empty($value) || $key == 'profile_pic') {
                    continue;
                }
                if ($key == 'aadhaar_file') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('aadhaar_file')->getClientOriginalExtension();
                    $request->file('aadhaar_file')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                } elseif ($key == 'cheque') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('cheque')->getClientOriginalExtension();
                    $request->file('cheque')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                } elseif ($key == 'asset_policy') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('asset_policy')->getClientOriginalExtension();
                    $request->file('asset_policy')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                } elseif ($key == 'pan_file') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('pan_file')->getClientOriginalExtension();
                    $request->file('pan_file')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                } elseif ($key == 'cv') {
                    $is_file    = 1;
                    $fileName   = $key . Carbon::now()->timestamp . '.' . $request->file('cv')->getClientOriginalExtension();
                    $request->file('cv')->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value      = $fileName;
                } elseif($key == 'video_file') {
                    $is_file = 1;
                    $fileName = $key . Carbon::now()->timestamp . '.' . $request->file('video_file')->getClientOriginalExtension();
                    $request->file($key)->move(storage_path('app/documents/employee/' . $request->id), $fileName);
                    $value = $fileName;
                }
                $profile        = EmployeeProfileDraft::where(['employee_id' => $request->id, 'field_name' => $key])->first();
                if (empty($profile)) {
                    $profile    = new EmployeeProfileDraft();
                }
                $profile->employee_id   = $request->id;
                $profile->field_name    = $key;
                $profile->field_value   = $value;
                // dd($value);
                $profile->is_approved   = null;
                $profile->approved_by   = null;
                $profile->is_file   = $is_file;
                $profile->save();
            }
            $user_ids = User::where('email', '<>', 'martha.folkes@theknowledgeacademy.com')->whereHas('roles', function ($query) {
                $query->where('name', 'HR');
            })->pluck('id', 'id')->toArray();
            $message    = "Profile updated by " . $currentDetails['name'];
            $link       = route("draftList");
            send_notification($user_ids, $message, $link);
        }
        return  back()->with('success', 'Data Saved As Draft For Approval');
    }

    public static function removeDocument()
    {
        $documents  = Document::onlyTrashed()->get();
        foreach ($documents as $document) {
            Storage::delete('document/' . $document->filename);
        }
        return 'done';
    }

    public function draft()
    {
        $this->authorize('hrEmployeeList', new Employee());

        $drafts = EmployeeProfileDraft::select('employee_profile_draft.*','employee.name as employee_name','departments.name as department_name')
                    ->leftJoin('employee','employee_profile_draft.employee_id','=','employee.id')
                    ->leftJoin('departments','employee.department_id','=','departments.id')
                    ->whereHas('employee.department')
                    ->whereNull('approved_by')
                    ->groupBy('employee_id')
                    ->orderBy('created_at', 'desc');
        if (request()->has('draft_field')) {
            $drafts->where('field_name',request()->draft_field);
        }
        if (request()->ajax()) {
            return DataTables::of($drafts)
            ->addColumn('action', function($drafts){

                $btn = '<a href="'. route("draftView", ["employee"=> $drafts->employee_id]).'" class="btn btn-primary btn-lg p-3 " >Action</a>';
                return $btn;
            })
            ->filterColumn('department_name', function($query, $keyword){
                $query->where('departments.name', 'like', "%$keyword%");
            })
            ->filterColumn('employee_name', function($query, $keyword){
                $query->where('employee.name', 'like', "%$keyword%");
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        $data['pendingProfiles']    =   $drafts->groupBy('employee_id')->get();
        $data['draftFields']        =   EmployeeProfileDraft::pluck('field_name','field_name')->toArray();
        return view('employee.draftList', $data);
    }

    public function draft_view(Employee $employee)
    {
        $employee->load(['draftProfiles', 'user']);
        $pending            =   $employee->draftProfiles->whereNull('approved_by');
        if ($pending->isEmpty()) {
            return redirect()->route('draftList')->with('No Draft Available');
        }
        $data['employee']   =   $employee;
        $data['drafts']     =   $pending;
        return view('employee.draftShow', $data);
    }

    public function draft_action(Request $request)
    {
        if ($request->ajax()) {
            $employee                       =   Employee::with('bankdetail', 'documents','user')->find($request->employee);
            $user                           =   $employee->user;
            $draft                          =   EmployeeProfileDraft::find($request->draft);
            if ($request->has('is_approved')) {
                $draft->is_approved         =   $request->is_approved =='true' ? 1 : 0;
                $draft->approved_by         =   auth()->user()->employee->id;
                $data                       =   array();
                $link = route("editProfile", ['employee' => $employee->id]);
                if ($request->is_approved == "true") {
                    if (in_array($draft->field_name, ['bank_name', 'account_no', 'ifsc_code', 'account_holder'])) {
                        $bankdetail         =   $employee->bankdetail;
                        if (empty($bankdetail)) {
                            $bankdetail     = new BankDetail();
                            $bankdetail->employee_id = $employee->id;
                        }
                        $field              =   $draft->field_name;
                        $old_value          =   $bankdetail->$field;
                        $bankdetail->$field =   $draft->field_value;
                        $draft->old_value   =   $old_value;
                        $bankdetail->save();
                    } elseif (in_array($draft->field_name, ['aadhaar_number', 'aadhaar_file', 'pan_number', 'pan_file', 'cv', 'cheque', 'asset_policy','video_file'])) {
                        $document           = $employee->documents;
                        $field              =   $draft->field_name;
                        if (empty($document)) {
                            $document               = new Document();
                            $document->employee_id  = $employee->id;
                        } elseif (!empty($document->$field) && in_array($draft->field_name, ['aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy','video_file'])) {
                            $old_file       = $document->$field;
                        }
                        $old_value          =   $document->$field;
                        $document->$field   =   $draft->field_value;
                        $draft->old_value   =   $old_value;
                        $document->save();
                    } elseif (in_array($draft->field_name, ['person_name', 'person_relation', 'person_contact', 'person_address'])) {
                        $emergencyContact   = $employee->employeeEmergencyContact;
                        $field              = $draft->field_name;
                        if (empty($emergencyContact)) {
                            $emergencyContact               = new EmployeeEmergencyContact();
                            $emergencyContact->employee_id  = $employee->id;
                        }
                        $field                      =   $draft->field_name;
                        $old_value                  =   $emergencyContact->$field;
                        $emergencyContact->$field   =   $draft->field_value;
                        $draft->old_value           =   $old_value;
                        $emergencyContact->save();
                    } elseif ($draft->field_name == 'profile_pic') {
                        $user->profile_pic  = $draft->field_value;
                        $user->save();
                    } else {
                        $field              =   $draft->field_name;
                        $old_value          =   $employee->$field;
                        $employee->$field   =   $draft->field_value;
                        $draft->old_value   =   $old_value;
                    }
                    $data['status']     = 'approved';
                    if (Str::after($draft->field_name, '_') == 'id') {
                        $draft->field_name  = ucfirst(Str::before($draft->field_name, '_'));
                    }
                    $message    =  'Your ' . $draft->field_name . ' has been approved';
                    send_notification([$request->user_id], $message, $link);
                } elseif ($request->is_approved == "false") {
                    if (Str::after($draft->field_name, '_') == 'id') {
                        $draft->field_name  = ucfirst(Str::before($draft->field_name, '_'));
                    }
                    $data['status']     = 'rejected';
                    $data['name']       = $employee->name;
                    $email              = $employee->office_email;
                    $subject            = $draft->field_name . ' has been rejected';
                    $data['field_name'] = $draft->field_name;
                    $data['message']    = $draft->field_name;
                    $data['link']       = route('login');
                    if (in_array($draft->field_name, ['aadhaar_file', 'pan_file', 'cv', 'cheque', 'asset_policy'])) {
                        $old_file       = $draft->field_value;
                    }
                    $message            = !empty($request->comment) ? $request->comment : null;
                    $data['remarks']    = $message;
                    $user = $employee->user;
                    $to   = $user->email;
                    $message            = (new Action($user, $data, $subject, 'email.profileRejected'))->onQueue('emails');
                    Mail::to($to)->later(Carbon::now(), $message);
                    send_notification([$request->user_id], 'Your ' . $draft->field_name . ' has been rejected', $link);
                }
                $draft->save();
                $employee->save();
                if (isset($old_file)) {
                    if (\Storage::exists("documents/employee/$employee->id/$old_file")) {
                        \Storage::delete("documents/employee/$employee->id/$old_file");
                    }
                }
                return $data;
            }
        }
        abort(403);
    }

    public function updateDepartment()
    {
        $this->authorize('hrUpdateEmployee',new Employee());
        $data['employees']          =   Employee::pluck('name', 'id');
        $data['departments']        =   Department::withoutGlobalScopes()->pluck('name', 'id');
        return view('employee.departmentChange', $data);
    }

    public function changeDepartment(Request $request)
    {
        $employee                   =   Employee::find($request->id);
        $department_id              =   $employee->department->id;
        return $department_id;
    }

    public function assignDepartment(Request $request)
    {
        $employee                   =   Employee::find($request->employee);
        $employee->department_id    =   $request->department;
        $employee->save();
        return  back()->with('success', 'Department Updated');
    }

    public function sendReminder(Employee $employee)
    {
        $this->send_email_reminder($employee);
    }

    public function send_email_reminder($employee)
    {
        $subject                   =     'Profile Incomplete';
        $message                   =     'Please complete your pending profile';
        $email                     =     $employee->office_email;
        $data['employee']          =     $employee->load('documents');
        $link                      =     ['name' => 'editProfile', 'parameter' => $employee->id];
        $profileReminder           =     PendingProfileReminder::create(['employee_id' => $employee->id, 'sent' => 0]);
        send_notification([$employee->user_id], $message, $link);
        send_email("email.profileUpdateReminder", $data, $subject, $message, array($email), null);
        $profileReminder->sent     =     1;
        $profileReminder->update();
    }

    public function exitList(Request $request, $export= false)
    {
        $this->authorize('hrNoDuesApprover', new Employee());
        $data['employees']                  =          Employee::withoutGlobalScopes()->has('employeeExitDetail')->pluck('name', 'name')->toArray();
        $data['departments']                =          Department::withoutGlobalScopes()->pluck('name', 'id')->toArray();
        $employees                          =           Employee::withoutGlobalScopes()->has('employeeExitDetail')->select('employee.id', 'employee.name', 'users.profile_pic', 'employee.biometric_id', 'employee.join_date',
                                                                    'employee.contract_date', 'departments.name as department_name', 'departments.id as department_id', 'employee_exit_details.exit_date', 'employee.office_email',
                                                                    'employee_exit_details.hr_no_due', 'employee_exit_details.it_no_due', 'employee_exit_details.dept_no_due', 'employee_exit_details.experience_file', 'employee.personal_email')
                                                                    ->leftJoin('departments','employee.department_id','departments.id')
                                                                    ->leftJoin('users','employee.user_id','users.id')
                                                                    ->leftJoin('employee_exit_details','employee_exit_details.employee_id','employee.id');
        if(!empty($request->name))
        {
            $employees  = $employees->where('employee.name', $request->name);
        }
        if(!empty($request->department_id))
        {
            $employees  = $employees->where('departments.id', $request->department_id);
        }
        if(!empty($request->dateFrom))
        {
            $employees  = $employees->whereBetween('exit_date', [$request->dateFrom, $request->dateTo]);
        }
        if(!empty($request->biometric_id))
        {
            $employees  = $employees->where('biometric_id', $request->biometric_id);
        }
        if($export)
        {
            return $employees->get();
        }
        if ($request->ajax())
        {
            return DataTables::of($employees)
                ->addColumn('image_source', function($employees){
                    if(!empty($employees->profile_pic) && Storage::exists( "image/employee/".$employees->profile_pic))
                    {
                        $imagePath  =    url("employee/picture/".$employees->profile_pic);
                    }
                    else
                    {
                        $imagePath =    url('/img/user.jpg');
                    }
                    $btn = '<a target="_blank" href="'.$imagePath.'" class="employee-image">
                                <img src="'.$imagePath.'" width="42" height="42"></a>';
                    
                    return $btn;
                })
                ->addColumn('detail', function($employees){
                    $btn = '<a href="'. route("employeeDetail", ["employee" => $employees->id]).'" class="p-2 text-primary fas fa-address-card"
                            style="font-size:20px;border-radius:5px;"></a>';

                    return $btn;
                })
                ->addColumn('status', function($employees){
                    if($employees->employeeExitDetail->status() == 'Experience Pending')
                    {
                        return '<button class="btn btn-lg p-3 btn-warning btn-rounded"
                                onclick="uploadExperience(`'.$employees->id.'`, `'.$employees->name.'`)"
                                data-toggle="modal" data-target="#exampleModal"> Upload Experience</button>';
                    }
                    else
                    {
                        return $employees->employeeExitDetail->status();
                    }
                })
                ->editColumn('exit_date',function($employees){
                    return getFormatedDate($employees->employeeExitDetail->exit_date);
                })
                ->rawColumns(['image_source', 'detail', 'status'])
                ->make(true);
        }
        return view('employee.exitList', $data);
    }

    public function exitForm()
    {
        $this->authorize('hrNoDuesApprover', new Employee());
        $data['departments']    =     Department::withoutGlobalScopes()->pluck('name', 'id')->toArray();
        $data['employees']      =     Employee::withoutGlobalScopes()->whereHas('user', function ($query) {
                                            $query->where('is_active', '1')->where('user_type', '<>', 'External');
                                        })->pluck('office_email', 'office_email');
        return view('employee.exitForm', $data);
    }

    public function getEmployees(Request $request, $department_id)
    {
        $employees  = Employee::where('department_id', $department_id);
        $key        = 'office_email';
        $value      = 'name';
        if($request->type == 'exit'){
            $employees  = $employees->withoutGlobalScopes()->has('employeeExitDetail');
            $key        = 'name';
        }
        $data = $employees->pluck($value, $key)->toArray();
        return $data;
    }

    public function getEmployeeDetail($email)
    {
        $data['employee']   =   Employee::with('user')->where('office_email', $email)->first();
        
        return view('employee.employeeDetailFragment', $data);
    }

    public function noDuesInitiate(Request $request)
    {
        $this->authorize('hrNoDuesApprover', new Employee());
        $employee                           =    Employee::withoutGlobalScopes()->where('office_email', $request->employee_id)->first();
        $exists                             =    EmployeeExitDetail::where('employee_id', $employee->id)->exists();
        if($exists)
        {
            return back()->with('failure', 'Employee already marked exit.');
        }
        $employeeExitDetail                 =    new EmployeeExitDetail();
        $employeeExitDetail->employee_id    =    $employee->id;
        $employeeExitDetail->reason         =    $request->reason;
        $employeeExitDetail->exit_date      =    $request->exit_date;
        $employeeExitDetail->action_by      =    auth()->user()->id;
        $employeeExitDetail->save();
        $user                               =    User::withoutGlobalScopes()->with('assetAssignments.assetSubType', 'assetAssignments.company')->find($employee->user_id);
        $user->is_active                    =    0;
        $subject                            =   $user->name."'s Assets unassigned.";
        $data['user_name']                  =   $user->name;
        $data['assetAssignments']           =   $user->assetAssignments;
        $data['message']                    =   $subject;
        $data['link']                       =   '/';
        $message                            =   $subject;
        $email                              =   User::whereHas('roles', function($query){
                                                    $query->where('name', 'assetManager');
                                                })->pluck('email');
        if($user->assetAssignments->isNotEmpty())
        {
            $message                         =  (new Action($user, $data, $message, 'email.assetDetails'))->onQueue('emails');
            Mail::to($email)->later(Carbon::now()->addSeconds(30), $message);
        }
        foreach($user->assetAssignments as $asset)
        {
            $asset->assigned_to               =  null;
            $asset->save();
        }
        $user->save();
        $employee->update(['is_active' => 0]);
        $approver_ids                         =  User::havingRole('No Dues Approver');
        $link                                 =  route("noDuesRequests");
        $message                              =  'Employee no dues requested.';
        send_notification($approver_ids, $message, $link);
        if(request()->root()=="https://ems.tka-in.com")
        {
            try {
                $this->deactivateSalesAccount($user->email,$employeeExitDetail->exit_date);
            } catch (\Throwable $th) {
                
            }
            try {
                $this->deactivateClivAccount($user->email,$employeeExitDetail->exit_date);
            } catch (\Throwable $th) {
                
            }
            try {
                $this->deactivateTrainerPortalAccount($user->email);
            } catch (\Throwable $th) {
                
            }
        }
        return redirect()->route('exitList')->with('success', 'No Dues Initiated.');
    }

    public function uploadExperience(Request $request)
    {
        $this->authorize('hrNoDuesApprover', new Employee());
        $employee       =   Employee::withoutGlobalScopes()->with('employeeExitDetail')->find($request->employee_id);
        if (request()->file('experience_file')) {
            $fileName   =   'experience_file' . Carbon::now()->timestamp . '.' . $request->file('experience_file')->getClientOriginalExtension();
            $request->file('experience_file')->move(storage_path('app/documents/employee/' . $request->employee_id), $fileName);

            $employee->employeeExitDetail->update(['experience_file' => $fileName]);
        }
        return redirect()->back()->with('success', 'Experience Uploaded');
    }

    public function noDuesRequests(Request $request)
    {
        if (!auth()->user()->can('hrNoDuesApprover', new Employee()) && !auth()->user()->can('itNoDuesApprover', new Employee()) && !auth()->user()->can('managerNoDuesApprover', new Employee()))
        {
            abort(403);
        }
        $managerIds                 =        User::havingRole('manager');
        $employees                  =        Employee::withoutGlobalScopes();
        if (in_array(auth()->user()->id, $managerIds) && !auth()->user()->hasRole('HR') && auth()->user()->employee->department->name != 'IT')
        {
            $user                   =       auth()->user();
            $managerDepartmentId    =       $user->employee->managerDepartments->pluck('id')->toArray();
            $employees->where('department_id', $managerDepartmentId);
        }
        $employees                  =       $employees->whereHas('employeeExitDetail', function ($query) {
                                                $query->whereNull('dept_no_due')->orWhereNull('it_no_due')->orWhereNull('hr_no_due');
                                            })
                                            ->with('employeeExitDetail','user')
                                            ->leftJoin("employee_exit_details","employee_exit_details.employee_id","=","employee.id")
                                            ->select('employee_exit_details.*','employee.id as employee_id','employee.name','employee.office_email','user_id','department_id')->orderBy("exit_date","desc");
            
                                           
                                           
        if(!empty($request->department_id))
        {
            $employees                  =  $employees->where('department_id',$request->department_id);
        }

        if(!empty($request->user_id))
        {
            $employees                  =  $employees->where('user_id',$request->user_id);
        }

        if(!empty($request->dateFrom))
        {
            // dd($request->dateFrom);
            $employees  = $employees->whereBetween('exit_date', [$request->dateFrom, $request->dateTo]);
        }

        $data['actions']            =       ['0' => 'Pending', '1' => 'Completed'];

        $data['employees']          =       $employees->paginate(10);
        $data['employeeDepartments']  	=   Employee::withoutGlobalScopes()->with('user','department')->select('biometric_id','user_id','department_id','name','office_email')->whereHas('user',function($user){
                                            $user->where('user_type','Employee');
                                            })->get()->groupBy('department.name');
        $data['department_id']          =   Department::pluck('name', 'id')->toArray();

        return view('employee.noDuesRequests', $data);
    }

    public function noDuesSubmit(Request $request, $employee)
    {
        $employee   = Employee::withoutGlobalScopes()->with('employeeExitDetail')->find($employee);
        // dd($employee,$request->dept_no_due);
        if (request()->has('dept_no_due'))
        {
            $employee->employeeExitDetail->update(['dept_no_due' => $request->dept_no_due]);
        }
        if (request()->has('it_no_due'))
        {
            $employee->employeeExitDetail->update(['it_no_due' => $request->it_no_due]);
        }
        if (request()->has('hr_no_due'))
        {
            $employee->employeeExitDetail->update(['hr_no_due' => $request->hr_no_due]);

            if ($employee->user->user_type == 'Office Junior')
            {
                $employee->employeeExitDetail->update(['dept_no_due' => 1]);
                $employee->employeeExitDetail->update(['it_no_due' =>  1]);
            }
        }
        return redirect()->back()->with('success', 'Submitted Successfully.');
    }

    // set birthday ReadOn
    function setBirthdayReadOn(Request $request)
    {
        Employee::find($request->id)->update(['birthday_reminder' => '1']);
    }

    function resetBirthdayReadOn()
    {
        Employee::where('birthday_reminder', '1')->update(['birthday_reminder' => null]);
    }

    public function showRecentJoinedUser(Request $request)
    {
        $this->authorize('hrEmployeeList', new Employee());
        $dateFrom           =       Carbon::now()->startOfMonth();
        $dateTo             =       Carbon::now()->endOfMonth();
        if (!empty($request->dateFrom)) {
            $dateFrom       =       $request->dateFrom;
        }
        if (!empty($request->dateTo)) {
            $dateTo         =       $request->dateTo;
        }
        $data['employees']  =       Employee::with('department')->whereDate('join_date', '>=', $dateFrom)
            ->whereDate('join_date', '<=', $dateTo)->get();
        return view('user.recentUsers', $data);
    }

    public function getEmail($employee_id)
    {
        $employee       = Employee::find($employee_id);
        return $employee->office_email;
    }
    
    public function barcodeList(Request $request)
    {
        $this->authorize('barcodeListView', new Employee());
        $employees                     =    Employee::whereHas('user', function ($user) {
                                                $user = $user->where('user_type', 'Employee');
                                            });
        $employeeNames                  =   Employee::get();

        if (request()->has('department_id')) {
            $employees                 =    $employees->where('department_id', $request->department_id);
            $employeeNames             =    $employeeNames->where('department_id', $request->department_id);
        }
        if (request()->has('name')) {
            $employees                 =    $employees->where('name', $request->name);
        }
        if(request()->has('id_card'))
        {
            $employees                 =    $employees->where('id_card','<>',null );
        }
        if(request()->has('not_id_card'))
        {
            $employees                 =    $employees->where('id_card',null );
        }
        $data['department_id']         =    Department::pluck('name', 'id')->toArray();
        $data['names']                 =    $employeeNames->pluck('name', 'name')->toArray();
        $data['employees']             =    $employees->paginate(20);
        return view('employee.barcodeList', $data);
    }

    public function storeIdCard(Request $request )
    {
        $employee       = Employee::findOrFail($request->id);
        if($request->has('id_card'))
        {
            $idCard      = $this->Image_prefix . Carbon::now()->timestamp . '.' . $request->file('id_card')->getClientOriginalExtension();
            $request->file('id_card')->move(storage_path('app/documents/employee/'.$employee->id), $idCard);
            $employee['id_card']      =   $idCard;
            $employee->save();
        }
        return redirect()->back()->with('success','Id Card Uploaded');
    }

    public function getBarCodeImage(Request $request)
    {
        ini_set('max_execution_time', -1);
        $employee        =   Employee::find($request->employee_id);
        $employee        =   Employee::find($request->employee_id);
        $generatorPNG    =   new BarcodeGeneratorPNG();
        $content         =   $generatorPNG->getBarcode("$employee->biometric_id", $generatorPNG::TYPE_CODE_128);
        $data            =   $content;
        $file            =   $employee->name. '.png';
        $destinationPath = storage_path() . "/documents/$employee->user_id/";
        if (!is_dir($destinationPath))
        {
            mkdir($destinationPath, 0777, true);
        }
        File::put($destinationPath . $file, $data);
        return Response::download($destinationPath.$file);
    }

    private function deactivateSalesAccount($email,$exitDate)
    {
     
            $sales       = new SalesSupportSync();
            $sales->deactivateAccount($email, $exitDate);
        
    }
    private function deactivateClivAccount($email,$exitDate)
    {
      
            $cliv       = new ClivSync();
            $cliv->deactivateAccount($email, $exitDate);
         
    }
    private function deactivateTrainerPortalAccount($email)
    {
      
            $trainerPortal       = new TrainerPortalSync();
            $trainerPortal->deactivateAccount($email);
        
    }

    public function exportExit(Request $request)
    {
        $this->authorize('exitExport', new Employee());
        ini_set('max_execution_time', -1);
        $employees   = $this->exitList($request, true);

        return Excel::download(new ExitEmployeeExport($employees), 'exit-employee.xlsx');
    }

    public function getImage($fileName)
    {
        $path       =  storage_path().'/app/image/employee/'.$fileName;
        $filedata   =  file_get_contents($path);
        if(request()->download){
            $headers        = array(
                'Content-Type: application/pdf',
            );
            try{
                $content    = Crypt::decrypt($filedata);

                return response()->streamDownload(function() use($content){ 
                    echo $content;
                }, $fileName ,$headers);
                
            }catch(DecryptException $e){

                try{
                    return response()->streamDownload(function() use($filedata){ 
                        echo $filedata;
                    }, $fileName,$headers);
                }catch(Exception $e){

                    return $e->getMessage();
                }

            }
            
        }else{
            return Response::make($filedata);
        }
    }
}
