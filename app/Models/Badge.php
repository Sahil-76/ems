<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected  $table = 'badges';
    protected $guarded = 'id';
    public $image_path = "badgeImages/";

    public function getImage()
    {// check file exist then return default image.
        $imageLink = url($this->image_path.$this->image);

        if ($this->hasImage()) {
            return $imageLink;
        } else {
             return null;
        }
    }

    public function hasImage()
    {
        if(empty($this->image)) return FALSE;
        
        if (file_exists(public_path($this->image_path.$this->image)))
        {
            return TRUE;
        }
        return FALSE;
    }
}
