<?php
// routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// // ===== PUBLIC ROUTES =====
// Route::get('/', [HomeController::class, 'index'])->name('home');

// // Product routes (public viewing)
// Route::prefix('products')->name('products.')->group(function () {
//     Route::get('/', [ProductController::class, 'index'])->name('index');
//     Route::get('/category/{category}', [ProductController::class, 'byCategory'])->name('category');
//     Route::get('/search', [SearchController::class, 'products'])->name('search');
//     Route::get('/{product}', [ProductController::class, 'show'])->name('show');
//     Route::get('/{product}/images/{image}', [ProductController::class, 'showImage'])->name('image');
// });

// // Category routes
// Route::prefix('categories')->name('categories.')->group(function () {
//     Route::get('/', [CategoryController::class, 'index'])->name('index');
//     Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
// });

// // User profiles (public viewing)
// Route::prefix('users')->name('users.')->group(function () {
//     Route::get('/{user}', [ProfileController::class, 'showPublic'])->name('show');
//     Route::get('/{user}/products', [ProfileController::class, 'userProducts'])->name('products');
//     Route::get('/{user}/reviews', [ReviewController::class, 'userReviews'])->name('reviews');
// });

// // ===== GUEST ROUTES (Chưa đăng nhập) =====
// Route::middleware('guest')->group(function () {
//     // Authentication routes
//     Route::prefix('auth')->name('auth.')->group(function () {
//         // Login
//         Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
//         Route::post('/login', [AuthController::class, 'login'])->name('login.post');
        
//         // Register
//         Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
//         Route::post('/register', [AuthController::class, 'register'])->name('register.post');
        
//         // Password Reset
//         Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
//         Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
//         Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
//         Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
        
//         // Social Authentication
//         Route::get('/{provider}', [AuthController::class, 'redirectToProvider'])->name('provider');
//         Route::get('/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('provider.callback');
//     });
    
//     // Email Verification
//     Route::prefix('email')->name('verification.')->group(function () {
//         Route::get('/verify', [AuthController::class, 'showVerifyEmail'])->name('notice');
//         Route::get('/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verify');
//         Route::post('/verification-notification', [AuthController::class, 'sendVerificationEmail'])->name('send');
//     });
// });

// // ===== AUTHENTICATED ROUTES =====
// Route::middleware(['auth', 'verified'])->group(function () {
    
//     // Logout
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
//     // ===== PRODUCT MANAGEMENT =====
//     Route::prefix('products')->name('products.')->group(function () {
//         Route::get('/create', [ProductController::class, 'create'])->name('create');
//         Route::post('/', [ProductController::class, 'store'])->name('store');
//         Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit')->middleware('can:update,product');
//         Route::put('/{product}', [ProductController::class, 'update'])->name('update')->middleware('can:update,product');
//         Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy')->middleware('can:delete,product');
        
//         // Product status management
//         Route::patch('/{product}/status', [ProductController::class, 'updateStatus'])->name('status')->middleware('can:update,product');
//         Route::patch('/{product}/featured', [ProductController::class, 'toggleFeatured'])->name('featured')->middleware('can:update,product');
        
//         // Product images
//         Route::post('/{product}/images', [ProductController::class, 'uploadImages'])->name('images.store')->middleware('can:update,product');
//         Route::delete('/images/{image}', [ProductController::class, 'deleteImage'])->name('images.destroy');
//     });
    
//     // ===== USER PROFILE =====
//     Route::prefix('profile')->name('profile.')->group(function () {
//         Route::get('/', [ProfileController::class, 'index'])->name('index');
//         Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
//         Route::put('/', [ProfileController::class, 'update'])->name('update');
//         Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        
//         // Profile sections
//         Route::get('/products', [ProfileController::class, 'myProducts'])->name('products');
//         Route::get('/purchases', [ProfileController::class, 'myPurchases'])->name('purchases');
//         Route::get('/sales', [ProfileController::class, 'mySales'])->name('sales');
//         Route::get('/statistics', [ProfileController::class, 'statistics'])->name('statistics');
        
//         // Account settings
//         Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
//         Route::put('/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
//         Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
//         Route::put('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
//     });
    
//     // ===== FAVORITES =====
//     Route::prefix('favorites')->name('favorites.')->group(function () {
//         Route::get('/', [FavoriteController::class, 'index'])->name('index');
//         Route::post('/{product}/toggle', [FavoriteController::class, 'toggle'])->name('toggle');
//         Route::delete('/clear', [FavoriteController::class, 'clear'])->name('clear');
//     });
    
//     // ===== CHAT SYSTEM =====
//     Route::prefix('chat')->name('chat.')->group(function () {
//         Route::get('/', [ChatController::class, 'index'])->name('index');
//         Route::get('/{user}', [ChatController::class, 'show'])->name('show');
//         Route::post('/{user}/messages', [ChatController::class, 'store'])->name('messages.store');
//         Route::get('/{user}/messages', [ChatController::class, 'getMessages'])->name('messages.get');
//         Route::patch('/messages/{message}/read', [ChatController::class, 'markAsRead'])->name('messages.read');
//         Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('messages.destroy');
        
//         // Chat management
//         Route::delete('/{user}', [ChatController::class, 'deleteConversation'])->name('conversation.destroy');
//         Route::patch('/{user}/block', [ChatController::class, 'blockUser'])->name('block');
//         Route::patch('/{user}/unblock', [ChatController::class, 'unblockUser'])->name('unblock');
//     });
    
//     // ===== REVIEWS & RATINGS =====
//     Route::prefix('reviews')->name('reviews.')->group(function () {
//         Route::get('/', [ReviewController::class, 'index'])->name('index');
//         Route::post('/products/{product}', [ReviewController::class, 'storeProductReview'])->name('products.store');
//         Route::post('/users/{user}', [ReviewController::class, 'storeUserReview'])->name('users.store');
//         Route::put('/{review}', [ReviewController::class, 'update'])->name('update')->middleware('can:update,review');
//         Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy')->middleware('can:delete,review');
        
//         // Review responses
//         Route::post('/{review}/responses', [ReviewController::class, 'storeResponse'])->name('responses.store');
//         Route::put('/responses/{response}', [ReviewController::class, 'updateResponse'])->name('responses.update');
//         Route::delete('/responses/{response}', [ReviewController::class, 'destroyResponse'])->name('responses.destroy');
//     });
    
//     // ===== NOTIFICATIONS =====
//     Route::prefix('notifications')->name('notifications.')->group(function () {
//         Route::get('/', [NotificationController::class, 'index'])->name('index');
//         Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
//         Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
//         Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
//         Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
        
//         // Notification settings
//         Route::get('/settings', [NotificationController::class, 'settings'])->name('settings');
//         Route::put('/settings', [NotificationController::class, 'updateSettings'])->name('settings.update');
//     });
    
//     // ===== REPORTS =====
//     Route::prefix('reports')->name('reports.')->group(function () {
//         Route::post('/products/{product}', [ReportController::class, 'reportProduct'])->name('products.store');
//         Route::post('/users/{user}', [ReportController::class, 'reportUser'])->name('users.store');
//         Route::post('/reviews/{review}', [ReportController::class, 'reportReview'])->name('reviews.store');
//         Route::get('/my-reports', [ReportController::class, 'myReports'])->name('my-reports');
//     });
    
//     // ===== TRANSACTIONS =====
//     Route::prefix('transactions')->name('transactions.')->group(function () {
//         Route::get('/', [TransactionController::class, 'index'])->name('index');
//         Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
//         Route::post('/products/{product}/buy', [TransactionController::class, 'initiatePurchase'])->name('buy');
//         Route::patch('/{transaction}/confirm', [TransactionController::class, 'confirmTransaction'])->name('confirm');
//         Route::patch('/{transaction}/cancel', [TransactionController::class, 'cancelTransaction'])->name('cancel');
//         Route::patch('/{transaction}/complete', [TransactionController::class, 'completeTransaction'])->name('complete');
//     });
    
//     // ===== SEARCH & FILTERS =====
//     Route::prefix('search')->name('search.')->group(function () {
//         Route::get('/advanced', [SearchController::class, 'advanced'])->name('advanced');
//         Route::get('/saved', [SearchController::class, 'savedSearches'])->name('saved');
//         Route::post('/save', [SearchController::class, 'saveSearch'])->name('save');
//         Route::delete('/saved/{search}', [SearchController::class, 'deleteSavedSearch'])->name('saved.destroy');
//     });
// });

// // ===== ADMIN ROUTES =====
// Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
//     // Dashboard
//     Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
//     Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
    
//     // User Management
//     Route::prefix('users')->name('users.')->group(function () {
//         Route::get('/', [AdminController::class, 'users'])->name('index');
//         Route::get('/{user}', [AdminController::class, 'showUser'])->name('show');
//         Route::patch('/{user}/verify', [AdminController::class, 'verifyUser'])->name('verify');
//         Route::patch('/{user}/ban', [AdminController::class, 'banUser'])->name('ban');
//         Route::patch('/{user}/unban', [AdminController::class, 'unbanUser'])->name('unban');
//         Route::delete('/{user}', [AdminController::class, 'deleteUser'])->name('destroy');
//     });
    
//     // Product Management
//     Route::prefix('products')->name('products.')->group(function () {
//         Route::get('/', [AdminController::class, 'products'])->name('index');
//         Route::get('/pending', [AdminController::class, 'pendingProducts'])->name('pending');
//         Route::get('/reported', [AdminController::class, 'reportedProducts'])->name('reported');
//         Route::patch('/{product}/approve', [AdminController::class, 'approveProduct'])->name('approve');
//         Route::patch('/{product}/reject', [AdminController::class, 'rejectProduct'])->name('reject');
//         Route::delete('/{product}/force-delete', [AdminController::class, 'forceDeleteProduct'])->name('force-delete');
//     });
    
//     // Category Management
//     Route::prefix('categories')->name('categories.')->group(function () {
//         Route::get('/', [AdminController::class, 'categories'])->name('index');
//         Route::get('/create', [AdminController::class, 'createCategory'])->name('create');
//         Route::post('/', [AdminController::class, 'storeCategory'])->name('store');
//         Route::get('/{category}/edit', [AdminController::class, 'editCategory'])->name('edit');
//         Route::put('/{category}', [AdminController::class, 'updateCategory'])->name('update');
//         Route::delete('/{category}', [AdminController::class, 'deleteCategory'])->name('destroy');
//     });
    
//     // Reports Management
//     Route::prefix('reports')->name('reports.')->group(function () {
//         Route::get('/', [AdminController::class, 'reports'])->name('index');
//         Route::get('/{report}', [AdminController::class, 'showReport'])->name('show');
//         Route::patch('/{report}/resolve', [AdminController::class, 'resolveReport'])->name('resolve');
//         Route::patch('/{report}/dismiss', [AdminController::class, 'dismissReport'])->name('dismiss');
//     });
    
//     // System Settings
//     Route::prefix('settings')->name('settings.')->group(function () {
//         Route::get('/', [AdminController::class, 'settings'])->name('index');
//         Route::put('/general', [AdminController::class, 'updateGeneralSettings'])->name('general');
//         Route::put('/email', [AdminController::class, 'updateEmailSettings'])->name('email');
//         Route::put('/security', [AdminController::class, 'updateSecuritySettings'])->name('security');
//     });
    
//     // Logs & Monitoring
//     Route::prefix('logs')->name('logs.')->group(function () {
//         Route::get('/activity', [AdminController::class, 'activityLogs'])->name('activity');
//         Route::get('/errors', [AdminController::class, 'errorLogs'])->name('errors');
//         Route::get('/system', [AdminController::class, 'systemLogs'])->name('system');
//     });
// });

// // ===== API ROUTES (for AJAX calls) =====
// Route::middleware(['auth:sanctum'])->prefix('api')->name('api.')->group(function () {
    
//     // Quick search
//     Route::get('/search/quick', [SearchController::class, 'quickSearch'])->name('search.quick');
    
//     // Product suggestions
//     Route::get('/products/suggestions', [ProductController::class, 'suggestions'])->name('products.suggestions');
    
//     // User suggestions for chat
//     Route::get('/users/suggestions', [ProfileController::class, 'suggestions'])->name('users.suggestions');
    
//     // Notification count
//     Route::get('/notifications/count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
    
//     // Chat status
//     Route::get('/chat/status', [ChatController::class, 'getStatus'])->name('chat.status');
//     Route::post('/chat/typing', [ChatController::class, 'setTyping'])->name('chat.typing');
    
//     // Location services
//     Route::get('/locations/provinces', [LocationController::class, 'provinces'])->name('locations.provinces');
//     Route::get('/locations/districts/{province}', [LocationController::class, 'districts'])->name('locations.districts');
//     Route::get('/locations/wards/{district}', [LocationController::class, 'wards'])->name('locations.wards');
// });

// // ===== SPECIAL ROUTES =====

// // Sitemap
// Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

// // RSS Feed
// Route::get('/feed', [HomeController::class, 'feed'])->name('feed');

// // Health Check
// Route::get('/health', function () {
//     return response()->json([
//         'status' => 'ok',
//         'timestamp' => now(),
//         'version' => config('app.version', '1.0.0')
//     ]);
// })->name('health');

// // Terms & Privacy
// Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
// Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
// Route::get('/about', [HomeController::class, 'about'])->name('about');
// Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
// Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// // ===== FALLBACK ROUTE =====
// Route::fallback(function () {
//     return view('errors.404');
// });
// Route::middleware(['auth', 'verified'])->group(function () {
    
//     // Logout
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
//     // ===== PRODUCT MANAGEMENT =====
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/create', [ProductController::class, 'create'])->name('create');
//         Route::post('/', [ProductController::class, 'store'])->name('store');
//         Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit')->middleware('can:update,product');
//         Route::put('/{product}', [ProductController::class, 'update'])->name('update')->middleware('can:update,product');
//         Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy')->middleware('can:delete,product');
    });
    Route::get('/', function () {
        return view('pages.home.index');
    })->name('home.index');

    Route::get('/index', function () {
        return redirect()->route('home.index');
    });

    Route::prefix('products')->name('products.')->group(function () {
        // Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/search', [ProductController::class, 'search'])->name('search');
        Route::get('/my', [ProductController::class, 'getProduct'])->name('my');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    });
    Route::prefix('auth')->name('auth.')->group(function () {
        // Login
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    });
     // Category Management
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'categories'])->name('categories');
        // Route::get('/create', [AdminController::class, 'createCategory'])->name('create');
        // Route::post('/', [AdminController::class, 'storeCategory'])->name('store');
        // Route::get('/{category}/edit', [AdminController::class, 'editCategory'])->name('edit');
        // Route::put('/{category}', [AdminController::class, 'updateCategory'])->name('update');
        // Route::delete('/{category}', [AdminController::class, 'deleteCategory'])->name('destroy');
    });