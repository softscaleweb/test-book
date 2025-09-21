<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

// Home — redirect users based on role; guests go to login
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        if ($user->hasRole('customer')) {
            return redirect()->route('search.flights');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('login');
    }

    return redirect()->route('login');
})->name('home');

// Authentication
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');


// Customer-only (authenticated + customer role)
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Flight search & details
    Route::get('/search/flights', [FlightController::class, 'index'])->name('search.flights');
    Route::get('/search/flights/{id}', [FlightController::class, 'show'])->name('search.flight.details');
    Route::post('/cart/flights/add', [FlightController::class, 'addToCart'])->name('cart.flights.add');

    // Hotel search & details
    Route::get('/search/hotels', [HotelController::class, 'index'])->name('search.hotels');
    Route::get('/search/hotels/{id}', [HotelController::class, 'show'])->name('search.hotel.details');
    Route::post('/cart/hotels/add', [HotelController::class, 'addToCart'])->name('cart.hotels.add');

    // Cart / Checkout
    Route::get('/checkout', [CartController::class, 'index'])->name('cart.summary');
    Route::post('/checkout/confirm', [CartController::class, 'confirm'])->name('cart.confirm');
});


// Authenticated (both customers and admins)
Route::middleware(['auth'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
});


// Admin area
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    });
