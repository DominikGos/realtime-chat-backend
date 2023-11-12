<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    use HasFactory;

    public function users(): BelongsToMany 
    {
        return $this->belongsToMany(User::class, 'user_chats');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    
    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function unreadMessages(): HasManyThrough
    {
        return $this->hasManyThrough(UnreadMessage::class, Message::class)->where('unread_by_id', Auth::id());
    }
}
