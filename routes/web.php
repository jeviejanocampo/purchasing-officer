<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Models\Inventory; 
use App\Models\Budget; 


Route::get('/main', function () {
    return view('main.main'); 
})->name('dashboard');

Route::get('/dashboard', function () {
    $inventoryCount = Inventory::count(); 
    return view('dashboard', compact('inventoryCount')); 
})->name('dashboard');

Route::get('/stock-procurement', function () {
    $inventoryCount = Inventory::count(); 
    $budgets = Budget::orderBy('created_at', 'desc')->get();
    $inventories = Inventory::orderBy('created_at', 'desc')->get();

    return view('addproduct', compact('inventoryCount', 'budgets', 'inventories')); 
})->name('calculation');


Route::post('/budget/store', [BudgetController::class, 'store'])->name('budget.store');

Route::get('/budget/allocation', [BudgetController::class, 'index'])->name('budget.allocation');

Route::post('/product/store', [ProductController::class, 'store']);

Route::post('/product/check-budget', [ProductController::class, 'checkBudgetIdentifier'])->name('product.checkBudget');
