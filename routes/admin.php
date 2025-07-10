<?php

use App\Http\Controllers\Backend\ActivityLogController;
use App\Http\Controllers\Backend\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->group(function () {
    
    // Dashboard
    // Activity Logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity_logs.show');
    Route::get('activity-logs-export', [ActivityLogController::class, 'export'])->name('activity_logs.export');
    Route::post('activity-logs-cleanup', [ActivityLogController::class, 'cleanup'])->name('activity_logs.cleanup');
    
    // Roles & Permissions
    Route::resource('roles', RoleController::class);
    Route::post('roles/{role}/assign-users', [RoleController::class, 'assignUsers'])->name('roles.assign-users');
    Route::post('roles/{role}/remove-user', [RoleController::class, 'removeUser'])->name('roles.remove-user');
});