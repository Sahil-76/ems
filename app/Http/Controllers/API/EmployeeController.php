<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!in_array($request->ip(), ['127.0.0.1', '94.200.108.210', '172.22.10.195', '46.236.35.245'] )) {
            abort(403);
        }

        $departments                = Department::withoutGlobalScopes()->with([
            'employees' => function ($employee) {
                $employee
                ->with(['user' => function($user){
                    $user->where('is_active', '1')->whereIn('user_type', ['Employee', 'Office Junior']);
                }]);
            }
        ])
        ->withCount(['employees' => function ($employee) {
            $employee
            ->whereHas('user', function ($user) {
                $user->where('is_active', '1')->whereIn('user_type', ['Employee', 'Office Junior']);
            });
        }])->get();
       
        return $departments;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
