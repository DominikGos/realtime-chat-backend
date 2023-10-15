<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
            'created_at' => TimestampResource::make($this->created_at),
            'updated_at' => TimestampResource::make($this->updated_at),
            'last_message' => MessageResource::make($this->messages()->with('user')->latest()->first()),
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
