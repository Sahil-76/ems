<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        
        $this->redirectTo = route('dashboard');
     
    }
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password','is_active');
        $credentials['is_active'] = 1;
        return $credentials;
   
    }
   

/**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        // Load user from database
        $user = \App\User::where($this->username(), $request->{$this->username()})->first();
        // Check if user was successfully loaded, that the password matches
        // and active is not 1. If so, override the default error message.
        if ($user && \Hash::check($request->password, $user->password) && $user->is_active != 1) {
       
            $errors = [$this->username() => 'Account Deactivated'];
         }
        if ($request->expectsJson())
        {
        return response()->json($errors, 422);
        }

        return redirect()->back()->withInput($request->only($this->username(), 'remember'))->withErrors($errors);
    }

    protected function authenticated(Request $request, $user)
    {
        $user->last_login_at = Carbon::now();
        $user->save();
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);
        return redirect()->route('login');
    }
}
