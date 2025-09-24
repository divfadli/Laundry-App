<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransOrderController;
use App\Http\Controllers\TypeOfServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// Authentication
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua route harus login
Route::middleware(['auth'])->group(function () {

    // Dashboard untuk semua role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customers
    // Administrator full access
    Route::middleware(['role:Administrator'])->group(function () {
        Route::resource('customers', CustomerController::class)->except(['store']);
    });

    // Administrator + Operator: hanya create & store
    Route::middleware(['role:Administrator,Operator'])->group(function () {
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    });

    // Users & Services hanya Administrator
    Route::middleware(['role:Administrator'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('services', TypeOfServiceController::class);
    });

    // Transaksi Laundry (Admin + Operator)
    Route::middleware(['role:Administrator,Operator'])->group(function () {
        Route::resource('orders', TransOrderController::class);
        Route::get('/orders/{order}/print', [TransOrderController::class, 'print'])->name('orders.print');
        Route::patch('/orders/{order}/complete', [TransOrderController::class, 'complete'])->name('orders.complete');
        Route::get('transaction', [TransOrderController::class, 'showTransaction'])->name('orders.transaction');
        Route::post('laundry_post', [TransOrderController::class, 'OrderStore'])->name('orders.laundry_post');
        Route::get('get-all-data-orders', [TransOrderController::class, 'getAllDataOrders'])->name('orders.getAllDataOrders');
        Route::put('/orders/{id}/status', [TransOrderController::class, 'pickupLaundry'])->name('orders.pickupLaundry');
    });

    // Laporan (Pimpinan)
    Route::middleware(['role:Pimpinan'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    });
});
