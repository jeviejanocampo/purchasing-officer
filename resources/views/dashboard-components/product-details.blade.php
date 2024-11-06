@extends('main.main')

@section('content')
<div class="bg-white rounded-lg shadow-md p-5">

    <button onclick="history.back()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors duration-300 mb-4">
        ← Back to Table
    </button>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <h1 class="text-xl font-bold mb-4">Product Details</h1>

        <!-- Product Price -->
        <div>
            <label class="font-medium text-gray-700">Product Price</label>
            <p id="product-price" class="block">{{ $product->product_price }}</p>
            <input type="text" name="product_price" id="edit-product-price" class="hidden mt-1 p-2 border rounded" placeholder="Enter product price" value="{{ $product->product_price }}">
        </div>

        <!-- Product Details ID -->
        <div>
            <label class="font-medium text-gray-700">Product Details ID</label>
            <p id="product-details-id" class="block">{{ $product->product_details_id }}</p>
            <input type="text" name="product_details_id" id="edit-product-details-id" class="hidden mt-1 p-2 border rounded" placeholder="Enter product details ID" value="{{ $product->product_details_id }}">
        </div>

        <!-- Product Description -->
        <div>
            <label class="font-medium text-gray-700">Product Description</label>
            <p id="product-description" class="block">{{ $product->product_description }}</p>
            <textarea name="product_description" id="edit-product-description" class="hidden mt-1 p-2 border rounded" placeholder="Enter product description">{{ $product->product_description }}</textarea>
        </div>

        <!-- Product Stocks -->
        <div>
            <label class="font-medium text-gray-700">Product Stocks</label>
            <p id="product-stocks" class="block">{{ $product->product_stocks }}</p>
            <input type="number" name="product_stocks" id="edit-product-stocks" class="hidden mt-1 p-2 border rounded" placeholder="Enter number of stocks" value="{{ $product->product_stocks }}">
        </div>

        <!-- Product Expiry Date -->
        <div>
            <label class="font-medium text-gray-700">Product Expiry Date</label>
            <p id="product-expiry-date" class="block">{{ $product->product_expiry_date }}</p>
            <input type="date" name="product_expiry_date" id="edit-product-expiry-date" class="hidden mt-1 p-2 border rounded" placeholder="Select expiry date" value="{{ $product->product_expiry_date }}">
        </div>

        <!-- Product Image -->
        <div>
            <label class="font-medium text-gray-700">Product Image</label>
            <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}" class="w-32 h-32 object-cover">
            <input type="file" name="product_image" id="edit-product-image" class="hidden mt-1 p-2 border rounded">
        </div>

        <!-- Product Status -->
        <div>
            <label class="font-medium text-gray-700">Product Status</label>
            <p id="product-status" class="block">{{ $product->product_status }}</p>
            <input type="text" name="product_status" id="edit-product-status" class="hidden mt-1 p-2 border rounded" placeholder="Enter product status" value="{{ $product->product_status }}">
        </div>

        <!-- Buttons (Edit and Cancel) -->
        <div class="col-span-2 mt-4 flex justify-end gap-4">
            <button id="edit-button" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-300">
                Edit
            </button>
            <button id="cancel-button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors duration-300 hidden">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    // Get elements
    const editButton = document.getElementById('edit-button');
    const cancelButton = document.getElementById('cancel-button');
    const editableFields = document.querySelectorAll('.hidden');
    const textFields = document.querySelectorAll('p'); // Select all <p> elements

    // Store initial values to restore later
    const initialValues = {
        product_price: "{{ $product->product_price }}",
        product_details_id: "{{ $product->product_details_id }}",
        product_description: "{{ $product->product_description }}",
        product_stocks: "{{ $product->product_stocks }}",
        product_expiry_date: "{{ $product->product_expiry_date }}",
        product_status: "{{ $product->product_status }}"
    };

    editButton.addEventListener('click', function() {
        // Show input fields when Edit button is clicked
        editableFields.forEach(field => field.classList.remove('hidden'));  // Show input fields
        textFields.forEach(field => field.classList.add('hidden'));  // Hide <p> text fields
        editButton.classList.add('hidden'); // Hide Edit button
        cancelButton.classList.remove('hidden'); // Show Cancel button
    });

    cancelButton.addEventListener('click', function() {
        // Revert to initial values when Cancel button is clicked
        document.getElementById('edit-product-price').value = initialValues.product_price;
        document.getElementById('edit-product-details-id').value = initialValues.product_details_id;
        document.getElementById('edit-product-description').value = initialValues.product_description;
        document.getElementById('edit-product-stocks').value = initialValues.product_stocks;
        document.getElementById('edit-product-expiry-date').value = initialValues.product_expiry_date;
        document.getElementById('edit-product-status').value = initialValues.product_status;

        // Hide input fields and cancel button, show text fields and edit button again
        editableFields.forEach(field => field.classList.add('hidden'));
        textFields.forEach(field => field.classList.remove('hidden'));
        cancelButton.classList.add('hidden');
        editButton.classList.remove('hidden');
    });
</script>

@endsection