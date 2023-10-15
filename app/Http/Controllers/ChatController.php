<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\ChatStoreRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\User;
use App\Models\UserChat;
use Error;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private function findChat(User $friend): ?Chat
    {
        $chat = Auth::user()->chats()->whereHas('users', function(Builder $query) use ($friend) {
            $query->where('user_id', $friend->id);
        })->first();

        return $chat;
    }

    public function store(ChatStoreRequest $request): JsonResponse
    {   
        $chat = new Chat();
        $friend = User::findOrFail($request->validated()['friendId']);
        $sharedChat = $this->findChat($friend);

        if($sharedChat) {
            return new JsonResponse([
                'message' => 'The chat already exists.',
                'chat' => ChatResource::make($sharedChat),
            ]);
        }

        if($friend->id == Auth::id()) 
            throw new Error('You cannot create chat with yourself.');

        $chat->save();
        $chat->users()->saveMany([$friend, Auth::user()]);

        return new JsonResponse([
            'chat' => ChatResource::make($chat)
        ], 201);
    }
}
