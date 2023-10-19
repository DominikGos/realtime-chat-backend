<?php 

namespace App\Traits;

use Carbon\Carbon;

trait HasTimestamp 
{
    public function formatTimestamp(?string $timestamp): ?string
    {
        if(empty($timestamp))
            return null;

        $now = now();
        $timestamp = Carbon::parse($timestamp);

        if ($timestamp->diffInDays($now) >= 1)
            $timestamp = $timestamp->format('M d, Y');
        else if ($timestamp->diffInDays($now) < 1)
            $timestamp = $timestamp->format('G:i');

        return $timestamp;
    }
}