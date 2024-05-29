<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table    = 'announcements';
    protected $guarded  = 'id';
    public $image_path  = "announcements/";

    public function getImage()
    {
        $imageLink = url($this->image_path.$this->attachment);
        if ($this->hasImage()) {
            return $imageLink;
        } else {
             return null;
        }
    }
    public function hasImage()
    {
        if(empty($this->attachment)) return FALSE;
        if (file_exists(public_path($this->image_path.$this->attachment)))
        {
            return TRUE;
        }
        return FALSE;
    }

    public function users()
    {
        return $this->belongsToMany('App\User','user_announcements','announcement_id','user_id');
    }
}
