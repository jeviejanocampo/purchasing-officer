<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Supplier;
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
            'supplier_id' => 'required|exists:suppliers,id', // Ensure the supplier exists
        ]);

        try {
            $budget = new Budget([
                'reference_code' => $validatedData['reference_code'],
                'product_to_buy' => $validatedData['product_to_buy'],
                'input_budget' => $validatedData['input_budget'],
                'balance' => $validatedData['input_budget'],
                'remaining_balance' => $validatedData['input_budget'],
                'supplier_id' => $validatedData['supplier_id'], // Save the selected supplier
            ]);

            $budget->save();

            // Log the budget creation action
            $this->logAction('Budget created', [
                'user_id' => session('user_id'),
                'reference_code' => $budget->reference_code,
                'product_to_buy' => $budget->product_to_buy,
                'input_budget' => $budget->input_budget,
                'supplier_id' => $budget->supplier_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Budget added successfully!',
                'data' => [
                    'input_budget' => $budget->input_budget,
                    'remaining_balance' => $budget->remaining_balance,
                    'reference_code' => $budget->reference_code,
                    'supplier_id' => $budget->supplier_id,
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
        $budget = Budget::with('supplier')->find($id);
    
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

    public function create()
    {
        $suppliers = Supplier::all(); // Fetch all suppliers
        return view('your-view-name', compact('suppliers'));
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'budget_status' => 'required|string|in:Available,Used,Closed',
        ]);

        try {
            $budget = Budget::findOrFail($id);
            $budget->budget_status = $request->input('budget_status');
            $budget->updated_at = now();
            $budget->save();

            return response()->json([
                'success' => true,
                'message' => 'Budget status updated successfully!',
                'new_status' => $budget->budget_status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update budget status. Please try again.',
            ], 500);
        }
    }

    
}
