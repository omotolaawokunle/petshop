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
            $user =  Auth::user();
            if ($user->is_admin) {
                return $this->success(data: [
                    'token' => $user->createToken(),
                ]);
            }
        }
        return $this->error(
            message: "Failed to authenticate user!",
            statusCode: ResponseCodes::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
