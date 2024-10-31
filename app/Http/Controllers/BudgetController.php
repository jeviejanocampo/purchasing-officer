<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use Illuminate\Support\Facades\Log;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        // Log incoming request data
        Log::info('Budget store request received:', $request->all());
    
        // Validate the incoming request
        $validatedData = $request->validate([
            'budget' => 'required|string', // Accept budget as a string first
            'budget-identifier' => 'required|string|max:255',
            'product_to_buy' => 'required|string|max:255', // Validate the product_to_buy field
        ]);
    
        try {
            // Strip the currency symbol and commas from the budget
            $cleanBudget = preg_replace('/[^\d.]/', '', $validatedData['budget']);
    
            // Create a new budget entry
            $budget = new Budget();
            $budget->reference_code = $validatedData['budget-identifier'];
            $budget->input_budget = $cleanBudget; // Store the cleaned budget
            $budget->product_to_buy = $validatedData['product_to_buy']; // Ensure this is populated
            $budget->remaining_balance = $cleanBudget; // Assuming you want to initialize this field
            $budget->save();
    
            // Log successful budget creation
            Log::info('Budget created successfully:', $budget->toArray());
    
            // Redirect back with a success message
            return redirect()->back()->with('success', 'Budget added successfully!');
        } catch (\Exception $e) {
            // Log the error message with the full exception for better debugging
            Log::error('Error saving budget:', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'request' => $request->all(), // Log request data for debugging
            ]);
    
            // Redirect back with an error message
            return redirect()->back()->withErrors(['error' => 'An error occurred while saving the budget.']);
        }
    }
    
    public function showAddProductForm()
    {
        // Retrieve all budget identifiers from the budget table
        $budgets = Budget::all();
    
        // Retrieve all unique product_to_buy values
        $productsToBuy = $budgets->pluck('product_to_buy')->unique();
    
        // Calculate the total remaining balance
        $totalRemainingBalance = $budgets->sum('remaining_balance');
    
        // Pass all data to the view
        return view('addproduct', compact('budgets', 'productsToBuy', 'totalRemainingBalance'));
    }
    

   
}
