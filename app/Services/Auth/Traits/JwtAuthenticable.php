<?php

namespace App\Services\Auth\Traits;

use Firebase\JWT\JWT;
use App\Models\JwtToken;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait JwtAuthenticable
{
    public function createToken(string $title): string
    {
        $uniqueId = $this->generateUniqueId();
        $token = JWT::encode([
            'iss' => config('app.url'),
            'aud' => config('app.url'),
            'iat' => now()->getTimestamp(),
            'nbf' => now()->getTimestamp(),
            'sub' => $this->id,
            'user_uuid' => $this->uuid,
            'unique_id' => $uniqueId,
        ], config('jwt.key.private'), 'RS256');
        $this->tokens()->create([
            'token_title' => $title,
            'unique_id' => $uniqueId,
            'last_used_at' => now(),
        ]);
        return $token;
    }

    private function generateUniqueId(): string
    {
        $uniqueId = Str::uuid();
        $id = Str::replace('-', '', $uniqueId);
        if (is_array($id)) {
            return $id[0];
        }
        return $id;
    }

    public function getAuthIdentifierName(): string
    {
        return 'uuid';
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(JwtToken::class);
    }
}
