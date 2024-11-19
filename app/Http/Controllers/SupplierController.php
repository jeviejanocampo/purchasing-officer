<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;


class SupplierController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'product_type' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Insert into database
        $supplier = Supplier::create($validated);

        // Return JSON response with success message and supplier data
        return response()->json([
            'success' => true,
            'message' => 'Supplier added successfully!',
            'supplier' => $supplier
        ]);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Active,Inactive',
        ]);
    
        $supplier = Supplier::findOrFail($id);
        $supplier->status = $request->input('status');
        $supplier->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!',
            'supplier' => $supplier,
        ]);
    }
    
}
