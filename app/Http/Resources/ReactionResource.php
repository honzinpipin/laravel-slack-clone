<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReactionResource extends JsonResource
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
            "emoji" => $this->emoji,
            "user" => new UserResource($this->whenLoaded("user")),
            "messsage_id" => $this->message_id,
            "created_at" => $this->created_at->toDateTimeString(),
        ];
    }
}
