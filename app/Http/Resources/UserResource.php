<?php

namespace App\Http\Resources;

use App\Traits\HasTimestamp;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use HasTimestamp;
    
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
            'avatar_link' => asset($this->avatar_path),
            'signed_in' => $this->formatTimestamp($this->signed_in),
            'created_at' => $this->formatTimestamp($this->created_at),
            'updated_at' => $this->formatTimestamp($this->updated_at),
        ];
    }
}
