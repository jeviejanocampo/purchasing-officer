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

    public function addRemarks(Request $request, $budget_identifier)
{
    $request->validate([
        'remarks' => 'required|string|max:255',
    ]);

    // Find the inventory item by budget identifier and update remarks
    $inventory = Inventory::where('budget_identifier', $budget_identifier)->first();
    if ($inventory) {
        $inventory->remarks = $request->remarks;
        $inventory->save();
        
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false], 404);
}

public function fetchInventoryData()
{
    $inventories = Inventory::orderBy('created_at', 'desc')->get();
    return response()->json($inventories);
}

}
