<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class UserController extends Controller{


    public function index(Request $request): JsonResponse
{
    $users = User::all(); 
    return response()->json($users);
}


    public function register(Request $request): JsonResponse{
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = new User([
        'name' => $data['name'],
        'email' => $data['email'],
    ]);

    $user->api_token = Str::random(80);
    $user->password = Hash::make($data['password']);
    $user->save();

    return response()->json([
        'user' => $user,
        'api_token' => $user->api_token
        
    ]);
    }

        public function login(Request $request): JsonResponse{
            $data = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $data['email'])->first();

            if(!$user || !Hash::check($data['password'], $user->password)){
                return response()->json(['message' => 'Invalid credentials'], 401);
            }


            $user->api_token = Str::random(80);
            $user->save();


            return response()->json([
                'user' => $user,
                'api_token' => $user->api_token
            ]);
        }


    public function logout(Request $request): JsonResponse{
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()->json(['message' => 'Logged out successfully']);
    }

    
}
