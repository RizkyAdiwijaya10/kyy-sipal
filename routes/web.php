<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\ItemsUnitController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\AdminLoanController;
use App\Http\Controllers\UserController;

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
    
    Route::middleware(['admin'])->group(function () {
        
        Route::resource('categories', CategoryController::class);
        Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');

        Route::resource('items', ItemsController::class);
        Route::post('items/import', [ItemsController::class, 'import'])->name('items.import');

        Route::resource('item-units', ItemsUnitController::class);
        Route::post('item-units/import', [ItemsUnitController::class, 'import'])->name('item-units.import');

        
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/loans', [AdminLoanController::class, 'index'])->name('loans.index');
            Route::get('/loans/{loan}', [AdminLoanController::class, 'show'])->name('loans.show');
            
            // Surat routes
            Route::get('/loans/{loan}/download-surat', [AdminLoanController::class, 'downloadSurat'])->name('loans.download-surat');
            Route::get('/loans/{loan}/view-surat', [AdminLoanController::class, 'viewSurat'])->name('loans.view-surat');
            
            Route::post('/loans/{loan}/approve', [AdminLoanController::class, 'approve'])->name('loans.approve');
            Route::post('/loans/{loan}/reject', [AdminLoanController::class, 'reject'])->name('loans.reject');
            Route::post('/loans/{loan}/confirm-borrowed', [AdminLoanController::class, 'confirmBorrowed'])->name('loans.confirm-borrowed');
            Route::post('/loans/{loan}/return', [AdminLoanController::class, 'returnItems'])->name('loans.return');
             Route::get('/loans/{loan}/json', [AdminLoanController::class, 'getDetailJson'])->name('loans.json');
            // Laporan
            Route::get('/reports', [AdminLoanController::class, 'reports'])->name('reports.loans');
            Route::get('/reports/print', [AdminLoanController::class, 'printReport'])->name('reports.print');

            Route::resource('users', UserController::class);

            Route::get('/return-requests', [AdminLoanController::class, 'returnRequests'])->name('loans.return-requests');
            Route::get('/return-requests/{loan}', [AdminLoanController::class, 'showReturnRequest'])->name('loans.return-request.show');
            Route::post('/return-requests/{loan}/approve', [AdminLoanController::class, 'approveReturnRequest'])->name('loans.return-request.approve');
            Route::post('/return-requests/{loan}/reject', [AdminLoanController::class, 'rejectReturnRequest'])->name('loans.return-request.reject');
            
        });
    });
    
    Route::middleware(['user'])->prefix('user')->name('user.')->group(function () {
        // Daftar Barang

        Route::get('/items', [LoansController::class, 'availableItems'])->name('items.index');
        Route::get('/items/{item}', [LoansController::class, 'showItem'])->name('items.show');
        Route::get('/loans/download-template', [LoansController::class, 'downloadTemplate'])->name('loans.download-template');      
        
        Route::get('/loans/create', [LoansController::class, 'createLoan'])->name('loans.create');
        Route::post('/loans', [LoansController::class, 'storeLoan'])->name('loans.store');
        Route::get('/loans/history', [LoansController::class, 'loanHistory'])->name('loans.history');
        Route::get('/loans/{loan}', [LoansController::class, 'showLoan'])->name('loans.show');
        Route::put('/loans/{loan}/cancel', [LoansController::class, 'cancelLoan'])->name('loans.cancel'); 
        // Route::get('/loans/{loan}/print', [LoansController::class, 'printLoan'])->name('loans.print'); 


        Route::get('/returns', [LoansController::class, 'returnIndex'])->name('returns.index');
        Route::get('/returns/{loan}/create', [LoansController::class, 'returnCreate'])->name('returns.create');
        Route::post('/returns/{loan}', [LoansController::class, 'returnStore'])->name('returns.store');
        Route::get('/returns/{loan}', [LoansController::class, 'returnShow'])->name('returns.show');
        Route::delete('/returns/{loan}', [LoansController::class, 'returnCancel'])->name('returns.cancel');
        Route::get('/loans/{loan}/json', [LoansController::class, 'getDetailJson'])->name('loans.json');
        // Route::get('/loans/return-requests', [LoansController::class, 'returnRequests'])->name('loans.return-requests');
        // Route::get('/loans/{loan}/return-request', [LoansController::class, 'createReturnRequest'])->name('loans.return-request.create');
        // Route::post('/loans/{loan}/return-request', [LoansController::class, 'storeReturnRequest'])->name('loans.return-request.store');
        // Route::get('/loans/{loan}/return-request/show', [LoansController::class, 'showReturnRequest'])->name('loans.return-request.show');
        // Route::delete('/loans/{loan}/return-request', [LoansController::class, 'cancelReturnRequest'])->name('loans.return-request.cancel');
    });
    
});

require __DIR__.'/auth.php';