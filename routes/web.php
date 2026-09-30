<?php

use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Restaurant\OrderController as RestaurantOrderController;
use App\Http\Controllers\Restaurant\MenuItemController;
use App\Http\Controllers\Rider\OrderController as RiderOrderController;
use App\Http\Controllers\Rider\AvailabilityController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Customer\FavoriteFoodController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\RestaurantReviewController;
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Customer\RestaurantController;
use App\Http\Controllers\Restaurant\ReviewReplyController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

// ============================================
// ROOT REDIRECT
// ============================================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('customer.restaurants');
});

// ============================================
// PUBLIC ROUTES (Guest Access)
// ============================================
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('customer.restaurants');
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('customer.restaurants.show');
Route::get('/restaurants/{restaurant}/reviews', [RestaurantReviewController::class, 'index'])
    ->name('customer.restaurants.reviews');
Route::get('/random-menu-items', [RestaurantController::class, 'randomMenuItems'])
    ->name('customer.menu.random');

// ============================================
// CUSTOMER ROUTES (Auth Required)
// ============================================
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('customer.favorites');
    Route::post('/favorites/{restaurant}/toggle', [FavoriteController::class, 'toggle'])->name('customer.favorites.toggle');
    Route::post('/favorites/food/{menuItem}/toggle', [FavoriteFoodController::class, 'toggle'])->name('customer.favorites.food.toggle');

    // Orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders');
    Route::post('/orders', [CustomerOrderController::class, 'store'])->name('customer.orders.store');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/orders/{order}/rate', [CustomerOrderController::class, 'rate'])->name('customer.orders.rate');
    Route::post('/orders/{order}/reorder', [CustomerOrderController::class, 'reorder'])->name('customer.orders.reorder');
    Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('customer.orders.cancel');

    // ⭐ CHAT — Customer
    Route::get('/chat', [CustomerChatController::class, 'inbox'])->name('customer.chat');
    Route::get('/chat/unread-active', [CustomerChatController::class, 'unreadActive'])->name('customer.chat.unread-active');
    Route::delete('/chat/clear-all', [CustomerChatController::class, 'clearChats'])->name('customer.chat.clear-all');
    Route::get('/chat/{order}', [CustomerChatController::class, 'show'])->name('customer.chat.show');

    // ⭐ CHAT — Order-specific (AJAX)
    Route::get('/orders/{order}/chat', [ChatController::class, 'index'])->name('customer.chat.index');
    Route::post('/orders/{order}/chat', [ChatController::class, 'store'])->name('customer.chat.store');
    Route::get('/orders/{order}/chat/unread', [ChatController::class, 'unread'])->name('customer.chat.unread');
    Route::post('/orders/{order}/chat/typing', [ChatController::class, 'typing'])->name('customer.chat.typing');
    Route::post('/orders/{order}/chat/mark-read', [ChatController::class, 'markRead'])->name('customer.chat.mark-read');

    // ⭐ REVIEWS — Customer
    Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('customer.reviews.store');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('customer.reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('customer.reviews.destroy');
    Route::post('/reviews/{review}/vote', [ReviewController::class, 'vote'])->name('customer.reviews.vote');
    Route::post('/reviews/{review}/report', [ReviewController::class, 'report'])->name('customer.reviews.report');
});

// ============================================
// RESTAURANT ROUTES
// ============================================
Route::middleware(['auth', 'role:restaurant'])->prefix('restaurant')->group(function () {
    // Dashboard & main pages
    Route::get('/dashboard', [RestaurantOrderController::class, 'dashboard'])->name('restaurant.dashboard');
    Route::get('/orders', [RestaurantOrderController::class, 'orders'])->name('restaurant.orders');
    Route::get('/analytics', [RestaurantOrderController::class, 'analytics'])->name('restaurant.analytics');

    // Order actions
    Route::post('/orders/{order}/confirm', [RestaurantOrderController::class, 'confirm'])
        ->name('restaurant.orders.confirm');

    Route::post('/orders/{order}/reject', [RestaurantOrderController::class, 'reject'])
        ->name('restaurant.orders.reject');
// ⭐ AI Assistant
    Route::post('/ai/chat', [\App\Http\Controllers\Restaurant\AiAssistantController::class, 'chat'])
        ->name('restaurant.ai.chat');

    Route::get('/ai/status', [\App\Http\Controllers\Restaurant\AiAssistantController::class, 'status'])
        ->name('restaurant.ai.status');
    // ⭐ Start Preparing — 'confirmed' → 'preparing'
    Route::post('/orders/{order}/ready', [RestaurantOrderController::class, 'ready'])
        ->name('restaurant.orders.ready');

    // ⭐ Mark as Ready — 'preparing' → 'ready_for_pickup' / 'rider_assigned'
    Route::post('/orders/{order}/mark-ready', [RestaurantOrderController::class, 'markReady'])
        ->name('restaurant.orders.mark-ready');

    Route::post('/orders/external', [RestaurantOrderController::class, 'storeExternal'])
        ->name('restaurant.orders.external');

    // Operating Hours
    Route::get('/hours', [RestaurantOrderController::class, 'hours'])->name('restaurant.hours');
    Route::patch('/hours', [RestaurantOrderController::class, 'updateHours'])->name('restaurant.hours.update');

    // Reviews
    Route::get('/reviews', [ReviewReplyController::class, 'index'])->name('restaurant.reviews.index');
    Route::post('/reviews/{review}/reply', [ReviewReplyController::class, 'store'])->name('restaurant.reviews.reply');

    // Toggle open/close
    Route::post('/toggle-open', [RestaurantOrderController::class, 'toggleOpen'])->name('restaurant.toggle-open');

    // Profile
    Route::get('/profile', fn() => view('restaurant.profile', ['restaurant' => auth()->user()->restaurant]))
        ->name('restaurant.profile');
    Route::patch('/profile', [RestaurantOrderController::class, 'updateProfile'])->name('restaurant.profile.update');

    // Image uploads
    Route::post('/profile/cover', [RestaurantOrderController::class, 'uploadCover'])->name('restaurant.profile.cover');
    Route::delete('/profile/cover', [RestaurantOrderController::class, 'removeCover'])->name('restaurant.profile.cover.remove');
    Route::post('/profile/image', [RestaurantOrderController::class, 'uploadProfileImage'])->name('restaurant.profile.image');
    Route::delete('/profile/image', [RestaurantOrderController::class, 'removeProfileImage'])->name('restaurant.profile.image.remove');

    // Menu items
    Route::resource('menu-items', MenuItemController::class);
    Route::patch('/menu-items/{menuItem}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('menu-items.toggle');
});
// ============================================
// RIDER ROUTES
// ============================================
Route::middleware(['auth', 'role:rider'])->prefix('rider')->group(function () {
    // Dashboard & main pages
    Route::get('/dashboard', [AvailabilityController::class, 'dashboard'])->name('rider.dashboard');
    Route::get('/history', [AvailabilityController::class, 'history'])->name('rider.history');
    Route::get('/earnings', [AvailabilityController::class, 'earnings'])->name('rider.earnings');

    // Availability & location
    Route::post('/online', [AvailabilityController::class, 'toggleOnline'])->name('rider.online');
    Route::post('/location', [AvailabilityController::class, 'updateLocation'])->name('rider.location');

    // ⭐ CHAT — Rider
    Route::get('/chat', [AvailabilityController::class, 'chatInbox'])->name('rider.chat');
    Route::get('/chat/unread-active', [AvailabilityController::class, 'unreadActive'])->name('rider.chat.unread-active');
    Route::delete('/chat/clear-all', [AvailabilityController::class, 'clearChats'])->name('rider.chat.clear-all');
    Route::get('/chat/{order}', [AvailabilityController::class, 'chat'])->name('rider.chat.show');

    // ⭐ CHAT — Order-specific (AJAX)
    Route::get('/orders/{order}/chat', [ChatController::class, 'index'])->name('rider.chat.index');
    Route::post('/orders/{order}/chat', [ChatController::class, 'store'])->name('rider.chat.store');
    Route::get('/orders/{order}/chat/unread', [ChatController::class, 'unread'])->name('rider.chat.unread');
    Route::post('/orders/{order}/chat/typing', [ChatController::class, 'typing'])->name('rider.chat.typing');
    Route::post('/orders/{order}/chat/mark-read', [ChatController::class, 'markRead'])->name('rider.chat.mark-read');

    // Order actions
    Route::post('/orders/{order}/accept', [RiderOrderController::class, 'accept'])->name('rider.orders.accept');
    Route::patch('/orders/{order}/status', [RiderOrderController::class, 'updateStatus'])->name('rider.orders.status');
    Route::post('/orders/{order}/payment', [RiderOrderController::class, 'recordPayment'])->name('rider.orders.payment');

    // Delivery offers
    Route::get('/offers/active', [RiderOrderController::class, 'activeOffers'])->name('rider.offers.active');
    Route::get('/offers/{order}/details', [RiderOrderController::class, 'showOffer'])->name('rider.offers.details');
});

// ============================================
// ADMIN ROUTES
// ============================================
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
        // ⭐ Light/Dark logo routes
    Route::post('/config/light-logo', [ConfigController::class, 'uploadLightLogo'])
        ->name('admin.config.light-logo.upload');
    Route::delete('/config/light-logo', [ConfigController::class, 'removeLightLogo'])
        ->name('admin.config.light-logo.remove');

    Route::post('/config/dark-logo', [ConfigController::class, 'uploadDarkLogo'])
        ->name('admin.config.dark-logo.upload');
    Route::delete('/config/dark-logo', [ConfigController::class, 'removeDarkLogo'])
        ->name('admin.config.dark-logo.remove');
    Route::get('/orders/{order}', [AccountController::class, 'showOrder'])->name('admin.orders.show');
    Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/accounts', [AccountController::class, 'accounts'])->name('admin.accounts');
    Route::get('/orders', [AccountController::class, 'orders'])->name('admin.orders');
    Route::get('/orders/export', [AccountController::class, 'exportOrders'])->name('admin.orders.export');
    Route::get('/reports', [AccountController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export', [AccountController::class, 'exportReports'])->name('admin.reports.export');

    // User management
    Route::patch('/users/{user}/approve', [AccountController::class, 'approve'])->name('admin.users.approve');
    Route::patch('/users/{user}/disable', [AccountController::class, 'disable'])->name('admin.users.disable');
    Route::delete('/users/{user}', [AccountController::class, 'destroy'])->name('admin.users.destroy');

    // Reviews moderation
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
    Route::patch('/reviews/{review}/hide', [AdminReviewController::class, 'hide'])->name('admin.reviews.hide');
    Route::patch('/reviews/{review}/publish', [AdminReviewController::class, 'publish'])->name('admin.reviews.publish');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    // Settings
    Route::get('/settings', fn() => view('admin.settings'))->name('admin.settings');
    Route::post('/config', [ConfigController::class, 'update'])->name('admin.config.update');
    Route::post('/config/logo', [ConfigController::class, 'uploadLogo'])->name('admin.config.logo.upload');
    Route::delete('/config/logo', [ConfigController::class, 'removeLogo'])->name('admin.config.logo.remove');
    Route::post('/config/logo/size', [ConfigController::class, 'updateLogoSize'])->name('admin.config.logo.size');
});

// ============================================
// NOTIFICATIONS (all authenticated users)
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Push subscriptions
    Route::get('/push/vapid-public-key', [PushSubscriptionController::class, 'vapidPublicKey'])->name('push.vapid');
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::delete('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    Route::get('/push/status', [PushSubscriptionController::class, 'status'])->name('push.status');
    Route::post('/push/test', [PushSubscriptionController::class, 'test'])->name('push.test');
});

// ============================================
// PROFILE & SETTINGS (all authenticated users)
// ============================================
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::delete('/settings/account', [SettingsController::class, 'destroy'])->name('settings.destroy');
});

// ============================================
// DASHBOARD REDIRECT
// ============================================
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->role) {
        'customer' => redirect()->route('customer.orders'),
        'restaurant' => redirect()->route('restaurant.dashboard'),
        'rider' => redirect()->route('rider.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        default => redirect('/'),
    };
})->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';