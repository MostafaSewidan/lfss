<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model 
{

    protected $table = 'settings';
    public $timestamps = true;
    protected $fillable = array('settings_category_id', 'key', 'value', 'display_name','data_type','level');

    public function category()
    {
        return $this->belongsTo('App\Models\SettingsCategory' ,'settings_category_id');
    }

    public function validation()
    {
        return $this->hasOne('App\Models\Validation');
    }


    public function photo()
    {
        return $this->morphOne(Attachment::class , 'attachmentable');
    }
}