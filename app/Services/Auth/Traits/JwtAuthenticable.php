<?php

namespace App\Services\Auth\Traits;

use Firebase\JWT\JWT;

trait JwtAuthenticable
{

    public function createToken()
    {
        return JWT::encode([
            'iss' => config('app.url'),
            'aud' => config('app.url'),
            'iat' => now()->getTimestamp(),
            'nbf' => now()->getTimestamp(),
            'user_uuid' => $this->uuid,
        ], config('jwt.key.private'), 'RS256');
    }
}
