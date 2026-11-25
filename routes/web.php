<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::pattern('transaction', '[0-9]+');
Route::pattern('category', '[0-9]+');
Route::pattern('budget', '[0-9]+');

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [InsightsController::class, 'dashboard'])->name('dashboard');
    Route::get('reports', [InsightsController::class, 'reports'])->name('reports');
    Route::resource('transactions', TransactionController::class)->except('show');
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('budgets', BudgetController::class)->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__.'/settings.php';
