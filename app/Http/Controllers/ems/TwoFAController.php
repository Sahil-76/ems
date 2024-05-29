<?php

namespace App\Http\Controllers\ems;

use App\Models\UserCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class TwoFAController extends Controller
{
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        if (!Session::has('user_2fa_sent')) {
            auth()->user()->generateCode();
        }
        
        return view('2fa');
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'=>'required',
        ]);
  
        $find = UserCode::where('user_id', auth()->user()->id)
                        ->where('code', $request->code)
                        ->where('updated_at', '>=', now()->subMinutes(3))
                        ->first();
        
        if (!is_null($find)) {
            Session::put('user_2fa', auth()->user()->id);
            return redirect()->route('dashboard');
        }
  
        return back()->with('error', 'You entered wrong code.');
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function resend()
    {
        auth()->user()->generateCode();
        return back()->with('success', 'We sent you code on your email.');
    }
}
