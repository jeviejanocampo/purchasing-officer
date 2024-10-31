<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory; 

class InventoryController extends Controller
{
    public function countProducts()
    {
        $productCount = Inventory::count();
        return view('addproduct', compact('productCount'));
    }
}
