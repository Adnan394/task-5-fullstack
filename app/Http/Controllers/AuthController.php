<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use League\OAuth2\Server\RequestEvent;

class AuthController extends Controller
{
    public function index(Request $request) {
        $data = $request->validate([
            'name' => 'required|min:5|unique:users',
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:8'
        ]);
        $data['password'] = Hash::make($request->password);

        if($data) {
            User::create($data);
            return response()->json(['message' => 'Register Success, Please Login!']);
        }

    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email:dns',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if(! $user || ! Hash::check($request->password, $user->password)) {
            return Response()->json([
                'message' => 'login invalid!',
            ]);
        }
        
        $token = $user->createToken($user->name)->accessToken;
        
        return response()->json([
            'message' => 'login success',
            'data' => [
                'name' => $user->name,
                'email' => $user->email
            ],
            'token' => $token,
        ]);
    }
}