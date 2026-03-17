<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\MedicalRecordController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', fn() => redirect()->route('dashboard'));

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['permission:patient.view'])->group(function () {
        Route::resource('patients', PatientController::class);
        Route::get('patients/search/json', [PatientController::class, 'search'])->name('patients.search');
        Route::get('patients/export/excel', [PatientController::class, 'export'])->name('patients.export');
        Route::post('patients/import-excel', [PatientController::class, 'import'])->name('patients.import');
    });

    Route::middleware(['permission:medical_record.view'])->group(function () {
        Route::resource('medical-records', MedicalRecordController::class);
    });

    Route::middleware(['permission:product.view'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::get('products/export/excel', [ProductController::class, 'export'])->name('products.export');
        Route::post('products/import-excel', [ProductController::class, 'import'])->name('products.import');
    });

    Route::middleware(['permission:transaction.view'])->group(function () {
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('transactions/product/search', [TransactionController::class, 'searchProduct'])->name('transactions.product.search');
        Route::get('transactions/get-medical-records', [TransactionController::class, 'getMedicalRecords'])->name('transactions.medical-records');
        Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    });

    Route::middleware(['permission:transaction.edit'])->group(function () {
        Route::patch('transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    });

    Route::middleware(['permission:report.view'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/penjualan', [ReportController::class, 'penjualan'])->name('penjualan');
        Route::get('/produk-terlaris', [ReportController::class, 'produkTerlaris'])->name('produk-terlaris');
        Route::get('/stok', [ReportController::class, 'stok'])->name('stok');
    });

    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
