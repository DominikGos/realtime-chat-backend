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
        $chat->unreadMessages()->where('unread_by_id', Auth::id())->delete();
        
        return new JsonResponse(null, 204);
    }
}
