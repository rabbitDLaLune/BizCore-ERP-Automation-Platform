<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseRequestController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    Route::get('/stock-movements', [StockMovementController::class, 'index'])
        ->middleware('permission:manage inventory')
        ->name('stock-movements.index');

    Route::resource('categories', CategoryController::class)
        ->middleware('permission:manage categories');

    Route::resource('products', ProductController::class)
        ->middleware('permission:manage products');

    Route::get('/stock-movements/create', [StockMovementController::class, 'create'])
        ->middleware('permission:manage inventory')
        ->name('stock-movements.create');

    Route::post('/stock-movements', [StockMovementController::class, 'store'])
        ->middleware('permission:manage inventory')
        ->name('stock-movements.store');

    Route::resource('customers', CustomerController::class)
        ->middleware('permission:manage customers');

    Route::resource('sales', SaleController::class)
        ->middleware('permission:manage sales');

    Route::get('/sales/{sale}/pdf', [SaleController::class, 'pdf'])
        ->middleware('permission:manage sales')
        ->name('sales.pdf');

    Route::resource('suppliers', SupplierController::class)
        ->middleware('permission:manage suppliers');

    Route::resource('purchase-requests', PurchaseRequestController::class)
        ->middleware('permission:manage purchase requests');

    Route::post('/purchase-requests/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])
        ->middleware('permission:approve purchase requests')
        ->name('purchase-requests.approve');

    Route::post('/purchase-requests/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject'])
        ->middleware('permission:approve purchase requests')
        ->name('purchase-requests.reject');

    Route::post('/purchase-requests/{purchaseRequest}/complete', [PurchaseRequestController::class, 'markCompleted'])
        ->middleware('permission:manage purchase requests')
        ->name('purchase-requests.complete');
});

require __DIR__ . '/auth.php';
