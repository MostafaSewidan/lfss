<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lost extends Model
{

    protected $table = 'losts';
    public $timestamps = true;
    protected $fillable = array('client_id', 'city_id', 'category_id', 'name', 'type', 'description');
    protected $hidden = array('created_at', 'updated_at', 'photo', 'city_id', 'category_id', 'client_id');
    protected $with = ['city', 'category', 'client'];

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

    public function notifications()
    {
        return $this->morphMany(Notification::class,'notifiable');
    }

    public function attachmentRelation()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function getPhotosAttribute()
    {
        if ($this->attachmentRelation()->count()) {
            $return = [];

            foreach ($this->attachmentRelation()->get() as $attachment) {
                array_push($return, asset($attachment->path));

            }

        } else {
            $return = [];
        }


        return $return;
    }
}