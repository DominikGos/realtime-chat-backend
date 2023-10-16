<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'avatar_path' => $this->avatar_path,
            'avatar_link' => asset($this->avatar_path),
            'signed_in' => TimestampResource::make($this->signed_in),
            'created_at' => TimestampResource::make($this->created_at),
            'updated_at' => TimestampResource::make($this->updated_at),
        ];
    }
}
