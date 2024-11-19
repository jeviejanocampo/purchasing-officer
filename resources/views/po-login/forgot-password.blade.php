<!-- forgot-password.blade.php -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
        <h3 class="text-2xl font-semibold text-center mb-6">Forgot Password</h3>

        <!-- Back Button -->
        <a href="{{ route('login.view') }}" class="text-blue-600 hover:underline mb-4 inline-block">← Back to Login</a>

        <!-- Email Verification Form -->
        <form id="verifyEmailForm">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Enter your email address</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500" placeholder="you@example.com">
            </div>
            <button type="button" id="verifyEmailBtn" class="mt-4 block w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Verify Email</button>
        </form>

        <!-- New Password Form (hidden initially) -->
        <div id="newPasswordForm" style="display: none;">
            <form id="resetPasswordForm">
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" id="new_password" name="new_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500" placeholder="••••••••">
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-500" placeholder="••••••••">
                </div>
                <button type="submit" class="mt-6 block w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Reset Password</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Simulate email verification for the demo
    document.getElementById('verifyEmailBtn').addEventListener('click', function() {
        const email = document.getElementById('email').value;
        if (email) {
            // Simulating email verification
            alert('Email verified successfully!');

            // Hide the email verification form and show the new password form
            document.getElementById('verifyEmailForm').style.display = 'none';
            document.getElementById('newPasswordForm').style.display = 'block';
        } else {
            alert('Please enter a valid email.');
        }
    });

    // Handle the password reset form submission
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const email = document.getElementById('email').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Make sure the passwords match
        if (newPassword !== confirmPassword) {
            alert('Passwords do not match.');
            return;
        }

        // Send the data to the server to reset the password
        fetch("{{ route('reset.password') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                email: email,
                new_password: newPassword,
                new_password_confirmation: confirmPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Show success message
                window.location.href = "{{ route('login.view') }}"; // Redirect to login page
            } else {
                alert(data.message); // Show error message
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again later.');
        });
    });
</script>
