<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\ReactionResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "content" => $this->content,

            "user" => new UserResource($this->whenLoaded('user')),
            "attachments" => AttachmentResource::collection($this->whenLoaded('attachments')),
            "replies" => MessageResource::collection($this->whenLoaded('replies')),
            "reactions" => ReactionResource::collection($this->whenLoaded('reactions')),
            "created_at" => $this->created_at->toDateTimeString(),

        ];
    }
}
