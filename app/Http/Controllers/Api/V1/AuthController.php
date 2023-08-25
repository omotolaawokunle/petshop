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
    /**
     * Login an admin account
     * @unauthenticated
     * @response array{success: bool, data: array{token: string}, error: string, errors: array}
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function adminLogin(LoginRequest $request): JsonResponse
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

    /**
     * Log out an admin account
     * @response array{success: bool, data: array, error: string, errors: array}
     * @return JsonResponse
     */
    public function adminLogout(): JsonResponse
    {
        Auth::logout();
        return $this->success(data: []);
    }

    /**
     * Login an user account
     * @unauthenticated
     * @response array{success: bool, data: array{token: string}, error: string, errors: array}
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function userLogin(LoginRequest $request): JsonResponse
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

    /**
     * Log out a user account
     * @response array{success: bool, data: array, error: string, errors: array}
     * @return JsonResponse
     */
    public function userLogout(): JsonResponse
    {
        Auth::logout();
        return $this->success(data: []);
    }

    /**
     * Creates a token to reset a user password
     * @unauthenticated
     * @response array{success: bool, data: array{reset_token: string}, error: string, errors: array}
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function sendResetPasswordLinkEmail(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        $resetToken = Password::createToken($user);
        $user->notify(new PasswordResetNotification($resetToken));
        return $this->success(['reset_token' => $resetToken]);
    }

    /**
     * Reset a user password with tokken
     * @unauthenticated
     * @response array{success: bool, data: array{message: string}, error: string, errors: array}
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
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
