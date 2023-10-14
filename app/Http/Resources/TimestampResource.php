<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimestampResource extends JsonResource
{
    private function formatTimestamp(string $timestamp): string
    {
        $now = now();
        $timestamp = Carbon::parse($timestamp);

        if ($timestamp->diffInDays($now) >= 1)
            $timestamp = $timestamp->format('M d, Y');
        else if ($timestamp->diffInDays($now) < 1)
            $timestamp = $timestamp->format('G:i');

        return $timestamp;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            $this->formatTimestamp($this->resource)
        ];
    }
}
