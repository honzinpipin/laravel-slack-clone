<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller{


    public function index(Request $request)
{
    $users = User::all(); 
    return response()->json($users);
}


    public function register(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'api_token' => Str::random(80),
    ]);

    return response()->json([
        'user' => $user,
        'api_token' => $user->api_token
        
    ]);
    }

        public function login(Request $request){
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


    public function logout(Request $request){
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()->json(['message' => 'Logged out successfully']);
    }

    
}
