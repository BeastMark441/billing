<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\NodeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\ServerController;
use App\Http\Controllers\Client\BalanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\Client\TicketController;
use App\Http\Controllers\Client\TicketMessageController as ClientTicketMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\TrialController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TicketMessageController as AdminTicketMessageController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/payment/callback', [PaymentController::class, 'callback']);

// Public catalog endpoints
Route::get('/products', [ProductController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/user/password', [AuthController::class, 'updatePassword']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    
    
    Route::name('client.')->prefix('client')->group(function () {
        Route::get('/servers', [ServerController::class, 'index']);
        Route::get('/servers/{server}', [ServerController::class, 'show']);
        Route::get('/servers/{server}/resources', [ServerController::class, 'resources']);
        Route::post('/servers/{server}/power', [ServerController::class, 'power']);
        Route::post('/servers/{server}/cancel', [ServerController::class, 'cancel']);
        Route::post('/servers/{server}/change-plan', [ServerController::class, 'changePlan']);
        
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders', [OrderController::class, 'index']);
        
        Route::get('/balance', [BalanceController::class, 'index']);
        Route::post('/balance/topup', [PaymentController::class, 'create']);

        Route::apiResource('tickets', TicketController::class)->except(['update']);
        Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply']);
        Route::put('/messages/{message}', [ClientTicketMessageController::class, 'update']);
        Route::delete('/messages/{message}', [ClientTicketMessageController::class, 'destroy']);
    });
    
    // Admin Routes
    Route::middleware(['can:admin'])->name('admin.')->prefix('admin')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\Admin\StatsController::class, 'index']);
        
        Route::post('/users/{user}/block', [UserController::class, 'block']);
        Route::post('/users/{user}/unblock', [UserController::class, 'unblock']);
        Route::post('/users/{user}/verify-email', [UserController::class, 'verifyEmail']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword']);
        
        Route::apiResource('products', ProductController::class);
        Route::apiResource('nodes', NodeController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('servers', \App\Http\Controllers\Admin\ServerController::class);
        Route::apiResource('tickets', AdminTicketController::class)->except(['store']);
        Route::post('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply']);
        Route::put('/messages/{message}', [AdminTicketMessageController::class, 'update']);
        Route::delete('/messages/{message}', [AdminTicketMessageController::class, 'destroy']);
        Route::get('/tickets-report', [AdminTicketController::class, 'report']);
        Route::apiResource('coupons', CouponController::class)->except(['show']);
        Route::apiResource('trials', TrialController::class)->except(['show']);
        Route::apiResource('categories', CategoryController::class)->except(['show']);
        Route::post('/categories/{category}/visibility', [CategoryController::class, 'setVisibility']);
        Route::post('/categories/{category}/products/visibility', [CategoryController::class, 'setProductsVisibility']);
    });
});
