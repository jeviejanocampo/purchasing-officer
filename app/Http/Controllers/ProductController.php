<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Budget;
use App\Models\Product;
use App\Models\Log; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadeLog;
use Illuminate\Support\Facades\DB;

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
    
        // Calculate the total cost based on the provided data
        $unitCost = $request->unit_cost;
        $piecesPerSet = $request->pieces_per_set;
        $stocksPerSet = $request->stocks_per_set;
        $totalCost = $unitCost * $piecesPerSet * $stocksPerSet;
    
        // Retrieve the budget using the identifier
        $budget = Budget::find($request->budget_identifier);
    
        // Get the PurchasingOfficer information from session
        $userId = session('user_id');
        $user = \App\Models\User::find($userId);
        $PurchasingOfficerName = $user ? $user->name : 'Guest'; // Default to 'Guest' if no user is found
        $PurchasingOfficerId = $user ? $user->id : 'N/A'; // Default to 'N/A' if no user is found
    
        // Log the action of creating an inventory item
        $this->logAction('Creating inventory item', [
            'user_id' => $userId,
            'budget_id' => $request->budget_identifier,
            'product_name' => $request->product_name,
            'total_cost' => $totalCost,
            'available_budget' => $budget->input_budget,
            'PurchasingOfficer_id' => $PurchasingOfficerId,
            'PurchasingOfficer_name' => $PurchasingOfficerName,
            'role' => 'PurchasingOfficer', // Default role for the PurchasingOfficer
        ]);
    
        // Check if the total cost exceeds the available budget
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
    
        // Create the inventory item
        Inventory::create([
            'budget_identifier' => $request->budget_identifier,
            'product_id' => $request->product_id,
            'product_name' => $request->product_name,
            'unit_cost' => $request->unit_cost,
            'pieces_per_set' => $request->pieces_per_set,
            'stocks_per_set' => $request->stocks_per_set,
            'exp_date' => $request->expiration_date,
        ]);
    
        // Update the budget's remaining balance and status
        $budget->remaining_balance = $budget->input_budget - $totalCost;
        $budget->budget_status = 'Budget Used'; // Update budget status
        $budget->save();
    
        // Log the action of adding the product and updating the budget
        $this->logAction('Product added and budget updated', [
            'user_id' => $userId,
            'budget_id' => $request->budget_identifier,
            'product_name' => $request->product_name,
            'remaining_balance' => $budget->remaining_balance,
            'PurchasingOfficer_id' => $PurchasingOfficerId,
            'PurchasingOfficer_name' => $PurchasingOfficerName,
            'role' => 'PurchasingOfficer', // Default role for the PurchasingOfficer
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
    
    // Private method to handle logging actions to the logs table
    private function logAction($message, $data)
    {
        // Get the PurchasingOfficer information from session (we don't need to fetch user again)
        $PurchasingOfficerId = $data['PurchasingOfficer_id'] ?? 'N/A';
        $PurchasingOfficerName = $data['PurchasingOfficer_name'] ?? 'Guest';
        $role = $data['role'] ?? 'PurchasingOfficer'; // Default to 'PurchasingOfficer' if not passed
    
        // Combine the message with additional data
        $logData = json_encode(array_merge([
            'message' => $message,
            'PurchasingOfficer_id' => $PurchasingOfficerId,
            'PurchasingOfficer_name' => $PurchasingOfficerName,
            'role' => $role, // Include the role in the log data
        ], $data));
    
        // Insert the log into the logs table, including the role
        Log::create([
            'log_data' => $logData,
            'role' => $role, // Ensure the role is inserted into the role column
        ]);
    
        // Optional: Log to Laravel's default log
        FacadeLog::info($message, $data);
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

    public function addRestockDetails(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_status' => 'required|string|in:TO BE ADDED',
            'image_name' => 'nullable|string|max:255', // Optional image name
        ]);

        try {
            // Retrieve the uploaded image file
            $image = $request->file('product_image');
            $filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            // Use the user-provided image name, or default to a sanitized version of the original filename
            $newImageName = $request->image_name ?: preg_replace('/[^a-zA-Z0-9-_\.]/', '', $filename);
            $newImageName .= '.' . $image->getClientOriginalExtension(); // Add file extension

            // Store image in the 'public/product-images' directory with the new name
            $image->storeAs('public/product-images', $newImageName);

            // Store product details in the database
            Product::create([
                'product_name' => $request->product_name,
                'product_image' => $newImageName, // Save the new image name in the database
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

    public function lowStockAlerts()
    {
        $lowStockProducts = Product::where('product_stocks', '<=', 30)->get();
        return response()->json($lowStockProducts);
    }

    public function getLowStockNotifications()
    {
        $lowStockProducts = Product::where('product_stocks', '<=', 30)->get();
        $notifications = $lowStockProducts->map(function($product) {
            return [
                'type' => 'Low Stock Alert',
                'message' => "Low stock alert! Product ID: {$product->product_id} - {$product->product_name} only has {$product->product_stocks} left.",
                'time' => now()->diffForHumans(),
            ];
        });

        return response()->json($notifications);
    }


    public function updateStocks(Request $request, $id)
    {
        // Find the product by ID
        $product = Product::findOrFail($id);
        
        // Validate the incoming request
        $request->validate([
            'product_stocks' => 'required|integer|min:0', // Ensure product_stocks is a valid integer and >= 0
        ]);
        
        // Update the product's stock
        $product->product_stocks = $request->input('product_stocks');
        $product->save();

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Stock updated successfully']);
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

    public function fetchInventoryData($productId)
{
    // Fetch the inventory record where product_id matches
    $inventory = Inventory::where('product_id', $productId)->first();

    // If inventory data is found, return it as JSON
    if ($inventory) {
        return response()->json([
            'unit_cost' => $inventory->unit_cost,
            'stocks_per_set' => $inventory->stocks_per_set,
            'exp_date' => $inventory->exp_date
        ]);
    } else {
        return response()->json(['message' => 'Inventory data not found'], 404);
    }
}

    public function update(Request $request, $product_id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'product_price' => 'required|numeric|min:0',
            'product_description' => 'required|string|max:255',
            'product_stocks' => 'required|integer|min:0',
            'product_expiry_date' => 'required|date',
            'product_details_id' => 'nullable|string|max:255', // Validate product_details_id if provided
        ]);

        // Find the product by ID
        $product = Product::find($product_id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Update the product with the validated data
            $product->product_price = $validatedData['product_price'];
            $product->product_description = $validatedData['product_description'];
            $product->product_stocks = $validatedData['product_stocks'];
            $product->product_expiry_date = $validatedData['product_expiry_date'];

            // Only update product_details_id if it's provided in the request
            if ($request->has('product_details_id')) {
                $product->product_details_id = $validatedData['product_details_id'];
            }

            // Check if a new image is uploaded and handle it
            if ($request->hasFile('product_image')) {
                // Store the uploaded image
                $imagePath = $request->file('product_image')->store('products', 'public');
                $product->product_image = $imagePath;
            }

            // Save the updated product
            $product->save();

            // Log the product update action
            FacadeLog::info("Product ID {$product_id} updated successfully.");

            // Commit the transaction
            DB::commit();

            // Redirect back to the product details page with success message
            return redirect()->route('product.show', $product_id)
                            ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            // If something goes wrong, rollback the transaction
            DB::rollBack();

            // Log the error using the correct log facade
            FacadeLog::error('Product update failed for ID ' . $product_id . ': ' . $e->getMessage());

            // Return error message
            return redirect()->back()->with('error', 'An error occurred while updating the product.');
        }
    }

    public function getUpdatedProducts()
    {
        // Fetch products along with their inventory, and calculate the total stocks_per_set
        $products = Product::with(['details', 'inventory'])  // Load product details and inventory (stocks_per_set)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($product) {
                // Calculate total stocks_per_set by summing up the stocks_per_set from all inventory records
                $totalStocks = $product->inventory->sum('stocks_per_set');
                $product->total_stocks = $totalStocks; // Add a custom property for total stocks
    
                return $product;
            });
    
        return response()->json($products);
    }
    
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id); // Find the product by its ID
            $product->delete(); // Delete the product
            return response()->json(['success' => true, 'message' => 'Product removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }
    }

    public function destroyProduct($id) {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Product not found']);
    }


}
