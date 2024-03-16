<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'text'
    ];

    public function files(): HasMany
    {
        return $this->hasMany(MessageFile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
   
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function answerToMessage(): BelongsTo 
    {
        return $this->belongsTo(Message::class, 'answer_to_message_id');
    }
}
