<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeExitDetail extends Model
{
    protected $table    = 'employee_exit_details';
    protected $guarded  = ['id'];

    public function status()
    {
        if ($this->hr_no_due && $this->it_no_due && $this->dept_no_due) 
        {
            if($this->experience_file)
            {
                $status = 'Exit';
            }else{
                $status = 'Experience Pending';
            }
            
        }else{
            $status = 'No Dues Pending';
        }
        return $status;
    }
}
