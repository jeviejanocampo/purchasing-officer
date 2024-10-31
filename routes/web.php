<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Models\Inventory; // Import Inventory model to access count

// Route for the main page
Route::get('/', function () {
    $inventoryCount = Inventory::count(); // Count the rows in the Inventory table
    return view('main.main', compact('inventoryCount')); // Pass the inventory count to the view
})->name('main');

Route::get('/add-product', function () {
    return view('addproduct'); // Return the addproduct view directly
})->name('product.add');

// Route for the dashboard
Route::get('/dashboard', function () {
    $inventoryCount = Inventory::count(); // Count the rows in the Inventory table
    return view('dashboard', compact('inventoryCount')); // Pass the inventory count to the dashboard view
})->name('dashboard');

// Route for calculation (show add product view)
Route::get('/calculation', function () {
    $inventoryCount = Inventory::count(); // Count the rows in the Inventory table
    return view('addproduct', compact('inventoryCount')); // Pass the inventory count to the add product view
})->name('calculation');