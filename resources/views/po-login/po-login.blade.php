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
            background-color: #f8fafc; /* Light background color */
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        function toggleForm(formType) {
            document.getElementById('email-password-form').style.display = formType === 'email' ? 'block' : 'none';
            document.getElementById('pin-form').style.display = formType === 'pin' ? 'block' : 'none';
            document.getElementById('signup-form').style.display = formType === 'signup' ? 'block' : 'none';
        }

        function generatePin() {
            const pin = Math.floor(10000 + Math.random() * 90000); // Generates a 5-digit random number
            document.getElementById('generated-pin').value = pin;
        }

        // Function to confirm before submission
        function confirmSubmission() {
            const confirmation = confirm("Confirm details before proceeding? Yes or No");
            if (confirmation) {
                submitRegistration();
            } else {
                console.log("User canceled the registration.");
            }
        }

        // Directly submit the signup form without confirmation and log the action
        function submitRegistration() {
            console.log("Submit button clicked. Preparing to submit the registration form.");
            document.getElementById('signup-form-element').submit();
            alert("Registration successful! Please wait for confirmation from admin to access your portal.");
            console.log("Form submitted.");
        }
    </script>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-md p-8 w-110">
        <h2 class="text-2xl font-bold text-center mb-6">Purchasing Officer Portal</h2>
        
        <div class="flex justify-center mb-4">
            <!-- <button onclick="toggleForm('email')" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300 mr-2">Login with Email</button> -->
            <button onclick="toggleForm('pin')" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">Login with PIN</button>
            <button onclick="toggleForm('signup')" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300 ml-2">Sign Up</button>
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
            <form action="{{ route('pin.login') }}" method="POST" id="pinLoginForm">
                @csrf
                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700">PIN</label>
                    <input type="password" name="pin" id="pin" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500" placeholder="Enter your PIN">
                </div>
                <button type="submit" class="mt-6 block w-full text-center bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300">Login</button>
            </form>

            @if(session('error'))
                <script>
                    swal("Error!", "{{ session('error') }}", "error");
                </script>
            @endif
        </div>




        <!-- Signup Form -->
        <div id="signup-form" style="display:none;">
            @if(session('success'))
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <h3 class="text-lg font-semibold mb-2">Sign Up</h3>
            <form action="{{ route('register') }}" method="POST" id="signup-form-element">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-green-500" placeholder="Full Name">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-green-500" placeholder="you@example.com">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-green-500" placeholder="••••••••">
                </div>
                <div class="mb-4">
                    <label for="generated-pin" class="block text-sm font-medium text-gray-700">PIN</label>
                    <div class="flex">
                        <input type="text" name="pin" id="generated-pin" readonly class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-green-500" placeholder="Generated PIN">
                        <button type="button" onclick="generatePin()" class="ml-2 bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300">Generate PIN</button>
                    </div>
                </div>
                <button type="button" onclick="confirmSubmission()" class="mt-6 w-full text-center bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-300">Confirm Register</button>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/pinlogin-alert.js') }}"></script>
</body>
</html>
