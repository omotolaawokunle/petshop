<?php

namespace App\Services\Auth\Traits;

use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use App\Models\JwtToken;

trait JwtAuthenticable
{

    public function createToken(string $title)
    {
        $uniqueId = $this->generateUniqueId();
        $token = JWT::encode([
            'iss' => config('app.url'),
            'aud' => config('app.url'),
            'iat' => now()->getTimestamp(),
            'nbf' => now()->getTimestamp(),
            'sub' => $this->id,
            'user_uuid' => $this->uuid,
            'unique_id' => $uniqueId
        ], config('jwt.key.private'), 'RS256');
        $this->tokens()->create([
            'token_title' => $title,
            'unique_id' => $uniqueId,
            'last_used_at' => now(),
        ]);
        return $token;
    }

    private function generateUniqueId()
    {
        $uniqueId = Str::uuid();
        return Str::replace('-', '', $uniqueId);
    }

    public function getAuthIdentifierName()
    {
        return 'uuid';
    }

    public function tokens()
    {
        return $this->hasMany(JwtToken::class);
    }
}
