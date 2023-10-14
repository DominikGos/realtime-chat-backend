<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
        $user->update($request->validated());

        return new JsonResponse([
            'user' => UserResource::make($user)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return new JsonResponse(null, 204);
    }
}
