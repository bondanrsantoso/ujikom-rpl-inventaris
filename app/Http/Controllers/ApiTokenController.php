<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Lib\UserTokenManager;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    public function update(Request $requets)
    {
        $token = UserTokenManager::generateToken($request->user());

        return response()->json([
            'token' => $token
        ]);
    }

    public function apiAuth(Request $request)
    {
        if($request->has("nip")){
            $username = $request->input("nip");
        } else {
            $username = $request->input("username");
        }
        $password = $request->input("password");

        if(Auth::once(['username' => $username, "password" => $password])){
            $user = User::where("username", $username)->first();
            $token = UserTokenManager::generateToken($request->user());
            return response()->json([
                'api_token' => $token
            ]);
        }
    }

    public function refreshToken(Request $request)
    {
        $token = UserTokenManager::generateToken($request->user());

        return response()->json([
            'api_token' => $token
        ]);
    }
}
