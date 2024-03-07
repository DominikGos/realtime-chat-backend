<?php

namespace App\Http\Controllers;

use App\Events\MessageRemoved;
use App\Events\MessageSent;
use App\Http\Requests\Message\MessageIndexRequest;
use App\Http\Requests\Message\MessageStoreRequest;
use App\Http\Resources\MessageFileResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\UnreadMessage;
use App\Services\FileService;
use App\Traits\HasFile;
use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    use HasFile;

    static string $filesDirectory = '/messages';
    static string $storageDisk = 's3';

    public function __construct()
    {
        $this->initFileService(self::$filesDirectory, self::$storageDisk);
    }

    public function index(MessageIndexRequest $request, int $chatId): JsonResponse
    {
        $start = $request->validated()['start'];
        $limit = $request->validated()['limit'] ?? 15;
        $chat = Chat::findOrFail($chatId);

        $this->authorize('viewMessages', $chat);

        $messages = $chat
            ->messages()
            ->with(['files', 'user'])
            ->orderBy('id', 'desc')
            ->offset($start)
            ->limit($limit)
            ->get();

        return new JsonResponse([
            'messages' => MessageResource::collection($messages)
        ]);
    }

    public function store(MessageStoreRequest $request, int $chatId)
    {
        if (empty($request->validated()['files_links']) && !isset($request->validated()['text'])) {
            return new JsonResponse([
                'message' => 'The message must contain text or files.'
            ], 422);
        }

        $chat = Chat::findOrFail($chatId);

        $this->authorize('storeMessage', $chat);

        $message = new Message($request->validated());
        $message->user()->associate(Auth::user());
        $message->chat()->associate($chat);
        $message->save();
        $filesLinks = $request->validated()['files_links'] ?? [];
        $files = [];

        foreach ($filesLinks as $link) {
            $files[] = new MessageFile(['path' => $this->fileService->getFilePath($link)]);
        }

        $message->files()->saveMany($files);
        $unreadMessage = new UnreadMessage();
        $unreadMessage->message()->associate($message);

        foreach ($chat->users as $user) {
            if ($user->id != Auth::id()) {
                $unreadMessage->unreadBy()->associate($user);
                $unreadMessage->save();
            }
        }

        MessageSent::dispatch($message->load(['chat.users', 'files']));

        return new JsonResponse([
            'message' => MessageResource::make($message->load('files'))
        ], 201);
    }

    public function destroy(int $chatId, int $messageId): JsonResponse
    {
        $chat = Chat::findOrFail($chatId);
        $message = Message::findOrFail($messageId);

        $this->authorize('destroyMessage', [$chat, $message]);

        foreach ($message->files as $file) {
            $this->fileService->destroy($file->path);
        }

        $message->delete();

        MessageRemoved::dispatch(MessageResource::make($message->load('chat'))->toJson(), $chat);

        return new JsonResponse(null, 204);
    }
}
