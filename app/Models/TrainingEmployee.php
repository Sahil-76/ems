<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEmployee extends Model
{
    use HasFactory;

    protected $table    = 'training_employees';
    protected $guarded  = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function trainer()
    {
        return $this->belongsTo('App\User', 'trainer_id');
    }
}
