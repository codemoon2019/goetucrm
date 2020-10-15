<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductOrderCommentAttachmentResource;
use App\Models\ProductOrderComment;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOrderCommentCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $attachmentAccessIds = is_null($this->attachment_access_ids) ?
            [] : json_decode($this->attachment_access_ids);

        $canViewAttachments = auth()->user()->username == $this->create_by;
        $canViewAttachments = $canViewAttachments || in_array(
            auth()->user()->id, $attachmentAccessIds);

        $viewers = $this->viewers->filter(function($viewer) {
            return $viewer->id != auth()->user()->id;
        })->values();
        
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'created_at' => $this->created_at->diffForHumans(),
            'user' => $this->user->first_name . ' ' . $this->user->last_name,
            'user_image' => $this->user->image,
            'viewers' => $viewers,
            'attachments' => $canViewAttachments ? ProductOrderCommentAttachmentResource::collection(
                $this->productOrderCommentAttachments) : [],
            'replies_count' => isset($this->children_count) ? $this->children_count : 0,
            'owner' => $this->user_id == auth()->user()->id
        ];
    }
}
