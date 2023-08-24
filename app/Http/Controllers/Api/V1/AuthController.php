<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Services\ResponseCodes;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Notifications\PasswordResetNotification;

class AuthController extends Controller
{
    public function adminLogin(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
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

    public function userLogin(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);
            return $this->success(data: [
                'token' => $user->createToken("admin-auth"),
            ]);
        }
        return $this->error(
            message: "Failed to authenticate user!",
            statusCode: ResponseCodes::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public function userLogout()
    {
        Auth::logout();
        return $this->success(data: []);
    }

    public function sendResetPasswordLinkEmail(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        $resetToken = Password::createToken($user);
        $user->notify(new PasswordResetNotification($resetToken));
        return $this->success(['reset_token' => $resetToken]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
                Password::deleteToken($user);
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return $this->success(data: ["message" => "Password has been successfully updated."]);
        }
        if ($response === Password::INVALID_TOKEN) {
            return $this->error(message: "Invalid or expired token.", statusCode: ResponseCodes::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->error(message: 'Unable to reset password');
    }
}
