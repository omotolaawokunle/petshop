<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\ResponseCodes;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function adminLogin(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            if ($user->is_admin) {
                $user->update(['last_login_at' => now()]);
                return $this->success(data: [
                    'token' => $user->createToken("admin-auth"),
                ]);
            }
        }
        return $this->error(
            message: "Failed to authenticate user!",
            statusCode: ResponseCodes::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public function adminLogout()
    {
        Auth::logout();
        return $this->success(data: []);
    }
}
