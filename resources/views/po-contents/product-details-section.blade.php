<!-- C:\xampp\htdocs\purchasing-officer\resources\views\po-contents\product-details-section.blade.php -->

<div id="product-details-section" class="section hidden bg-white rounded-lg shadow-md p-5">
    <h1 class="text-xl font-bold mb-4">Product Details</h1>
    
    <!-- Note about updating product status -->
    <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
        <strong>Note:</strong> To make a product available for sale, update its status to "In Stock."
    </div>

    <div class="bg-red-100 text-green-800 p-3 rounded-md mb-4">
        <strong>Note:</strong> When the product is already in products section but undefined, add details for Inventory
        <!-- Add the button below the note -->
        <button 
            class="ml-4 bg-yellow-500 text-white rounded-md px-4 py-2 hover:bg-blue-600"
            onclick="document.getElementById('section-selector').value = 'add-product'; toggleSections();"
        >
            Add Product to the Inventory
        </button>
    </div>

    <!-- Search and Date Range Filters -->
    <div class="mb-4 flex justify-between items-center">
        <!-- Search Filter -->
        <div class="flex items-center">
            <label for="search" class="text-sm font-medium text-gray-700 mr-2">Search:</label>
            <input 
                type="text" 
                id="search" 
                class="p-2 border border-gray-300 rounded-md text-sm"
                placeholder="Search by Product Name or ID"
            />
        </div>

        <!-- Date Range Filter -->
        <div class="flex items-center">
            <label for="start-date" class="text-sm font-medium text-gray-700 mr-2">Date Range:</label>
            <input 
                type="date" 
                id="start-date" 
                class="p-2 border border-gray-300 rounded-md text-sm"
            />
            <span class="mx-2">to</span>
            <input 
                type="date" 
                id="end-date" 
                class="p-2 border border-gray-300 rounded-md text-sm"
            />
        </div>
    </div>

    <!-- Product Table -->
    <table id="product-table" class="min-w-full table-auto text-sm">
        <thead>
            <tr>
                <th class="px-2 py-1 border-b">Product ID</th>
                <th class="px-2 py-1 border-b">Category</th>
                <th class="px-2 py-1 border-b">Product Name</th>
                <th class="px-2 py-1 border-b">Product Image</th>
                <th class="px-2 py-1 border-b">Active Stocks</th>
                <th class="px-2 py-1 border-b">Created At</th>
                <th class="px-2 py-1 border-b">Product Status</th>
                <th class="px-2 py-1 border-b">Actions</th>
            </tr>
        </thead>
        <tbody id="product-body">
            <!-- Product rows will be injected here -->
        </tbody>
    </table>
</div>

<!-- Include JavaScript file for filters -->
<script src="{{ asset('js/filter-product-details.js') }}"></script>
