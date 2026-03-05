<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\SumberDanaController;
use App\Http\Controllers\ItemsUnitController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\AdminLoanController;
use App\Http\Controllers\UserLoanController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard (untuk semua role)
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Profile (untuk semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ========== ROUTES UNTUK ADMIN ==========
    Route::middleware(['admin'])->group(function () {
        
        // Master Data (CRUD)
        Route::resource('categories', CategoryController::class);
        Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');

        Route::resource('sumber-dana', SumberDanaController::class);
        Route::post('sumber-dana/import', [SumberDanaController::class, 'import'])->name('sumber-dana.import');

        Route::resource('items', ItemsController::class);
        Route::post('items/import', [ItemsController::class, 'import'])->name('items.import');

        Route::resource('item-units', ItemsUnitController::class);
        Route::post('item-units/import', [ItemsUnitController::class, 'import'])->name('item-units.import');

        
        // Manajemen Peminjaman (Admin)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/loans', [AdminLoanController::class, 'index'])->name('loans.index');
            Route::get('/loans/{loan}', [AdminLoanController::class, 'show'])->name('loans.show');
            Route::post('/loans/{loan}/approve', [AdminLoanController::class, 'approve'])->name('loans.approve');
            Route::post('/loans/{loan}/reject', [AdminLoanController::class, 'reject'])->name('loans.reject');
            Route::post('/loans/{loan}/confirm-borrowed', [AdminLoanController::class, 'confirmBorrowed'])->name('loans.confirm-borrowed');
            Route::post('/loans/{loan}/return', [AdminLoanController::class, 'returnItems'])->name('loans.return');
            
            // Laporan
            Route::get('/reports/loans', [AdminLoanController::class, 'reports'])->name('reports.loans');
        });
    });
    
    // ========== ROUTES UNTUK USER ==========
    Route::middleware(['user'])->prefix('user')->name('user.')->group(function () {
        Route::get('/loans/create', [LoansController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoansController::class, 'store'])->name('loans.store');

        // Daftar Alat
        Route::get('/items', [UserLoanController::class, 'availableItems'])->name('items.index');
        Route::get('/items/{item}', [UserLoanController::class, 'showItem'])->name('items.show');
        
        // // Peminjaman
        // Route::get('/loans/create', [UserLoanController::class, 'createLoan'])->name('loans.create');
        // Route::post('/loans', [UserLoanController::class, 'storeLoan'])->name('loans.store');
        Route::get('/loans/history', [UserLoanController::class, 'loanHistory'])->name('loans.history');
        Route::get('/loans/{loan}', [UserLoanController::class, 'showLoan'])->name('loans.show');
        Route::put('/loans/{loan}/cancel', [UserLoanController::class, 'cancelLoan'])->name('loans.cancel');
    });
    
});

require __DIR__.'/auth.php';