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
    /**
     * Retrieve a user by their unique identifier
     * @param mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        if (static::where('uuid', $identifier)->exists()) {
            return static::where('uuid', $identifier)->first();
        }
        return null;
    }
}
