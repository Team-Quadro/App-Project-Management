<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', \App\Livewire\SuperAdmin\Dashboard::class)->name('dashboard');
        Route::get('/tenants', \App\Livewire\SuperAdmin\TenantManager::class)->name('tenants.index');
        Route::get('/users', \App\Livewire\SuperAdmin\UserManager::class)->name('users.index');
    });
