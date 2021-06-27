<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model 
{

    protected $table = 'governorates';
    public $timestamps = true;
    protected $fillable = array('governorate_name_ar','governorate_name_ar');
    protected $hidden = array('created_at', 'updated_at','governorate_name_ar','governorate_name_ar');
    protected $appends = array('name');

    public function cities()
    {
        return $this->hasMany('App\Models\City');
    }

    public function getNameAttribute()
    {
        return $this['governorate_name_'.app()->getLocale()];

    }

}