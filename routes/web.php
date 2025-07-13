<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ColorController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HeelController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ProductVariantController;
use App\Http\Controllers\Web\SizeController;
use App\Http\Controllers\Web\StockAmountController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\VehicleController;
use App\Http\Controllers\Web\WarehouseController;
use App\Models\City;
use App\Models\Subdistrict;
use App\Models\Village;
use Illuminate\Http\Request;
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

    Route::prefix('colors')->group(function () {
        Route::get('/', [ColorController::class, 'index'])->name('colors.index');
        Route::post('/', [ColorController::class, 'store'])->name('colors.store');
        Route::put('/{id}', [ColorController::class, 'update'])->name('colors.update');
        Route::get('/{id}', [ColorController::class, 'show'])->name('colors.show');
        Route::delete('/{id}', [ColorController::class, 'destroy'])->name('colors.destroy');
    });

    Route::prefix('sizes')->group(function () {
        Route::get('/', [SizeController::class, 'index'])->name('sizes.index');
        Route::post('/', [SizeController::class, 'store'])->name('sizes.store');
        Route::put('/{id}', [SizeController::class, 'update'])->name('sizes.update');
        Route::get('/{id}', [SizeController::class, 'show'])->name('sizes.show');
        Route::delete('/{id}', [SizeController::class, 'destroy'])->name('sizes.destroy');
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    Route::prefix('heels')->group(function () {
        Route::get('/', [HeelController::class, 'index'])->name('heels.index');
        Route::post('/', [HeelController::class, 'store'])->name('heels.store');
        Route::put('/{id}', [HeelController::class, 'update'])->name('heels.update');
        Route::get('/{id}', [HeelController::class, 'show'])->name('heels.show');
        Route::delete('/{id}', [HeelController::class, 'destroy'])->name('heels.destroy');
    });

    Route::prefix('product-variants')->group(function () {
        Route::get('/', [ProductVariantController::class, 'index'])->name('product-variants.index');
        Route::post('/', [ProductVariantController::class, 'store'])->name('product-variants.store');
        Route::put('/{id}', [ProductVariantController::class, 'update'])->name('product-variants.update');
        Route::get('/{id}', [ProductVariantController::class, 'show'])->name('product-variants.show');
        Route::delete('/{id}', [ProductVariantController::class, 'destroy'])->name('product-variants.destroy');
    });

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('api')->group(function() {
        Route::get('provinces', [LocationController::class, 'getProvinces']);
        Route::get('cities', [LocationController::class, 'getCities']);
        Route::get('subdistricts', [LocationController::class, 'getSubdistricts']);
        Route::get('villages', [LocationController::class, 'getVillages']);
        Route::get('/warehouses/{warehouse}/stock-amounts', [StockAmountController::class, 'getWarehouseStock']);
    });

    Route::prefix('stock-amounts')->group(function () {
        Route::get('/', [StockAmountController::class, 'index'])->name('stock-amounts.index');
        Route::post('/', [StockAmountController::class, 'store'])->name('stock-amounts.store');
        Route::put('/{id}', [StockAmountController::class, 'update'])->name('stock-amounts.update');
        Route::get('/{id}', [StockAmountController::class, 'show'])->name('stock-amounts.show');
        Route::delete('/{id}', [StockAmountController::class, 'destroy'])->name('stock-amounts.destroy');
        
    });

});
