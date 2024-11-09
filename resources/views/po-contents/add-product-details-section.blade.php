<div id="add-product-details-section" class="section hidden bg-white rounded-lg shadow-md p-5">
    <h1 class="text-xl font-bold mb-4 mt-10">Add Product Details for Restocking</h1>
    
    <!-- Message about PENDING budget status -->
    <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
        <strong>Note:</strong> ❗ Only new added budget with status PENDING can be used for adding.
    </div>

    <form action="{{ route('product.addRestockDetails') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            <!-- Product Name -->
            <div class="mt-4 mb-4">
                <label for="product_name" class="block text-sm font-medium text-gray-700">Product Name</label>
                <select id="product_name" name="product_name" class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Product</option>
                    @foreach($productNames as $product)
                        <option value="{{ $product->product_to_buy }}">{{ $product->product_to_buy }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Image -->
            <div class="mt-4 mb-4">
                <label for="product_image" class="block text-sm font-medium text-gray-700">Product Image</label>
                <input type="file" id="product_image" name="product_image" class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="updateImageName()">
            </div>

            <!-- Manually Rename Image -->
            <div class="mt-4 mb-4">
                <label for="image_name" class="block text-sm font-medium text-gray-700">Image Filename (optional)</label>
                <input type="text" id="image_name" name="image_name" class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter custom image name (optional)">
            </div>

            <!-- Product Status -->
            <div class="mt-4 mb-4">
                <label for="product_status" class="block text-sm font-medium text-gray-700">Product Status</label>
                <select id="product_status" name="product_status" class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="TO BE ADDED">To Be Added</option>
                </select>
            </div>

        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-300 mt-4">
            Add Product
        </button>
    </form>
</div>

<script>
    // This function will update the image name field when the user selects an image
    function updateImageName() {
        const imageFileInput = document.getElementById('product_image');
        const imageNameField = document.getElementById('image_name');
        
        if (imageFileInput.files.length > 0) {
            // Extract the original file name (without extension) and set it as the default in the text field
            const fileName = imageFileInput.files[0].name.replace(/\.[^/.]+$/, ''); // Remove file extension
            imageNameField.value = fileName;  // Set the input value to the file name
        }
    }
</script>
