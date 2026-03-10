<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InfrastructureCategoryController as AdminInfrastructureCategoryController;
use App\Http\Controllers\Admin\InfrastructureServiceController as AdminInfrastructureServiceController;
use App\Http\Controllers\Admin\InfrastructureSubcategoryController as AdminInfrastructureSubcategoryController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Static Pages
Route::get('/products', [PageController::class, 'products'])->name('products');
Route::get('/solutions', [PageController::class, 'solutions'])->name('solutions');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'account'])->name('dashboard');
    Route::get('/dashboard/account', [DashboardController::class, 'account'])->name('dashboard.account');
    Route::get('/dashboard/security', [DashboardController::class, 'security'])->name('dashboard.security');
    Route::get('/dashboard/security/logs', [DashboardController::class, 'logs'])->name('dashboard.logs');
    Route::get('/dashboard/infrastructure', [DashboardController::class, 'infrastructure'])->name('dashboard.infrastructure');

    // Billing
    Route::get('/dashboard/billing', [BillingController::class, 'index'])->name('dashboard.billing');
    // Deprecated route, redirect to overview
    Route::get('/dashboard/billing/expenses', [BillingController::class, 'expenses'])->name('dashboard.billing.expenses');

    // Help / Tickets
    Route::get('/dashboard/tickets', [TicketController::class, 'index'])->name('dashboard.tickets.index');
    Route::get('/dashboard/tickets/create', [TicketController::class, 'create'])->name('dashboard.tickets.create');
    Route::post('/dashboard/tickets', [TicketController::class, 'store'])->name('dashboard.tickets.store');
    Route::get('/dashboard/tickets/{ticket}', [TicketController::class, 'show'])->name('dashboard.tickets.show');
    Route::post('/dashboard/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('dashboard.tickets.reply');
    Route::put('/dashboard/tickets/messages/{message}', [TicketController::class, 'updateMessage'])->name('dashboard.tickets.message.update');
    Route::delete('/dashboard/tickets/messages/{message}', [TicketController::class, 'deleteMessage'])->name('dashboard.tickets.message.destroy');

    // System Status (Placeholder view)
    Route::get('/dashboard/status', function () {
        return view('dashboard.status');
    })->name('dashboard.status');

    // Notifications
    Route::get('/notifications/{id}/read', [DashboardController::class, 'markNotificationAsRead'])->name('notifications.read');

    // Orders
    Route::resource('orders', \App\Http\Controllers\OrderController::class)->only(['index', 'show']);
    Route::get('/infrastructure/services/{service}/order', [\App\Http\Controllers\OrderController::class, 'create'])->name('orders.create');
    Route::post('/infrastructure/services/{service}/order', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{order}/auto-renewal', [\App\Http\Controllers\OrderController::class, 'toggleAutoRenewal'])->name('orders.auto-renewal');
    Route::post('/orders/{order}/renew', [\App\Http\Controllers\OrderController::class, 'renew'])->name('orders.renew');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/balance', [AdminUserController::class, 'updateBalance'])->name('users.balance');
    Route::post('users/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])->name('users.verify-email');
    Route::post('users/{user}/send-verification', [AdminUserController::class, 'sendVerificationLink'])->name('users.send-verification');
    Route::post('users/{user}/send-reset', [AdminUserController::class, 'sendResetLink'])->name('users.send-reset');
    Route::post('users/{user}/ban-ip', [AdminUserController::class, 'banIp'])->name('users.ban-ip');
    Route::post('users/{user}/notify', [AdminUserController::class, 'notifyUser'])->name('users.notify');

    // Admin Tickets
    Route::resource('tickets', AdminTicketController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    Route::put('tickets/messages/{message}', [AdminTicketController::class, 'updateMessage'])->name('tickets.message.update');
    Route::delete('tickets/messages/{message}', [AdminTicketController::class, 'deleteMessage'])->name('tickets.message.destroy');

    // Infrastructure Categories
    Route::resource('infrastructure/categories', AdminInfrastructureCategoryController::class)->names([
        'index' => 'infrastructure.categories.index',
        'create' => 'infrastructure.categories.create',
        'store' => 'infrastructure.categories.store',
        'edit' => 'infrastructure.categories.edit',
        'update' => 'infrastructure.categories.update',
        'destroy' => 'infrastructure.categories.destroy',
    ]);

    // Infrastructure Subcategories
    Route::resource('infrastructure/subcategories', AdminInfrastructureSubcategoryController::class)->names([
        'index' => 'infrastructure.subcategories.index',
        'create' => 'infrastructure.subcategories.create',
        'store' => 'infrastructure.subcategories.store',
        'edit' => 'infrastructure.subcategories.edit',
        'update' => 'infrastructure.subcategories.update',
        'destroy' => 'infrastructure.subcategories.destroy',
    ]);

    // Infrastructure Services
    Route::resource('infrastructure/services', AdminInfrastructureServiceController::class)->names([
        'index' => 'infrastructure.services.index',
        'create' => 'infrastructure.services.create',
        'store' => 'infrastructure.services.store',
        'edit' => 'infrastructure.services.edit',
        'update' => 'infrastructure.services.update',
        'destroy' => 'infrastructure.services.destroy',
    ]);

    // Orders Management
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->names([
        'index' => 'orders.index',
        'show' => 'orders.show',
        'update' => 'orders.update',
        'destroy' => 'orders.destroy',
    ])->only(['index', 'show', 'update', 'destroy']);
    
    Route::post('orders/{order}/change-plan', [\App\Http\Controllers\Admin\OrderController::class, 'changePlan'])->name('orders.change-plan');
    Route::post('orders/{order}/refund', [\App\Http\Controllers\Admin\OrderController::class, 'refund'])->name('orders.refund');
});

require __DIR__.'/auth.php';
