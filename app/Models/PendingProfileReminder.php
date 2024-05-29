<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingProfileReminder extends Model
{
    protected $guarded  = ['id'];
    protected $table    = 'pending_profile_reminder';
}
