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
            case 'clients':
                $type = 'App\Models\Client';
                break;
        }

        $query->where('tokenable_type' , $type);
    }
}