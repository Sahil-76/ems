<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class LiveAttendance extends Model
{
    protected $connection   =   'sqlsrv';
    protected $table        =   'paralleltable';
    protected $appends      =   [
                                    'punch_date',
                                    'punch_time',
                                    'us_punch_date',
                                    'us_punch_time'
                                ];

}
