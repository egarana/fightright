<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberMembershipController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Membership Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')
        ->name('users.')
        ->controller(UserController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{user}/edit', 'edit')->name('edit');
            Route::put('{user}', 'update')->name('update');
            Route::delete('{user}', 'destroy')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Members
    |--------------------------------------------------------------------------
    */
    Route::prefix('members')
        ->name('members.')
        ->controller(MemberController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{member}', 'show')->name('show');
            Route::get('{member}/edit', 'edit')->name('edit');
            Route::put('{member}', 'update')->name('update');
            Route::delete('{member}', 'destroy')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Memberships
    |--------------------------------------------------------------------------
    */
    Route::prefix('memberships')
        ->name('memberships.')
        ->controller(MembershipController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{membership}/edit', 'edit')->name('edit');
            Route::put('{membership}', 'update')->name('update');
            Route::delete('{membership}', 'destroy')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Member Memberships
    |--------------------------------------------------------------------------
    */
    Route::prefix('member-memberships')
        ->name('member-memberships.')
        ->controller(MemberMembershipController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{member_membership}', 'show')->name('show');
            Route::post('{member_membership}/cancel', 'cancel')->name('cancel');
            Route::delete('{member_membership}', 'destroy')->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Attendances
    |--------------------------------------------------------------------------
    */
    Route::prefix('attendances')
        ->name('attendances.')
        ->controller(AttendanceController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('today', 'today')->name('today');
            Route::get('check-in', 'showCheckIn')->name('check-in.show');
            Route::post('check-in', 'checkIn')->name('check-in');
            Route::post('{attendance}/check-out', 'checkOut')->name('check-out');
            Route::delete('{attendance}', 'destroy')->name('destroy');
        });
});

require __DIR__ . '/settings.php';
