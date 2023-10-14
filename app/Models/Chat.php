<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
}
