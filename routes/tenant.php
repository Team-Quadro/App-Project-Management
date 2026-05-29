<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tenants/register', [\App\Http\Controllers\TenantRegistrationController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [\App\Http\Controllers\TenantRegistrationController::class, 'store'])->name('tenants.store');
    Route::post('/tenants/join', [\App\Http\Controllers\TenantJoinRequestController::class, 'store'])->name('tenants.join');
    Route::get('/active-tasks', [\App\Http\Controllers\ActiveTaskController::class, 'index'])->name('tasks.active');
    Route::get('/company/users', \App\Livewire\Company\UserManager::class)->name('company.users.index');
});
