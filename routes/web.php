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
    return Inertia::render('Welcome', [
        'featuredServices' => Service::where('is_active', true)->take(3)->get(),
        'latestPosts' => BlogPost::where('is_published', true)->latest('published_at')->take(3)->get(),
        'zones' => \App\Models\Zone::pluck('name'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/shop', function () {
    return Inertia::render('Shop');
});

Route::get('/services', function () {
    return Inertia::render('Services', [
        'services' => Service::where('is_active', true)
            ->whereIn('type', ['transfer', 'tour', 'private'])
            ->get()
    ]);
});

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
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'appointments' => Auth::user()->appointments()->with('service')->latest()->get()
        ]);
    })->name('dashboard');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login'); // Placeholder
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Auth/Register'); // Placeholder
})->name('register');

// Workshop Routes
Route::get('/workshops', [App\Http\Controllers\WorkshopController::class, 'index'])->name('workshops.index');
Route::post('/workshops/meeting', [App\Http\Controllers\WorkshopController::class, 'requestMeeting'])->name('workshops.meeting');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{slug}/comment', [BlogController::class, 'comment'])->name('blog.comment');
Route::post('/blog/{slug}/like', [BlogController::class, 'like'])->name('blog.like');
Route::post('/blog/{slug}/save', [BlogController::class, 'save'])->name('blog.save')->middleware('auth');
Route::post('/comments/{comment}/like', [BlogController::class, 'likeComment'])->name('comments.like');

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login/attempt', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login.attempt');
    Route::post('/login/verify', [\App\Http\Controllers\Admin\AuthController::class, 'verifyTwoFactor'])->name('admin.login.verify');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');

        Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

        // Zone Editor
        Route::get('/zones', [\App\Http\Controllers\Admin\ZoneController::class, 'index'])->name('admin.zones.index');
        Route::post('/zones', [\App\Http\Controllers\Admin\ZoneController::class, 'store'])->name('admin.zones.store');
        Route::put('/zones/{zone}', [\App\Http\Controllers\Admin\ZoneController::class, 'update'])->name('admin.zones.update');
        Route::delete('/zones/{zone}', [\App\Http\Controllers\Admin\ZoneController::class, 'destroy'])->name('admin.zones.destroy');

        // Profile
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::post('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');
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
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/debug-data', function () {
    return ['services' => \App\Models\Service::all()];
});

Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

