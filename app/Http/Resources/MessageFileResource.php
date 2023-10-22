<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pathLink = $this->path
            ? asset($this->path)
            : null;

        return [
            'id' => $this->id,
            'file_link' => $pathLink,
        ];
    }
}
