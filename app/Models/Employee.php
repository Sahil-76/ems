<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $table                =   'employee';
    protected $appends              =   ['pending_fields'];
    protected $guarded              =   ['id'];
    public $image_path              =   "image/employee/";
    public $image_id_card_path      =   "storage/app/documents/employee/";
    public $additional_attributes   =   ['pending_fields'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('is_active', function (Builder $builder) {
            $builder->where('employee.is_active', '=', 1);
        });
        // static::addGlobalScope('guest', function (Builder $builder) {
        //     $builder->where('employee.is_active', '=', 1)->whereHas('user.roles',function($query){
        //         $query->where('name','<>','Guest');
        //     });
        // });
    }

    public function toArray()
    {
        $output                     = parent::toArray();
        $output['personal_email']   = $this->personal_email;
        $output['birth_date']       = $this->birth_date;
        $output['phone']            = $this->phone;

        return $output;
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id')->withoutGlobalScopes();
    }


    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }

    public function managerDepartments()
    {
        return $this->hasMany('App\Models\Department', 'manager_id', 'id');
    }

    public function teamLeaderDepartments()
    {
        return $this->hasMany('App\Models\Department', 'team_leader_id', 'id');
    }

    public function profileReminder()
    {
        return $this->hasMany('App\Models\PendingProfileReminder');
    }

    public function qualification()
    {
        return $this->belongsTo('App\Models\Qualification', 'qualification_id');
    }

    // public function getImagePath()
    // {// check file exist then return default image.

    //     if ($this->hasImage()) {
    //         return url("employee/picture/".$this->profile_pic);
    //     } else {

    //         return url('/img/user.jpg');
    //     }
    // }

    // public function hasImage()
    // {
    //     if(empty($this->profile_pic)) return FALSE;
    //     if (Storage::exists($this->image_path.$this->profile_pic))
    //     {
    //         return TRUE;
    //     }
    //     return FALSE;
    // }

    public function documents()
    {
        return $this->hasOne('App\Models\Document', 'employee_id');
    }

    public function bankdetail()
    {
        return $this->hasOne('App\Models\BankDetail', 'employee_id');
    }

    public function activity()
    {
        return  $this->morphOne('App\Models\ActivityLog', 'module');
    }

    public function designation()
    {
        return $this->belongsTo('App\Models\Designation')->withDefault();
    }

    public function delete()
    {
        if ($this->user) {
            $this->user->delete();
        }
        return parent::delete();
    }

    public function restore()
    {
        $this->user()->restore();
        return parent::restore();
    }

    // public function getImageSourceAttribute()
    // {
    //     return $this->getImagePath();
    // }

    public function draftProfiles()
    {
        return $this->hasMany('App\Models\EmployeeProfileDraft');
    }

    public function getPendingFieldsAttribute()
    {
        if ($this->relationLoaded('documents')) {
            if (!empty($this->documents)) {
                $attributes = $this->documents->getAttributes();
                $pending    = [];

                foreach ($attributes as $key => $value) {
                    if (in_array($key, ['pan_number', 'pan_file', 'deleted_at'])) {
                        continue;
                    }

                    if ($value == null) {
                        $pending[] = ucwords(str_replace('_', ' ', $key));
                    }
                }

                if (empty($this->user->profile_pic)) {
                    $pending[] = 'Profile Picture';
                }

                if (empty($this->employeeEmergencyContact)) {
                    $pending[] = 'Emergancy Contact Details';
                }

                $pending    = implode(',', $pending);

                return $pending;
            }

            return "All details Pending";
        } else {
            return null;
        }
    }

    public function employeeExitDetail()
    {
        return $this->hasOne('App\Models\EmployeeExitDetail', 'employee_id');
    }

    public function employeeEmergencyContact()
    {
        return $this->hasOne('App\Models\EmployeeEmergencyContact', 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany('App\Models\Leave', 'employee_id');
    }

    public function getShiftAttribute($value)
    {
        $start_time = Carbon::createFromFormat('H:i:s', $this->user->shiftType->start_time)->format('g:i A');
        $end_time   = Carbon::createFromFormat('H:i:s', $this->user->shiftType->end_time)->format('g:i A');

        return  strtoupper($this->user->shiftType->name . "(" . $start_time . "-" . $end_time . ")");
    }

    public function getIdCardPath()
    {
        $imageLink  = url($this->image_id_card_path . $this->id . "/" . $this->id_card_photo);

        if ($this->hasIdCard()) {
            return $imageLink;
        } else {
            return url('/img/user.jpg');
        }
    }

    public function hasIdCard()
    {
        if (empty($this->id_card_photo)) return FALSE;
        if (storage_path($this->image_id_card_path . $this->id . "/" . $this->id_card_photo)) {
            return TRUE;
        }
        return FALSE;
    }
}
