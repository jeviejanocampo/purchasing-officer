<!-- C:\xampp\htdocs\purchasing-officer\resources\views\po-contents\product-details-section.blade.php -->
<div id="product-details-section" class="section hidden bg-white rounded-lg shadow-md p-5">
    <h1 class="text-xl font-bold mb-4">Product Details</h1>
    
    <!-- Note about updating product status -->
   
   

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
                <th class="px-2 py-1 border-b">Active Stocks Per Set</th>
                <th class="px-2 py-1 border-b">Retailed Price</th>
                <th class="px-2 py-1 border-b">Created At</th>
                <!-- <th class="px-2 py-1 border-b">Product Status</th>
                <th class="px-2 py-1 border-b">Actions</th> -->
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
        let hasPolled = false; 

        // Function to update the product_stocks in the database after a delay
        function updateProductStocks(productId, newStocks) {
            setTimeout(() => {
                fetch(`/product/${productId}/update-stocks`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ product_stocks: newStocks })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Product stocks updated successfully');
                    } else {
                        console.log('Failed to update product stocks');
                    }
                })
                .catch(error => {
                    console.error('Error updating product stocks:', error);
                });
            }, 5000); // 5000 ms = 5 seconds
        }

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
                        <td class="px-2 py-1 border-b">${product.product_price}</td>
                        <td class="px-2 py-1 border-b">${product.created_at}</td>
                       
                    `;

                    tableBody.appendChild(row);

                    // Trigger the update of product stocks after 5 seconds
                    updateProductStocks(product.product_id, stocksPerSet);
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
    
    function removeProduct(productId) {
    if (confirm("Are you sure you want to remove this product?")) {
        fetch(`/product/${productId}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product removed successfully');
                // Optionally, remove the product row from the table
                document.querySelector(`tr[data-details-status][data-id="${productId}"]`)?.remove();
            } else {
                alert('Failed to remove product');
            }
        })
        .catch(error => console.error('Error removing product:', error));
    }
}

</script>

<!-- Include JavaScript file for filters -->
<script src="{{ asset('js/filter-product-details.js') }}"></script>
