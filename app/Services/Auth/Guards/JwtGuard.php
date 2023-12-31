<?php

namespace App\Services\Auth\Guards;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class JwtGuard implements Guard
{
    use GuardHelpers;

    protected Request $request;

    /**
     * Create a new authentication guard.
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        try {
            return $this->user = $this->validateToken();
        } catch (\Exception $e) {
            Log::error($e);
            return $this->user = null;
        }
    }

    /**
     * Check if token is valid and return user
     *
     * @return Authenticatable|null
     */
    protected function validateToken(): ?Authenticatable
    {
        /** @var string $bearerToken */
        $bearerToken = $this->getTokenForRequest();
        if ($bearerToken === "") {
            return null;
        }
        $token = (array) JWT::decode($bearerToken, new Key(config('jwt.key.public'), 'RS256'));
        /** @var \App\Models\User $user */
        $user = $this->provider->retrieveById($token['user_uuid']);
        if ($user) {
            $savedToken = JwtToken::where('user_id', $user->id)->where('unique_id', $token['unique_id'])->first();
            if ($savedToken) {
                $savedToken->update(['last_used_at' => now()]);
                return $user;
            }
        }
        return null;
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest(): string
    {
        return $this->request->bearerToken() ?? "";
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array<int|string>  $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        if (empty($credentials['id'])) {
            return false;
        }

        if ($this->provider->retrieveById($credentials['id'])) {
            return true;
        }

        return false;
    }

    public function logout(): void
    {
        $token = (array) JWT::decode($this->getTokenForRequest(), new Key(config('jwt.key.public'), 'RS256'));
        /** @var \App\Models\User $user */
        $user = $this->user();
        JwtToken::where('unique_id', $token['unique_id'])->where('user_id', $user->id)->delete();
        $this->user = null;
        return;
    }
}
