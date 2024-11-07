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
            'product_id' => 'required|exists:products,product_id',
            'product_name' => 'required|string|max:255',
            'unit_cost' => 'required|numeric|min:0',
            'pieces_per_set' => 'required|integer|min:1',
            'stocks_per_set' => 'required|integer|min:1',
            'expiration_date' => 'required|date|after:today',
        ]);

        $unitCost = $request->unit_cost;
        $piecesPerSet = $request->pieces_per_set;
        $stocksPerSet = $request->stocks_per_set;
        $totalCost = $unitCost * $piecesPerSet * $stocksPerSet;

        $budget = Budget::find($request->budget_identifier);
        $userId = session('user_id');

        $this->logAction('Creating inventory item', [
            'user_id' => $userId,
            'budget_id' => $request->budget_identifier,
            'product_name' => $request->product_name,
            'total_cost' => $totalCost,
            'available_budget' => $budget->input_budget,
        ]);

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

        Inventory::create([
            'budget_identifier' => $request->budget_identifier,
            'product_id' => $request->product_id,
            'product_name' => $request->product_name,
            'unit_cost' => $request->unit_cost,
            'pieces_per_set' => $request->pieces_per_set,
            'stocks_per_set' => $request->stocks_per_set,
            'exp_date' => $request->expiration_date,
        ]);

        $budget->remaining_balance = $budget->input_budget - $totalCost;
        $budget->budget_status = 'Budget Used'; // Update budget status
        $budget->save();

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
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_status' => 'required|string|in:TO BE ADDED',
        ]);
    
        try {
            $image = $request->file('product_image');
            $filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $filename);
            $imageName = $filename . '.' . $image->getClientOriginalExtension();
            $image->storeAs('', $imageName); // Store the image in default location
    
            Product::create([
                'product_name' => $request->product_name,
                'product_image' => $imageName,
                'product_status' => $request->product_status,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Product added successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function showProductDetails()
    {
        $products = Product::all(); // Fetch all products from the database

        return view('your-view-name', compact('products'));
    }

    public function updateStatus(Request $request, $product_id)
{
    // Validate the input
    $validated = $request->validate([
        'product_status' => 'required|string|in:In Stock,Damaged,Expired,Ordered,PENDING',
    ]);

    // Find the product by ID
    $product = Product::find($product_id);

    if (!$product) {
        // Return a JSON error response if the product is not found
        return response()->json([
            'success' => false,
            'message' => 'Product not found'
        ], 404);
    }

    // Check if product_description or product_stocks is 'TO BE DEFINED'
    if (($product->product_description == 'TO BE DEFINED' || $product->product_stocks == 'TO BE DEFINED') 
        && $validated['product_status'] == 'In Stock') {
        return response()->json([
            'success' => false,
            'message' => 'Cannot update status to "In Stock" because product details (description or stocks) are undefined'
        ], 400); // Send an error response
    }

    // Update the product status
    $product->product_status = $validated['product_status'];
    $product->save();

    // Return a JSON response with success message and updated product data
    return response()->json([
        'success' => true,
        'message' => 'Product status updated successfully',
        'product_id' => $product->product_id,
        'product_status' => $product->product_status
    ]);
}

}
