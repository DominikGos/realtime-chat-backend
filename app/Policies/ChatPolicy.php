<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class ChatPolicy
{
    private function userBelongsToChat(User $user, Chat $chat): bool
    {
        return $chat->users()->find($user->id)
            ? true 
            : false;
    }
    
    private function messageBelongsToChat(Chat $chat, Message $message): bool
    {
        return $chat->messages()->find($message->id)
            ? true 
            : false;
    }

    public function storeMessage(User $user, Chat $chat): bool 
    {
        return $this->userBelongsToChat($user, $chat)
            ? true
            : false;
    }
   
    public function destroyMessage(User $user, Chat $chat, Message $message): bool 
    {
        return $this->userBelongsToChat($user, $chat) && $this->messageBelongsToChat($chat, $message) && Gate::allows('manage-resource', $message)
            ? true
            : false;
    }
}
