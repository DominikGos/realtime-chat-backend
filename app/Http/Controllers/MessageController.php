<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\MessageIndexRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(MessageIndexRequest $request, int $chatId): JsonResponse
    {
        $start = $request->validated()['start'];
        $limit = 10;
        $chat = Chat::findOrFail($chatId);
        $messages = $chat
            ->messages()
            ->orderBy('id', 'desc')
            ->offset($start)
            ->limit($limit)
            ->get();

        return new JsonResponse([
            'messages' => MessageResource::collection($messages)
        ]);
    }
}
