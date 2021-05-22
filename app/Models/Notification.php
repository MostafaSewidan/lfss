<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    public $timestamps = true;
    protected $fillable = array('notifiable_type', 'notifiable_id','title','body');

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function clients()
    {
        return $this->morphedByMany(Client::class,'notifiable')->withPivot('is_read');
    }
}