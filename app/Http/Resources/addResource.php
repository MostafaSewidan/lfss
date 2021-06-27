<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class addResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'first_photo' => count($this->photos) ? $this->photos[0] : null,
            'photos' =>  $this->photos,
            'type' => $this->type,
            'description' => $this->description,
            'city' => $this->city,
            'category' => $this->category,
        ];
    }
}
