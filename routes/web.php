<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImpersonationController;
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
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::prefix('dashboard')
        ->name('dashboard.')
        ->controller(DashboardController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
        });

    /*
    |--------------------------------------------------------------------------
    | Impersonation (Super Admin Only)
    |--------------------------------------------------------------------------
    */
    // Stop impersonating - available to anyone being impersonated (must be before wildcard route)
    Route::post('impersonate/stop', [ImpersonationController::class, 'stop'])
        ->name('impersonate.stop');

    Route::prefix('impersonate')
        ->name('impersonate.')
        ->controller(ImpersonationController::class)
        ->middleware('role_or_404:super-admin')
        ->group(function () {
            Route::post('{user}', 'start')->name('start');
        });

    /*
    |--------------------------------------------------------------------------
    | Users (Super Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')
        ->name('users.')
        ->middleware('role_or_404:super-admin')
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

            // Edit - super-admin, owner, manager only
            Route::get('{member}/edit', 'edit')
                ->middleware('can_or_404:edit_members')
                ->name('edit');
            Route::put('{member}', 'update')
                ->middleware('can_or_404:edit_members')
                ->name('update');

            // Delete - super-admin, owner only
            Route::delete('{member}', 'destroy')
                ->middleware('can_or_404:delete_members')
                ->name('destroy');

            // Member Memberships (nested resource)
            Route::prefix('{member}/memberships')
                ->name('memberships.')
                ->controller(MemberMembershipController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    // Delete - super-admin, owner, manager only
                    Route::delete('{memberMembership}', 'destroy')
                        ->middleware('can_or_404:manage_member_memberships')
                        ->name('destroy');
                });
        });

    /*
    |--------------------------------------------------------------------------
    | Memberships (View: all, CUD: super-admin & owner only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('memberships')
        ->name('memberships.')
        ->controller(MembershipController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');

            // Create/Edit/Delete - super-admin & owner only
            Route::get('create', 'create')
                ->middleware('can_or_404:manage_memberships')
                ->name('create');
            Route::post('/', 'store')
                ->middleware('can_or_404:manage_memberships')
                ->name('store');
            Route::get('{membership}/edit', 'edit')
                ->middleware('can_or_404:manage_memberships')
                ->name('edit');
            Route::put('{membership}', 'update')
                ->middleware('can_or_404:manage_memberships')
                ->name('update');
            Route::delete('{membership}', 'destroy')
                ->middleware('can_or_404:manage_memberships')
                ->name('destroy');
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

            // Cancel/Delete - super-admin, owner, manager only
            Route::post('{member_membership}/cancel', 'cancel')
                ->middleware('can_or_404:manage_member_memberships')
                ->name('cancel');
            Route::delete('{member_membership}', 'destroy')
                ->middleware('can_or_404:manage_member_memberships')
                ->name('destroy');
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
            Route::get('create', 'showCheckIn')->name('create');
            Route::post('/', 'checkIn')->name('store');
            Route::post('check-in', 'checkIn')->name('check-in');
        });
});

require __DIR__ . '/settings.php';
