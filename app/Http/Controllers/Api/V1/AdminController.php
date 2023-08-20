<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Services\Filters\UserFilter;
use App\Models\User;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
