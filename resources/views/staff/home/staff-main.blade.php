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
            <img src="{{ asset('images/purchasing-officer.png') }}" alt="Profile Image" class="w-14 h-14 rounded-full mx-auto mb-2"> 
            <div class="font-semibold">{{ $user->name ?? 'Guest' }}</div>
            <div class="text-center mb-10">Staff Officer</div>
        </div>

        <nav>
            <ul class="flex flex-col space-y-2 lg:space-y-4 lg:block text-center lg:text-left">
                <li class="py-2 hover:bg-blue-700 transition-colors">
                    <a class="block">Dashboard</a>
                </li>
                <li class="py-2 hover:bg-blue-700 transition-colors">
                <a href="{{ route('view-orders') }}" class="block">View Orders</a>
                </li>
                <li class="py-2 hover:bg-blue-700 transition-colors">
                    <a  class="block">Activity Logs</a>
                </li>
                <li class="py-2 hover:bg-blue-700 transition-colors">
                    <a href="#" onclick="event.preventDefault(); confirmLogout();" class="block">Logout</a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="pt-20 p-6 flex-1 lg:ml-56">
        <!-- Sticky Header with shadow and Notifications text on top-left -->
        <header class="bg-white shadow-md fixed top-0 left-0 right-0 p-4 z-0 lg:ml-56 flex items-center h-12 no-print">
            <h1 class="text-xl font-semibold text-gray-800"></h1>
            <!-- Notification Bar Icon on the Top Right -->
            <a href="#" onclick="event.preventDefault(); showNotificationModal();" class="notification-bar">
                <img src="{{ asset('images/notification-bar.png') }}" alt="Notifications" class="w-8 h-8">
            </a>
        </header>

        @yield('content')
        @include('dashboard-components.notifications')
    </main>

    <form id="logout-form" action="{{ route('staff.logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <div id="toast-container" class="fixed bottom-5 right-5 space-y-2 z-50"></div>

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
                        playTingSound();
                    }
                })
                .catch(error => console.error('Error fetching pending orders:', error));
        }

        // Run the polling function every 10 seconds
        setInterval(fetchPendingOrders, 10000); // 10000ms = 10 seconds

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

        // Remove the toast after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    // Function to play the ting sound
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
