<?php

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chatId}', function(User $user, int $chatId) {
    $chat = Chat::findOrFail($chatId);

    return $user->can('view', $chat) 
        ? true
        : false;

}, ['middleware' => ['auth:sanctum']]);

Broadcast::channel('user.updated', function(User $user) {
    return $user 
        ? true
        : false;
});