<?php

namespace App\Http\Controllers;

use App\Models\Inventory;  // Assuming you have an Inventory model
use App\Models\Budget;  // Import the Budget model

class DashboardController extends Controller
{
    public function showDashboard()
    {
        // Retrieve the inventory data
        $inventoryItems = Inventory::all();
        
        // Calculate the total cost per product with a 25% markup and prepare the data
        $productCosts = [];
    
        foreach ($inventoryItems as $item) {
            // Apply the 25% markup to the total cost
            $totalCost = ($item->unit_cost * $item->pieces_per_set) * 1.25;
            $productCosts[] = [
                'name' => $item->product_name,
                'cost' => $totalCost
            ];
        }

        // Retrieve and sum all the input budgets and remaining balances from the 'budget' table
        $totalBudgetAllocated = Budget::sum('input_budget'); // Sum of all input_budget values
        $totalRemainingBalance = Budget::sum('remaining_balance'); // Sum of all remaining_balance values
    
        return view('dashboard', [
            'inventoryCount' => $inventoryItems->count(),
            'totalBudgetAllocated' => $totalBudgetAllocated,
            'productCosts' => $productCosts,
            'remainingBalance' => $totalRemainingBalance,  // Display the total remaining balance
        ]);
    }
}
