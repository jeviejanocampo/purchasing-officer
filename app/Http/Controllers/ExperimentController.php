<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckoutDetail;
use App\Models\Inventory;
use Illuminate\Support\Facades\Log;

class ExperimentController extends Controller
{
    public function showTab()
    {
        return view('expirement-tab');
    }

    public function updateStatus(Request $request)
    {
        // Retrieve input data from the request
        $checkoutIds = $request->input('checkout_ids'); // An array of checkout IDs, expect only one
        $status = $request->input('status');           // The status (COMPLETED or CANCELLED)
        $inventoryIds = $request->input('inventory_ids'); // An array of corresponding inventory IDs
    
        // Log received input for debugging
        Log::info('Received Checkout IDs: ' . implode(', ', $checkoutIds) . ' Status: ' . $status . ' Inventory IDs: ' . implode(', ', $inventoryIds));
    
        // Validate inputs
        if (count($checkoutIds) !== 1 || count($inventoryIds) === 0) {
            return response()->json(['success' => false, 'message' => 'Mismatch between Checkout IDs and Inventory IDs.']);
        }
    
        // Process all checkout details with the same checkout_id
        $checkoutId = $checkoutIds[0]; // Since we're updating all rows for a single checkout_id
    
        // Fetch all the checkout details for the provided checkout_id
        $checkoutDetails = CheckoutDetail::where('checkout_id', $checkoutId)->get();
    
        if ($checkoutDetails->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No matching records found for the provided Checkout ID.']);
        }
    
        foreach ($checkoutDetails as $index => $checkoutDetail) {
            // Log the update attempt
            Log::info("Attempting to update status for Checkout ID: $checkoutId with Inventory ID: $inventoryIds[$index] and status: $status");
    
            // Update the status in CheckoutDetail table for the current checkout_id
            $checkoutDetail->status = $status;
            $checkoutDetail->inventory_id = $inventoryIds[$index];  // Insert the corresponding inventory_id
            $checkoutDetail->save();
    
            Log::info("Successfully updated status for Checkout ID: $checkoutId to $status with Inventory ID: $inventoryIds[$index]");
    
            // If the status is "COMPLETED", proceed to adjust inventory stock
            if ($status === 'COMPLETED') {
                $productQuantity = $checkoutDetail->product_quantity;
    
                // Get the corresponding inventory record for the provided inventory_id
                $inventory = Inventory::find($inventoryIds[$index]);
    
                if ($inventory) {
                    // Subtract the product_quantity from stocks_per_set in the inventory
                    $inventory->stocks_per_set -= $productQuantity;
                    $inventory->save();
    
                    Log::info('Updated Inventory ID: ' . $inventoryIds[$index] . ' New Stock: ' . $inventory->stocks_per_set);
                } else {
                    // If inventory is not found for the given inventory_id
                    Log::error("Inventory not found for ID $inventoryIds[$index]");
                    return response()->json(['success' => false, 'message' => 'Inventory not found for ID ' . $inventoryIds[$index]]);
                }
            }
        }
    
        return response()->json(['success' => true, 'message' => 'Statuses updated and inventory stock adjusted successfully.']);
    }
    
    


    


    public function getCheckoutDetails($checkoutId)
    {
        // Fetch the checkout details based on the provided checkoutId
        $checkoutDetails = CheckoutDetail::where('checkout_id', $checkoutId)->get();

        // Check if we found any details for this checkoutId
        if ($checkoutDetails->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No checkout details found for the provided Checkout ID.']);
        }

        // Return the checkout details as JSON
        return response()->json(['success' => true, 'data' => $checkoutDetails]);
    }
}
