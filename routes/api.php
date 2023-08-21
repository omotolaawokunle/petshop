<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/v1')->namespace("Api\V1")->name('api.v1.')->group(function () {
    Route::prefix('/admin')->name('admin.')->group(function () {
        Route::post('/login', [AuthController::class, 'adminLogin'])->name('login');
        Route::middleware(['auth:api', 'is_admin'])->group(function () {
            Route::get('/user-listing', [AdminController::class, 'getUserListing'])->name('user-listing');
            Route::post('/create', [AdminController::class, 'store'])->name('create');
            Route::put('/user-edit/{user}', [AdminController::class, 'updateUser'])->name('users.edit');
            Route::delete('/user-delete/{user}', [AdminController::class, 'destroyUser'])->name('users.delete');
            Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');
        });
    });

    Route::prefix('/user')->name('user.')->group(function () {
        Route::post('/login', [AuthController::class, 'userLogin'])->name('login');
        Route::middleware(['auth:api'])->group(function () {

            Route::post('/logout', [AuthController::class, 'userLogout'])->name('logout');
        });
    });
});
