<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PushNotificationController;

// Customer Front-End Routes (Public)
Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [HomeController::class, 'search']);
Route::get('/medicine/{id}', [HomeController::class, 'medicineDetails']);
Route::get('/set-location', [HomeController::class, 'setLocation']);
Route::get('/medicines/search', [HomeController::class, 'medicineSearchSuggestions']);
Route::get('/popular-medicines', [HomeController::class, 'popularMedicines']);
Route::get('/nearby-pharmacies', [HomeController::class, 'nearbyPharmacies']);

// Debug route - temporary
Route::get('/debug-shops', function() {
    $shops = \App\Models\Shop::all(['id', 'name', 'latitude', 'longitude', 'area']);
    return response()->json([
        'total_shops' => $shops->count(),
        'shops' => $shops,
        'session_location' => [
            'lat' => session('user_lat'),
            'lng' => session('user_lng'),
            'name' => session('user_location')
        ]
    ]);
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register/shop', [AuthController::class, 'showShopRegister']);
Route::post('/register/shop', [AuthController::class, 'shopRegister']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout']);

// WhatsApp Quick Order Actions (Public with Secure Hash Token)
Route::get('/order/{id}/action/{action}', [OrderController::class, 'whatsappAction']);

// WhatsApp Interactive Bot Webhook
Route::match(['get', 'post'], '/api/whatsapp/webhook', [OrderController::class, 'whatsappWebhook']);

// Cart Actions (Public)
Route::get('/smartcart', [CartController::class, 'index']);
Route::get('/smartcart/results', [CartController::class, 'results']);
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/update', [CartController::class, 'update']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [HomeController::class, 'profile']);
    Route::get('/profile/orders', [HomeController::class, 'orders']);
    Route::get('/profile/addresses', [HomeController::class, 'addresses']);
    Route::get('/profile/favourites', [HomeController::class, 'favourites']);
    Route::get('/profile/notifications', [HomeController::class, 'notifications']);
    Route::get('/profile/settings', [HomeController::class, 'settings']);
    Route::get('/profile/help', [HomeController::class, 'help']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::get('/order/{id}/success', [OrderController::class, 'success']);
    Route::get('/order/{id}/status', [OrderController::class, 'getStatus']);
    Route::get('/prescription/upload', [PrescriptionController::class, 'uploadForm']);
    Route::post('/prescription/upload', [PrescriptionController::class, 'store']);
    Route::get('/prescription/{id}/success', [PrescriptionController::class, 'success']);
    
    // Web Push Notification Routes
    Route::post('/subscribe', [PushNotificationController::class, 'subscribe']);
    Route::post('/subscribe/test', [PushNotificationController::class, 'sendTestNotification']);
});

// Protected Shop Dashboard Routes (Requires Auth & Shop Owner Role)
Route::middleware(['auth', 'role.shop_owner'])->group(function () {
    Route::get('/shop/dashboard', [ShopController::class, 'dashboard']);
    Route::post('/api/order/status-background', [ShopController::class, 'statusBackground']);
    Route::post('/shop/toggle-online', [ShopController::class, 'toggleOnline']);
    Route::post('/shop/toggle-delivery', [ShopController::class, 'toggleDelivery']);
    Route::get('/shop/quicksetup', [ShopController::class, 'quickSetupIndex']);
    Route::post('/shop/quicksetup', [ShopController::class, 'quickSetupSave']);
    Route::get('/shop/inventory', [ShopController::class, 'inventoryIndex']);
    Route::get('/shop/medicines/search', [ShopController::class, 'medicineSearchSuggestions']);
    Route::post('/shop/inventory/add', [ShopController::class, 'inventoryAdd']);
    Route::post('/shop/inventory/bulk-upload', [ShopController::class, 'inventoryBulkUpload']);
    Route::post('/shop/inventory/upload-image/{id}', [ShopController::class, 'uploadInventoryExtraImage']);
    Route::get('/shop/inventory/sample-template', [ShopController::class, 'downloadSampleInventoryTemplate']);
    Route::delete('/shop/inventory/delete/{id}', [ShopController::class, 'inventoryDelete']);
    Route::get('/shop/orders', [ShopController::class, 'ordersIndex']);
    Route::post('/shop/order/status', [ShopController::class, 'ordersUpdate']);
    Route::get('/shop/settings', [ShopController::class, 'settingsIndex']);
    Route::post('/shop/update-timings', [ShopController::class, 'updateTimings']);
    Route::post('/shop/update-image', [ShopController::class, 'updateShopImage']);
    Route::post('/shop/update-delivery-settings', [ShopController::class, 'updateDeliverySettings']);
    Route::post('/shop/prescription/status', [PrescriptionController::class, 'updateStatus']);
    Route::post('/shop/update-location', [ShopController::class, 'updateLocation']);
});

// Shop Registration is open to Auth users
Route::middleware('auth')->group(function () {
    Route::post('/shop/register', [ShopController::class, 'register']);
});

// Protected Admin Operations Dashboard Routes (Requires Auth & Admin Role)
Route::middleware(['auth', 'role.admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('/admin/stores', [AdminController::class, 'stores']);
    Route::post('/admin/stores/status', [AdminController::class, 'storesStatus']);
    Route::post('/admin/stores/image', [AdminController::class, 'updateShopImage']);
    Route::delete('/admin/stores/delete/{id}', [AdminController::class, 'storesDelete']);
    Route::get('/admin/approvals', [AdminController::class, 'approvals']);
    Route::get('/admin/medicines', [AdminController::class, 'medicines']);
    Route::post('/admin/medicines/add', [AdminController::class, 'medicinesAdd']);
    Route::get('/admin/clean-inventory-duplicates', [AdminController::class, 'cleanInventoryDuplicates']);
    Route::delete('/admin/medicines/delete/{id}', [AdminController::class, 'medicinesDelete']);
    Route::get('/admin/orders', [AdminController::class, 'orders']);
    Route::post('/admin/orders/status', [AdminController::class, 'ordersStatus']);
    Route::post('/admin/orders/assign-shop', [AdminController::class, 'ordersAssignShop']);
    Route::post('/admin/orders/update-charges', [AdminController::class, 'ordersUpdateCharges']);
    Route::get('/admin/coupons', [AdminController::class, 'coupons']);
    Route::post('/admin/coupons/add', [AdminController::class, 'couponsAdd']);
    Route::delete('/admin/coupons/delete/{id}', [AdminController::class, 'couponsDelete']);
    Route::post('/admin/settings/delivery', [AdminController::class, 'updateDeliverySettings']);
    Route::get('/admin/commission', [AdminController::class, 'commission']);
    Route::post('/admin/commission', [AdminController::class, 'commissionUpdate']);
});

Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon']);
