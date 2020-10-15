<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $table = 'trainings';

    public function modules()
    {
        return $this->hasMany("App\\Models\\TrainingModule", "training_id", "id");
    }

    public function product()
    {
       return $this->hasOne('App\Models\Product','id','product_id');
    }

}
