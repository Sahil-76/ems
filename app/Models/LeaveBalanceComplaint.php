<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class LeaveBalanceComplaint extends Model
{
    protected $table    =   "leave_balance_complaints";
    protected $guarded  =   ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScope('is_active');
    }
}
