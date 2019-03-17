<?php

namespace App\Lib;

use App\User;
use Illuminate\Support\Str;

class UserTokenManager  
{
    public static function generateToken(User $user)
    {
        $token = Str::random(60);

        $user->forceFill([
            'api_token' => hash('sha256', $token)
        ])->save();

        return $token;
    }
}
