<?php

use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\TeamController;
use App\Models\Invitation;
use App\Models\ShortUrl;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/s/{code}', RedirectController::class)->name('short-urls.redirect');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/invitations/{token}', [AcceptInvitationController::class, 'show'])->name('invitations.accept');
    Route::post('/invitations/{token}', [AcceptInvitationController::class, 'store'])->name('invitations.accept.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/short-urls', [ShortUrlController::class, 'index'])
        ->name('short-urls.index')
        ->can('viewAny', ShortUrl::class);
    Route::get('/short-urls/export', [ShortUrlController::class, 'export'])
        ->name('short-urls.export')
        ->can('viewAny', ShortUrl::class);
    Route::post('/short-urls', [ShortUrlController::class, 'store'])
        ->name('short-urls.store')
        ->can('create', ShortUrl::class);

    Route::get('/invite', [InvitationController::class, 'create'])
        ->name('invitations.create')
        ->can('create', Invitation::class);
    Route::post('/invite', [InvitationController::class, 'store'])
        ->name('invitations.store')
        ->can('create', Invitation::class);

    Route::get('/clients', [CompanyController::class, 'index'])
        ->name('clients.index')
        ->can('view-clients');
    Route::get('/team', [TeamController::class, 'index'])
        ->name('team.index')
        ->can('view-team');
});
