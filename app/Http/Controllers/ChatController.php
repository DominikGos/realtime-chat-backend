<?php

namespace App\Http\Controllers;

use App\Events\ChatCreated;
use App\Http\Requests\Chat\ChatStoreRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\User;
use App\Services\ChatService;
use Error;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService)
    {}

    public function index(): JsonResponse
    {
        $chats = Auth::user()
            ->chats()
            ->with(['lastMessage', 'users', 'lastMessage.user'])
            ->get()
            ->sortByDesc('lastMessage.created_at');
        
        return new JsonResponse([
            'chats' => ChatResource::collection($chats)
        ]);
    }

    public function get(int $id): JsonResponse
    {
        $chat = Chat::with('users')->findOrFail($id);

        $this->authorize('view', $chat);

        return new JsonResponse([
            'chat' => ChatResource::make($chat)
        ]);
    }   

    public function store(ChatStoreRequest $request): JsonResponse
    {   
        $friend = User::findOrFail($request->validated()['friend_id']);
        $chat = $this->chatService->findChat($friend);

        if($friend->id == Auth::id()) 
            throw new Error('You cannot create chat with yourself.');
            
        if($chat) {
            return new JsonResponse([
                'message' => 'The chat already exists.',
                'chat' => ChatResource::make($chat->load('users')),
            ]);
        }

        $chat = new Chat();
        $chat->save();
        $chat->users()->saveMany([$friend, Auth::user()]);
        $chat->load('users');

        ChatCreated::dispatch($chat, $friend);

        return new JsonResponse([
            'chat' => ChatResource::make($chat)
        ], 201);
    }
}
