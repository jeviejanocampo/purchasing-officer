<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="container">
    <h1>Experiment Tab</h1>

    <!-- Input Form for checkout_id, status, and inventory_id -->
    <form id="update-status-form">
        <div class="mb-4">
            <label for="checkout_id" class="block text-sm font-medium text-gray-700">Checkout ID:</label>
            <input 
                type="number" 
                id="checkout_id" 
                class="p-2 border border-gray-300 rounded-md text-sm"
                placeholder="Enter Checkout ID" 
                required
            />
        </div>

        <!-- Placeholder for dynamically generated inventory_id inputs -->
        <div id="inventory-inputs"></div>

        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status:</label>
            <select 
                id="status" 
                class="p-2 border border-gray-300 rounded-md text-sm" 
                required
            >
                <option value="COMPLETED">COMPLETED</option>
                <option value="CANCELLED">CANCELLED</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
            Update Status
        </button>
    </form>

    <!-- Display any success or error messages -->
    <div id="status-message" class="mt-4"></div>
</div>

<script>
    // JavaScript to handle the form submission and update the checkout details
    document.getElementById('checkout_id').addEventListener('input', function(event) {
        const checkoutId = event.target.value;

        // Clear previous inventory inputs
        document.getElementById('inventory-inputs').innerHTML = '';

        // Fetch checkout details for the given checkout_id
        if (checkoutId) {
            fetch(`/get-checkout-details/${checkoutId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        // Generate input fields for inventory_id based on the number of rows returned
                        data.data.forEach((detail, index) => {
                            const div = document.createElement('div');
                            div.classList.add('mb-4');
                            div.innerHTML = `
                                <label for="inventory_id_${index}" class="block text-sm font-medium text-gray-700">
                                    Inventory ID for Product ${detail.product_id}:
                                </label>
                                <input 
                                    type="number" 
                                    id="inventory_id_${index}" 
                                    name="inventory_id[]" 
                                    class="p-2 border border-gray-300 rounded-md text-sm"
                                    placeholder="Enter Inventory ID for Product ${detail.product_id}" 
                                    required
                                />
                            `;
                            document.getElementById('inventory-inputs').appendChild(div);
                        });
                    } else {
                        document.getElementById('status-message').innerHTML = "<p class='text-red-500'>No details found for this Checkout ID.</p>";
                    }
                })
                .catch(error => {
                    document.getElementById('status-message').innerHTML = "<p class='text-red-500'>Error fetching details. Please try again.</p>";
                    console.error('Error:', error);
                });
        }
    });

   // Handle form submission
document.getElementById('update-status-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const checkoutId = document.getElementById('checkout_id').value; // single checkout_id
    const status = document.getElementById('status').value;
    const inventoryIds = Array.from(document.querySelectorAll('[name="inventory_id[]"]')).map(input => input.value);

    // Validate the input
    if (!checkoutId || !status || inventoryIds.length === 0 || inventoryIds.some(id => !id)) {
        document.getElementById('status-message').innerHTML = "<p class='text-red-500'>Please provide Checkout ID, Status, and Inventory IDs.</p>";
        return;
    }

    // Send the request to update the status
    fetch(`/update-checkout-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ checkout_ids: [checkoutId], status: status, inventory_ids: inventoryIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('status-message').innerHTML = "<p class='text-green-500'>Status updated successfully!</p>";
        } else {
            document.getElementById('status-message').innerHTML = "<p class='text-red-500'>Failed to update the status.</p>";
        }
    })
    .catch(error => {
        document.getElementById('status-message').innerHTML = "<p class='text-red-500'>An error occurred. Please try again.</p>";
        console.error('Error:', error);
    });
});

</script>

</body>
</html>
