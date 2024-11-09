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

                    row.innerHTML = `
                        <td class="px-2 py-1 border-b">${product.product_id}</td>
                        <td class="px-2 py-1 border-b">${product.product_name}</td>
                        <td class="px-2 py-1 border-b">
                            <img src="{{ asset('storage/') }}/${product.product_image}" alt="${product.product_name}" class="w-12 h-12 object-cover">
                        </td>
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
                                ? '<span class="text-white bg-red-500 px-3 py-1 rounded">Details undefined, need action</span>'
                                : ''}
                            <a href="{{ route('product.details', '') }}/${product.product_id}" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors duration-300 mt-2 heartbeat-animation">
                                Edit
                            </a>
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
