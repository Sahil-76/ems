<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Encryptable;

class BankDetail extends Model
{
    protected $table='bank_detail';
    use SoftDeletes;
    use Encryptable;

    protected $encryptable = [ "account_holder","ifsc_code","account_no","bank_name"];


    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id'); 
    }
}
