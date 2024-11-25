<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<style>
    .selected-row {
        background-color: #f0f4f8; /* Light background color for selected row */
        transform: translateY(-5px); /* Move the row up slightly */
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); /* Create a subtle shadow for the 3D effect */
        transition: transform 0.2s ease, box-shadow 0.2s ease; /* Smooth transition for both transform and shadow */
    }
</style>
<div id="view-orders-section" class="section bg-white rounded-lg shadow-md p-5">
    <h2 class="text-xl font-bold mb-5">Order Confirmation Dashboard</h2>

    <!-- Filters Section -->
    <div class="mb-4">
        <!-- Filter by Today's Orders -->
        <label for="today" class="mr-2 hidden">Today's Orders</label>
        <input type="checkbox" id="today" class="hidden"/>

        <!-- Filter by Status -->
        <label for="status" class="ml-4 mr-2">Order Status</label>
        <select id="status" class="border p-2">
            <option value="">All</option>
            <option value="Approved">Approved</option>
            <option value="Returned">Returned</option>
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Rejected">Rejected</option>
            <option value="Cancelled">Cancelled</option>
        </select>

        <!-- Search Bar -->
        <input type="text" id="search-bar" placeholder="Search by Order ID or User ID" class="border p-2 ml-4" />

        <label for="start-date" class="ml-4 mr-2">Start Date</label>
        <input type="date" id="start-date" class="border p-2" />

        <label for="end-date" class="ml-4 mr-2">End Date</label>
        <input type="date" id="end-date" class="border p-2" />

        <!-- Clear Filter Button -->
        <button type="button" id="clear-filters" class="px-4 py-2 bg-gray-400 text-white rounded-lg ml-4">
            Clear Filter
        </button>

    </div>

    <!-- Orders Table -->
    <div class="overflow-x-auto">
        <table class="table-auto w-full border-collapse border border-gray-200" id="orders-table">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left">Order ID</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">User ID</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Order Date</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Payment Method</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Latitude</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Longitude</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Total Amount To Pay</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Order Status</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">View</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr class="order-row" onclick="selectRow(this)">
                    <td class="border border-gray-300 px-4 py-2 relative order-id" data-order-date="{{ $order->order_date }}">
                        {{ $order->order_id }}
                        <span class="new-order-label absolute right-0 top-0 bg-blue-800 text-white text-xs px-2 py-1 rounded-full rounded-full hidden">
                            New Order
                        </span>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">{{ $order->user_id }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $order->order_date }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        @if ($order->payment_method == 'COD')
                            <span class="text-sm">Cash on Delivery</span>
                        @else
                            {{ $order->payment_method }}
                        @endif
                    </td>
                    <td class="border border-gray-300 px-4 py-2">{{ $order->checkout->latitude }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $order->checkout->longitude }}</td>
                    <td class="border border-gray-300 px-4 py-2">PHP {{ number_format($order->checkout->total_amount, 2) }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        @if ($order->order_status == 'In Process')
                            <span class="text-blue-700">In Process</span>
                        @elseif ($order->order_status == 'TO RECEIVED')
                            <span class="text-green-700">Approved</span>
                        @elseif ($order->order_status == 'Rejected')
                            <span class="text-red-700">Rejected</span>
                        @elseif ($order->order_status == 'Cancelled')
                            <span class="text-orange-700">Cancelled</span>
                        @elseif ($order->order_status == 'PENDING')
                            <span class="text-yellow-700">Pending</span>
                        @elseif ($order->order_status == 'Returned')
                            <span class="text-violet-700">Returned</span>
                        @elseif ($order->order_status == 'Completed')
                            <span class="text-pink-700">Completed</span>
                        @else
                            <span class="text-gray-500">Unknown</span>
                        @endif
                        <button onclick="openEditStatusModal('{{ $order->order_id }}', '{{ $order->order_status }}')" class="ml-2 text-blue-500 hover:text-blue-700 flex items-center">
                            <i class="fas fa-pencil-alt mr-2"></i> Edit Status
                        </button>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button class="text-blue-500 hover:text-blue-700 flex items-center" onclick="viewOrderDetails('{{ $order->order_id }}', '{{ $order->checkout_id }}', '{{ $order->user_id }}')">
                            <i class="fas fa-eye mr-2"></i> View Details
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>

<!-- Edit Status Modal -->
<div id="edit-status-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Edit Order Status</h2>
        <form id="edit-status-form" action="{{ route('edit-status') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" id="order-id">
            <div class="mb-4">
                <label for="status" class="block text-gray-700">Status</label>
                <select id="status" name="order_status" class="block w-full mt-2 p-2 border rounded-lg">
                    <option value="In Process">In Process</option>
                    <option value="TO RECEIVED">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <div class="flex justify-between">
                <button type="button" onclick="closeEditStatusModal()" class="px-4 py-2 bg-gray-400 text-white rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Viewing Checkout Details -->
<div id="modal" class="fixed inset-0 z-50 hidden bg-gray-800 bg-opacity-50">
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white rounded-lg p-5 w-1/3">
            <h2 class="text-xl font-bold mb-5">Order Details</h2>

            <!-- Display Selected Order ID -->
            <!-- <div class="mb-4">
                <label class="font-medium" id="selected-order-id">Selected Order ID: </label>
            </div> -->

            <div id="checkout-details" class="space-y-4">
                <!-- Checkout details will be populated via JavaScript -->
            </div>

            <!-- Add status change section -->
            <div class="mt-4">
                <label for="order-status" class="font-medium">Change Order Status</label>
                <select id="order-status" class="border p-2 mt-2 w-full">
                    <option value="PENDING">Pending</option>
                    <option value="CONFIRMED">Confirmed</option>
                    <option value="REJECTED">Rejected</option>
                    <option value="CANCELLED">Cancelled</option>
                </select>
            </div>

            <div class="mt-4 flex justify-between">
                <button class="bg-gray-500 text-white py-2 px-4 rounded" onclick="closeModal()">Close</button>
                <button class="bg-blue-500 text-white py-2 px-4 rounded" onclick="updateOrderStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderRows = document.querySelectorAll('.order-id');

        orderRows.forEach(row => {
            const orderDate = row.getAttribute('data-order-date');
            const orderDateTime = new Date(orderDate);
            const now = new Date();
            const diffInMinutes = (now - orderDateTime) / (1000 * 60); // Difference in minutes

            if (diffInMinutes <= 10) {
                const label = row.querySelector('.new-order-label');
                if (label) {
                    label.classList.remove('hidden'); // Show the label if within 10 minutes
                }
            }
        });
    });
</script>
<script>
    // Function to select a row and apply the 3D effect
    function selectRow(row) {
        // Remove 'selected-row' class from all rows
        const rows = document.querySelectorAll('.order-row');
        rows.forEach(r => r.classList.remove('selected-row'));

        // Add 'selected-row' class to the clicked row
        row.classList.add('selected-row');
    }

    // Function to remove 'selected-row' when clicking outside the table rows
    document.addEventListener('click', function(event) {
        const table = document.getElementById('orders-table');
        const rows = table.querySelectorAll('.order-row');

        // If the click was outside the table, remove the highlight from all rows
        if (!table.contains(event.target)) {
            rows.forEach(row => row.classList.remove('selected-row'));
        }
    });

    // Modify the onclick event on table rows to apply the selection
    document.querySelectorAll('.order-row').forEach(row => {
        row.addEventListener('click', function() {
            selectRow(row);
        });
    });


</script>
<script>
    // Open the modal and set the selected order id and current status
    function openEditStatusModal(orderId, currentStatus) {
        // Set the order ID and current status in the modal
        document.getElementById('order-id').value = orderId;
        document.getElementById('status').value = currentStatus;
        
        // Show the modal
        document.getElementById('edit-status-modal').classList.remove('hidden');
    }

    // Close the modal
    function closeEditStatusModal() {
        // Hide the modal
        document.getElementById('edit-status-modal').classList.add('hidden');
    }

    // Example of handling order status update
    document.getElementById('edit-status-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const form = event.target;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success: Show an alert
                alert('Order status updated successfully!');
                // Optionally reload the page
                location.reload();
            } else {
                // Error: Show an alert
                alert('Failed to update order status: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Show an alert in case of network failure
            alert('There was an error updating the order status.');
        });
    });


</script>
<script>
    // Get elements
    const todayFilter = document.getElementById('today');
    const statusFilter = document.getElementById('status');
    const searchBar = document.getElementById('search-bar');
    const orderRows = document.querySelectorAll('.order-row');

    function filterOrders() {
       // Modify this line inside filterOrders function to fix today's date comparison
        const todayChecked = todayFilter.checked;
        const statusValue = statusFilter.value.toUpperCase();
        const searchValue = searchBar.value.toLowerCase();
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;

        // Get today's date in 'YYYY-MM-DD' format
        const todayDate = new Date().toISOString().split('T')[0]; // Get today's date in 'YYYY-MM-DD' format

     orderRows.forEach(row => {
        const orderId = row.cells[0].textContent.trim();
        const userId = row.cells[1].textContent.trim();
        const orderDate = row.cells[2].textContent.trim().split(' ')[0]; // Extracts the date portion 'YYYY-MM-DD'
        const orderStatusText = row.cells[7].querySelector('span') ? row.cells[7].querySelector('span').textContent.trim().toUpperCase() : ''; // Get status text from the <span>

        let showRow = true;

        // Filter by today's orders (comparison now only considers the date part of orderDate)
        if (todayChecked && orderDate !== todayDate) {
            showRow = false;
        }

        // Filter by selected status
        if (statusValue && orderStatusText !== statusValue) {
            showRow = false;
        }

        // Filter by selected date range
        if (startDate && new Date(orderDate) < new Date(startDate)) {
            showRow = false;
        }

        if (endDate && new Date(orderDate) > new Date(endDate)) {
            showRow = false;
        }

        // Filter by search term (orderId or userId)
        if (searchValue && !orderId.toLowerCase().includes(searchValue) && !userId.toLowerCase().includes(searchValue)) {
            showRow = false;
        }

        // Show or hide row based on conditions
        if (showRow) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
     });
    }

    // Add event listeners for the new date range inputs
        document.getElementById('start-date').addEventListener('change', filterOrders);
        document.getElementById('end-date').addEventListener('change', filterOrders);

        // Event listeners for the filters
        todayFilter.addEventListener('change', filterOrders);
        statusFilter.addEventListener('change', filterOrders);
        searchBar.addEventListener('input', filterOrders);

        // Initial filter when the page loads
        filterOrders();

            document.getElementById('clear-filters').addEventListener('click', function() {
        // Reset the date range inputs
        document.getElementById('start-date').value = todayDate; // Set today's date as the start date
        document.getElementById('end-date').value = todayDate; // Set today's date as the end date

        // Reset the other filters (optional)
        document.getElementById('today').checked = false;
        document.getElementById('status').value = '';

        // Reset the search bar
        document.getElementById('search-bar').value = '';

        // Apply the filter function again to refresh the table
        filterOrders();
    });
    document.getElementById('clear-filters').addEventListener('click', function() {
    // Get today's date in Hong Kong timezone
    const today = new Date(); // Current date and time
    const timezoneOffset = 8 * 60; // Hong Kong is UTC+8
    const hkDate = new Date(today.getTime() + timezoneOffset * 60 * 1000); // Adjust to UTC+8
    const localDate = hkDate.toISOString().split('T')[0]; // Format as 'YYYY-MM-DD'

    // Reset the date range inputs to today's date in Hong Kong timezone
    document.getElementById('start-date').value = localDate; // Set start date
    document.getElementById('end-date').value = localDate; // Set end date

    // Reset the other filters (optional)
    document.getElementById('today').checked = false; // Reset today filter
    document.getElementById('status').value = ''; // Reset status filter

    // Reset the search bar
    document.getElementById('search-bar').value = '';

    // Apply the filter function again to refresh the table
    filterOrders();
});


</script>
<script src="{{ asset('js/staff/order-details-modal.js') }}"></script>
</body>
</html>