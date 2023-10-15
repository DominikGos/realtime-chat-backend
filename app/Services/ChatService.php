<?php 

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ChatService 
{
    function findChat(User $friend): ?Chat
    {
        $chat = Auth::user()->chats()->whereHas('users', function(Builder $query) use ($friend) {
            $query->where('user_id', $friend->id);
        })->first();

        return $chat;
    }
}