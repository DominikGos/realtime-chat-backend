<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserUpdated;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->validated())) {
            /** @var User $user */
            $user = Auth::user();
            $user->signed_in = now();
            $user->save();
            $token = $user->createToken('app', ['user'])->plainTextToken;

            UserUpdated::dispatch($user);

            return new JsonResponse([
                'user' => UserResource::make($user),
                'token' => $token,
            ], 200);
        }

        return new JsonResponse([
            'errors' => [
                'email' => ['These credentials do not match our records.']
            ],
        ], 422);
    }

    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $user->signed_in = null;
        $user->currentAccessToken()->delete();
        $user->save();

        UserUpdated::dispatch($user);
        
        return new JsonResponse(null, 204);
    }
}
