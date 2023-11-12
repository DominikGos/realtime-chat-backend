<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UnreadMessageController;
use App\Http\Controllers\UserController;
use App\Http\Resources\ChatResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return new JsonResponse([
        'user' =>  UserResource::make($request->user())
    ]);
});

Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::group(['as' => 'users.', 'prefix' => '/users'], function() {
        Route::get('', [UserController::class, 'index'])->name('index');

        Route::get('/{id}', [UserController::class, 'show'])->name('show');

        Route::put('/{id}', [UserController::class, 'update'])->name('update');

        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');

        Route::post('/files', [UserController::class, 'storeFile'])->name('files.store');

        Route::delete('/files', [UserController::class, 'destroyFile'])->name('files.destroy');

        Route::get('/chats-ids', [UserController::class, 'userChatsIds'])->name('chats.ids');
    });
   
    Route::group(['as' => 'chats.', 'prefix' => '/chats'], function() {
        Route::get('', [ChatController::class, 'index'])->name('index');

        Route::get('/{id}', [ChatController::class, 'get'])->name('get');
        
        Route::post('', [ChatController::class, 'store'])->name('store');
        
        Route::get('/{id}/messages', [MessageController::class, 'index'])->name('messages.index');

        Route::post('/{id}/messages', [MessageController::class, 'store'])->name('messages.store');

        Route::delete('/{id}/messages/{messageId}', [MessageController::class, 'destroy'])->name('messages.destroy');

        Route::post('/messages/files', [MessageController::class, 'storeFile'])->name('messages.files.store');

        Route::delete('/messages/files', [MessageController::class, 'destroyFile'])->name('messages.files.destroy');

        Route::delete('/{id}/unread-messages', [UnreadMessageController::class, 'destroy'])->name('unread.messages.destroy');
    });
});
