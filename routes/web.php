<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\TenantApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::get('/register-company', [TenantRegistrationController::class, 'create'])->name('tenants.register');
    Route::post('/register-company', [TenantRegistrationController::class, 'store'])->name('tenants.store');
});

Route::middleware(['auth', 'verified', 'tenant.active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');

    // Invitations
    Route::get('/invitations', [ProjectInvitationController::class, 'index'])->name('invitations.index');
    Route::patch('/invitations/{invitation}/accept', [ProjectInvitationController::class, 'accept'])->name('invitations.accept');
    Route::patch('/invitations/{invitation}/decline', [ProjectInvitationController::class, 'decline'])->name('invitations.decline');

    // Projects
    Route::resource('projects', ProjectController::class);

    // Tasks (nested under projects)
    Route::resource('projects.tasks', TaskController::class)->except(['index', 'show']);
    Route::patch('projects/{project}/tasks/{task}/status', [TaskController::class , 'updateStatus'])
        ->name('projects.tasks.status');
});

Route::middleware(['auth', 'verified', 'superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/tenants', [TenantApprovalController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/{tenant}', [TenantApprovalController::class, 'show'])->name('tenants.show');
        Route::patch('/tenants/{tenant}', [TenantApprovalController::class, 'update'])->name('tenants.update');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';