<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProfileDraft extends Model
{
    protected $table    = "employee_profile_draft";
    protected $guarded  = "id";

    public function employee()
    {
        return $this->belongsTo("App\Models\Employee");
    }

    public function approver()
    {
        return $this->belongsTo('App\Models\Employee','approved_by');
    }
}
