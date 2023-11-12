<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnreadMessageController extends Controller
{
    public function destroy(int $chatId): JsonResponse
    {
        $chat = Chat::findOrFail($chatId);

        $this->authorize('destroyUnreadMessages', $chat);
        
        $chat->unreadMessages()->delete();
        
        return new JsonResponse(null, 204);
    }
}
