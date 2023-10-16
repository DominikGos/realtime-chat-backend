<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'text' => $this->text,
            'created_at' => TimestampResource::make($this->created_at),
            'updated_at' => TimestampResource::make($this->updated_at),
            'files' => MessageFileResource::collection($this->whenLoaded('files')),
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
