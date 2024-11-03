<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogController;
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

Route::view('/po-login', 'po-login.po-login')->name('login.view');

Route::get('/admin-login', function () {
    return view('admin.main.po-main');
})->name('admin-login');


Route::match(['get', 'post'], '/register', [AuthController::class, 'register'])->name('register');

Route::post('/pin-login', [AuthController::class, 'pinLogin'])->name('pin.login');

Route::get('/budgets/{id}', [BudgetController::class, 'showBudgetDetails']);

Route::get('/po-logs', [LogController::class, 'index'])->name('po.logs');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/inventory/{budget_identifier}/remarks', [InventoryController::class, 'addRemarks']);

Route::get('/inventory/data', [InventoryController::class, 'fetchInventoryData'])->name('inventory.data');
