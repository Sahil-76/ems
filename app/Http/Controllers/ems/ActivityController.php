<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\User;


class ActivityController extends Controller
{
 
  public function view(Request $request)
  {
    $this->authorize('view',new ActivityLog());

    $modules                        =   ActivityLog::select('module_type')->pluck('module_type', 'module_type')->toArray();
    $users                       =   [0 => "ALL"] + User::select('id','name')->whereHas('activities')->get()->pluck('name', 'id')->toArray();
    $new['All']                  =   'ALL';
    $modules                     =   array_merge($new, $modules);
    $data['modules']             =   json_encode($modules, JSON_HEX_APOS);
    $data['users']               =   json_encode($users, JSON_HEX_APOS);

    return view('logs.activitylogs', $data);
  }

  public function list(Request $request)
  {
    $this->authorize('view',new ActivityLog());

    $pageIndex    =   $request->pageIndex;
    $pageSize     =   $request->pageSize;
    $logs         =   ActivityLog::with('user')->orderBy('created_at', 'desc');

    if ($request->user_id != "0") {
      $logs       =   $logs->where('user_id', $request->user_id);
    
    }

    if ($request->module_type != 'All') 
    {
        $logs     =   $logs->where('module_type', $request->module_type);

    }

    $data['itemsCount'] = $logs->count();
    $data['data']       = $logs->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
    
    return json_encode($data);
  }
}
