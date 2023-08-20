<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Services\Filters\UserFilter;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\AdminResource;
use App\Http\Requests\UserRequest;
use App\Http\Requests\AdminRequest;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function getUserListing(Request $request, UserFilter $userFilter)
    {
        $users = User::filter($userFilter)->where('is_admin', 0)->paginate($request->get('limit', 10));

        return response()->json($users);
    }

    /**
     * Create admin user.
     */
    public function store(AdminRequest $request)
    {
        $admin = User::create($request->toArray());
        $admin->update(['is_admin' => 1]);
        return $this->success(new AdminResource($admin));
    }

    /**
     * Update user account.
     */
    public function updateUser(UserRequest $request, User $user)
    {
        $user->update($request->toArray());
        return $this->success(new UserResource($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyUser(User $user)
    {
        $user->delete();
        return $this->success([]);
    }
}
