<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Document extends Model
{
    use SoftDeletes;
    protected $table        = 'documents';
    public $document_path   = "app\documents";
    protected $guarded      = ['id'];
  
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id'); 
    }

    public function activity()
    {  
        return  $this->morphOne('App\Models\ActivityLog','module');
    }
}
