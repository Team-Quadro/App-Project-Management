<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TenantJoinRequestController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\WorkflowStageController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\TenantApprovalController;
use App\Http\Controllers\SuperAdmin\TenantManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tenants/register', [TenantRegistrationController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantRegistrationController::class, 'store'])->name('tenants.store');
    Route::post('/tenants/join', [TenantJoinRequestController::class, 'store'])->name('tenants.join');

    Route::get('/company/users', [CompanyUserController::class, 'index'])->name('company.users.index');
    Route::patch('/company/users/{joinRequest}/approve', [CompanyUserController::class, 'approve'])->name('company.users.approve');
    Route::patch('/company/users/{joinRequest}/reject', [CompanyUserController::class, 'reject'])->name('company.users.reject');
});

Route::middleware(['auth', 'verified', 'user.active', 'tenant.active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // Projects
    Route::resource('projects', ProjectController::class);

    // Tasks (nested under projects)
    Route::resource('projects.tasks', TaskController::class)->except(['index', 'show']);
    Route::patch('projects/{project}/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('projects.tasks.status');
    Route::patch('projects/{project}/tasks/{task}/move', [TaskController::class, 'moveToStage'])
        ->name('projects.tasks.move');

    // Workflow Stages (global per-tenant sections)
    Route::post('/workflow-stages', [WorkflowStageController::class, 'store'])->name('workflow-stages.store');
    Route::delete('/workflow-stages/{workflowStage}', [WorkflowStageController::class, 'destroy'])->name('workflow-stages.destroy');
    Route::patch('/workflow-stages/reorder', [WorkflowStageController::class, 'reorder'])->name('workflow-stages.reorder');
});

Route::middleware(['auth', 'verified', 'superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/switch-tenant', [SuperAdminDashboardController::class, 'switchTenant'])->name('switch-tenant');

        Route::get('/tenants', [TenantApprovalController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [TenantManagementController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [TenantManagementController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}/edit', [TenantManagementController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [TenantManagementController::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{tenant}', [TenantManagementController::class, 'destroy'])->name('tenants.destroy');
        Route::get('/tenants/{tenant}', [TenantApprovalController::class, 'show'])->name('tenants.show');
        Route::patch('/tenants/{tenant}/status', [TenantApprovalController::class, 'update'])->name('tenants.status');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';