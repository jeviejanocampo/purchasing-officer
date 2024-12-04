<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Customers</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

<div id="view-customers-section" class="section bg-white rounded-lg shadow-md p-5">
    <h2 class="text-xl font-bold mb-5">Customer Details</h2>

    <!-- Search Bar -->
    <div class="mb-4">
        <input 
            type="text" 
            id="searchInput" 
            placeholder="Search by User ID or Full Name..." 
            class="w-full p-2 border rounded"
        >
    </div>

    <table class="min-w-full table-auto" id="customers-table">
        <thead>
            <tr>
                <th class="px-4 py-2 border">User ID</th>
                <th class="px-4 py-2 border">Full Name</th>
                <th class="px-4 py-2 border">Email</th>
                <th class="px-4 py-2 border">Phone Number</th>
                <th class="px-4 py-2 border">Birth Date</th>
                <th class="px-4 py-2 border">Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="px-4 py-2 border">{{ $user->user_id }}</td>
                    <td class="px-4 py-2 border">{{ $user->user_fullname }}</td>
                    <td class="px-4 py-2 border">{{ $user->user_email }}</td>
                    <td class="px-4 py-2 border">{{ $user->user_number }}</td>
                    <td class="px-4 py-2 border">{{ $user->user_bdate }}</td>
                    <td class="px-4 py-2 border">{{ $user->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 border text-center">No active customers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#customers-table tbody tr');

        rows.forEach(row => {
            const userId = row.cells[0].textContent.toLowerCase();
            const fullName = row.cells[1].textContent.toLowerCase();

            if (userId.includes(filter) || fullName.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
