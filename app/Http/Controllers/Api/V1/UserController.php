<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Http\Resources\UserWithTokenResource;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user): JsonResponse
    {
        //
    }

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
    public function show(User $user): JsonResponse
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        //
    }
}
