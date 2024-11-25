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
            background-color: #f8fafc; 
            zoom: 90%;
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
                <li class="py-2 hover:bg-blue-1000 hover:rounded-lg transition-all flex items-center {{ request()->routeIs('dashboard') ? 'bg-white text-black-800' : '' }}" style="border-radius: 20px;">
                    <img src="{{ request()->routeIs('dashboard') ? asset('images/dash11.png') : asset('images/dash1.png') }}" alt="Dashboard Icon" class="w-5 h-5 lg:w-6 lg:h-6 mr-2">
                    <a href="{{ route('dashboard') }}" class="block {{ request()->routeIs('dashboard') ? 'text-black' : 'text-white' }}" style="font-size: 15px;">Dashboard</a>
                </li>
                <li class="py-2 hover:bg-blue-1000 hover:rounded-lg transition-all flex items-center {{ request()->routeIs('calculation') ? 'bg-white text-black-800' : '' }}" style="border-radius: 20px;">
                    <img src="{{ request()->routeIs('calculation') ? asset('images/dash22.png') : asset('images/dash2.png') }}" alt="Inventory Icon" class="w-5 h-5 lg:w-6 lg:h-6 mr-2">
                    <a href="{{ route('calculation') }}" class="block {{ request()->routeIs('calculation') ? 'text-black' : 'text-white' }}" style="font-size: 15px;">Inventory Management</a>
                </li>
                <li class="py-2 hover:bg-blue-1000 hover:rounded-lg transition-all flex items-center {{ request()->routeIs('po.logs') ? 'bg-white text-black-800' : '' }}" style="border-radius: 20px;">
                    <img src="{{ request()->routeIs('po.logs') ? asset('images/dash33.png') : asset('images/dash3.png') }}" alt="Logs Icon" class="w-5 h-5 lg:w-6 lg:h-6 mr-2">
                    <a href="{{ route('po.logs') }}" class="block {{ request()->routeIs('po.logs') ? 'text-black' : 'text-white' }}" style="font-size: 15px;">Activity Logs</a>
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
                    <div class="text-sm text-gray-500">Purchasing Officer</div>
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

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <div id="toast-container" class="fixed bottom-5 right-5 space-y-2 z-50"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let isSoundUnlocked = false;

            // Fetch low stock alerts
            fetch('{{ route("lowStockAlerts") }}')
                .then(response => response.json())
                .then(data => {
                    data.forEach(product => {
                        showToast(`Low stock alert! ${product.product_name} only has ${product.product_stocks} left.`);
                    });
                })
                .catch(error => console.error('Error fetching low stock alerts:', error));
        });

        function showToast(message) {
            // Play the sound

            // Create the toast notification
            const toast = document.createElement('div');
            toast.className = 'bg-red-500 text-white px-4 py-2 rounded shadow flex items-center space-x-2';
            toast.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="text-xl font-bold hover:opacity-70">&times;</button>
            `;

                // Append to the container
                const container = document.getElementById('toast-container');
                container.appendChild(toast);

                // Remove the toast after 5 seconds
                setTimeout(() => {
                    toast.remove();
                }, 5000);
            }
    </script>

    <script src="{{ asset('js/notification-modal.js') }}"></script>

</body>
</html>
