<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\InfrastructureCategoryController as AdminInfrastructureCategoryController;
use App\Http\Controllers\Admin\InfrastructureServiceController as AdminInfrastructureServiceController;
use App\Http\Controllers\Admin\InfrastructureSubcategoryController as AdminInfrastructureSubcategoryController;
use App\Http\Controllers\Admin\LogController as AdminLogController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicReceiptController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TBankApiController;
use App\Http\Controllers\TelegramIntegrationController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', function () {
    return view('welcome');
});

// Categories & Products (Public)
Route::get('/categories/{slug}', [PageController::class, 'category'])->name('categories.show');
Route::get('/categories/{slug}/{subSlug}', [PageController::class, 'subcategory'])->name('categories.subcategory');

Route::get('/products', [PageController::class, 'products'])->name('products');
Route::get('/products/{category:slug}', [PageController::class, 'productCategory'])->name('products.category');
Route::get('/products/{category:slug}/{service:slug}', [PageController::class, 'productService'])->name('products.service');
Route::get('/solutions', [PageController::class, 'solutions'])->name('solutions');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::get('/status', [PageController::class, 'status'])->name('status');
Route::get('/knowledge-base', [PageController::class, 'knowledgeBase'])->name('knowledge-base');
Route::get('/api-docs', [PageController::class, 'apiDocs'])->name('api-docs');

Route::get('/legal', [PageController::class, 'legal'])->name('legal');
Route::get('/legal/{doc}', [PageController::class, 'legalDoc'])->name('legal.doc');

// SEO
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// Public Receipts
Route::get('/r/{receipt}/{token}', [PublicReceiptController::class, 'show'])->name('receipts.public.show');
Route::get('/r/{receipt}/{token}/download', [PublicReceiptController::class, 'download'])->name('receipts.public.download');
Route::get('/r/{receipt}/{token}/verify', [PublicReceiptController::class, 'verify'])->name('receipts.public.verify');

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/account', [DashboardController::class, 'account'])->name('dashboard.account');
    Route::get('/dashboard/security', [DashboardController::class, 'security'])->name('dashboard.security');
    Route::get('/dashboard/security/logs', [DashboardController::class, 'logs'])->name('dashboard.logs');

    Route::post('/dashboard/security/telegram/link', [TelegramIntegrationController::class, 'startLink'])->name('telegram.link.start');
    Route::post('/dashboard/security/telegram/unlink', [TelegramIntegrationController::class, 'unlink'])->name('telegram.link.unlink');
    Route::post('/dashboard/security/notifications', [TelegramIntegrationController::class, 'updatePreferences'])->name('notifications.preferences.update');
    Route::get('/dashboard/infrastructure', [DashboardController::class, 'infrastructure'])->name('dashboard.infrastructure');

    // Billing
    Route::get('/dashboard/billing', [BillingController::class, 'index'])->name('dashboard.billing');
    
    // Help / Tickets
    Route::get('/dashboard/tickets', [TicketController::class, 'index'])->name('dashboard.tickets.index');
    Route::get('/dashboard/tickets/create', [TicketController::class, 'create'])->name('dashboard.tickets.create');
    Route::post('/dashboard/tickets', [TicketController::class, 'store'])->name('dashboard.tickets.store');
    Route::get('/dashboard/tickets/{ticket}', [TicketController::class, 'show'])->name('dashboard.tickets.show');
    Route::post('/dashboard/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('dashboard.tickets.reply');
    Route::put('/dashboard/tickets/messages/{message}', [TicketController::class, 'updateMessage'])->name('dashboard.tickets.message.update');
    Route::delete('/dashboard/tickets/messages/{message}', [TicketController::class, 'deleteMessage'])->name('dashboard.tickets.message.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.delete');

    // Orders & Ordering (Dashboard context)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/dashboard/infrastructure/services/{service}/order', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/dashboard/infrastructure/services/{service}/order', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{order}/auto-renewal', [OrderController::class, 'toggleAutoRenewal'])->name('orders.auto-renewal');
    Route::post('/orders/{order}/renew', [OrderController::class, 'renew'])->name('orders.renew');

    // Cart
    Route::get('/dashboard/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/infrastructure/services/{service}/cart', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/dashboard/cart/{order}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/dashboard/cart/{order}/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Payments
    Route::post('/payments/create', [TBankApiController::class, 'store'])->name('payments.create');
    Route::get('/payments/success', [TBankApiController::class, 'success'])->name('payments.success');
    Route::get('/payments/failed', [TBankApiController::class, 'failed'])->name('payments.failed');

    // Receipts
    Route::get('/receipts', [ReceiptController::class, 'index'])->name('dashboard.receipts.index');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('dashboard.receipts.show');
    Route::get('/receipts/{receipt}/download', [ReceiptController::class, 'download'])->name('dashboard.receipts.download');
});

// Webhooks (Public)
Route::post('/payments/webhook', [TBankApiController::class, 'webhook'])->name('payments.webhook');
Route::post('/telegram/webhook', TelegramWebhookController::class)->name('telegram.webhook');

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Admin Users Management
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/balance', [AdminUserController::class, 'updateBalance'])->name('users.balance');
    Route::post('users/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])->name('users.verify-email');
    Route::post('users/{user}/send-verification', [AdminUserController::class, 'sendVerificationLink'])->name('users.send-verification');
    Route::post('users/{user}/send-reset', [AdminUserController::class, 'sendResetLink'])->name('users.send-reset');
    Route::post('users/{user}/ban-ip', [AdminUserController::class, 'banIp'])->name('users.ban-ip');
    Route::post('users/{user}/notify', [AdminUserController::class, 'notifyUser'])->name('users.notify');

    // Admin Tickets (Support)
    Route::resource('tickets', AdminTicketController::class);
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

    Route::resource('infrastructure/subcategories', AdminInfrastructureSubcategoryController::class)->names([
        'index' => 'infrastructure.subcategories.index',
        'create' => 'infrastructure.subcategories.create',
        'store' => 'infrastructure.subcategories.store',
        'edit' => 'infrastructure.subcategories.edit',
        'update' => 'infrastructure.subcategories.update',
        'destroy' => 'infrastructure.subcategories.destroy',
    ]);

    Route::resource('infrastructure/services', AdminInfrastructureServiceController::class)->names([
        'index' => 'infrastructure.services.index',
        'create' => 'infrastructure.services.create',
        'store' => 'infrastructure.services.store',
        'edit' => 'infrastructure.services.edit',
        'update' => 'infrastructure.services.update',
        'destroy' => 'infrastructure.services.destroy',
    ]);

    // Orders Management
    Route::get('orders/cart', [\App\Http\Controllers\Admin\OrderController::class, 'cartIndex'])->name('orders.cart.index');
    
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->names([
        'index' => 'orders.index',
        'create' => 'orders.create',
        'store' => 'orders.store',
        'show' => 'orders.show',
        'update' => 'orders.update',
        'destroy' => 'orders.destroy',
    ])->only(['index', 'create', 'store', 'show', 'update', 'destroy']);

    Route::post('orders/{order}/change-plan', [\App\Http\Controllers\Admin\OrderController::class, 'changePlan'])->name('orders.change-plan');
    Route::post('orders/{order}/refund', [\App\Http\Controllers\Admin\OrderController::class, 'refund'])->name('orders.refund');

    // Admin Finance
    Route::get('/finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('finance.index');
    Route::delete('/finance', [\App\Http\Controllers\Admin\FinanceController::class, 'destroyFiltered'])->middleware('superadmin')->name('finance.destroy');
    Route::delete('/finance/{log}', [\App\Http\Controllers\Admin\FinanceController::class, 'destroy'])->middleware('superadmin')->name('finance.logs.destroy');

    // Admin Logs
    Route::get('/logs', [AdminLogController::class, 'index'])->name('logs.index');
});

require __DIR__.'/auth.php';
