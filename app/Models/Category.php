<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model 
{

    protected $table = 'categories';
    public $timestamps = true;
    protected $fillable = array('name');
    protected $hidden = array('created_at', 'updated_at');

    public function losts()
    {
        return $this->hasMany('App\Models\Lost');
    }

}