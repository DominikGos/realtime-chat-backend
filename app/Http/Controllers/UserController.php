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
        $limit = 10;
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

        if(empty($request->validated()['avatar_path'])) {
            $this->fileService->destroy($user->avatar_path);
        }

        $user->update($request->validated());

        return new JsonResponse([
            'user' => UserResource::make($user)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if($user->avatar_path) {
            $this->fileService->destroy($user->avatar_path);
        }

        $user->delete();

        return new JsonResponse(null, 204);
    }
}
