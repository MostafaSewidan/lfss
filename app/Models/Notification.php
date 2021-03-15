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

    public function agents()
    {
        return $this->morphedByMany(Agent::class,'notifiable')->withPivot('is_read');
    }

    public function visitors()
    {
        return $this->morphedByMany(Visitor::class,'notifiable')->withPivot('is_read');
    }

    public function workers()
    {
        return $this->morphedByMany(Worker::class,'notifiable')->withPivot('is_read');
    }

    public function attachment()
    {
        return $this->morphOne(Attachment::class , 'attachmentable');
    }

    public function getPhotoAttribute()
    {
        return $this->image ? asset($this->image->path) : null;
    }


    /////////////////////////////////////
    /// get Attribute
    public function getTypeAttribute()
    {
        return $this->SwitchNotification($this->notifiable_type , 'type');
    }

    public function getIsGeneralAttribute()
    {
        return $this->SwitchNotification($this->notifiable_type , 'general');

    }

    public function getResourcesAttribute()
    {
        return $this->SwitchNotification($this->notifiable_type , 'resources');
    }

    /**
     * @param $notifiable_type
     * @param string $response
     * @return string
     */
    public function SwitchNotification($notifiable_type, $response = 'type'): string
    {
        switch ($notifiable_type) {
            case 'App\Models\Request':
                $type = [
                    'type' => 'request',
                    'general' => 0,
                    'resources' => 'App\Http\Resources\RequestLite',
                ];
                break;
            case 'App\Models\Post':
                $type = [
                    'type' => 'post',
                    'general' => 0,
                    'resources' => 'App\Http\Resources\PostLite',
                ];
                break;
            default :
                $type = [
                    'type' => 'general',
                    'general' => 1,
                    'resources' => 'App\Http\Resources\Notification',
                ];
        }
        return $type[$response];
    }
}