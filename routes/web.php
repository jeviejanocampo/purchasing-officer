<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory; 
use App\Models\Budget; 


Route::get('/main', function () {
    return view('main.main'); 
})->name('dashboard');

Route::get('/dashboard', function () {
    $inventoryCount = Inventory::count(); 
    $totalBudgetAllocated = Inventory::sum(DB::raw('unit_cost * pieces_per_set * stocks_per_set')); 
    return view('dashboard', compact('inventoryCount', 'totalBudgetAllocated')); 
})->name('dashboard');

Route::get('/stock-procurement', function () {
    // Fetch the count of inventory items
    $inventoryCount = Inventory::count();

    // Fetch all budgets sorted by creation date
    $budgets = Budget::orderBy('created_at', 'desc')->get();

    // Fetch all inventories sorted by creation date
    $inventories = Inventory::orderBy('created_at', 'desc')->get();

    // Fetch all product details from the products table
    $products = \App\Models\Product::all(); // Fetch all columns from the products table

    // Fetch all product IDs from the products table
    $productIds = \App\Models\Product::select('product_id', 'product_name')->get();

    $productNames = \App\Models\Budget::select('product_to_buy')
    ->where('budget_status', 'PENDING')
    ->distinct()
    ->get();


    // Pass the data to the view
    return view('addproduct', compact('inventoryCount', 'budgets', 'inventories', 'productIds', 'products', 'productNames'));
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

Route::get('/addproduct', [ProductController::class, 'addProduct'])->name('addproduct');

Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.details');

Route::post('/inventory/{id}/update-status', [InventoryController::class, 'updateStatus']);

Route::post('/product/add-restock-details', [ProductController::class, 'addRestockDetails'])->name('product.addRestockDetails');

Route::get('/product-details/{product_id}', function ($product_id) {
    // Fetch the product by ID
    $product = \App\Models\Product::find($product_id);

    // Fetch the inventory data by matching product_id in the inventory table
    $inventory = \App\Models\Inventory::where('product_id', $product_id)->first();

    // Pass both product and inventory data to the view
    return view('dashboard-components.product-details', compact('product', 'inventory'));
})->name('product.details');


Route::put('/product/update-status/{product_id}', [ProductController::class, 'updateStatus'])->name('product.updateStatus');

Route::get('/product/{productId}/inventory-data', [ProductController::class, 'fetchInventoryData']);

Route::put('/product/{product_id}/update', [ProductController::class, 'update'])->name('product.update');

Route::get('/products/update', [ProductController::class, 'getUpdatedProducts']);

Route::post('/inventory/update/{id}', [InventoryController::class, 'updateStockQuantity']);
