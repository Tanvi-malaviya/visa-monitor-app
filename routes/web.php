<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('customer.dashboard');
    }

    return redirect()->route('login');
});

// Authentication routes
// Customer Login (Applicant Portal)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Admin Login (Sathwara InfoTech Management Portal)
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/demo-login/{type}', [AuthController::class, 'demoLogin'])->name('demo.login');

// Customer Portal routes (Read-only for applicants)
Route::middleware(['auth'])->prefix('portal')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::post('/monitoring/check', [CustomerController::class, 'runManualCheck'])->name('monitoring.check');
    Route::post('/monitoring/otp', [CustomerController::class, 'submitOtp'])->name('monitoring.otp');
    Route::post('/slots/{slot}/book', [CustomerController::class, 'bookSlot'])->name('slots.book');
    Route::post('/notifications/read-all', [CustomerController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
});

// Admin Panel routes (Admin manages all customers, preferences, and automated booking)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
    Route::post('/customers/create', [AdminController::class, 'storeCustomer'])->name('customers.store');
    Route::post('/customers/{applicant}/update', [AdminController::class, 'updateCustomerPreferences'])->name('customers.update');
    Route::post('/customers/{applicant}/autofill', [AdminController::class, 'triggerAutoFill'])->name('customers.autofill');
    Route::post('/customers/{user}/delete', [AdminController::class, 'deleteCustomer'])->name('customers.delete');
    Route::post('/jobs/{task}/toggle', [AdminController::class, 'toggleJobStatus'])->name('jobs.toggle');
    Route::post('/jobs/batch-check', [AdminController::class, 'runBatchCheck'])->name('jobs.batch-check');
    Route::post('/simulate/slot', [AdminController::class, 'simulateSlotDrop'])->name('simulate.slot');
    Route::post('/simulate/otp/{task}', [AdminController::class, 'simulateOtpChallenge'])->name('simulate.otp');
    Route::post('/engine/toggle', [AdminController::class, 'toggleEngine'])->name('engine.toggle');
});

// Public Proxy & Asset endpoints for live third-party portal embedding
Route::get('/portal-proxy', [AdminController::class, 'portalProxy'])->name('portal.proxy');
Route::get('/portal-template', [AdminController::class, 'portalTemplate'])->name('portal.template');
Route::any('/portal-asset', [AdminController::class, 'portalAsset'])->name('portal.asset');
