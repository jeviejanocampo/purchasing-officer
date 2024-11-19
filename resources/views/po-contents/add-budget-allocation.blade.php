<meta name="csrf-token" content="{{ csrf_token() }}">
<div id="budget-allocation-section" class="section hidden bg-white rounded-lg shadow-md p-5">
    <h1 class="text-xl font-bold mb-4">Budget Allocation Details for Product Purchase</h1>

    <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
        <strong>Note:</strong> To restock, Add budget first
        <!-- Add the button below the note -->
        <button 
            class="ml-4 bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600"
            onclick="document.getElementById('section-selector').value = 'add-budget'; toggleSections();"
        >
         Add Budget
        </button>
    </div>

    <!-- Search Bar -->
    <div class="mb-4 flex">
        <input 
            type="text" 
            id="budget-search" 
            placeholder="Search by Budget ID" 
            class="w-1/4 p-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200"
            onkeyup="searchBudget()"
        />
    </div>

    <button 
        onclick="location.reload()" 
        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors duration-300 mb-4">
        Refresh
    </button>

    <div class="overflow-y-auto h-200">
        <table class="min-w-full border-collapse border border-gray-300 mt-4 text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-1 py-1">Budget ID</th>
                    <th class="border px-1 py-1">Balance</th>
                    <th class="border px-1 py-1">Status</th>
                    <th class="border px-1 py-1">Details</th>
                    <th class="border px-1 py-1">Actions</th>
                </tr>
            </thead>
            <tbody id="budgetTableBody">
                @php
                    $totalBalance = 0;
                @endphp
                @foreach ($budgets as $budget)
                    <tr>
                        <td class="border px-1 py-1">{{ $budget->id }}</td>
                        <td class="border px-1 py-1">
                            @if($budget->remaining_balance == 0)
                                <span class="text-red-500 font-semibold">Budget not used yet</span>
                            @else
                                ₱{{ number_format($budget->remaining_balance, 2) }}
                                @php
                                    $totalBalance += $budget->remaining_balance;
                                @endphp
                            @endif
                        </td>
                        <td class="border px-1 py-1">{{ $budget->budget_status }}</td>
                        <td class="border px-1 py-1">
                            <button 
                                onclick="openModal({{ json_encode($budget) }})" 
                                class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition-colors duration-300 text-xs">
                                View
                            </button>
                        </td>
                        <td class="border px-1 py-1">
                            <button 
                                onclick="openEditStatusModal({{ json_encode($budget) }})" 
                                class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 transition-colors duration-300 text-xs">
                                Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Editing Status -->
<div id="editStatusModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-5 w-1/3 mx-auto">
        <h2 class="text-xl font-bold mb-4">Update Budget Status</h2>
        <form id="updateStatusForm" onsubmit="handleUpdateStatus(event)">
            @csrf
            <input type="hidden" id="budgetId" name="budget_id">
            <div class="mb-4">
                <label for="budget_status" class="block text-sm font-medium text-gray-700">Select Status</label>
                <select 
                    id="budget_status" 
                    name="budget_status" 
                    class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                    <option value="Available">Available</option>
                    <option value="Used">Used</option>
                    <option value="Closed">Closed</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="button" 
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors duration-300 mr-2"
                    onclick="closeEditStatusModal()">Cancel</button>
                <button type="submit" 
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors duration-300">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Open modal to edit budget status
    function openEditStatusModal(budget) {
        document.getElementById('budgetId').value = budget.id;
        document.getElementById('budget_status').value = budget.budget_status;
        document.getElementById('editStatusModal').classList.remove('hidden');
    }

    // Close the modal
    function closeEditStatusModal() {
        document.getElementById('editStatusModal').classList.add('hidden');
    }

    // Handle the form submission
    function handleUpdateStatus(event) {
        event.preventDefault(); // Prevent form default submission

        const form = document.getElementById('updateStatusForm');
        const formData = new FormData(form);
        const budgetId = formData.get('budget_id');
        const budgetStatus = formData.get('budget_status');

        // AJAX call using fetch
        fetch(`/budget/${budgetId}/update-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP status ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                window.alert(data.message);

                // Update the UI with the new status
                const statusElement = document.querySelector(`.budget-status-${budgetId}`);
                if (statusElement) {
                    statusElement.innerText = data.new_status;
                }

                // Close the modal
                closeEditStatusModal();
            } else {
                // Show error message
                window.alert(data.message);
            }
        })
        .catch(error => {
            // Handle unexpected errors
            console.error("Error:", error);
            window.alert("An unexpected error occurred. Please try again.");
        });
    }
</script>
