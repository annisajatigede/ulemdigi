<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MusicController as AdminMusicController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TemplateController as AdminTemplateController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvitationEditorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicInvitationController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

// 1. Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Public Digital Invitation Routes
Route::get('/u/{slug}', [PublicInvitationController::class, 'show'])->name('invitation.show');
Route::get('/u/{slug}/{token}', [PublicInvitationController::class, 'showGuest'])->name('invitation.guest');
Route::post('/u/{slug}/rsvp', [PublicInvitationController::class, 'submitRsvp'])->name('invitation.rsvp');

// 3. Payment Webhooks & Testing Simulators (Exempted from CSRF)
Route::post('/payment/midtrans/webhook', [CheckoutController::class, 'midtransWebhook'])->name('payment.midtrans.webhook');
Route::post('/payment/simulator/callback', [CheckoutController::class, 'simulatorCallback'])->name('payment.simulator.callback');

// 4. Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Checkout Flow
    Route::get('/checkout/select', [CheckoutController::class, 'select'])->name('checkout.select');
    Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('/checkout/pay/{transaction}', [CheckoutController::class, 'payPage'])->name('checkout.pay.page');
    Route::get('/checkout/success/{transaction}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Invitation Editor
    Route::get('/editor/{invitation}', [InvitationEditorController::class, 'edit'])->name('editor.edit');
    Route::put('/editor/{invitation}', [InvitationEditorController::class, 'update'])->name('editor.update');
    Route::post('/editor/{invitation}/toggle-publish', [InvitationEditorController::class, 'togglePublish'])->name('editor.toggle-publish');
    Route::get('/editor/{invitation}/preview', [InvitationEditorController::class, 'livePreview'])->name('editor.preview');

    // Guests & RSVP Management
    Route::get('/invitations/{invitation}/guests', [GuestController::class, 'index'])->name('guests.index');
    Route::post('/invitations/{invitation}/guests', [GuestController::class, 'store'])->name('guests.store');
    Route::put('/invitations/{invitation}/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
    Route::post('/invitations/{invitation}/guests/{guest}/checkin', [GuestController::class, 'checkIn'])->name('guests.checkin');
    Route::delete('/invitations/{invitation}/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
    Route::get('/invitations/{invitation}/guests/export', [GuestController::class, 'exportCsv'])->name('guests.export');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 5. Admin Panel Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // CRUD Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // CRUD Templates
        Route::get('/templates', [AdminTemplateController::class, 'index'])->name('templates.index');
        Route::post('/templates', [AdminTemplateController::class, 'store'])->name('templates.store');
        Route::put('/templates/{template}', [AdminTemplateController::class, 'update'])->name('templates.update');
        Route::delete('/templates/{template}', [AdminTemplateController::class, 'destroy'])->name('templates.destroy');

        // CRUD Musics Library (Admin Preset Musics)
        Route::get('/musics', [AdminMusicController::class, 'index'])->name('musics.index');
        Route::post('/musics', [AdminMusicController::class, 'store'])->name('musics.store');
        Route::post('/musics/{music}/toggle', [AdminMusicController::class, 'toggle'])->name('musics.toggle');
        Route::delete('/musics/{music}', [AdminMusicController::class, 'destroy'])->name('musics.destroy');

        // CRUD Plans
        Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
        Route::put('/plans/{plan}', [AdminPlanController::class, 'update'])->name('plans.update');

        // Transactions Management
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/export', [AdminTransactionController::class, 'exportCsv'])->name('transactions.export');

        // Site Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';
