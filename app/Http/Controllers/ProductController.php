<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Budget; // Import the Budget model
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'budget_identifier' => 'required|exists:budget,id', // Ensure 'budgets' matches your table name
            'product_name' => 'required|string|max:255',
            'unit_cost' => 'required|numeric|min:0',
            'pieces_per_set' => 'required|integer|min:1',
            'stocks_per_set' => 'required|integer|min:1',
            'expiration_date' => 'required|date|after:today',
        ]);
    
        // Calculate total cost
        $unitCost = $request->unit_cost;
        $piecesPerSet = $request->pieces_per_set;
        $stocksPerSet = $request->stocks_per_set;
    
        $totalCost = $unitCost * $piecesPerSet * $stocksPerSet;
    
        // Find the budget
        $budget = Budget::find($request->budget_identifier);
    
        // Check if the total cost exceeds the available input budget
        if ($totalCost > $budget->input_budget) {
            return response()->json([
                'success' => false,
                'message' => 'Total cost exceeds available budget.',
            ], 400); // 400 Bad Request
        }
    
        // Create a new inventory item
        Inventory::create([
            'budget_identifier' => $request->budget_identifier,
            'product_name' => $request->product_name,
            'unit_cost' => $request->unit_cost,
            'pieces_per_set' => $request->pieces_per_set,
            'stocks_per_set' => $request->stocks_per_set,
            'exp_date' => $request->expiration_date,
        ]);
    
        // Update remaining balance directly to reflect the new cost
        $budget->remaining_balance = $budget->input_budget - $totalCost; // Set the remaining balance
        $budget->save();
    
        // Return a JSON response with success message
        return response()->json([
            'success' => true,
            'message' => 'Product added and budget updated successfully.',
            'data' => [
                'input_budget' => number_format($budget->input_budget, 2), // Format for currency display
                'remaining_balance' => number_format($budget->remaining_balance, 2),
            ],
        ]);
    }

    public function create()
    {
        // Retrieve all budgets
        $budgets = Budget::all();
        return view('addproduct', compact('budgets'));
    }

    public function showInventory()
    {
        // Retrieve all inventories
        $inventories = Inventory::all();

        return view('addproduct', compact('inventories')); // Make sure the view name matches your actual view file
    }

    public function checkBudgetIdentifier(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'budget_identifier' => 'required|exists:budget,id',
        ]);

        // Check if the budget identifier is already used in the inventory
        $isUsed = Inventory::where('budget_identifier', $request->budget_identifier)->exists();

        return response()->json(['is_used' => $isUsed]);
    }
}
