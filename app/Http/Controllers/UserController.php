<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;

class UserController extends Controller
{


    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::all();
        return UserResource::collection($users);
    }


    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $user->api_token = Str::random(80);
        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'user' => new UserResource($user),
            'api_token' => $user->api_token

        ]);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }


        $user->api_token = Str::random(80);
        $user->save();


        return response()->json([
            'user' => new UserResource($user),
            'api_token' => $user->api_token
        ]);
    }


    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()->json(['message' => 'Logged out successfully']);
    }


}
