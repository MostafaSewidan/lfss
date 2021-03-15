<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Validation extends Model 
{

    protected $table = 'validations';
    public $timestamps = true;
    protected $fillable = ['value'];

    public function setting()
    {
        return $this->belongsTo('App\Models\Setting');
    }
}