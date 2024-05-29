<?php

namespace App\Http\Controllers\ems;

use App\Models\Badge;
use Illuminate\Http\Request;
use App\Http\Requests\BadgeRequest;
use App\Http\Controllers\Controller;

class BadgeController extends Controller
{

    public function index()
    {
        $this->authorize('view', new Badge());
        $data['badges']         = Badge::all();
        return view('badge.index',$data);
    }

    public function create()
    {
        $badge                  =   new Badge();
        $this->authorize('create', $badge);
        $data['badge']          =   $badge;
        $data['submitRoute']    =   'badge.store';
        $data['method']         =   'POST';
        return view('badge.form',$data);
    }

    public function store(BadgeRequest $request)
    {
        $badge          =       new Badge();
        $this->authorize('create', $badge);
        $badge->name    =       $request->name;
        if($request->hasFile('image'))
        {
            $files              =       $request->name.'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('badgeImages/'), $files);
            $badge['image']     =       $files;
        }
        $badge->save();
        return redirect(route('badge.index'))->with('success','Added Successfully');
    }


    public function edit($id)
    {
        $this->authorize('update', new Badge());
        $data['badge']          =   Badge::findOrFail($id);
        $data['submitRoute']    =   ['badge.update',$id];
        $data['method']         =   'PUT';
        return view('badge.form',$data);
    }

    public function update(BadgeRequest $request, $id)
    {
        $this->authorize('update', new Badge());
        $badge                  =  Badge::findOrFail($id);
        $badge->name            =  $request->name;
        if($request->hasFile('image'))
        {
            if(!empty($badge->image))
            {
                unlink(public_path('badgeImages/'.$badge->image));
            }
            $files               =  $request->name.'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('badgeImages/'), $files);
            $badge['image']      =  $files;
        }
        $badge->update();
        return redirect(route('badge.index'))->with('success','Updated Successfully');
    }

    public function destroy($id)
    {
        $this->authorize('delete', new Badge());
        $badge      =   Badge::findOrFail($id);
        if(!empty($badge->image))
        {
            $path   =   $badge->image;
            unlink(public_path('badgeImages/'.$path));
        }
        $badge->delete();
    }

    public function download_image(Request $request)
    {
        $file       = public_path("badgeImages/$request->reference");
        return response()->file($file);
    }
}
