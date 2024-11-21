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
                <th class="px-2 py-1 border-b">Category '1' Water | '2' Grocery</th>
                <th class="px-2 py-1 border-b">Product Name</th>
                <th class="px-2 py-1 border-b">Product Image</th>
                <th class="px-2 py-1 border-b">Active Stocks</th>
                <th class="px-2 py-1 border-b">Product Price</th>
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
<script>
  (function() {
            // Flag to ensure the polling happens only once
            let hasPolled = false; // Change from isPollingActive to hasPolled

            // Function to fetch the updated product list
            function fetchUpdatedProducts() {
            fetch('/products/update')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('product-body');
                tableBody.innerHTML = ''; // Clear existing rows

                data.forEach(product => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-details-status', product.product_details == 'TO BE DEFINED' || product.product_price == 0 || product.product_price == 0.00 ? 'undefined' : 'defined');

                    // Use the total_stocks value (sum of stocks_per_set) for the Active Stocks
                    const stocksPerSet = product.total_stocks;

                    row.innerHTML = `
                        <td class="px-2 py-1 border-b">${product.product_id}</td>
                        <td class="px-2 py-1 border-b">${product.details ? product.details.category_name : 'N/A'}</td>
                        <td class="px-2 py-1 border-b">${product.product_name}</td>
                        <td class="px-2 py-1 border-b">
                            <img src="/storage/product-images/${product.product_image}" alt="${product.product_name}" class="w-20 h-18 object-cover">
                        </td>
                        <td class="px-2 py-1 border-b">${stocksPerSet}</td> <!-- Active Stocks -->
                        <td class="px-2 py-1 border-b">${product.product_price}</td> <!-- Active Stocks -->
                        <td class="px-2 py-1 border-b">${product.created_at}</td>
                        <td class="border px-1 py-1">
                            <span id="status-${product.product_id}">${product.product_status}</span>
                            <button 
                                class="ml-2 bg-yellow-500 text-white rounded-md px-1 py-1" 
                                onclick="openSetStatusModal(${product.product_id}, '${product.product_status}')"
                            >
                                Edit
                            </button>
                        </td>
                        <td class="px-2 py-1 border-b">
                            ${product.product_details == 'TO BE DEFINED' || product.product_price == 0 || product.product_price == 0.00
                                ? '<span class="text-white bg-red-500 px-3 py-1 rounded">Details undefined, need actions</span>'
                                : ''}
                            <a href="{{ route('product.details', '') }}/${product.product_id}" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors duration-300 mt-2 heartbeat-animation">
                               ✎
                            </a>
                             <button 
                                class="ml-2 bg-red-500 text-white rounded-md px-2 py-1"
                                onclick="removeProduct(${product.product_id})">
                                Remove
                            </button>
                        </td>
                    `;

                    tableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Error fetching product data:', error));
            }
                // Function to trigger polling only once
                function startPollingOnce() {
                    if (!hasPolled) {
                        hasPolled = true; // Set flag to prevent further polling
                        fetchUpdatedProducts(); // Fetch updated product data once
                    }
                }

                // Use IntersectionObserver to detect if the table section is in view
                const productTableSection = document.getElementById('product-details-section');

                const observer = new IntersectionObserver(entries => {
                    // If the table section is in view and has not polled yet
                    if (entries[0].isIntersecting && !hasPolled) {
                        startPollingOnce();
                    }
                }, { threshold: 0.5 }); // Start polling when 50% of the section is in view

                // Start observing the product table section
                observer.observe(productTableSection);

                // Initial fetch when the page loads
                fetchUpdatedProducts();
            })();


            // Function to remove a product by ID
    function removeProduct(productId) {
        if (confirm('Are you sure you want to remove this product?')) {
            fetch(`/product/${productId}/remove`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row from the table
                    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                    if (row) {
                        row.remove();
                    }
                    alert(data.message);
                } else {
                    alert('Failed to remove the product.');
                }
            })
            .catch(error => {
                console.error('Error removing product:', error);
                alert('An error occurred while removing the product.');
            });
        }
    }
</script>
<!-- Include JavaScript file for filters -->
<script src="{{ asset('js/filter-product-details.js') }}"></script>
