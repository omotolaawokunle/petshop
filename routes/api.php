<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\AuthController;

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
            Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');
        });
    });
});
