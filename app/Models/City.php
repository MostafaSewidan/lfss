<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model 
{

    protected $table = 'cities';
    public $timestamps = true;
    protected $fillable = array('governorate_id', 'city_name_ar','city_name_en');
    protected $hidden = array('governorate_id', 'created_at', 'updated_at','city_name_ar','city_name_en');
    protected $with = array('governorate');
    protected $appends = array('name');

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


    public function getNameAttribute()
    {
        return $this['city_name_'.app()->getLocale()];
    }

}