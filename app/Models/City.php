<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model 
{

    protected $table = 'cities';
    public $timestamps = true;
    protected $fillable = array('governorate_id', 'name');
    protected $hidden = array('governorate_id', 'created_at', 'updated_at');
    protected $with = array('governorate');

    public function clients()
    {
        return $this->hasMany('App\Models\Client');
    }

    public function losts()
    {
        return $this->hasMany('App\Models\Lost');
    }

    public function governorate()
    {
        return $this->belongsTo('App\Models\Governorate');
    }

}