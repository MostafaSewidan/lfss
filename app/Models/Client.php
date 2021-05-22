<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'clients';
    public $timestamps = true;
    protected $fillable = array('city_id', 'name', 'phone', 'email', 'photo', 'pin_code', 'password');
    protected $hidden = array('created_at', 'updated_at', 'api_token', 'password', 'pin_code_date_expired', 'pin_code','is_active','photo','city_id');
    protected $appends = ['profile_photo'];
    protected $with = ['city'];

    public function notifications()
    {
        return $this->morphToMany('App\Models\Notification', 'notifiable')->withPivot('is_read');
    }

    public function losts()
    {
        return $this->hasMany('App\Models\Lost');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function tokens()
    {
        return $this->morphMany('App\Models\Token', 'tokenable');
    }

    public function getProfilePhotoAttribute()
    {
        return $this->photo ? asset($this->photo) : null;
    }

}