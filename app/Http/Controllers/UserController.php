<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(IndexRequest $request): JsonResponse
    {
        $start = $request->validated()['start'];
        $limit = 10;

        $users = User::orderBy('id')->offset($start)->limit($limit)->get();

        return new JsonResponse([
            'users' => UserResource::collection($users)
        ]);
    }
}
