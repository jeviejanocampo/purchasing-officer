{{-- resources/main/main.blade.php --}}

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
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-64 bg-blue-900 text-white">
            <div class="p-6 text-center text-lg font-semibold">
                Admin Sidebar
            </div>
            <nav class="mt-8 flex flex-col items-center md:items-start"> <!-- Centered nav items -->
                <ul class="w-full">
                    <li class="py-2 hover:bg-blue-700 transition-colors">
                        <a href="{{ route('dashboard') }}" class="block text-center">Dashboard</a>
                    </li>
                    <li class="py-2 hover:bg-blue-700 transition-colors">
                        <a href="{{ route('calculation') }}" class="block text-center">Calculation</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 p-6 flex-1 overflow-auto"> <!-- Added margin-left for fixed sidebar -->
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Admin Panel</h1>
            @yield('content')
        </main>
    </div>
</body>
</html>
