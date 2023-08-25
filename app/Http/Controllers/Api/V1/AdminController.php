<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserRequest;
use App\Http\Requests\AdminRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\Filters\UserFilter;
use App\Http\Resources\AdminResource;

class AdminController extends Controller
{
    /**
     * Get a listing of users.
     *
     */
    public function getUserListing(Request $request, UserFilter $userFilter): JsonResponse
    {
        $users = User::filter($userFilter)
            ->where('is_admin', 0)
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: $users, onlyData: true);
    }

    /**
     * Create admin user.
     */
    public function store(AdminRequest $request): JsonResponse
    {
        $admin = User::create($request->toArray());
        $admin->update(['is_admin' => 1]);
        return $this->success(new AdminResource($admin));
    }

    /**
     * Update user account.
     * @param User $user The uuid of the user
     */
    public function updateUser(UserRequest $request, User $user): JsonResponse
    {
        $user->update($request->toArray());
        return $this->success(new UserResource($user));
    }

    /**
     * Delete a user account.
     * @param User $user The uuid of the user
     */
    public function destroyUser(User $user): JsonResponse
    {
        $user->delete();
        return $this->success([]);
    }
}
