<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\ChatIndexRequest;
use App\Http\Requests\Chat\ChatStoreRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\User;
use App\Models\UserChat;
use App\Services\ChatService;
use Error;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService)
    {}

    public function index(ChatIndexRequest $request): JsonResponse
    {
        $start = $request->validated()['start'];
        $limit = 1;
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

    public function store(ChatStoreRequest $request): JsonResponse
    {   
        $chat = new Chat();
        $friend = User::findOrFail($request->validated()['friend_id']);
        $sharedChat = $this->chatService->findChat($friend);

        if($sharedChat) {
            return new JsonResponse([
                'message' => 'The chat already exists.',
                'chat' => ChatResource::make($sharedChat->load('users')),
            ]);
        }

        if($friend->id == Auth::id()) 
            throw new Error('You cannot create chat with yourself.');

        $chat->save();
        $chat->users()->saveMany([$friend, Auth::user()]);

        return new JsonResponse([
            'chat' => ChatResource::make($chat->load('users'))
        ], 201);
    }
}
