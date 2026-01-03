<?php

use App\Models\Service;
use App\Models\BlogPost;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () {
    if (app()->bound('tenant')) {
        return Inertia::render('TenantWelcome', [
            'tenant' => app('tenant'),
            'services' => \App\Models\Service::where('organization_id', app('tenant')->id)->where('is_active', true)->get()
        ]);
    }

    return Inertia::render('Welcome', [
        'featuredServices' => Service::where('is_active', true)->take(3)->get(),
        'latestPosts' => BlogPost::where('is_published', true)->latest('published_at')->take(3)->get(),
        'latestReviews' => \App\Models\Review::where('is_approved', true)->latest()->take(5)->get(),
        'zones' => \App\Models\Zone::all(['name', 'coordinates']), // Pass coordinates for geofencing
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Force Login/Register to Redirect to Main Domain if accessed via Tenant
Route::get('/login', function () {
    if (app()->bound('tenant')) {
        return redirect()->away(config('app.url') . '/login');
    }
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/register', function () {
    if (app()->bound('tenant')) {
        return redirect()->away(config('app.url') . '/register');
    }
    return Inertia::render('Auth/Register');
})->name('register');

Route::get('/shop', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{id}', [\App\Http\Controllers\ShopController::class, 'show'])->name('shop.show');

Route::get('/services', [\App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [\App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');

Route::get('/special-services', function () {
    return Inertia::render('SpecialServices', [
        'services' => Service::where('is_active', true)->where('type', 'special')->get()
    ]);
})->name('special-services');

// Booking Routes
Route::get('/booking/individual', [BookingController::class, 'showIndividualForm'])->name('booking.individual');
Route::post('/booking/individual', [BookingController::class, 'storeIndividual'])->name('booking.store.individual');
Route::get('/booking/group', [BookingController::class, 'showGroupForm'])->name('booking.group');
Route::post('/booking/group', [BookingController::class, 'storeGroup'])->name('booking.store.group');
Route::get('/booking/special', [BookingController::class, 'showSpecialForm'])->name('booking.special');
Route::post('/booking/special', [BookingController::class, 'storeSpecial'])->name('booking.store.special');
Route::get('/booking/workshop', [BookingController::class, 'showWorkshopForm'])->name('booking.workshop');
Route::post('/booking/workshop', [BookingController::class, 'storeWorkshop'])->name('booking.store.workshop');
Route::get('/booking/{appointment}/date', [BookingController::class, 'selectDate'])->name('booking.date');
Route::post('/booking/{appointment}/date', [BookingController::class, 'saveDate'])->name('booking.save-date');
Route::get('/booking/{appointment}/payment', [BookingController::class, 'payment'])->name('booking.payment');
Route::post('/booking/{appointment}/payment', [BookingController::class, 'processPayment'])->name('booking.process-payment');
Route::get('/booking/{token}/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
Route::get('/booking/{token}/reject', [BookingController::class, 'reject'])->name('booking.reject');
Route::post('/booking/{appointment}/accept', [BookingController::class, 'acceptReservation'])->name('booking.accept'); // Admin accept
Route::get('/booking/{appointment}/accept-signed', [BookingController::class, 'acceptReservationSigned'])->name('booking.accept.signed')->middleware('signed');

Route::get('/downloads/{token}', [App\Http\Controllers\DownloadsController::class, 'index'])->name('downloads.index');

Route::middleware([
    'auth',
    'verified',
])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});



// Workshop Routes
Route::get('/workshops', [App\Http\Controllers\WorkshopController::class, 'index'])->name('workshops.index');
Route::post('/workshops/meeting', [App\Http\Controllers\WorkshopController::class, 'requestMeeting'])->name('workshops.meeting');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{slug}/comment', [BlogController::class, 'comment'])->name('blog.comment');
Route::post('/blog/{slug}/like', [BlogController::class, 'like'])->name('blog.like');
Route::post('/blog/{slug}/save', [BlogController::class, 'save'])->name('blog.save')->middleware('auth');
Route::post('/comments/{comment}/like', [BlogController::class, 'likeComment'])->name('comments.like');

// Provider Portal
Route::middleware(['auth', 'verified'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/services', [\App\Http\Controllers\Provider\ProviderPortalController::class, 'index'])->name('services.index');
    Route::get('/services/{providerService}/edit', [\App\Http\Controllers\Provider\ProviderPortalController::class, 'edit'])->name('services.edit');
    Route::put('/services/{providerService}', [\App\Http\Controllers\Provider\ProviderPortalController::class, 'update'])->name('services.update');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login/attempt', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login.attempt');
    Route::post('/login/verify', [\App\Http\Controllers\Admin\AuthController::class, 'verifyTwoFactor'])->name('admin.login.verify');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('admin.dashboard');

        Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

        // Zone Editor
        Route::get('/zones', [\App\Http\Controllers\Admin\ZoneController::class, 'index'])->name('admin.zones.index');
        Route::post('/zones', [\App\Http\Controllers\Admin\ZoneController::class, 'store'])->name('admin.zones.store');
        Route::put('/zones/{zone}', [\App\Http\Controllers\Admin\ZoneController::class, 'update'])->name('admin.zones.update');
        Route::delete('/zones/{zone}', [\App\Http\Controllers\Admin\ZoneController::class, 'destroy'])->name('admin.zones.destroy');

        // Profile
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::post('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');

        // Providers Management
        Route::resource('/providers', \App\Http\Controllers\Admin\ProviderController::class)->names('admin.providers');

        // Vehicles (Fleet)
        Route::post('/vehicles', [\App\Http\Controllers\Admin\VehicleController::class, 'store'])->name('admin.vehicles.store');
        Route::put('/vehicles/{vehicle}', [\App\Http\Controllers\Admin\VehicleController::class, 'update'])->name('admin.vehicles.update');
        Route::delete('/vehicles/{vehicle}', [\App\Http\Controllers\Admin\VehicleController::class, 'destroy'])->name('admin.vehicles.destroy');

        // Provider Services (Price Lists)
        Route::post('/provider-services', [\App\Http\Controllers\Admin\ProviderServiceController::class, 'store'])->name('admin.provider-services.store');
        Route::put('/provider-services/{providerService}', [\App\Http\Controllers\Admin\ProviderServiceController::class, 'update'])->name('admin.provider-services.update');
        Route::delete('/provider-services/{providerService}', [\App\Http\Controllers\Admin\ProviderServiceController::class, 'destroy'])->name('admin.provider-services.destroy');

        // Reservations Operation
        Route::get('/reservations', [\App\Http\Controllers\Admin\ReservationController::class, 'index'])->name('admin.reservations.index');
        Route::post('/reservations/{item}/assign', [\App\Http\Controllers\Admin\ReservationController::class, 'assignProvider'])->name('admin.reservations.assign');
        Route::post('/operator/service/{item}/status', [\App\Http\Controllers\ServiceStatusController::class, 'updateItemStatus'])->name('operator.update-status');
        Route::post('/operator/availability', [\App\Http\Controllers\ServiceStatusController::class, 'toggleAvailability'])->name('operator.toggle-availability');
        Route::post('/reservations/{item}/cancel-vendor', [\App\Http\Controllers\Admin\ReservationController::class, 'cancelProvider'])->name('admin.reservations.cancel_vendor');

        // Organization Management (Root)
        Route::middleware(['role:root'])->group(function () {
            Route::get('/organizations', [\App\Http\Controllers\Admin\OrganizationController::class, 'index'])->name('admin.organizations.index');
            Route::post('/organizations', [\App\Http\Controllers\Admin\OrganizationController::class, 'store'])->name('admin.organizations.store');
            Route::put('/organizations/{organization}', [\App\Http\Controllers\Admin\OrganizationController::class, 'update'])->name('admin.organizations.update');
            Route::delete('/organizations/{organization}', [\App\Http\Controllers\Admin\OrganizationController::class, 'destroy'])->name('admin.organizations.destroy');
        });

        // Stripe Connect
        Route::get('/stripe/connect', [\App\Http\Controllers\Admin\StripeConnectController::class, 'connect'])->name('admin.stripe.connect');
        Route::get('/stripe/return', [\App\Http\Controllers\Admin\StripeConnectController::class, 'handleReturn'])->name('admin.stripe.return');

        // Change Requests (Provider Approvals)
        Route::get('/change-requests', [\App\Http\Controllers\Admin\ChangeRequestController::class, 'index'])->name('admin.change-requests.index');
        Route::get('/change-requests/{changeRequest}', [\App\Http\Controllers\Admin\ChangeRequestController::class, 'show'])->name('admin.change-requests.show');
        Route::post('/change-requests/{changeRequest}/approve', [\App\Http\Controllers\Admin\ChangeRequestController::class, 'approve'])->name('admin.change-requests.approve');
        Route::post('/change-requests/{changeRequest}/reject', [\App\Http\Controllers\Admin\ChangeRequestController::class, 'reject'])->name('admin.change-requests.reject');

        // Activity Logs
        Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActionLogController::class, 'index'])->name('admin.activity-logs.index');

        // Review Management
        Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('admin.reviews.approve');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    });
});

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');

// Scheduling Routes
Route::get('/scheduling', [App\Http\Controllers\SchedulingController::class, 'index'])->name('scheduling.index');
Route::post('/scheduling', [App\Http\Controllers\SchedulingController::class, 'store'])->name('scheduling.store');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/details', [CheckoutController::class, 'details'])->name('checkout.details');
Route::post('/checkout/details', [CheckoutController::class, 'storeDetails'])->name('checkout.store_details');
Route::get('/checkout/{reservation}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/checkout/{reservation}/pay', [CheckoutController::class, 'processPayment'])->name('checkout.process_payment');
Route::get('/checkout/{bookingRef}/success', [CheckoutController::class, 'stripeSuccess'])->name('checkout.stripe.success');
Route::get('/checkout/pending/{reservation}', [CheckoutController::class, 'pending'])->name('checkout.pending');
// Admin Confirmation Route (Protected in real app, using GET for email link simplicity)
Route::get('/admin/reservations/{reservation}/confirm', [CheckoutController::class, 'confirmPayment'])->name('admin.reservations.confirm');

// Vendor Confirmation
Route::get('/vendor/confirm/{token}', [App\Http\Controllers\VendorController::class, 'confirm'])->name('vendor.confirm');


Route::get('/debug-data', function () {
    return ['services' => \App\Models\Service::all()];
});

Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

// SaaS Onboarding Routes
Route::get('/partner/register', [App\Http\Controllers\OnboardingController::class, 'showRegistrationForm'])->name('partner.register');
Route::post('/partner/register', [App\Http\Controllers\OnboardingController::class, 'storeOrganization'])->name('partner.store');

Route::get('/debug-db', function () {
    return [
        'connection' => \Illuminate\Support\Facades\DB::getDefaultConnection(),
        'database' => \Illuminate\Support\Facades\DB::connection()->getDatabaseName(),
        'host' => \Illuminate\Support\Facades\DB::getConfig('host'),
    ];
});

Route::get('/partner/payment/{organization}', [App\Http\Controllers\OnboardingController::class, 'showPayment'])->name('partner.payment.show');
Route::post('/partner/payment/{organization}/stripe', [App\Http\Controllers\OnboardingController::class, 'initiateStripePayment'])->name('partner.payment.stripe');
Route::get('/partner/payment/{organization}/success', [App\Http\Controllers\OnboardingController::class, 'handlePaymentSuccess'])->name('partner.payment.success');

Route::get('/partner/setup/{organization}', [App\Http\Controllers\OnboardingController::class, 'showSetupForm'])->name('partner.setup.show');

// Review Routes
Route::get('/review/{token}', [App\Http\Controllers\ReviewController::class, 'show'])->name('reviews.show');
Route::post('/review/{token}', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

