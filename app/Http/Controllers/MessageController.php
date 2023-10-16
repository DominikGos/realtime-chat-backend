<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\MessageIndexRequest;
use App\Http\Requests\Message\MessageStoreRequest;
use App\Http\Resources\MessageFileResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageFile;
use App\Services\FileService;
use App\Traits\HasFile;
use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    use HasFile;

    private FileService $fileService;
    static string $filesDirectory = 'message';

    public function __construct() {
        $this->initFileService(self::$filesDirectory); //reuired for HasFile trait
        $this->fileService = new FileService(self::$filesDirectory, 'public');
    }

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

    public function store(MessageStoreRequest $request, int $chatId): JsonResponse
    {
        if( ! isset($request->validated()['filesPaths']) && ! isset($request->validated()['text']))
            throw new Error('The message must contain text or files.');

        $chat = Chat::findOrFail($chatId);
        $message = new Message($request->validated());
        $message->user()->associate(Auth::user());
        $message->chat()->associate($chat);
        $message->save();
        $filesPaths = $request->validated()['filesPaths'] ?? [];
        $files = [];

        foreach($filesPaths as $path) {
            $files[] = new MessageFile(['path' => $path]);
        }

        $message->files()->saveMany($files);
        
        return new JsonResponse([
            'message' => MessageResource::make($message->load('files'))
        ], 201);
    }

    public function destroy(int $messageId): JsonResponse
    {
        $message = Message::findOrFail($messageId);
        
        foreach($message->files as $file) {
            $this->fileService->destroy($file->path);
        }

        $message->delete();

        return new JsonResponse(null, 204);
    }
}
