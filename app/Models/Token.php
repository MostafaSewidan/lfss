<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model 
{

    protected $table = 'tokens';
    public $timestamps = true;
    protected $fillable = array('os', 'serial_number','token');

    public function tokenable()
    {
        return $this->morphTo();
    }



    public function scopeCheckType($query,$relation)
    {
        $type = '';

        switch ($relation)
        {
            case 'agents':
                $type = 'App\Models\Agent';
                break;
            case 'visitors':
                $type = 'App\Models\Visitor';
                break;
            case 'workers':
                $type = 'App\Models\Worker';
                break;
        }

        $query->where('tokenable_type' , $type);
    }
}