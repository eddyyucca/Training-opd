<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainingScanController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::get('/training-registration/{token}', [TrainingScanController::class, 'showRegistration'])->name('trainings.registration.show');
Route::post('/training-registration/{token}', [TrainingScanController::class, 'submitRegistration'])->name('trainings.registration.submit');
Route::get('/training-registration/{token}/success', [TrainingScanController::class, 'showRegistrationSuccess'])->name('trainings.registration.success');
Route::get('/training-attendance/{token}', [TrainingScanController::class, 'showAttendance'])->name('trainings.attendance.show');
Route::post('/training-attendance/{token}', [TrainingScanController::class, 'submitAttendance'])->name('trainings.attendance.submit');
Route::get('/training-attendance/{token}/success', [TrainingScanController::class, 'showAttendanceSuccess'])->name('trainings.attendance.success');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/reports/departments', [DashboardController::class, 'departmentReport'])->name('reports.departments');
    Route::get('/masters', [MasterDataController::class, 'index'])->name('masters.index');
    Route::get('/masters/{section}', [MasterDataController::class, 'section'])->name('masters.section');
    Route::post('/masters', [MasterDataController::class, 'store'])->name('masters.store');
    Route::put('/masters/{type}/{id}', [MasterDataController::class, 'update'])->name('masters.update');
    Route::delete('/masters/{type}/{id}', [MasterDataController::class, 'destroy'])->name('masters.destroy');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::resource('employees', EmployeeController::class);
    Route::get('/trainings/{training}/registration-qr', [TrainingController::class, 'registrationQr'])->name('trainings.registration.qr');
    Route::get('/trainings/{training}/attendance-qr', [TrainingController::class, 'attendanceQr'])->name('trainings.attendance.qr');
    Route::resource('trainings', TrainingController::class);
});
