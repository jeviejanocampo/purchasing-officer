<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchasing Officer</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <style>
        body {
            zoom: 90%;
            background-color: #f8fafc; 
        }
        .sidebar {
            background-color: #010224; 
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 50;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            max-height: 80%;
            overflow-y: auto;
            border-radius: 8px;
        }
        .notification-bar {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
        }
        
        /* Hide header when printing */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100" style="font-family: 'Lato', sans-serif;">
    <div class="flex flex-col min-h-screen lg:flex-row">
    @php
        $userId = session('user_id');
        $user = \App\Models\User::find($userId); 
    @endphp

    <aside class="sidebar text-white w-full lg:w-56 lg:fixed lg:inset-y-0 lg:left-0 p-4 lg:p-6 lg:text-lg">

        <div class="text-center mb-6">
            <img src="{{ asset('images/mstinio-logo.jpg') }}" alt="Profile Image" class="w-17 h-17 rounded-full mx-auto mb-2"> 
        </div>

        <nav>
            <ul class="flex flex-col space-y-2 lg:space-y-4 lg:block text-center lg:text-left">
                <h1 style="color:white; font-size:20">PAGES</h1>
                <!-- <li class="py-2 hover:bg-blue-1000 hover:rounded-lg transition-all flex items-center {{ request()->routeIs('calculation') ? 'bg-white text-black-800' : '' }}" style="border-radius: 20px;">
                    <img src="{{ request()->routeIs('calculation') ? asset('images/dash22.png') : asset('images/dash2.png') }}" alt="Inventory Icon" class="w-5 h-5 lg:w-6 lg:h-6 mr-2">
                    <a class="block {{ request()->routeIs('calculation') ? 'text-black' : 'text-white' }}" style="font-size: 15px;">Dashboard</a>
                </li> -->
                <li class="py-2 hover:bg-blue-1000 hover:rounded-lg transition-all flex items-center {{ request()->routeIs('view-orders') ? 'bg-white text-black-800' : '' }}" style="border-radius: 20px;">
                    <img src="{{ request()->routeIs('view-orders') ? asset('images/dash22.png') : asset('images/dash2.png') }}" alt="Inventory Icon" class="w-5 h-5 lg:w-6 lg:h-6 mr-2">
                    <a href="{{ route('view-orders') }}" class="block {{ request()->routeIs('view-orders') ? 'text-black' : 'text-white' }}" style="font-size: 15px;">View Orders</a>
                    
                    <!-- Dynamic Badge for Pending Orders -->
                    @if($pendingOrdersCount > 0)
                        <span class="ml-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full flex items-center justify-center" style="width: 20px; height: 20px;">
                            {{ $pendingOrdersCount }}
                        </span>
                    @endif  
                </li>

                <li class="py-2 hover:bg-blue-1000 hover:rounded-lg transition-all flex items-center {{ request()->routeIs('staff.logs') ? 'bg-white text-black-800' : '' }}" style="border-radius: 20px;">
                    <img src="{{ request()->routeIs('staff.logs') ? asset('images/dash33.png') : asset('images/dash3.png') }}" alt="Logs Icon" class="w-5 h-5 lg:w-6 lg:h-6 mr-2">
                    <a href="{{ route('staff.logs') }}" class="block {{ request()->routeIs('staff.logs') ? 'text-black' : 'text-white' }}" style="font-size: 15px;">Activity Logs</a>
                </li>
                <li class="py-2 hover:bg-blue-1000 hover:rounded-lg transition-all flex items-center {{ request()->routeIs('logout') ? 'bg-white text-black-800' : '' }}" style="border-radius: 20px;">
                    <img src="{{ request()->routeIs('logout') ? asset('images/dash44.png') : asset('images/dash4.png') }}" alt="Logout Icon" class="w-5 h-5 lg:w-6 lg:h-6 mr-2">
                    <a href="#" onclick="event.preventDefault(); confirmLogout();" class="block {{ request()->routeIs('logout') ? 'text-black' : 'text-white' }}" style="font-size: 15px;">Logout</a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="pt-20 p-6 flex-1 lg:ml-56">
        <!-- Sticky Header with shadow and Notifications text on top-left -->
        <header class="bg-white shadow-md fixed top-0 left-0 right-0 p-4 z-0 lg:ml-56 flex items-center h-12 no-print"  
        style="border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;">
            <h1 class="text-xl font-semibold text-gray-800"></h1>
            <!-- Notification Bar Icon on the Top Right -->
            <div class="flex items-center space-x-4 ml-auto">
                <div class="text-right">
                    <div class="font-semibold">{{ $user->name ?? 'Guest' }}</div>
                    <div class="text-sm text-gray-500">Staff Officer</div>
                </div>
                <img src="{{ asset('images/po.png') }}" alt="Profile Image" class="w-8 h-8 rounded-full">
            </div>
            <!-- <a href="#" onclick="event.preventDefault(); showNotificationModal();" class="notification-bar">
                <img src="{{ asset('images/notification-bar.png') }}" alt="Notifications" class="w-8 h-8">
            </a> -->
        </header>

        @yield('content')
        @include('dashboard-components.notifications')
    </main>

    <form id="logout-form" action="{{ route('staff.logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <div id="toast-container" class="fixed bottom-5 right-5 space-y-2 z-50"></div>

    
<!-- <script>
     // Add this in your blade template or separate JS file
    setInterval(function() {
        $.ajax({
            url: '/check-for-pending-orders',
            method: 'GET',
            success: function(response) {
                if (response.newPendingOrders) {
                    console.log('New PENDING orders detected! Refreshing page...');
                    location.reload();  // Refresh the page if there are new orders
                } else {
                    console.log('No new PENDING orders.');
                }
            },
            error: function(error) {
                console.log('Error in AJAX request:', error);
            }
        });
    }, 60000);  // 1 minute interval


</script> -->
<!-- Floating Refresh Button -->


<!-- JavaScript to Refresh the Page -->
<script>
    // Function to refresh the page
    function refreshPage() {
        location.reload();  // This will refresh the page
    }

    // Listen for 'Enter' key press
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            refreshPage();  // Trigger refresh when Enter is pressed
        }
    });
</script>


<script>
        document.addEventListener('DOMContentLoaded', () => {
            let lastChecked = new Date().toISOString(); // Initialize with the current timestamp
            const displayedOrderIds = new Set(JSON.parse(localStorage.getItem('displayedOrderIds')) || []); // Load displayed orders from localStorage

            // Polling function to fetch new pending orders from the server
            function fetchPendingOrders() {
                fetch('{{ route("pendingOrders") }}?last_checked=' + encodeURIComponent(lastChecked))
                    .then(response => response.json())
                    .then(data => {
                        let newOrderFound = false;

                        data.forEach(order => {
                            if (!displayedOrderIds.has(order.order_id)) {
                                // If the order is new (not displayed), show the toast
                                showToast(`New order placed! Order ID: ${order.order_id}, Status: ${order.order_status}.`);
                                displayedOrderIds.add(order.order_id); // Mark this order as displayed
                                newOrderFound = true;
                            }
                        });

                        // Save the updated displayedOrderIds to localStorage
                        localStorage.setItem('displayedOrderIds', JSON.stringify(Array.from(displayedOrderIds)));

                        // Update the lastChecked timestamp to the current time
                        lastChecked = new Date().toISOString();

                        // Play the sound only if a new order was found
                        if (newOrderFound) {
                            playTingSound();  // Play sound immediately
                        }
                    })
                    .catch(error => console.error('Error fetching pending orders:', error));
            }

            // Run the polling function every 10 seconds
            // setInterval(fetchPendingOrders, 10000); // 10000ms = 10 seconds

            // Run the function once immediately after the page loads
            fetchPendingOrders();
        });

        // Function to show toast notification
        function showToast(message) {
            // Create the toast element
            const toast = document.createElement('div');
            toast.className = 'bg-green-500 text-white px-4 py-2 rounded shadow flex items-center space-x-2';
            toast.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="text-xl font-bold hover:opacity-70">&times;</button>
            `;

            // Append the toast to the container
            const container = document.getElementById('toast-container');
            container.appendChild(toast);

            // Remove the toast after 5 seconds and refresh the page
            setTimeout(() => {
                toast.remove();
                window.location.reload(); // Automatic page refresh after toast disappears
            }, 5000);
        }

        // Function to play the ting sound immediately
        function playTingSound() {
            const audio = new Audio('{{ asset("sounds/ting.mp3") }}');
            audio.play().catch(error => {
                console.error("Error playing sound:", error);
            });
        }
</script>



    <script src="{{ asset('js/notification-modal.js') }}"></script>

</body>
</html>
