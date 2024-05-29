<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\Notifications;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{

    public static function send_notification($user_ids, String $message, $routeWithParameter,$type): bool
    {

        $user_ids   = Arr::wrap($user_ids);
        $users      = User::find($user_ids);
        if(is_array($routeWithParameter))
            $route  = route($routeWithParameter['name'], $routeWithParameter['parameter']);
        else
            $route  = $routeWithParameter;
        foreach($users as $user){
            $notification           = new Notifications();
            $notification->user_id  = $user->id;
            $notification->message  = $message;
            $notification->link     = $route;
            $notification->type     = $type;
            $notification->save();
        }
        return true;
    }

    public function get_notification():string
    {
        $user                   =   auth()->user();
        $notifications          =   $user->notifications->load('user.employee');
        $data['count']          =   $notifications->count();
        $data['notifications']  =   $notifications;
        return json_encode($data);
    }

    public function set_read_status()
    {
        $notification           = Notifications::find(request()->notification);
        $notification->read_on  = Carbon::now();
        $notification->save();
        return 'done';
    }

    public function clear_notification(Request $request)
    {
        $user           = auth()->user();
        Notifications::where(['user_id'=> $user->id,'type'=>$request->type])->whereNull('read_on')->update(['read_on'=>Carbon::now()]);
    }

    public function deleteNotifications()
    {
        $today      = Carbon::today();
        $oldDate    = $today->subDays(30);
        Notifications::where('created_at','<=',$oldDate)->delete();
    }
}
