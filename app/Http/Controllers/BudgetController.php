<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Log; // Import the Log model
use Illuminate\Support\Facades\Log as FacadeLog;

class BudgetController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'reference_code' => 'required|string|max:255',
            'product_to_buy' => 'required|string|max:255',
            'input_budget' => 'required|numeric|min:0',
        ]);

        // Retrieve the User ID from the session
        $userId = session('user_id');

        try {
            $budget = new Budget([
                'reference_code' => $validatedData['reference_code'],
                'product_to_buy' => $validatedData['product_to_buy'],
                'input_budget' => $validatedData['input_budget'],
                'balance' => $validatedData['input_budget'],
                'remaining_balance' => $validatedData['input_budget'], // Set this to the same value as input_budget
            ]);

            $budget->save();

            // Log the budget creation action
            $this->logAction('Budget created', [
                'user_id' => $userId,
                'reference_code' => $budget->reference_code,
                'product_to_buy' => $budget->product_to_buy,
                'input_budget' => $budget->input_budget,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Budget added successfully!',
                'data' => [
                    'input_budget' => $budget->input_budget,
                    'remaining_balance' => $budget->remaining_balance,
                    'reference_code' => $budget->reference_code,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add budget. Please try again.'
            ], 500);
        }
    }

    public function showAddProductForm()
    {
        $budgets = Budget::all(['id', 'reference_code', 'product_to_buy']); // Fetch relevant columns
        return view('addproduct', compact('budgets')); // Pass the data to the view
    }

    // Method to retrieve all budget records and pass them to the view
    public function index()
    {
        $budgets = Budget::all();
        return view('addproduct', compact('budgets'));
    }

    public function showBudgetDetails($id)
    {
        // Retrieve the budget by ID
        $budget = Budget::find($id);
    
        if (!$budget) {
            return response()->json([
                'success' => false,
                'message' => 'Budget not found.',
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => $budget,
        ]);
    }

    private function logAction($message, $data)
    {
        $logData = json_encode(array_merge(['message' => $message], $data)); 
        Log::create(['log_data' => $logData]); 
        FacadeLog::info($message, $data); 
    }

    
}
