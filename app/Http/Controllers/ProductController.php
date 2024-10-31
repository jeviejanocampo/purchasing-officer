<?php

namespace App\Http\Controllers;

use App\Models\Inventory; // Import the Inventory model
use Illuminate\Http\Request;
use App\Models\Budget; // Ensure this is imported at the top
use Illuminate\Support\Facades\Log; // Import the Log facade

class ProductController extends Controller
{
    // Method to show the form (if needed)
    public function create()
    {
        $budget = Budget::all(); // Get all budget to populate the select options
        return view('addproduct', compact('budget')); // Adjust this based on your view path
    }

    // Method to handle adding a product
    public function addProduct(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'budget_identifier' => 'required|exists:budget,id',
            'product_name' => 'required|string',
            'exp_date' => 'required|date',
            'unit_cost' => 'required|numeric|min:0',
            'pieces_per_set' => 'required|integer|min:1',
            'stocks_per_set' => 'required|integer|min:0',
        ]);
    
        // Check if the budget identifier has already been used
        $budgetId = $validatedData['budget_identifier'];
        $existingProduct = Inventory::where('budget_identifier', $budgetId)->first();
    
        if ($existingProduct) {
            return redirect()->back()->withErrors(['Budget identifier has already been used for a product.']);
        }
    
        // Find the budget by identifier
        $budget = Budget::findOrFail($budgetId);
    
        // Calculate the updated remaining balance
        $inputBudget = $budget->input_budget;
        $unitCost = $validatedData['unit_cost'];
        $piecesPerSet = $validatedData['pieces_per_set'];
        $stocksPerSet = $validatedData['stocks_per_set'];
    
        $totalCostForStocks = ($unitCost * $piecesPerSet) * $stocksPerSet; // Total cost for the stocks
        $updatedRemainingBalance = $inputBudget - $totalCostForStocks; // Updated remaining balance
    
        // Log the updated remaining balance
        Log::info('Updated Remaining Balance for Budget ID ' . $budget->id . ': ₱' . number_format($updatedRemainingBalance, 2));
    
        // Update the remaining balance in the budget table
        $budget->remaining_balance = $updatedRemainingBalance;
        $budget->save(); // Save changes to the budget
    
        // Insert the new product into the inventory
        Inventory::create([
            'budget_identifier' => $validatedData['budget_identifier'],
            'product_name' => $validatedData['product_name'],
            'exp_date' => $validatedData['exp_date'],
            'unit_cost' => $validatedData['unit_cost'],
            'pieces_per_set' => $validatedData['pieces_per_set'],
            'stocks_per_set' => $validatedData['stocks_per_set'],
        ]);
    
        // Redirect back with success message
        return redirect()->back()->with('success', 'Product added successfully!');
    }
    

    
    public function showAddProductForm()
    {
        // Retrieve all budgets and unique products to buy
        $budgets = Budget::all();
        $productsToBuy = Budget::pluck('product_to_buy')->unique();
    
        // Count the rows in the Inventory table
        $inventoryCount = Inventory::count();
    
        // Calculate the total remaining balance across all budgets
        $totalRemainingBalance = $budgets->sum('remaining_balance');
    
        // Pass data to the view
        return view('addproduct', compact('budgets', 'productsToBuy', 'inventoryCount', 'totalRemainingBalance'));
    }
    
    
}
