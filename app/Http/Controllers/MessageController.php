<?php

namespace App\Http\Controllers;

use App\Events\MessageRemoved;
use App\Events\MessageSent;
use App\Http\Requests\Message\MessageIndexRequest;
use App\Http\Requests\Message\MessageStoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Services\MessageService;
use App\Traits\HasFile;
use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    use HasFile;

    static string $filesDirectory = '/messages';
    static string $storageDisk = 's3';
    private MessageService $messageService;

    public function __construct()
    {
        $this->initFileService(self::$filesDirectory, self::$storageDisk);
        $this->messageService = new MessageService;
    }

    public function index(MessageIndexRequest $request, int $chatId): JsonResponse
    {
        $start = $request->validated()['start'];
        $limit = $request->validated()['limit'] ?? 15;
        $chat = Chat::findOrFail($chatId);

        $this->authorize('viewMessages', $chat);

        $messages = $chat
            ->messages()
            ->with(['files', 'user', 'answerToMessage', 'answerToMessage.files'])
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
        if (empty($request->validated()['files_links']) && !isset($request->validated()['text'])) {
            return new JsonResponse([
                'message' => 'The message must contain text or files.'
            ], 422);
        }

        $chat = Chat::findOrFail($chatId);

        $this->authorize('storeMessage', $chat);

        $message = null;

        try {
            DB::beginTransaction();

            $message = new Message($request->validated());

            $this->messageService->storeMessage($message, Auth::user(), $chat, $request->validated()['answer_to_message_id'] ?? null);

            $this->messageService->storeMessageFiles($request->validated()['files_links'] ?? [], $message);

            $this->messageService->storeUnreadMessages($message, Auth::user(), $chat);

            DB::commit();

            MessageSent::dispatch($message->load(['chat.users', 'files', 'answerToMessage', 'answerToMessage.files']));

            return new JsonResponse([
                'message' => MessageResource::make($message->load('files'))
            ], 201);
        } catch (\Throwable $th) {

            DB::rollBack();

            return new JsonResponse([
                'message' => 'Something went wrong while saving your message.'
            ], 422);
        }
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
