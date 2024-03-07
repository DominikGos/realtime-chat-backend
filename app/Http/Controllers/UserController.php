<?php

namespace App\Http\Controllers;

use App\Events\UserUpdated;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserSearchRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\User;
use App\Services\FileService;
use App\Traits\HasFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    use HasFile;

    static string $filesDirectory = '/avatars';
    static string $storageDisk = 's3';

    public function __construct()
    {
        $this->initFileService(self::$filesDirectory, self::$storageDisk);
    }

    public function index(UserIndexRequest $request): JsonResponse
    {
        $start = $request->validated()['start'];
        $limit = $request->validated()['limit'] ?? 15;
        $users = User::orderBy('id')->offset($start)->limit($limit)->get();

        return new JsonResponse([
            'users' => UserResource::collection($users)
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return new JsonResponse([
            'user' => UserResource::make($user)
        ]);
    }

    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $avatarPath = $user->avatar_path;

        Gate::authorize('manage-profile', $user);
        
        if(empty($request->validated()['avatar_link']) && isset($user->avatar_path)) {
            $this->fileService->destroy($user->avatar_path);
            $avatarPath = null;
        }

        if($request->validated()['avatar_link']) {
            $avatarPath = $this->fileService->getFilePath($request->validated()['avatar_link']);
        }
        
        $userData = array_merge($request->validated(), ['avatar_path' => $avatarPath]);
        $user->update($userData);
        
        UserUpdated::dispatch($user);
        
        return new JsonResponse([
            'user' => UserResource::make($user)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        Gate::authorize('manage-profile', $user);
        
        if($user->avatar_path) {
            $this->fileService->destroy($user->avatar_path);
        }

        $user->delete();

        return new JsonResponse(null, 204);
    }
    
    public function userChatsIds(): JsonResponse 
    {   
        $ids = Auth::user()->chats()->pluck('chat_id');

        return new JsonResponse([
            'ids' => $ids
        ]);
    }

    public function search(string $userName): JsonResponse 
    {
        $firstPart = '';
        $secondPart = '';
        $userNameParts = explode(' ', $userName);
        $firstPart = $userNameParts[0];
        $secondPart = $userNameParts[1] ?? null;


        $users = User::where('first_name', 'like', "$firstPart%")
            ->orWhere('last_name', 'like', "$firstPart%")
            ->when($secondPart, function(Builder $query, string $secondPart) {
                $query->where('last_name', 'like', "$secondPart%")
                    ->orWhere('first_name', 'like', "$secondPart%");
            })
            ->get();

        return new JsonResponse([
            'users' => UserResource::collection($users)
        ]);
    }
}
