<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\OrderStatusController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\BrandController;
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
        Route::post('/forgot-password', [AuthController::class, 'sendResetPasswordLinkEmail'])->name('forgot-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
        Route::post('/create', [UserController::class, 'store'])->name('create');
        Route::middleware(['auth:api'])->group(function () {
            Route::get('/', [UserController::class, 'show'])->name('show');
            Route::put('/edit', [UserController::class, 'update'])->name('edit');
            Route::delete('/', [UserController::class, 'destroy'])->name('delete');
            Route::post('/logout', [AuthController::class, 'userLogout'])->name('logout');
        });
    });
    Route::get('/order-statuses', [OrderStatusController::class, 'index'])->name('order-status');
    Route::prefix('/order-status')->name('order-status.')->group(function () {
        Route::get('/{orderStatus}', [OrderStatusController::class, 'show'])->name('show');
        Route::middleware(['auth:api'])->group(function () {
            Route::post('/create', [OrderStatusController::class, 'store'])->name('create');
            Route::put('/{orderStatus}', [OrderStatusController::class, 'update'])->name('edit');
            Route::delete('/{orderStatus}', [OrderStatusController::class, 'destroy'])->name('delete');
        });
    });

    Route::get('/categories', [CategoryController::class, 'index'])->name('category');
    Route::prefix('/category')->name('category.')->group(function () {
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::middleware(['auth:api'])->group(function () {
            Route::post('/create', [CategoryController::class, 'store'])->name('create');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('edit');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('delete');
        });
    });

    Route::get('/brands', [BrandController::class, 'index'])->name('brand');
    Route::prefix('/brand')->name('brand.')->group(function () {
        Route::get('/{brand}', [BrandController::class, 'show'])->name('show');
        Route::middleware(['auth:api'])->group(function () {
            Route::post('/create', [BrandController::class, 'store'])->name('create');
            Route::put('/{brand}', [BrandController::class, 'update'])->name('edit');
            Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('delete');
        });
    });

    Route::get('/payments', [PaymentController::class, 'index'])->name('payment')->middleware('auth:api');
    Route::prefix('/payment')->name('payment.')->group(function () {
        Route::middleware(['auth:api'])->group(function () {
            Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
            Route::post('/create', [PaymentController::class, 'store'])->name('create');
            Route::put('/{payment}', [PaymentController::class, 'update'])->name('edit');
            Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('delete');
        });
    });
});
