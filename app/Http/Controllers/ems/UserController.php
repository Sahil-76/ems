<?php

namespace App\Http\Controllers\ems;

use Auth;
use Mail;
use App\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Session;
use \Rap2hpoutre\LaravelLogViewer\LogViewerController;
use App\Http\Controllers\Auth\ForgotPasswordController;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view', new User());

        $users                  = User::withoutGlobalScope('is_active')->with('roles');

        if($request->filled('status')){
            $users->where('is_active', $request->status);
        }

        if($request->has('dateFrom') && $request->has('dateTo')){
            $users  =   $users->whereDate('last_login_at', '>=',$request->dateFrom)->whereDate('last_login_at', '<=', $request->dateTo);
        }

        $data['users']          = $users->get();
        return view('user.user', $data);
    }

    public function create()
    {
        $data['user']           =   new User();
        $data['submitRoute']    =   'user.store';
        $data['method']         =   'POST';
        $data['userTypes']      =   config('employee.userTypes');

        return view('user.createForm',$data);
    }

    public function store(UserRequest $request)
    {
        $inputs                     = $request->all();
        $user                       = new User();
        $user->name                 = $inputs['name'];
        $user->email                = $inputs['email'];
        $user->user_type            = $inputs['user_type'];
        $user->is_active            = empty($inputs['is_active']) ? 0 : 1;
        $user->save();

        if(isset($inputs['is_active']))
        {
            $reset                  = new ForgotPasswordController;
            $reset->sendResetLinkEmail($request);
        }
        
        return back()->with('success', 'User Created');
    }

    public function edit($id)
    {
        $this->authorize('update', new User());
        $data['user']           =   User::withoutGlobalScope('is_active')->with('employee')->find($id);
        $data['roles']          =   Role::all();
        $data['userTypes']      =   config('employee.userTypes');

        return view('user.userForm', $data);
    }

    public function update(UserRequest $request, $id)
    {
        $this->authorize('update', new User());
        $inputs                     = $request->all();
        $user                       = User::withoutGlobalScope('is_active')->with('employee')->find($id);
        $user->name                 = $inputs['name'];
        $user->email                = $inputs['email'];
        $user->user_type            = $inputs['user_type'];
        $user->is_active            = empty($inputs['is_active']) ? 0 : 1;
        $user->is_external          = empty($inputs['is_external']) ? 0 : 1;
        if (!empty($inputs['is_active'] && !empty($user->employee))) {
            $employee               = Employee::withoutGlobalScope('is_active')->where('user_id', $id)->first();
            $employee->is_active    = 1;

            $employee->save();
        }
        $user->save();
        if (isset($inputs['resetPwd'])) {
            $reset                  = new ForgotPasswordController;
            $reset->sendResetLinkEmail($request);
        }
        return redirect()->back()->with('success', 'User Updated');
    }

    public function assignRoles(Request $request)
    {
        $userid         = $request->input('user');
        $user           = User::find($userid);
        $roles          = $request->input('role'); // array of role ids
        if (empty($roles)) {
            $roles      = array();
        }
        $user->roles()->sync($roles);
        return redirect()->back()->with('success', 'Role Assigned Successful');
    }

    public function updatePasswordView()
    {
        $data['user']           = new User();
        $data['submitRoute']    = 'updatePassword';

        return view('user.updatePassword', $data);
    }

    public function updatePassword(PasswordRequest $request)
    {
        $user           = auth()->user();
        if (!password_verify($request->current_password, $user->password)) {
            $data['current_password'] = "Current Password doesn't Matched!";
            return back()->withErrors($data);
        }
        $hash           = Hash::make($request->password);
        $user->password = $hash;
        $user->save();
        auth::logout();
        return redirect('/login');
    }

    private static function generatePassword(): String
    {
        $length         = rand(8, 10);
        $alphabet       = '@#-_$%^&+=!1234567890abcdefghijklmnopqrstuvwxyz1234567890@#-_$%^&+=!1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ@#-_$%^&+=!1234567890@#-_$%^&+=!';
        $pass           = array();
        $alphaLength    = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++)
        {
            $n          = rand(0, $alphaLength);
            $pass[]     = $alphabet[$n];
        }

        return implode($pass);
    }

    public function sendPassword($user)
    {
        $data['user']   = $user;

        Mail::send('email.password', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Password Reset');
        });
    }

    public function adminList()
    {
        $admin_ids      = User::havingRole(['admin']);
        $data['admins'] = User::select('id', 'name', 'email', 'is_active')->find($admin_ids);

        return view('user.adminsList', $data);
    }

    public function switchUser()
    {
        if (!(auth()->user()->hasRole('admin') || auth()->user()->hasRole('HR')))
        {
            abort(403);
        }
        else
        {
            ini_set('max_execution_time', -1);

            $data['employeeDepartments']      =   User::with('employee.department')->where('is_active', 1)
            // ->whereHas('employee', function ($employee) {
            //     $employee->select('biometric_id', 'name');
            // })
            ->get()->groupBy('employee.department.name');

            return view('user.switchUserList', $data);
        }
    }

    public function loginWithAnotherUser(Request $request)
    {
        if (!(auth()->user()->hasRole('admin') || auth()->user()->hasRole('HR'))) {

            abort(403);
        } else {
            $input  = $request->input();
            $user   = User::find($input['id']);
            if (!empty($user)) {
                if (!Session::has('orig_user')) {
                    $request->session()->put('orig_user', Auth::id());
                }
                Auth::loginUsingId($input['id']);
                return redirect(route('dashboard'))->with('success', 'User Account switched Successful');
            }
        }
    }

    public function switchUserLogout(Request $request)
    {
        $user      = $request->session()->get('orig_user');
        $orig_user = User::find($user);
        if (!empty($orig_user)) {
            Auth::loginUsingId($user);
            $request->session()->forget('orig_user');
        }
        return redirect()->route('dashboard')->with('success', 'User Back to original account.');
    }


    public function laravelLogs()
    {
        if (in_array(strtolower(auth()->user()->email), User::$developers)) {
            $log    =   new LogViewerController();
            return $log->index();
        }
        return abort(403);
    }
}
