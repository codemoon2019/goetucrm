<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TicketDetailResource extends JsonResource
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
            'message' => $this->message,
            'is_internal' => $this->is_internal,
            'image' => $this->createdBy->image,
            'created_at' => $this->created_at->diffForHumans(),
            'attachments' => $this->attachments,
            'created_by' => $this->createdBy
        ];
    }
}
