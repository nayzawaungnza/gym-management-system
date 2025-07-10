<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Guest routes (not authenticated)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    
    // Registration
    Route::get('/register', [App\Http\Controllers\Auth\AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
    
    // Password Reset
    Route::get('/forgot-password', [App\Http\Controllers\Auth\AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated routes
Route::middleware(['auth', 'check_user_active'])->group(function () {
    // Logout
    Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');
    
    // Email Verification
    Route::get('/email/verify', [App\Http\Controllers\Auth\AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [App\Http\Controllers\Auth\AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Backend\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Members Management
        Route::resource('members', App\Http\Controllers\Backend\MemberController::class);
        Route::post('members/{member}/change-status', [App\Http\Controllers\Backend\MemberController::class, 'changeStatus'])->name('members.change-status');
         Route::post('members/calculate-end-date', [App\Http\Controllers\Backend\MemberController::class, 'calculateEndDate'])->name('members.calculate-end-date');
         
        // Trainers Management
        Route::resource('trainers', App\Http\Controllers\Backend\TrainerController::class);
        Route::post('trainers/{trainer}/change-status', [App\Http\Controllers\Backend\TrainerController::class, 'changeStatus'])->name('trainers.change-status');
        
        // Classes Management
        Route::resource('classes', App\Http\Controllers\Backend\ClassController::class);
        Route::post('classes/{class}/cancel', [App\Http\Controllers\Backend\ClassController::class, 'cancel'])->name('classes.cancel');
        
        // Payments Management
        Route::resource('payments', App\Http\Controllers\Backend\PaymentController::class);
        Route::get('payments/{payment}/receipt', [App\Http\Controllers\Backend\PaymentController::class, 'receipt'])->name('payments.receipt');
        
        // Equipment Management
        Route::resource('equipment', App\Http\Controllers\Backend\EquipmentController::class);
        Route::post('equipment/{equipment}/maintenance', [App\Http\Controllers\Backend\EquipmentController::class, 'scheduleMaintenance'])->name('equipment.maintenance');
        
        // Attendance Management
        Route::get('attendance', [App\Http\Controllers\Backend\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance/check-in', [App\Http\Controllers\Backend\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('attendance/check-out', [App\Http\Controllers\Backend\AttendanceController::class, 'checkOut'])->name('attendance.check-out');
        Route::get('attendance/verify/{token}', [App\Http\Controllers\Backend\AttendanceController::class, 'verifyQR'])->name('attendance.verify');
        
        // Activity Logs
        Route::get('activity-logs', [App\Http\Controllers\Backend\ActivityLogController::class, 'index'])->name('activity_logs.index');
        Route::get('activity-logs/{activityLog}', [App\Http\Controllers\Backend\ActivityLogController::class, 'show'])->name('activity_logs.show');
        Route::get('activity-logs-export', [App\Http\Controllers\Backend\ActivityLogController::class, 'export'])->name('activity_logs.export');
        Route::post('activity-logs-cleanup', [App\Http\Controllers\Backend\ActivityLogController::class, 'cleanup'])->name('activity_logs.cleanup');
        
        // Roles & Permissions
        Route::resource('roles', App\Http\Controllers\Backend\RoleController::class);
        Route::post('roles/{role}/assign-users', [App\Http\Controllers\Backend\RoleController::class, 'assignUsers'])->name('roles.assign-users');
        Route::post('roles/{role}/remove-user', [App\Http\Controllers\Backend\RoleController::class, 'removeUser'])->name('roles.remove-user');
        
        // Reports
        Route::get('reports/membership', [App\Http\Controllers\Backend\MemberController::class, 'membershipReport'])->name('reports.membership');
        Route::get('reports/financial', [App\Http\Controllers\Backend\MemberController::class, 'financialReport'])->name('reports.financial');
        
        // Export routes
        Route::prefix('exports')->name('exports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Backend\Admin\AdminExportController::class, 'index'])->name('index');
            Route::post('members', [App\Http\Controllers\Backend\Admin\AdminExportController::class, 'exportMembers'])->name('members');
            Route::post('attendance', [App\Http\Controllers\Backend\Admin\AdminExportController::class, 'exportAttendance'])->name('attendance');
            Route::post('trainers', [App\Http\Controllers\Backend\Admin\AdminExportController::class, 'exportTrainers'])->name('trainers');
            Route::post('payments', [App\Http\Controllers\Backend\Admin\AdminExportController::class, 'exportPayments'])->name('payments');
            Route::post('quick', [App\Http\Controllers\Backend\Admin\AdminExportController::class, 'quickExport'])->name('quick');
        });
        
        // Member specific exports
        Route::post('members/{member}/export-attendance', [App\Http\Controllers\Backend\MemberController::class, 'exportAttendance'])->name('members.export-attendance');
        Route::post('members/bulk-export', [App\Http\Controllers\Backend\MemberController::class, 'bulkExport'])->name('members.bulk-export');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Trainer Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:Trainer'])->prefix('trainer')->name('trainer.')->group(function () {
        // Trainer Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Backend\Trainer\TrainerDashboardController::class, 'index'])->name('dashboard');
        
        // Schedule Management
        Route::get('schedule', [App\Http\Controllers\Backend\Trainer\TrainerDashboardController::class, 'schedule'])->name('schedule');
        Route::get('classes', [App\Http\Controllers\Backend\Trainer\TrainerDashboardController::class, 'classes'])->name('classes');
        Route::post('classes/{class}/update', [App\Http\Controllers\Backend\Trainer\TrainerDashboardController::class, 'updateClass'])->name('classes.update');
        
        // Attendance Management
        Route::get('attendance', [App\Http\Controllers\Backend\Trainer\TrainerDashboardController::class, 'attendance'])->name('attendance');
        Route::post('attendance/mark', [App\Http\Controllers\Backend\Trainer\TrainerDashboardController::class, 'markAttendance'])->name('attendance.mark');
        Route::post('attendance/check-in', [App\Http\Controllers\Backend\Trainer\TrainerDashboardController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('attendance/check-out', [App\Http\Controllers\Backend\Trainer\TrainerDashboardController::class, 'checkOut'])->name('attendance.check-out');
        
        // Export routes
        Route::prefix('exports')->name('exports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Backend\Trainer\TrainerExportController::class, 'index'])->name('index');
            Route::post('attendance', [App\Http\Controllers\Backend\Trainer\TrainerExportController::class, 'exportAttendance'])->name('attendance');
            Route::post('classes', [App\Http\Controllers\Backend\Trainer\TrainerExportController::class, 'exportClasses'])->name('classes');
        });
    });
    
    /*
    |--------------------------------------------------------------------------
    | Member Routes (Default Dashboard)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:Member'])->group(function () {
        // Member Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'index'])->name('dashboard');
        
        // Member Profile
        Route::prefix('member')->name('member.')->group(function () {
            Route::get('profile', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'profile'])->name('profile');
            Route::post('profile/update', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'updateProfile'])->name('profile.update');
            
            // Schedule and Classes
            Route::get('schedule', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'schedule'])->name('schedule');
            Route::post('classes/{class}/register', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'registerClass'])->name('classes.register');
            Route::post('classes/{registration}/cancel', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'cancelRegistration'])->name('classes.cancel');
            
            // Payments
            Route::get('payments', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'payments'])->name('payments');
            Route::post('payments/make', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'makePayment'])->name('payments.make');
            
            // Attendance
            Route::get('attendance', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'attendance'])->name('attendance');
            Route::post('attendance/check-in', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'checkIn'])->name('attendance.check-in');
            Route::post('attendance/check-out', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'checkOut'])->name('attendance.check-out');
            
            // QR Code Check-in
            Route::get('qr-checkin', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'qrCheckin'])->name('qr-checkin');
            Route::post('qr-verify', [App\Http\Controllers\Backend\Member\MemberDashboardController::class, 'verifyQR'])->name('qr-verify');
        });
    });
});
?>