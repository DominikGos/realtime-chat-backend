<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $password = Hash::make($request->validated('password'));
        $userData = array_merge($request->validated(), ['password' => $password]);
        $user = User::create($userData);

        return new JsonResponse([
            'user' => UserResource::make($user),
        ], 201);
    }
}
