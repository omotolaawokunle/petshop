<?php

namespace App\Services\Auth\Guards;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\GuardHelpers;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use App\Models\JwtToken;

class JwtGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Http\Request  $request
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
     * @return void
     */
    protected function validateToken()
    {
        $bearerToken = $this->getTokenForRequest();
        if (is_null($bearerToken)) return null;
        $token = (array) JWT::decode($this->getTokenForRequest(), new Key(config('jwt.key.public'), 'RS256'));
        if ($user = $this->provider->retrieveById($token['user_uuid'])) {
            if ($savedToken = JwtToken::where('user_id', $user->id)->where('unique_id', $token['unique_id'])->first()) {
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
    public function getTokenForRequest(): ?string
    {
        return $this->request->bearerToken();
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials['id'])) {
            return false;
        }

        if ($this->provider->retrieveById($credentials['id'])) {
            return true;
        }

        return false;
    }

    public function logout()
    {
        $token = (array) JWT::decode($this->getTokenForRequest(), new Key(config('jwt.key.public'), 'RS256'));

        JwtToken::where('unique_id', $token['unique_id'])->where('user_id', $this->user()->id)->delete();
        $this->user = null;
        return;
    }
}
