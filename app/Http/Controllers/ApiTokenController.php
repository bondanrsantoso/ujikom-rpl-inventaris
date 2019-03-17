<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Lib\UserTokenManager;

class ApiTokenController extends Controller
{
    public function update(Request $requets)
    {
        $token = UserTokenManager::generateToken($request->user());

        return response()->json([
            'token' => $token
        ]);
    }
}
