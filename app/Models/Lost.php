<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lost extends Model 
{

    protected $table = 'losts';
    public $timestamps = true;
    protected $fillable = array('client_id', 'city_id', 'category_id', 'name', 'photo', 'type','description');
    protected $hidden = array('created_at', 'updated_at','photo','city_id','category_id','client_id');
    protected $appends = ['photo_path'];
    protected $with = ['city','category','client'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function getPhotoPathAttribute()
    {
        return $this->photo ? asset($this->photo) : null;
    }
}