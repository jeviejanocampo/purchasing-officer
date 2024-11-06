<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Budget;
use App\Models\Product;
use App\Models\Log; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadeLog;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'budget_identifier' => 'required|exists:budget,id',
            'product_id' => 'required|exists:products,product_id',  // Validate product_id
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
    
        // Retrieve the User ID from session
        $userId = session('user_id');

        // Log the request details
        $this->logAction('Creating inventory item', [
            'user_id' => $userId,
            'budget_id' => $request->budget_identifier,
            'product_name' => $request->product_name,
            'total_cost' => $totalCost,
            'available_budget' => $budget->input_budget,
        ]);
    
        // Check if the total cost exceeds the available input budget
        if ($totalCost > $budget->input_budget) {
            $this->logAction('Total cost exceeds available budget', [
                'user_id' => $userId,
                'budget_id' => $request->budget_identifier,
                'total_cost' => $totalCost,
                'available_budget' => $budget->input_budget,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Total cost exceeds available budget.',
            ], 400);
        }
    
        // Create a new inventory item
        Inventory::create([
            'budget_identifier' => $request->budget_identifier,
            'product_id' => $request->product_id,  // Save product_id
            'product_name' => $request->product_name,
            'unit_cost' => $request->unit_cost,
            'pieces_per_set' => $request->pieces_per_set,
            'stocks_per_set' => $request->stocks_per_set,
            'exp_date' => $request->expiration_date,
        ]);
    
        // Update remaining balance directly to reflect the new cost
        $budget->remaining_balance = $budget->input_budget - $totalCost;
        $budget->save();
    
        // Log successful inventory addition and budget update
        $this->logAction('Product added and budget updated', [
            'user_id' => $userId,
            'budget_id' => $request->budget_identifier,
            'product_name' => $request->product_name,
            'remaining_balance' => $budget->remaining_balance,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Product added and budget updated successfully.',
            'data' => [
                'input_budget' => number_format($budget->input_budget, 2),
                'remaining_balance' => number_format($budget->remaining_balance, 2),
            ],
        ]);
    }

    public function create()
    {
        $productIds = \App\Models\Product::select('product_id')->get();
        $budgets = Budget::with('products')->get();
        $productIds = Product::select('product_id')->get();
        return view('addproduct', compact('budgets'));
    }

    public function showInventory()
    {
        $inventories = Inventory::all();
        return view('addproduct', compact('inventories'));
    }

    public function checkBudgetIdentifier(Request $request)
    {
        $request->validate([
            'budget_identifier' => 'required|exists:budget,id',
        ]);

        $isUsed = Inventory::where('budget_identifier', $request->budget_identifier)->exists();
        return response()->json(['is_used' => $isUsed]);
    }

    // Private method to handle logging actions to the logs table
    private function logAction($message, $data)
    {
        $logData = json_encode(array_merge(['message' => $message], $data)); // Convert to JSON format
        Log::create(['log_data' => $logData]); // Insert into logs table
        FacadeLog::info($message, $data); // Optional: Also log to Laravel's default log
    }

    public function addRestockDetails(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_status' => 'required|string|in:Ordered,Back-Ordered,In Stock,Not Available,Out of Stock',
        ]);

        try {
            // Handle image upload and clean up the filename
            $image = $request->file('product_image');
            
            // Get original filename and remove any unwanted characters
            $filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $filename); // Remove non-alphanumeric characters
            
            // Ensure the file name is unique by appending a timestamp or using a unique identifier
            $imageName = $filename . '.' . $image->getClientOriginalExtension();
            
            // Store the image in the default storage path
            $image->storeAs('', $imageName); // Only the filename, no path (it goes to default storage location)

            // Create a new product (Product Price, Description, Stocks, and Expiry Date will use defaults)
            Product::create([
                'product_name' => $request->product_name,
                'product_image' => $imageName,
                'product_status' => $request->product_status,  // Store selected status
                // Product price, description, stocks, and expiry date will use their default values
            ]);

            // Return with success message
            return redirect()->back()->with('success', 'Product added successfully.');

        } catch (\Exception $e) {
            // Return with error message
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function showProductDetails()
    {
        $products = Product::all(); // Fetch all products from the database

        return view('your-view-name', compact('products'));
    }
}
