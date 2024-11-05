<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use Illuminate\Support\Facades\Log; // Import the Log facade

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

    public function show($id)
    {
        $inventory = Inventory::findOrFail($id);
        return view('details', compact('inventory'));
    }

    public function updateStatus(Request $request, $id)
    {
        Log::info("Received request to update status for inventory ID: {$id}");

        $request->validate([
            'set_status' => 'required|string|max:255',
        ]);

        $inventory = Inventory::findOrFail($id);
        $inventory->set_status = $request->set_status;
        
        if ($inventory->save()) {
            Log::info("Status updated successfully for inventory ID: {$id} to '{$request->set_status}'");
            return response()->json(['success' => true]);
        } else {
            Log::error("Failed to update status for inventory ID: {$id}");
            return response()->json(['success' => false]);
        }
    }
}
