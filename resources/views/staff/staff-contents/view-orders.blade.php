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

    <!-- Two-column layout -->
    <div class="grid grid-cols-12 gap-4">
        <!-- Left Column (Table) -->
        <div class="col-span-11">
            <!-- Filters Section -->
            <div class="mb-4">
                <!-- Filter by Today's Orders -->
                <label for="today" class="mr-2 hidden">Today's Orders</label>
                <input type="checkbox" id="today" class="hidden"/>

                <label for="filter-date" class="mr-3">Filter Status</label>
                <select id="status" class="border p-2">
                    <option value="">All</option>
                    <option value="Approved">Approved</option>
                    <option value="Returned">Returned</option>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Cancelled">Cancelled</option>
                </select>

                <label for="filter-date" class="ml-4">Search</label>
                <input type="text" id="search-bar" placeholder="Search by Order ID or User ID" class="border rounded-lg  p-2 ml-4 mr-4 w-60" />

                    <!-- Date Filter -->
                    <label for="filter-date" class="mr-2">Select Date</label>
                    <input type="date" id="filter-date" class="border p-2" />



                    <button id="clear-filter-btn" type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg ml-4 mr-4">
                        Clear Filter
                    </button>


                    <label for="dropdown" class="mr-2">Select Option</label>
                    <select id="dropdown" class="border p-2">
                        <option value="1">Purchasing Officer 1</option>
                        <option value="2">Purchasing Officer 2 </option>
                    </select>

                    <!-- Contact Button -->
                    <button type="button" id="contact-btn" class="px-4 py-2 bg-blue-500 text-white rounded-lg ml-4" onclick="showContactAlert()">
                        Contact
                    </button>
                    <script>
                        // Function to show alert when Contact button is clicked
                        function showContactAlert() {
                            // Get the selected value from the dropdown
                            var selectedValue = document.getElementById("dropdown").value;
                            
                            // Set a different number for each option
                            var contactNumber = '';
                            if (selectedValue === '1') {
                                contactNumber = '09453244521'; // Number for Option 1
                            } else if (selectedValue === '2') {
                                contactNumber = '09567893456'; // Number for Option 2
                            }
                            
                            // Show alert with the selected value and contact number
                            alert('Contact this person to request restocking ' + selectedValue + '. Contact Number: ' + contactNumber);
                        }
                    </script>

                  
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
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $order->order_date }}
                                <br>
                                <small style="color: red; font-weight:500">
                                Order Placed {{ \Carbon\Carbon::parse($order->order_date)->diffForHumans() }}
                                </small>
                            </td>
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
                                @elseif ($order->order_status == 'Approved')
                                    <span class="text-green-700">Approved</span>
                                @else
                                    <span class="text-gray-500">Unknown</span>
                                @endif
                                <button onclick="openEditStatusModal('{{ $order->order_id }}', '{{ $order->order_status }}')" 
                                        class="ml-2 text-blue-500 hover:text-blue-700 flex items-center bg-green-100 hover:bg-green-200 rounded-lg px-3 py-1 transition-all">
                                    <i class="fas fa-pencil-alt mr-2"></i> Edit Status
                                    <i class="fas fa-chevron-down ml-2"></i> <!-- Downward arrow -->
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

        <!-- Right Column (Order Count) -->
        <div id="overview-section" class="col-span-1 p-5 rounded-lg shadow-md" style="font-size: 12px;">
                <h3 class="text-s font-bold mb-4" style="font-size: 12px;">OVERVIEW</h3>
                <div class="mb-4">
                    <p class="text-sm font-semibold text-green-800" style="font-size: 12px;">Orders Today</p>
                    <p id="orders-today" class="text-lg">0</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm font-semibold text-red-800" style="font-size: 12px;">Pending Orders</p>
                    <p id="pending-orders" class="text-lg">0</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm font-semibold text-yellow-500" style="font-size: 12px;">Delivered (To Received Status)</p>
                    <p id="delivered-orders" class="text-lg">0</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm font-semibold" style="font-size: 12px;">Cancelled</p>
                    <p id="cancelled-orders" class="text-lg">0</p>
                </div>
            </div>
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
    // Function to filter orders by the selected date
    function filterByDate() {
        var selectedDate = document.getElementById('filter-date').value;
        var rows = document.querySelectorAll('#orders-table .order-row');

        // If no date is selected, show all orders
        if (!selectedDate) {
            rows.forEach(function(row) {
                row.style.display = '';  // Show all rows if no filter is applied
            });
        } else {
            rows.forEach(function(row) {
                var orderDateTime = row.querySelector('.order-id').dataset.orderDate; // Get the order date-time from data attribute
                var orderDate = orderDateTime.split(' ')[0]; // Get only the date part (YYYY-MM-DD)
                
                // If the order's date matches the selected date, show the row; otherwise, hide it
                if (orderDate === selectedDate) {
                    row.style.display = ''; // Show this row
                } else {
                    row.style.display = 'none'; // Hide this row
                }
            });
        }
    }

    // Event listener for the date filter change
    document.getElementById('filter-date').addEventListener('change', function() {
        filterByDate();
    });

    // Function to clear the date filter
    function clearDateFilter() {
        document.getElementById('filter-date').value = '';  // Reset the date input
        filterByDate();  // Call the filter function to show all rows
    }

    // Event listener for the clear filter button
    document.getElementById('clear-filter-btn').addEventListener('click', function() {
        clearDateFilter();
    });

</script>
<script>
            document.addEventListener('DOMContentLoaded', function () {
                fetch('/orders/overview')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('orders-today').textContent = data.orders_today;
                        document.getElementById('pending-orders').textContent = data.pending_orders;
                        document.getElementById('delivered-orders').textContent = data.delivered_orders;
                        document.getElementById('cancelled-orders').textContent = data.cancelled_orders;
                    })
                    .catch(error => console.error('Error fetching data:', error));
            });
        </script>
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
        const todayChecked = todayFilter.checked;
        const statusValue = statusFilter.value.toUpperCase();
        const searchValue = searchBar.value.toLowerCase();

        // Get today's date in 'YYYY-MM-DD' format
        const todayDate = new Date().toISOString().split('T')[0];

        orderRows.forEach(row => {
            const orderId = row.cells[0].textContent.trim();
            const userId = row.cells[1].textContent.trim();
            const orderDate = row.cells[2].textContent.trim().split(' ')[0]; // Extracts the date portion 'YYYY-MM-DD'
            const orderStatusText = row.cells[7].querySelector('span') 
                ? row.cells[7].querySelector('span').textContent.trim().toUpperCase() 
                : ''; // Get status text from the <span>

            let showRow = true;

            // Filter by today's orders (comparison now only considers the date part of orderDate)
            if (todayChecked && orderDate !== todayDate) {
                showRow = false;
            }

            // Filter by selected status
            if (statusValue && orderStatusText !== statusValue) {
                showRow = false;
            }

            // Filter by search term (orderId or userId)
            if (searchValue && !orderId.toLowerCase().includes(searchValue) && !userId.toLowerCase().includes(searchValue)) {
                showRow = false;
            }

            // Show or hide row based on conditions
            row.style.display = showRow ? '' : 'none';
        });
    }

    // Event listeners for the filters
    todayFilter.addEventListener('change', filterOrders);
    statusFilter.addEventListener('change', filterOrders);
    searchBar.addEventListener('input', filterOrders);

    // Initial filter when the page loads
    filterOrders();
</script>

<script src="{{ asset('js/staff/order-details-modal.js') }}"></script>
</body>
</html>