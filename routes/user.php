<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/onboarding', \App\Livewire\Onboarding::class)->name('onboarding.index');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'user.active', 'tenant.active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Projects
    Route::get('projects', \App\Livewire\Project\ProjectList::class)->name('projects.index');
    Route::get('projects/create', \App\Livewire\Project\CreateProject::class)->name('projects.create');
    Route::get('projects/{project}/edit', \App\Livewire\Project\EditProject::class)->name('projects.edit');
    Route::get('projects/{project}', \App\Livewire\Project\Board::class)->name('projects.show');

    // Tasks API (kept for AJAX/API compatibility)
    Route::patch('projects/{project}/tasks/{task}/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])
        ->name('projects.tasks.status');
    Route::patch('projects/{project}/tasks/{task}/move', [\App\Http\Controllers\TaskController::class, 'moveToStage'])
        ->name('projects.tasks.move');

    // Workflow Stages
    Route::post('/workflow-stages', [\App\Http\Controllers\WorkflowStageController::class, 'store'])->name('workflow-stages.store');
    Route::delete('/workflow-stages/{workflowStage}', [\App\Http\Controllers\WorkflowStageController::class, 'destroy'])->name('workflow-stages.destroy');
    Route::patch('/workflow-stages/reorder', [\App\Http\Controllers\WorkflowStageController::class, 'reorder'])->name('workflow-stages.reorder');
});
