<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\ChatIndexRequest;
use App\Http\Requests\Chat\ChatStoreRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\User;
use App\Services\ChatService;
use Error;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService)
    {}

    public function index(ChatIndexRequest $request): JsonResponse
    {
        $start = $request->validated()['start'];
        $limit = 15;
        $chats = Chat::with(['users', 'messages' => function(EloquentBuilder $query) {
            $query->orderBy('created_at');
        }])
            ->offset($start)
            ->limit($limit)
            ->get();

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

        return new JsonResponse([
            'chat' => ChatResource::make($chat->load('users'))
        ], 201);
    }
}
