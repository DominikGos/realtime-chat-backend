<?php

namespace App\Http\Resources;

use App\Traits\HasTimestamp;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    use HasTimestamp;
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $filesLinks = [];
       
        $files = $this->whenLoaded('files');

        foreach($files as $file) {
            $filesLinks[] = $file->path;
        }

        return [
            'id' => $this->id,
            'text' => $this->text,
            'created_at' => $this->formatTimestamp($this->created_at),
            'updated_at' => $this->formatTimestamp($this->updated_at),
            'files_links' => $filesLinks,
            'answer_to_message' => MessageResource::make($this->whenLoaded('answerToMessage')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'chat' => ChatResource::make($this->whenLoaded('chat'))
        ];
    }
}
