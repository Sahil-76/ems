<?php

namespace App\Http\Controllers\ems;

use App\User;
use App\Models\Team;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Requests\TeamRequest;
use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    public function index()
    {
        $this->authorize('view',new Team());
        $teams                  =   Team::with('department');
        if(!auth()->user()->hasRole('HR') && !auth()->user()->hasRole('Admin'))
        {
            $teams              =   $teams->where('department_id', auth()->user()->employee->department_id);
        }
        $data['teams']          =   $teams->get();
        return view('team.index',$data);
    }

    public function create()
    {
        $this->authorize('create',new Team());
        $data['team']           =   new Team();
        $data['submitRoute']    =   'team.store';
        $data['method']         =   'POST';
        $data['departments']    =   Department::pluck('name','id')->toArray();
        $users                  =   User::where('is_active', 1)->where('user_type', 'Employee');
        if(!auth()->user()->hasRole('HR') && !auth()->user()->hasRole('Admin'))
        {
            $users              =   $users->whereHas('employee',function($query)
                                            {
                                                $query->where('department_id',auth()->user()->employee->department_id);
                                            });
        }
        $users                  =   $users->select('id','name','email')->get();
        $array                  =   [];
        foreach($users as $user)
        {
            $array[$user->id]   =   $user->name."(".$user->email.")";
        }
        $data['users']          =   $array;
        return view('team.form',$data);
    }

    public function store(TeamRequest $request)
    {
        $team                   =   new Team();
        $this->authorize('create',$team);
        $team->name             =   $request->name;
        $team->reporting_person =   $request->reporting_person;
        $team->department_id    =   !empty($request->department_id) ? $request->department_id : auth()->user()->employee->department_id;
        $team->save();
        if(!empty($request->users))
        {
            User::whereIn('id', $request->users)->update(['team_id' => $team->id]);
        }
        return redirect()->route('team.index')->with('success','Team Created');
    }

    public function edit($id)
    {
        $this->authorize('update',new Team());
        $team                   =   Team::with('users')->findOrFail($id);
        $data['team']           =   $team;
        $data['submitRoute']    =   ['team.update',['team'=>$id]];
        $data['method']         =   'PUT';
        $data['departments']    =   Department::pluck('name','id')->toArray();
        $data['selectedUsers']  =   $team->users->pluck('id')->toArray();
        $users                  =   User::where('is_active', 1)->where('user_type', 'Employee');
        if(!auth()->user()->hasRole('HR') && !auth()->user()->hasRole('Admin'))
        {
            $users              =   $users->whereHas('employee',function($query)
                                            {
                                                $query->where('department_id',auth()->user()->employee->department_id);
                                            });
        }
        $users                  =   $users->select('id','name','email')->get();
        $array                  =   [];
        foreach($users as $user)
        {
            $array[$user->id]   =   $user->name."(".$user->email.")";
        }
        $data['users']          =   $array;
        return view('team.form',$data);
    }

    public function update(TeamRequest $request, $id)
    {
        $this->authorize('update',new Team());
        $team                   =   Team::findOrFail($id);
        $team->name             =   $request->name;
        $team->reporting_person =   $request->reporting_person;
        $team->department_id    =   !empty($request->department_id) ? $request->department_id : auth()->user()->employee->department_id;
        $team->save();
        if(!empty($request->users))
        {
            User::whereIn('id', $request->users)->update(['team_id' => $team->id]);
        }
        elseif(!empty($team->users))
        {
            $user_ids           =   $team->users->pluck('id')->toArray();

            User::whereIn('id', $user_ids)->update(['team_id' => null]);
        }
        return redirect()->route('team.index')->with('success','Team Updated');
    }

    public function destroy($id)
    {
        $this->authorize('delete',new Team());
        $team                   =   Team::findOrFail($id);
        if(!empty($team->users))
        {
            $user_ids           =   $team->users->pluck('id')->toArray();
            User::whereIn('id', $user_ids)->update(['team_id' => null]);
        }
        $team->delete();
    }

    public function dashboard()
    {
        $this->authorize('dashboard', new Team());
        $data['data']           =   Team::with('department')->withCount('users')->get()->groupBy('department.name');
        return view('team.dashboard',$data);
    }

    public function teamUsers(Request $request)
    {
        $data['team']           =   Team::with('users')->findOrFail($request->id);
        $response['view']       =   view('team.users', $data)->render();
        return $response;
    }
}
