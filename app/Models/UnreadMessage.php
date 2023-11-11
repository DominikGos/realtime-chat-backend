<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnreadMessage extends Model
{
    use HasFactory;

    public function unreadBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
  
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
