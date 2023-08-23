<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Http\Resources\UserWithTokenResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request, User $user): JsonResponse
    {
        $user = User::create($request->toArray());
        return $this->success(new UserWithTokenResource($user));
    }

    /**
     * Display the specified resource.
     */
    public function show(): JsonResponse
    {
        return $this->success(new UserResource(Auth::user()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request): JsonResponse
    {
        $user = Auth::user();
        $user->update($request->toArray());
        return $this->success(new UserResource($user));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): JsonResponse
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete();
        return $this->success([]);
    }
}
