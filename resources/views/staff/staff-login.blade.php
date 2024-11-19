{{-- resources/views/po-login/po-login.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        body {
            background-color: #f8fafc;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-md p-8 w-110">
        <h2 class="text-2xl font-bold text-center mb-6">Staff Officer Portal</h2>
        
        <div class="flex justify-center mb-4">
            <button onclick="showForm('pin-form')" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">Login with PIN</button>
            <button onclick="showForm('signup-form')" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300 ml-2">Sign Up</button>
        </div>

        <!-- Email and Password Form -->
        <div id="email-password-form" style="display:none;">
            <h3 class="text-lg font-semibold mb-2">Login using Email and Password</h3>
            <form>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500" placeholder="you@example.com">
                </div>
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500" placeholder="••••••••">
                </div>
                <a href="/main" class="mt-6 block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300">Login</a>
            </form>
        </div>
        
        <!-- PIN Form -->
        <div id="pin-form" style="display:none;">
            <h3 class="text-lg font-semibold mb-2">Login using PIN</h3>
            <form onsubmit="loginWithPin(event)">
                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700">PIN</label>
                    <input type="password" name="pin" id="pin" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500" placeholder="Enter your PIN">
                </div>
                <button type="submit" class="mt-6 block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300">Login</button>
                <p class="mt-2 text-sm text-blue-600 cursor-pointer hover:underline">Forgot PIN?</p>
            </form>
        </div>

        <!-- Signup Form -->
        <div id="signup-form" style="display:none;">
            <h3 class="text-lg font-semibold mb-2">Sign Up</h3>
            <form id="signupForm" onsubmit="submitSignupForm(event)">
                @csrf
                <div>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500">
                </div>
                <div class="mt-4">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500">
                </div>
                <div class="mt-4">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500">
                </div>
                <div class="mt-4">
                    <label for="pin">PIN (5 digits):</label>
                    <input type="text" id="pin" name="pin" required pattern="\d{5}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500">
                </div>

                <!-- Hidden field for default role 'staff' -->
                <input type="hidden" name="role" value="staff">

                <button type="submit" class="mt-6 block w-full text-center bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-300">Sign Up</button>
            </form>
        </div>
    </div>

    <script>
        function showForm(formId) {
            // Hide all forms
            document.getElementById('email-password-form').style.display = 'none';
            document.getElementById('pin-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'none';

            // Show selected form
            document.getElementById(formId).style.display = 'block';
        }

        async function submitSignupForm(event) {
            event.preventDefault();

            const formData = new FormData(document.getElementById('signupForm'));

            try {
                const response = await fetch("{{ route('staff-signup') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    swal("Success", result.message, "success").then(() => {
                        window.location.href = "{{ route('staff.home.main') }}";
                    });
                } else {
                    swal("Error", result.message || 'An error occurred.', "error");
                }
            } catch (error) {
                console.error('Error:', error);
                swal("Error", 'An error occurred. Please try again.', "error");
            }
        }

        async function loginWithPin(event) {
        event.preventDefault();

        const pin = document.getElementById('pin').value;

        try {
            const response = await fetch("{{ route('login.pin') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ pin })
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect; // This will now redirect to the correct staff-main page
            } else {
                swal("Error", result.message || 'Invalid PIN', "error");
            }
        } catch (error) {
            console.error('Error:', error);
            swal("Error", 'An error occurred. Please try again.', "error");
        }
    }
    </script>
</body>
</html>
