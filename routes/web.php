<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return view('welcome');
});

// Remove the first direct view route for /add-product
Route::get('/add-product', [ProductController::class, 'showAddProductForm'])->name('add.product');

Route::post('/budget/store', [BudgetController::class, 'store']);
Route::post('/add-product', [ProductController::class, 'addProduct'])->name('product.add');


