<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingsCategory extends Model 
{

    protected $table = 'settings_categories';
    public $timestamps = true;
    protected $fillable = array('name', 'level');

    public function settings()
    {
        return $this->hasMany('App\Models\Setting');
    }

}