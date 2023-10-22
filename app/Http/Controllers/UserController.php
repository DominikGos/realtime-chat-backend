<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FileService;
use App\Traits\HasFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    use HasFile;

    static string $filesDirectory = 'user';

    public function __construct()
    {
        $this->initFileService(self::$filesDirectory);
    }

    public function index(UserIndexRequest $request): JsonResponse
    {
        $start = $request->validated()['start'];
        $limit = 15;
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

        Gate::authorize('manage-profile', $user);

        if(empty($request->validated()['avatar_link'])) {
            $this->fileService->destroy($user->avatar_path);
        }

        $avatarPath = $this->fileService->getFilePath($request->validated()['avatar_link']);
        $userData = array_merge($request->validated(), ['avatar_path' => $avatarPath]);
        $user->update($userData);

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
}
