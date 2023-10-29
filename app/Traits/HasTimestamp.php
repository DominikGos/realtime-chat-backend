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
        $timestamp = Carbon::parse($timestamp)->setTimezone('GMT+1');
        $date = '';

        if ($timestamp->diffInYears($now) >= 1)
            $date = $timestamp->format('M d Y');
        else if($timestamp->diffInDays($now) >= 1)
            $date = $timestamp->format('G:i M d');
        else if ($timestamp->diffInDays($now) < 1) 
            $date = $timestamp->format('G:i');
        
        return $date;
    }
}