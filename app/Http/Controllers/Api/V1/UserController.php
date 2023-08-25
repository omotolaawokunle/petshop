<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserWithTokenResource;

class UserController extends Controller
{
    /**
     * Create a user account.
     * @unauthenticated
     */
    public function store(UserRequest $request, User $user): JsonResponse
    {
        $user = User::create($request->toArray());
        return $this->success(new UserWithTokenResource($user));
    }

    /**
     * Get current authenticated user account.
     */
    public function show(): JsonResponse
    {
        return $this->success(new UserResource(Auth::user()));
    }

    /**
     * Update current authenticated user account.
     */
    public function update(UserRequest $request): JsonResponse
    {
        $user = Auth::user();
        $user->update($request->toArray());
        return $this->success(new UserResource($user));
    }

    /**
     * Delete current authenticated user account.
     */
    public function destroy(): JsonResponse
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete();
        return $this->success([]);
    }
}
