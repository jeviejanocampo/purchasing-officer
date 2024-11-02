<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> <!-- Link Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <style>
        body {
            background-color: #f8fafc; /* Light background color for the body */
        }
        .sidebar {
            background-color: #213A57; /* Sidebar background color */
        }
    </style>
</head>
<body class="bg-gray-100" style="font-family: 'Roboto', sans-serif;">

    <div class="flex flex-col min-h-screen lg:flex-row">
        <aside class="sidebar text-white w-full lg:w-64 lg:fixed lg:inset-y-0 lg:left-0 p-4 lg:p-6 lg:text-lg">
            <div class="text-center mb-10">
                Admin Sidebar
            </div>
            <nav>
                <ul class="flex flex-col space-y-2 lg:space-y-4 lg:block text-center lg:text-left">
                    <li class="py-2 hover:bg-blue-700 transition-colors">
                        <a href="{{ route('dashboard') }}" class="block">Dashboard</a>
                    </li>
                    <li class="py-2 hover:bg-blue-700 transition-colors">
                        <a href="{{ route('calculation') }}" class="block">Stock Procurement</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="p-6 flex-1 lg:ml-64">
            @yield('content')
        </main>
    </div>
</body>
</html>
