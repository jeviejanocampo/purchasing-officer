<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Logs</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
@extends('main.main') 

@section('content')
    <div class="container mx-auto p-5">
        <h1 class="text-3xl font-bold mb-4">Purchase Officer Logs</h1>

        <!-- Search bar, date filter, and category filter section -->
        <div class="mb-5 flex justify-between items-center">
            <input type="text" id="search-input" placeholder="Search logs..." class="p-2 border border-gray-300 rounded-md shadow-sm">
            <div class="flex space-x-2 items-center">
                <input type="date" id="start-date" class="p-2 border border-gray-300 rounded-md shadow-sm">
                <input type="date" id="end-date" class="p-2 border border-gray-300 rounded-md shadow-sm">
                <select id="category-filter" class="p-2 bg-white border border-gray-300 rounded-md shadow-sm">
                    <option value="">Select Category</option>
                    <option value="budget_created" data-keywords="Budget created">Budget Created</option>
                    <option value="inventory" data-keywords="Creating inventory item,product_name,total_cost">Inventory</option>
                    <option value="product_added" data-keywords="Product added,Product added and budget updated">Product Added</option>
                </select>
                <button id="clear-button" class="p-2 bg-gray-500 text-white rounded-md shadow-sm">Clear</button>
                <button id="show-all-button" class="p-2 bg-gray-700 text-white rounded-md shadow-sm">Show All</button>
            </div>
        </div>

        @if(session('user_id'))
            <p class="hidden" id="user-id">Your User ID: {{ session('user_id') }}</p>
        @else
            <p class="hidden">You are not logged in.</p>
        @endif  

        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm">
                    <th class="p-4 border-b">ID</th>
                    <th class="p-4 border-b">Log Data</th>
                    <th class="p-4 border-b">Created At</th>
                    <th class="p-4 border-b">Updated At</th>
                </tr>
            </thead>
            <tbody id="logs-table">
                @foreach($logs as $log)
                    <tr class="log-row hover:bg-gray-100">
                        <td class="p-4 border-b text-center">{{ $log->id }}</td>
                        <td class="p-4 border-b">
                            <!-- Improved log data formatting -->
                            @if(isset($log->log_data))
                                <div class="font-semibold text-lg mb-2">Log Message:</div>
                                <pre class="bg-gray-50 p-3 rounded-md border border-gray-200">{{ json_encode(json_decode($log->log_data), JSON_PRETTY_PRINT) }}</pre>
                            @else
                                <p class="text-gray-500">No log data available.</p>
                            @endif
                        </td>
                        <td class="p-4 border-b text-center">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="p-4 border-b text-center">{{ $log->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($logs->isEmpty())
            <div class="mt-4 text-center text-gray-500">
                No logs found.
            </div>
        @endif
    </div>
@endsection

<!-- Separate JavaScript file -->
<script src="{{ asset('js/po-logs.js') }}"></script>

<!-- Inline JavaScript for filtering logic -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Filter logs by category keywords
        document.getElementById("category-filter").addEventListener("change", function() {
            const selectedOption = this.options[this.selectedIndex];
            const keywords = selectedOption.getAttribute("data-keywords") ? selectedOption.getAttribute("data-keywords").split(",") : [];
            filterLogs(keywords);
        });

        // Show all logs
        document.getElementById("show-all-button").addEventListener("click", function() {
            document.querySelectorAll(".log-row").forEach(row => row.style.display = "table-row");
        });

        // Clear the filter
        document.getElementById("clear-button").addEventListener("click", function() {
            document.getElementById("category-filter").value = "";
            document.querySelectorAll(".log-row").forEach(row => row.style.display = "table-row");
        });

        // Filter logs based on keywords
        function filterLogs(keywords) {
            document.querySelectorAll(".log-row").forEach(row => {
                const logData = row.querySelector("td:nth-child(2)").innerText;
                const match = keywords.some(keyword => logData.toLowerCase().includes(keyword.toLowerCase()));
                row.style.display = match ? "table-row" : "none";
            });
        }
    });
</script>

</body>
</html>
