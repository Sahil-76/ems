<?php

namespace App\Http\Controllers\ems;

use App\User;
use App\Models\Announcement;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\AnnouncementRequest;

class AnnouncementController extends Controller
{

    public function index()
    {
        $this->authorize('view', new Announcement());

        $data['announcements']      =   Announcement::all();

        return view('announcement.index',$data);
    }

    public function getAnnouncement()
    {
        $announcement = Announcement::all();

        return DataTables::of($announcement)
                ->addIndexColumn()
                ->addColumn('id',function($announcement){
                        $btn = '<a href="'.route("announcement.edit", ['announcement' => $announcement->id]).'"><i class="fa fa-edit"></i></a>  &nbsp;';
                        $btn = $btn.'<a onclick="deleteItem(`'.route('announcement.destroy',$announcement->id).'`)"><i class="fa fa-trash text-danger" style="cursor:pointer;"></i></a>';
                    return $btn;
                })
                ->rawColumns(['id'])
                ->make(true);
    }

    public function create()
    {
        $announcement                   =  new Announcement();

        $this->authorize('create', $announcement);

        $data['announcement']           =  $announcement;
        $data['submitRoute']            =   ['announcement.store'];
        $data['method']                 =   'POST';
        $data['users']                  =   User::where('is_active','1')->where('user_type','Employee')->pluck('name','id')->toArray();

        return view('announcement.form',$data);
    }

    public function store(AnnouncementRequest $request)
    {
        $announcement                   =   new Announcement();

        $this->authorize('create',  $announcement);

        $announcement->title            =   $request->title;
        $announcement->start_dt         =   $request->start_dt;
        $announcement->end_dt           =   $request->end_dt;
        $announcement->is_publish       =   (isset($request->is_publish) ? 1 : 0);
        $announcement->start_time       =   $request->start_time;
        $announcement->end_time         =   $request->end_time;
        $announcement->description      =   $request->description;

        if($request->hasFile('attachment'))
        {
            $files                      =       $request->title.'.'.$request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->move(public_path('announcements/'), $files);
            $announcement['attachment'] =       $files;
        }

        $announcement->save();

        $announcement->users()->sync($request['user_id']);

        return redirect()->route('announcement.index')->with('success', 'Announcement Created');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $this->authorize('update', new Announcement());

        $data['announcement']       =   Announcement::findOrFail($id);
        $data['submitRoute']        =   ['announcement.update',$id];
        $data['method']             =   'PUT';
        $data['users']              =   User::pluck('name','id')->toArray();

        return view('announcement.form',$data);
    }

    public function update(AnnouncementRequest $request, $id)
    {
        $this->authorize('update', new Announcement());

        $announcement                   =   Announcement::findOrFail($id);
        $announcement->title            =   $request->title;
        $announcement->start_dt         =   $request->start_dt;
        $announcement->end_dt           =   $request->end_dt;
        $announcement->is_publish       =   (isset($request->is_publish) ? 1 : 0);
        $announcement->start_time       =   $request->start_time;
        $announcement->end_time         =   $request->end_time;
        $announcement->description      =   $request->description;

        if($request->hasFile('attachment'))
        {
            if(!empty($announcement->attachment))
            {
                unlink(public_path('announcements/'.$announcement->attachment));
            }
            $files                         =  $request->title.'.'.$request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->move(public_path('announcements/'), $files);
            $announcement['attachment']    =  $files;
        }

        $announcement->update();

        $announcement->users()->sync($request['user_id']);

        return redirect(route('announcement.index'))->with('success','Announcement Successfully Updated');
    }

    public function destroy($id)
    {
        $this->authorize('delete', new Announcement());

        $announcement      =   Announcement::with('users')->findOrFail($id);

        if(!empty($announcement->users))
        {
            $announcement->users()->detach();
        }
        if(!empty($announcement->attachment))
        {
            $path       =   $announcement->attachment;
            unlink(public_path('announcements/'.$path));
        }

        $announcement->delete();
    }

// can be use in future to download announcement attachments

    // public function downloadAnnouncement(Request $request)
    // {
    //     $file       = public_path("announcements/$request->reference");

    //     return response()->file($file);
    // }
}
