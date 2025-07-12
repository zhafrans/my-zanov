<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\VehicleController;
use App\Http\Controllers\Web\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'getProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::prefix('warehouses')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::post('/', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::put('/{id}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::get('/{id}', [WarehouseController::class, 'show'])->name('warehouses.show');
        Route::delete('/{id}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');
    });

     Route::prefix('vehicles')->group(function () {
        Route::get('/', [VehicleController::class, 'index'])->name('vehicles.index');
        Route::post('/', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::put('/{id}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::get('/{id}', [VehicleController::class, 'show'])->name('vehicles.show');
        Route::delete('/{id}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
    });
});
