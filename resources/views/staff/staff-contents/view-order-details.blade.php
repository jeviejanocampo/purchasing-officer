<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
@extends('staff.home.staff-main')

@section('content')
<h1 style="font-size: 36px; margin-bottom: 20px; font-weight:bold">Order Details</h1>
<div class="container mx-auto p-5 bg-white rounded-lg">
    <button onclick="history.back()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors duration-300 mb-4">
        ← Back to Table
    </button>
    
    <!-- Display the Order ID with a status dropdown -->
    @if (isset($order_id))
        <div class="pb-4 mb-4 text-sm">
            <div class="flex justify-between items-center">
                <div>
                    <span style="font-size: 22px; font-weight:bold">Order ID:#</span>
                    <span class="font-semibold" style="font-size: 22px;">{{ $order_id }}</span>
                </div>
                <!-- Dropdown for changing status (UI only) -->
                <button onclick="openStatusModal()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none">
                    Change Status
                </button>

                <!-- Modal UI -->
                <div id="statusModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white p-6 rounded-lg w-96">
                        <h3 class="text-lg font-semibold mb-4">Update Order Status</h3>
                        <div class="flex items-center space-x-2">
                            <label for="order-status" class="text-sm">Select Status:</label>
                            <select id="order-status" class="bg-gray-100 text-sm p-2 border rounded-md">
                                <option value="In Process">In Process</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button onclick="updateOrderStatus()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mr-2">Update</button>
                            <button onclick="closeStatusModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($checkoutDetails->isNotEmpty())
        <div class="pb-4 mb-4 border-b text-sm">
            <div>
                <span style="font-size: 18px;">Date Ordered:</span>
                <span style="font-size: 18px; font-weight:bold">{{ $checkoutDetails->first()->created_at }}</span>
            </div>
        </div>
    @endif

    @if (isset($message))
        <p class="text-red-500">{{ $message }}</p>
    @else
        <div class="pb-4 mb-4 border-b grid grid-cols-2 gap-10">
            <!-- User Details Section with Labels -->
            <div class="col-span-1">
                <h3 class="text-xl mb-4">Customer Details</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span>Full Name:</span>
                        <span class="font-semibold">{{ $user->user_fullname }}</span>
                    </div>  
                    <div class="flex justify-between">
                        <span>Email:</span>
                        <span class="font-semibold">{{ $user->user_email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phone Number:</span>
                        <span class="font-semibold">{{ $user->user_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Birthdate:</span>
                        <span class="font-semibold">{{ $user->user_bdate }}</span>
                    </div>
                </div>
            </div>


            <!-- Delivery Address Section -->
            <div class="col-span-1">
                <h3 class="text-xl mb-4">Delivery Address Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span>Full Name:</span>
                        <span class="font-semibold">{{ $deliveryAddress->d_full_name }}</span>
                    </div>    
                    <div class="flex justify-between">
                        <span>Contact Number:</span>
                        <span class="font-semibold">{{ $deliveryAddress->d_contact_number }}</span>
                    </div>    
                    <div class="flex justify-between">
                        <span>Address:</span>
                        <span class="font-semibold">{{ $deliveryAddress->d_address }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Postal Code:</span>
                        <span class="font-semibold">{{ $deliveryAddress->d_postal_code }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span>Latitude:</span>
                            <span class="font-semibold ml-2" id="latitude">{{ $deliveryAddress->u_latitude }}</span>
                        </div>
                        <button class="ml-2 text-white bg-blue-500 hover:bg-blue-600 active:bg-blue-700 px-4 py-2 rounded" onclick="copyToClipboard('latitude')">Copy</button>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span>Longitude:</span>
                            <span class="font-semibold ml-2" id="longitude">{{ $deliveryAddress->u_longitude }}</span>
                        </div>
                        <button class="ml-2 text-white bg-blue-500 hover:bg-blue-600 active:bg-blue-700 px-4 py-2 rounded" onclick="copyToClipboard('longitude')">Copy</button>
                    </div>

                    <div class="flex justify-between">
                        <span>Status:</span>
                        <span class="font-semibold">{{ $deliveryAddress->d_status }}</span>
                    </div>
                </div>
            </div>

        </div>  

        <h1 style="font-size: 26px; margin-bottom: 20px;">Product Details</h1>

        @foreach($checkoutDetails as $detail)
        <div class="pb-4 mb-4">
        <table class="table-auto w-full border-collapse">
            <thead class="bg-gray-100">
                <tr class="text-sm">
                    <th class="font-semibold px-2 py-1">Order Details ID</th>
                    <th class="font-semibold px-2 py-1">Product Name</th>
                    <th class="font-semibold px-2 py-1">Quantity</th>
                    <th class="font-semibold px-2 py-1">Retail Price</th>
                    <th class="font-semibold px-2 py-1">Product Name</th>
                    <th class="font-semibold px-2 py-1">Description</th>
                    <!-- <th class="font-semibold px-2 py-1">Stock</th> -->
                    <th class="font-semibold px-2 py-1">Expiry Date</th>
                    <th class="font-semibold px-2 py-1">Status</th>
                    <th class="font-semibold px-2 py-1">Product Image</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-sm">
                    <td class=" px-2 py-1">{{ $detail->checkout_details_id }}</td>
                    <td class=" px-2 py-1">{{ $detail->product_name }}</td>
                    <td class=" px-2 py-1">{{ $detail->product_quantity }}</td>
                    <td class=" px-2 py-1">PHP {{ number_format($detail->product_price, 2) }}</td>
                    <td class=" px-2 py-1">{{ $detail->product_name }}</td>
                    <td class=" px-2 py-1">{{ $detail->product_description }}</td>
                    <!-- <td class=" px-2 py-1">{{ $detail->product_stocks }}</td> -->
                    <td class=" px-2 py-1">{{ $detail->product_expiry_date }}</td>
                    <td class=" px-2 py-1">{{ $detail->product_status }}</td>
                    <td class=" px-2 py-1">
                        <img src="{{ $detail->product_image }}" alt="{{ $detail->product_name }}" class="w-32 h-32 object-cover">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
        @endforeach
    @endif
</div>


<div class="webview-container" style="width: 100%; height: 500px; overflow: hidden; margin-top: 20px  rounded-lg">
<h1 style="font-size: 36px; margin-bottom: 20px; font-weight:bold">Check Location</h1>
    <iframe src="https://www.gps-coordinates.net/" style="width: 100%; height: 100%; border: none;" title="GPS Coordinates Webview"></iframe>
</div>

@endsection
<script>
                        function copyToClipboard(id) {
                            const textToCopy = document.getElementById(id).innerText;
                            navigator.clipboard.writeText(textToCopy).then(() => {
                                alert(id.charAt(0).toUpperCase() + id.slice(1) + " copied to clipboard!");
                            }).catch(err => {
                                alert("Error copying text: " + err);
                            });
                        }
                    </script>

<script>
    let currentOrderId = '{{ $order_id }}'; // Make sure this is dynamically passed from the backend

    // Open the modal
    function openStatusModal() {
        const modal = document.getElementById('statusModal');
        modal.classList.remove('hidden');
    }

    // Close the modal
    function closeStatusModal() {
        const modal = document.getElementById('statusModal');
        modal.classList.add('hidden');
    }

    // Update the order status
    function updateOrderStatus() {
        const status = document.getElementById('order-status').value; // Get the selected status

        if (!currentOrderId) {
            alert('No order selected');
            console.log('Error: No order selected');
            return;
        }

        // Get CSRF token from the meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('CSRF Token:', csrfToken);
        console.log('Updating status for Order ID:', currentOrderId, 'Status:', status);

        // Send the updated status to the server
        fetch(`/update-order-status/${currentOrderId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Ensure CSRF token is sent here
            },
            body: JSON.stringify({
                order_status: status
            })
        })
        .then(response => {
            console.log('Response Status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response Data:', data);
            if (data.success) {
                alert('Order status updated successfully');
                location.reload(); // This reloads the current page
                closeStatusModal(); // Close the modal after updating status
                
                // Optionally refresh the page to reflect changes
                location.reload();
            } else {
                alert('Error updating order status');
                console.log('Error: ', data.error || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error updating order status:', error);
            alert('Error updating order status.');
        });
    }
</script>

</body>
</html>