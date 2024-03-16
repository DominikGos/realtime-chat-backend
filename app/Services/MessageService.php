<?php 

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\UnreadMessage;
use App\Models\User;

class MessageService {
    public function storeMessage(Message $message, User $author, Chat $chat, ?int $answerToMessageId): void
    {
        $message->user()->associate($author);
        $message->chat()->associate($chat);
        
        if($answerToMessageId) 
            $message->answer_to_message_id = $answerToMessageId;

        $message->save();
    }

    public function storeMessageFiles(array $filesLinks, Message $message): void
    {
        $files = [];

        foreach ($filesLinks as $link) {
            $files[] = new MessageFile(['path' => $link]);
        }

        $message->files()->saveMany($files);
    }

    public function storeUnreadMessages(Message $message, User $author, Chat $chat): void
    {
        $unreadMessage = new UnreadMessage();
        $unreadMessage->message()->associate($message);

        foreach ($chat->users as $user) {
            if ($user->id != $author->id) {
                $unreadMessage->unreadBy()->associate($user);
                $unreadMessage->save();
            }
        }
    }
}